import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../../services/api';
import { StatCard } from '../../components/common/StatCard';
import { BookOpen, Users, Star, AlertTriangle, Library, FileCheck2, Sparkles, ArrowRight } from 'lucide-react';

export const TrainerDashboard: React.FC = () => {
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/trainer/dashboard')
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

  const stats = data?.stats || {
    totalCourses: 0,
    totalTrainees: 0,
    averageRating: 0,
    atRiskCount: 0,
    sentimentCounts: { POSITIVE: 0, NEUTRAL: 0, NEGATIVE: 0 }
  };

  const totalFeedbackCount =
    stats.sentimentCounts.POSITIVE + stats.sentimentCounts.NEUTRAL + stats.sentimentCounts.NEGATIVE;

  return (
    <div className="space-y-8">
      {/* Header Banner */}
      <div className="glass-panel p-6 sm:p-8 rounded-3xl border border-emerald-500/30 bg-gradient-to-r from-emerald-950/40 via-slate-900 to-slate-950 relative overflow-hidden">
        <span className="badge-moes text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider inline-block mb-2">
          Trainer & Instructor Console
        </span>
        <h1 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
          Capacity Building Management
        </h1>
        <p className="text-xs sm:text-sm text-slate-300 mt-2 max-w-2xl leading-relaxed">
          Monitor trainee progress across operational radar, satellite, and numerical weather prediction courses, track engagement dropout risk flags, and author adaptive assessments.
        </p>
        <div className="mt-5 flex flex-wrap gap-3">
          <Link
            to="/trainer/questionnaires"
            className="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-md shadow-emerald-600/30 transition-all flex items-center gap-1.5"
          >
            <FileCheck2 className="w-3.5 h-3.5" /> Author New Quiz / MCQ
          </Link>
          <Link
            to="/trainer/library"
            className="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-750 text-slate-200 text-xs font-semibold border border-slate-700"
          >
            Upload Study Materials
          </Link>
        </div>
      </div>

      {/* KPI Stats Grid */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          title="Authored Courses"
          value={stats.totalCourses}
          icon={BookOpen}
          iconColor="text-emerald-400"
        />
        <StatCard
          title="Enrolled Trainees"
          value={stats.totalTrainees}
          icon={Users}
          iconColor="text-imd-400"
        />
        <StatCard
          title="Average Rating"
          value={`${stats.averageRating} / 5.0`}
          icon={Star}
          iconColor="text-amber-400"
        />
        <StatCard
          title="At-Risk Trainees"
          value={stats.atRiskCount}
          icon={AlertTriangle}
          iconColor="text-rose-400"
          change={stats.atRiskCount > 0 ? 'Requires Intervention' : 'Healthy Engagement'}
          changeType={stats.atRiskCount > 0 ? 'negative' : 'positive'}
        />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left 2 Cols: My Authored Courses */}
        <div className="lg:col-span-2 space-y-4">
          <div className="flex items-center justify-between">
            <h2 className="text-base font-bold text-white flex items-center gap-2">
              <BookOpen className="w-4 h-4 text-emerald-400" /> Active Course Modules
            </h2>
            <Link to="/trainer/trainees" className="text-xs text-emerald-400 hover:underline">
              Inspect Trainee Roster
            </Link>
          </div>

          <div className="space-y-3">
            {data?.courses?.map((course: any) => (
              <div
                key={course.id}
                className="glass-card rounded-2xl p-5 border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
              >
                <div>
                  <div className="flex items-center gap-2 mb-1">
                    <span className="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300">
                      {course.code}
                    </span>
                    <span className="text-xs text-slate-400 font-medium">{course.subject}</span>
                  </div>
                  <h3 className="text-sm font-bold text-white">{course.title}</h3>
                  <div className="flex items-center gap-4 text-xs text-slate-400 mt-2">
                    <span>{course.enrollmentCount} Active Trainees</span>
                    {course.atRiskCount > 0 && (
                      <span className="badge-risk text-[10px] font-bold px-2 py-0.5 rounded">
                        ⚠️ {course.atRiskCount} At Risk
                      </span>
                    )}
                  </div>
                </div>

                <Link
                  to={`/trainer/trainees?courseId=${course.id}`}
                  className="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition-colors shrink-0"
                >
                  Monitor Progress →
                </Link>
              </div>
            ))}
          </div>
        </div>

        {/* Right Col: Feedback Sentiment Triage Card */}
        <div className="space-y-6">
          <div className="glass-card rounded-3xl p-6 border border-slate-800">
            <h3 className="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
              <Sparkles className="w-4 h-4 text-moes-500" /> Sentiment Triage
            </h3>

            <div className="space-y-3">
              <div>
                <div className="flex justify-between text-xs text-slate-300 mb-1">
                  <span className="text-emerald-400 font-semibold">Positive Reviews</span>
                  <span className="font-bold">{stats.sentimentCounts.POSITIVE}</span>
                </div>
                <div className="w-full bg-slate-800 rounded-full h-2">
                  <div
                    className="bg-emerald-500 h-2 rounded-full"
                    style={{
                      width: `${totalFeedbackCount > 0 ? (stats.sentimentCounts.POSITIVE / totalFeedbackCount) * 100 : 0}%`
                    }}
                  />
                </div>
              </div>

              <div>
                <div className="flex justify-between text-xs text-slate-300 mb-1">
                  <span className="text-amber-400 font-semibold">Neutral Feedback</span>
                  <span className="font-bold">{stats.sentimentCounts.NEUTRAL}</span>
                </div>
                <div className="w-full bg-slate-800 rounded-full h-2">
                  <div
                    className="bg-amber-400 h-2 rounded-full"
                    style={{
                      width: `${totalFeedbackCount > 0 ? (stats.sentimentCounts.NEUTRAL / totalFeedbackCount) * 100 : 0}%`
                    }}
                  />
                </div>
              </div>

              <div>
                <div className="flex justify-between text-xs text-slate-300 mb-1">
                  <span className="text-rose-400 font-semibold">Negative Feedback</span>
                  <span className="font-bold">{stats.sentimentCounts.NEGATIVE}</span>
                </div>
                <div className="w-full bg-slate-800 rounded-full h-2">
                  <div
                    className="bg-rose-500 h-2 rounded-full"
                    style={{
                      width: `${totalFeedbackCount > 0 ? (stats.sentimentCounts.NEGATIVE / totalFeedbackCount) * 100 : 0}%`
                    }}
                  />
                </div>
              </div>
            </div>

            <div className="mt-5 pt-4 border-t border-slate-800">
              <Link
                to="/trainer/feedbacks"
                className="block text-center text-xs font-semibold text-emerald-400 hover:text-emerald-300"
              >
                Inspect All Reviews & Triage →
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
