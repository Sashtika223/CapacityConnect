import { Router } from 'express';
import authRoutes from './authRoutes';
import traineeRoutes from './traineeRoutes';
import trainerRoutes from './trainerRoutes';
import adminRoutes from './adminRoutes';
import courseRoutes from './courseRoutes';
import resourceRoutes from './resourceRoutes';
import assessmentRoutes from './assessmentRoutes';
import verifyRoutes from './verifyRoutes';
import { prisma } from '../prisma/client';
import { requireAuth } from '../middleware/auth';

const router = Router();

router.use('/auth', authRoutes);
router.use('/trainee', traineeRoutes);
router.use('/trainer', trainerRoutes);
router.use('/admin', adminRoutes);
router.use('/courses', courseRoutes);
router.use('/resources', resourceRoutes);
router.use('/assessments', assessmentRoutes);
router.use('/verify', verifyRoutes);

// In-app user notifications endpoint
router.get('/notifications', requireAuth, async (req: any, res) => {
  const userId = req.user.id;
  const userRole = req.user.role;

  const notifications = await prisma.notification.findMany({
    where: {
      OR: [
        { recipientId: userId },
        { targetRole: userRole },
        { targetRole: null, recipientId: null }
      ]
    },
    orderBy: { createdAt: 'desc' },
    take: 30
  });

  const unreadCount = notifications.filter((n) => !n.isRead && (n.recipientId === userId || !n.recipientId)).length;

  res.json({ success: true, count: notifications.length, unreadCount, notifications });
});

router.patch('/notifications/:id/read', requireAuth, async (req: any, res) => {
  const { id } = req.params;
  await prisma.notification.update({
    where: { id },
    data: { isRead: true }
  });
  res.json({ success: true, message: 'Notification marked as read' });
});

router.patch('/notifications/read-all', requireAuth, async (req: any, res) => {
  const userId = req.user.id;
  await prisma.notification.updateMany({
    where: {
      OR: [
        { recipientId: userId },
        { targetRole: req.user.role },
        { targetRole: null, recipientId: null }
      ]
    },
    data: { isRead: true }
  });
  res.json({ success: true, message: 'All notifications marked as read' });
});

export default router;
