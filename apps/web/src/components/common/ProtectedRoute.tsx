import React from 'react';
import { Navigate, Outlet } from 'react-router-dom';
import { useAuthStore } from '../../store/authStore';
import { Role } from '@capacity-connect/shared-types';

interface ProtectedRouteProps {
  allowedRoles?: Role[];
}

export const ProtectedRoute: React.FC<ProtectedRouteProps> = ({ allowedRoles }) => {
  const { user, isAuthenticated, isLoading } = useAuthStore();

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-[#070e20]">
        <div className="flex flex-col items-center gap-3">
          <div className="w-10 h-10 border-4 border-imd-500/30 border-t-imd-500 rounded-full animate-spin" />
          <p className="text-xs font-medium text-slate-400">Verifying IMD session...</p>
        </div>
      </div>
    );
  }

  if (!isAuthenticated || !user) {
    return <Navigate to="/login" replace />;
  }

  if (allowedRoles && !allowedRoles.includes(user.role)) {
    // If user role is not permitted, route them to their appropriate role home
    if (user.role === Role.ADMIN) return <Navigate to="/admin/dashboard" replace />;
    if (user.role === Role.TRAINER) return <Navigate to="/trainer/dashboard" replace />;
    return <Navigate to="/dashboard" replace />;
  }

  return <Outlet />;
};
