import { Router } from 'express';
import { AdminController } from '../controllers/adminController';
import { requireAuth, requireRole } from '../middleware/auth';
import { validateBody } from '../middleware/validate';
import { Role, CreateAnnouncementSchema } from '@capacity-connect/shared-types';

const router = Router();

router.use(requireAuth);
router.use(requireRole([Role.ADMIN]));

router.get('/users/pending', AdminController.getPendingUsers);
router.patch('/users/:userId/status', AdminController.updateUserStatus);
router.get('/users', AdminController.getAllUsers);
router.get('/analytics', AdminController.getSystemAnalytics);
router.get('/competency-map/suggest', AdminController.suggestTrainerForSubject);
router.post('/announcements', validateBody(CreateAnnouncementSchema), AdminController.publishAnnouncement);
router.post('/risk-evaluation/trigger', AdminController.triggerRiskEvaluation);
router.get('/audit-logs', AdminController.getAuditLogs);

export default router;
