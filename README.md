# NANDSKILLS — Enterprise SaaS Multi-Tenant Platform

NANDSKILLS is a secure, production-ready, highly scalable, multi-tenant SaaS application built to integrate all aspects of academic institutions and learning companies. It functions as a complete **LMS, CMS, CRM, Student Information System (SIS), Education ERP, HR & Payroll System, Library & Inventory Tracker, Support Desk, and White-Label engine**.

---

## 🚀 Key Modules & System Features

### 1. LMS (Learning Management System)
*   **Structured Course Builder**: Courses supporting modules/chapters, lessons, version controls, and version-parenting.
*   **Media Types**: Supports HTML, video files, audio files, PDFs, and SCORM metadata.
*   **Student Portals & Certifications**: Course progression charts, assignment submission uploads, quiz attempts with scores, and certificate code verification tools.
*   **Student Course Catalog & Self-Enrollment**: Browse page for students to search and self-enroll in published courses, allowing immediate access to class materials and live meetings.
*   **Virtual Classroom (Premium WebRTC)**: Scheduling tool for trainers with live, responsive video sessions.
    *   **Immersive Interface**: A distraction-free full-page route `/meeting/{meetingId}/session` utilizing a premium dark dashboard layout.
    *   **Collaboration Tools**:
        *   **Autosaving Notebook**: Notes are autosaved in the client's local storage and can be downloaded as a `.txt` file.
        *   **Shared Interactive Whiteboard**: Real-time synced drawing canvas. Pencil colors/widths and clear options are restricted to trainers/hosts; students view changes in read-only mode.
    *   **Camera Customizations**: Client-side video filters (Normal, Blur Background, Grayscale, Retro Sepia, Warm Sunlight, Cool Night, Vivid HDR).
    *   **Layout Options**: Toggle fullscreen on video grids and pop out individual feeds using the native browser Picture-in-Picture (PiP) API.
    *   **Signal Glare Mitigation**: Initiator logic prevents double offer collisions during WebRTC connection negotiations.

### 2. CMS (Content Management System)
*   **Global Layout Configurator**: notices ticker, page hero adjustments, custom layout headers.
*   **Page Builder**: Allows tenant administrators to add, order, and configure custom page templates.
*   **Landing Page**: Dynamically renders layout configurations based on subdomains/domains.
*   **Role Protection**: Strictly gated to ensure only authorized administrators (Super Admin, Tenant Admin) can build pages.

### 3. CRM (Customer Relationship Management)
*   **Visual Sales Kanban**: Track leads through customizable pipelines (New, Working, Closed).
*   **Lead Activity Logger**: Record client touchpoints, deal values, and source channels.

### 4. Student Information System (SIS)
*   **Rosters**: Student profiles with unique roll numbers, admission details, and statuses.
*   **Attendance Tracking**: Interactive manual check sheets, bulk check-ins, and 30-minute self-check-in verification codes.
*   **Auto-Attendance Marking**: When a student joins a live WebRTC virtual classroom, their attendance is automatically marked as `PRESENT` under the main branch for the current day.
*   **Student Onboarding**: Registration forms automatically create corresponding `StudentProfile` records, initializing tenant-scoped roll numbers (e.g., `NS-2026-XXXX`) and admission records to prevent orphan accounts.
*   **Family Map**: Link students to parental profiles for centralized monitoring.

### 5. Enterprise console & ERP Modules
Accessible via `/admin/console` with active tab states preserved in the URL query string (`?activeTab=...`).

*   **Advanced RBAC System**: Configure custom tenant-scoped roles and descriptions, audit user logins, and approve/reject staff access requests.
*   **Multi-Campus Management**: Create and link branches, buildings, departments, and classrooms/rooms with custom capacity bounds.
*   **Examination System**: Schedule exams for sessions/courses and record student marks. Grade and GPA are automatically computed (Pass/Fail at 50% limit) using custom `GradeScale` mapping rules or standard academic fallback scales.
*   **HR & Payroll System**:
    *   File employee leave requests (Casual, Sick, Annual).
    *   Approve or reject leaves in real-time from the interactive **Leave Requests Board**.
    *   Process monthly employee payroll releases.
*   **Inventory & Asset Tracker**: Register school property, track physical item statuses, and log device checkouts to specific users.
*   **Library Management**: Catalog books, log active student book loans with auto due-dates, track copy inventory decrement upon lending, and restore counts when marked returned.
*   **Accreditation & Compliance**: Configure accreditation frameworks and auditing agencies, and manage compliance criteria checklists with interactive Met/Unmet toggle controls.
*   **Gamification Engine**: Configure customized reward badges (name, description, XP threshold) and award XP points to users for academic participation.
*   **Subscription & Billing**: Manage active tenant limits (user caps, course caps, storage bytes) and view invoice details.

---

## 🛠️ Technology Stack & Architecture

*   **Language & Core**: PHP 8.2+ / Laravel 12 (latest modern enterprise routing, containers, and database layers)
*   **Frontend UI**: Livewire 3 (reactive SPA-like experience with no API lag) & Tailwind CSS (Custom themed clean dashboards)
*   **Datastore Layer**: MySQL / PostgreSQL (Local environment runs directly on MySQL port 3306)
*   **Scoping Engine**: All models utilize the custom `HasTenantScope` trait and `IdentifyTenant` middleware to separate data boundaries.
*   **Security & Validation**: 
    *   Multi-tenant scoping checks for unique email addresses (`Rule::unique('users', 'email')->where('tenant_id', $tenantId)`) prevent DB unique key constraints.
    *   Protected controller gates prevent students or unauthorized users from executing administrative actions.

---

## 🔑 Default Login Credentials (Seed Data)

Use these accounts to access the portals via the dashboard [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login):

| Role | Email | Password | Access Details |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@nandskills.com` | `superadmin123` | Global SaaS limits, plans, and tenant list. |
| **Tenant Admin**| `admin@nandskills.com` | `admin123` | Complete control over LMS, CMS, ERP, CRM. |
| **Trainer** | `trainer@nandskills.com` | `trainer123` | Classroom player, manual attendance logs, scheduling. |
| **Student** | `student@nandskills.com` | `student123` | Enrolled courses, self check-in attendance, certificates. |
| **Parent** | `parent@nandskills.com` | `parent123` | Children monitoring, tuition invoices. |

---

## 💻 Local Setup & Execution

### Prerequisites
*   PHP 8.2+ installed locally.
*   MySQL service running on port 3306.

### Run commands
1.  **Configure environment**: Make sure `.env` specifies your local database:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nandskills_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```
2.  **Migrate & Seed**: Run database schema creation and seed the credentials above:
    ```bash
    php artisan migrate:fresh --seed
    ```
3.  **Compile Assets**: Compile Tailwind styles and frontend components:
    ```bash
    npm run build
    ```
4.  **Start Dev Server**: Launch Laravel development server:
    ```bash
    php artisan serve --host=127.0.0.1 --port=8000
    ```

---

## 🧪 Automated Testing
Run the feature tests validating tenant separation rules, virtual meeting isolation, employee payroll, asset checks, book loans, and student attendance check-ins:
```bash
php artisan test
```
