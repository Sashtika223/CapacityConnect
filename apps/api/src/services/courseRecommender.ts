import { RecommendationResult } from '@capacity-connect/shared-types';

export class CourseRecommender {
  /**
   * Tokenizes text into normalized unique word stems
   */
  private static tokenize(text: string): string[] {
    return text
      .toLowerCase()
      .replace(/[^a-z0-9\s]/g, ' ')
      .split(/\s+/)
      .filter((w) => w.length > 2);
  }

  /**
   * Computes Cosine Similarity between two term-frequency maps
   */
  private static cosineSimilarity(vecA: Map<string, number>, vecB: Map<string, number>): number {
    let dotProduct = 0;
    let normA = 0;
    let normB = 0;

    for (const val of vecA.values()) {
      normA += val * val;
    }
    for (const val of vecB.values()) {
      normB += val * val;
    }

    if (normA === 0 || normB === 0) return 0;

    for (const [key, valA] of vecA.entries()) {
      if (vecB.has(key)) {
        dotProduct += valA * (vecB.get(key) || 0);
      }
    }

    return dotProduct / (Math.sqrt(normA) * Math.sqrt(normB));
  }

  /**
   * Builds TF vector with weight multiplier for specific high-signal fields (skills/interests)
   */
  private static buildVector(terms: string[], weightMultiplier: number = 1): Map<string, number> {
    const vec = new Map<string, number>();
    for (const term of terms) {
      const tokens = this.tokenize(term);
      for (const t of tokens) {
        vec.set(t, (vec.get(t) || 0) + weightMultiplier);
      }
    }
    return vec;
  }

  /**
   * Recommends courses for a user based on profile skills, interests, and past enrollments
   */
  public static recommend(
    userProfile: { interests: string[]; skills: string[]; department?: string | null },
    courses: any[],
    enrolledCourseIds: Set<string>
  ): RecommendationResult[] {
    // 1. Build trainee profile vector (skills weighted 2x, interests weighted 1.5x)
    const userVec = new Map<string, number>();

    const skillTokens = this.buildVector(userProfile.skills || [], 2.0);
    const interestTokens = this.buildVector(userProfile.interests || [], 1.5);
    const deptTokens = userProfile.department ? this.buildVector([userProfile.department], 1.2) : new Map();

    for (const [k, v] of skillTokens.entries()) userVec.set(k, (userVec.get(k) || 0) + v);
    for (const [k, v] of interestTokens.entries()) userVec.set(k, (userVec.get(k) || 0) + v);
    for (const [k, v] of deptTokens.entries()) userVec.set(k, (userVec.get(k) || 0) + v);

    const recommendations: RecommendationResult[] = [];

    for (const course of courses) {
      // Exclude already enrolled courses
      if (enrolledCourseIds.has(course.id)) continue;

      const courseTags: string[] = Array.isArray(course.tags) ? course.tags : [];
      const courseVec = new Map<string, number>();

      const tagTokens = this.buildVector(courseTags, 2.0);
      const subjectTokens = this.buildVector([course.subject || ''], 1.5);
      const categoryTokens = this.buildVector([course.category || ''], 1.2);
      const descTokens = this.buildVector([course.title || '', course.description || ''], 1.0);

      for (const [k, v] of tagTokens.entries()) courseVec.set(k, (courseVec.get(k) || 0) + v);
      for (const [k, v] of subjectTokens.entries()) courseVec.set(k, (courseVec.get(k) || 0) + v);
      for (const [k, v] of categoryTokens.entries()) courseVec.set(k, (courseVec.get(k) || 0) + v);
      for (const [k, v] of descTokens.entries()) courseVec.set(k, (courseVec.get(k) || 0) + v);

      const simScore = this.cosineSimilarity(userVec, courseVec);

      // Find matching tag intersections
      const matched = courseTags.filter((tag) => {
        const tTokens = this.tokenize(tag);
        return tTokens.some((tok) => userVec.has(tok));
      });

      let reason = 'Recommended based on overall departmental relevance';
      if (matched.length > 0) {
        reason = `Matches your focus in ${matched.slice(0, 3).join(', ')}`;
      } else if (simScore > 0.2) {
        reason = `Relevant to your domain in ${course.subject}`;
      }

      recommendations.push({
        courseId: course.id,
        title: course.title,
        code: course.code,
        category: course.category,
        subject: course.subject,
        level: course.level,
        durationHours: course.durationHours,
        tags: courseTags,
        similarityScore: Math.round(simScore * 100) / 100,
        matchedTags: matched,
        recommendationReason: reason
      });
    }

    // Sort descending by similarity score
    return recommendations.sort((a, b) => b.similarityScore - a.similarityScore);
  }
}
