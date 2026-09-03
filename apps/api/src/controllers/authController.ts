import { Request, Response } from 'express';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import { prisma } from '../prisma/client';
import { CONFIG } from '../config';
import { Role, ApprovalStatus } from '@capacity-connect/shared-types';
import { AuthRequest } from '../middleware/auth';

export class AuthController {
  public static async signup(req: Request, res: Response): Promise<void> {
    const { email, password, name, role, employeeId, department, organization, designation } = req.body;

    const existingUser = await prisma.user.findFirst({
      where: {
        OR: [{ email }, ...(employeeId ? [{ employeeId }] : [])]
      }
    });

    if (existingUser) {
      res.status(409).json({ success: false, message: 'User with this email or Employee ID already exists.' });
      return;
    }

    const salt = await bcrypt.genSalt(10);
    const passwordHash = await bcrypt.hash(password, salt);

    // Trainers require Admin approval by default; Trainees and Admins are Approved
    const approvalStatus = role === Role.TRAINER ? ApprovalStatus.PENDING : ApprovalStatus.APPROVED;

    const user = await prisma.user.create({
      data: {
        email,
        passwordHash,
        name,
        role: role || Role.TRAINEE,
        approvalStatus,
        employeeId: employeeId || null,
        department: department || null,
        organization: organization || 'India Meteorological Department (IMD)',
        designation: designation || null,
        profile: {
          create: {
            interests: [],
            skills: []
          }
        }
      },
      select: {
        id: true,
        email: true,
        name: true,
        role: true,
        approvalStatus: true,
        employeeId: true,
        department: true,
        organization: true,
        designation: true,
        createdAt: true
      }
    });

    if (approvalStatus === ApprovalStatus.PENDING) {
      res.status(201).json({
        success: true,
        message: 'Trainer account created successfully. It is currently pending Admin approval.',
        user,
        requiresApproval: true
      });
      return;
    }

    // Generate tokens for approved users
    const accessToken = jwt.sign({ id: user.id, email: user.email, role: user.role }, CONFIG.JWT_SECRET, {
      expiresIn: CONFIG.JWT_EXPIRES_IN as any
    });
    const refreshToken = jwt.sign({ id: user.id }, CONFIG.JWT_REFRESH_SECRET, {
      expiresIn: CONFIG.JWT_REFRESH_EXPIRES_IN as any
    });

    res.status(201).json({
      success: true,
      message: 'Signup successful',
      user,
      accessToken,
      refreshToken
    });
  }

  public static async login(req: Request, res: Response): Promise<void> {
    const { email, password } = req.body;

    const user = await prisma.user.findUnique({
      where: { email },
      include: { profile: true }
    });

    if (!user) {
      res.status(401).json({ success: false, message: 'Invalid email or password.' });
      return;
    }

    const isValid = await bcrypt.compare(password, user.passwordHash);
    if (!isValid) {
      res.status(401).json({ success: false, message: 'Invalid email or password.' });
      return;
    }

    if (user.approvalStatus === ApprovalStatus.PENDING) {
      res.status(403).json({
        success: false,
        message: 'Your Trainer registration is still pending Admin verification and approval.'
      });
      return;
    }

    if (user.approvalStatus === ApprovalStatus.REJECTED || user.approvalStatus === ApprovalStatus.SUSPENDED) {
      res.status(403).json({
        success: false,
        message: `Your account has been ${user.approvalStatus.toLowerCase()}. Contact your IMD training administrator.`
      });
      return;
    }

    const accessToken = jwt.sign({ id: user.id, email: user.email, role: user.role }, CONFIG.JWT_SECRET, {
      expiresIn: CONFIG.JWT_EXPIRES_IN as any
    });
    const refreshToken = jwt.sign({ id: user.id }, CONFIG.JWT_REFRESH_SECRET, {
      expiresIn: CONFIG.JWT_REFRESH_EXPIRES_IN as any
    });

    const { passwordHash: _, ...safeUser } = user;

    res.json({
      success: true,
      message: 'Login successful',
      user: safeUser,
      accessToken,
      refreshToken
    });
  }

  public static async refresh(req: Request, res: Response): Promise<void> {
    const { refreshToken } = req.body;

    if (!refreshToken) {
      res.status(400).json({ success: false, message: 'Refresh token is required.' });
      return;
    }

    try {
      const decoded = jwt.verify(refreshToken, CONFIG.JWT_REFRESH_SECRET) as { id: string };
      const user = await prisma.user.findUnique({
        where: { id: decoded.id },
        select: { id: true, email: true, role: true, approvalStatus: true }
      });

      if (!user || user.approvalStatus !== ApprovalStatus.APPROVED) {
        res.status(401).json({ success: false, message: 'Invalid or revoked refresh session.' });
        return;
      }

      const accessToken = jwt.sign({ id: user.id, email: user.email, role: user.role }, CONFIG.JWT_SECRET, {
        expiresIn: CONFIG.JWT_EXPIRES_IN as any
      });

      res.json({
        success: true,
        accessToken
      });
    } catch {
      res.status(401).json({ success: false, message: 'Expired or invalid refresh token.' });
    }
  }

  public static async logout(req: Request, res: Response): Promise<void> {
    res.json({ success: true, message: 'Logged out successfully.' });
  }

  public static async getMe(req: AuthRequest, res: Response): Promise<void> {
    const user = await prisma.user.findUnique({
      where: { id: req.user!.id },
      include: {
        profile: true,
        achievements: true,
        _count: {
          select: {
            enrollments: true,
            certificates: true,
            authoredCourses: true
          }
        }
      }
    });

    if (!user) {
      res.status(404).json({ success: false, message: 'User not found' });
      return;
    }

    const { passwordHash: _, ...safeUser } = user;
    res.json({ success: true, user: safeUser });
  }
}
