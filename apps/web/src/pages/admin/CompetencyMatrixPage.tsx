import React, { useState, useEffect } from 'react';
import { api } from '../../services/api';
import { BrainCircuit, Search, Star, Award, TrendingUp, Clock, CheckCircle2, Sparkles, ChevronRight } from 'lucide-react';

export const CompetencyMatrixPage: React.FC = () => {
  const [subject, setSubject] = useState('Radar Meteorology');
  const [suggestions, setSuggestions] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    handleSearch();
  }, []);

  const handleSearch = async (e?: React.FormEvent) => {
    if (e) e.preventDefault();
    if (!subject.trim()) return;

    setLoading(true);
    try {
      const res = await api.get(`/admin/competency-map/suggest?subject=${encodeURIComponent(subject)}`);
      if (res.data.success) {
        setSuggestions(res.data.suggestions || []);
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6 max-w-5xl mx-auto">
      {/* Header Banner */}
      <div className="glass-panel p-6 sm:p-8 rounded-3xl border border-purple-500/30 bg-gradient-to-r from-purple-950/40 via-slate-900 to-slate-950">
        <div className="flex items-center gap-2 mb-2">
          <span className="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase tracking-wider flex items-center gap-1.5">
            <BrainCircuit className="w-3.5 h-3.5" /> AI Competency Matcher Engine
          </span>
        </div>
        <h1 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
          Competency-to-Trainer Assignment Matrix
        </h1>
        <p className="text-xs sm:text-sm text-slate-300 mt-2 max-w-2xl leading-relaxed">
          Scores and auto-suggests best-fit IMD faculty using a multi-factor weighted formula: Trainee Pass Rate (35%), Student Feedback Rating (30%), Verified Subject Proficiency (20%), and Recency of Activity (15%).
        </p>

        {/* Search Bar */}
        <form onSubmit={handleSearch} className="mt-6 flex gap-3 max-w-xl">
          <div className="relative flex-1">
            <Search className="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              value={subject}
              onChange={(e) => setSubject(e.target.value)}
              placeholder="Search subject (e.g. Radar Meteorology, NWP, Satellite)..."
              className="glass-input w-full pl-10 pr-4 py-2.5 rounded-xl text-xs sm:text-sm"
            />
          </div>
          <button
            type="submit"
            disabled={loading}
            className="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 shrink-0 disabled:opacity-50"
          >
            {loading ? 'Evaluating...' : 'Match Trainers'}
          </button>
        </form>

        {/* Preset Topic Pills */}
        <div className="flex flex-wrap items-center gap-2 mt-4 text-xs">
          <span className="text-slate-400 text-[11px] font-medium">Quick Domains:</span>
          {['Radar Meteorology', 'Satellite Meteorology', 'Numerical Weather Prediction (NWP)'].map((sub) => (
            <button
              key={sub}
              type="button"
              onClick={() => { setSubject(sub); }}
              className="text-[10px] px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-purple-600/30 text-slate-300 hover:text-white border border-slate-700/60 transition-colors"
            >
              {sub}
            </button>
          ))}
        </div>
      </div>

      {/* Results Stream */}
      {loading ? (
        <div className="py-20 flex justify-center">
          <div className="w-10 h-10 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin" />
        </div>
      ) : suggestions.length === 0 ? (
        <div className="glass-card p-12 rounded-3xl text-center border border-slate-800 text-slate-400 text-xs">
          No candidate trainers found for "{subject}".
        </div>
      ) : (
        <div className="space-y-4">
          {suggestions.map((trainer, idx) => (
            <div
              key={trainer.trainerId}
              className={`glass-card rounded-3xl p-6 border transition-all ${
                idx === 0
                  ? 'border-purple-500/40 bg-gradient-to-r from-purple-950/20 via-slate-900 to-slate-900'
                  : 'border-slate-800'
              }`}
            >
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <div>
                  <div className="flex items-center gap-2 mb-1">
                    <span className={`text-[10px] font-extrabold px-2.5 py-0.5 rounded-full ${
                      idx === 0 ? 'bg-purple-500/30 text-purple-200 border border-purple-500/40' : 'bg-slate-800 text-slate-400'
                    }`}>
                      Rank #{idx + 1} Best Match
                    </span>
                    <span className="text-xs text-slate-400 font-medium">{trainer.department}</span>
                  </div>
                  <h3 className="text-lg font-bold text-white">{trainer.trainerName}</h3>
                </div>

                <div className="text-left md:text-right shrink-0">
                  <div className="text-3xl font-black text-white flex items-baseline md:justify-end gap-1">
                    <span>{trainer.totalScore}</span>
                    <span className="text-xs text-purple-400 font-bold">/ 100</span>
                  </div>
                  <span className="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
                    Composite Suitability Index
                  </span>
                </div>
              </div>

              {/* Explainable Metric Breakdown Bars */}
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80 mb-4">
                <div>
                  <div className="flex justify-between text-[11px] text-slate-300 mb-1">
                    <span>Trainee Pass Rate (35%)</span>
                    <strong className="text-white">{trainer.rawMetrics.traineePassRate}%</strong>
                  </div>
                  <div className="w-full bg-slate-800 rounded-full h-1.5">
                    <div
                      className="bg-imd-500 h-1.5 rounded-full"
                      style={{ width: `${Math.min(100, trainer.rawMetrics.traineePassRate)}%` }}
                    />
                  </div>
                </div>

                <div>
                  <div className="flex justify-between text-[11px] text-slate-300 mb-1">
                    <span>Feedback Score (30%)</span>
                    <strong className="text-amber-400">{trainer.rawMetrics.averageFeedbackRating} / 5</strong>
                  </div>
                  <div className="w-full bg-slate-800 rounded-full h-1.5">
                    <div
                      className="bg-amber-400 h-1.5 rounded-full"
                      style={{ width: `${(trainer.rawMetrics.averageFeedbackRating / 5) * 100}%` }}
                    />
                  </div>
                </div>

                <div>
                  <div className="flex justify-between text-[11px] text-slate-300 mb-1">
                    <span>Domain Skill (20%)</span>
                    <strong className="text-emerald-400">{trainer.rawMetrics.baseProficiency}%</strong>
                  </div>
                  <div className="w-full bg-slate-800 rounded-full h-1.5">
                    <div
                      className="bg-emerald-500 h-1.5 rounded-full"
                      style={{ width: `${trainer.rawMetrics.baseProficiency}%` }}
                    />
                  </div>
                </div>

                <div>
                  <div className="flex justify-between text-[11px] text-slate-300 mb-1">
                    <span>Recency Factor (15%)</span>
                    <strong className="text-purple-300">{trainer.rawMetrics.daysSinceLastActive}d ago</strong>
                  </div>
                  <div className="w-full bg-slate-800 rounded-full h-1.5">
                    <div
                      className="bg-purple-500 h-1.5 rounded-full"
                      style={{ width: `${Math.max(20, 100 - trainer.rawMetrics.daysSinceLastActive * 0.6)}%` }}
                    />
                  </div>
                </div>
              </div>

              {/* Explainable Text Summary */}
              <div className="p-3.5 rounded-xl bg-purple-950/20 border border-purple-500/20 text-xs text-purple-200 leading-relaxed font-medium">
                <Sparkles className="w-4 h-4 inline mr-1.5 text-purple-400" />
                {trainer.explanation}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};
