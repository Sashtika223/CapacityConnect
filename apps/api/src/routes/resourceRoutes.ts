import { Router } from 'express';
import { ResourceController } from '../controllers/resourceController';
import { requireAuth, requireRole } from '../middleware/auth';
import { validateBody } from '../middleware/validate';
import { CreateResourceSchema, Role } from '@capacity-connect/shared-types';

const router = Router();

router.use(requireAuth);

router.get('/', ResourceController.getResources);
router.post('/', requireRole([Role.TRAINER, Role.ADMIN]), validateBody(CreateResourceSchema), ResourceController.createResource);
router.post('/:id/access', ResourceController.accessResource);

export default router;
