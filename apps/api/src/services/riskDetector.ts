import cron from 'node-cron';
import { prisma } from '../prisma/client';

export class RiskDetector {
  /**
   * Evaluates all active enrollments and flags at-risk trainees with explainable diagnostic reasons
   */
  public static async evaluateDropoutRisk(): Promise<{ evaluatedCount: number; flaggedCount: number }> {
    const activeEnrollments = await prisma.enrollment.findMany({
      where: {
        status: { in: ['ENROLLED', 'IN_PROGRESS'] }
      },
      include: {
        user: {
          include: {
            resourceAccesses: true,
            attempts: true
          }
        },
        course: {
          include: {
            resources: true,
            questionnaires: true
          }
        }
      }
    });

    const now = new Date();
    let flaggedCount = 0;

    for (const enrollment of activeEnrollments) {
      const daysSinceEnrolled = Math.max(1, Math.floor((now.getTime() - enrollment.enrolledAt.getTime()) / (1000 * 3600 * 24)));
      const daysSinceLastAccess = Math.floor((now.getTime() - enrollment.lastAccessedAt.getTime()) / (1000 * 3600 * 24));

      // Calculate resource engagement ratio
      const totalCourseResources = enrollment.course.resources.length;
      const accessedResourceIds = new Set(
        enrollment.user.resourceAccesses
          .filter((a) => enrollment.course.resources.some((r) => r.id === a.resourceId))
          .map((a) => a.resourceId)
      );
      const resourceAccessRatio = totalCourseResources > 0 ? (accessedResourceIds.size / totalCourseResources) * 100 : 100;

      // Check overdue questionnaires
      const overdueQuizzes = enrollment.course.questionnaires.filter((q) => {
        if (!q.deadline) return false;
        const isPastDeadline = q.deadline.getTime() < now.getTime();
        const hasAttempted = enrollment.user.attempts.some((att) => att.questionnaireId === q.id);
        return isPastDeadline && !hasAttempted;
      });

      let isAtRisk = false;
      const reasons: string[] = [];

      // Risk condition 1: Inactive for > 7 days after enrollment
      if (daysSinceLastAccess > 7) {
        isAtRisk = true;
        reasons.push(`Inactive for ${daysSinceLastAccess} days`);
      }

      // Risk condition 2: Very low resource engagement (< 25%) after 5+ days
      if (daysSinceEnrolled > 5 && totalCourseResources > 2 && resourceAccessRatio < 25) {
        isAtRisk = true;
        reasons.push(`Low resource access (${Math.round(resourceAccessRatio)}% completed)`);
      }

      // Risk condition 3: Missed assessment deadlines
      if (overdueQuizzes.length > 0) {
        isAtRisk = true;
        reasons.push(`${overdueQuizzes.length} overdue assessment(s)`);
      }

      // Risk condition 4: Progress stagnant at 0% after 10 days
      if (daysSinceEnrolled > 10 && enrollment.progressPercentage === 0) {
        isAtRisk = true;
        reasons.push('0% course progress after 10+ days of enrollment');
      }

      const riskReason = isAtRisk ? reasons.join('; ') : null;

      // Update enrollment risk status
      await prisma.enrollment.update({
        where: { id: enrollment.id },
        data: {
          atRisk: isAtRisk,
          riskReason
        }
      });

      if (isAtRisk) {
        flaggedCount++;
      }
    }

    return {
      evaluatedCount: activeEnrollments.length,
      flaggedCount
    };
  }

  /**
   * Initializes the cron job to run nightly at 02:00 AM
   */
  public static initCronJob() {
    // Run every day at 02:00 AM
    cron.schedule('0 2 * * *', async () => {
      console.log('[CRON] Running nightly engagement & dropout risk evaluation...');
      try {
        const stats = await this.evaluateDropoutRisk();
        console.log(`[CRON] Risk evaluation completed: ${stats.flaggedCount}/${stats.evaluatedCount} flagged as At Risk.`);
      } catch (err) {
        console.error('[CRON] Risk evaluation job failed:', err);
      }
    });

    console.log('[CRON] Risk Detector cron initialized (Daily at 02:00 AM).');
  }
}
