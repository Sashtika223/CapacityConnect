import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../../services/api';
import { Search, Filter, BookOpen, Clock, User, Award, CheckCircle2 } from 'lucide-react';

export const CourseCatalogPage: React.FC = () => {
  const [courses, setCourses] = useState<any[]>([]);
  const [search, setSearch] = useState('');
  const [selectedSubject, setSelectedSubject] = useState('ALL');
  const [selectedLevel, setSelectedLevel] = useState('ALL');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchCourses();
  }, [selectedSubject, selectedLevel]);

  const fetchCourses = async () => {
    setLoading(true);
    try {
      const params: any = {};
      if (selectedSubject !== 'ALL') params.subject = selectedSubject;
      if (selectedLevel !== 'ALL') params.level = selectedLevel;

      const res = await api.get('/courses', { params });
      if (res.data.success) {
        setCourses(res.data.courses);
      }
    } finally {
      setLoading(false);
    }
  };

  const filteredCourses = courses.filter((c) => {
    const q = search.toLowerCase();
    return (
      c.title.toLowerCase().includes(q) ||
      c.code.toLowerCase().includes(q) ||
      c.description.toLowerCase().includes(q) ||
      c.subject.toLowerCase().includes(q)
    );
  });

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-white tracking-tight">IMD Training Catalog</h1>
          <p className="text-xs text-slate-400 mt-1">
            Official operational courses and capacity building programmes for meteorological scientists
          </p>
        </div>
      </div>

      {/* Filter and Search Bar */}
      <div className="glass-panel p-4 rounded-2xl border border-slate-800 flex flex-col md:flex-row items-center gap-3">
        <div className="relative flex-1 w-full">
          <Search className="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Search courses by keyword, code, radar, satellite, NWP..."
            className="glass-input w-full pl-10 pr-4 py-2 rounded-xl text-xs"
          />
        </div>

        <div className="flex items-center gap-2 w-full md:w-auto">
          <select
            value={selectedSubject}
            onChange={(e) => setSelectedSubject(e.target.value)}
            className="glass-input px-3 py-2 rounded-xl text-xs bg-slate-900 text-slate-300 w-1/2 md:w-auto"
          >
            <option value="ALL">All Subjects</option>
            <option value="Radar Meteorology">Radar Meteorology</option>
            <option value="Satellite Meteorology">Satellite Meteorology</option>
            <option value="Numerical Weather Prediction (NWP)">NWP Modeling</option>
          </select>

          <select
            value={selectedLevel}
            onChange={(e) => setSelectedLevel(e.target.value)}
            className="glass-input px-3 py-2 rounded-xl text-xs bg-slate-900 text-slate-300 w-1/2 md:w-auto"
          >
            <option value="ALL">All Levels</option>
            <option value="BEGINNER">Beginner</option>
            <option value="INTERMEDIATE">Intermediate</option>
            <option value="ADVANCED">Advanced</option>
          </select>
        </div>
      </div>

      {/* Course Grid */}
      {loading ? (
        <div className="py-20 flex justify-center">
          <div className="w-8 h-8 border-4 border-imd-500/30 border-t-imd-500 rounded-full animate-spin" />
        </div>
      ) : filteredCourses.length === 0 ? (
        <div className="glass-card rounded-3xl p-12 text-center">
          <BookOpen className="w-12 h-12 text-slate-600 mx-auto mb-3" />
          <h3 className="text-sm font-bold text-slate-300">No courses match your filter</h3>
          <p className="text-xs text-slate-500 mt-1">Try searching for other keywords or clearing the subject filters.</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {filteredCourses.map((c) => (
            <div
              key={c.id}
              className="glass-card rounded-3xl p-5 border border-slate-800 flex flex-col justify-between hover:border-imd-500/40 transition-all group"
            >
              <div>
                <div className="flex items-center justify-between gap-2 mb-2.5">
                  <span className="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-imd-500/20 text-imd-300 border border-imd-500/30">
                    {c.code}
                  </span>
                  <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${
                    c.level === 'ADVANCED' ? 'bg-purple-500/20 text-purple-300' :
                    c.level === 'INTERMEDIATE' ? 'bg-blue-500/20 text-blue-300' :
                    'bg-emerald-500/20 text-emerald-300'
                  }`}>
                    {c.level}
                  </span>
                </div>

                <h3 className="text-sm font-bold text-white group-hover:text-imd-300 transition-colors line-clamp-2">
                  {c.title}
                </h3>
                <p className="text-[11px] text-slate-400 mt-2 line-clamp-3 leading-relaxed">
                  {c.description}
                </p>

                {/* Course Tags */}
                <div className="flex flex-wrap gap-1.5 mt-3">
                  {(c.tags || []).slice(0, 3).map((tag: string) => (
                    <span key={tag} className="text-[9px] px-2 py-0.5 rounded-md bg-slate-800/80 text-slate-300 border border-slate-700/60">
                      {tag}
                    </span>
                  ))}
                </div>
              </div>

              <div className="mt-5 pt-4 border-t border-slate-800">
                <div className="flex items-center justify-between text-[11px] text-slate-400 mb-3">
                  <span className="flex items-center gap-1.5">
                    <User className="w-3.5 h-3.5 text-slate-500" />
                    {c.trainer.name}
                  </span>
                  <span className="flex items-center gap-1">
                    <Clock className="w-3.5 h-3.5 text-slate-500" />
                    {c.durationHours}h
                  </span>
                </div>

                <Link
                  to={`/courses/${c.id}`}
                  className="block w-full py-2.5 px-4 rounded-xl text-center text-xs font-semibold bg-slate-800 hover:bg-imd-600 text-slate-200 hover:text-white transition-all shadow-sm group-hover:shadow-imd-500/20"
                >
                  View Syllabus & Enroll
                </Link>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};
