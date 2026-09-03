import React, { useState, useEffect } from 'react';
import { api } from '../../services/api';
import { Award, Download, ExternalLink, QrCode, ShieldCheck, Calendar, User } from 'lucide-react';
import { Link } from 'react-router-dom';

export const CertificatesPage: React.FC = () => {
  const [certificates, setCertificates] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/trainee/dashboard')
      .then((res) => {
        if (res.data.success) {
          setCertificates(res.data.certificates || []);
        }
      })
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="py-20 flex justify-center">
        <div className="w-10 h-10 border-4 border-imd-500/30 border-t-imd-500 rounded-full animate-spin" />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-extrabold text-white tracking-tight">Verified Certifications</h1>
        <p className="text-xs text-slate-400 mt-1">
          Government of India (IMD/MoES) capacity building credentials secured with cryptographic QR verification
        </p>
      </div>

      {certificates.length === 0 ? (
        <div className="glass-card rounded-3xl p-12 text-center border border-slate-800">
          <Award className="w-12 h-12 text-slate-600 mx-auto mb-3" />
          <h3 className="text-sm font-bold text-slate-300">No Certificates Earned Yet</h3>
          <p className="text-xs text-slate-500 mt-1 mb-4">
            Enroll in an IMD module and score at least 70% in the assessment to receive your official certificate.
          </p>
          <Link
            to="/courses"
            className="px-4 py-2 rounded-xl bg-imd-600 text-white text-xs font-semibold"
          >
            Explore Courses
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {certificates.map((cert) => (
            <div
              key={cert.id}
              className="glass-card rounded-3xl p-6 border border-amber-500/20 shadow-xl relative overflow-hidden flex flex-col justify-between"
            >
              <div>
                <div className="flex items-start justify-between gap-4 mb-4">
                  <div>
                    <span className="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-400 border border-amber-500/30 uppercase tracking-wider flex items-center gap-1">
                      <ShieldCheck className="w-3 h-3" /> Accredited IMD Credential
                    </span>
                    <h3 className="text-base font-bold text-white mt-2 leading-snug">
                      {cert.course.title}
                    </h3>
                    <p className="text-[11px] text-slate-400 mt-1 font-mono">
                      Cert No: <strong className="text-slate-200">{cert.certificateNumber}</strong>
                    </p>
                  </div>

                  {/* QR Code preview */}
                  <div className="p-1.5 rounded-xl bg-white shrink-0 shadow-md">
                    <img
                      src={cert.qrCodeDataUrl}
                      alt="Verification QR"
                      className="w-16 h-16 object-contain"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-xs">
                  <div>
                    <span className="text-[10px] text-slate-500 uppercase font-semibold">Grade</span>
                    <p className="text-white font-bold mt-0.5">{cert.grade}</p>
                  </div>
                  <div>
                    <span className="text-[10px] text-slate-500 uppercase font-semibold">Issued Date</span>
                    <p className="text-white font-medium mt-0.5">
                      {new Date(cert.issueDate).toLocaleDateString('en-IN', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                      })}
                    </p>
                  </div>
                </div>
              </div>

              <div className="mt-5 pt-4 border-t border-slate-800 flex items-center justify-between gap-3">
                <Link
                  to={`/verify/${cert.verificationCode}`}
                  target="_blank"
                  className="text-xs font-semibold text-imd-400 hover:text-imd-300 flex items-center gap-1"
                >
                  <ExternalLink className="w-3.5 h-3.5" /> Public QR Link
                </Link>

                {cert.pdfUrl && (
                  <a
                    href={cert.pdfUrl}
                    download={`${cert.certificateNumber}.pdf`}
                    target="_blank"
                    rel="noreferrer"
                    className="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-750 text-white text-xs font-semibold flex items-center gap-1.5 transition-colors border border-slate-700"
                  >
                    <Download className="w-3.5 h-3.5 text-moes-500" /> Download PDF
                  </a>
                )}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};
