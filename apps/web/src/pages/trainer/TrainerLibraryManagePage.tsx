import React, { useState, useEffect } from 'react';
import { api } from '../../services/api';
import { Library, Plus, Download, FileText, Video, Presentation, CheckCircle2, Save, X } from 'lucide-react';
import { ResourceFormat } from '@capacity-connect/shared-types';

export const TrainerLibraryManagePage: React.FC = () => {
  const [resources, setResources] = useState<any[]>([]);
  const [courses, setCourses] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showUploadModal, setShowUploadModal] = useState(false);

  // Form State
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [format, setFormat] = useState<ResourceFormat>(ResourceFormat.PDF);
  const [fileUrl, setFileUrl] = useState('');
  const [subject, setSubject] = useState('Radar Meteorology');
  const [courseId, setCourseId] = useState('');
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [resData, courseData] = await Promise.all([
        api.get('/resources'),
        api.get('/courses')
      ]);
      if (resData.data.success) setResources(resData.data.resources);
      if (courseData.data.success) setCourses(courseData.data.courses);
    } finally {
      setLoading(false);
    }
  };

  const handleCreateResource = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      const res = await api.post('/resources', {
        title,
        description,
        format,
        fileUrl: fileUrl || '/uploads/sample-lecture.pdf',
        subject,
        courseId: courseId || undefined,
        tags: [subject, format]
      });

      if (res.data.success) {
        setShowUploadModal(false);
        setTitle('');
        setDescription('');
        setFileUrl('');
        fetchData();
      }
    } catch (err: any) {
      alert(err.response?.data?.message || 'Failed to upload resource');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
            <Library className="w-6 h-6 text-emerald-400" /> Trainer Study Material Management
          </h1>
          <p className="text-xs text-slate-400 mt-1">
            Curate and manage meteorological slide decks, operational guides, and video masterclasses
          </p>
        </div>

        <button
          onClick={() => setShowUploadModal(true)}
          className="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold flex items-center gap-2 shadow-md shadow-emerald-600/30 transition-all shrink-0"
        >
          <Plus className="w-4 h-4" /> Add Learning Resource
        </button>
      </div>

      {/* Resource Table */}
      {loading ? (
        <div className="py-20 flex justify-center">
          <div className="w-8 h-8 border-4 border-emerald-500/30 border-t-emerald-500 rounded-full animate-spin" />
        </div>
      ) : resources.length === 0 ? (
        <div className="glass-card rounded-3xl p-12 text-center text-xs text-slate-400">
          No resources uploaded yet. Click Add Learning Resource to publish study material.
        </div>
      ) : (
        <div className="glass-card rounded-3xl border border-slate-800 overflow-hidden shadow-xl">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs">
              <thead className="bg-slate-900/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                <tr>
                  <th className="py-3 px-4">Title & Subject</th>
                  <th className="py-3 px-4">Format</th>
                  <th className="py-3 px-4">Linked Course</th>
                  <th className="py-3 px-4">Author</th>
                  <th className="py-3 px-4">Downloads</th>
                  <th className="py-3 px-4 text-right">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800/60">
                {resources.map((r) => (
                  <tr key={r.id} className="hover:bg-slate-850 transition-colors">
                    <td className="py-3 px-4">
                      <div className="font-bold text-white">{r.title}</div>
                      <div className="text-[10px] text-imd-400 mt-0.5">{r.subject}</div>
                    </td>
                    <td className="py-3 px-4">
                      <span className="px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 font-medium text-[10px]">
                        {r.format}
                      </span>
                    </td>
                    <td className="py-3 px-4 text-slate-300">
                      {r.course ? `${r.course.code}` : <span className="text-slate-500">Unlinked</span>}
                    </td>
                    <td className="py-3 px-4 text-slate-300">{r.trainer?.name}</td>
                    <td className="py-3 px-4 font-mono font-semibold text-white">{r.downloadCount}</td>
                    <td className="py-3 px-4 text-right">
                      <a
                        href={r.fileUrl}
                        target="_blank"
                        rel="noreferrer"
                        className="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold inline-flex items-center gap-1"
                      >
                        <Download className="w-3.5 h-3.5" /> View
                      </a>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Upload Modal */}
      {showUploadModal && (
        <div className="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="glass-panel w-full max-w-lg rounded-3xl p-6 border border-slate-700 shadow-2xl relative">
            <div className="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
              <h3 className="text-sm font-bold text-white flex items-center gap-2">
                <Plus className="w-4 h-4 text-emerald-400" /> Add Trainer Study Resource
              </h3>
              <button
                onClick={() => setShowUploadModal(false)}
                className="p-1 rounded-lg text-slate-400 hover:text-white"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <form onSubmit={handleCreateResource} className="space-y-4 text-xs">
              <div>
                <label className="block text-slate-300 font-semibold mb-1">Resource Title</label>
                <input
                  type="text"
                  required
                  value={title}
                  onChange={(e) => setTitle(e.target.value)}
                  placeholder="e.g. INSAT-3DR Rapid Scan True-Color RGB Analysis Guide"
                  className="glass-input w-full px-3 py-2 rounded-xl"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-slate-300 font-semibold mb-1">Format</label>
                  <select
                    value={format}
                    onChange={(e) => setFormat(e.target.value as any)}
                    className="glass-input w-full px-3 py-2 rounded-xl bg-slate-900"
                  >
                    <option value={ResourceFormat.PDF}>PDF Document</option>
                    <option value={ResourceFormat.PPT}>PPT Presentation</option>
                    <option value={ResourceFormat.VIDEO}>Video Masterclass</option>
                    <option value={ResourceFormat.DOC}>Technical Doc</option>
                  </select>
                </div>

                <div>
                  <label className="block text-slate-300 font-semibold mb-1">Subject</label>
                  <select
                    value={subject}
                    onChange={(e) => setSubject(e.target.value)}
                    className="glass-input w-full px-3 py-2 rounded-xl bg-slate-900"
                  >
                    <option value="Radar Meteorology">Radar Meteorology</option>
                    <option value="Satellite Meteorology">Satellite Meteorology</option>
                    <option value="Numerical Weather Prediction (NWP)">NWP Modeling</option>
                    <option value="Tropical Cyclones">Tropical Cyclones</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-slate-300 font-semibold mb-1">Linked Course (Optional)</label>
                <select
                  value={courseId}
                  onChange={(e) => setCourseId(e.target.value)}
                  className="glass-input w-full px-3 py-2 rounded-xl bg-slate-900"
                >
                  <option value="">None (General Library)</option>
                  {courses.map((c) => (
                    <option key={c.id} value={c.id}>
                      {c.code} - {c.title}
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-slate-300 font-semibold mb-1">File URL / Storage Path</label>
                <input
                  type="text"
                  value={fileUrl}
                  onChange={(e) => setFileUrl(e.target.value)}
                  placeholder="/uploads/my-lecture.pdf"
                  className="glass-input w-full px-3 py-2 rounded-xl text-slate-300 font-mono"
                />
              </div>

              <div className="flex justify-end gap-2 pt-4 border-t border-slate-800">
                <button
                  type="button"
                  onClick={() => setShowUploadModal(false)}
                  className="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  disabled={submitting}
                  className="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30 disabled:opacity-50"
                >
                  {submitting ? 'Publishing...' : 'Publish to Library'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
