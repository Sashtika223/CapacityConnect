import { z } from 'zod';

export const CompetencyMapSchema = z.object({
  trainerId: z.string(),
  subject: z.string().min(2, 'Subject is required'),
  proficiencyScore: z.number().min(1).max(100).default(80),
  evidence: z.string().optional()
});

export type CompetencyMapInput = z.infer<typeof CompetencyMapSchema>;

export interface TrainerScoreBreakdown {
  trainerId: string;
  trainerName: string;
  department?: string | null;
  totalScore: number; // 0-100
  factors: {
    traineePassRateScore: number; // weighted
    feedbackScore: number;        // weighted
    proficiencyScore: number;     // weighted
    recencyScore: number;         // weighted
  };
  rawMetrics: {
    traineePassRate: number;      // 0-100%
    averageFeedbackRating: number;// 1-5
    baseProficiency: number;      // 1-100
    activeCoursesCount: number;
    daysSinceLastActive: number;
  };
  explanation: string;
}
