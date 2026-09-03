import React, { useState, useEffect } from 'react';
import { api } from '../../services/api';
import { useAuthStore } from '../../store/authStore';
import { User, Compass, Briefcase, Award, Plus, X, Save, CheckCircle2 } from 'lucide-react';

export const TraineeProfilePage: React.FC = () => {
  const { user } = useAuthStore();
  const [qualifications, setQualifications] = useState('');
  const [experienceYears, setExperienceYears] = useState(0);
  const [bio, setBio] = useState('');
  const [skills, setSkills] = useState<string[]>([]);
  const [interests, setInterests] = useState<string[]>([]);
  const [newSkill, setNewSkill] = useState('');
  const [newInterest, setNewInterest] = useState('');
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [savedSuccess, setSavedSuccess] = useState(false);

  useEffect(() => {
    api.get('/trainee/profile').then((res) => {
      if (res.data.success && res.data.profile) {
        const p = res.data.profile;
        setQualifications(p.qualifications || '');
        setExperienceYears(p.experienceYears || 0);
        setBio(p.bio || '');
        setSkills(p.skills || []);
        setInterests(p.interests || []);
      }
    }).finally(() => setLoading(false));
  }, []);

  const handleAddSkill = () => {
    if (newSkill.trim() && !skills.includes(newSkill.trim())) {
      setSkills([...skills, newSkill.trim()]);
      setNewSkill('');
    }
  };

  const handleAddInterest = () => {
    if (newInterest.trim() && !interests.includes(newInterest.trim())) {
      setInterests([...interests, newInterest.trim()]);
      setNewInterest('');
    }
  };

  const handleRemoveSkill = (s: string) => setSkills(skills.filter((item) => item !== s));
  const handleRemoveInterest = (i: string) => setInterests(interests.filter((item) => item !== i));

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    setSavedSuccess(false);

    try {
      const res = await api.put('/trainee/profile', {
        qualifications,
        experienceYears: Number(experienceYears),
        bio,
        skills,
        interests
      });

      if (res.data.success) {
        setSavedSuccess(true);
        setTimeout(() => setSavedSuccess(false), 3000);
      }
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="py-20 flex justify-center">
        <div className="w-10 h-10 border-4 border-imd-500/30 border-t-imd-500 rounded-full animate-spin" />
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      <div>
        <h1 className="text-2xl font-extrabold text-white tracking-tight">Competency Profile</h1>
        <p className="text-xs text-slate-400 mt-1">
          Your competencies and interests directly power the AI Course Recommender and trainer competency mapping
        </p>
      </div>

      <form onSubmit={handleSave} className="space-y-6">
        {/* User Card */}
        <div className="glass-card rounded-3xl p-6 border border-slate-800 flex items-center gap-4">
          <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-imd-500 to-moes-500 flex items-center justify-center font-bold text-xl text-white shadow-lg">
            {user?.name.charAt(0)}
          </div>
          <div>
            <h3 className="text-lg font-bold text-white">{user?.name}</h3>
            <p className="text-xs text-slate-400 font-mono">{user?.employeeId} • {user?.designation}</p>
            <p className="text-xs text-imd-400 mt-0.5">{user?.department} • {user?.organization}</p>
          </div>
        </div>

        {savedSuccess && (
          <div className="p-3.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-xs font-bold text-emerald-300 flex items-center gap-2">
            <CheckCircle2 className="w-4 h-4" /> Competency profile updated successfully! AI recommendations refreshed.
          </div>
        )}

        {/* Profile Details */}
        <div className="glass-panel rounded-3xl p-6 sm:p-8 border border-slate-800 space-y-5">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Educational Qualifications
              </label>
              <input
                type="text"
                value={qualifications}
                onChange={(e) => setQualifications(e.target.value)}
                placeholder="e.g. M.Sc. Meteorology, Ph.D. Atmospheric Physics"
                className="glass-input block w-full px-3.5 py-2.5 rounded-xl text-xs"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Years of Meteorological Experience
              </label>
              <input
                type="number"
                min="0"
                value={experienceYears}
                onChange={(e) => setExperienceYears(Number(e.target.value))}
                className="glass-input block w-full px-3.5 py-2.5 rounded-xl text-xs"
              />
            </div>
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Professional Biography
            </label>
            <textarea
              rows={3}
              value={bio}
              onChange={(e) => setBio(e.target.value)}
              placeholder="Summary of your forecasting focus, model development, radar operations, or research domains..."
              className="glass-input block w-full p-3 rounded-2xl text-xs"
            />
          </div>

          {/* Skills Tag Input */}
          <div>
            <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Technical Competencies & Skills
            </label>
            <div className="flex flex-wrap gap-2 mb-3">
              {skills.map((s) => (
                <span
                  key={s}
                  className="px-3 py-1 rounded-full bg-imd-500/20 text-imd-300 border border-imd-500/30 text-xs font-medium flex items-center gap-1.5"
                >
                  {s}
                  <button type="button" onClick={() => handleRemoveSkill(s)} className="hover:text-white">
                    <X className="w-3 h-3" />
                  </button>
                </span>
              ))}
            </div>
            <div className="flex gap-2">
              <input
                type="text"
                value={newSkill}
                onChange={(e) => setNewSkill(e.target.value)}
                onKeyDown={(e) => { if (e.key === 'Enter') { e.preventDefault(); handleAddSkill(); } }}
                placeholder="Add skill tag (e.g. WRF Model, Doppler Radar, Python, Fortran) and hit Add"
                className="glass-input flex-1 px-3.5 py-2 rounded-xl text-xs"
              />
              <button
                type="button"
                onClick={handleAddSkill}
                className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1"
              >
                <Plus className="w-3.5 h-3.5" /> Add
              </button>
            </div>
          </div>

          {/* Interests Tag Input */}
          <div>
            <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Research & Operational Interests
            </label>
            <div className="flex flex-wrap gap-2 mb-3">
              {interests.map((i) => (
                <span
                  key={i}
                  className="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-medium flex items-center gap-1.5"
                >
                  {i}
                  <button type="button" onClick={() => handleRemoveInterest(i)} className="hover:text-white">
                    <X className="w-3 h-3" />
                  </button>
                </span>
              ))}
            </div>
            <div className="flex gap-2">
              <input
                type="text"
                value={newInterest}
                onChange={(e) => setNewInterest(e.target.value)}
                onKeyDown={(e) => { if (e.key === 'Enter') { e.preventDefault(); handleAddInterest(); } }}
                placeholder="Add interest tag (e.g. Cyclone Dynamics, Flash Floods, AI Weather Models)"
                className="glass-input flex-1 px-3.5 py-2 rounded-xl text-xs"
              />
              <button
                type="button"
                onClick={handleAddInterest}
                className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1"
              >
                <Plus className="w-3.5 h-3.5" /> Add
              </button>
            </div>
          </div>

          <div className="pt-4 border-t border-slate-800 flex justify-end">
            <button
              type="submit"
              disabled={saving}
              className="px-6 py-2.5 rounded-xl bg-imd-600 hover:bg-imd-500 text-white text-xs font-bold shadow-lg shadow-imd-600/30 flex items-center gap-2 transition-all disabled:opacity-50"
            >
              <Save className="w-4 h-4" /> {saving ? 'Saving...' : 'Save Profile Changes'}
            </button>
          </div>
        </div>
      </form>
    </div>
  );
};
