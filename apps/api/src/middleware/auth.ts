import { Request, Response, NextFunction } from 'express';
import jwt from 'jsonwebtoken';
import { CONFIG } from '../config';
import { prisma } from '../prisma/client';
import { Role, ApprovalStatus } from '@capacity-connect/shared-types';

export interface AuthRequest extends Request {
  user?: {
    id: string;
    email: string;
    role: Role;
    name: string;
    approvalStatus: ApprovalStatus;
  };
}

export const requireAuth = async (req: AuthRequest, res: Response, next: NextFunction): Promise<void> => {
  try {
    const authHeader = req.headers.authorization;
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      res.status(401).json({ success: false, message: 'Authentication required. No token provided.' });
      return;
    }

    const token = authHeader.split(' ')[1];
    const decoded = jwt.verify(token, CONFIG.JWT_SECRET) as {
      id: string;
      email: string;
      role: Role;
    };

    const user = await prisma.user.findUnique({
      where: { id: decoded.id },
      select: { id: true, email: true, role: true, name: true, approvalStatus: true }
    });

    if (!user) {
      res.status(401).json({ success: false, message: 'User account not found.' });
      return;
    }

    if (user.approvalStatus === 'PENDING') {
      res.status(403).json({
        success: false,
        message: 'Your account registration is currently pending Admin approval.'
      });
      return;
    }

    if (user.approvalStatus === 'SUSPENDED' || user.approvalStatus === 'REJECTED') {
      res.status(403).json({
        success: false,
        message: `Your account access has been ${user.approvalStatus.toLowerCase()}. Please contact Admin.`
      });
      return;
    }

    req.user = user as any;
    next();
  } catch (error: any) {
    if (error.name === 'TokenExpiredError') {
      res.status(401).json({ success: false, message: 'Token expired. Please refresh token.', expired: true });
      return;
    }
    res.status(401).json({ success: false, message: 'Invalid or corrupted authentication token.' });
  }
};

export const requireRole = (roles: Role[]) => {
  return (req: AuthRequest, res: Response, next: NextFunction): void => {
    if (!req.user) {
      res.status(401).json({ success: false, message: 'Unauthorized' });
      return;
    }

    if (!roles.includes(req.user.role)) {
      res.status(403).json({
        success: false,
        message: `Access forbidden: Requires one of [${roles.join(', ')}] roles.`
      });
      return;
    }

    next();
  };
};
