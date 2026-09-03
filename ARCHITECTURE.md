# CAPACITY CONNECT — Architectural Design Document
**National Learning Management & Capacity Building Portal for IMD / MoES Personnel**

---

## 1. System Philosophy & Objectives
CAPACITY CONNECT was designed as an enterprise-grade, high-reliability Capacity Building and Learning Management Portal for the India Meteorological Department (IMD) and Ministry of Earth Sciences (MoES). The architecture delivers:
- Strict role-based governance across Trainees, Trainers, and Administrative Officers.
- Continuous engagement tracking with proactive dropout risk mitigation.
- Tamper-proof, cryptographically verifiable capacity certificates to solve credential falsification.
- Calibrated, adaptive learning mechanics using item response theory (IRT-lite).
- Real-time emergency & circular push broadcasting via WebSockets.

---

## 2. Monorepo Structure & Decoupled Packages
```
capacity-connect/
├── apps/
│   ├── api/                     # Node.js, Express, TypeScript, Socket.io, Prisma ORM
│   │   ├── src/
│   │   │   ├── controllers/     # Modular business endpoints per role
│   │   │   ├── middleware/      # JWT verification, RBAC guard, Zod validation, error handler
│   │   │   ├── services/        # 9 Innovation Layer Engines (see Section 3)
│   │   │   ├── routes/          # Express REST router definitions
│   │   │   └── prisma/          # Prisma client and comprehensive IMD seed script
│   │   └── Dockerfile           # Multi-stage production container build
│   │
│   └── web/                     # React 18, Vite, TypeScript, TailwindCSS, Zustand, Recharts
│       ├── src/
│       │   ├── components/      # Glassmorphism cards, stat widgets, notification drawer
│       │   ├── pages/           # Trainee, Trainer, and Admin operational interfaces
│       │   ├── layouts/         # Role-based dashboard layouts
│       │   ├── store/           # Zustand state persistence (Auth, Notifications)
│       │   └── services/        # Axios API client with automatic JWT token refresh
│       ├── public/              # PWA manifest, SVG icons, service worker for offline assets
│       └── Dockerfile           # Optimized Nginx static distribution container
│
├── packages/
│   └── shared-types/            # Canonical TypeScript interfaces and Zod validation schemas
│
├── prisma/
│   └── schema.prisma            # 16-entity relational schema with foreign keys and enums
│
├── docker-compose.yml           # Multi-container orchestration (Postgres, API, Web, Nginx)
├── ARCHITECTURE.md              # Innovation algorithms and system design decisions
└── README.md                    # Setup guide, role-based demo paths, and API references
```

---

## 3. Innovation Layer — Algorithmic Design Decisions

### 3.1 AI Competency-to-Trainer Matcher (`apps/api/src/services/competencyMatcher.ts`)
* **Problem**: When a new specialized operational course (e.g., Dual-Pol Radar or Rapid Cyclogenesis) is mandated by MoES, selecting the optimal instructor was historically subjective and manual.
* **Solution**: An explainable multi-factor scoring formula that ranks candidate trainers on a 0–100 index:
  $$\text{Composite Score} = 0.35 \times (\text{Trainee Pass Rate}) + 0.30 \times (\text{Avg Feedback Rating} \times 20) + 0.20 \times (\text{Verified Base Proficiency}) + 0.15 \times (\text{Recency Factor})$$
  - **Trainee Pass Rate (35%)**: Gauges the instructor's pedagogical effectiveness based on historical attempt results.
  - **Student Feedback Rating (30%)**: Normalizes qualitative feedback (1–5 scale).
  - **Base Competency (20%)**: Self-declared or MoES committee-verified proficiency in the domain.
  - **Recency Factor (15%)**: Exponential decay metric penalizing dormancy ($>90$ days inactive).
* **Explainability**: Every recommendation includes a breakdown string and individual factor scores for administrative transparency.

### 3.2 Adaptive MCQ Assessment Engine (`apps/api/src/services/adaptiveEngine.ts`)
* **Problem**: Standard static quizzes fail to accurately calibrate the competency of varied personnel (e.g., early-career Scientific Assistants vs. senior Meteorologists).
* **Solution**: An Item Response Theory (IRT-lite) running accuracy and streak-based engine:
  - Trainees start at `MEDIUM` difficulty.
  - A positive streak of $+2$ consecutive correct answers elevates the question difficulty to `HARD`.
  - An error streak ($\le -1$) recalibrates the question difficulty down to `EASY`.
  - Computes running accuracy, current streak, and estimated ability level (`BEGINNER`, `INTERMEDIATE`, `EXPERT`) in real time.

### 3.3 Smart Course Recommender (`apps/api/src/services/courseRecommender.ts`)
* **Problem**: Recommending next courses without external proprietary ML cloud dependencies.
* **Solution**: In-house term-frequency vector space and Cosine Similarity:
  - Vectorizes user profile (skills weighted $2.0\times$, interests weighted $1.5\times$, department weighted $1.2\times$).
  - Vectorizes course syllabus (tags weighted $2.0\times$, subject $1.5\times$, description $1.0\times$).
  - Calculates Cosine Similarity: $\cos(\theta) = \frac{\mathbf{A} \cdot \mathbf{B}}{\|\mathbf{A}\|\|\mathbf{B}\|}$.
  - Returns similarity percentages with matched tag intersections and human-readable explanations.

### 3.4 Auto-Certificate Generator (`apps/api/src/services/certificateService.ts`)
* **Problem**: Paper certificates and easily forged PDF templates.
* **Solution**: On passing score completion ($\ge 70\%$), `pdf-lib` programmatically renders a landscape A4 credential with:
  - Dynamic student name, designation, department, course code, grade, and timestamp.
  - Cryptographic QR code linking to `/verify/:verificationCode`.
  - Public endpoint `GET /verify/:code` validates authenticity against database records.

### 3.5 Engagement / Dropout Risk Flag (`apps/api/src/services/riskDetector.ts`)
* **Problem**: Trainees stalling or abandoning modules unnoticed.
* **Solution**: Automated rule-based diagnostic evaluation (scheduled via `node-cron` nightly at 02:00 AM, or on-demand via admin):
  - Flags `atRisk: true` if inactive for $>7$ days.
  - Flags if course material access ratio is $<25\%$ after 5 days.
  - Flags if upcoming questionnaire deadlines are overdue.
  - Stores human-readable diagnostic reasons (e.g., *"Inactive for 12 days; 0% progress"*).

### 3.6 In-App Real-Time Notification & Broadcast Feed (`apps/api/src/services/notificationService.ts`)
* **Problem**: Urgent weather circulars or exam notifications need instant push delivery.
* **Solution**: Socket.io server with targeted room routing:
  - Personal rooms: `user:${userId}`
  - Role-targeted broadcasts: `role:TRAINEE`, `role:TRAINER`, `role:ADMIN`
  - Global announcements: `role:ALL`
  - Persisted in PostgreSQL database with read/unread tracking.

### 3.7 Rule-Based Feedback Sentiment Triage (`apps/api/src/services/sentimentAnalyzer.ts`)
* **Problem**: Administrators need immediate alerts on negative feedback without external LLM API costs or rate limits.
* **Solution**: High-performance rule-based lexicon parser:
  - Handles meteorological terms, valence shifters (negations like *"not"*, intensifiers like *"very"*).
  - Blends star ratings with text polarity to assign `POSITIVE`, `NEUTRAL`, or `NEGATIVE` tags and numerical scores $[-1.0, +1.0]$.

### 3.8 Trainer Library Search & Access Tracking (`apps/api/src/controllers/resourceController.ts`)
* Multi-format search across PDF, PPT, and Video materials.
* Logs every download/access to `ResourceAccessLog`, feeding directly into the engagement risk algorithm.

### 3.9 Accessibility, Responsive UI & PWA Offline Caching
* Responsive Tailwind CSS theme with WCAG-AA contrast ratios.
* PWA Web Manifest (`/manifest.json`) and Service Worker (`/sw.js`) enabling offline access to downloaded assets.

---

## 4. Security & Relational Integrity
- **Password Protection**: Passwords hashed with `bcryptjs` (salt rounds: 10).
- **Authentication**: Stateless JSON Web Tokens (Access token: 15m; Refresh token: 7d with auto-renewal interceptor).
- **Relational Integrity**: Foreign key constraints and cascade rules enforced via Prisma schema.
- **Route Guards**: Dual-layer RBAC on both server Express middleware (`requireAuth`, `requireRole`) and client React Router (`<ProtectedRoute allowedRoles={[...]} />`).
- **Input Validation**: All payloads validated with `zod` schemas.
