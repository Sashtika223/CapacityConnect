import React from 'react';
import { NavLink } from 'react-router-dom';
import { useAuthStore } from '../../store/authStore';
import {
  LayoutDashboard,
  BookOpen,
  Library,
  FileCheck2,
  Award,
  Users,
  BrainCircuit,
  Megaphone,
  Activity,
  History,
  Sparkles,
  Compass
} from 'lucide-react';

export const Sidebar: React.FC = () => {
  const { user } = useAuthStore();
  const role = user?.role || 'TRAINEE';

  const traineeLinks = [
    { to: '/dashboard', label: 'My Dashboard', icon: LayoutDashboard },
    { to: '/courses', label: 'Course Catalog', icon: BookOpen },
    { to: '/library', label: 'Trainer Library', icon: Library },
    { to: '/assessments', label: 'MCQ Assessments', icon: FileCheck2 },
    { to: '/recommendations', label: 'Smart Recommendations', icon: Sparkles, badge: 'AI' },
    { to: '/certificates', label: 'Verified Certificates', icon: Award },
    { to: '/profile', label: 'Competency Profile', icon: Compass },
  ];

  const trainerLinks = [
    { to: '/trainer/dashboard', label: 'Trainer Portal', icon: LayoutDashboard },
    { to: '/trainer/courses', label: 'Authored Courses', icon: BookOpen },
    { to: '/trainer/library', label: 'Resource Library', icon: Library },
    { to: '/trainer/questionnaires', label: 'MCQ & Quiz Builder', icon: FileCheck2 },
    { to: '/trainer/trainees', label: 'Trainee Monitor & Risk', icon: Activity, badge: 'Risk Flag' },
    { to: '/trainer/feedbacks', label: 'Feedback Sentiment', icon: Sparkles },
    { to: '/profile', label: 'My Subject Tags', icon: Compass },
  ];

  const adminLinks = [
    { to: '/admin/dashboard', label: 'Admin Analytics', icon: LayoutDashboard },
    { to: '/admin/approvals', label: 'Pending Approvals', icon: Users, badge: 'Queue' },
    { to: '/admin/competency-matcher', label: 'AI Competency Matrix', icon: BrainCircuit, badge: 'AI' },
    { to: '/admin/announcements', label: 'Broadcasts & Push', icon: Megaphone },
    { to: '/admin/users', label: 'User Directory', icon: Users },
    { to: '/admin/audit-logs', label: 'System Audit Logs', icon: History },
  ];

  const links = role === 'ADMIN' ? adminLinks : role === 'TRAINER' ? trainerLinks : traineeLinks;

  return (
    <aside className="w-64 shrink-0 hidden md:block border-r border-slate-800 bg-[#070e20]/60 backdrop-blur-md p-4 min-h-[calc(100vh-4rem)]">
      <div className="space-y-6">
        <div>
          <div className="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 px-3 mb-2">
            Navigation ({role})
          </div>
          <nav className="space-y-1">
            {links.map((link) => {
              const Icon = link.icon;
              return (
                <NavLink
                  key={link.to}
                  to={link.to}
                  end={link.to === '/dashboard' || link.to === '/trainer/dashboard' || link.to === '/admin/dashboard'}
                  className={({ isActive }) =>
                    `flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-medium transition-all group ${
                      isActive
                        ? 'bg-gradient-to-r from-imd-600/30 to-moes-600/20 text-white border border-imd-500/40 shadow-sm shadow-imd-500/10'
                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40'
                    }`
                  }
                >
                  <div className="flex items-center gap-3">
                    <Icon className="w-4 h-4 text-slate-400 group-hover:text-imd-400 transition-colors" />
                    <span>{link.label}</span>
                  </div>
                  {link.badge && (
                    <span className={`text-[9px] font-extrabold px-1.5 py-0.2 rounded-md ${
                      link.badge === 'AI' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' :
                      link.badge === 'Risk Flag' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' :
                      'bg-imd-500/20 text-imd-300 border border-imd-500/30'
                    }`}>
                      {link.badge}
                    </span>
                  )}
                </NavLink>
              );
            })}
          </nav>
        </div>

        {/* Quick Help Card */}
        <div className="p-3.5 rounded-xl bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800">
          <div className="flex items-center gap-2 text-xs font-semibold text-slate-200">
            <Sparkles className="w-4 h-4 text-moes-500" />
            <span>Capacity Connect AI</span>
          </div>
          <p className="text-[11px] text-slate-400 mt-1">
            Live adaptive testing, similarity matching, and QR certificate generation enabled.
          </p>
        </div>
      </div>
    </aside>
  );
};
