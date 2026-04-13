# Project Progress Report: Smart Food Redistribution Platform

**Course:** CSE3292 - Software Development III: Web Programming  
**Date of Submission:** 12 April 2026  
**Group ID:** [Add Group ID if applicable]  
**Group Members:**  
- [Student Name 1] - [Student ID]  
- [Student Name 2] - [Student ID]  
- [Student Name 3] - [Student ID]  

## 1. Project Overview and Current Status

### 1.1. Project Summary

**Goal:**  
Smart Food is a web-based food redistribution platform designed to reduce food waste by connecting food donors such as restaurants or stores with receiver organizations such as NGOs and charities. The system allows donors to post surplus food, receivers to claim it, and administrators to monitor, moderate, and manage platform activity.

**Technologies Used:**  
- Laravel 12
- PHP 8.2
- MySQL
- Blade templating engine
- Tailwind CSS
- Vite
- PHPUnit 11

**Current Completion Status:**  
Approximately **90% complete**.

**High-Level Status Statement:**  
All core user-facing workflows are functional, including registration, login, donation posting, claiming, completion, notifications, donor archive/relist flow, and admin moderation. The application is currently in the polishing and production-readiness phase, with pagination, queue-based background processing, and deployment hardening remaining as the main next steps.

### 1.2. Progress Against Schedule

**Status:**  
On Track

**Reason for Deviation:**  
The project experienced a few technical delays related to environment configuration, Windows-specific view compilation issues during testing, and authentication compatibility for legacy password hashes. These issues were resolved successfully, and the project progressed faster afterward because the architecture became more stable and easier to extend.

## 2. Detailed Development Progress

### 2.1. Backend and Database Implementation

| Component | Description of Functionality | Status |
|---|---|---|
| Database Schema | Designed and implemented core tables for users, donations, notifications, moderation metadata, and reports. | Complete |
| Authentication Module | Session-based authentication with registration, login, logout, profile update, password reset, and role-aware behavior. | Complete |
| Donation Management | Donors can create, edit, delete, archive, relist, and mark pickups as completed. | Complete |
| Claim Flow | Receivers can browse active listings and claim available donations. | Complete |
| Notification System | Users receive database notifications for key activities such as claims and pickup completion. | Complete |
| Admin Moderation | Admin can hide and restore listings, suspend and restore users, and resolve reports. | Complete |
| Reporting System | Users can report suspicious listings or users for admin review. | Complete |

**Database Schema Summary:**  
The main entities currently implemented are:
- `users`: stores donor, receiver, and admin accounts with role and suspension metadata
- `donations`: stores donation details, claim state, image path, archive-related status, and moderation fields
- `notifications`: stores database notifications for claim and pickup activity
- `reports`: stores user-submitted reports against donations or user accounts

**Simple Schema Snippet:**  
- `users (id, name, email, password, role, organization_name, suspended_at, suspension_reason)`
- `donations (id, food_category, quantity, quantity_kg, expiry_time, location, status, is_hidden, donor_id, receiver_id, moderated_by, image_path, picked_up_at, moderation_reason)`
- `reports (id, type, status, reporter_id, donation_id, reported_user_id, reason, resolved_by, resolved_at, admin_notes)`

**Major Implemented Routes / Endpoints:**  
- `POST /register`
- `POST /login`
- `POST /donate`
- `POST /marketplace/{donation}/claim`
- `POST /marketplace/{donation}/complete`
- `POST /donations/{donation}/relist`
- `POST /reports/donations/{donation}`
- `POST /admin/donations/{donation}/hide`

**Authentication and Authorization:**  
The project uses Laravel's session-based authentication system. Authorization rules for donation actions are enforced using a dedicated `DonationPolicy`, ensuring that only appropriate roles can create, claim, edit, delete, complete, or relist donations. Additional admin-only access control is applied for moderation and reporting actions.

**Key Feature Implemented This Period:**  
The most significant backend achievement during this phase is the **admin moderation and reporting workflow**, where users can report suspicious donations or accounts, and administrators can review, hide, restore, suspend, or resolve issues through the admin dashboard.

### 2.2. Frontend Implementation

| Component | Description of UI/UX | Integration Status |
|---|---|---|
| Login/Registration Pages | Fully styled authentication forms with validation feedback and role-based registration fields. | Yes |
| Main Dashboard | Donor and receiver dashboards showing activity summaries, listings, claims, and notifications. | Yes |
| Donation Creation/Edit Form | Form to create and update donation details, including optional image upload. | Yes |
| Marketplace | Displays available food listings with category filters, claim actions, and report forms. | Yes |
| Notification Center | Allows users to filter notifications and mark them as read. | Yes |
| Admin Dashboard | Displays platform statistics, moderation controls, report queue, and recent activity. | Yes |
| Navigation/Layout | Responsive navigation with role-aware links for dashboard, notifications, and admin tools. | Complete |

**UI Status:**  
The frontend is implemented using Blade templates, Tailwind CSS, and Vite. The interface is responsive, role-aware, and visually consistent across login, dashboard, marketplace, archive, notification, and admin pages. Recent UI work also improved mobile menu behavior, fixed text encoding issues, and polished authentication placeholders and footer content.

**Screenshot Placeholder:**  
Insert screenshots of:
- Login page
- Marketplace page
- Donor dashboard
- Admin moderation dashboard

## 3. Verification and Testing

### 3.1. Testing Summary

**Testing Framework:**  
PHPUnit 11

**Unit / Feature Testing Coverage:**  
Key application flows have been covered through Laravel feature tests. The current suite validates:
- registration and login
- profile updates
- donation claim flow
- donor edit/archive/relist flow
- image replacement behavior
- notification read flow
- admin moderation actions
- report submission and resolution flow

**Integration Testing:**  
The front-end and back-end have been tested together through full request-response cycles using Laravel feature tests, validating database writes, redirects, authorization rules, and rendered pages.

**Current Test Result:**  
Latest full test execution result:
- **40 tests passed**
- **120 assertions**

**Example Successful End-to-End Test Case:**  
**Scenario:** User reports a suspicious donation and the admin resolves it.  
**Steps:**  
1. A receiver logs into the platform.  
2. The receiver submits a report against a suspicious donation from the marketplace.  
3. The report is inserted into the database as an open report.  
4. An admin opens the admin dashboard and reviews the report queue.  
5. The admin resolves the report with an optional note.  
**Result:** The report status changes to `resolved`, the resolver is stored, and the system confirms the action successfully.

### 3.2. Identified Issues/Bugs

**Major Issue 1:**  
The application originally had a MySQL configuration inconsistency caused by cached or duplicate environment values.  
**Status:** Resolved

**Major Issue 2:**  
Windows file locking caused Blade compiled view rename failures during automated tests.  
**Status:** Resolved

**Major Issue 3:**  
Some legacy password hashes triggered runtime authentication errors during login.  
**Status:** Resolved

**Major Issue 4:**  
Listings and donors previously lacked a user-driven moderation/reporting mechanism.  
**Status:** Resolved

## 4. Technical Challenges and Solutions

### 4.1. Challenge 1: Environment and Database Configuration Stability

**Description:**  
During development, the application showed inconsistent database behavior because the environment file contained conflicting database connection values and the configuration needed to be fully aligned with MySQL.

**Solution Implemented:**  
The environment settings were cleaned, the application was standardized to run on MySQL only, migrations were rebuilt, seeders were verified, and configuration/cache clearing commands were applied. This ensured predictable database behavior across development and testing.

### 4.2. Challenge 2: Windows Blade View Compilation Lock Issue

**Description:**  
Automated tests failed on Windows because Blade's compiled view files sometimes could not be renamed due to file lock behavior in the local environment.

**Solution Implemented:**  
A Windows-safe filesystem fallback was introduced for the testing environment. This prevented transient rename failures and allowed the PHPUnit suite to run consistently on Windows without breaking the application logic.

### 4.3. Challenge 3: Secure Role-Based Moderation and Authorization

**Description:**  
As the project expanded, simple controller-only checks became harder to maintain and could potentially create authorization gaps for actions such as claim, edit, delete, complete, and relist.

**Solution Implemented:**  
A dedicated `DonationPolicy` was introduced to centralize authorization logic. This made the code safer, easier to maintain, and more extensible. Additional moderation fields and report resolution workflows were also added so the admin panel can actively manage platform abuse.

## 5. Work Plan and Next Steps

### 5.1. Remaining Tasks

The top priority tasks remaining before final submission are:

1. Implement pagination for marketplace, admin dashboard, notifications, and dashboard activity tables.
2. Add queue and scheduler support for automated notification processing and expiry-related background tasks.
3. Integrate email notifications for claim, completion, suspension, and reporting events.
4. Finalize production-readiness items such as deployment checklist, storage configuration, and permission review.
5. Perform final UI polishing, mobile fine-tuning, and documentation improvement.

### 5.2. Proposed Timeline

| Task Group | Estimated Completion Date |
|---|---|
| Pagination and UI Refinement | 15 April 2026 |
| Background Jobs, Email, and Final Bug Fixing | 18 April 2026 |
| Final Documentation, Screenshots, and Deployment Preparation | 20 April 2026 |

## Appendix Suggestions

For the final submitted PDF version, it is recommended to include:
- screenshots of the login page, marketplace, donor dashboard, notification center, and admin dashboard
- a simple ERD or schema image
- selected code snippets for `DonationPolicy`, admin moderation actions, and report flow
- latest successful test summary screenshot

## Final Status Statement

The Smart Food Redistribution Platform is now a stable and feature-rich Laravel web application with working authentication, donation lifecycle management, notification handling, report submission, and admin moderation capabilities. The current state is appropriate for a strong progress evaluation, and the remaining tasks are primarily related to optimization, deployment readiness, and final polish rather than missing core functionality.
