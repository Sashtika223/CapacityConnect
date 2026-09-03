import { Response } from 'express';
import { AuthRequest } from '../middleware/auth';
import { prisma } from '../prisma/client';

export class TrainerController {
  /**
   * Trainer Dashboard stats
   */
  public static async getDashboardStats(req: AuthRequest, res: Response): Promise<void> {
    const trainerId = req.user!.id;

    const [courses, resources, questionnaires, feedbacks, competencies] = await Promise.all([
      prisma.course.findMany({
        where: { trainerId },
        include: {
          enrollments: {
            include: { user: { select: { name: true, email: true, department: true } } }
          }
        }
      }),
      prisma.learningResource.findMany({
        where: { trainerId }
      }),
      prisma.questionnaire.findMany({
        where: { trainerId },
        include: { attempts: true }
      }),
      prisma.feedback.findMany({
        where: { trainerId },
        orderBy: { createdAt: 'desc' }
      }),
      prisma.competencyMap.findMany({
        where: { trainerId }
      })
    ]);

    const totalTrainees = new Set(courses.flatMap((c) => c.enrollments.map((e) => e.userId))).size;
    const totalEnrollments = courses.reduce((sum, c) => sum + c.enrollments.length, 0);
    const atRiskEnrollments = courses.flatMap((c) => c.enrollments).filter((e) => e.atRisk);

    let avgRating = 0;
    if (feedbacks.length > 0) {
      avgRating = feedbacks.reduce((acc, f) => acc + f.rating, 0) / feedbacks.length;
    }

    // Sentiment breakdown
    const sentimentCounts = {
      POSITIVE: feedbacks.filter((f) => f.sentimentTag === 'POSITIVE').length,
      NEUTRAL: feedbacks.filter((f) => f.sentimentTag === 'NEUTRAL').length,
      NEGATIVE: feedbacks.filter((f) => f.sentimentTag === 'NEGATIVE').length
    };

    res.json({
      success: true,
      stats: {
        totalCourses: courses.length,
        totalTrainees,
        totalEnrollments,
        totalResources: resources.length,
        totalAssessments: questionnaires.length,
        averageRating: Math.round(avgRating * 10) / 10,
        atRiskCount: atRiskEnrollments.length,
        sentimentCounts
      },
      courses: courses.map((c) => ({
        id: c.id,
        title: c.title,
        code: c.code,
        subject: c.subject,
        level: c.level,
        status: c.status,
        enrollmentCount: c.enrollments.length,
        atRiskCount: c.enrollments.filter((e) => e.atRisk).length
      })),
      recentFeedbacks: feedbacks.slice(0, 5),
      competencies
    });
  }

  /**
   * Monitor Trainee Participation & Risk Flags
   */
  public static async getTraineeMonitor(req: AuthRequest, res: Response): Promise<void> {
    const trainerId = req.user!.id;
    const { courseId } = req.query;

    const enrollments = await prisma.enrollment.findMany({
      where: {
        course: {
          trainerId,
          ...(courseId ? { id: String(courseId) } : {})
        }
      },
      include: {
        user: {
          select: {
            id: true,
            name: true,
            email: true,
            department: true,
            designation: true,
            resourceAccesses: true,
            attempts: {
              where: {
                questionnaire: { course: { trainerId } }
              },
              include: { questionnaire: { select: { title: true } } }
            }
          }
        },
        course: {
          select: { id: true, title: true, code: true }
        },
        certificate: true
      },
      orderBy: [{ atRisk: 'desc' }, { progressPercentage: 'asc' }]
    });

    const atRiskTrainees = enrollments.filter((e) => e.atRisk);
    const completedTrainees = enrollments.filter((e) => e.status === 'COMPLETED');

    res.json({
      success: true,
      totalCount: enrollments.length,
      atRiskCount: atRiskTrainees.length,
      completedCount: completedTrainees.length,
      trainees: enrollments.map((e) => ({
        enrollmentId: e.id,
        userId: e.user.id,
        name: e.user.name,
        email: e.user.email,
        department: e.user.department,
        designation: e.user.designation,
        courseId: e.course.id,
        courseTitle: e.course.title,
        courseCode: e.course.code,
        progressPercentage: e.progressPercentage,
        status: e.status,
        atRisk: e.atRisk,
        riskReason: e.riskReason,
        lastAccessedAt: e.lastAccessedAt,
        enrolledAt: e.enrolledAt,
        resourcesAccessedCount: e.user.resourceAccesses.length,
        attemptsCount: e.user.attempts.length,
        latestScore: e.user.attempts[0]?.percentage ?? null,
        certificateIssued: !!e.certificate
      }))
    });
  }

  /**
   * Get Feedback aggregation and sentiment analytics for trainer
   */
  public static async getFeedbackAnalytics(req: AuthRequest, res: Response): Promise<void> {
    const trainerId = req.user!.id;

    const feedbacks = await prisma.feedback.findMany({
      where: { trainerId },
      include: {
        course: { select: { title: true, code: true } },
        user: { select: { name: true, department: true } }
      },
      orderBy: { createdAt: 'desc' }
    });

    const total = feedbacks.length;
    const avgOverall = total > 0 ? feedbacks.reduce((acc, f) => acc + f.rating, 0) / total : 0;
    const avgContent = total > 0 ? feedbacks.reduce((acc, f) => acc + f.contentRating, 0) / total : 0;
    const avgDelivery = total > 0 ? feedbacks.reduce((acc, f) => acc + f.deliveryRating, 0) / total : 0;

    const sentimentBreakdown = {
      POSITIVE: feedbacks.filter((f) => f.sentimentTag === 'POSITIVE').length,
      NEUTRAL: feedbacks.filter((f) => f.sentimentTag === 'NEUTRAL').length,
      NEGATIVE: feedbacks.filter((f) => f.sentimentTag === 'NEGATIVE').length
    };

    res.json({
      success: true,
      metrics: {
        totalReviews: total,
        avgOverall: Math.round(avgOverall * 10) / 10,
        avgContent: Math.round(avgContent * 10) / 10,
        avgDelivery: Math.round(avgDelivery * 10) / 10,
        sentimentBreakdown
      },
      feedbacks
    });
  }
}
