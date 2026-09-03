import { Response } from 'express';
import { AuthRequest } from '../middleware/auth';
import { prisma } from '../prisma/client';
import { CourseStatus, EnrollmentStatus } from '@capacity-connect/shared-types';

export class CourseController {
  /**
   * Get all courses with search, subject filter, level filter
   */
  public static async getCourses(req: AuthRequest, res: Response): Promise<void> {
    const { search, subject, level, category } = req.query;

    const where: any = {
      status: CourseStatus.PUBLISHED
    };

    if (search) {
      where.OR = [
        { title: { contains: String(search), mode: 'insensitive' } },
        { description: { contains: String(search), mode: 'insensitive' } },
        { code: { contains: String(search), mode: 'insensitive' } },
        { subject: { contains: String(search), mode: 'insensitive' } }
      ];
    }

    if (subject) {
      where.subject = { contains: String(subject), mode: 'insensitive' };
    }

    if (level) {
      where.level = String(level);
    }

    if (category) {
      where.category = { contains: String(category), mode: 'insensitive' };
    }

    const courses = await prisma.course.findMany({
      where,
      include: {
        trainer: { select: { id: true, name: true, department: true, designation: true } },
        _count: { select: { enrollments: true, resources: true, questionnaires: true } }
      },
      orderBy: { createdAt: 'desc' }
    });

    res.json({ success: true, count: courses.length, courses });
  }

  /**
   * Get Course by ID with details, resources, and assessment syllabus
   */
  public static async getCourseById(req: AuthRequest, res: Response): Promise<void> {
    const { id } = req.params;
    const userId = req.user?.id;

    const course = await prisma.course.findUnique({
      where: { id },
      include: {
        trainer: { select: { id: true, name: true, email: true, department: true, designation: true } },
        resources: {
          where: { isPublic: true },
          orderBy: { createdAt: 'asc' }
        },
        questionnaires: {
          where: { status: 'ACTIVE' },
          select: {
            id: true,
            title: true,
            description: true,
            isAdaptive: true,
            deadline: true,
            durationMinutes: true,
            passingScore: true,
            _count: { select: { questions: true } }
          }
        },
        feedbacks: {
          include: { user: { select: { name: true, department: true } } },
          orderBy: { createdAt: 'desc' },
          take: 10
        },
        _count: { select: { enrollments: true, certificates: true } }
      }
    });

    if (!course) {
      res.status(404).json({ success: false, message: 'Course not found' });
      return;
    }

    // Check if user is enrolled
    let userEnrollment = null;
    if (userId) {
      userEnrollment = await prisma.enrollment.findUnique({
        where: {
          userId_courseId: { userId, courseId: id }
        },
        include: {
          certificate: true
        }
      });
    }

    res.json({ success: true, course, enrollment: userEnrollment });
  }

  /**
   * Create a new course (Trainer & Admin)
   */
  public static async createCourse(req: AuthRequest, res: Response): Promise<void> {
    const trainerId = req.user!.id;
    const { title, code, description, category, subject, targetAudience, level, durationHours, tags, thumbnailUrl, passingScore } = req.body;

    const existing = await prisma.course.findUnique({ where: { code } });
    if (existing) {
      res.status(409).json({ success: false, message: 'Course with this code already exists.' });
      return;
    }

    const course = await prisma.course.create({
      data: {
        title,
        code,
        description,
        category,
        subject,
        targetAudience,
        level: level || 'BEGINNER',
        durationHours: durationHours || 10,
        tags: tags || [],
        thumbnailUrl,
        trainerId,
        passingScore: passingScore || 70,
        status: CourseStatus.PUBLISHED
      }
    });

    res.status(201).json({ success: true, message: 'Course created successfully', course });
  }

  /**
   * Enroll user in course
   */
  public static async enrollCourse(req: AuthRequest, res: Response): Promise<void> {
    const userId = req.user!.id;
    const { courseId } = req.body;

    const course = await prisma.course.findUnique({ where: { id: courseId } });
    if (!course) {
      res.status(404).json({ success: false, message: 'Course not found' });
      return;
    }

    const existing = await prisma.enrollment.findUnique({
      where: { userId_courseId: { userId, courseId } }
    });

    if (existing) {
      res.status(409).json({ success: false, message: 'Already enrolled in this course.' });
      return;
    }

    const enrollment = await prisma.enrollment.create({
      data: {
        userId,
        courseId,
        status: EnrollmentStatus.ENROLLED,
        progressPercentage: 5, // starting progress
        lastAccessedAt: new Date()
      },
      include: {
        course: { select: { title: true, code: true } }
      }
    });

    res.status(201).json({ success: true, message: 'Enrolled successfully!', enrollment });
  }

  /**
   * Update Enrollment Progress
   */
  public static async updateProgress(req: AuthRequest, res: Response): Promise<void> {
    const userId = req.user!.id;
    const { courseId } = req.params;
    const { progressPercentage } = req.body;

    const enrollment = await prisma.enrollment.update({
      where: { userId_courseId: { userId, courseId } },
      data: {
        progressPercentage: Math.min(100, Math.max(0, progressPercentage)),
        lastAccessedAt: new Date(),
        status: progressPercentage >= 100 ? EnrollmentStatus.COMPLETED : EnrollmentStatus.IN_PROGRESS,
        completedAt: progressPercentage >= 100 ? new Date() : null,
        atRisk: false // active progress clears risk flag
      }
    });

    res.json({ success: true, message: 'Progress updated', enrollment });
  }
}
