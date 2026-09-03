import { QuestionDifficulty } from '@capacity-connect/shared-types';

export interface AnswerHistoryItem {
  questionId: string;
  difficulty: QuestionDifficulty;
  isCorrect: boolean;
  timeSpentSeconds?: number;
}

export class AdaptiveEngine {
  /**
   * Determines the next target difficulty based on running accuracy and streak (IRT-lite)
   */
  public static determineNextDifficulty(
    history: AnswerHistoryItem[],
    currentDifficulty: QuestionDifficulty = QuestionDifficulty.MEDIUM
  ): {
    nextDifficulty: QuestionDifficulty;
    runningAccuracy: number;
    currentStreak: number;
    estimatedAbility: 'BEGINNER' | 'INTERMEDIATE' | 'EXPERT';
  } {
    if (!history || history.length === 0) {
      return {
        nextDifficulty: QuestionDifficulty.MEDIUM,
        runningAccuracy: 0,
        currentStreak: 0,
        estimatedAbility: 'INTERMEDIATE'
      };
    }

    const totalAnswered = history.length;
    const correctCount = history.filter((h) => h.isCorrect).length;
    const runningAccuracy = Math.round((correctCount / totalAnswered) * 100);

    // Calculate current streak (positive for consecutive correct, negative for incorrect)
    let streak = 0;
    for (let i = history.length - 1; i >= 0; i--) {
      if (history[i].isCorrect) {
        if (streak >= 0) streak++;
        else break;
      } else {
        if (streak <= 0) streak--;
        else break;
      }
    }

    let nextDifficulty = currentDifficulty;

    if (streak >= 2) {
      // Upgrade difficulty
      if (currentDifficulty === QuestionDifficulty.EASY) {
        nextDifficulty = QuestionDifficulty.MEDIUM;
      } else if (currentDifficulty === QuestionDifficulty.MEDIUM) {
        nextDifficulty = QuestionDifficulty.HARD;
      }
    } else if (streak <= -1) {
      // Downgrade difficulty
      if (currentDifficulty === QuestionDifficulty.HARD) {
        nextDifficulty = QuestionDifficulty.MEDIUM;
      } else if (currentDifficulty === QuestionDifficulty.MEDIUM) {
        nextDifficulty = QuestionDifficulty.EASY;
      }
    }

    let estimatedAbility: 'BEGINNER' | 'INTERMEDIATE' | 'EXPERT' = 'INTERMEDIATE';
    if (runningAccuracy >= 75 && nextDifficulty === QuestionDifficulty.HARD) {
      estimatedAbility = 'EXPERT';
    } else if (runningAccuracy < 50 && nextDifficulty === QuestionDifficulty.EASY) {
      estimatedAbility = 'BEGINNER';
    }

    return {
      nextDifficulty,
      runningAccuracy,
      currentStreak: streak,
      estimatedAbility
    };
  }

  /**
   * Selects the next best question from the remaining available pool
   */
  public static selectNextQuestion(
    availableQuestions: any[],
    targetDifficulty: QuestionDifficulty,
    answeredQuestionIds: Set<string>
  ): any | null {
    const unasked = availableQuestions.filter((q) => !answeredQuestionIds.has(q.id));
    if (unasked.length === 0) return null;

    // 1. Try exact difficulty match
    const exactMatches = unasked.filter((q) => q.difficulty === targetDifficulty);
    if (exactMatches.length > 0) {
      return exactMatches[Math.floor(Math.random() * exactMatches.length)];
    }

    // 2. Fallback to adjacent difficulty
    if (targetDifficulty === QuestionDifficulty.HARD) {
      const mediumMatches = unasked.filter((q) => q.difficulty === QuestionDifficulty.MEDIUM);
      if (mediumMatches.length > 0) return mediumMatches[0];
    } else if (targetDifficulty === QuestionDifficulty.EASY) {
      const mediumMatches = unasked.filter((q) => q.difficulty === QuestionDifficulty.MEDIUM);
      if (mediumMatches.length > 0) return mediumMatches[0];
    }

    // 3. Fallback to any remaining question
    return unasked[0];
  }
}
