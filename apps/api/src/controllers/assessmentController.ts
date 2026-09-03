import { Response } from 'express';
import { AuthRequest } from '../middleware/auth';
import { prisma } from '../prisma/client';
import { AdaptiveEngine, AnswerHistoryItem } from '../services/adaptiveEngine';
import { CertificateService } from '../services/certificateService';
import { QuestionDifficulty, EnrollmentStatus } from '@capacity-connect/shared-types';

export class AssessmentController {
  /**
   * Get Questionnaires (all or by course)
   */
  public static async getQuestionnaires(req: AuthRequest, res: Response): Promise<void> {
    const { courseId } = req.query;

    const questionnaires = await prisma.questionnaire.findMany({
      where: {
        status: 'ACTIVE',
        ...(courseId ? { courseId: String(courseId) } : {})
      },
      include: {
        course: { select: { title: true, code: true } },
        trainer: { select: { name: true, department: true } },
        _count: { select: { questions: true, attempts: true } }
      },
      orderBy: { createdAt: 'desc' }
    });

    res.json({ success: true, count: questionnaires.length, questionnaires });
  }

  /**
   * Get single Questionnaire with questions (sanitized for trainees taking test)
   */
  public static async getQuestionnaireById(req: AuthRequest, res: Response): Promise<void> {
    const { id } = req.params;
    const isTrainerOrAdmin = req.user?.role === 'TRAINER' || req.user?.role === 'ADMIN';

    const questionnaire = await prisma.questionnaire.findUnique({
      where: { id },
      include: {
        course: { select: { id: true, title: true, code: true, passingScore: true } },
        trainer: { select: { name: true } },
        questions: {
          include: {
            options: {
              select: {
                id: true,
                text: true,
                orderIndex: true,
                ...(isTrainerOrAdmin ? { isCorrect: true } : {})
              }
            }
          },
          orderBy: { orderIndex: 'asc' }
        }
      }
    });

    if (!questionnaire) {
      res.status(404).json({ success: false, message: 'Assessment questionnaire not found.' });
      return;
    }

    res.json({ success: true, questionnaire });
  }

  /**
   * Create Questionnaire with Questions and Options (Trainer & Admin)
   */
  public static async createQuestionnaire(req: AuthRequest, res: Response): Promise<void> {
    const trainerId = req.user!.id;
    const { title, description, courseId, isAdaptive, deadline, durationMinutes, passingScore, questions } = req.body;

    const questionnaire = await prisma.questionnaire.create({
      data: {
        title,
        description,
        courseId: courseId || null,
        trainerId,
        isAdaptive: isAdaptive || false,
        deadline: deadline ? new Date(deadline) : null,
        durationMinutes: durationMinutes || 30,
        passingScore: passingScore || 70,
        questions: {
          create: questions.map((q: any, qIdx: number) => ({
            text: q.text,
            explanation: q.explanation || null,
            difficulty: q.difficulty || QuestionDifficulty.MEDIUM,
            points: q.points || 1,
            orderIndex: qIdx,
            tags: q.tags || [],
            options: {
              create: q.options.map((opt: any, optIdx: number) => ({
                text: opt.text,
                isCorrect: opt.isCorrect || false,
                orderIndex: optIdx
              }))
            }
          }))
        }
      },
      include: {
        questions: { include: { options: true } }
      }
    });

    res.status(201).json({ success: true, message: 'Questionnaire created successfully', questionnaire });
  }

  /**
   * Get Next Question in Adaptive MCQ Mode (IRT-lite)
   */
  public static async getNextAdaptiveQuestion(req: AuthRequest, res: Response): Promise<void> {
    const { questionnaireId, history } = req.body as {
      questionnaireId: string;
      history: AnswerHistoryItem[];
    };

    const questionnaire = await prisma.questionnaire.findUnique({
      where: { id: questionnaireId },
      include: {
        questions: {
          include: {
            options: {
              select: { id: true, text: true, orderIndex: true }
            }
          }
        }
      }
    });

    if (!questionnaire) {
      res.status(404).json({ success: false, message: 'Questionnaire not found' });
      return;
    }

    const answeredIds = new Set((history || []).map((h) => h.questionId));
    const totalQuestionsPlanned = Math.min(10, questionnaire.questions.length);

    // If max question limit reached or all answered
    if (answeredIds.size >= totalQuestionsPlanned || answeredIds.size >= questionnaire.questions.length) {
      const stats = AdaptiveEngine.determineNextDifficulty(history || []);
      res.json({
        success: true,
        isComplete: true,
        summary: stats
      });
      return;
    }

    // Determine target difficulty for next question
    const currentDiff = history && history.length > 0 ? history[history.length - 1].difficulty : QuestionDifficulty.MEDIUM;
    const { nextDifficulty, runningAccuracy, currentStreak, estimatedAbility } = AdaptiveEngine.determineNextDifficulty(
      history || [],
      currentDiff
    );

    // Select the next unasked question matching the target difficulty
    const nextQ = AdaptiveEngine.selectNextQuestion(questionnaire.questions, nextDifficulty, answeredIds);

    if (!nextQ) {
      res.json({
        success: true,
        isComplete: true,
        summary: { nextDifficulty, runningAccuracy, currentStreak, estimatedAbility }
      });
      return;
    }

    res.json({
      success: true,
      isComplete: false,
      question: {
        id: nextQ.id,
        text: nextQ.text,
        difficulty: nextQ.difficulty,
        points: nextQ.points,
        orderIndex: answeredIds.size + 1,
        options: nextQ.options
      },
      currentStreak,
      runningAccuracy,
      estimatedAbilityLevel: estimatedAbility,
      questionNumber: answeredIds.size + 1,
      totalPlannedQuestions: totalQuestionsPlanned
    });
  }

  /**
   * Submit Assessment Attempt (Standard or Adaptive)
   */
  public static async submitAssessment(req: AuthRequest, res: Response): Promise<void> {
    const userId = req.user!.id;
    const { questionnaireId, answers, durationSeconds, isAdaptive } = req.body;

    const questionnaire = await prisma.questionnaire.findUnique({
      where: { id: questionnaireId },
      include: {
        course: true,
        questions: {
          include: { options: true }
        }
      }
    });

    if (!questionnaire) {
      res.status(404).json({ success: false, message: 'Questionnaire not found.' });
      return;
    }

    // Grade the submission
    let totalScore = 0;
    let maxPossibleScore = 0;
    const gradedAnswers: any[] = [];

    const questionMap = new Map(questionnaire.questions.map((q) => [q.id, q]));

    for (const ans of answers) {
      const question = questionMap.get(ans.questionId);
      if (!question) continue;

      maxPossibleScore += question.points;
      const correctOption = question.options.find((o) => o.isCorrect);
      const isCorrect = correctOption?.id === ans.selectedOptionId;

      if (isCorrect) {
        totalScore += question.points;
      }

      gradedAnswers.push({
        questionId: question.id,
        questionText: question.text,
        difficulty: question.difficulty,
        selectedOptionId: ans.selectedOptionId,
        correctOptionId: correctOption?.id,
        isCorrect,
        pointsEarned: isCorrect ? question.points : 0,
        explanation: question.explanation
      });
    }

    const percentage = maxPossibleScore > 0 ? Math.round((totalScore / maxPossibleScore) * 100 * 10) / 10 : 0;
    const passed = percentage >= questionnaire.passingScore;

    // Record attempt in database
    const attempt = await prisma.assessmentAttempt.create({
      data: {
        questionnaireId,
        userId,
        score: totalScore,
        maxScore: maxPossibleScore,
        percentage,
        passed,
        isAdaptive: isAdaptive || false,
        answers: gradedAnswers,
        durationSeconds: durationSeconds || 0,
        submittedAt: new Date()
      }
    });

    let certificate = null;

    // If passed and linked to a course, auto-complete enrollment and generate Certificate!
    if (passed && questionnaire.courseId) {
      const enrollment = await prisma.enrollment.findUnique({
        where: { userId_courseId: { userId, courseId: questionnaire.courseId } }
      });

      if (enrollment) {
        await prisma.enrollment.update({
          where: { id: enrollment.id },
          data: {
            progressPercentage: 100,
            status: EnrollmentStatus.COMPLETED,
            completedAt: new Date(),
            atRisk: false
          }
        });

        // Generate verified PDF Certificate
        certificate = await CertificateService.generateCertificate(
          enrollment.id,
          userId,
          questionnaire.courseId,
          percentage
        );
      }
    }

    res.json({
      success: true,
      message: passed ? 'Congratulations! Assessment passed.' : 'Assessment completed. Score below passing threshold.',
      attempt: {
        id: attempt.id,
        score: totalScore,
        maxScore: maxPossibleScore,
        percentage,
        passed,
        passingScore: questionnaire.passingScore,
        isAdaptive: attempt.isAdaptive,
        durationSeconds: attempt.durationSeconds,
        gradedAnswers
      },
      certificateEarned: !!certificate,
      certificate
    });
  }
}
