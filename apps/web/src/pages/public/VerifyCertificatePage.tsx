import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';
import { ShieldCheck, Award, Calendar, User, BookOpen, AlertTriangle, Download, ArrowLeft } from 'lucide-react';

export const VerifyCertificatePage: React.FC = () => {
  const { code } = useParams<{ code: string }>();
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!code) return;
    axios.get(`/api/verify/${code}`)
      .then((res) => {
        if (res.data.success) {
          setData(res.data);
        } else {
          setError(res.data.message || 'Verification record not found');
        }
      })
      .catch((err) => {
        setError(err.response?.data?.message || 'Invalid or revoked certificate code');
      })
      .finally(() => setLoading(false));
  }, [code]);

  if (loading) {
    return (
      <div className="min-h-screen bg-[#070e20] flex items-center justify-center p-4">
        <div className="flex flex-col items-center gap-3">
          <div className="w-12 h-12 border-4 border-imd-500/30 border-t-imd-500 rounded-full animate-spin" />
          <p className="text-xs font-semibold text-slate-300">Validating Cryptographic Record with IMD Registry...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#070e20] text-slate-100 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
      {/* Background Glow */}
      <div className="absolute top-1/4 left-1/2 -translate-x-1/2 w-[600px] h-[350px] bg-emerald-500/10 blur-[120px] pointer-events-none rounded-full" />

      <div className="max-w-xl mx-auto w-full z-10 space-y-6">
        <div className="text-center">
          <Link to="/" className="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white mb-4">
            <ArrowLeft className="w-3.5 h-3.5" /> Back to Capacity Connect
          </Link>
          <div className="text-[10px] font-extrabold uppercase tracking-widest text-imd-400 mb-1">
            Government of India • Ministry of Earth Sciences (MoES)
          </div>
          <h1 className="text-2xl font-extrabold text-white">Public Certificate Verification</h1>
        </div>

        {error ? (
          <div className="glass-card rounded-3xl p-8 border border-rose-500/30 text-center space-y-4 shadow-2xl">
            <div className="w-14 h-14 rounded-full bg-rose-500/15 text-rose-400 flex items-center justify-center mx-auto">
              <AlertTriangle className="w-8 h-8" />
            </div>
            <h2 className="text-lg font-bold text-white">Certificate Verification Failed</h2>
            <p className="text-xs text-rose-300 leading-relaxed max-w-sm mx-auto">{error}</p>
            <div className="pt-2 text-[11px] text-slate-500 font-mono">Code queried: {code}</div>
          </div>
        ) : (
          <div className="glass-panel rounded-3xl p-6 sm:p-8 border border-emerald-500/30 shadow-2xl space-y-6 relative overflow-hidden">
            {/* Authenticity Stamp */}
            <div className="flex items-center justify-between pb-4 border-b border-slate-800">
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shadow-lg shadow-emerald-500/10 shrink-0">
                  <ShieldCheck className="w-7 h-7" />
                </div>
                <div>
                  <div className="text-[10px] uppercase tracking-wider font-extrabold text-emerald-400">
                    Officially Verified Record
                  </div>
                  <h3 className="text-sm font-bold text-white">Authentic IMD/MoES Credential</h3>
                </div>
              </div>

              {data.certificate.qrCodeDataUrl && (
                <div className="p-1 rounded-lg bg-white shrink-0 hidden sm:block">
                  <img src={data.certificate.qrCodeDataUrl} alt="QR" className="w-12 h-12" />
                </div>
              )}
            </div>

            {/* Recipient and Course Details */}
            <div className="space-y-3.5 text-xs">
              <div className="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-1">
                <span className="text-[10px] uppercase font-bold text-slate-500">Certified Candidate</span>
                <p className="text-base font-bold text-white">{data.certificate.recipientName}</p>
                <p className="text-[11px] text-slate-300">
                  {data.certificate.recipientDesignation} • {data.certificate.recipientDepartment}
                </p>
                <p className="text-[10px] text-imd-400 font-medium">{data.certificate.recipientOrganization}</p>
              </div>

              <div className="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-1">
                <span className="text-[10px] uppercase font-bold text-slate-500">Course Programme</span>
                <p className="text-sm font-bold text-white">{data.certificate.courseTitle}</p>
                <p className="text-[11px] text-slate-400">
                  Code: <strong className="text-slate-200">{data.certificate.courseCode}</strong> • Instructor: {data.certificate.instructorName}
                </p>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div className="p-3 rounded-2xl bg-slate-900/80 border border-slate-800">
                  <span className="text-[10px] uppercase font-bold text-slate-500">Grade Conferred</span>
                  <p className="text-sm font-bold text-amber-400 mt-0.5">{data.certificate.grade}</p>
                </div>
                <div className="p-3 rounded-2xl bg-slate-900/80 border border-slate-800">
                  <span className="text-[10px] uppercase font-bold text-slate-500">Issue Date</span>
                  <p className="text-xs font-bold text-white mt-0.5">
                    {new Date(data.certificate.issueDate).toLocaleDateString('en-IN', {
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric'
                    })}
                  </p>
                </div>
              </div>
            </div>

            <div className="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
              <span className="text-[10px] text-slate-500 font-mono">
                Cert No: {data.certificate.certificateNumber}
              </span>

              {data.certificate.pdfUrl && (
                <a
                  href={data.certificate.pdfUrl}
                  target="_blank"
                  rel="noreferrer"
                  download
                  className="px-4 py-2 rounded-xl bg-imd-600 hover:bg-imd-500 text-white font-semibold flex items-center gap-1.5 shadow-md shadow-imd-600/20"
                >
                  <Download className="w-3.5 h-3.5" /> Download Official PDF
                </a>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
