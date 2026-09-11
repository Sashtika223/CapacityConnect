<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\Public\CertificateVerificationController;
use App\Http\Controllers\Public\CourseCatalogController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\Admin\AdminAIContentController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminCapacitySimulatorController;
use App\Http\Controllers\Admin\AdminCertificateController;
use App\Http\Controllers\Admin\AdminCompetencyController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDepartmentController;
use App\Http\Controllers\Admin\AdminKnowledgeGraphController;
use App\Http\Controllers\Admin\AdminRiskRadarController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminTrainerController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Trainer\TrainerAnalyticsController;
use App\Http\Controllers\Trainer\TrainerCourseController;
use App\Http\Controllers\Trainer\TrainerCurriculumController;
use App\Http\Controllers\Trainer\TrainerDashboardController;
use App\Http\Controllers\Trainer\TrainerEnrollmentController;
use App\Http\Controllers\Trainer\TrainerRecertificationController;
use App\Http\Controllers\Trainer\TrainerSubmissionController;
use App\Http\Controllers\Trainee\TraineeAIAssistantController;
use App\Http\Controllers\Trainee\TraineeAssessmentController;
use App\Http\Controllers\Trainee\TraineeCertificateController;
use App\Http\Controllers\Trainee\TraineeDashboardController;
use App\Http\Controllers\Trainee\TraineeDigitalTwinController;
use App\Http\Controllers\Trainee\TraineeLearningController;
use App\Http\Controllers\Trainee\TraineeProfileController;
use App\Http\Controllers\Trainee\TraineeSimulationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseCatalogController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [CourseCatalogController::class, 'show'])->name('courses.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/certificate/verify/{code?}', [CertificateVerificationController::class, 'verify'])->name('certificate.verify');

/*
|--------------------------------------------------------------------------
| Guest (Auth) Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/two-factor', [AuthController::class, 'showTwoFactor'])->name('two-factor');
    Route::post('/two-factor', [AuthController::class, 'verifyTwoFactor'])->name('two-factor.verify');

    // Notification Preferences
    Route::get('/notifications/preferences', [NotificationPreferenceController::class, 'edit'])->name('notifications.preferences');
    Route::put('/notifications/preferences', [NotificationPreferenceController::class, 'update'])->name('notifications.preferences.update');

    /*
    |----------------------------------------------------------------------
    | Admin Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware([\App\Http\Middleware\EnsureRole::class.':admin', \App\Http\Middleware\EnsureActiveAccount::class])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle-status');

        // Trainer Approval Board (§23 — dedicated AdminTrainerController, §22 — protected by admin middleware)
        Route::get('/trainers', [AdminTrainerController::class, 'index'])->name('admin.trainers.index');
        Route::get('/trainers/{user}', [AdminTrainerController::class, 'show'])->name('admin.trainers.show');
        Route::post('/trainers/{user}/approve', [AdminTrainerController::class, 'approve'])->name('admin.trainers.approve');
        Route::post('/trainers/{user}/reject', [AdminTrainerController::class, 'reject'])->name('admin.trainers.reject');

        // Departments
        Route::get('/departments', [AdminDepartmentController::class, 'index'])->name('admin.departments.index');
        Route::post('/departments', [AdminDepartmentController::class, 'store'])->name('admin.departments.store');
        Route::put('/departments/{department}', [AdminDepartmentController::class, 'update'])->name('admin.departments.update');
        Route::delete('/departments/{department}', [AdminDepartmentController::class, 'destroy'])->name('admin.departments.destroy');

        // Courses
        Route::get('/courses', [AdminCourseController::class, 'index'])->name('admin.courses.index');
        Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->name('admin.courses.show');
        Route::post('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('admin.courses.approve');
        Route::post('/courses/{course}/reject', [AdminCourseController::class, 'reject'])->name('admin.courses.reject');

        // Certificates
        Route::get('/certificates', [AdminCertificateController::class, 'index'])->name('admin.certificates.index');
        Route::post('/certificates/{certificate}/revoke', [AdminCertificateController::class, 'revoke'])->name('admin.certificates.revoke');

        // Announcements
        Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->name('admin.announcements.index');
        Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->name('admin.announcements.store');
        Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');

        // Competencies
        Route::get('/competencies', [AdminCompetencyController::class, 'index'])->name('admin.competencies.index');
        Route::post('/competencies', [AdminCompetencyController::class, 'store'])->name('admin.competencies.store');
        Route::post('/competencies/skills', [AdminCompetencyController::class, 'storeSkill'])->name('admin.competencies.skills.store');

        // Intelligence & Analytics
        Route::get('/risk-radar', [AdminRiskRadarController::class, 'index'])->name('admin.risk-radar');
        Route::get('/knowledge-graph', [AdminKnowledgeGraphController::class, 'index'])->name('admin.knowledge-graph');
        Route::get('/expert-discovery', [AdminKnowledgeGraphController::class, 'expertDiscovery'])->name('admin.expert-discovery');
        Route::get('/capacity-simulator', [AdminCapacitySimulatorController::class, 'index'])->name('admin.capacity-simulator');

        // AI Content
        Route::get('/ai-content', [AdminAIContentController::class, 'index'])->name('admin.ai-content.index');
        Route::post('/ai-content/generate', [AdminAIContentController::class, 'generate'])->name('admin.ai-content.generate');
        Route::post('/ai-content/{generation}/review', [AdminAIContentController::class, 'review'])->name('admin.ai-content.review');

        // Settings & Audit
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
        Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('admin.audit-logs');
    });

    /*
    |----------------------------------------------------------------------
    | Trainer Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('trainer')->middleware([\App\Http\Middleware\EnsureRole::class.':trainer', \App\Http\Middleware\EnsureActiveAccount::class])->group(function () {
        Route::get('/dashboard', [TrainerDashboardController::class, 'index'])->name('trainer.dashboard');

        // Courses
        Route::get('/courses', [TrainerCourseController::class, 'index'])->name('trainer.courses.index');
        Route::get('/courses/create', [TrainerCourseController::class, 'create'])->name('trainer.courses.create');
        Route::post('/courses', [TrainerCourseController::class, 'store'])->name('trainer.courses.store');
        Route::get('/courses/{course}/edit', [TrainerCourseController::class, 'edit'])->name('trainer.courses.edit');
        Route::put('/courses/{course}', [TrainerCourseController::class, 'update'])->name('trainer.courses.update');
        Route::post('/courses/{course}/submit', [TrainerCourseController::class, 'submitForApproval'])->name('trainer.courses.submit');

        // Curriculum Builder
        Route::get('/courses/{course}/curriculum', [TrainerCurriculumController::class, 'builder'])->name('trainer.curriculum.builder');
        Route::post('/courses/{course}/modules', [TrainerCurriculumController::class, 'storeModule'])->name('trainer.curriculum.modules.store');
        Route::post('/courses/{course}/modules/{module}/lessons', [TrainerCurriculumController::class, 'storeLesson'])->name('trainer.curriculum.lessons.store');
        Route::post('/courses/{course}/resources', [TrainerCurriculumController::class, 'storeResource'])->name('trainer.curriculum.resources.store');
        Route::post('/courses/{course}/assessments', [TrainerCurriculumController::class, 'storeAssessment'])->name('trainer.curriculum.assessments.store');
        Route::post('/courses/{course}/assessments/{assessment}/questions', [TrainerCurriculumController::class, 'storeQuestion'])->name('trainer.curriculum.questions.store');
        Route::post('/courses/{course}/practicals', [TrainerCurriculumController::class, 'storePractical'])->name('trainer.curriculum.practicals.store');

        // Enrollments
        Route::get('/courses/{course}/enrollments', [TrainerEnrollmentController::class, 'index'])->name('trainer.enrollments.index');
        Route::post('/courses/{course}/enrollments/{user}/message', [TrainerEnrollmentController::class, 'messageLearner'])->name('trainer.enrollments.message');

        // Submissions
        Route::get('/submissions', [TrainerSubmissionController::class, 'index'])->name('trainer.submissions.index');
        Route::get('/submissions/{submission}', [TrainerSubmissionController::class, 'show'])->name('trainer.submissions.show');
        Route::post('/submissions/{submission}/grade', [TrainerSubmissionController::class, 'grade'])->name('trainer.submissions.grade');

        // Recertification
        Route::get('/recertification/{certificateNumber}', [TrainerRecertificationController::class, 'show'])->name('trainer.recertification.show');
        Route::post('/recertification/{certificateNumber}', [TrainerRecertificationController::class, 'submit'])->name('trainer.recertification.submit');

        // Analytics
        Route::get('/analytics', [TrainerAnalyticsController::class, 'index'])->name('trainer.analytics');
    });

    /*
    |----------------------------------------------------------------------
    | Trainee Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('trainee')->middleware([\App\Http\Middleware\EnsureRole::class.':trainee', \App\Http\Middleware\EnsureActiveAccount::class])->group(function () {
        Route::get('/dashboard', [TraineeDashboardController::class, 'index'])->name('trainee.dashboard');

        // Learning
        Route::get('/my-courses', [TraineeLearningController::class, 'myCourses'])->name('trainee.learning.index');
        Route::post('/courses/{course}/enroll', [TraineeLearningController::class, 'enroll'])->name('trainee.learning.enroll');
        Route::get('/courses/{course}/learn/{lesson?}', [TraineeLearningController::class, 'player'])->name('trainee.learning.player');
        Route::post('/courses/{course}/lessons/{lesson}/complete', [TraineeLearningController::class, 'completeLesson'])->name('trainee.learning.complete');
        Route::get('/resources/{resource}/download', [TraineeLearningController::class, 'downloadResource'])->name('trainee.learning.resource.download');
        Route::post('/courses/{course}/feedback', [TraineeLearningController::class, 'submitFeedback'])->name('trainee.learning.feedback');

        // Assessments
        Route::get('/assessments', [TraineeAssessmentController::class, 'index'])->name('trainee.assessments.index');
        Route::get('/assessments/{assessment}', [TraineeAssessmentController::class, 'show'])->name('trainee.assessments.show');
        Route::post('/assessments/{assessment}/submit', [TraineeAssessmentController::class, 'submit'])->name('trainee.assessments.submit');
        Route::get('/assessments/attempts/{attempt}/result', [TraineeAssessmentController::class, 'result'])->name('trainee.assessments.result');
        Route::post('/practicals/{practical}/submit', [TraineeAssessmentController::class, 'submitPractical'])->name('trainee.practicals.submit');

        // Certificates
        Route::get('/certificates', [TraineeCertificateController::class, 'index'])->name('trainee.certificates.index');
        Route::get('/certificates/{certificate}', [TraineeCertificateController::class, 'show'])->name('trainee.certificates.show');
        Route::get('/certificates/{certificate}/download', [TraineeCertificateController::class, 'download'])->name('trainee.certificates.download');

        // Digital Twin
        Route::get('/digital-twin', [TraineeDigitalTwinController::class, 'index'])->name('trainee.digital-twin');

        // Simulations
        Route::get('/simulations', [TraineeSimulationController::class, 'index'])->name('trainee.simulations.index');
        Route::get('/simulations/{scenario}', [TraineeSimulationController::class, 'show'])->name('trainee.simulations.show');
        Route::post('/simulations/{scenario}/attempts/{attempt}/decide', [TraineeSimulationController::class, 'decide'])->name('trainee.simulations.decide');

        // AI Assistant
        Route::get('/ai-assistant', [TraineeAIAssistantController::class, 'index'])->name('trainee.ai.assistant');
        Route::post('/ai-assistant/chat', [TraineeAIAssistantController::class, 'chat'])->name('trainee.ai.chat');

        // Profile
        Route::get('/profile', [TraineeProfileController::class, 'show'])->name('trainee.profile');
        Route::put('/profile', [TraineeProfileController::class, 'update'])->name('trainee.profile.update');
        Route::post('/profile/avatar', [TraineeProfileController::class, 'uploadAvatar'])->name('trainee.profile.avatar');
        Route::post('/profile/qualifications', [TraineeProfileController::class, 'addQualification'])->name('trainee.profile.qualifications.store');
        Route::post('/profile/experience', [TraineeProfileController::class, 'addExperience'])->name('trainee.profile.experience.store');
        Route::post('/profile/skills', [TraineeProfileController::class, 'addSkill'])->name('trainee.profile.skills.store');
        Route::post('/profile/interests', [TraineeProfileController::class, 'addInterest'])->name('trainee.profile.interests.store');
        Route::delete('/profile/interests', [TraineeProfileController::class, 'removeInterest'])->name('trainee.profile.interests.destroy');
        Route::post('/profile/two-factor', [TraineeProfileController::class, 'toggleTwoFactor'])->name('trainee.profile.two-factor');
        Route::delete('/profile/courses/{courseId}/unenroll', [TraineeProfileController::class, 'unenroll'])->name('trainee.profile.unenroll');
    });
});
