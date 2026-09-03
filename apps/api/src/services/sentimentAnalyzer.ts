import { SentimentTag } from '@capacity-connect/shared-types';

/**
 * Lightweight Rule-Based Lexicon Sentiment Analyzer
 * Fast, self-contained sentiment scoring without external LLM latency or costs.
 */
export class SentimentAnalyzer {
  private static positiveWords = new Set([
    'excellent', 'great', 'awesome', 'good', 'clear', 'helpful', 'engaging',
    'informative', 'thorough', 'brilliant', 'insightful', 'well-structured',
    'practical', 'enjoyed', 'recommended', 'best', 'superb', 'valuable',
    'effective', 'comprehensive', 'interactive', 'crisp', 'satisfying', 'outstanding'
  ]);

  private static negativeWords = new Set([
    'poor', 'bad', 'confusing', 'unclear', 'boring', 'rushed', 'incomplete',
    'difficult', 'terrible', 'disappointing', 'shallow', 'outdated', 'hard',
    'lacking', 'frustrating', 'monotone', 'unorganized', 'slow', 'vague',
    'waste', 'horrible', 'irrelevant', 'buggy', 'glitchy'
  ]);

  private static intensifiers = new Set([
    'very', 'extremely', 'really', 'highly', 'truly', 'incredibly', 'exceptionally'
  ]);

  private static negations = new Set([
    'not', 'never', 'hardly', 'barely', 'scarcely', 'no', "didn't", "wasn't", "isn't"
  ]);

  public static analyze(text: string, rating?: number): { tag: SentimentTag; score: number } {
    if (!text || text.trim().length === 0) {
      if (rating !== undefined) {
        if (rating >= 4) return { tag: SentimentTag.POSITIVE, score: 0.8 };
        if (rating <= 2) return { tag: SentimentTag.NEGATIVE, score: -0.8 };
        return { tag: SentimentTag.NEUTRAL, score: 0.0 };
      }
      return { tag: SentimentTag.NEUTRAL, score: 0.0 };
    }

    const tokens = text.toLowerCase().replace(/[^a-z0-9\s'-]/g, ' ').split(/\s+/);
    let score = 0;
    let multiplier = 1;
    let wordCount = 0;

    for (let i = 0; i < tokens.length; i++) {
      const word = tokens[i];
      if (!word) continue;

      if (this.negations.has(word)) {
        multiplier = -1;
        continue;
      }

      if (this.intensifiers.has(word)) {
        multiplier *= 1.5;
        continue;
      }

      if (this.positiveWords.has(word)) {
        score += 1.0 * multiplier;
        wordCount++;
        multiplier = 1; // reset modifier
      } else if (this.negativeWords.has(word)) {
        score -= 1.0 * multiplier;
        wordCount++;
        multiplier = 1; // reset modifier
      }
    }

    // Blend star rating if provided (e.g. 1-5 scale)
    let normalizedScore = wordCount > 0 ? score / Math.max(1, wordCount) : 0;
    if (rating !== undefined) {
      const ratingNormalized = (rating - 3) / 2; // maps 1->-1, 3->0, 5->+1
      normalizedScore = 0.6 * normalizedScore + 0.4 * ratingNormalized;
    }

    normalizedScore = Math.max(-1, Math.min(1, normalizedScore));

    let tag = SentimentTag.NEUTRAL;
    if (normalizedScore > 0.15) {
      tag = SentimentTag.POSITIVE;
    } else if (normalizedScore < -0.15) {
      tag = SentimentTag.NEGATIVE;
    }

    return {
      tag,
      score: Math.round(normalizedScore * 100) / 100
    };
  }
}
