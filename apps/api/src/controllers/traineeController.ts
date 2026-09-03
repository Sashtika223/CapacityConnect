import { Response } from 'express';
import { AuthRequest } from '../middleware/auth';
import { prisma } from '../prisma/client';
import { CourseRecommender } from '../services/courseRecommender';
import { SentimentAnalyzer } from '../services/sentimentAnalyzer';

export class TraineeController {
  /**
   * Get trainee profile
   */
  public static async getProfile(req: AuthRequest, res: Response): Promise<void> {
    const profile = await prisma.profile.findUnique({
      where: { userId: req.user!.id },
      include: {
        user: {
          select: {
            id: true,
            email: true,
            name: true,
            role: true,
            employeeId: true,
            department: true,
            organization: true,
            designation: true,
            avatarUrl: true
          }
        }
      }
    });

    res.json({ success: true, profile });
  }

  /**
   * Update trainee profile (qualifications, experience, interests, skills)
   */
  public static async updateProfile(req: AuthRequest, res: Response): Promise<void> {
    const { qualifications, experienceYears, bio, interests, skills, certificatesEarned } = req.body;

    const profile = await prisma.profile.upsert({
      where: { userId: req.user!.id },
      create: {
        userId: req.user!.id,
        qualifications,
        experienceYears: experienceYears || 0,
        bio,
        interests: interests || [],
        skills: skills || [],
        certificatesEarned: certificatesEarned || []
      },
      update: {
        qualifications,
        experienceYears,
        bio,
        interests,
        skills,
        certificatesEarned
      }
    });

    res.json({ success: true, message: 'Profile updated successfully', profile });
  }

  /**
   * Trainee Dashboard statistics
   */
  public static async getDashboardStats(req: AuthRequest, res: Response): Promise<void> {
    const userId = req.user!.id;

    const [enrollments, certificates, upcomingQuizzes, achievements] = await Promise.all([
      prisma.enrollment.findMany({
        where: { userId },
        include: {
          course: {
            select: {
              id: true,
              title: true,
              code: true,
              subject: true,
              durationHours: true,
              thumbnailUrl: true,
              trainer: { select: { name: true, department: true } }
            }
          }
        },
        orderBy: { lastAccessedAt: 'desc' }
      }),
      prisma.certificate.findMany({
        where: { userId },
        include: { course: true },
        orderBy: { issueDate: 'desc' }
      }),
      prisma.questionnaire.findMany({
        where: {
          status: 'ACTIVE',
          course: {
            enrollments: {
              some: { userId, status: { in: ['ENROLLED', 'IN_PROGRESS'] } }
            }
          }
        },
        include: {
          course: { select: { title: true, code: true } },
          attempts: { where: { userId } }
        },
        take: 5
      }),
      prisma.achievement.findMany({
        where: { userId },
        orderBy: { unlockedAt: 'desc' }
      })
    ]);

    const activeCount = enrollments.filter((e) => e.status === 'ENROLLED' || e.status === 'IN_PROGRESS').length;
    const completedCount = enrollments.filter((e) => e.status === 'COMPLETED').length;
    const avgProgress = enrollments.length > 0 ? enrollments.reduce((acc, e) => acc + e.progressPercentage, 0) / enrollments.length : 0;

    res.json({
      success: true,
      stats: {
        totalEnrolled: enrollments.length,
        activeCourses: activeCount,
        completedCourses: completedCount,
        averageProgress: Math.round(avgProgress),
        certificatesEarned: certificates.length,
        achievementsCount: achievements.length
      },
      enrollments,
      certificates,
      upcomingQuizzes: upcomingQuizzes.map((q) => ({
        id: q.id,
        title: q.title,
        courseTitle: q.course?.title,
        courseCode: q.course?.code,
        deadline: q.deadline,
        durationMinutes: q.durationMinutes,
        hasAttempted: q.attempts.length > 0,
        isPassed: q.attempts.some((a) => a.passed)
      })),
      achievements
    });
  }

  /**
   * Smart AI Course Recommendations for Trainee
   */
  public static async getRecommendations(req: AuthRequest, res: Response): Promise<void> {
    const userId = req.user!.id;

    const [user, allCourses, enrollments] = await Promise.all([
      prisma.user.findUnique({
        where: { id: userId },
        include: { profile: true }
      }),
      prisma.course.findMany({
        where: { status: 'PUBLISHED' },
        include: { trainer: { select: { name: true, department: true } } }
      }),
      prisma.enrollment.findMany({
        where: { userId },
        select: { courseId: true }
      })
    ]);

    const enrolledCourseIds = new Set(enrollments.map((e) => e.courseId));

    const userProfile = {
      interests: (user?.profile?.interests as string[]) || [],
      skills: (user?.profile?.skills as string[]) || [],
      department: user?.department
    };

    const recommendations = CourseRecommender.recommend(userProfile, allCourses, enrolledCourseIds);

    res.json({
      success: true,
      count: recommendations.length,
      recommendations
    });
  }

  /**
   * Submit course and trainer feedback
   */
  public static async submitFeedback(req: AuthRequest, res: Response): Promise<void> {
    const userId = req.user!.id;
    const { courseId, rating, contentRating, deliveryRating, comments } = req.body;

    const course = await prisma.course.findUnique({
      where: { id: courseId },
      select: { trainerId: true }
    });

    if (!course) {
      res.status(404).json({ success: false, message: 'Course not found' });
      return;
    }

    // Run Rule-Based Sentiment Analysis
    const sentiment = SentimentAnalyzer.analyze(comments, rating);

    const feedback = await prisma.feedback.create({
      data: {
        courseId,
        userId,
        trainerId: course.trainerId,
        rating,
        contentRating: contentRating || 5,
        deliveryRating: deliveryRating || 5,
        comments,
        sentimentTag: sentiment.tag as any,
        sentimentScore: sentiment.score
      }
    });

    res.status(201).json({
      success: true,
      message: 'Feedback submitted successfully',
      feedback,
      sentimentAnalysis: {
        tag: sentiment.tag,
        score: sentiment.score,
        analyzedBy: 'Rule-Based Lexicon Engine'
      }
    });
  }
}
