import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuthStore } from '../../store/authStore';
import { api } from '../../services/api';
import { CloudLightning, Lock, Mail, ShieldAlert, ArrowRight, Sparkles, UserCheck } from 'lucide-react';

export const LoginPage: React.FC = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const { setAuth } = useAuthStore();
  const navigate = useNavigate();

  const handleLogin = async (e: React.FormEvent, customEmail?: string, customPassword?: string) => {
    if (e) e.preventDefault();
    setError('');
    setLoading(true);

    const targetEmail = customEmail || email;
    const targetPassword = customPassword || password;

    try {
      const res = await api.post('/auth/login', {
        email: targetEmail,
        password: targetPassword
      });

      if (res.data.success) {
        setAuth(res.data.user, res.data.accessToken, res.data.refreshToken);

        const role = res.data.user.role;
        if (role === 'ADMIN') navigate('/admin/dashboard');
        else if (role === 'TRAINER') navigate('/trainer/dashboard');
        else navigate('/dashboard');
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Login failed. Please check your credentials.');
    } finally {
      setLoading(false);
    }
  };

  const quickLogin = (e: string, p: string) => {
    setEmail(e);
    setPassword(p);
    handleLogin(null as any, e, p);
  };

  return (
    <div className="min-h-screen bg-[#070e20] flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
      {/* Subtle Background Glows */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-imd-600/10 blur-[120px] pointer-events-none rounded-full" />
      <div className="absolute bottom-0 right-10 w-[400px] h-[300px] bg-moes-500/10 blur-[100px] pointer-events-none rounded-full" />

      <div className="sm:mx-auto sm:w-full sm:max-w-md z-10 text-center">
        <div className="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-imd-600 to-moes-500 p-0.5 shadow-xl shadow-imd-600/30 mb-4">
          <div className="w-full h-full bg-[#070e20] rounded-[14px] flex items-center justify-center">
            <CloudLightning className="w-7 h-7 text-imd-400" />
          </div>
        </div>
        <h2 className="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
          CAPACITY CONNECT
        </h2>
        <p className="mt-1 text-xs text-slate-400 uppercase tracking-widest font-semibold">
          IMD • Ministry of Earth Sciences (MoES)
        </p>
      </div>

      <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md z-10 px-4 sm:px-0">
        <div className="glass-panel py-8 px-6 sm:px-8 rounded-3xl border border-slate-800 shadow-2xl relative">
          {error && (
            <div className="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-start gap-3">
              <ShieldAlert className="w-5 h-5 text-rose-400 shrink-0 mt-0.5" />
              <p className="text-xs text-rose-300 leading-relaxed">{error}</p>
            </div>
          )}

          {/* Quick Demo Credentials Switcher */}
          <div className="mb-6 p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
            <div className="flex items-center gap-2 mb-2 text-xs font-bold text-slate-300 uppercase tracking-wider">
              <Sparkles className="w-3.5 h-3.5 text-moes-500" /> One-Click Demo Access
            </div>
            <div className="grid grid-cols-2 gap-2">
              <button
                type="button"
                onClick={() => quickLogin('trainee1@imd.gov.in', 'Password@123')}
                className="px-2.5 py-1.5 rounded-lg bg-blue-500/15 hover:bg-blue-500/25 border border-blue-500/30 text-[11px] font-semibold text-blue-300 transition-colors flex items-center justify-center gap-1"
              >
                <UserCheck className="w-3 h-3" /> Trainee (Rohan)
              </button>
              <button
                type="button"
                onClick={() => quickLogin('trainer.radar@imd.gov.in', 'Password@123')}
                className="px-2.5 py-1.5 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 text-[11px] font-semibold text-emerald-300 transition-colors flex items-center justify-center gap-1"
              >
                <UserCheck className="w-3 h-3" /> Trainer (Dr. Roy)
              </button>
              <button
                type="button"
                onClick={() => quickLogin('admin@imd.gov.in', 'Admin@123')}
                className="px-2.5 py-1.5 rounded-lg bg-purple-500/15 hover:bg-purple-500/25 border border-purple-500/30 text-[11px] font-semibold text-purple-300 transition-colors flex items-center justify-center gap-1"
              >
                <UserCheck className="w-3 h-3" /> Admin (Director)
              </button>
              <button
                type="button"
                onClick={() => quickLogin('trainee.atrisk@imd.gov.in', 'Password@123')}
                className="px-2.5 py-1.5 rounded-lg bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 text-[11px] font-semibold text-rose-300 transition-colors flex items-center justify-center gap-1"
              >
                <UserCheck className="w-3 h-3" /> At-Risk Trainee
              </button>
            </div>
          </div>

          <form className="space-y-4" onSubmit={handleLogin}>
            <div>
              <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Official Email Address
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                  <Mail className="w-4 h-4" />
                </div>
                <input
                  type="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="name@imd.gov.in"
                  className="glass-input block w-full pl-10 pr-3.5 py-2.5 rounded-xl text-sm placeholder-slate-500"
                />
              </div>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Password
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                  <Lock className="w-4 h-4" />
                </div>
                <input
                  type="password"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="••••••••••••"
                  className="glass-input block w-full pl-10 pr-3.5 py-2.5 rounded-xl text-sm placeholder-slate-500"
                />
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full mt-2 py-3 px-4 rounded-xl bg-gradient-to-r from-imd-600 to-moes-600 hover:from-imd-500 hover:to-moes-500 text-white font-semibold text-sm shadow-lg shadow-imd-600/30 transition-all flex items-center justify-center gap-2 group disabled:opacity-50"
            >
              {loading ? (
                <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
              ) : (
                <>
                  Sign in to Portal <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </>
              )}
            </button>
          </form>

          <div className="mt-6 pt-5 border-t border-slate-800 text-center">
            <p className="text-xs text-slate-400">
              New employee or trainer?{' '}
              <Link to="/signup" className="text-imd-400 hover:text-imd-300 font-semibold underline">
                Register account
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};
