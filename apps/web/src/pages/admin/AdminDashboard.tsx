import React, { useState, useEffect } from 'react';
import { api } from '../../services/api';
import { StatCard } from '../../components/common/StatCard';
import {
  Users,
  BookOpen,
  Award,
  TrendingUp,
  AlertTriangle,
  PieChart as PieChartIcon,
  BarChart2,
  ShieldCheck,
  CheckCircle2
} from 'lucide-react';
import {
  AreaChart,
  Area,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  BarChart,
  Bar,
  PieChart,
  Pie,
  Cell,
  Legend
} from 'recharts';

export const AdminDashboard: React.FC = () => {
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/admin/analytics')
      .then((res) => {
        if (res.data.success) setData(res.data);
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

  const summary = data?.summary || {
    totalUsers: 0,
    totalTrainees: 0,
    totalTrainers: 0,
    totalCourses: 0,
    totalEnrollments: 0,
    totalCertificates: 0,
    completionRate: 0,
    atRiskCount: 0
  };

  const COLORS = ['#0c8ee9', '#10b981', '#8b5cf6', '#f59e0b', '#ec4899'];

  return (
    <div className="space-y-8">
      {/* Admin Executive Banner */}
      <div className="glass-panel p-6 sm:p-8 rounded-3xl border border-purple-500/30 bg-gradient-to-r from-purple-950/40 via-slate-900 to-slate-950 relative overflow-hidden">
        <span className="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase tracking-wider inline-block mb-2">
          Ministry of Earth Sciences • Executive Analytics
        </span>
        <h1 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
          National Capacity Analytics & Monitoring
        </h1>
        <p className="text-xs sm:text-sm text-slate-300 mt-2 max-w-2xl leading-relaxed">
          Comprehensive real-time telemetry across IMD meteorological capacity building, active enrollee engagement ratios, and certified competencies.
        </p>
      </div>

      {/* KPI Counters */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          title="Total IMD Personnel"
          value={summary.totalUsers}
          icon={Users}
          iconColor="text-imd-400"
          change={`${summary.totalTrainers} Trainers • ${summary.totalTrainees} Trainees`}
        />
        <StatCard
          title="Enrollment Count"
          value={summary.totalEnrollments}
          icon={BookOpen}
          iconColor="text-emerald-400"
          change={`${summary.completionRate}% Completion Rate`}
          changeType="positive"
        />
        <StatCard
          title="Certificates Issued"
          value={summary.totalCertificates}
          icon={Award}
          iconColor="text-amber-400"
        />
        <StatCard
          title="Dropout Risk Alerts"
          value={summary.atRiskCount}
          icon={AlertTriangle}
          iconColor="text-rose-400"
          change={summary.atRiskCount > 0 ? 'Active Interventions' : 'All Clear'}
          changeType={summary.atRiskCount > 0 ? 'negative' : 'positive'}
        />
      </div>

      {/* Recharts Analytics Row 1 */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Enrollments vs Completions Trend */}
        <div className="lg:col-span-2 glass-card rounded-3xl p-6 border border-slate-800">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h3 className="text-sm font-bold text-white flex items-center gap-2">
                <TrendingUp className="w-4 h-4 text-imd-400" /> Capacity Growth & Certification Trend
              </h3>
              <p className="text-[11px] text-slate-400">Monthly enrollments versus verified course completions</p>
            </div>
          </div>

          <div className="h-64 w-full">
            <ResponsiveContainer width="100%" height="100%">
              <AreaChart data={data?.enrollmentTrends || []}>
                <defs>
                  <linearGradient id="colorEnrolls" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#0c8ee9" stopOpacity={0.4}/>
                    <stop offset="95%" stopColor="#0c8ee9" stopOpacity={0}/>
                  </linearGradient>
                  <linearGradient id="colorComps" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#10b981" stopOpacity={0.4}/>
                    <stop offset="95%" stopColor="#10b981" stopOpacity={0}/>
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" stroke="#1e293b" />
                <XAxis dataKey="month" stroke="#64748b" textAnchor="end" tick={{ fontSize: 11 }} />
                <YAxis stroke="#64748b" tick={{ fontSize: 11 }} />
                <Tooltip contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '12px' }} />
                <Area type="monotone" dataKey="enrollments" stroke="#0c8ee9" fillOpacity={1} fill="url(#colorEnrolls)" name="Enrollments" />
                <Area type="monotone" dataKey="completions" stroke="#10b981" fillOpacity={1} fill="url(#colorComps)" name="Certifications" />
              </AreaChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Department Distribution PieChart */}
        <div className="glass-card rounded-3xl p-6 border border-slate-800 flex flex-col justify-between">
          <div>
            <h3 className="text-sm font-bold text-white flex items-center gap-2 mb-1">
              <PieChartIcon className="w-4 h-4 text-purple-400" /> Division Participation
            </h3>
            <p className="text-[11px] text-slate-400">Personnel distribution by meteorological branch</p>
          </div>

          <div className="h-56 w-full my-auto">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={data?.departmentDistribution || []}
                  dataKey="value"
                  nameKey="name"
                  cx="50%"
                  cy="50%"
                  outerRadius={75}
                  innerRadius={40}
                  paddingAngle={4}
                >
                  {(data?.departmentDistribution || []).map((entry: any, index: number) => (
                    <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '12px' }} />
              </PieChart>
            </ResponsiveContainer>
          </div>

          <div className="flex flex-wrap gap-2 text-[10px] text-slate-300">
            {(data?.departmentDistribution || []).map((d: any, idx: number) => (
              <span key={d.name} className="flex items-center gap-1">
                <span className="w-2 h-2 rounded-full" style={{ backgroundColor: COLORS[idx % COLORS.length] }} />
                {d.name.split(' ')[0]}: <strong>{d.value}</strong>
              </span>
            ))}
          </div>
        </div>
      </div>

      {/* Course Performance BarChart */}
      <div className="glass-card rounded-3xl p-6 border border-slate-800">
        <h3 className="text-sm font-bold text-white flex items-center gap-2 mb-1">
          <BarChart2 className="w-4 h-4 text-emerald-400" /> Module Pass Rates & Certifications
        </h3>
        <p className="text-[11px] text-slate-400 mb-4">Course-wise comparison of active trainees versus verified passes</p>

        <div className="h-64 w-full">
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={data?.coursePerformance || []} barGap={4}>
              <CartesianGrid strokeDasharray="3 3" stroke="#1e293b" />
              <XAxis dataKey="code" stroke="#64748b" tick={{ fontSize: 11 }} />
              <YAxis stroke="#64748b" tick={{ fontSize: 11 }} />
              <Tooltip contentStyle={{ backgroundColor: '#0f172a', borderColor: '#334155', borderRadius: '12px' }} />
              <Legend wrapperStyle={{ fontSize: '11px', color: '#94a3b8' }} />
              <Bar dataKey="enrollments" fill="#0c8ee9" name="Total Enrolled" radius={[6, 6, 0, 0]} />
              <Bar dataKey="certifications" fill="#10b981" name="Certified Passed" radius={[6, 6, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>
    </div>
  );
};
