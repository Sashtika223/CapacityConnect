import { TrainerScoreBreakdown } from '@capacity-connect/shared-types';
import { prisma } from '../prisma/client';

export class CompetencyMatcher {
  /**
   * Suggests and ranks trainers for a given subject using a weighted scoring formula:
   * Score = 0.35 * (Trainee Pass Rate) + 0.30 * (Avg Feedback Rating / 5 * 100) + 0.20 * (Proficiency) + 0.15 * (Recency Score)
   */
  public static async suggestTrainersForSubject(subject: string): Promise<TrainerScoreBreakdown[]> {
    // 1. Fetch all active trainers
    const trainers = await prisma.user.findMany({
      where: {
        role: 'TRAINER',
        approvalStatus: 'APPROVED'
      },
      include: {
        competencies: {
          where: {
            subject: {
              contains: subject,
              mode: 'insensitive'
            }
          }
        },
        authoredCourses: {
          include: {
            enrollments: {
              include: {
                certificate: true
              }
            },
            feedbacks: true,
            questionnaires: {
              include: {
                attempts: true
              }
            }
          }
        },
        trainerFeedbacks: true
      }
    });

    const results: TrainerScoreBreakdown[] = [];

    const now = new Date();

    for (const trainer of trainers) {
      // Metric 1: Base proficiency from CompetencyMap or fallback
      const matchingComp = trainer.competencies[0];
      const baseProficiency = matchingComp ? matchingComp.proficiencyScore : 65;

      // Metric 2: Trainee Pass Rate across all courses & assessments
      let totalAttempts = 0;
      let passedAttempts = 0;

      for (const course of trainer.authoredCourses) {
        for (const quiz of course.questionnaires) {
          for (const attempt of quiz.attempts) {
            totalAttempts++;
            if (attempt.passed) passedAttempts++;
          }
        }
      }

      const traineePassRate = totalAttempts > 0 ? (passedAttempts / totalAttempts) * 100 : 75; // Default prior 75%

      // Metric 3: Average Feedback Rating
      const allFeedbacks = trainer.trainerFeedbacks;
      let avgRating = 4.2; // Default baseline
      if (allFeedbacks.length > 0) {
        const sum = allFeedbacks.reduce((acc, f) => acc + f.rating, 0);
        avgRating = sum / allFeedbacks.length;
      }
      const feedbackScoreOutOf100 = (avgRating / 5) * 100;

      // Metric 4: Recency of activity
      let lastActiveDate = trainer.updatedAt;
      if (trainer.authoredCourses.length > 0) {
        const sortedCourses = [...trainer.authoredCourses].sort((a, b) => b.updatedAt.getTime() - a.updatedAt.getTime());
        lastActiveDate = sortedCourses[0].updatedAt;
      }

      const daysSinceLastActive = Math.max(0, Math.floor((now.getTime() - lastActiveDate.getTime()) / (1000 * 3600 * 24)));
      // Recency factor decays over 90 days
      const recencyScore = Math.max(40, 100 - daysSinceLastActive * 0.6);

      // Weighted scoring calculation
      const traineePassFactor = Math.round(0.35 * traineePassRate * 10) / 10;
      const feedbackFactor = Math.round(0.30 * feedbackScoreOutOf100 * 10) / 10;
      const proficiencyFactor = Math.round(0.20 * baseProficiency * 10) / 10;
      const recencyFactor = Math.round(0.15 * recencyScore * 10) / 10;

      const totalScore = Math.round((traineePassFactor + feedbackFactor + proficiencyFactor + recencyFactor) * 10) / 10;

      const explanation = `Ranked #${results.length + 1} with ${totalScore}/100. Strong student outcomes (${Math.round(traineePassRate)}% pass rate, weight 35%), ${avgRating.toFixed(1)}/5 feedback score (weight 30%), ${baseProficiency}% base subject competency (weight 20%), and active in last ${daysSinceLastActive} days.`;

      results.push({
        trainerId: trainer.id,
        trainerName: trainer.name,
        department: trainer.department,
        totalScore,
        factors: {
          traineePassRateScore: traineePassFactor,
          feedbackScore: feedbackFactor,
          proficiencyScore: proficiencyFactor,
          recencyScore: recencyFactor
        },
        rawMetrics: {
          traineePassRate: Math.round(traineePassRate * 10) / 10,
          averageFeedbackRating: Math.round(avgRating * 10) / 10,
          baseProficiency,
          activeCoursesCount: trainer.authoredCourses.length,
          daysSinceLastActive
        },
        explanation
      });
    }

    // Sort descending by totalScore
    return results.sort((a, b) => b.totalScore - a.totalScore);
  }
}
