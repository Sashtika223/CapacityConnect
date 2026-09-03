import { Router } from 'express';
import { CourseController } from '../controllers/courseController';
import { requireAuth, requireRole } from '../middleware/auth';
import { validateBody } from '../middleware/validate';
import { CreateCourseSchema, Role } from '@capacity-connect/shared-types';

const router = Router();

router.use(requireAuth);

router.get('/', CourseController.getCourses);
router.get('/:id', CourseController.getCourseById);
router.post('/', requireRole([Role.TRAINER, Role.ADMIN]), validateBody(CreateCourseSchema), CourseController.createCourse);
router.post('/enroll', CourseController.enrollCourse);
router.put('/:courseId/progress', CourseController.updateProgress);

export default router;
