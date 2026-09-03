import { Router } from 'express';
import { TraineeController } from '../controllers/traineeController';
import { requireAuth, requireRole } from '../middleware/auth';
import { validateBody } from '../middleware/validate';
import { Role, ProfileUpdateSchema, CreateFeedbackSchema } from '@capacity-connect/shared-types';

const router = Router();

router.use(requireAuth);

router.get('/profile', TraineeController.getProfile);
router.put('/profile', validateBody(ProfileUpdateSchema), TraineeController.updateProfile);
router.get('/dashboard', TraineeController.getDashboardStats);
router.get('/recommendations', TraineeController.getRecommendations);
router.post('/feedback', validateBody(CreateFeedbackSchema), TraineeController.submitFeedback);

export default router;
