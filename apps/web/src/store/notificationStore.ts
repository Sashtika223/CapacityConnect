import { create } from 'zustand';
import { io, Socket } from 'socket.io-client';

export interface NotificationItem {
  id: string;
  recipientId?: string | null;
  targetRole?: string | null;
  title: string;
  message: string;
  type: string;
  link?: string | null;
  isRead: boolean;
  createdAt: string;
}

interface NotificationState {
  notifications: NotificationItem[];
  unreadCount: number;
  socket: Socket | null;
  initSocket: (userId?: string, role?: string) => void;
  setNotifications: (items: NotificationItem[], unread: number) => void;
  addNotification: (item: NotificationItem) => void;
  markAsRead: (id: string) => void;
  markAllAsRead: () => void;
}

export const useNotificationStore = create<NotificationState>((set, get) => ({
  notifications: [],
  unreadCount: 0,
  socket: null,

  initSocket: (userId?: string, role?: string) => {
    if (get().socket) return; // already initialized

    const socket = io(window.location.origin, {
      transports: ['websocket', 'polling']
    });

    socket.on('connect', () => {
      socket.emit('join', { userId, role });
    });

    socket.on('notification:new', (notification: NotificationItem) => {
      get().addNotification(notification);
    });

    set({ socket });
  },

  setNotifications: (notifications, unreadCount) => {
    set({ notifications, unreadCount });
  },

  addNotification: (item) => {
    set((state) => ({
      notifications: [item, ...state.notifications],
      unreadCount: state.unreadCount + 1
    }));
  },

  markAsRead: (id) => {
    set((state) => ({
      notifications: state.notifications.map((n) => (n.id === id ? { ...n, isRead: true } : n)),
      unreadCount: Math.max(0, state.unreadCount - 1)
    }));
  },

  markAllAsRead: () => {
    set((state) => ({
      notifications: state.notifications.map((n) => ({ ...n, isRead: true })),
      unreadCount: 0
    }));
  }
}));
