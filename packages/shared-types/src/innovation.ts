import { z } from 'zod';

export enum SentimentTag {
  POSITIVE = 'POSITIVE',
  NEUTRAL = 'NEUTRAL',
  NEGATIVE = 'NEGATIVE'
}

export enum NotificationType {
  ANNOUNCEMENT = 'ANNOUNCEMENT',
  DEADLINE = 'DEADLINE',
  CERTIFICATE = 'CERTIFICATE',
  RISK_ALERT = 'RISK_ALERT',
  SYSTEM = 'SYSTEM'
}

export const CreateFeedbackSchema = z.object({
  courseId: z.string(),
  rating: z.number().min(1).max(5),
  contentRating: z.number().min(1).max(5).default(5),
  deliveryRating: z.number().min(1).max(5).default(5),
  comments: z.string().min(3, 'Comments must be at least 3 characters')
});

export type CreateFeedbackInput = z.infer<typeof CreateFeedbackSchema>;

export const CreateAnnouncementSchema = z.object({
  title: z.string().min(3, 'Title is required'),
  message: z.string().min(5, 'Message is required'),
  targetRole: z.enum(['ALL', 'TRAINEE', 'TRAINER', 'ADMIN']).default('ALL'),
  link: z.string().optional()
});

export type CreateAnnouncementInput = z.infer<typeof CreateAnnouncementSchema>;

export interface RecommendationResult {
  courseId: string;
  title: string;
  code: string;
  category: string;
  subject: string;
  level: string;
  durationHours: number;
  tags: string[];
  similarityScore: number;
  matchedTags: string[];
  recommendationReason: string;
}

export interface AdaptiveQuestionNext {
  question: {
    id: string;
    text: string;
    difficulty: string;
    points: number;
    orderIndex: number;
    options: { id: string; text: string; orderIndex: number }[];
  };
  currentStreak: number;
  runningAccuracy: number;
  estimatedAbilityLevel: 'BEGINNER' | 'INTERMEDIATE' | 'EXPERT';
  isComplete: boolean;
  questionNumber: number;
  totalPlannedQuestions: number;
}
