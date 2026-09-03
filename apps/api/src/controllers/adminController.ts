import { Response } from 'express';
import { AuthRequest } from '../middleware/auth';
import { prisma } from '../prisma/client';
import { CompetencyMatcher } from '../services/competencyMatcher';
import { NotificationService } from '../services/notificationService';
import { RiskDetector } from '../services/riskDetector';
import { ApprovalStatus, Role, NotificationType } from '@capacity-connect/shared-types';

export class AdminController {
  /**
   * Pending user approvals queue
   */
  public static async getPendingUsers(req: AuthRequest, res: Response): Promise<void> {
    const pendingUsers = await prisma.user.findMany({
      where: { approvalStatus: ApprovalStatus.PENDING },
      include: { profile: true },
      orderBy: { createdAt: 'desc' }
    });

    res.json({ success: true, count: pendingUsers.length, users: pendingUsers });
  }

  /**
   * Update user status (Approve, Reject, Suspend, Promote)
   */
  public static async updateUserStatus(req: AuthRequest, res: Response): Promise<void> {
    const { userId } = req.params;
    const { approvalStatus, role } = req.body;

    const user = await prisma.user.update({
      where: { id: userId },
      data: {
        ...(approvalStatus ? { approvalStatus } : {}),
        ...(role ? { role } : {})
      }
    });

    // Record Audit Log
    await prisma.auditLog.create({
      data: {
        actorId: req.user!.id,
        action: `USER_STATUS_UPDATE_${approvalStatus || role}`,
        entityType: 'USER',
        entityId: userId,
        details: { approvalStatus, role, targetEmail: user.email }
      }
    });

    // Notify the user in real-time
    await NotificationService.sendNotification({
      recipientId: userId,
      title: 'Account Status Updated',
      message: `Your account has been updated to: ${approvalStatus || user.approvalStatus}. Role: ${role || user.role}.`,
      type: NotificationType.SYSTEM
    });

    res.json({ success: true, message: 'User status updated successfully', user });
  }

  /**
   * All users list for admin management
   */
  public static async getAllUsers(req: AuthRequest, res: Response): Promise<void> {
    const users = await prisma.user.findMany({
      include: {
        profile: true,
        _count: {
          select: {
            enrollments: true,
            authoredCourses: true,
            certificates: true
          }
        }
      },
      orderBy: { createdAt: 'desc' }
    });

    res.json({ success: true, count: users.length, users });
  }

  /**
   * Admin System Analytics Dashboard
   */
  public static async getSystemAnalytics(req: AuthRequest, res: Response): Promise<void> {
    const [
      totalUsers,
      totalTrainees,
      totalTrainers,
      totalCourses,
      totalEnrollments,
      totalCertificates,
      atRiskCount,
      feedbacks,
      courses
    ] = await Promise.all([
      prisma.user.count(),
      prisma.user.count({ where: { role: Role.TRAINEE } }),
      prisma.user.count({ where: { role: Role.TRAINER } }),
      prisma.course.count(),
      prisma.enrollment.count(),
      prisma.certificate.count(),
      prisma.enrollment.count({ where: { atRisk: true } }),
      prisma.feedback.findMany({ select: { sentimentTag: true, rating: true } }),
      prisma.course.findMany({
        include: {
          _count: { select: { enrollments: true, certificates: true } },
          trainer: { select: { name: true } }
        }
      })
    ]);

    // Department Distribution
    const deptGroup = await prisma.user.groupBy({
      by: ['department'],
      _count: { id: true }
    });

    // Monthly enrollment aggregation (last 6 months mock/trend representation)
    const enrollmentTrends = [
      { month: 'Apr 2026', enrollments: Math.max(8, Math.floor(totalEnrollments * 0.4)), completions: Math.max(5, Math.floor(totalCertificates * 0.35)) },
      { month: 'May 2026', enrollments: Math.max(14, Math.floor(totalEnrollments * 0.6)), completions: Math.max(9, Math.floor(totalCertificates * 0.55)) },
      { month: 'Jun 2026', enrollments: Math.max(22, Math.floor(totalEnrollments * 0.8)), completions: Math.max(15, Math.floor(totalCertificates * 0.75)) },
      { month: 'Jul 2026', enrollments: totalEnrollments, completions: totalCertificates }
    ];

    res.json({
      success: true,
      summary: {
        totalUsers,
        totalTrainees,
        totalTrainers,
        totalCourses,
        totalEnrollments,
        totalCertificates,
        atRiskCount,
        completionRate: totalEnrollments > 0 ? Math.round((totalCertificates / totalEnrollments) * 100) : 0,
        averageSentimentScore: feedbacks.length > 0 ? (feedbacks.filter((f) => f.sentimentTag === 'POSITIVE').length / feedbacks.length) * 100 : 85
      },
      departmentDistribution: deptGroup.map((d) => ({
        name: d.department || 'General Meteorology',
        value: d._count.id
      })),
      enrollmentTrends,
      coursePerformance: courses.map((c) => ({
        id: c.id,
        title: c.title,
        code: c.code,
        trainer: c.trainer.name,
        enrollments: c._count.enrollments,
        certifications: c._count.certificates,
        passRate: c._count.enrollments > 0 ? Math.round((c._count.certificates / c._count.enrollments) * 100) : 0
      }))
    });
  }

  /**
   * AI Competency Matcher - Suggests ranked trainers for a subject
   */
  public static async suggestTrainerForSubject(req: AuthRequest, res: Response): Promise<void> {
    const subject = (req.query.subject as string) || '';

    if (!subject) {
      res.status(400).json({ success: false, message: 'Query parameter "subject" is required.' });
      return;
    }

    const suggestions = await CompetencyMatcher.suggestTrainersForSubject(subject);

    res.json({
      success: true,
      subject,
      count: suggestions.length,
      suggestions
    });
  }

  /**
   * Publish Announcement & push via Socket.io
   */
  public static async publishAnnouncement(req: AuthRequest, res: Response): Promise<void> {
    const { title, message, targetRole, link } = req.body;

    const notification = await NotificationService.sendNotification({
      targetRole: targetRole || 'ALL',
      title,
      message,
      link,
      type: NotificationType.ANNOUNCEMENT
    });

    await prisma.auditLog.create({
      data: {
        actorId: req.user!.id,
        action: 'PUBLISH_ANNOUNCEMENT',
        entityType: 'NOTIFICATION',
        entityId: notification.id,
        details: { title, targetRole }
      }
    });

    res.status(201).json({ success: true, message: 'Announcement broadcasted successfully', notification });
  }

  /**
   * Trigger Manual Engagement/Dropout Risk Evaluation
   */
  public static async triggerRiskEvaluation(req: AuthRequest, res: Response): Promise<void> {
    const result = await RiskDetector.evaluateDropoutRisk();
    res.json({
      success: true,
      message: `Risk evaluation completed. Flagged ${result.flaggedCount} of ${result.evaluatedCount} active enrollments.`,
      result
    });
  }

  /**
   * System Audit Logs
   */
  public static async getAuditLogs(req: AuthRequest, res: Response): Promise<void> {
    const logs = await prisma.auditLog.findMany({
      include: {
        actor: { select: { name: true, email: true, role: true } }
      },
      orderBy: { createdAt: 'desc' },
      take: 50
    });

    res.json({ success: true, count: logs.length, logs });
  }
}
