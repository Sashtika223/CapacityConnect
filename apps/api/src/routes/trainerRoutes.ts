import { Router } from 'express';
import { TrainerController } from '../controllers/trainerController';
import { requireAuth, requireRole } from '../middleware/auth';
import { Role } from '@capacity-connect/shared-types';

const router = Router();

router.use(requireAuth);
router.use(requireRole([Role.TRAINER, Role.ADMIN]));

router.get('/dashboard', TrainerController.getDashboardStats);
router.get('/trainees', TrainerController.getTraineeMonitor);
router.get('/feedbacks', TrainerController.getFeedbackAnalytics);

export default router;
