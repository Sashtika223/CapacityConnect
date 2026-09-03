import { Router } from 'express';
import { VerifyController } from '../controllers/verifyController';

const router = Router();

// Public endpoint for QR code verification
router.get('/:code', VerifyController.verifyCertificate);

export default router;
