import React, { useState, useEffect } from 'react';
import { useSearchParams } from 'react-router-dom';
import { api } from '../../services/api';
import { Users, AlertTriangle, CheckCircle, Clock, ShieldAlert, RefreshCw, Activity, Search } from 'lucide-react';

export const TraineeMonitorPage: React.FC = () => {
  const [searchParams] = useSearchParams();
  const courseIdParam = searchParams.get('courseId');

  const [trainees, setTrainees] = useState<any[]>([]);
  const [stats, setStats] = useState({ totalCount: 0, atRiskCount: 0, completedCount: 0 });
  const [filterAtRisk, setFilterAtRisk] = useState(false);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const [evaluating, setEvaluating] = useState(false);
  const [evalResult, setEvalResult] = useState<any>(null);

  useEffect(() => {
    fetchTrainees();
  }, [courseIdParam]);

  const fetchTrainees = async () => {
    setLoading(true);
    try {
      const res = await api.get('/trainer/trainees', {
        params: courseIdParam ? { courseId: courseIdParam } : {}
      });
      if (res.data.success) {
        setTrainees(res.data.trainees);
        setStats({
          totalCount: res.data.totalCount,
          atRiskCount: res.data.atRiskCount,
          completedCount: res.data.completedCount
        });
      }
    } finally {
      setLoading(false);
    }
  };

  const handleTriggerRiskEvaluation = async () => {
    setEvaluating(true);
    setEvalResult(null);
    try {
      const res = await api.post('/admin/risk-evaluation/trigger');
      if (res.data.success) {
        setEvalResult(res.data.message);
        fetchTrainees();
      }
    } catch (err: any) {
      alert(err.response?.data?.message || 'Error running evaluation');
    } finally {
      setEvaluating(false);
    }
  };

  const filteredTrainees = trainees.filter((t) => {
    if (filterAtRisk && !t.atRisk) return false;
    const q = search.toLowerCase();
    return (
      t.name.toLowerCase().includes(q) ||
      t.email.toLowerCase().includes(q) ||
      (t.department && t.department.toLowerCase().includes(q)) ||
      t.courseTitle.toLowerCase().includes(q)
    );
  });

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2.5">
            <Activity className="w-6 h-6 text-rose-400" /> Trainee Participation & Risk Monitor
          </h1>
          <p className="text-xs text-slate-400 mt-1">
            Automated dropout risk detection flags low study engagement (&lt;25% resources), prolonged inactivity, and missed deadlines
          </p>
        </div>

        <button
          onClick={handleTriggerRiskEvaluation}
          disabled={evaluating}
          className="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-2 border border-slate-700 transition-colors shadow-sm disabled:opacity-50 shrink-0"
        >
          <RefreshCw className={`w-3.5 h-3.5 ${evaluating ? 'animate-spin' : ''}`} />
          {evaluating ? 'Analyzing Activity Logs...' : 'Re-Run Risk Evaluation'}
        </button>
      </div>

      {evalResult && (
        <div className="p-3.5 rounded-xl bg-imd-500/10 border border-imd-500/30 text-xs text-imd-300">
          ✨ {evalResult}
        </div>
      )}

      {/* KPI Counters */}
      <div className="grid grid-cols-3 gap-4">
        <div className="glass-card p-4 rounded-2xl border border-slate-800 text-center">
          <p className="text-[10px] uppercase font-bold text-slate-400">Total Enrolled</p>
          <p className="text-2xl font-extrabold text-white mt-1">{stats.totalCount}</p>
        </div>
        <div
          onClick={() => setFilterAtRisk(!filterAtRisk)}
          className={`glass-card p-4 rounded-2xl border text-center cursor-pointer transition-all ${
            filterAtRisk
              ? 'border-rose-500 bg-rose-500/15'
              : 'border-rose-500/30 hover:border-rose-500/50'
          }`}
        >
          <p className="text-[10px] uppercase font-bold text-rose-400 flex items-center justify-center gap-1">
            <AlertTriangle className="w-3 h-3" /> Flagged At Risk
          </p>
          <p className="text-2xl font-extrabold text-rose-400 mt-1">{stats.atRiskCount}</p>
          <p className="text-[9px] text-slate-400 mt-0.5">{filterAtRisk ? 'Showing At-Risk only' : 'Click to filter'}</p>
        </div>
        <div className="glass-card p-4 rounded-2xl border border-slate-800 text-center">
          <p className="text-[10px] uppercase font-bold text-emerald-400">Completed & Certified</p>
          <p className="text-2xl font-extrabold text-emerald-400 mt-1">{stats.completedCount}</p>
        </div>
      </div>

      {/* Search Bar */}
      <div className="glass-panel p-3 rounded-xl border border-slate-800 flex items-center gap-2">
        <Search className="w-4 h-4 text-slate-500 ml-2" />
        <input
          type="text"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Filter trainees by name, email, division..."
          className="glass-input flex-1 px-3 py-1.5 rounded-lg text-xs"
        />
      </div>

      {/* Trainees Roster Table */}
      {loading ? (
        <div className="py-20 flex justify-center">
          <div className="w-8 h-8 border-4 border-rose-500/30 border-t-rose-500 rounded-full animate-spin" />
        </div>
      ) : filteredTrainees.length === 0 ? (
        <div className="glass-card p-12 rounded-3xl text-center border border-slate-800 text-xs text-slate-400">
          No trainees match the current filters.
        </div>
      ) : (
        <div className="glass-card rounded-3xl border border-slate-800 overflow-hidden shadow-xl">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs">
              <thead className="bg-slate-900/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                <tr>
                  <th className="py-3 px-4">Trainee Name & Division</th>
                  <th className="py-3 px-4">Course Enrolled</th>
                  <th className="py-3 px-4">Course Progress</th>
                  <th className="py-3 px-4">Assessments</th>
                  <th className="py-3 px-4">Risk Diagnostic Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800/60">
                {filteredTrainees.map((t) => (
                  <tr
                    key={t.enrollmentId}
                    className={`hover:bg-slate-850 transition-colors ${
                      t.atRisk ? 'bg-rose-950/10' : ''
                    }`}
                  >
                    <td className="py-3 px-4">
                      <div className="font-bold text-white">{t.name}</div>
                      <div className="text-[11px] text-slate-400">{t.email}</div>
                      <div className="text-[10px] text-imd-400 mt-0.5">{t.department || 'IMD Operational'}</div>
                    </td>
                    <td className="py-3 px-4">
                      <div className="font-semibold text-slate-200">{t.courseTitle}</div>
                      <span className="text-[10px] px-1.5 py-0.5 rounded bg-slate-800 text-slate-400">
                        {t.courseCode}
                      </span>
                    </td>
                    <td className="py-3 px-4">
                      <div className="w-32">
                        <div className="flex justify-between text-[11px] text-slate-300 mb-1">
                          <span>{t.progressPercentage}%</span>
                          <span className="text-[10px] text-slate-400 uppercase">{t.status}</span>
                        </div>
                        <div className="w-full bg-slate-800 rounded-full h-1.5">
                          <div
                            className={`h-1.5 rounded-full ${
                              t.status === 'COMPLETED'
                                ? 'bg-emerald-500'
                                : t.atRisk
                                ? 'bg-rose-500'
                                : 'bg-imd-500'
                            }`}
                            style={{ width: `${t.progressPercentage}%` }}
                          />
                        </div>
                      </div>
                    </td>
                    <td className="py-3 px-4">
                      {t.certificateIssued ? (
                        <span className="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-bold text-[10px] inline-flex items-center gap-1">
                          🏆 Certified
                        </span>
                      ) : t.latestScore !== null ? (
                        <span className="text-slate-300 font-mono font-semibold">
                          Score: {t.latestScore}%
                        </span>
                      ) : (
                        <span className="text-slate-500">Not Attempted</span>
                      )}
                    </td>
                    <td className="py-3 px-4">
                      {t.atRisk ? (
                        <div>
                          <span className="badge-risk text-[10px] font-extrabold px-2 py-0.5 rounded inline-flex items-center gap-1">
                            <ShieldAlert className="w-3 h-3" /> Flagged At Risk
                          </span>
                          <p className="text-[10px] text-rose-300 mt-1 max-w-xs leading-tight">
                            {t.riskReason || 'Low engagement detected'}
                          </p>
                        </div>
                      ) : (
                        <span className="px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 font-semibold text-[10px] inline-flex items-center gap-1">
                          <CheckCircle className="w-3 h-3" /> On Track
                        </span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
};
