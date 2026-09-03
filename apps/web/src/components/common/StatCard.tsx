import React from 'react';
import { LucideIcon } from 'lucide-react';

interface StatCardProps {
  title: string;
  value: string | number;
  change?: string;
  changeType?: 'positive' | 'negative' | 'neutral';
  icon: LucideIcon;
  iconColor?: string;
}

export const StatCard: React.FC<StatCardProps> = ({
  title,
  value,
  change,
  changeType = 'neutral',
  icon: Icon,
  iconColor = 'text-imd-400'
}) => {
  return (
    <div className="glass-card rounded-2xl p-5 relative overflow-hidden group">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-xs font-medium text-slate-400 uppercase tracking-wider">{title}</p>
          <p className="text-2xl sm:text-3xl font-extrabold text-white mt-1.5 tracking-tight">{value}</p>
          {change && (
            <p
              className={`text-xs mt-2 flex items-center gap-1 font-medium ${
                changeType === 'positive'
                  ? 'text-emerald-400'
                  : changeType === 'negative'
                  ? 'text-rose-400'
                  : 'text-slate-400'
              }`}
            >
              {change}
            </p>
          )}
        </div>
        <div className="p-3.5 rounded-xl bg-slate-800/80 border border-slate-700/60 shadow-inner group-hover:scale-110 transition-transform">
          <Icon className={`w-6 h-6 ${iconColor}`} />
        </div>
      </div>
      <div className="absolute -right-6 -bottom-6 w-24 h-24 bg-imd-500/5 rounded-full blur-2xl group-hover:bg-imd-500/10 transition-all pointer-events-none" />
    </div>
  );
};
