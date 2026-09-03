import React, { useState, useEffect } from 'react';
import { api } from '../../services/api';
import { FileCheck2, Plus, Trash2, CheckCircle2, Sparkles, Save, HelpCircle, Layers } from 'lucide-react';

export const QuestionnaireBuilderPage: React.FC = () => {
  const [courses, setCourses] = useState<any[]>([]);
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [courseId, setCourseId] = useState('');
  const [isAdaptive, setIsAdaptive] = useState(false);
  const [durationMinutes, setDurationMinutes] = useState(30);
  const [passingScore, setPassingScore] = useState(70);
  const [questions, setQuestions] = useState<any[]>([
    {
      text: 'What radar product is best suited for detecting horizontal wind shear in the planetary boundary layer?',
      explanation: 'Radial velocity slices with Velocity Azimuth Display (VAD) wind profiles resolve low-level shear.',
      difficulty: 'MEDIUM',
      points: 2,
      options: [
        { text: 'Velocity Azimuth Display (VAD) Wind Profile', isCorrect: true },
        { text: 'Total Water Content Index', isCorrect: false },
        { text: 'Echo Tops Height Map', isCorrect: false },
        { text: 'Zero Isotherm Range Ring', isCorrect: false }
      ]
    }
  ]);

  const [saving, setSaving] = useState(false);
  const [successMsg, setSuccessMsg] = useState('');

  useEffect(() => {
    api.get('/courses').then((res) => {
      if (res.data.success) setCourses(res.data.courses);
    });
  }, []);

  const handleAddQuestion = () => {
    setQuestions([
      ...questions,
      {
        text: '',
        explanation: '',
        difficulty: 'MEDIUM',
        points: 2,
        options: [
          { text: '', isCorrect: true },
          { text: '', isCorrect: false },
          { text: '', isCorrect: false },
          { text: '', isCorrect: false }
        ]
      }
    ]);
  };

  const handleRemoveQuestion = (idx: number) => {
    setQuestions(questions.filter((_, i) => i !== idx));
  };

  const handleQuestionChange = (idx: number, field: string, value: any) => {
    const updated = [...questions];
    updated[idx][field] = value;
    setQuestions(updated);
  };

  const handleOptionChange = (qIdx: number, optIdx: number, text: string) => {
    const updated = [...questions];
    updated[qIdx].options[optIdx].text = text;
    setQuestions(updated);
  };

  const handleSetCorrectOption = (qIdx: number, optIdx: number) => {
    const updated = [...questions];
    updated[qIdx].options = updated[qIdx].options.map((opt: any, i: number) => ({
      ...opt,
      isCorrect: i === optIdx
    }));
    setQuestions(updated);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!title || questions.length === 0) return;

    setSaving(true);
    setSuccessMsg('');

    try {
      const res = await api.post('/assessments', {
        title,
        description,
        courseId: courseId || undefined,
        isAdaptive,
        durationMinutes: Number(durationMinutes),
        passingScore: Number(passingScore),
        questions
      });

      if (res.data.success) {
        setSuccessMsg('MCQ Assessment Questionnaire created and published successfully!');
        setTitle('');
        setDescription('');
      }
    } catch (err: any) {
      alert(err.response?.data?.message || 'Error creating questionnaire');
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      <div>
        <h1 className="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
          <FileCheck2 className="w-6 h-6 text-emerald-400" /> MCQ & Assessment Builder
        </h1>
        <p className="text-xs text-slate-400 mt-1">
          Create calibrated question sets with Easy, Medium, and Hard tiers. Toggle Adaptive Mode to activate dynamic IRT-lite testing.
        </p>
      </div>

      {successMsg && (
        <div className="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-xs text-emerald-300 flex items-center gap-2 font-bold">
          <CheckCircle2 className="w-4 h-4" /> {successMsg}
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Questionnaire Metadata */}
        <div className="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800 space-y-4">
          <div>
            <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
              Assessment Title
            </label>
            <input
              type="text"
              required
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              placeholder="e.g. Advanced Doppler Weather Radar Diagnostics Test"
              className="glass-input w-full px-3.5 py-2.5 rounded-xl text-sm font-semibold"
            />
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Target Course
              </label>
              <select
                value={courseId}
                onChange={(e) => setCourseId(e.target.value)}
                className="glass-input w-full px-3 py-2 rounded-xl text-xs bg-slate-900"
              >
                <option value="">Standalone / Unlinked</option>
                {courses.map((c) => (
                  <option key={c.id} value={c.id}>
                    {c.code} - {c.title}
                  </option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Duration (Minutes)
              </label>
              <input
                type="number"
                min="5"
                value={durationMinutes}
                onChange={(e) => setDurationMinutes(Number(e.target.value))}
                className="glass-input w-full px-3 py-2 rounded-xl text-xs"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                Passing Grade (%)
              </label>
              <input
                type="number"
                min="50"
                max="100"
                value={passingScore}
                onChange={(e) => setPassingScore(Number(e.target.value))}
                className="glass-input w-full px-3 py-2 rounded-xl text-xs"
              />
            </div>
          </div>

          {/* Adaptive Engine Mode Toggle */}
          <div className="pt-3 border-t border-slate-800 flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="p-2 rounded-xl bg-purple-500/20 text-purple-400">
                <Sparkles className="w-5 h-5" />
              </div>
              <div>
                <h4 className="text-xs font-bold text-white">Enable Adaptive MCQ Engine (IRT-lite)</h4>
                <p className="text-[11px] text-slate-400">Dynamically adjust difficulty based on trainee streaks and running accuracy</p>
              </div>
            </div>

            <label className="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                checked={isAdaptive}
                onChange={(e) => setIsAdaptive(e.target.checked)}
                className="sr-only peer"
              />
              <div className="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
            </label>
          </div>
        </div>

        {/* Questions List */}
        <div className="space-y-4">
          <div className="flex items-center justify-between">
            <h2 className="text-sm font-bold text-white uppercase tracking-wider">
              Questions ({questions.length})
            </h2>
            <button
              type="button"
              onClick={handleAddQuestion}
              className="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 border border-slate-700"
            >
              <Plus className="w-3.5 h-3.5 text-emerald-400" /> Add Question
            </button>
          </div>

          {questions.map((q, qIdx) => (
            <div key={qIdx} className="glass-card rounded-3xl p-6 border border-slate-800 space-y-4 relative">
              <div className="flex items-center justify-between gap-3">
                <span className="text-xs font-bold text-slate-300">Question #{qIdx + 1}</span>
                <div className="flex items-center gap-3">
                  <select
                    value={q.difficulty}
                    onChange={(e) => handleQuestionChange(qIdx, 'difficulty', e.target.value)}
                    className="glass-input px-2.5 py-1 rounded-lg text-xs bg-slate-900 text-slate-300"
                  >
                    <option value="EASY">Easy</option>
                    <option value="MEDIUM">Medium</option>
                    <option value="HARD">Hard</option>
                  </select>

                  <input
                    type="number"
                    min="1"
                    value={q.points}
                    onChange={(e) => handleQuestionChange(qIdx, 'points', Number(e.target.value))}
                    className="glass-input w-16 px-2 py-1 rounded-lg text-xs"
                    title="Points"
                  />

                  {questions.length > 1 && (
                    <button
                      type="button"
                      onClick={() => handleRemoveQuestion(qIdx)}
                      className="p-1.5 text-slate-500 hover:text-rose-400 transition-colors"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  )}
                </div>
              </div>

              <div>
                <input
                  type="text"
                  required
                  value={q.text}
                  onChange={(e) => handleQuestionChange(qIdx, 'text', e.target.value)}
                  placeholder="Enter meteorological question prompt..."
                  className="glass-input w-full px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium"
                />
              </div>

              {/* Options */}
              <div className="space-y-2 pt-2">
                <p className="text-[10px] uppercase font-bold text-slate-500">
                  Select the radio button for the correct option:
                </p>
                {q.options.map((opt: any, optIdx: number) => (
                  <div key={optIdx} className="flex items-center gap-2">
                    <input
                      type="radio"
                      name={`correct-${qIdx}`}
                      checked={opt.isCorrect}
                      onChange={() => handleSetCorrectOption(qIdx, optIdx)}
                      className="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 cursor-pointer"
                    />
                    <input
                      type="text"
                      required
                      value={opt.text}
                      onChange={(e) => handleOptionChange(qIdx, optIdx, e.target.value)}
                      placeholder={`Option ${String.fromCharCode(65 + optIdx)}`}
                      className="glass-input flex-1 px-3 py-1.5 rounded-xl text-xs"
                    />
                  </div>
                ))}
              </div>

              <div>
                <input
                  type="text"
                  value={q.explanation}
                  onChange={(e) => handleQuestionChange(qIdx, 'explanation', e.target.value)}
                  placeholder="Scientific explanation for post-assessment review..."
                  className="glass-input w-full px-3 py-1.5 rounded-xl text-[11px] text-slate-300"
                />
              </div>
            </div>
          ))}
        </div>

        <div className="flex justify-end pt-4">
          <button
            type="submit"
            disabled={saving}
            className="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-moes-600 hover:from-emerald-500 hover:to-moes-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/30 flex items-center gap-2 disabled:opacity-50"
          >
            <Save className="w-4 h-4" /> {saving ? 'Publishing...' : 'Save & Publish Questionnaire'}
          </button>
        </div>
      </form>
    </div>
  );
};
