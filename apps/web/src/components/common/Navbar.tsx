import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuthStore } from '../../store/authStore';
import { useNotificationStore } from '../../store/notificationStore';
import { Bell, CloudLightning, LogOut, User, CheckCircle2, Shield, GraduationCap, Award } from 'lucide-react';
import { NotificationFeed } from './NotificationFeed';
import { api } from '../../services/api';

export const Navbar: React.FC = () => {
  const { user, logout } = useAuthStore();
  const { unreadCount, setNotifications, initSocket } = useNotificationStore();
  const [showNotifications, setShowNotifications] = useState(false);
  const [showProfileMenu, setShowProfileMenu] = useState(false);
  const navigate = useNavigate();

  useEffect(() => {
    if (user) {
      initSocket(user.id, user.role);
      // Fetch notifications initial list
      api.get('/notifications').then((res) => {
        if (res.data.success) {
          setNotifications(res.data.notifications, res.data.unreadCount);
        }
      }).catch(() => {});
    }
  }, [user, initSocket, setNotifications]);

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const getRoleBadge = (role?: string) => {
    switch (role) {
      case 'ADMIN':
        return <span className="flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30"><Shield className="w-3 h-3"/> Admin</span>;
      case 'TRAINER':
        return <span className="flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30"><GraduationCap className="w-3 h-3"/> Trainer</span>;
      default:
        return <span className="flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30"><User className="w-3 h-3"/> Trainee</span>;
    }
  };

  return (
    <header className="sticky top-0 z-40 w-full border-b border-slate-800 bg-[#070e20]/80 backdrop-blur-md">
      <div className="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        {/* Brand Logo */}
        <Link to="/" className="flex items-center gap-3 group">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-imd-600 to-moes-500 p-0.5 flex items-center justify-center shadow-lg shadow-imd-500/20 group-hover:scale-105 transition-transform">
            <div className="w-full h-full bg-[#070e20] rounded-[10px] flex items-center justify-center">
              <CloudLightning className="w-5 h-5 text-imd-400 group-hover:text-moes-500 transition-colors" />
            </div>
          </div>
          <div>
            <div className="flex items-center gap-2">
              <span className="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-slate-200 to-imd-300 bg-clip-text text-transparent">
                CAPACITY CONNECT
              </span>
              <span className="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-imd-500/20 text-imd-300 border border-imd-500/30">
                IMD • MoES
              </span>
            </div>
            <p className="text-[10px] text-slate-400 hidden sm:block">National Meteorological Capacity Building & Training Portal</p>
          </div>
        </Link>

        {/* Right Nav actions */}
        <div className="flex items-center gap-3">
          {/* Realtime Notification Bell */}
          <div className="relative">
            <button
              onClick={() => setShowNotifications(!showNotifications)}
              aria-label="View notifications"
              className="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800/60 transition-colors relative"
            >
              <Bell className="w-5 h-5" />
              {unreadCount > 0 && (
                <span className="absolute top-1 right-1 w-5 h-5 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center animate-pulse">
                  {unreadCount > 9 ? '9+' : unreadCount}
                </span>
              )}
            </button>

            {showNotifications && (
              <NotificationFeed onClose={() => setShowNotifications(false)} />
            )}
          </div>

          {/* User Profile Menu */}
          {user ? (
            <div className="relative">
              <button
                onClick={() => setShowProfileMenu(!showProfileMenu)}
                className="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-800/60 transition-colors border border-slate-700/50"
              >
                <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-imd-500 to-slate-700 flex items-center justify-center font-bold text-sm text-white shadow">
                  {user.name.charAt(0)}
                </div>
                <div className="hidden md:block text-left">
                  <div className="text-xs font-semibold text-slate-200 flex items-center gap-1.5">
                    {user.name}
                  </div>
                  <div className="text-[11px] text-slate-400 truncate max-w-[140px]">
                    {user.department || user.organization}
                  </div>
                </div>
                {getRoleBadge(user.role)}
              </button>

              {showProfileMenu && (
                <div className="absolute right-0 mt-2 w-56 rounded-xl bg-slate-900 border border-slate-700 shadow-2xl py-2 z-50 animate-in fade-in slide-in-from-top-2">
                  <div className="px-4 py-2 border-b border-slate-800">
                    <p className="text-xs font-medium text-slate-400">Signed in as</p>
                    <p className="text-sm font-semibold text-slate-200 truncate">{user.email}</p>
                    <p className="text-xs text-imd-400 mt-0.5">{user.designation || user.role}</p>
                  </div>

                  <Link
                    to="/profile"
                    onClick={() => setShowProfileMenu(false)}
                    className="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800 hover:text-white"
                  >
                    <User className="w-4 h-4 text-slate-400" />
                    My Competency Profile
                  </Link>

                  {user.role === 'TRAINEE' && (
                    <Link
                      to="/certificates"
                      onClick={() => setShowProfileMenu(false)}
                      className="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800 hover:text-white"
                    >
                      <Award className="w-4 h-4 text-moes-500" />
                      My Verified Certificates
                    </Link>
                  )}

                  <div className="border-t border-slate-800 my-1" />

                  <button
                    onClick={handleLogout}
                    className="flex w-full items-center gap-2.5 px-4 py-2 text-xs text-rose-400 hover:bg-rose-500/10 transition-colors"
                  >
                    <LogOut className="w-4 h-4" />
                    Sign Out
                  </button>
                </div>
              )}
            </div>
          ) : (
            <Link
              to="/login"
              className="text-xs font-semibold px-4 py-2 rounded-lg bg-imd-600 hover:bg-imd-500 text-white transition-all shadow-md shadow-imd-600/30"
            >
              Sign In
            </Link>
          )}
        </div>
      </div>
    </header>
  );
};
