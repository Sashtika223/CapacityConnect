import React, { useState, useEffect } from 'react';
import { api } from '../../services/api';
import { Sparkles, Star, MessageSquare, ThumbsUp, MinusCircle, ThumbsDown } from 'lucide-react';

export const FeedbackAnalyticsPage: React.FC = () => {
  const [data, setData] = useState<any>(null);
  const [filterTag, setFilterTag] = useState<string>('ALL');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/trainer/feedbacks')
      .then((res) => {
        if (res.data.success) setData(res.data);
      })
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="py-20 flex justify-center">
        <div className="w-10 h-10 border-4 border-emerald-500/30 border-t-emerald-500 rounded-full animate-spin" />
      </div>
    );
  }

  const metrics = data?.metrics || {
    totalReviews: 0,
    avgOverall: 0,
    avgContent: 0,
    avgDelivery: 0,
    sentimentBreakdown: { POSITIVE: 0, NEUTRAL: 0, NEGATIVE: 0 }
  };

  const feedbacks = data?.feedbacks || [];
  const filteredFeedbacks = feedbacks.filter((f: any) => {
    if (filterTag !== 'ALL' && f.sentimentTag !== filterTag) return false;
    return true;
  });

  const getSentimentBadge = (tag: string, score: number) => {
    switch (tag) {
      case 'POSITIVE':
        return (
          <span className="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-extrabold flex items-center gap-1">
            <ThumbsUp className="w-3 h-3" /> Positive ({score > 0 ? `+${score}` : score})
          </span>
        );
      case 'NEGATIVE':
        return (
          <span className="px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-extrabold flex items-center gap-1">
            <ThumbsDown className="w-3 h-3" /> Negative ({score})
          </span>
        );
      default:
        return (
          <span className="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-extrabold flex items-center gap-1">
            <MinusCircle className="w-3 h-3" /> Neutral ({score})
          </span>
        );
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
          <Sparkles className="w-6 h-6 text-moes-500" /> Feedback Sentiment Analytics
        </h1>
        <p className="text-xs text-slate-400 mt-1">
          Rule-based lexicon sentiment scoring auto-triages student reviews without external API dependencies or latency
        </p>
      </div>

      {/* Metric Cards */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div className="glass-card p-4 rounded-2xl border border-slate-800 text-center">
          <p className="text-[10px] uppercase font-bold text-slate-400">Total Reviews</p>
          <p className="text-2xl font-extrabold text-white mt-1">{metrics.totalReviews}</p>
        </div>
        <div className="glass-card p-4 rounded-2xl border border-slate-800 text-center">
          <p className="text-[10px] uppercase font-bold text-amber-400">Avg Overall Rating</p>
          <p className="text-2xl font-extrabold text-amber-400 mt-1">{metrics.avgOverall} / 5</p>
        </div>
        <div className="glass-card p-4 rounded-2xl border border-slate-800 text-center">
          <p className="text-[10px] uppercase font-bold text-emerald-400">Content Quality</p>
          <p className="text-2xl font-extrabold text-emerald-400 mt-1">{metrics.avgContent} / 5</p>
        </div>
        <div className="glass-card p-4 rounded-2xl border border-slate-800 text-center">
          <p className="text-[10px] uppercase font-bold text-imd-400">Delivery Quality</p>
          <p className="text-2xl font-extrabold text-imd-400 mt-1">{metrics.avgDelivery} / 5</p>
        </div>
      </div>

      {/* Filter Tabs */}
      <div className="flex items-center gap-2 border-b border-slate-800 pb-3">
        <button
          onClick={() => setFilterTag('ALL')}
          className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
            filterTag === 'ALL' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white'
          }`}
        >
          All Feedback ({feedbacks.length})
        </button>
        <button
          onClick={() => setFilterTag('POSITIVE')}
          className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
            filterTag === 'POSITIVE' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'text-slate-400 hover:text-emerald-400'
          }`}
        >
          Positive ({metrics.sentimentBreakdown.POSITIVE})
        </button>
        <button
          onClick={() => setFilterTag('NEUTRAL')}
          className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
            filterTag === 'NEUTRAL' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'text-slate-400 hover:text-amber-400'
          }`}
        >
          Neutral ({metrics.sentimentBreakdown.NEUTRAL})
        </button>
        <button
          onClick={() => setFilterTag('NEGATIVE')}
          className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
            filterTag === 'NEGATIVE' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'text-slate-400 hover:text-rose-400'
          }`}
        >
          Negative ({metrics.sentimentBreakdown.NEGATIVE})
        </button>
      </div>

      {/* Feedbacks Stream */}
      <div className="space-y-3">
        {filteredFeedbacks.length === 0 ? (
          <div className="glass-card p-10 rounded-3xl text-center text-xs text-slate-500">
            No feedback entries in this category.
          </div>
        ) : (
          filteredFeedbacks.map((f: any) => (
            <div key={f.id} className="glass-card rounded-2xl p-5 border border-slate-800 space-y-3">
              <div className="flex items-center justify-between gap-3">
                <div className="flex items-center gap-2">
                  <div className="flex items-center gap-1">
                    {[1, 2, 3, 4, 5].map((s) => (
                      <Star
                        key={s}
                        className={`w-3.5 h-3.5 ${
                          s <= f.rating ? 'text-amber-400 fill-amber-400' : 'text-slate-700'
                        }`}
                      />
                    ))}
                  </div>
                  <span className="text-xs font-bold text-white ml-1">{f.course?.title}</span>
                </div>
                {getSentimentBadge(f.sentimentTag, f.sentimentScore)}
              </div>

              <p className="text-xs text-slate-200 leading-relaxed font-medium bg-slate-900/50 p-3 rounded-xl border border-slate-800/60">
                "{f.comments}"
              </p>

              <div className="flex items-center justify-between text-[11px] text-slate-400 pt-1">
                <span>By: {f.user?.name} ({f.user?.department || 'Trainee'})</span>
                <span>{new Date(f.createdAt).toLocaleDateString()}</span>
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  );
};
