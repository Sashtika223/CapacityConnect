import { z } from 'zod';

export enum CourseLevel {
  BEGINNER = 'BEGINNER',
  INTERMEDIATE = 'INTERMEDIATE',
  ADVANCED = 'ADVANCED'
}

export enum CourseStatus {
  DRAFT = 'DRAFT',
  PUBLISHED = 'PUBLISHED',
  ARCHIVED = 'ARCHIVED'
}

export enum EnrollmentStatus {
  ENROLLED = 'ENROLLED',
  IN_PROGRESS = 'IN_PROGRESS',
  COMPLETED = 'COMPLETED',
  DROPPED = 'DROPPED'
}

export enum ResourceFormat {
  VIDEO = 'VIDEO',
  PPT = 'PPT',
  PDF = 'PDF',
  DOC = 'DOC',
  URL = 'URL'
}

export const CreateCourseSchema = z.object({
  title: z.string().min(3, 'Title must be at least 3 characters'),
  code: z.string().min(2, 'Course code is required'),
  description: z.string().min(10, 'Description must be at least 10 characters'),
  category: z.string().min(2, 'Category is required'),
  subject: z.string().min(2, 'Subject is required'),
  targetAudience: z.string().optional(),
  level: z.nativeEnum(CourseLevel).default(CourseLevel.BEGINNER),
  durationHours: z.number().min(1).default(10),
  tags: z.array(z.string()).default([]),
  thumbnailUrl: z.string().optional(),
  passingScore: z.number().min(1).max(100).default(70)
});

export type CreateCourseInput = z.infer<typeof CreateCourseSchema>;

export const CreateResourceSchema = z.object({
  title: z.string().min(2, 'Resource title is required'),
  description: z.string().optional(),
  format: z.nativeEnum(ResourceFormat).default(ResourceFormat.PDF),
  fileUrl: z.string().min(1, 'File URL or path is required'),
  fileSize: z.number().optional(),
  durationMinutes: z.number().optional(),
  subject: z.string().min(2, 'Subject is required'),
  tags: z.array(z.string()).default([]),
  courseId: z.string().optional(),
  isPublic: z.boolean().default(true)
});

export type CreateResourceInput = z.infer<typeof CreateResourceSchema>;
