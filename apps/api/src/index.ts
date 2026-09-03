import http from 'http';
import express from 'express';
import cors from 'cors';
import helmet from 'helmet';
import morgan from 'morgan';
import path from 'path';
import fs from 'fs';
import { Server as SocketIOServer } from 'socket.io';
import { CONFIG } from './config';
import apiRoutes from './routes';
import { errorHandler } from './middleware/errorHandler';
import { NotificationService } from './services/notificationService';
import { RiskDetector } from './services/riskDetector';
import { prisma } from './prisma/client';

const app = express();
const server = http.createServer(app);

// Initialize Socket.io
const io = new SocketIOServer(server, {
  cors: {
    origin: '*',
    methods: ['GET', 'POST', 'PATCH', 'PUT', 'DELETE']
  }
});

NotificationService.initialize(io);

// Security & Utility Middleware
app.use(helmet({ crossOriginResourcePolicy: false }));
app.use(cors({ origin: '*' }));
app.use(express.json({ limit: '20mb' }));
app.use(express.urlencoded({ extended: true, limit: '20mb' }));
app.use(morgan('dev'));

// Ensure uploads folder exists
const uploadsDir = path.resolve(process.cwd(), 'uploads');
if (!fs.existsSync(uploadsDir)) {
  fs.mkdirSync(uploadsDir, { recursive: true });
}
app.use('/uploads', express.static(uploadsDir));

// Health check endpoint
app.get('/api/health', async (req, res) => {
  try {
    await prisma.$queryRaw`SELECT 1`;
    res.json({
      status: 'HEALTHY',
      service: 'CAPACITY CONNECT API',
      organization: 'IMD / MoES',
      timestamp: new Date().toISOString(),
      database: 'CONNECTED'
    });
  } catch (err: any) {
    res.status(500).json({
      status: 'UNHEALTHY',
      service: 'CAPACITY CONNECT API',
      database: 'DISCONNECTED',
      error: err.message
    });
  }
});

// Mount Central API Routes
app.use('/api', apiRoutes);

// Global Error Handler
app.use(errorHandler);

// Initialize Cron Jobs
RiskDetector.initCronJob();

// Start Server
const PORT = CONFIG.PORT;
server.listen(PORT, () => {
  console.log(`=======================================================`);
  console.log(`🚀 CAPACITY CONNECT API is running on http://localhost:${PORT}`);
  console.log(`🌍 Organization: India Meteorological Department / MoES`);
  console.log(`⚡ Environment: ${CONFIG.NODE_ENV}`);
  console.log(`=======================================================`);
});

export { app, server };
