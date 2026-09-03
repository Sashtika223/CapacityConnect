import { Server as SocketIOServer } from 'socket.io';
import { prisma } from '../prisma/client';
import { Role, NotificationType } from '@capacity-connect/shared-types';

export class NotificationService {
  private static io: SocketIOServer | null = null;

  public static initialize(ioServer: SocketIOServer) {
    this.io = ioServer;

    this.io.on('connection', (socket) => {
      // User joins role-based room and personal room
      socket.on('join', (data: { userId?: string; role?: string }) => {
        if (data.userId) {
          socket.join(`user:${data.userId}`);
        }
        if (data.role) {
          socket.join(`role:${data.role}`);
        }
        socket.join('role:ALL');
      });

      socket.on('disconnect', () => {
        // Socket disconnected
      });
    });

    console.log('[SOCKET] Notification Socket.io service initialized.');
  }

  /**
   * Dispatches a real-time notification to a specific user or role broadcast
   */
  public static async sendNotification(params: {
    recipientId?: string;
    targetRole?: Role | 'ALL';
    title: string;
    message: string;
    type?: NotificationType;
    link?: string;
  }) {
    const { recipientId, targetRole, title, message, type = NotificationType.ANNOUNCEMENT, link } = params;

    // Save notification in database
    const notif = await prisma.notification.create({
      data: {
        recipientId: recipientId || null,
        targetRole: targetRole === 'ALL' || !targetRole ? null : (targetRole as any),
        title,
        message,
        type: type as any,
        link,
        isRead: false
      }
    });

    if (this.io) {
      if (recipientId) {
        // Direct push to user
        this.io.to(`user:${recipientId}`).emit('notification:new', notif);
      } else if (targetRole && targetRole !== 'ALL') {
        // Broadcast to role
        this.io.to(`role:${targetRole}`).emit('notification:new', notif);
      } else {
        // Broadcast to all
        this.io.to('role:ALL').emit('notification:new', notif);
      }
    }

    return notif;
  }
}
