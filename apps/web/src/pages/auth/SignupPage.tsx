import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { api } from '../../services/api';
import { CloudLightning, ShieldAlert, ArrowRight, UserCheck, GraduationCap, CheckCircle2 } from 'lucide-react';
import { Role } from '@capacity-connect/shared-types';

export const SignupPage: React.FC = () => {
  const [role, setRole] = useState<Role>(Role.TRAINEE);
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [employeeId, setEmployeeId] = useState('');
  const [department, setDepartment] = useState('Radar Meteorology Division');
  const [designation, setDesignation] = useState('Scientific Assistant');
  const [error, setError] = useState('');
  const [successMsg, setSuccessMsg] = useState('');
  const [loading, setLoading] = useState(false);

  const navigate = useNavigate();

  const handleSignup = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setSuccessMsg('');
    setLoading(true);

    try {
      const res = await api.post('/auth/signup', {
        name,
        email,
        password,
        role,
        employeeId,
        department,
        designation,
        organization: 'India Meteorological Department (IMD)'
      });

      if (res.data.success) {
        if (res.data.requiresApproval) {
          setSuccessMsg(res.data.message);
        } else {
          navigate('/login');
        }
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Registration failed. Please review your input.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-[#070e20] flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
      <div className="sm:mx-auto sm:w-full sm:max-w-md z-10 text-center">
        <div className="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-imd-600 to-moes-500 p-0.5 shadow-xl shadow-imd-600/30 mb-3">
          <div className="w-full h-full bg-[#070e20] rounded-[14px] flex items-center justify-center">
            <CloudLightning className="w-6 h-6 text-imd-400" />
          </div>
        </div>
        <h2 className="text-2xl font-extrabold tracking-tight text-white">
          Join Capacity Connect
        </h2>
        <p className="mt-1 text-xs text-slate-400">
          Official IMD / MoES Capacity Building Registration
        </p>
      </div>

      <div className="mt-6 sm:mx-auto sm:w-full sm:max-w-md z-10 px-4 sm:px-0">
        <div className="glass-panel py-7 px-6 sm:px-8 rounded-3xl border border-slate-800 shadow-2xl relative">
          {error && (
            <div className="mb-4 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-start gap-2.5">
              <ShieldAlert className="w-5 h-5 text-rose-400 shrink-0 mt-0.5" />
              <p className="text-xs text-rose-300">{error}</p>
            </div>
          )}

          {successMsg ? (
            <div className="text-center py-6">
              <div className="w-12 h-12 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center mx-auto mb-3">
                <CheckCircle2 className="w-6 h-6" />
              </div>
              <h3 className="text-base font-bold text-white mb-2">Registration Submitted</h3>
              <p className="text-xs text-slate-300 leading-relaxed mb-6">{successMsg}</p>
              <Link
                to="/login"
                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-imd-600 hover:bg-imd-500 text-white text-xs font-semibold"
              >
                Back to Sign In <ArrowRight className="w-4 h-4" />
              </Link>
            </div>
          ) : (
            <form className="space-y-4" onSubmit={handleSignup}>
              {/* Role Selection */}
              <div>
                <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                  Select Your Account Role
                </label>
                <div className="grid grid-cols-2 gap-3">
                  <button
                    type="button"
                    onClick={() => setRole(Role.TRAINEE)}
                    className={`p-3 rounded-xl border text-left flex flex-col gap-1 transition-all ${
                      role === Role.TRAINEE
                        ? 'bg-imd-600/20 border-imd-500 text-white'
                        : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'
                    }`}
                  >
                    <div className="flex items-center gap-1.5 font-bold text-xs">
                      <UserCheck className="w-4 h-4 text-imd-400" /> Trainee
                    </div>
                    <span className="text-[10px] text-slate-400 leading-tight">Instant access to courses & quizzes</span>
                  </button>

                  <button
                    type="button"
                    onClick={() => setRole(Role.TRAINER)}
                    className={`p-3 rounded-xl border text-left flex flex-col gap-1 transition-all ${
                      role === Role.TRAINER
                        ? 'bg-emerald-600/20 border-emerald-500 text-white'
                        : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'
                    }`}
                  >
                    <div className="flex items-center gap-1.5 font-bold text-xs">
                      <GraduationCap className="w-4 h-4 text-emerald-400" /> Trainer
                    </div>
                    <span className="text-[10px] text-slate-400 leading-tight">Requires Admin approval</span>
                  </button>
                </div>
              </div>

              {role === Role.TRAINER && (
                <div className="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-[11px] text-amber-300">
                  ⚠️ Trainer registration requires manual verification by the IMD Director General administration before you can create courses or upload lectures.
                </div>
              )}

              <div>
                <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                  Full Name
                </label>
                <input
                  type="text"
                  required
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="e.g. Dr. Priya Patel"
                  className="glass-input block w-full px-3.5 py-2 rounded-xl text-sm"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                    Employee ID
                  </label>
                  <input
                    type="text"
                    required
                    value={employeeId}
                    onChange={(e) => setEmployeeId(e.target.value)}
                    placeholder="IMD-2026-99"
                    className="glass-input block w-full px-3.5 py-2 rounded-xl text-sm"
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                    Official Email
                  </label>
                  <input
                    type="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="name@imd.gov.in"
                    className="glass-input block w-full px-3.5 py-2 rounded-xl text-sm"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                    Department
                  </label>
                  <select
                    value={department}
                    onChange={(e) => setDepartment(e.target.value)}
                    className="glass-input block w-full px-3 py-2 rounded-xl text-xs bg-slate-900"
                  >
                    <option value="Radar Meteorology Division">Radar Meteorology</option>
                    <option value="Satellite Meteorology Division">Satellite Meteorology</option>
                    <option value="Numerical Weather Prediction (NWP)">NWP Modeling</option>
                    <option value="Cyclone Warning Division">Cyclone Warning</option>
                    <option value="Aviation Meteorology">Aviation Met</option>
                    <option value="Agro-Meteorology Division">Agro-Meteorology</option>
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                    Designation
                  </label>
                  <input
                    type="text"
                    value={designation}
                    onChange={(e) => setDesignation(e.target.value)}
                    placeholder="Scientist 'D' / Meteorologist"
                    className="glass-input block w-full px-3.5 py-2 rounded-xl text-sm"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">
                  Password
                </label>
                <input
                  type="password"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="At least 6 characters"
                  className="glass-input block w-full px-3.5 py-2 rounded-xl text-sm"
                />
              </div>

              <button
                type="submit"
                disabled={loading}
                className="w-full mt-3 py-2.5 px-4 rounded-xl bg-gradient-to-r from-imd-600 to-moes-600 hover:from-imd-500 hover:to-moes-500 text-white font-semibold text-xs shadow-lg shadow-imd-600/30 transition-all flex items-center justify-center gap-2 group disabled:opacity-50"
              >
                {loading ? (
                  <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                ) : (
                  <>
                    Complete Registration <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                  </>
                )}
              </button>
            </form>
          )}

          <div className="mt-5 pt-4 border-t border-slate-800 text-center">
            <p className="text-xs text-slate-400">
              Already registered?{' '}
              <Link to="/login" className="text-imd-400 hover:text-imd-300 font-semibold underline">
                Sign In
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};
