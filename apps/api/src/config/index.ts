import dotenv from 'dotenv';
import path from 'path';

dotenv.config({ path: path.resolve(__dirname, '../../../.env') });
dotenv.config(); // fallback to process.env

export const CONFIG = {
  PORT: process.env.PORT ? parseInt(process.env.PORT, 10) : 5000,
  NODE_ENV: process.env.NODE_ENV || 'development',
  DATABASE_URL: process.env.DATABASE_URL || 'postgresql://postgres:postgres@localhost:5432/capacity_connect?schema=public',
  JWT_SECRET: process.env.JWT_SECRET || 'imd_capacity_connect_super_secret_jwt_key_2026',
  JWT_REFRESH_SECRET: process.env.JWT_REFRESH_SECRET || 'imd_capacity_connect_refresh_secret_key_2026',
  JWT_EXPIRES_IN: '1h',
  JWT_REFRESH_EXPIRES_IN: '7d',
  CLIENT_URL: process.env.CLIENT_URL || 'http://localhost:3000',
  UPLOAD_DIR: path.resolve(process.cwd(), process.env.UPLOAD_DIR || 'uploads')
};
