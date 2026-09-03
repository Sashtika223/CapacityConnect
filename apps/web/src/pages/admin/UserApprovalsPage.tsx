import React, { useState, useEffect } from 'react';
import { api } from '../../services/api';
import { Users, CheckCircle2, XCircle, AlertCircle, Shield, Clock } from 'lucide-react';
import { ApprovalStatus } from '@capacity-connect/shared-types';

export const UserApprovalsPage: React.FC = () => {
  const [pendingUsers, setPendingUsers] = useState<any[]>([]);
  const [allUsers, setAllUsers] = useState<any[]>([]);
  const [activeTab, setActiveTab] = useState<'PENDING' | 'ALL'>('PENDING');
  const [loading, setLoading] = useState(true);
  const [processingId, setProcessingId] = useState<string | null>(null);

  useEffect(() => {
    fetchUsers();
  }, []);

  const fetchUsers = async () => {
    setLoading(true);
    try {
      const [pendingRes, allRes] = await Promise.all([
        api.get('/admin/users/pending'),
        api.get('/admin/users')
      ]);
      if (pendingRes.data.success) setPendingUsers(pendingRes.data.users);
      if (allRes.data.success) setAllUsers(allRes.data.users);
    } finally {
      setLoading(false);
    }
  };

  const handleUpdateStatus = async (userId: string, approvalStatus: ApprovalStatus) => {
    setProcessingId(userId);
    try {
      const res = await api.patch(`/admin/users/${userId}/status`, { approvalStatus });
      if (res.data.success) {
        fetchUsers();
      }
    } catch (err: any) {
      alert(err.response?.data?.message || 'Error updating status');
    } finally {
      setProcessingId(null);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
            <Shield className="w-6 h-6 text-purple-400" /> Personnel Approval & Role Governance
          </h1>
          <p className="text-xs text-slate-400 mt-1">
            Review and approve prospective IMD/MoES trainer accreditations and govern user permissions
          </p>
        </div>

        <div className="flex items-center gap-2 bg-slate-900 p-1 rounded-xl border border-slate-800">
          <button
            onClick={() => setActiveTab('PENDING')}
            className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-all ${
              activeTab === 'PENDING'
                ? 'bg-purple-600 text-white shadow-sm'
                : 'text-slate-400 hover:text-white'
            }`}
          >
            Pending Queue ({pendingUsers.length})
          </button>
          <button
            onClick={() => setActiveTab('ALL')}
            className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-all ${
              activeTab === 'ALL'
                ? 'bg-purple-600 text-white shadow-sm'
                : 'text-slate-400 hover:text-white'
            }`}
          >
            All Personnel ({allUsers.length})
          </button>
        </div>
      </div>

      {loading ? (
        <div className="py-20 flex justify-center">
          <div className="w-8 h-8 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin" />
        </div>
      ) : activeTab === 'PENDING' ? (
        pendingUsers.length === 0 ? (
          <div className="glass-card rounded-3xl p-12 text-center border border-slate-800">
            <CheckCircle2 className="w-12 h-12 text-emerald-400 mx-auto mb-3" />
            <h3 className="text-sm font-bold text-white">Approval Queue is Clear</h3>
            <p className="text-xs text-slate-400 mt-1">All registered trainers have been verified and processed.</p>
          </div>
        ) : (
          <div className="space-y-4">
            {pendingUsers.map((u) => (
              <div
                key={u.id}
                className="glass-card rounded-3xl p-6 border border-purple-500/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-5"
              >
                <div>
                  <div className="flex items-center gap-2 mb-1.5">
                    <span className="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30 flex items-center gap-1">
                      <Clock className="w-3 h-3" /> Awaiting Verification
                    </span>
                    <span className="text-xs font-mono text-slate-400">{u.employeeId}</span>
                  </div>

                  <h3 className="text-base font-bold text-white">{u.name}</h3>
                  <p className="text-xs text-slate-400 mt-0.5">
                    {u.email} • {u.designation || 'Staff'} • <strong className="text-slate-300">{u.department}</strong>
                  </p>
                  {u.profile?.bio && (
                    <p className="text-xs text-slate-300 mt-2 bg-slate-900/60 p-2.5 rounded-xl border border-slate-800">
                      "{u.profile.bio}"
                    </p>
                  )}
                  {u.profile?.qualifications && (
                    <p className="text-[11px] text-imd-400 mt-2 font-medium">
                      Credentials: {u.profile.qualifications} ({u.profile.experienceYears} Years Exp)
                    </p>
                  )}
                </div>

                <div className="flex items-center gap-2 shrink-0 w-full md:w-auto justify-end">
                  <button
                    onClick={() => handleUpdateStatus(u.id, ApprovalStatus.REJECTED)}
                    disabled={processingId === u.id}
                    className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-rose-400 border border-slate-700 hover:border-rose-500/40 text-xs font-semibold transition-all disabled:opacity-50"
                  >
                    Reject
                  </button>
                  <button
                    onClick={() => handleUpdateStatus(u.id, ApprovalStatus.APPROVED)}
                    disabled={processingId === u.id}
                    className="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition-all disabled:opacity-50"
                  >
                    <CheckCircle2 className="w-4 h-4" /> Approve Trainer
                  </button>
                </div>
              </div>
            ))}
          </div>
        )
      ) : (
        /* All Users Directory */
        <div className="glass-card rounded-3xl border border-slate-800 overflow-hidden shadow-xl">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs">
              <thead className="bg-slate-900/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                <tr>
                  <th className="py-3 px-4">Name & Email</th>
                  <th className="py-3 px-4">Role</th>
                  <th className="py-3 px-4">Department</th>
                  <th className="py-3 px-4">Status</th>
                  <th className="py-3 px-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800/60">
                {allUsers.map((u) => (
                  <tr key={u.id} className="hover:bg-slate-850 transition-colors">
                    <td className="py-3 px-4">
                      <div className="font-bold text-white">{u.name}</div>
                      <div className="text-[11px] text-slate-400 font-mono">{u.email}</div>
                    </td>
                    <td className="py-3 px-4">
                      <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${
                        u.role === 'ADMIN' ? 'bg-purple-500/20 text-purple-300' :
                        u.role === 'TRAINER' ? 'bg-emerald-500/20 text-emerald-300' :
                        'bg-blue-500/20 text-blue-300'
                      }`}>
                        {u.role}
                      </span>
                    </td>
                    <td className="py-3 px-4 text-slate-300">{u.department || 'General'}</td>
                    <td className="py-3 px-4">
                      <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${
                        u.approvalStatus === 'APPROVED' ? 'bg-emerald-500/20 text-emerald-300' :
                        u.approvalStatus === 'PENDING' ? 'bg-amber-500/20 text-amber-300' :
                        'bg-rose-500/20 text-rose-300'
                      }`}>
                        {u.approvalStatus}
                      </span>
                    </td>
                    <td className="py-3 px-4 text-right">
                      {u.approvalStatus === 'APPROVED' ? (
                        <button
                          onClick={() => handleUpdateStatus(u.id, ApprovalStatus.SUSPENDED)}
                          className="text-[11px] text-slate-400 hover:text-rose-400 font-medium"
                        >
                          Suspend
                        </button>
                      ) : (
                        <button
                          onClick={() => handleUpdateStatus(u.id, ApprovalStatus.APPROVED)}
                          className="text-[11px] text-emerald-400 hover:text-emerald-300 font-medium"
                        >
                          Activate
                        </button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
};
