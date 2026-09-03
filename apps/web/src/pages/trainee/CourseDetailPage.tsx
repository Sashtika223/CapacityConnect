import React, { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { api } from '../../services/api';
import { BookOpen, Clock, User, Award, CheckCircle, FileText, Play, Download, Star, MessageSquare, Send, ShieldCheck } from 'lucide-react';

export const CourseDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const [course, setCourse] = useState<any>(null);
  const [enrollment, setEnrollment] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [enrolling, setEnrolling] = useState(false);

  // Feedback form state
  const [rating, setRating] = useState(5);
  const [contentRating, setContentRating] = useState(5);
  const [deliveryRating, setDeliveryRating] = useState(5);
  const [comments, setComments] = useState('');
  const [submittingFeedback, setSubmittingFeedback] = useState(false);
  const [feedbackSuccess, setFeedbackSuccess] = useState<any>(null);

  const navigate = useNavigate();

  useEffect(() => {
    fetchCourseDetails();
  }, [id]);

  const fetchCourseDetails = async () => {
    setLoading(true);
    try {
      const res = await api.get(`/courses/${id}`);
      if (res.data.success) {
        setCourse(res.data.course);
        setEnrollment(res.data.enrollment);
      }
    } finally {
      setLoading(false);
    }
  };

  const handleEnroll = async () => {
    setEnrolling(true);
    try {
      const res = await api.post('/courses/enroll', { courseId: id });
      if (res.data.success) {
        setEnrollment(res.data.enrollment);
      }
    } catch (err: any) {
      alert(err.response?.data?.message || 'Enrollment failed');
    } finally {
      setEnrolling(false);
    }
  };

  const handleAccessResource = async (resId: string, fileUrl: string) => {
    try {
      await api.post(`/resources/${resId}/access`);
      // Open or download resource
      window.open(fileUrl, '_blank');
      fetchCourseDetails();
    } catch {
      window.open(fileUrl, '_blank');
    }
  };

  const handleSubmitFeedback = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!comments.trim()) return;

    setSubmittingFeedback(true);
    try {
      const res = await api.post('/trainee/feedback', {
        courseId: id,
        rating,
        contentRating,
        deliveryRating,
        comments
      });

      if (res.data.success) {
        setFeedbackSuccess(res.data);
        setComments('');
        fetchCourseDetails();
      }
    } catch (err: any) {
      alert(err.response?.data?.message || 'Failed to submit review');
    } finally {
      setSubmittingFeedback(false);
    }
  };

  if (loading) {
    return (
      <div className="py-20 flex justify-center">
        <div className="w-10 h-10 border-4 border-imd-500/30 border-t-imd-500 rounded-full animate-spin" />
      </div>
    );
  }

  if (!course) {
    return <div className="text-center py-20 text-slate-400">Course not found.</div>;
  }

  return (
    <div className="max-w-5xl mx-auto space-y-8">
      {/* Course Header Banner */}
      <div className="glass-panel rounded-3xl p-6 sm:p-8 border border-slate-800 relative overflow-hidden">
        <div className="flex flex-col md:flex-row md:items-start justify-between gap-6 relative z-10">
          <div className="flex-1">
            <div className="flex items-center gap-2 mb-2">
              <span className="text-[10px] font-extrabold px-2.5 py-0.5 rounded-md bg-imd-500/20 text-imd-300 border border-imd-500/30">
                {course.code}
              </span>
              <span className="text-xs text-moes-500 font-bold uppercase tracking-wider">
                {course.category}
              </span>
              <span className="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300">
                {course.level}
              </span>
            </div>

            <h1 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
              {course.title}
            </h1>
            <p className="text-xs sm:text-sm text-slate-300 mt-3 leading-relaxed">
              {course.description}
            </p>

            <div className="flex flex-wrap items-center gap-4 sm:gap-6 mt-5 pt-4 border-t border-slate-800/80 text-xs text-slate-400">
              <div className="flex items-center gap-1.5">
                <User className="w-4 h-4 text-imd-400" />
                <span>Instructor: <strong className="text-white">{course.trainer.name}</strong></span>
              </div>
              <div className="flex items-center gap-1.5">
                <Clock className="w-4 h-4 text-moes-500" />
                <span>Duration: <strong className="text-white">{course.durationHours} Hours</strong></span>
              </div>
              <div className="flex items-center gap-1.5">
                <Award className="w-4 h-4 text-amber-400" />
                <span>Passing Grade: <strong className="text-white">{course.passingScore}%</strong></span>
              </div>
            </div>
          </div>

          {/* Enrollment CTA Card */}
          <div className="w-full md:w-64 p-5 rounded-2xl bg-slate-900/90 border border-slate-700/60 shadow-xl shrink-0 text-center">
            {enrollment ? (
              <div className="space-y-3">
                <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 text-xs font-bold">
                  <CheckCircle className="w-3.5 h-3.5" /> Enrolled
                </div>
                <div>
                  <div className="flex justify-between text-xs text-slate-400 mb-1">
                    <span>Progress</span>
                    <span className="font-bold text-white">{enrollment.progressPercentage}%</span>
                  </div>
                  <div className="w-full bg-slate-800 rounded-full h-2">
                    <div
                      className="bg-gradient-to-r from-imd-500 to-moes-500 h-2 rounded-full"
                      style={{ width: `${enrollment.progressPercentage}%` }}
                    />
                  </div>
                </div>

                {enrollment.certificate && (
                  <Link
                    to="/certificates"
                    className="block w-full py-2 px-3 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 text-xs font-bold transition-all"
                  >
                    🏆 View Certificate
                  </Link>
                )}
              </div>
            ) : (
              <div className="space-y-3">
                <p className="text-xs text-slate-400">Enroll to access learning materials, take adaptive tests, and receive an accredited MoES certificate.</p>
                <button
                  onClick={handleEnroll}
                  disabled={enrolling}
                  className="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-imd-600 to-moes-600 hover:from-imd-500 hover:to-moes-500 text-white font-semibold text-xs shadow-lg shadow-imd-600/30 transition-all disabled:opacity-50"
                >
                  {enrolling ? 'Enrolling...' : 'Enroll in Programme'}
                </button>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Course Resources & Study Material */}
      <div className="space-y-4">
        <h2 className="text-lg font-bold text-white flex items-center gap-2">
          <FileText className="w-5 h-5 text-imd-400" /> Trainer Library & Study Material ({course.resources?.length || 0})
        </h2>

        {course.resources?.length === 0 ? (
          <p className="text-xs text-slate-500">No downloadable files attached to this module yet.</p>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {course.resources.map((res: any) => (
              <div
                key={res.id}
                className="glass-card rounded-2xl p-4 border border-slate-800 flex items-center justify-between gap-3"
              >
                <div className="flex items-center gap-3 min-w-0">
                  <div className="w-10 h-10 rounded-xl bg-imd-500/10 border border-imd-500/20 flex items-center justify-center text-imd-400 shrink-0">
                    <FileText className="w-5 h-5" />
                  </div>
                  <div className="min-w-0">
                    <h4 className="text-xs font-bold text-white truncate">{res.title}</h4>
                    <p className="text-[10px] text-slate-400 mt-0.5">{res.format} • {res.subject}</p>
                  </div>
                </div>

                <button
                  onClick={() => handleAccessResource(res.id, res.fileUrl)}
                  className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors shrink-0"
                  title="Access / Download"
                >
                  <Download className="w-4 h-4" />
                </button>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Assessment Questionnaires */}
      <div className="space-y-4">
        <h2 className="text-lg font-bold text-white flex items-center gap-2">
          <Award className="w-5 h-5 text-purple-400" /> MCQ Assessments & Certification
        </h2>

        {course.questionnaires?.length === 0 ? (
          <p className="text-xs text-slate-500">No active assessments configured for this course yet.</p>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {course.questionnaires.map((q: any) => (
              <div key={q.id} className="glass-card rounded-2xl p-5 border border-purple-500/20">
                <div className="flex items-center justify-between text-xs mb-2">
                  <span className="font-bold text-purple-300 uppercase tracking-wider">
                    {q.isAdaptive ? 'Adaptive Engine (IRT-lite)' : 'Standard MCQ'}
                  </span>
                  <span className="text-slate-400 font-medium">{q.durationMinutes} Mins</span>
                </div>
                <h4 className="text-sm font-bold text-white mb-2">{q.title}</h4>
                <p className="text-[11px] text-slate-400 leading-relaxed mb-4">{q.description}</p>

                <div className="flex items-center justify-between pt-3 border-t border-slate-800/80">
                  <span className="text-[11px] text-slate-400">Passing: {q.passingScore}%</span>
                  <Link
                    to={`/assessments/${q.id}`}
                    className="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-semibold shadow-md shadow-purple-600/30 flex items-center gap-1.5 transition-all"
                  >
                    <Play className="w-3.5 h-3.5" /> Start Test
                  </Link>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Feedback & Review Form (Innovation #9 Sentiment Triage) */}
      <div className="glass-card rounded-3xl p-6 sm:p-8 border border-slate-800 space-y-6">
        <div>
          <h2 className="text-base font-bold text-white flex items-center gap-2">
            <MessageSquare className="w-5 h-5 text-moes-500" /> Submit Course & Trainer Feedback
          </h2>
          <p className="text-xs text-slate-400 mt-1">
            Your review will be automatically triaged using our rule-based sentiment classifier to support continuous curriculum enhancement.
          </p>
        </div>

        {feedbackSuccess && (
          <div className="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30">
            <div className="flex items-center gap-2 text-xs font-bold text-emerald-400">
              <ShieldCheck className="w-4 h-4" /> Feedback Recorded & Sentiment Triaged!
            </div>
            <p className="text-xs text-slate-300 mt-1">{feedbackSuccess.message}</p>
            <div className="mt-2 flex items-center gap-3 text-[11px]">
              <span className="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-bold">
                Sentiment: {feedbackSuccess.sentimentAnalysis?.tag}
              </span>
              <span className="text-slate-400">
                Score: {feedbackSuccess.sentimentAnalysis?.score}
              </span>
            </div>
          </div>
        )}

        <form onSubmit={handleSubmitFeedback} className="space-y-4">
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label className="block text-xs font-semibold text-slate-300 mb-1.5">Overall Rating</label>
              <div className="flex items-center gap-1">
                {[1, 2, 3, 4, 5].map((star) => (
                  <button
                    key={star}
                    type="button"
                    onClick={() => setRating(star)}
                    className="p-1 text-slate-600 hover:text-amber-400 transition-colors"
                  >
                    <Star className={`w-5 h-5 ${star <= rating ? 'text-amber-400 fill-amber-400' : ''}`} />
                  </button>
                ))}
              </div>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-300 mb-1.5">Content Quality</label>
              <select
                value={contentRating}
                onChange={(e) => setContentRating(Number(e.target.value))}
                className="glass-input w-full px-3 py-1.5 rounded-xl text-xs bg-slate-900"
              >
                <option value={5}>5 - Excellent & Comprehensive</option>
                <option value={4}>4 - Good and Relevant</option>
                <option value={3}>3 - Average</option>
                <option value={2}>2 - Below Expectation</option>
                <option value={1}>1 - Incomplete</option>
              </select>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-300 mb-1.5">Trainer Delivery</label>
              <select
                value={deliveryRating}
                onChange={(e) => setDeliveryRating(Number(e.target.value))}
                className="glass-input w-full px-3 py-1.5 rounded-xl text-xs bg-slate-900"
              >
                <option value={5}>5 - Outstanding Clarity</option>
                <option value={4}>4 - Clear and Helpful</option>
                <option value={3}>3 - Moderate</option>
                <option value={2}>2 - Hard to Follow</option>
                <option value={1}>1 - Unclear</option>
              </select>
            </div>
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-300 mb-1.5">Your Review & Comments</label>
            <textarea
              required
              rows={3}
              value={comments}
              onChange={(e) => setComments(e.target.value)}
              placeholder="Detail your experience with the lectures, simulations, and radar/satellite case studies..."
              className="glass-input w-full p-3 rounded-2xl text-xs placeholder-slate-500"
            />
          </div>

          <button
            type="submit"
            disabled={submittingFeedback}
            className="px-5 py-2.5 rounded-xl bg-imd-600 hover:bg-imd-500 text-white text-xs font-semibold flex items-center gap-2 shadow-md shadow-imd-600/30 transition-all disabled:opacity-50"
          >
            <Send className="w-3.5 h-3.5" /> Submit Review
          </button>
        </form>
      </div>
    </div>
  );
};
