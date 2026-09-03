import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../../services/api';
import { StatCard } from '../../components/common/StatCard';
import { BookOpen, Award, CheckCircle, Clock, Sparkles, ArrowRight, Play, AlertCircle, TrendingUp } from 'lucide-react';

export const TraineeDashboard: React.FC = () => {
  const [data, setData] = useState<any>(null);
  const [recommendations, setRecommendations] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      api.get('/trainee/dashboard'),
      api.get('/trainee/recommendations')
    ])
      .then(([dashRes, recRes]) => {
        if (dashRes.data.success) setData(dashRes.data);
        if (recRes.data.success) setRecommendations(recRes.data.recommendations);
      })
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="py-20 flex justify-center">
        <div className="w-10 h-10 border-4 border-imd-500/30 border-t-imd-500 rounded-full animate-spin" />
      </div>
    );
  }

  const stats = data?.stats || {
    totalEnrolled: 0,
    activeCourses: 0,
    completedCourses: 0,
    averageProgress: 0,
    certificatesEarned: 0
  };

  return (
    <div className="space-y-8">
      {/* Welcome Banner */}
      <div className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-imd-900/60 via-slate-900 to-moes-950/40 p-6 sm:p-8 border border-slate-800 shadow-xl">
        <div className="relative z-10 max-w-2xl">
          <span className="badge-imd text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider inline-block mb-2">
            IMD Capacity Development Portal
          </span>
          <h1 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
            Welcome back, Forecaster
          </h1>
          <p className="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
            Enhance your scientific and forecasting skills with accredited modules in Doppler Radar, Satellite interpretation, and Numerical Weather Prediction.
          </p>
          <div className="mt-5 flex flex-wrap items-center gap-3">
            <Link
              to="/courses"
              className="px-4 py-2.5 rounded-xl bg-imd-600 hover:bg-imd-500 text-white text-xs font-semibold shadow-md shadow-imd-600/30 transition-all flex items-center gap-1.5"
            >
              Browse Course Catalog <ArrowRight className="w-3.5 h-3.5" />
            </Link>
            <Link
              to="/library"
              className="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-colors"
            >
              Trainer Study Library
            </Link>
          </div>
        </div>
      </div>

      {/* KPI Stats Grid */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          title="Enrolled Courses"
          value={stats.totalEnrolled}
          icon={BookOpen}
          iconColor="text-imd-400"
        />
        <StatCard
          title="Avg Progress"
          value={`${stats.averageProgress}%`}
          icon={TrendingUp}
          iconColor="text-moes-500"
          change={`${stats.completedCourses} Completed`}
          changeType="positive"
        />
        <StatCard
          title="Certificates"
          value={stats.certificatesEarned}
          icon={Award}
          iconColor="text-amber-400"
        />
        <StatCard
          title="Upcoming Quizzes"
          value={data?.upcomingQuizzes?.length || 0}
          icon={Clock}
          iconColor="text-purple-400"
        />
      </div>

      {/* Main Content Layout */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left 2 Cols: My Active Enrollments */}
        <div className="lg:col-span-2 space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-base font-bold text-white flex items-center gap-2">
              <BookOpen className="w-4 h-4 text-imd-400" /> My Current Enrollments
            </h2>
            <Link to="/courses" className="text-xs text-imd-400 hover:underline">
              Explore More
            </Link>
          </div>

          {data?.enrollments?.length === 0 ? (
            <div className="glass-card rounded-2xl p-8 text-center">
              <p className="text-xs text-slate-400 mb-3">You are not enrolled in any courses yet.</p>
              <Link
                to="/courses"
                className="px-4 py-2 rounded-xl bg-imd-600 text-white text-xs font-semibold"
              >
                Enroll in your first course
              </Link>
            </div>
          ) : (
            <div className="space-y-4">
              {data?.enrollments?.map((e: any) => (
                <div
                  key={e.id}
                  className="glass-card rounded-2xl p-5 border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                >
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2 mb-1">
                      <span className="text-[10px] font-bold px-2 py-0.5 rounded bg-imd-500/20 text-imd-300">
                        {e.course.code}
                      </span>
                      <span className="text-[11px] text-slate-400 font-medium">
                        {e.course.subject}
                      </span>
                      {e.atRisk && (
                        <span className="badge-risk text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1">
                          <AlertCircle className="w-3 h-3" /> Engagement Risk
                        </span>
                      )}
                    </div>
                    <h3 className="text-sm font-bold text-white truncate">{e.course.title}</h3>
                    <p className="text-[11px] text-slate-400 mt-1">
                      Instructor: {e.course.trainer.name} ({e.course.trainer.department})
                    </p>

                    {/* Progress Bar */}
                    <div className="mt-3 flex items-center gap-3">
                      <div className="flex-1 bg-slate-800 rounded-full h-2 overflow-hidden">
                        <div
                          className="bg-gradient-to-r from-imd-500 to-moes-500 h-full rounded-full transition-all duration-500"
                          style={{ width: `${e.progressPercentage}%` }}
                        />
                      </div>
                      <span className="text-xs font-bold text-slate-300 shrink-0">
                        {e.progressPercentage}%
                      </span>
                    </div>

                    {e.atRisk && e.riskReason && (
                      <p className="text-[10px] text-rose-400 mt-1.5 font-medium">
                        ⚠️ Reason: {e.riskReason}
                      </p>
                    )}
                  </div>

                  <div className="flex items-center gap-2 shrink-0">
                    <Link
                      to={`/courses/${e.course.id}`}
                      className="px-3.5 py-2 rounded-xl bg-imd-600 hover:bg-imd-500 text-white text-xs font-semibold flex items-center gap-1.5 shadow-md shadow-imd-600/20 transition-all"
                    >
                      <Play className="w-3.5 h-3.5" /> Continue
                    </Link>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* AI Course Recommendations Section */}
          {recommendations.length > 0 && (
            <div className="pt-2">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-base font-bold text-white flex items-center gap-2">
                  <Sparkles className="w-4 h-4 text-purple-400" /> AI Skill-Matched Courses
                </h2>
                <Link to="/recommendations" className="text-xs text-purple-400 hover:underline">
                  View Matching Engine
                </Link>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {recommendations.slice(0, 2).map((rec: any) => (
                  <div key={rec.courseId} className="glass-card rounded-2xl p-4 border border-purple-500/20">
                    <div className="flex items-center justify-between text-[10px] mb-1.5">
                      <span className="font-bold text-purple-300 uppercase tracking-wider">{rec.category}</span>
                      <span className="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-bold">
                        {Math.round(rec.similarityScore * 100)}% Match
                      </span>
                    </div>
                    <h4 className="text-xs font-bold text-white line-clamp-2">{rec.title}</h4>
                    <p className="text-[11px] text-slate-400 mt-1 line-clamp-1">{rec.recommendationReason}</p>
                    <Link
                      to={`/courses/${rec.courseId}`}
                      className="mt-3 block text-center text-xs font-semibold py-1.5 px-3 rounded-lg bg-purple-600/30 hover:bg-purple-600/50 text-purple-200 border border-purple-500/30 transition-colors"
                    >
                      Inspect Course
                    </Link>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>

        {/* Right Col: Quizzes & Achievements */}
        <div className="space-y-6">
          {/* Upcoming MCQ Deadlines */}
          <div className="glass-card rounded-3xl p-5 border border-slate-800">
            <h3 className="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
              <Clock className="w-4 h-4 text-purple-400" /> Assessment Deadlines
            </h3>
            {data?.upcomingQuizzes?.length === 0 ? (
              <p className="text-xs text-slate-500 py-4 text-center">No pending assessments.</p>
            ) : (
              <div className="space-y-3">
                {data?.upcomingQuizzes?.map((q: any) => (
                  <div key={q.id} className="p-3 rounded-xl bg-slate-900/80 border border-slate-800">
                    <div className="flex items-center justify-between text-[10px] text-slate-400">
                      <span>{q.courseCode}</span>
                      <span className="text-purple-300 font-semibold">{q.durationMinutes} Mins</span>
                    </div>
                    <h4 className="text-xs font-semibold text-slate-200 mt-1 truncate">{q.title}</h4>
                    <div className="mt-2.5 flex items-center justify-between">
                      {q.hasAttempted ? (
                        <span className="text-[10px] text-emerald-400 flex items-center gap-1 font-semibold">
                          <CheckCircle className="w-3 h-3" /> Attempted
                        </span>
                      ) : (
                        <span className="text-[10px] text-amber-400">Pending Attempt</span>
                      )}
                      <Link
                        to={`/assessments/${q.id}`}
                        className="text-[11px] font-semibold text-imd-400 hover:text-imd-300"
                      >
                        Start Test →
                      </Link>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Badges & Achievements */}
          <div className="glass-card rounded-3xl p-5 border border-slate-800">
            <h3 className="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
              <Award className="w-4 h-4 text-amber-400" /> Earned Badges
            </h3>
            {data?.achievements?.length === 0 ? (
              <p className="text-xs text-slate-500 py-4 text-center">Complete courses to unlock badges.</p>
            ) : (
              <div className="space-y-2.5">
                {data?.achievements?.map((ach: any) => (
                  <div key={ach.id} className="flex items-center gap-3 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                    <div className="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 shrink-0">
                      <Award className="w-4 h-4" />
                    </div>
                    <div>
                      <h5 className="text-xs font-bold text-slate-200">{ach.title}</h5>
                      <p className="text-[10px] text-slate-400">{ach.description}</p>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};
