import { Router } from 'express';
import { AssessmentController } from '../controllers/assessmentController';
import { requireAuth, requireRole } from '../middleware/auth';
import { validateBody } from '../middleware/validate';
import { CreateQuestionnaireSchema, SubmitAssessmentSchema, Role } from '@capacity-connect/shared-types';

const router = Router();

router.use(requireAuth);

router.get('/', AssessmentController.getQuestionnaires);
router.get('/:id', AssessmentController.getQuestionnaireById);
router.post('/', requireRole([Role.TRAINER, Role.ADMIN]), validateBody(CreateQuestionnaireSchema), AssessmentController.createQuestionnaire);
router.post('/adaptive/next', AssessmentController.getNextAdaptiveQuestion);
router.post('/submit', validateBody(SubmitAssessmentSchema), AssessmentController.submitAssessment);

export default router;
