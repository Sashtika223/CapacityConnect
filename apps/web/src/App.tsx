import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { useAuthStore } from './store/authStore';
import { DashboardLayout } from './layouts/DashboardLayout';
import { ProtectedRoute } from './components/common/ProtectedRoute';

// Auth Pages
import { LoginPage } from './pages/auth/LoginPage';
import { SignupPage } from './pages/auth/SignupPage';

// Trainee Pages
import { TraineeDashboard } from './pages/trainee/TraineeDashboard';
import { CourseCatalogPage } from './pages/trainee/CourseCatalogPage';
import { CourseDetailPage } from './pages/trainee/CourseDetailPage';
import { MCQPlayerPage } from './pages/trainee/MCQPlayerPage';
import { CertificatesPage } from './pages/trainee/CertificatesPage';
import { TraineeProfilePage } from './pages/trainee/TraineeProfilePage';
import { RecommendationsPage } from './pages/trainee/RecommendationsPage';
import { TrainerLibraryViewPage } from './pages/trainee/TrainerLibraryViewPage';

// Trainer Pages
import { TrainerDashboard } from './pages/trainer/TrainerDashboard';
import { TraineeMonitorPage } from './pages/trainer/TraineeMonitorPage';
import { TrainerLibraryManagePage } from './pages/trainer/TrainerLibraryManagePage';
import { QuestionnaireBuilderPage } from './pages/trainer/QuestionnaireBuilderPage';
import { FeedbackAnalyticsPage } from './pages/trainer/FeedbackAnalyticsPage';

// Admin Pages
import { AdminDashboard } from './pages/admin/AdminDashboard';
import { UserApprovalsPage } from './pages/admin/UserApprovalsPage';
import { CompetencyMatrixPage } from './pages/admin/CompetencyMatrixPage';
import { AnnouncementPublisherPage } from './pages/admin/AnnouncementPublisherPage';
import { AuditLogsPage } from './pages/admin/AuditLogsPage';

// Public Verification
import { VerifyCertificatePage } from './pages/public/VerifyCertificatePage';

import { Role } from '@capacity-connect/shared-types';

export const App: React.FC = () => {
  const { user, isAuthenticated } = useAuthStore();

  const getHomeRoute = () => {
    if (!isAuthenticated || !user) return '/login';
    if (user.role === Role.ADMIN) return '/admin/dashboard';
    if (user.role === Role.TRAINER) return '/trainer/dashboard';
    return '/dashboard';
  };

  return (
    <Routes>
      {/* Public Routes */}
      <Route path="/login" element={<LoginPage />} />
      <Route path="/signup" element={<SignupPage />} />
      <Route path="/verify/:code" element={<VerifyCertificatePage />} />

      {/* Root redirection */}
      <Route path="/" element={<Navigate to={getHomeRoute()} replace />} />

      {/* Trainee & Shared Dashboard Layout */}
      <Route element={<ProtectedRoute />}>
        <Route element={<DashboardLayout />}>
          <Route path="/dashboard" element={<TraineeDashboard />} />
          <Route path="/courses" element={<CourseCatalogPage />} />
          <Route path="/courses/:id" element={<CourseDetailPage />} />
          <Route path="/assessments" element={<CourseCatalogPage />} />
          <Route path="/assessments/:id" element={<MCQPlayerPage />} />
          <Route path="/certificates" element={<CertificatesPage />} />
          <Route path="/profile" element={<TraineeProfilePage />} />
          <Route path="/recommendations" element={<RecommendationsPage />} />
          <Route path="/library" element={<TrainerLibraryViewPage />} />
        </Route>
      </Route>

      {/* Trainer Protected Routes */}
      <Route element={<ProtectedRoute allowedRoles={[Role.TRAINER, Role.ADMIN]} />}>
        <Route element={<DashboardLayout />}>
          <Route path="/trainer/dashboard" element={<TrainerDashboard />} />
          <Route path="/trainer/courses" element={<TrainerDashboard />} />
          <Route path="/trainer/trainees" element={<TraineeMonitorPage />} />
          <Route path="/trainer/library" element={<TrainerLibraryManagePage />} />
          <Route path="/trainer/questionnaires" element={<QuestionnaireBuilderPage />} />
          <Route path="/trainer/feedbacks" element={<FeedbackAnalyticsPage />} />
        </Route>
      </Route>

      {/* Admin Protected Routes */}
      <Route element={<ProtectedRoute allowedRoles={[Role.ADMIN]} />}>
        <Route element={<DashboardLayout />}>
          <Route path="/admin/dashboard" element={<AdminDashboard />} />
          <Route path="/admin/approvals" element={<UserApprovalsPage />} />
          <Route path="/admin/competency-matcher" element={<CompetencyMatrixPage />} />
          <Route path="/admin/announcements" element={<AnnouncementPublisherPage />} />
          <Route path="/admin/users" element={<UserApprovalsPage />} />
          <Route path="/admin/audit-logs" element={<AuditLogsPage />} />
        </Route>
      </Route>

      {/* Fallback */}
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
};

export default App;
