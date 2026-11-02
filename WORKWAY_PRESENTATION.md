# WORKWAY — Project Report and Presentation

## Project Overview

**Project title:** WORKWAY — Online Worker Service Booking and Management System  
**Project type:** Full-stack web application  
**Primary users:** Customers, department heads, workers, and administrators

WORKWAY provides a shared online process for requesting household services, assigning a worker, tracking job progress, recording payment, and collecting customer feedback. The main application uses a React frontend, a Node.js and Express backend, an SQLite database, and Socket.IO for live updates. A separate, optional PHP entry point demonstrates a few PHP endpoints; it is not the backend for the main booking workflow.

## Contents

- Abstract
- [1. Introduction](#1-introduction)
- [2. Literature Review and Background Study](#2-literature-review-and-background-study)
- [3. System Requirements](#3-system-requirements)
- [4. System Design and Methodology](#4-system-design-and-methodology)
- [5. Technology Stack](#5-technology-stack)
- [6. Implementation](#6-implementation)
- [7. Results and Discussion](#7-results-and-discussion)
- [8. Challenges and Issues Faced](#8-challenges-and-issues-faced)
- [9. Testing](#9-testing)
- [10. Appendix A — React Frontend Files](#10-appendix-a--react-frontend-files)
- [11. Appendix B — Node.js Backend Files](#11-appendix-b--nodejs-backend-files)
- [12. Appendix C — Database and Real-Time Files](#12-appendix-c--database-and-real-time-files)
- [13. Appendix D — PHP and Project Operation Files](#13-appendix-d--php-and-project-operation-files)
- [14. Conclusion and Future Scope](#14-conclusion-and-future-scope)
- [15. References](#15-references)
- [16. System Usage / Use Case Description](#16-system-usage--use-case-description)
- [17. User Manual — Running the Application](#17-user-manual--running-the-application)
- [18. Glossary of Terms](#18-glossary-of-terms)
- [19. SDG Mapping](#19-sdg-mapping)

## Abstract

WORKWAY is a web-based worker service booking and management system. It allows a customer to find a service, describe a job, and submit a booking. The request is routed to its service department, where a department head can assign a worker. Workers update the job as it progresses, and the customer can follow booking status, record payment, and provide feedback.

The application separates features by user role. React builds the browser interface, while a Node.js and Express API validates requests, applies workflow rules, and reads or writes data in SQLite. Socket.IO supports timely status and notification updates. PHP is included as an optional, independent demonstration with a project overview, health check, and example login endpoint. The PHP example is not connected to the primary booking database or authentication workflow.

## 1. Introduction

### 1.1 Background

Arranging a service often involves coordinating a customer, a service provider, a dispatcher, and the worker who performs the job. When requests and updates are handled through separate channels, it can be difficult to track responsibility and progress. WORKWAY brings these steps into one application.

### 1.2 Problem Statement

The project addresses the need for an organized way to submit service requests, route them to the right department, assign workers, and keep the customer informed about progress.

### 1.3 Objectives

- Let customers browse services and request work online.
- Route bookings to the department responsible for the selected service.
- Give department heads a view of requests and worker assignments.
- Let workers manage assignments and update job status.
- Show customers booking progress and related notifications.
- Keep payment and feedback records associated with completed work.
- Give administrators tools for overseeing platform records.

### 1.4 Scope

The project includes role-specific pages, booking workflows, worker assignment, status history, notifications, payment-recording flows, feedback, and administration screens. It is a project application and demonstration; it does not, by itself, provide a production payment gateway or a production-ready PHP authentication service.

## 2. Literature Review and Background Study

WORKWAY applies common patterns from web-based service marketplaces and workflow-management systems:

- **Client/server architecture:** The browser presents the interface; a server provides shared business logic and data access.
- **Role-based access:** Customers, department heads, workers, and administrators see different pages and permitted operations.
- **REST-style APIs:** HTTP endpoints allow the frontend to request or update application data.
- **Relational data storage:** SQLite tables represent users, services, departments, bookings, status history, payments, feedback, and notifications.
- **Event-driven updates:** Socket.IO allows the server to send updates to connected clients without requiring every page to be manually refreshed.
- **Component-based user interfaces:** React components can be reused across pages to keep common controls and status displays consistent.

These are established software design approaches rather than a claim that a formal literature survey or comparative benchmark was conducted. The implementation uses them to organize a service-request workflow from creation to closure.

## 3. System Requirements

### 3.1 Functional Requirements

1. A customer can register, sign in, browse services, and create a booking.
2. The system associates a booking with its service and responsible department.
3. A department head can review requests and assign or reassign a worker.
4. A worker can view assignments and update supported work statuses.
5. A customer can view booking history, progress, and notifications.
6. The system can store payment and feedback records for a booking.
7. An administrator can view and manage core users, departments, services, bookings, payments, and feedback.
8. The PHP demonstration can return a project overview and health response, and provides an example login endpoint.

### 3.2 Non-Functional Requirements

- **Usability:** Role-specific pages should make common tasks easy to locate.
- **Maintainability:** Pages, routes, controllers, and shared interface components are organized into separate modules.
- **Data integrity:** Booking records are related to the relevant user, service, department, and worker.
- **Responsiveness:** The web interface should be usable on common desktop and mobile browsers.
- **Security:** Authentication, role checks, password hashing, and safe secret management are important for deployment. The PHP sample is illustrative only and must not be used as a production authentication service.
- **Availability:** The main application requires its frontend and backend processes to be running. Live updates require an active connection to the backend.

### 3.3 Basic Environment Requirements

- A modern Windows, macOS, or Linux environment.
- Node.js and npm versions compatible with the project's Vite version.
- A modern web browser.
- PHP 8 or later only when running the optional PHP example.
- No separate database server is required for the local SQLite database.

## 4. System Design and Methodology

### 4.1 Architecture

```text
Browser
  React pages and shared components
       | HTTP requests (Axios) and live connection (Socket.IO Client)
       v
  Node.js / Express API and Socket.IO server
       |
       v
  SQLite database file

Optional, separate process:
  PHP built-in web server -> PHP demonstration endpoints
```

The PHP process is optional and separate. The React application uses the Node.js API for its main workflows.

### 4.2 Main Components

- **Frontend:** React pages, reusable components, route protection, authentication context, and API client.
- **Backend:** Express routes, controllers, authentication middleware, business rules, and Socket.IO handlers.
- **Database:** SQLite tables initialized by the backend and stored in `database/workway.sqlite`.
- **PHP demonstration:** A standalone PHP entry point that returns JSON for a small set of routes.

### 4.3 Booking Workflow

1. The customer selects a service and submits the job details.
2. The API verifies required fields and checks that the selected service is active.
3. The backend saves the booking and its initial status history in SQLite.
4. The backend routes the booking to the service department and creates relevant notifications.
5. A department head assigns an available worker.
6. The worker accepts or rejects the assignment and updates progress as appropriate.
7. The customer follows the status timeline and receives updates.
8. After work completion, the payment and feedback steps can be recorded and the booking can be closed.

### 4.4 Development Method

The system is organized around user roles and the lifecycle of a service booking. Frontend pages call backend API routes; route middleware checks identity and access, controllers apply workflow rules, and the database stores resulting records. The interface and server are run as separate development processes, with the root start script launching both.

## 5. Technology Stack

| Layer | Technology | Simple explanation |
|---|---|---|
| User interface | React | Builds the interactive pages from reusable interface components. |
| Page navigation | React Router | Displays the correct page for a URL and supports protected role-based routes. |
| Frontend development server | Vite | Runs the frontend during development and builds it for distribution. |
| API client | Axios | Sends HTTP requests from the React pages to the backend API. |
| Main server | Node.js and Express | Runs the main API and handles application requests. |
| Authentication | JSON Web Token (JWT), bcryptjs | Supports authenticated requests and password-hash operations in the main backend. |
| Real-time communication | Socket.IO and Socket.IO Client | Sends booking and notification updates to connected clients. |
| Database | SQLite and sqlite3 | Stores application records in a local relational database file. |
| Optional example service | PHP | Demonstrates a separate JSON health, status, and example login API. |

### React in Simple Terms

React is the JavaScript library used to build what the user sees in the browser. A page such as the booking form is made from components. Components can keep shared elements—such as the navigation bar, status badge, or notification display—consistent across the application. React Router connects URLs to pages, and protected routes restrict parts of the interface by role. React is the frontend; it does not directly replace the backend or database.

### PHP in Simple Terms

PHP is a server-side language that can receive a web request and return a response. In this project, `php/index.php` is a small, separate example: it returns a project overview, a health-check response, and a demonstration login response. It is not the main API, does not power the React booking workflow, and does not use the SQLite database. Its example login logic and token are for demonstration only.

## 6. Implementation

### 6.1 Frontend Implementation

The frontend is in `frontend/src`. `App.jsx` connects public pages and role-protected routes. Pages are grouped by user role: customer, department head, worker, and administrator. Shared components provide navigation, status display, notifications, protected-route behavior, and messages. The API helper sends requests to the Node backend and attaches the session token when present.

### 6.2 Backend Implementation

The main backend is in `backend`. `server.js` configures Express, mounts API route groups, initializes Socket.IO, and starts listening after database initialization. Route modules connect URL paths to controller functions. Authentication middleware checks signed-in identity and role; controllers apply workflow logic and use the database helpers.

### 6.3 Database Implementation

The SQLite database is stored at `database/workway.sqlite`. Backend initialization creates or updates tables for core application records. The schema covers users and roles, departments, services, bookings, status history, payments, feedback, and notifications. The backend database module provides asynchronous query helpers.

### 6.4 PHP Implementation

The optional PHP implementation is concentrated in `php/index.php`. It reads the requested path and HTTP method, returns JSON responses, checks required fields for the example login request, and returns a not-found response for unsupported routes. `php/README.md` documents how to start the PHP built-in server.

## 7. Results and Discussion

The implemented project provides a connected set of role-oriented application screens and a main API for service booking workflows. The source includes booking creation and department routing, worker assignment workflows, status history, payment and feedback areas, administrative pages, and Socket.IO integration. The local SQLite setup avoids the need to configure a separate database server for development.

The PHP code demonstrates basic PHP request handling alongside the main stack, but it does not integrate with the operational workflow. The project is suitable for local demonstration and further development. Production use would require deployment configuration, security hardening, monitoring, backups, and a real payment-provider integration.

## 8. Challenges and Issues Faced

- **Coordinating multiple roles:** Each role has distinct screens and permissions; routes and APIs must agree on role names and allowed actions.
- **Maintaining a valid booking lifecycle:** Status transitions need to be controlled so a booking cannot jump arbitrarily through the workflow.
- **Keeping updates timely:** Customers, workers, and department heads need current information; real-time delivery depends on the Socket.IO connection.
- **Managing related data:** A booking relies on correctly linked customer, service, department, worker, and history records.
- **Running multiple services locally:** The main application uses separate frontend and backend processes, while PHP is a separate optional process.
- **Avoiding confusion between PHP and the main API:** The PHP endpoint is a demonstration and must be clearly distinguished from the Node.js application backend.
- **Preparing for production:** The demo payment flow, PHP login sample, local database, and development server settings require production-specific replacements or configuration before deployment.

## 9. Testing

The following checks are appropriate for validating the implemented workflows. They are presented as a manual test checklist, not as a claim that a dedicated automated test suite has been run.

| Test area | Example check | Expected result |
|---|---|---|
| Application startup | Start the frontend and backend using the instructions below. | Frontend and API start on their configured local ports; database initialization completes. |
| API health | Request `GET /api/health` from the backend. | The API returns a successful health response. |
| Sign-in and access | Sign in and attempt to open pages for allowed and disallowed roles. | Users reach their role area; protected pages are restricted to permitted roles. |
| Booking validation | Submit a booking with required information, then try omitting a required field. | Valid details can create a booking; incomplete details receive a validation response. |
| Department workflow | Create a booking and review it as its department head. | The request is routed to the service department and is available for assignment. |
| Worker workflow | Assign a worker and update the assigned job through supported statuses. | The booking state and history reflect the valid workflow. |
| Customer tracking | Open the booking tracking view and observe a status update. | The customer sees the latest status; real-time updates work while connected. |
| Payment and feedback | Complete the available project payment flow and submit feedback. | The related records and booking state are reflected in the application. |
| PHP endpoints | Run the optional PHP server and request `/`, `/api/health`, and `/api/login`. | The PHP service returns the documented JSON response for each supported request. |

No separate test directory or automated test command is listed in the provided project structure. For a formal release, add automated tests for API validation, role permissions, booking status transitions, and frontend user flows.

## 10. Appendix A — React Frontend Files

| Important file | Purpose |
|---|---|
| `frontend/src/main.jsx` | Frontend entry point; mounts the React application. |
| `frontend/src/App.jsx` | Connects routes, providers, shared layout, and role-protected pages. |
| `frontend/src/context/AuthContext.jsx` | Shares authentication state and role-related helpers across the interface. |
| `frontend/src/context/SocketContext.jsx` | Provides the frontend Socket.IO connection context. |
| `frontend/src/services/api.js` | Configures the Axios client for the backend API. |
| `frontend/src/components/ProtectedRoute.jsx` | Restricts frontend routes according to authentication and allowed roles. |
| `frontend/src/pages/user/CreateBooking.jsx` | Presents the customer booking form. |
| `frontend/src/pages/user/LiveTracking.jsx` | Displays booking progress to the customer. |
| `frontend/src/pages/head/HeadRequests.jsx` | Supports department-head request review. |
| `frontend/src/pages/worker/CurrentWork.jsx` | Supports worker work-progress actions. |
| `frontend/src/pages/admin/AdminDashboard.jsx` | Provides an administrative overview. |

### Sample Code — `frontend/src/main.jsx`

This entry file mounts the React application into the HTML page and enables React's development checks.

```jsx
import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import './styles/index.css'

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
)
```

### Sample Code — `frontend/src/components/ProtectedRoute.jsx`

This excerpt shows how the frontend redirects signed-out users to login and checks whether a user's role is allowed to see a page.

```jsx
if (!isAuthenticated || !user) {
  return <Navigate to="/login" replace />;
}

if (allowedRoles && allowedRoles.length > 0) {
  const isAllowed = allowedRoles.includes(user.role) ||
    ((allowedRoles.includes('DEPARTMENT_HEAD') || allowedRoles.includes('HR')) &&
      (user.role === 'DEPARTMENT_HEAD' || user.role === 'HR'));
  if (!isAllowed) {
    return <Navigate to={getDashboardRoute(user.role)} replace />;
  }
}

return children;
```

## 11. Appendix B — Node.js Backend Files

| Important file | Purpose |
|---|---|
| `backend/server.js` | Configures Express, mounts routes, initializes Socket.IO, and starts the server. |
| `backend/routes/authRoutes.js` | Defines authentication API routes. |
| `backend/routes/bookingRoutes.js` | Defines booking endpoints and applies authentication and role checks. |
| `backend/controllers/authController.js` | Implements main-backend authentication operations. |
| `backend/controllers/bookingController.js` | Implements booking creation, workflow transitions, and booking-related operations. |
| `backend/middleware/authMiddleware.js` | Checks authentication and role requirements for protected API routes. |
| `backend/utils/dbInit.js` | Initializes the database schema and seed data. |
| `backend/config/database.js` | Opens the SQLite database and provides query helpers. |

### Sample Code — `backend/routes/bookingRoutes.js`

The route applies authentication and restricts booking creation to users with the `USER` role.

```js
router.post(
  '/',
  authenticate,
  requireRole('USER'),
  bookingController.createBooking
);
```

### Sample Code — `backend/controllers/bookingController.js`

This excerpt validates required booking fields before processing the request.

```js
const { service_id, work_description, location, booking_date, booking_time } = req.body;

if (!service_id || !work_description || !location || !booking_date || !booking_time) {
  return res.status(400).json({
    success: false,
    message: 'Required booking details are missing.'
  });
}
```

## 12. Appendix C — Database and Real-Time Files

| Important file | Purpose |
|---|---|
| `database/workway.sqlite` | Local SQLite database file used by the main backend. |
| `backend/utils/dbInit.js` | Creates or updates the application's tables. |
| `backend/config/database.js` | Defines the path to the database file and database access helpers. |
| `backend/sockets/socketHandler.js` | Configures Socket.IO behavior and booking-update delivery. |
| `frontend/src/context/SocketContext.jsx` | Makes the frontend real-time connection available to pages. |
| `frontend/src/pages/user/LiveTracking.jsx` | Uses booking status information to show customer progress. |

The database file may contain local development data. Back it up before replacing or reinitializing it, and do not treat a sample local database as a production backup strategy.

### Sample Code — `backend/config/database.js`

The database helper wraps SQLite reads in a Promise so backend controllers can use `async`/`await`.

```js
const get = (sql, params = []) => {
  return new Promise((resolve, reject) => {
    db.get(sql, params, (err, row) => {
      if (err) reject(err);
      else resolve(row);
    });
  });
};
```

### Sample Code — `backend/sockets/socketHandler.js`

The server sends a booking update to the room for that booking when a booking code is available.

```js
if (bookingCode) {
  ioInstance
    .to(`booking_${bookingCode}`)
    .emit('bookingStatusUpdated', payload);
}
```

## 13. Appendix D — PHP and Project Operation Files

| Important file | Purpose |
|---|---|
| `php/index.php` | Implements the separate PHP JSON overview, health, and example login endpoints. |
| `php/README.md` | Documents how to run the optional PHP server and lists its endpoints. |
| `php/composer.json` | Defines PHP project metadata; the current PHP example has no Composer dependencies. |
| `package.json` | Provides root scripts, including the combined development start command and optional PHP script. |
| `start.js` | Starts the backend and Vite frontend development processes. |
| `backend/package.json` | Lists backend scripts and Node.js dependencies. |
| `frontend/package.json` | Lists frontend scripts and React/Vite dependencies. |

### Sample Code — `php/index.php`

The PHP health endpoint returns a JSON response with the service status and current timestamp.

```php
if ($requestUri === '/api/health' && $method === 'GET') {
    jsonResponse(200, [
        'status' => 'ok',
        'service' => 'workway-php',
        'language' => 'PHP',
        'timestamp' => gmdate('c')
    ]);
}
```

### Sample Code — `start.js`

The root start script launches the backend and frontend development processes separately.

```js
const backend = spawn('npm', ['start'], {
  cwd: path.join(__dirname, 'backend'),
  stdio: 'inherit',
  shell: true
});

const frontend = spawn('npm', ['run', 'dev'], {
  cwd: path.join(__dirname, 'frontend'),
  stdio: 'inherit',
  shell: true
});
```

## 14. Conclusion and Future Scope

WORKWAY demonstrates a full service-request path: customers book a service, department heads assign workers, workers update progress, and customers can follow completion, payment, and feedback steps. React provides the browser interface, Node.js and Express implement the primary application API, SQLite stores local application data, and Socket.IO supports live updates. PHP is included as a separate example service rather than as part of the main workflow.

Future work could include:

- Integrating a secure payment provider and verified transaction callbacks.
- Adding automated unit, API, and end-to-end tests.
- Improving operational logging, error reporting, and monitoring.
- Moving production data to a managed database with backup and migration procedures.
- Providing configurable deployment environments and secure secret management.
- Improving accessibility, localization, and mobile usability.
- Replacing the PHP demo login with a properly designed service only if PHP authentication becomes a real requirement.

## 15. References

1. React documentation, [https://react.dev/](https://react.dev/).
2. React Router documentation, [https://reactrouter.com/](https://reactrouter.com/).
3. Vite documentation, [https://vite.dev/](https://vite.dev/).
4. Node.js documentation, [https://nodejs.org/docs/](https://nodejs.org/docs/).
5. Express documentation, [https://expressjs.com/](https://expressjs.com/).
6. Socket.IO documentation, [https://socket.io/docs/v4/](https://socket.io/docs/v4/).
7. SQLite documentation, [https://www.sqlite.org/docs.html](https://www.sqlite.org/docs.html).
8. PHP documentation, [https://www.php.net/docs.php](https://www.php.net/docs.php).
9. WORKWAY source files and included project README files.

## 16. System Usage / Use Case Description

### Main Use Case: Book and Complete a Service

**Primary actor:** Customer  
**Supporting actors:** Department head, worker, administrator

**Preconditions:** The application services are running, the customer can sign in, and the requested service is available.

**Main flow:**

1. The customer signs in and selects a service.
2. The customer enters a work description, location, date, and time, then submits the request.
3. The system validates the request, saves it, and routes it to the relevant department.
4. The department head reviews the request and assigns a worker.
5. The worker accepts the assignment and updates the work status.
6. The customer follows the booking status and receives available notifications.
7. After work completion, the customer follows the payment and feedback steps.
8. The system retains the booking and related status, payment, and feedback information.

**Alternative flow:** If a worker rejects an assignment, the department head can review and reassign the request where the workflow permits.

**Other use cases:** A worker reviews assignments and work history; a department head reviews requests and workers; an administrator oversees users, services, departments, bookings, payments, and feedback.

## 17. User Manual — Running the Application

### 17.1 Prerequisites

Install Node.js and npm. PHP is optional and is needed only for the PHP demonstration. Run the commands from the project root folder.

### 17.2 Install dependencies

```powershell
cd backend
npm install
cd ..\frontend
npm install
cd ..
```

### 17.3 Start the main application

```powershell
npm run dev
```

The root start script launches the backend and frontend development server. By default, the backend reports its API at `http://localhost:5000/api`, and Vite typically serves the frontend at `http://localhost:5173`. Open the frontend URL in a browser. The backend initializes the SQLite database when it starts.

If needed, the services can be started separately in two terminals:

```powershell
cd backend
npm start
```

```powershell
cd frontend
npm run dev
```

### 17.4 Run the optional PHP example

Ensure PHP is installed and available as `php` on the command line, then from the project root run:

```powershell
php -S 127.0.0.1:8001 -t php
```

Try `http://127.0.0.1:8001/` or `http://127.0.0.1:8001/api/health` in a browser. To call the example login endpoint, send a JSON `POST` request to `http://127.0.0.1:8001/api/login` with `username` and `password` fields. The PHP service is separate from the main application; its example login is not for real accounts.

### 17.5 Stop the services

Stop each running development process with `Ctrl+C` in its terminal. Keep the `database/workway.sqlite` file if you want to retain local application data.

## 18. Glossary of Terms

| Term | Meaning |
|---|---|
| API | A defined interface through which software components exchange requests and responses. |
| Booking | A customer's request for a particular service. |
| Component | A reusable piece of a React user interface. |
| Controller | Backend code that handles a request and applies application behavior. |
| Department head | A role that reviews department requests and coordinates workers. |
| Endpoint | A URL and HTTP method that provides a specific API operation. |
| Express | A Node.js web framework used to build the main API. |
| Frontend | The part of the application displayed and used in the browser. |
| JWT | JSON Web Token; a token format used to represent authenticated identity/claims. |
| Middleware | Backend code that runs during request handling, often for authentication or access checks. |
| PHP | A server-side programming language; in this project, used for a separate demonstration endpoint. |
| React | A JavaScript library used to build the application's user interface. |
| REST | A common style for designing network APIs around resources and HTTP methods. |
| SQLite | A relational database stored in a file rather than requiring a separate database server. |
| Socket.IO | A library for event-based communication between a server and connected clients. |
| Status history | The recorded sequence of changes made to a booking's state. |
| Vite | A frontend development server and build tool. |

## 19. SDG Mapping

The project has an **indirect, potential alignment** with the following United Nations Sustainable Development Goals. This mapping describes the project's intended contribution; it does not claim certification or measured social impact.

| SDG | Connection to WORKWAY |
|---|---|
| **SDG 8 — Decent Work and Economic Growth** | The platform organizes service requests and worker assignments, which may help coordinate access to service work. The project does not independently guarantee employment quality or worker protections. |
| **SDG 9 — Industry, Innovation and Infrastructure** | WORKWAY demonstrates a digital service-management workflow using web technologies, APIs, a database, and real-time communication. |
| **SDG 11 — Sustainable Cities and Communities** | Easier access to organized local household services may support day-to-day community needs. This is a potential use, not a measured outcome. |
