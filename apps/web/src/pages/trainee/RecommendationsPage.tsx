import React, { useState, useEffect } from 'react';
import { api } from '../../services/api';
import { Link } from 'react-router-dom';
import { Sparkles, ArrowRight, BookOpen, Clock, CheckCircle2, Compass } from 'lucide-react';

export const RecommendationsPage: React.FC = () => {
  const [recommendations, setRecommendations] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/trainee/recommendations')
      .then((res) => {
        if (res.data.success) {
          setRecommendations(res.data.recommendations || []);
        }
      })
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="py-20 flex justify-center">
        <div className="w-10 h-10 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin" />
      </div>
    );
  }

  return (
    <div className="max-w-5xl mx-auto space-y-6">
      {/* Header Banner */}
      <div className="glass-panel p-6 sm:p-8 rounded-3xl border border-purple-500/30 relative overflow-hidden bg-gradient-to-r from-purple-950/40 via-slate-900 to-slate-950">
        <div className="flex items-center gap-2 mb-2">
          <span className="px-2.5 py-1 rounded-full bg-purple-500/20 text-purple-300 text-[10px] font-extrabold uppercase tracking-wider border border-purple-500/30 flex items-center gap-1.5">
            <Sparkles className="w-3.5 h-3.5 text-purple-400" /> Vector Similarity Engine
          </span>
        </div>
        <h1 className="text-2xl font-extrabold text-white tracking-tight">
          Smart AI Course Recommendations
        </h1>
        <p className="text-xs sm:text-sm text-slate-300 mt-2 max-w-2xl leading-relaxed">
          Powered by an in-house term-frequency vector space and cosine similarity engine. We match your department, self-tagged skills, and research interests directly against course syllabus vectors.
        </p>
      </div>

      {/* List */}
      {recommendations.length === 0 ? (
        <div className="glass-card rounded-3xl p-12 text-center">
          <Sparkles className="w-12 h-12 text-slate-600 mx-auto mb-3" />
          <h3 className="text-sm font-bold text-slate-300">You are enrolled in all relevant courses!</h3>
          <p className="text-xs text-slate-500 mt-1">Check back as trainers publish new modules.</p>
        </div>
      ) : (
        <div className="space-y-4">
          {recommendations.map((rec, idx) => (
            <div
              key={rec.courseId}
              className="glass-card rounded-3xl p-6 border border-purple-500/20 hover:border-purple-500/40 transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-5"
            >
              <div className="flex-1">
                <div className="flex items-center gap-2 mb-1.5">
                  <span className="text-[10px] font-extrabold px-2 py-0.5 rounded bg-purple-500/20 text-purple-300">
                    Rank #{idx + 1}
                  </span>
                  <span className="text-xs font-bold text-slate-400">{rec.code}</span>
                  <span className="text-xs text-slate-500">•</span>
                  <span className="text-xs text-slate-400 font-medium">{rec.subject}</span>
                </div>

                <h3 className="text-base font-bold text-white">{rec.title}</h3>
                <p className="text-xs text-purple-300 mt-1.5 font-medium flex items-center gap-1.5">
                  <Sparkles className="w-3.5 h-3.5 shrink-0" /> {rec.recommendationReason}
                </p>

                {/* Matched Tags */}
                <div className="flex flex-wrap items-center gap-2 mt-3">
                  <span className="text-[10px] uppercase font-bold text-slate-500">Overlap:</span>
                  {rec.tags.map((tag: string) => {
                    const isMatched = rec.matchedTags.includes(tag);
                    return (
                      <span
                        key={tag}
                        className={`text-[10px] px-2 py-0.5 rounded-md font-medium ${
                          isMatched
                            ? 'bg-purple-500/30 text-purple-200 border border-purple-500/40 font-bold'
                            : 'bg-slate-800 text-slate-400'
                        }`}
                      >
                        {tag}
                      </span>
                    );
                  })}
                </div>
              </div>

              {/* Match Score & Action */}
              <div className="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-800 shrink-0">
                <div className="text-right">
                  <div className="text-2xl font-black text-white">
                    {Math.round(rec.similarityScore * 100)}%
                  </div>
                  <div className="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
                    Similarity Score
                  </div>
                </div>

                <Link
                  to={`/courses/${rec.courseId}`}
                  className="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-lg shadow-purple-600/30 flex items-center gap-1.5 transition-all"
                >
                  Inspect & Enroll <ArrowRight className="w-3.5 h-3.5" />
                </Link>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};
