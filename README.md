# CAPACITY CONNECT (IMD • MoES)
**National Learning Management & Capacity Building Portal**

---

## 1. Overview
**CAPACITY CONNECT** is a production-grade Learning Management and Capacity Building portal engineered for the **India Meteorological Department (IMD)** and the **Ministry of Earth Sciences (MoES)**. It provides role-governed training delivery across operational meteorology domains—such as Doppler Weather Radar (DWR) operations, INSAT-3D/3DR satellite interpretation, and Numerical Weather Prediction (NWP/WRF) modeling.

The portal incorporates **9 Innovation Layer Engines**, including AI-driven competency-to-trainer matching, adaptive Item Response Theory (IRT-lite) assessments, in-house vector cosine course recommendations, cryptographic QR certificate validation, and automated engagement/dropout risk flagging.

---

## 2. Tech Stack

| Layer | Technologies |
| :--- | :--- |
| **Frontend** | React 18, Vite, TypeScript, TailwindCSS, Zustand (State), TanStack Query, Recharts, Lucide Icons, Canvas Confetti |
| **Backend** | Node.js, Express, TypeScript, Socket.io (Realtime push), Prisma ORM, PDF-Lib, QRCode, Multer, Node-Cron, Zod |
| **Database** | PostgreSQL 18+ (Relational integrity for enrollments, attempts, certificates, and audit logs) |
| **Auth & Security** | JWT (Access & Refresh tokens with auto-renewal), Bcrypt (10 salt rounds), Role-Based Access Control (RBAC), Helmet |
| **Deployment** | Docker Compose (`postgres`, `api`, `web`, `nginx`), .env configuration, PWA Service Worker for offline asset caching |

---

## 3. Demo Credentials

Quick one-click login buttons are available on the Login screen (`http://localhost:3000/login`). Alternatively, use:

| Role | Email | Password | Personnel Name & Department |
| :--- | :--- | :--- | :--- |
| 👑 **Administrator** | `admin@imd.gov.in` | `Admin@123` | Dr. M. Mohapatra, Director General & DGM Office |
| 👨‍🏫 **Trainer (Radar Lead)** | `trainer.radar@imd.gov.in` | `Password@123` | Dr. S. K. Roy, Scientist 'F', Radar Meteorology Div |
| 👨‍🏫 **Trainer (Satellite Lead)** | `trainer.satellite@imd.gov.in` | `Password@123` | Dr. Ananya Sharma, Scientist 'E', Satellite Div |
| 🧑‍🎓 **Trainee (Certified)** | `trainee1@imd.gov.in` | `Password@123` | Rohan Verma, Scientific Assistant, NWP Modeling |
| ⚠️ **Trainee (At-Risk)** | `trainee.atrisk@imd.gov.in` | `Password@123` | Suresh Gupta, Agromet Observer (Stalled enrollment) |
| ⏳ **Trainer (Pending Approval)** | `trainer.pending@imd.gov.in` | `Password@123` | Dr. Vikramaditya Rathore (Awaiting Admin approval) |

---

## 4. Quick Start & Setup

### Option A: Local Development (Current Environment)
The project runs natively on Node.js v20+ with PostgreSQL:

```bash
# 1. Clone or open monorepo root
cd d:/cc1

# 2. Install dependencies across all workspaces
npm install

# 3. Build shared-types package
npm run build --workspace=@capacity-connect/shared-types

# 4. Generate Prisma client & synchronize PostgreSQL schema
npx prisma db push --schema=prisma/schema.prisma

# 5. Populate realistic IMD/MoES demo seed data
npx tsx apps/api/src/prisma/seed.ts

# 6. Build & Start API Backend (Port 5000)
npm run build --workspace=@capacity-connect/api
npm run dev:api

# 7. Start Frontend Web Application (Port 3000)
npm run dev:web
```

Access the web portal at **`http://localhost:3000`** (API running at `http://localhost:5000`).

### Option B: Docker Compose Multi-Container Orchestration
To deploy PostgreSQL, Express API, Vite Web, and Nginx reverse proxy simultaneously:

```bash
# Start all containers in detached mode
docker compose up --build -d

# Verify running services
docker compose ps

# Access Portal
# Web Frontend: http://localhost:3000
# API Endpoints: http://localhost:5000/api
```

---

## 5. Working End-to-End Demo Paths

### Path 1: Trainee Experience
1. Open `http://localhost:3000/login` and click **"Trainee (Rohan)"** (or log in with `trainee1@imd.gov.in` / `Password@123`).
2. **Dashboard**: View enrolled courses (`IMD-RAD-201`), progress statistics, earned achievement badges, and AI course recommendations (e.g. *WRF Modeling*).
3. **Course Catalog**: Navigate to `/courses`, search for *"Satellite"*, click into a course to view syllabus, downloadable PPTs/PDFs, and assessment details.
4. **Adaptive MCQ Player**: Navigate to `/assessments/0` or launch the Doppler Radar assessment. Answer questions and observe the difficulty tag adjust between `EASY`, `MEDIUM`, and `HARD` based on your running streak.
5. **Verified Certificate**: Score $\ge 70\%$ to trigger celebration and certificate issuance. Navigate to `/certificates` to view your credential with embedded cryptographic QR code.
6. **Public Verification**: Click **"Public QR Link"** (or visit `http://localhost:3000/verify/IMD-7A9B3C`) to inspect the authentic public verification seal.

### Path 2: Trainer Experience
1. Log in as **"Trainer (Dr. Roy)"** (`trainer.radar@imd.gov.in` / `Password@123`).
2. **Trainee Monitor & Risk Flagging**: Navigate to `/trainer/trainees` to monitor student rosters. Notice the crimson **"Engagement Risk"** badge on Suresh Gupta (`trainee.atrisk@imd.gov.in`) with the diagnostic reason: *"Inactive for 12 days; 0% course progress"*.
3. **Trigger Re-Evaluation**: Click **"Re-Run Risk Evaluation"** to trigger the nightly engagement scanner on demand.
4. **MCQ Builder**: Navigate to `/trainer/questionnaires` to author a new assessment, toggle the **Adaptive MCQ Engine (IRT-lite)** switch, add questions with difficulty tiers, and publish.
5. **Study Library**: Navigate to `/trainer/library` to upload new lecture slide decks or video masterclasses with subject tags.
6. **Sentiment Triage**: Navigate to `/trainer/feedbacks` to view real-time student sentiment breakdown (`POSITIVE`, `NEUTRAL`, `NEGATIVE`) triaged by our rule-based lexicon parser.

### Path 3: Admin Experience
1. Log in as **"Admin (Director)"** (`admin@imd.gov.in` / `Admin@123`).
2. **Executive Analytics**: Navigate to `/admin/dashboard` to inspect Recharts area charts for monthly enrollments vs. completions, course pass rates, and division distribution pie charts.
3. **Personnel Approval Queue**: Navigate to `/admin/approvals`. View Dr. Vikramaditya Rathore in the pending queue and click **"Approve Trainer"** to grant course authoring permissions.
4. **AI Competency Matcher**: Navigate to `/admin/competency-matcher`. Search for *"Radar Meteorology"* or *"Numerical Weather Prediction"* to view candidate trainers ranked by the weighted algorithm:
   $$\text{Score} = 0.35(\text{Pass Rate}) + 0.30(\text{Feedback}) + 0.20(\text{Proficiency}) + 0.15(\text{Recency})$$
   Read the transparent, explainable reasoning behind each score.
5. **Real-time Push Announcement**: Navigate to `/admin/announcements`, enter a circular title and message, select target audience `ALL`, and click **"Dispatch Live Broadcast"** to push instantly to connected users via Socket.io.
6. **Audit Trail**: Navigate to `/admin/audit-logs` to inspect timestamped logs of all administrative actions.

---

## 6. The 9 Innovation Layer Engines

1. **AI Competency-to-Trainer Matcher (`GET /api/admin/competency-map/suggest?subject=X`)**: Weighted multi-factor ranking algorithm with explainable factor breakdowns.
2. **Adaptive MCQ Assessment Engine (`POST /api/assessments/adaptive/next`)**: Dynamic item response theory (IRT-lite) question calibration based on live user streak and running accuracy.
3. **Smart Course Recommender (`GET /api/trainee/recommendations`)**: In-house term frequency vector space and cosine similarity matcher correlating trainee skills/interests with course syllabus tags.
4. **Auto-Certificate Generator (`pdf-lib` + `GET /api/verify/:code`)**: Programmatically renders landscape A4 official credentials with embedded verification QR codes and public registry lookup.
5. **Engagement / Dropout Risk Flag (`POST /api/admin/risk-evaluation/trigger`)**: Automated rule-based diagnostic scanner (`node-cron` nightly at 02:00 AM) that flags enrollees with low material interaction ($<25\%$), overdue deadlines, or prolonged inactivity.
6. **Real-time In-App Notifications (`Socket.io`)**: Instant notification feed with role-targeted broadcast rooms (`role:TRAINEE`, `role:TRAINER`, `role:ADMIN`, `role:ALL`) and unread badges.
7. **Accessibility & Multi-Device PWA**: WCAG-AA compliant high-contrast dark theme, fully responsive layout, PWA manifest (`/manifest.json`), and Service Worker (`/sw.js`) for offline study caching.
8. **Trainer Library Search & Access Logging**: Full-text and format-filtered repository (PDF, PPT, Video) with atomic download tracking to feed engagement metrics.
9. **Rule-Based Feedback Sentiment Triage**: Fast, self-contained lexicon sentiment parser auto-tagging feedback as `POSITIVE`, `NEUTRAL`, or `NEGATIVE` with polarity scoring.

---

## 7. Key REST API Endpoints

### Auth & Roles
* `POST /api/auth/signup` — Register as Trainee or Trainer (Trainers queue for Admin approval).
* `POST /api/auth/login` — Sign in and receive JWT access token (1h) and refresh token (7d).
* `POST /api/auth/refresh` — Exchange refresh token for new access token.
* `GET /api/auth/me` — Retrieve current authenticated session and profile.

### Trainee & Learning
* `GET /api/trainee/dashboard` — Personal progress, enrollments, upcoming quizzes, badges.
* `GET /api/trainee/recommendations` — AI-calculated cosine similarity course suggestions.
* `GET /api/courses` — Searchable course catalog with subject and level filters.
* `POST /api/courses/enroll` — Enroll in a course module.
* `POST /api/trainee/feedback` — Submit star rating and qualitative review (auto-sentiment triaged).

### Assessments & Certification
* `GET /api/assessments/:id` — Retrieve assessment questions (options sanitized for trainees).
* `POST /api/assessments/adaptive/next` — IRT-lite adaptive question calibration based on history.
* `POST /api/assessments/submit` — Submit quiz attempt, auto-grade, and generate certificate if passed.
* `GET /api/verify/:code` — **Public** cryptographic certificate verification endpoint.

### Trainer & Library
* `GET /api/trainer/dashboard` — Course overview, feedback sentiment breakdown, at-risk count.
* `GET /api/trainer/trainees` — Trainee participation monitor with dropout risk diagnostics.
* `GET /api/resources` — Trainer study library search with format filters.
* `POST /api/resources/:id/access` — Log learning resource download and update engagement.
* `POST /api/assessments` — Create new questionnaire with difficulty tiers and adaptive toggle.

### Administration & Governance
* `GET /api/admin/users/pending` — Queue of pending trainer registrations.
* `PATCH /api/admin/users/:userId/status` — Approve, reject, or suspend user accounts.
* `GET /api/admin/analytics` — Recharts aggregate data (trends, pass rates, department distribution).
* `GET /api/admin/competency-map/suggest?subject=Radar` — AI trainer matcher with explainability.
* `POST /api/admin/announcements` — Publish and push real-time Socket.io announcement.
* `POST /api/admin/risk-evaluation/trigger` — Trigger engagement dropout risk scanner.
* `GET /api/admin/audit-logs` — Administrative audit trail inspection.
