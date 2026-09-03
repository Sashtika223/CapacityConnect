import { create } from 'zustand';
import { UserResponse, Role } from '@capacity-connect/shared-types';

interface AuthState {
  user: UserResponse | null;
  token: string | null;
  refreshToken: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  setAuth: (user: UserResponse, token: string, refreshToken?: string) => void;
  updateUser: (user: Partial<UserResponse>) => void;
  logout: () => void;
}

export const useAuthStore = create<AuthState>((set) => {
  const savedToken = localStorage.getItem('cc_token');
  const savedRefreshToken = localStorage.getItem('cc_refresh_token');
  const savedUser = localStorage.getItem('cc_user');

  return {
    user: savedUser ? JSON.parse(savedUser) : null,
    token: savedToken,
    refreshToken: savedRefreshToken,
    isAuthenticated: !!savedToken,
    isLoading: false,
    setAuth: (user, token, refreshToken) => {
      localStorage.setItem('cc_token', token);
      if (refreshToken) localStorage.setItem('cc_refresh_token', refreshToken);
      localStorage.setItem('cc_user', JSON.stringify(user));
      set({ user, token, refreshToken: refreshToken || null, isAuthenticated: true });
    },
    updateUser: (updatedFields) => {
      set((state) => {
        if (!state.user) return state;
        const updatedUser = { ...state.user, ...updatedFields };
        localStorage.setItem('cc_user', JSON.stringify(updatedUser));
        return { user: updatedUser as UserResponse };
      });
    },
    logout: () => {
      localStorage.removeItem('cc_token');
      localStorage.removeItem('cc_refresh_token');
      localStorage.removeItem('cc_user');
      set({ user: null, token: null, refreshToken: null, isAuthenticated: false });
    }
  };
});
