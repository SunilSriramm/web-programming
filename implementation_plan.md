# WORKWAY – Full-Stack Online Worker Service Booking & Management System

WORKWAY is a production-style full-stack web application designed for booking skilled workers (Electrician, Plumber, Carpenter, Painter, etc.), routing requests automatically to specialized department heads, assigning workers, tracking live status via WebSockets (Socket.IO), completing payments, and gathering user feedback.

## Architecture & Technology Stack

```
                   WORKWAY APPLICATION
                            |
           React Single-Page App (Vite + Router)
                            |
                     REST APIs + WebSocket
                            |
                  Node.js + Express Server
                            |
         +------------------+------------------+
         |                                     |
  SQLite / Database Database            Socket.IO Engine
  (Persistent tables & relations)       (Real-time updates & rooms)
```

- **Frontend**: React 18 (Vite), React Router v6, Lucide Icons, Axios, Socket.IO Client, Custom Sleek Modern CSS Design System (Dark/Light glassy accents, responsive UI).
- **Backend**: Node.js, Express.js, Socket.IO Server, JWT Authentication, bcryptjs password hashing, SQLite / MySQL driver (using SQLite `better-sqlite3`/`sqlite3` for zero-setup execution with MySQL-compatible SQL queries).
- **Authentication**: Single unified login endpoint (`/login`) for ALL roles (`ADMIN`, `USER`, `DEPARTMENT_HEAD`, `WORKER`). Backend auto-detects role and returns identity token.

---

## Key Features & Business Rules

1. **Single Common Login Portal (`/login`)**:
   - Single form with Username/ID and Password.
   - No role selector on frontend; backend inspects user record across users/heads/workers tables or unified auth table.
   - Redirects dynamically based on role (`/admin/dashboard`, `/user/dashboard`, `/head/dashboard`, `/worker/dashboard`).
2. **Automatic Department Routing**:
   - Service (e.g. Ceiling Fan Install) maps to Department (e.g. Electrical Department).
   - Creating a booking sets status to `BOOKED` then automatically transitions to `SENT_TO_DEPARTMENT`.
3. **Department Head Isolation**:
   - Department Heads manage ONLY their department's workers and bookings.
   - Head can assign available workers, view worker status, and create new workers under their department.
4. **Worker Workflow**:
   - Workers receive real-time assignment notifications.
   - Accept or Reject job. Rejection notifies Department Head for reassignment.
   - Status transitions: `ACCEPTED` -> `ON_THE_WAY` -> `WORK_STARTED` -> `WORK_IN_PROGRESS` -> `WORK_COMPLETED`.
5. **Real-time User Tracking**:
   - Live visual status timeline (stepper) with Socket.IO room broadcast (`booking_WB1001`, `user_X`).
   - Updates status instantly without requiring page refresh.
6. **Payment & Feedback**:
   - After `WORK_COMPLETED`, booking enters `PAYMENT_PENDING`.
   - Sandbox payment with UPI, Card, Net Banking, or Cash generates transaction code (`TXN...`) -> `PAYMENT_SUCCESS`.
   - User submits 1–5 star rating & feedback -> `FEEDBACK_SUBMITTED` -> `CLOSED`.
7. **Admin Control**:
   - Total system oversight: Manage users, department heads, workers, departments, services, payments, feedback, and system logs.

---

## Database Schema Design (`database/schema.sql`)

- **`users`**: `id`, `user_code`, `name`, `email`, `phone`, `password_hash`, `address`, `status`, `created_at`
- **`departments`**: `id`, `name`, `description`, `icon`, `status`, `created_at`
- **`department_heads`**: `id`, `head_code`, `department_id`, `name`, `email`, `phone`, `password_hash`, `status`, `created_at`
- **`workers`**: `id`, `worker_code`, `department_id`, `name`, `email`, `phone`, `password_hash`, `address`, `availability` ('AVAILABLE'/'BUSY'/'OFFLINE'), `status`, `created_at`
- **`services`**: `id`, `department_id`, `name`, `description`, `base_price`, `status`, `created_at`
- **`bookings`**: `id`, `booking_code`, `user_id`, `service_id`, `department_id`, `worker_id`, `work_description`, `location`, `booking_date`, `booking_time`, `additional_details`, `amount`, `status`, `created_at`, `updated_at`
- **`booking_status_history`**: `id`, `booking_id`, `status`, `updated_by_role`, `updated_by_id`, `message`, `created_at`
- **`payments`**: `id`, `booking_id`, `user_id`, `amount`, `payment_method`, `transaction_id`, `status`, `paid_at`, `created_at`
- **`feedback`**: `id`, `booking_id`, `user_id`, `worker_id`, `rating`, `comment`, `created_at`
- **`notifications`**: `id`, `user_type`, `user_id`, `booking_id`, `title`, `message`, `is_read`, `created_at`

---

## Proposed Project Structure

```
WORKWAY/
├── backend/
│   ├── config/
│   │   └── database.js
│   ├── controllers/
│   │   ├── authController.js
│   │   ├── userController.js
│   │   ├── headController.js
│   │   ├── workerController.js
│   │   ├── adminController.js
│   │   ├── bookingController.js
│   │   ├── paymentController.js
│   │   └── feedbackController.js
│   ├── middleware/
│   │   ├── authMiddleware.js
│   │   └── roleMiddleware.js
│   ├── routes/
│   │   ├── authRoutes.js
│   │   ├── userRoutes.js
│   │   ├── headRoutes.js
│   │   ├── workerRoutes.js
│   │   ├── adminRoutes.js
│   │   ├── bookingRoutes.js
│   │   ├── paymentRoutes.js
│   │   └── feedbackRoutes.js
│   ├── sockets/
│   │   └── socketHandler.js
│   ├── utils/
│   │   ├── dbInit.js
│   │   └── seedData.js
│   ├── .env
│   ├── package.json
│   └── server.js
├── frontend/
│   ├── public/
│   ├── src/
│   │   ├── assets/
│   │   ├── components/
│   │   │   ├── Navbar.jsx
│   │   │   ├── Sidebar.jsx
│   │   │   ├── Footer.jsx
│   │   │   ├── StatusBadge.jsx
│   │   │   ├── ProgressTimeline.jsx
│   │   │   ├── NotificationDropdown.jsx
│   │   │   ├── ProtectedRoute.jsx
│   │   │   └── Toast.jsx
│   │   ├── context/
│   │   │   ├── AuthContext.jsx
│   │   │   └── SocketContext.jsx
│   │   ├── pages/
│   │   │   ├── Home.jsx
│   │   │   ├── About.jsx
│   │   │   ├── Services.jsx
│   │   │   ├── Login.jsx
│   │   │   ├── Register.jsx
│   │   │   ├── user/
│   │   │   │   ├── UserDashboard.jsx
│   │   │   │   ├── CreateBooking.jsx
│   │   │   │   ├── UserBookings.jsx
│   │   │   │   ├── BookingDetails.jsx
│   │   │   │   ├── LiveTracking.jsx
│   │   │   │   ├── PaymentPage.jsx
│   │   │   │   ├── PaymentHistory.jsx
│   │   │   │   └── UserNotifications.jsx
│   │   │   ├── head/
│   │   │   │   ├── HeadDashboard.jsx
│   │   │   │   ├── HeadRequests.jsx
│   │   │   │   ├── HeadBookings.jsx
│   │   │   │   ├── HeadWorkers.jsx
│   │   │   │   └── CreateWorker.jsx
│   │   │   ├── worker/
│   │   │   │   ├── WorkerDashboard.jsx
│   │   │   │   ├── WorkerAssignments.jsx
│   │   │   │   ├── CurrentWork.jsx
│   │   │   │   └── WorkerHistory.jsx
│   │   │   └── admin/
│   │   │       ├── AdminDashboard.jsx
│   │   │       ├── AdminUsers.jsx
│   │   │       ├── AdminHeads.jsx
│   │   │       ├── AdminWorkers.jsx
│   │   │       ├── AdminDepartments.jsx
│   │   │       ├── AdminServices.jsx
│   │   │       ├── AdminBookings.jsx
│   │   │       ├── AdminPayments.jsx
│   │   │       └── AdminFeedback.jsx
│   │   ├── services/
│   │   │   └── api.js
│   │   ├── styles/
│   │   │   └── index.css
│   │   ├── App.jsx
│   │   └── main.jsx
│   ├── package.json
│   └── vite.config.js
└── README.md
```

---

## Pre-seeded Demo Accounts

| Role | Username / ID | Password | Access Level |
|---|---|---|---|
| **ADMIN** | `ADMIN001` | Admin@123`` | Global system admin |
| **USER** | `USER001` | `User@123` | Customer requesting services |
| **DEPARTMENT_HEAD** | ELECHEAD01`` | `Head@123` | Head of Electrical Dept |
| **DEPARTMENT_HEAD** | `PLUMHEAD01` | `Head@123` | Head of Plumbing Dept |
| **WORKER** | `WORKER001` | `Worker@123` | Electrician (Kumar) |
| **WORKER** | `WORKER002` | `Worker@123` | Plumber (Ravi) |

---

## Implementation Phases

### Phase 1: Setup Workspace & Backend Base
- Initialize `backend` package.json with Express, Cors, dotenv, bcryptjs, jsonwebtoken, socket.io, sqlite3.
- Build Database initializer `dbInit.js` with foreign keys, seed data, and schema auto-creation.
- Build JWT authentication middleware and single-endpoint unified login controller `POST /api/auth/login`.

### Phase 2: Core Domain Logic & REST APIs
- **Booking & Routing API**: Automatic department routing when service is chosen. Status history tracking.
- **Department Head APIs**: Request queue, available worker filtering, assign/reassign endpoint, create worker endpoint.
- **Worker APIs**: Assigned job list, Accept/Reject job endpoints, progressive status transition endpoint with validation (`WORKER_ACCEPTED` -> `ON_THE_WAY` -> `WORK_STARTED` -> `WORK_IN_PROGRESS` -> `WORK_COMPLETED`).
- **Payment & Feedback APIs**: Process payment transaction, write payment record, trigger feedback submission mode, close booking.
- **Admin APIs**: Full CRUD on departments, services, heads, workers, users, and summary statistics.

### Phase 3: Socket.IO Integration
- Server socket rooms (`user_{id}`, `head_{deptId}`, `worker_{workerId}`, `booking_{bookingCode}`).
- Real-time event broadcasting for all status changes and notifications.

### Phase 4: Frontend Development
- Initialize React (Vite) app.
- Build responsive UI with dark/light premium aesthetic, status badges, timelines, navigation bars, and dynamic dashboards.
- Implement socket subscriber context for live timeline progress bar without manual refresh.

### Phase 5: End-to-End Verification
- Run complete demo flow (User -> Booking -> Dept Head -> Worker Accept -> Live Progress updates -> Payment -> Rating -> Closed).
- Verify single login authorization restrictions across roles.

---

## Verification Plan

### Automated & API Tests
1. **Unified Login Test**: Verify `ADMIN001`, `USER001`, `ELECHEAD01`, `WORKER001` all log in via `/api/auth/login` and receive correct JWT role payloads without passing `role` in request body.
2. **Role Authorization Test**: Ensure `WORKER001` token gets 403 on `/api/admin/users` and `USER001` gets 403 on `/api/head/workers`.
3. **Invalid Transition Test**: Attempt moving booking status directly from `BOOKED` to `WORK_COMPLETED` and confirm validation fails.

### Manual End-to-End Demo Verification
1. Login as `USER001`, book **Electrician - Ceiling Fan Installation**.
2. Observe status `BOOKED` -> `SENT_TO_DEPARTMENT` automatically.
3. Login as `ELECHEAD01` in second browser/tab, view request `WB1001`, assign `Kumar (WORKER001)`.
4. Login as `WORKER001` in third browser/tab, accept job `WB1001`.
5. Step through status buttons: **On the Way** -> **Start Work** -> **Work in Progress** -> **Complete Work**.
6. Switch to `USER001` live tracking tab: verify timeline updates dynamically in real-time without page refresh.
7. Complete payment via simulated UPI gateway.
8. Submit 5-star rating & comment "Excellent service!".
9. Confirm status becomes `CLOSED` and entry appears in history.
