import { z } from 'zod';

export enum QuestionDifficulty {
  EASY = 'EASY',
  MEDIUM = 'MEDIUM',
  HARD = 'HARD'
}

export enum AssessmentStatus {
  DRAFT = 'DRAFT',
  ACTIVE = 'ACTIVE',
  CLOSED = 'CLOSED'
}

export const OptionSchema = z.object({
  id: z.string().optional(),
  text: z.string().min(1, 'Option text is required'),
  isCorrect: z.boolean().default(false),
  orderIndex: z.number().default(0)
});

export const QuestionSchema = z.object({
  id: z.string().optional(),
  text: z.string().min(3, 'Question text is required'),
  explanation: z.string().optional(),
  difficulty: z.nativeEnum(QuestionDifficulty).default(QuestionDifficulty.MEDIUM),
  points: z.number().default(1),
  orderIndex: z.number().default(0),
  tags: z.array(z.string()).default([]),
  options: z.array(OptionSchema).min(2, 'At least two options are required')
});

export const CreateQuestionnaireSchema = z.object({
  title: z.string().min(3, 'Title is required'),
  description: z.string().optional(),
  courseId: z.string().optional(),
  isAdaptive: z.boolean().default(false),
  deadline: z.string().optional(),
  durationMinutes: z.number().min(1).default(30),
  passingScore: z.number().min(1).max(100).default(70),
  questions: z.array(QuestionSchema).min(1, 'At least one question is required')
});

export type CreateQuestionnaireInput = z.infer<typeof CreateQuestionnaireSchema>;

export const SubmitAssessmentSchema = z.object({
  questionnaireId: z.string(),
  answers: z.array(
    z.object({
      questionId: z.string(),
      selectedOptionId: z.string()
    })
  ),
  durationSeconds: z.number().default(0),
  isAdaptive: z.boolean().default(false)
});

export type SubmitAssessmentInput = z.infer<typeof SubmitAssessmentSchema>;
