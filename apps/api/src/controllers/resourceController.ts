import { Response } from 'express';
import { AuthRequest } from '../middleware/auth';
import { prisma } from '../prisma/client';
import { ResourceFormat } from '@capacity-connect/shared-types';

export class ResourceController {
  /**
   * Search and filter Trainer Library resources
   */
  public static async getResources(req: AuthRequest, res: Response): Promise<void> {
    const { search, subject, format, courseId } = req.query;

    const where: any = {
      isPublic: true
    };

    if (search) {
      where.OR = [
        { title: { contains: String(search), mode: 'insensitive' } },
        { description: { contains: String(search), mode: 'insensitive' } },
        { subject: { contains: String(search), mode: 'insensitive' } }
      ];
    }

    if (subject) {
      where.subject = { contains: String(subject), mode: 'insensitive' };
    }

    if (format) {
      where.format = String(format) as ResourceFormat;
    }

    if (courseId) {
      where.courseId = String(courseId);
    }

    const resources = await prisma.learningResource.findMany({
      where,
      include: {
        trainer: { select: { id: true, name: true, department: true } },
        course: { select: { id: true, title: true, code: true } }
      },
      orderBy: { createdAt: 'desc' }
    });

    res.json({ success: true, count: resources.length, resources });
  }

  /**
   * Upload / Create a resource in the Trainer Library
   */
  public static async createResource(req: AuthRequest, res: Response): Promise<void> {
    const trainerId = req.user!.id;
    const { title, description, format, fileUrl, fileSize, durationMinutes, subject, tags, courseId, isPublic } = req.body;

    const resource = await prisma.learningResource.create({
      data: {
        title,
        description,
        format: format || ResourceFormat.PDF,
        fileUrl: fileUrl || '/uploads/sample-lecture.pdf',
        fileSize: fileSize || 2048000,
        durationMinutes,
        subject,
        tags: tags || [],
        courseId: courseId || null,
        trainerId,
        isPublic: isPublic !== undefined ? isPublic : true,
        version: 1
      },
      include: {
        trainer: { select: { name: true } }
      }
    });

    res.status(201).json({ success: true, message: 'Resource published to Trainer Library', resource });
  }

  /**
   * Access / Download Resource and log engagement for risk analysis
   */
  public static async accessResource(req: AuthRequest, res: Response): Promise<void> {
    const userId = req.user!.id;
    const { id } = req.params;

    const resource = await prisma.learningResource.findUnique({
      where: { id }
    });

    if (!resource) {
      res.status(404).json({ success: false, message: 'Resource not found' });
      return;
    }

    // Increment download/view count and log access
    await Promise.all([
      prisma.learningResource.update({
        where: { id },
        data: { downloadCount: { increment: 1 } }
      }),
      prisma.resourceAccessLog.create({
        data: {
          userId,
          resourceId: id,
          accessedAt: new Date()
        }
      })
    ]);

    // Also update enrollment lastAccessedAt if resource is linked to a course
    if (resource.courseId) {
      await prisma.enrollment.updateMany({
        where: { userId, courseId: resource.courseId },
        data: { lastAccessedAt: new Date() }
      });
    }

    res.json({
      success: true,
      message: 'Access logged successfully',
      resource
    });
  }
}
