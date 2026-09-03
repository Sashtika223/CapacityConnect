import React, { useState } from 'react';
import { api } from '../../services/api';
import { Megaphone, Send, CheckCircle2, Shield, Users, Bell } from 'lucide-react';

export const AnnouncementPublisherPage: React.FC = () => {
  const [title, setTitle] = useState('');
  const [message, setMessage] = useState('');
  const [targetRole, setTargetRole] = useState('ALL');
  const [link, setLink] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [successMsg, setSuccessMsg] = useState('');

  const handlePublish = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!title || !message) return;

    setSubmitting(true);
    setSuccessMsg('');
    try {
      const res = await api.post('/admin/announcements', {
        title,
        message,
        targetRole,
        link: link || undefined
      });

      if (res.data.success) {
        setSuccessMsg(`Announcement successfully broadcasted in real-time to [${targetRole}] users via Socket.io!`);
        setTitle('');
        setMessage('');
        setLink('');
      }
    } catch (err: any) {
      alert(err.response?.data?.message || 'Error broadcasting announcement');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <div>
        <h1 className="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
          <Megaphone className="w-6 h-6 text-purple-400" /> Broadcast & Realtime Announcement Dispatch
        </h1>
        <p className="text-xs text-slate-400 mt-1">
          Publish high-priority circulars, exam schedule updates, and system advisories directly to employees via Socket.io push
        </p>
      </div>

      {successMsg && (
        <div className="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-xs font-bold text-emerald-300 flex items-center gap-2">
          <CheckCircle2 className="w-4 h-4" /> {successMsg}
        </div>
      )}

      <div className="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800">
        <form onSubmit={handlePublish} className="space-y-4">
          <div>
            <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Announcement Title
            </label>
            <input
              type="text"
              required
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              placeholder="e.g. Cyclone Forecaster Refresher Workshop 2026 Scheduled"
              className="glass-input w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold"
            />
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Target Audience
              </label>
              <select
                value={targetRole}
                onChange={(e) => setTargetRole(e.target.value)}
                className="glass-input w-full px-3 py-2 rounded-xl text-xs bg-slate-900"
              >
                <option value="ALL">All IMD Personnel (Public Broadcast)</option>
                <option value="TRAINEE">Trainees & Scientific Staff Only</option>
                <option value="TRAINER">Instructors & Trainers Only</option>
                <option value="ADMIN">Administrative Officers Only</option>
              </select>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Direct Link (Optional)
              </label>
              <input
                type="text"
                value={link}
                onChange={(e) => setLink(e.target.value)}
                placeholder="/courses or /library"
                className="glass-input w-full px-3 py-2 rounded-xl text-xs"
              />
            </div>
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Announcement Body
            </label>
            <textarea
              required
              rows={4}
              value={message}
              onChange={(e) => setMessage(e.target.value)}
              placeholder="Provide complete circular details, guidelines, or instruction text..."
              className="glass-input w-full p-3 rounded-2xl text-xs leading-relaxed"
            />
          </div>

          <div className="pt-3 border-t border-slate-800 flex justify-end">
            <button
              type="submit"
              disabled={submitting}
              className="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all disabled:opacity-50"
            >
              <Send className="w-4 h-4" /> {submitting ? 'Dispatching...' : 'Dispatch Live Broadcast'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};
