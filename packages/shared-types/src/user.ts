import { z } from 'zod';

export enum Role {
  TRAINEE = 'TRAINEE',
  TRAINER = 'TRAINER',
  ADMIN = 'ADMIN'
}

export enum ApprovalStatus {
  PENDING = 'PENDING',
  APPROVED = 'APPROVED',
  REJECTED = 'REJECTED',
  SUSPENDED = 'SUSPENDED'
}

export const SignupSchema = z.object({
  email: z.string().email('Invalid email address'),
  password: z.string().min(6, 'Password must be at least 6 characters'),
  name: z.string().min(2, 'Name must be at least 2 characters'),
  role: z.nativeEnum(Role).default(Role.TRAINEE),
  employeeId: z.string().optional(),
  department: z.string().optional(),
  organization: z.string().default('India Meteorological Department (IMD)'),
  designation: z.string().optional()
});

export type SignupInput = z.infer<typeof SignupSchema>;

export const LoginSchema = z.object({
  email: z.string().email(),
  password: z.string().min(1, 'Password is required')
});

export type LoginInput = z.infer<typeof LoginSchema>;

export const ProfileUpdateSchema = z.object({
  qualifications: z.string().optional(),
  experienceYears: z.number().min(0).optional(),
  bio: z.string().optional(),
  interests: z.array(z.string()).optional(),
  skills: z.array(z.string()).optional(),
  certificatesEarned: z.array(z.any()).optional()
});

export type ProfileUpdateInput = z.infer<typeof ProfileUpdateSchema>;

export interface UserResponse {
  id: string;
  email: string;
  name: string;
  role: Role;
  approvalStatus: ApprovalStatus;
  employeeId?: string | null;
  department?: string | null;
  organization: string;
  designation?: string | null;
  avatarUrl?: string | null;
  createdAt: string;
  profile?: {
    qualifications?: string | null;
    experienceYears: number;
    bio?: string | null;
    interests: string[];
    skills: string[];
    certificatesEarned: any[];
  } | null;
}
