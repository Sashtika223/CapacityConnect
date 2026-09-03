import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { api } from '../../services/api';
import confetti from 'canvas-confetti';
import { Clock, ShieldAlert, Award, CheckCircle2, XCircle, ArrowRight, Sparkles, RefreshCw, ChevronRight, Zap } from 'lucide-react';

export const MCQPlayerPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const [quiz, setQuiz] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  // Assessment flow state
  const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
  const [selectedOptionId, setSelectedOptionId] = useState<string>('');
  const [answers, setAnswers] = useState<any[]>([]); // { questionId, selectedOptionId }
  const [secondsRemaining, setSecondsRemaining] = useState(1800);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [result, setResult] = useState<any>(null);

  // Adaptive Engine State (IRT-lite)
  const [isAdaptive, setIsAdaptive] = useState(false);
  const [adaptiveHistory, setAdaptiveHistory] = useState<any[]>([]);
  const [adaptiveQuestion, setAdaptiveQuestion] = useState<any>(null);
  const [adaptiveStreak, setAdaptiveStreak] = useState(0);
  const [adaptiveAccuracy, setAdaptiveAccuracy] = useState(0);
  const [estimatedAbility, setEstimatedAbility] = useState('INTERMEDIATE');
  const [questionCount, setQuestionCount] = useState(1);
  const [totalPlanned, setTotalPlanned] = useState(5);

  const navigate = useNavigate();

  useEffect(() => {
    fetchQuiz();
  }, [id]);

  // Countdown timer
  useEffect(() => {
    if (!quiz || result) return;
    const timer = setInterval(() => {
      setSecondsRemaining((prev) => {
        if (prev <= 1) {
          clearInterval(timer);
          handleSubmitTest();
          return 0;
        }
        return prev - 1;
      });
    }, 1000);
    return () => clearInterval(timer);
  }, [quiz, result, answers]);

  const fetchQuiz = async () => {
    setLoading(true);
    try {
      const res = await api.get(`/assessments/${id}`);
      if (res.data.success) {
        const qData = res.data.questionnaire;
        setQuiz(qData);
        setSecondsRemaining(qData.durationMinutes * 60);

        if (qData.isAdaptive) {
          setIsAdaptive(true);
          fetchNextAdaptiveQuestion([], qData.id);
        }
      }
    } finally {
      setLoading(false);
    }
  };

  const fetchNextAdaptiveQuestion = async (history: any[], quizId?: string) => {
    try {
      const res = await api.post('/assessments/adaptive/next', {
        questionnaireId: quizId || id,
        history
      });

      if (res.data.success) {
        if (res.data.isComplete) {
          // Finished all adaptive questions -> submit test
          submitAdaptiveAnswers(history);
        } else {
          setAdaptiveQuestion(res.data.question);
          setAdaptiveStreak(res.data.currentStreak);
          setAdaptiveAccuracy(res.data.runningAccuracy);
          setEstimatedAbility(res.data.estimatedAbilityLevel);
          setQuestionCount(res.data.questionNumber);
          setTotalPlanned(res.data.totalPlannedQuestions);
          setSelectedOptionId('');
        }
      }
    } catch (err) {
      console.error('Error fetching adaptive question:', err);
    }
  };

  const handleNextAdaptive = () => {
    if (!selectedOptionId) return;

    // Evaluate current answer optimistically
    const selectedOpt = adaptiveQuestion.options.find((o: any) => o.id === selectedOptionId);
    // Note: Backend validates truth upon final submit, but for IRT streak we pass our choice
    const newHistoryItem = {
      questionId: adaptiveQuestion.id,
      selectedOptionId,
      difficulty: adaptiveQuestion.difficulty,
      // For demonstration, track answer
      isCorrect: true // Will be scored accurately by backend
    };

    const nextHistory = [...adaptiveHistory, newHistoryItem];
    setAdaptiveHistory(nextHistory);
    setAnswers((prev) => [...prev, { questionId: adaptiveQuestion.id, selectedOptionId }]);

    fetchNextAdaptiveQuestion(nextHistory);
  };

  const submitAdaptiveAnswers = async (finalHistory: any[]) => {
    setIsSubmitting(true);
    try {
      const answersPayload = finalHistory.map((h) => ({
        questionId: h.questionId,
        selectedOptionId: h.selectedOptionId
      }));

      const res = await api.post('/assessments/submit', {
        questionnaireId: id,
        answers: answersPayload,
        durationSeconds: (quiz?.durationMinutes || 30) * 60 - secondsRemaining,
        isAdaptive: true
      });

      if (res.data.success) {
        setResult(res.data);
        if (res.data.attempt.passed) {
          confetti({ particleCount: 120, spread: 70, origin: { y: 0.6 } });
        }
      }
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleSelectOptionStandard = (optId: string) => {
    setSelectedOptionId(optId);
    const currQ = quiz.questions[currentQuestionIndex];
    setAnswers((prev) => {
      const filtered = prev.filter((a) => a.questionId !== currQ.id);
      return [...filtered, { questionId: currQ.id, selectedOptionId: optId }];
    });
  };

  const handleSubmitTest = async () => {
    setIsSubmitting(true);
    try {
      const res = await api.post('/assessments/submit', {
        questionnaireId: id,
        answers,
        durationSeconds: (quiz?.durationMinutes || 30) * 60 - secondsRemaining,
        isAdaptive: false
      });

      if (res.data.success) {
        setResult(res.data);
        if (res.data.attempt.passed) {
          confetti({ particleCount: 120, spread: 70, origin: { y: 0.6 } });
        }
      }
    } finally {
      setIsSubmitting(false);
    }
  };

  const formatTime = (secs: number) => {
    const mins = Math.floor(secs / 60);
    const remSecs = secs % 60;
    return `${mins}:${remSecs < 10 ? '0' : ''}${remSecs}`;
  };

  if (loading) {
    return (
      <div className="py-20 flex justify-center">
        <div className="w-10 h-10 border-4 border-imd-500/30 border-t-imd-500 rounded-full animate-spin" />
      </div>
    );
  }

  // Result View Screen
  if (result) {
    const { attempt, certificate, certificateEarned } = result;

    return (
      <div className="max-w-3xl mx-auto space-y-6">
        <div className="glass-panel rounded-3xl p-8 border border-slate-800 text-center relative overflow-hidden">
          <div className="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl mb-4">
            {attempt.passed ? (
              <Award className="w-8 h-8 text-amber-400" />
            ) : (
              <XCircle className="w-8 h-8 text-rose-400" />
            )}
          </div>

          <h2 className="text-2xl font-extrabold text-white">
            {attempt.passed ? 'Assessment Successfully Passed!' : 'Assessment Completed'}
          </h2>
          <p className="text-xs text-slate-400 mt-1">
            {attempt.passed
              ? 'You have met the required passing criteria set by IMD capacity guidelines.'
              : 'Your score did not meet the passing grade threshold. Review explanations below and re-attempt.'}
          </p>

          <div className="grid grid-cols-3 gap-3 max-w-md mx-auto my-6 p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
            <div>
              <p className="text-[10px] text-slate-400 uppercase font-bold">Your Score</p>
              <p className="text-xl font-extrabold text-white mt-1">{attempt.percentage}%</p>
            </div>
            <div>
              <p className="text-[10px] text-slate-400 uppercase font-bold">Passing Grade</p>
              <p className="text-xl font-extrabold text-slate-300 mt-1">{attempt.passingScore}%</p>
            </div>
            <div>
              <p className="text-[10px] text-slate-400 uppercase font-bold">Status</p>
              <p className={`text-xl font-extrabold mt-1 ${attempt.passed ? 'text-emerald-400' : 'text-rose-400'}`}>
                {attempt.passed ? 'PASSED' : 'FAILED'}
              </p>
            </div>
          </div>

          {/* Certificate Award Banner */}
          {certificateEarned && certificate && (
            <div className="mb-6 p-5 rounded-2xl bg-gradient-to-r from-amber-500/10 via-slate-900 to-emerald-500/10 border border-amber-500/30 text-left">
              <div className="flex items-center justify-between">
                <div>
                  <span className="text-[10px] font-extrabold px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 uppercase tracking-wider">
                    Official Certification Generated
                  </span>
                  <h4 className="text-sm font-bold text-white mt-1">
                    {certificate.course?.title || quiz.course?.title}
                  </h4>
                  <p className="text-xs text-slate-400 mt-0.5">
                    Cert No: <strong className="text-slate-200">{certificate.certificateNumber}</strong> • Grade: {certificate.grade}
                  </p>
                </div>
                <Link
                  to="/certificates"
                  className="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20 transition-all shrink-0"
                >
                  View Certificate →
                </Link>
              </div>
            </div>
          )}

          <div className="flex justify-center gap-3">
            <Link
              to="/dashboard"
              className="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold"
            >
              Return to Dashboard
            </Link>
            <button
              onClick={() => window.location.reload()}
              className="px-5 py-2.5 rounded-xl bg-imd-600 hover:bg-imd-500 text-white text-xs font-semibold flex items-center gap-1.5"
            >
              <RefreshCw className="w-3.5 h-3.5" /> Retake Test
            </button>
          </div>
        </div>

        {/* Detailed Question Review with Explanations */}
        <div className="space-y-4">
          <h3 className="text-sm font-bold text-slate-200">Answer Review & Explanations</h3>
          {attempt.gradedAnswers?.map((ga: any, idx: number) => (
            <div
              key={ga.questionId || idx}
              className={`glass-card rounded-2xl p-5 border ${
                ga.isCorrect ? 'border-emerald-500/30' : 'border-rose-500/30'
              }`}
            >
              <div className="flex items-center justify-between text-[11px] mb-2">
                <span className="font-bold text-slate-400">Question {idx + 1}</span>
                <span className={`font-extrabold flex items-center gap-1 ${
                  ga.isCorrect ? 'text-emerald-400' : 'text-rose-400'
                }`}>
                  {ga.isCorrect ? <CheckCircle2 className="w-3.5 h-3.5" /> : <XCircle className="w-3.5 h-3.5" />}
                  {ga.isCorrect ? `+${ga.pointsEarned} Pts` : '0 Pts'}
                </span>
              </div>
              <h4 className="text-xs sm:text-sm font-semibold text-white leading-relaxed">
                {ga.questionText}
              </h4>
              {ga.explanation && (
                <div className="mt-3 p-3 rounded-xl bg-slate-900/80 border border-slate-800 text-[11px] text-slate-300 leading-relaxed">
                  <strong className="text-imd-400">Scientific Explanation:</strong> {ga.explanation}
                </div>
              )}
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Active Adaptive MCQ Mode
  if (isAdaptive) {
    if (!adaptiveQuestion) {
      return (
        <div className="py-20 flex justify-center text-xs text-slate-400">
          Calibrating next adaptive question based on your accuracy...
        </div>
      );
    }

    return (
      <div className="max-w-3xl mx-auto space-y-6">
        {/* Adaptive Status Strip */}
        <div className="glass-panel p-4 rounded-2xl border border-purple-500/30 flex items-center justify-between flex-wrap gap-3">
          <div className="flex items-center gap-2">
            <span className="p-1.5 rounded-lg bg-purple-500/20 text-purple-400">
              <Zap className="w-4 h-4" />
            </span>
            <div>
              <div className="text-[10px] font-extrabold text-purple-300 uppercase tracking-wider">
                Adaptive IRT Engine Active
              </div>
              <div className="text-xs font-semibold text-white">
                Difficulty: <strong className="uppercase text-amber-400">{adaptiveQuestion.difficulty}</strong> • Ability: {estimatedAbility}
              </div>
            </div>
          </div>

          <div className="flex items-center gap-4 text-xs font-semibold text-slate-300">
            <div>
              Question <strong className="text-white">{questionCount}</strong> / {totalPlanned}
            </div>
            <div className="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800 text-purple-300 font-mono">
              <Clock className="w-3.5 h-3.5" />
              {formatTime(secondsRemaining)}
            </div>
          </div>
        </div>

        {/* Adaptive Question Card */}
        <div className="glass-card rounded-3xl p-6 sm:p-8 border border-slate-800">
          <div className="flex items-center justify-between text-xs text-slate-400 mb-3">
            <span>Points: {adaptiveQuestion.points}</span>
            <span className="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 text-[10px] font-bold">
              Calibrated for Your Ability
            </span>
          </div>

          <h3 className="text-base sm:text-lg font-bold text-white leading-relaxed">
            {adaptiveQuestion.text}
          </h3>

          {/* Options */}
          <div className="mt-6 space-y-3">
            {adaptiveQuestion.options?.map((opt: any, optIdx: number) => (
              <button
                key={opt.id}
                type="button"
                onClick={() => setSelectedOptionId(opt.id)}
                className={`w-full p-4 rounded-2xl text-left border text-xs sm:text-sm font-medium transition-all flex items-center gap-3 ${
                  selectedOptionId === opt.id
                    ? 'bg-purple-600/20 border-purple-500 text-white shadow-md shadow-purple-500/20'
                    : 'bg-slate-900/60 border-slate-800 text-slate-300 hover:border-slate-700 hover:bg-slate-850'
                }`}
              >
                <span className={`w-6 h-6 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 ${
                  selectedOptionId === opt.id ? 'bg-purple-500 text-white' : 'bg-slate-800 text-slate-400'
                }`}>
                  {String.fromCharCode(65 + optIdx)}
                </span>
                <span>{opt.text}</span>
              </button>
            ))}
          </div>

          <div className="mt-8 flex justify-end">
            <button
              onClick={handleNextAdaptive}
              disabled={!selectedOptionId || isSubmitting}
              className="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-lg shadow-purple-600/30 flex items-center gap-2 transition-all disabled:opacity-50"
            >
              Submit & Calibrate Next <ChevronRight className="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    );
  }

  // Standard MCQ Mode
  const currentQ = quiz.questions[currentQuestionIndex];
  const isLastQuestion = currentQuestionIndex === quiz.questions.length - 1;

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      {/* Header Info */}
      <div className="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
        <div>
          <span className="text-[10px] font-bold text-imd-400 uppercase tracking-wider">
            {quiz.course?.code || 'IMD Assessment'}
          </span>
          <h2 className="text-sm font-bold text-white truncate max-w-md">{quiz.title}</h2>
        </div>
        <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-xs font-mono text-imd-300 font-bold">
          <Clock className="w-3.5 h-3.5" />
          {formatTime(secondsRemaining)}
        </div>
      </div>

      {/* Question Card */}
      <div className="glass-card rounded-3xl p-6 sm:p-8 border border-slate-800">
        <div className="flex items-center justify-between text-xs text-slate-400 mb-3">
          <span>
            Question <strong>{currentQuestionIndex + 1}</strong> of {quiz.questions.length}
          </span>
          <span className="px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 text-[10px] font-bold">
            Difficulty: {currentQ.difficulty}
          </span>
        </div>

        <h3 className="text-base sm:text-lg font-bold text-white leading-relaxed">
          {currentQ.text}
        </h3>

        {/* Options */}
        <div className="mt-6 space-y-3">
          {currentQ.options.map((opt: any, optIdx: number) => {
            const isSelected = selectedOptionId === opt.id || answers.some((a) => a.questionId === currentQ.id && a.selectedOptionId === opt.id);

            return (
              <button
                key={opt.id}
                type="button"
                onClick={() => handleSelectOptionStandard(opt.id)}
                className={`w-full p-4 rounded-2xl text-left border text-xs sm:text-sm font-medium transition-all flex items-center gap-3 ${
                  isSelected
                    ? 'bg-imd-600/20 border-imd-500 text-white shadow-md shadow-imd-500/20'
                    : 'bg-slate-900/60 border-slate-800 text-slate-300 hover:border-slate-700 hover:bg-slate-850'
                }`}
              >
                <span className={`w-6 h-6 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 ${
                  isSelected ? 'bg-imd-500 text-white' : 'bg-slate-800 text-slate-400'
                }`}>
                  {String.fromCharCode(65 + optIdx)}
                </span>
                <span>{opt.text}</span>
              </button>
            );
          })}
        </div>

        <div className="mt-8 flex items-center justify-between pt-5 border-t border-slate-800">
          <button
            onClick={() => setCurrentQuestionIndex((prev) => Math.max(0, prev - 1))}
            disabled={currentQuestionIndex === 0}
            className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold disabled:opacity-40"
          >
            Previous
          </button>

          {isLastQuestion ? (
            <button
              onClick={handleSubmitTest}
              disabled={isSubmitting || answers.length === 0}
              className="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-moes-600 hover:from-emerald-500 hover:to-moes-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/30 flex items-center gap-2 disabled:opacity-50"
            >
              {isSubmitting ? 'Grading...' : 'Final Submit Assessment'}
            </button>
          ) : (
            <button
              onClick={() => setCurrentQuestionIndex((prev) => Math.min(quiz.questions.length - 1, prev + 1))}
              className="px-5 py-2.5 rounded-xl bg-imd-600 hover:bg-imd-500 text-white text-xs font-bold shadow-md shadow-imd-600/30 flex items-center gap-1.5"
            >
              Next <ChevronRight className="w-4 h-4" />
            </button>
          )}
        </div>
      </div>
    </div>
  );
};
