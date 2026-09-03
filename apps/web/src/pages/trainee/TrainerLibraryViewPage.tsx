import React, { useState, useEffect } from 'react';
import { api } from '../../services/api';
import { Library, Search, FileText, Video, Presentation, Download, ExternalLink, Filter } from 'lucide-react';

export const TrainerLibraryViewPage: React.FC = () => {
  const [resources, setResources] = useState<any[]>([]);
  const [search, setSearch] = useState('');
  const [formatFilter, setFormatFilter] = useState('ALL');
  const [subjectFilter, setSubjectFilter] = useState('ALL');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchResources();
  }, [formatFilter, subjectFilter]);

  const fetchResources = async () => {
    setLoading(true);
    try {
      const params: any = {};
      if (formatFilter !== 'ALL') params.format = formatFilter;
      if (subjectFilter !== 'ALL') params.subject = subjectFilter;

      const res = await api.get('/resources', { params });
      if (res.data.success) {
        setResources(res.data.resources);
      }
    } finally {
      setLoading(false);
    }
  };

  const handleAccess = async (resId: string, url: string) => {
    try {
      await api.post(`/resources/${resId}/access`);
      window.open(url, '_blank');
      fetchResources();
    } catch {
      window.open(url, '_blank');
    }
  };

  const filtered = resources.filter((r) => {
    const q = search.toLowerCase();
    return (
      r.title.toLowerCase().includes(q) ||
      (r.description && r.description.toLowerCase().includes(q)) ||
      r.subject.toLowerCase().includes(q)
    );
  });

  const getFormatIcon = (format: string) => {
    switch (format) {
      case 'VIDEO':
        return <Video className="w-5 h-5 text-rose-400" />;
      case 'PPT':
        return <Presentation className="w-5 h-5 text-amber-400" />;
      default:
        return <FileText className="w-5 h-5 text-imd-400" />;
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-extrabold text-white tracking-tight">Trainer Study Library</h1>
        <p className="text-xs text-slate-400 mt-1">
          Direct repository of meteorological lectures, slide decks, operational guides, and research PDFs curated by IMD leads
        </p>
      </div>

      {/* Filter Bar */}
      <div className="glass-panel p-4 rounded-2xl border border-slate-800 flex flex-col md:flex-row items-center gap-3">
        <div className="relative flex-1 w-full">
          <Search className="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Search study material, nowcasting guide, radar calibration..."
            className="glass-input w-full pl-10 pr-4 py-2 rounded-xl text-xs"
          />
        </div>

        <div className="flex items-center gap-2 w-full md:w-auto">
          <select
            value={formatFilter}
            onChange={(e) => setFormatFilter(e.target.value)}
            className="glass-input px-3 py-2 rounded-xl text-xs bg-slate-900 text-slate-300 w-1/2 md:w-auto"
          >
            <option value="ALL">All Formats</option>
            <option value="PDF">PDF Documents</option>
            <option value="PPT">PPT Slide Decks</option>
            <option value="VIDEO">Video Masterclasses</option>
          </select>

          <select
            value={subjectFilter}
            onChange={(e) => setSubjectFilter(e.target.value)}
            className="glass-input px-3 py-2 rounded-xl text-xs bg-slate-900 text-slate-300 w-1/2 md:w-auto"
          >
            <option value="ALL">All Subjects</option>
            <option value="Radar Meteorology">Radar Meteorology</option>
            <option value="Satellite Meteorology">Satellite Meteorology</option>
            <option value="Numerical Weather Prediction (NWP)">NWP Modeling</option>
          </select>
        </div>
      </div>

      {/* Resource Grid */}
      {loading ? (
        <div className="py-20 flex justify-center">
          <div className="w-8 h-8 border-4 border-imd-500/30 border-t-imd-500 rounded-full animate-spin" />
        </div>
      ) : filtered.length === 0 ? (
        <div className="glass-card rounded-3xl p-12 text-center border border-slate-800">
          <Library className="w-12 h-12 text-slate-600 mx-auto mb-3" />
          <h3 className="text-sm font-bold text-slate-300">No resources found</h3>
          <p className="text-xs text-slate-500 mt-1">Try relaxing your format and subject filters.</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {filtered.map((r) => (
            <div
              key={r.id}
              className="glass-card rounded-3xl p-5 border border-slate-800 flex flex-col justify-between hover:border-imd-500/40 transition-all group"
            >
              <div>
                <div className="flex items-center justify-between mb-3">
                  <div className="p-2.5 rounded-xl bg-slate-900 border border-slate-800 shadow-inner">
                    {getFormatIcon(r.format)}
                  </div>
                  <span className="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-400">
                    {r.format}
                  </span>
                </div>

                <h3 className="text-sm font-bold text-white group-hover:text-imd-300 transition-colors line-clamp-2">
                  {r.title}
                </h3>
                <p className="text-[11px] text-slate-400 mt-1.5 line-clamp-2 leading-relaxed">
                  {r.description || 'Curated study material for operational capacity development.'}
                </p>

                <div className="flex flex-wrap gap-1.5 mt-3">
                  <span className="text-[10px] px-2 py-0.5 rounded-md bg-imd-500/15 text-imd-300 border border-imd-500/30 font-medium">
                    {r.subject}
                  </span>
                  {r.course && (
                    <span className="text-[10px] px-2 py-0.5 rounded-md bg-slate-800 text-slate-300">
                      {r.course.code}
                    </span>
                  )}
                </div>
              </div>

              <div className="mt-5 pt-4 border-t border-slate-800 flex items-center justify-between">
                <div className="text-[11px] text-slate-400">
                  By <strong className="text-slate-200">{r.trainer.name}</strong>
                  <span className="block text-[10px] text-slate-500">{r.downloadCount} views/accesses</span>
                </div>

                <button
                  onClick={() => handleAccess(r.id, r.fileUrl)}
                  className="px-3.5 py-2 rounded-xl bg-imd-600 hover:bg-imd-500 text-white text-xs font-semibold flex items-center gap-1.5 transition-all shadow-md shadow-imd-600/20"
                >
                  <Download className="w-3.5 h-3.5" /> Access
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};
