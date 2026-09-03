import React from 'react';
import { useNotificationStore } from '../../store/notificationStore';
import { api } from '../../services/api';
import { Bell, Check, ExternalLink, X, Info, AlertTriangle, Award, Megaphone } from 'lucide-react';
import { Link } from 'react-router-dom';

export const NotificationFeed: React.FC<{ onClose: () => void }> = ({ onClose }) => {
  const { notifications, unreadCount, markAsRead, markAllAsRead } = useNotificationStore();

  const handleMarkAsRead = async (id: string, e: React.MouseEvent) => {
    e.stopPropagation();
    try {
      await api.patch(`/notifications/${id}/read`);
      markAsRead(id);
    } catch {}
  };

  const handleMarkAll = async () => {
    try {
      await api.patch('/notifications/read-all');
      markAllAsRead();
    } catch {}
  };

  const getTypeIcon = (type: string) => {
    switch (type) {
      case 'CERTIFICATE':
        return <Award className="w-4 h-4 text-emerald-400" />;
      case 'RISK_ALERT':
        return <AlertTriangle className="w-4 h-4 text-rose-400" />;
      case 'ANNOUNCEMENT':
        return <Megaphone className="w-4 h-4 text-imd-400" />;
      default:
        return <Info className="w-4 h-4 text-blue-400" />;
    }
  };

  return (
    <div className="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-slate-900 border border-slate-700 shadow-2xl z-50 overflow-hidden animate-in fade-in slide-in-from-top-2">
      {/* Header */}
      <div className="flex items-center justify-between px-4 py-3 border-b border-slate-800 bg-slate-950/60">
        <div className="flex items-center gap-2">
          <Bell className="w-4 h-4 text-imd-400" />
          <span className="text-xs font-bold uppercase tracking-wider text-slate-200">
            Realtime Feed ({unreadCount})
          </span>
        </div>
        <div className="flex items-center gap-2">
          {unreadCount > 0 && (
            <button
              onClick={handleMarkAll}
              className="text-[11px] text-imd-400 hover:text-imd-300 font-medium flex items-center gap-1"
            >
              <Check className="w-3 h-3" /> Mark all read
            </button>
          )}
          <button onClick={onClose} className="text-slate-400 hover:text-white p-1 rounded-md">
            <X className="w-4 h-4" />
          </button>
        </div>
      </div>

      {/* List */}
      <div className="max-h-[380px] overflow-y-auto divide-y divide-slate-800/60">
        {notifications.length === 0 ? (
          <div className="py-8 text-center text-xs text-slate-500">
            No notifications in feed
          </div>
        ) : (
          notifications.map((notif) => (
            <div
              key={notif.id}
              className={`p-3.5 transition-colors flex items-start gap-3 hover:bg-slate-800/40 ${
                !notif.isRead ? 'bg-imd-950/20 border-l-2 border-imd-500' : ''
              }`}
            >
              <div className="mt-0.5 p-2 rounded-lg bg-slate-800 border border-slate-700/60 shrink-0">
                {getTypeIcon(notif.type)}
              </div>
              <div className="flex-1 min-w-0">
                <div className="flex items-center justify-between gap-1">
                  <h4 className="text-xs font-semibold text-slate-200 truncate">{notif.title}</h4>
                  <span className="text-[10px] text-slate-500 whitespace-nowrap">
                    {new Date(notif.createdAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                  </span>
                </div>
                <p className="text-[11px] text-slate-400 mt-1 leading-relaxed line-clamp-3">
                  {notif.message}
                </p>
                <div className="flex items-center justify-between mt-2 pt-1 border-t border-slate-800/40">
                  {notif.link ? (
                    <Link
                      to={notif.link}
                      onClick={onClose}
                      className="text-[11px] text-imd-400 hover:underline flex items-center gap-1 font-medium"
                    >
                      View Details <ExternalLink className="w-3 h-3" />
                    </Link>
                  ) : (
                    <span />
                  )}
                  {!notif.isRead && (
                    <button
                      onClick={(e) => handleMarkAsRead(notif.id, e)}
                      className="text-[10px] text-slate-400 hover:text-slate-200"
                    >
                      Mark read
                    </button>
                  )}
                </div>
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  );
};
