import { Router } from 'express';
import { AuthController } from '../controllers/authController';
import { validateBody } from '../middleware/validate';
import { SignupSchema, LoginSchema } from '@capacity-connect/shared-types';
import { requireAuth } from '../middleware/auth';

const router = Router();

router.post('/signup', validateBody(SignupSchema), AuthController.signup);
router.post('/login', validateBody(LoginSchema), AuthController.login);
router.post('/refresh', AuthController.refresh);
router.post('/logout', AuthController.logout);
router.get('/me', requireAuth, AuthController.getMe);

export default router;
