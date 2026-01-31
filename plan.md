# CRM Homland - Development Plan
## CTO Perspective | Phase-wise Development Strategy

---

## 📋 Technology Stack

### Backend
- **Framework**: Laravel 11.x
- **Database**: MySQL 8.0+
- **API**: RESTful API with **JWT Authentication** (aligned with current API implementation)
- **Storage**: Local + Google Drive API (for backups)
- **Queue**: Laravel Queue (Redis/Database driver)
- **Cache**: Redis
- **API Versioning**: `/api/v1/...` from day 1

### Frontend (Web)
- **Framework**: Laravel Blade + Livewire OR Vue.js 3
- **CSS Framework**: Tailwind CSS 3.x
- **UI Components**: Headless UI / Shadcn UI
- **Mobile-First**: Responsive design approach

### Mobile App
- **Framework**: Quasar Framework (Vue.js based)
- **Platform**: Android (Primary), iOS (Future)
- **Build Tool**: Capacitor (for native features)
- **State Management**: Pinia
- **HTTP Client**: Axios

### Infrastructure & Services
- **Version Control**: Git
- **CI/CD**: GitHub Actions / GitLab CI
- **Backup Service**: Google Drive API
- **SMS Gateway**: Local SMS provider API
- **Speech-to-Text**: Google Speech-to-Text API / Local ML model
- **Call Detection**: Android TelephonyManager API

### Development Tools
- **Package Manager**: Composer (Backend), npm/yarn (Frontend)
- **Testing**: PHPUnit, Jest/Vitest
- **Code Quality**: PHP CS Fixer, ESLint, Prettier

---

## 🗄️ Database Schema & Migrations

### Database Design Principles
- **Infinite Hierarchy**: Using Adjacency List Model with recursive CTEs for unlimited depth
- **Hierarchy Performance**: Add **Materialized Path** columns (`path`, `depth`) to speed up tree queries + mobile sync (still keep `parent_id`)
- **Offline Support**: All tables include `sync_status` and `sync_at` for offline-first architecture
- **Soft Deletes**: Using Laravel's soft delete trait for data recovery
- **Audit Trail**: `created_by`, `updated_by`, `created_at`, `updated_at` on all tables
- **Indexing**: Strategic indexes on foreign keys and frequently queried fields
- **Security**: Device binding + admin revoke + optional app lock (PIN/Biometric) without forcing auto logout

---

### Core Tables

#### 1. `users` Table
**Purpose**: System users (CRM agents, admins)

```sql
- id (bigint, primary key, auto increment)
- name (string, 255)
- email (string, 255, unique)
- mobile (string, 20, unique, nullable)
- password (string, 255, hashed)
- role (enum: 'admin', 'employee', 'manager')
- is_active (boolean, default: true)
- remember_token (string, 100, nullable)
- email_verified_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `email`, `mobile`, `role`

---

#### 2. `personal_access_tokens` Table
**Purpose**: API authentication tokens (60-year expiry)

```sql
- id (bigint, primary key, auto increment)
- tokenable_type (string, 255)
- tokenable_id (bigint)
- name (string, 255)
- token (string, 64, unique)
- abilities (text, nullable)
- last_used_at (timestamp, nullable)
- expires_at (timestamp, nullable) // 60 years from creation
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `tokenable_type`, `tokenable_id`, `token`

---

#### 3. `categories` Table
**Purpose**: Unlimited depth category system (self-referencing)

```sql
- id (bigint, primary key, auto increment)
- parent_id (bigint, nullable, foreign key -> categories.id)
- name (string, 255)
- slug (string, 255, unique)
- path (string, 1000, index) // materialized path, e.g., "/1/15/80/"
- depth (unsigned_smallint, default: 0, index)
- description (text, nullable)
- icon (string, 100, nullable)
- sort_order (integer, default: 0)
- is_active (boolean, default: true)
- created_by (bigint, foreign key -> users.id)
- updated_by (bigint, foreign key -> users.id, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `parent_id`, `slug`, `is_active`, `created_by`

**Note**: Use recursive CTE for infinite depth queries

---

#### 4. `addresses` Table
**Purpose**: Infinite depth address hierarchy (self-referencing)

```sql
- id (bigint, primary key, auto increment)
- parent_id (bigint, nullable, foreign key -> addresses.id)
- name (string, 255) // e.g., "Dhaka", "Jatrabari", "Shohid Faruk Road"
- type (enum: 'country', 'division', 'district', 'upazila', 'union', 'village', 'area', 'road', 'other')
- path (string, 1000, index) // materialized path, e.g., "/bd/dhaka/jatrabari/..."
- depth (unsigned_smallint, default: 0, index)
- code (string, 50, nullable, unique) // Postal code or custom code
- latitude (decimal, 10, 8, nullable)
- longitude (decimal, 11, 8, nullable)
- is_active (boolean, default: true)
- sort_order (integer, default: 0)
- created_by (bigint, foreign key -> users.id)
- updated_by (bigint, foreign key -> users.id, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `parent_id`, `type`, `code`, `is_active`

**Note**: Supports infinite nesting (e.g., Dhaka > Jatrabari > Shohid Faruk Road > ...)

---

#### 5. `customer_groups` Table
**Purpose**: Customer grouping for segmentation

```sql
- id (bigint, primary key, auto increment)
- name (string, 255, unique)
- description (text, nullable)
- color (string, 7, nullable) // Hex color code
- is_active (boolean, default: true)
- created_by (bigint, foreign key -> users.id)
- updated_by (bigint, foreign key -> users.id, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `name`, `is_active`

---

#### 6. `interested_types` Table
**Purpose**: Types of customer interests (e.g., "buy next month", "area change")

```sql
- id (bigint, primary key, auto increment)
- name (string, 255, unique)
- description (text, nullable)
- color (string, 7, nullable)
- is_active (boolean, default: true)
- sort_order (integer, default: 0)
- created_by (bigint, foreign key -> users.id)
- updated_by (bigint, foreign key -> users.id, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `name`, `is_active`

---

#### 7. `professions` Table
**Purpose**: Customer profession types and details

```sql
- id (bigint, primary key, auto increment)
- type (string, 100) // e.g., "job", "business", "student", "housewife"
- business_type (string, 255, nullable) // If type is "business"
- job_title (string, 255, nullable) // If type is "job"
- company_name (string, 255, nullable) // If type is "job"
- description (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `type`

---

#### 8. `customers` Table
**Purpose**: Core customer information

```sql
- id (bigint, primary key, auto increment)
- name (string, 255)
- mobile (string, 20, unique, index)
- email (string, 255, nullable, index)
- alternative_mobile (string, 20, nullable)
- customer_group_id (bigint, nullable, foreign key -> customer_groups.id)
- profession_id (bigint, nullable, foreign key -> professions.id)
- current_address_id (bigint, nullable, foreign key -> addresses.id) // For address hierarchy
- current_address_text (text, nullable) // Free text current address
- nearest_market (string, 255, nullable)
- preferred_area (string, 255, nullable)
- target_real_estate (text, nullable) // JSON or text
- notes (text, nullable)
- is_active (boolean, default: true)
- created_by (bigint, foreign key -> users.id)
- updated_by (bigint, foreign key -> users.id, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `mobile`, `email`, `customer_group_id`, `profession_id`, `current_address_id`, `is_active`, `created_by`

---

#### 9. `projects` Table
**Purpose**: Real estate projects

```sql
- id (bigint, primary key, auto increment)
- name (string, 255)
- slug (string, 255, unique)
- description (text, nullable)
- address_id (bigint, nullable, foreign key -> addresses.id)
- address_text (text, nullable)
- project_type (enum: 'apartment', 'land', 'commercial', 'other', nullable)
- status (enum: 'planning', 'ongoing', 'completed', 'on_hold', default: 'ongoing')
- is_active (boolean, default: true)
- created_by (bigint, foreign key -> users.id)
- updated_by (bigint, foreign key -> users.id, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `slug`, `address_id`, `status`, `is_active`

---

#### 10. `leads` Table
**Purpose**: Main CRM lead/entry table (based on form structure)

```sql
- id (bigint, primary key, auto increment)
- lead_source (string, 255, nullable) // Where lead came from
- customer_id (bigint, foreign key -> customers.id)
- project_id (bigint, nullable, foreign key -> projects.id)
- customer_requirement (text, nullable)
- preferred_area (string, 255, nullable)
- next_contact_date (date, nullable)
- remarks (text, nullable)
- interested_type_id (bigint, nullable, foreign key -> interested_types.id)
- status (enum: 'new', 'contacted', 'qualified', 'converted', 'lost', default: 'new')
- priority (enum: 'low', 'medium', 'high', default: 'medium')
- sync_status (enum: 'synced', 'pending', 'failed', default: 'synced') // For offline
- sync_at (timestamp, nullable)
- created_by (bigint, foreign key -> users.id)
- updated_by (bigint, foreign key -> users.id, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `customer_id`, `project_id`, `interested_type_id`, `status`, `next_contact_date`, `sync_status`, `created_by`, `created_at`

---

#### 11. `pricing` Table
**Purpose**: Pricing information for leads

```sql
- id (bigint, primary key, auto increment)
- lead_id (bigint, foreign key -> leads.id, unique)
- quoted_price (decimal, 15, 2, nullable) // Price quoted to customer
- customer_budget (decimal, 15, 2, nullable) // Customer's budget
- agreeable_price (decimal, 15, 2, nullable) // Agreed price
- currency (string, 3, default: 'BDT')
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `lead_id`

---

#### 12. `customer_notes` Table
**Purpose**: Notes and remarks for customers/leads

```sql
- id (bigint, primary key, auto increment)
- customer_id (bigint, foreign key -> customers.id)
- lead_id (bigint, nullable, foreign key -> leads.id)
- note (text)
- note_type (enum: 'general', 'call', 'meeting', 'follow_up', default: 'general')
- is_important (boolean, default: false)
- sync_status (enum: 'synced', 'pending', 'failed', default: 'synced')
- sync_at (timestamp, nullable)
- created_by (bigint, foreign key -> users.id)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `customer_id`, `lead_id`, `note_type`, `created_by`, `created_at`, `sync_status`

---

#### 13. `follow_ups` Table
**Purpose**: Follow-up scheduling and tracking

```sql
- id (bigint, primary key, auto increment)
- lead_id (bigint, foreign key -> leads.id)
- customer_id (bigint, foreign key -> customers.id)
- scheduled_date (date)
- scheduled_time (time, nullable)
- follow_up_type (enum: 'call', 'visit', 'meeting', 'email', 'sms', default: 'call')
- status (enum: 'pending', 'completed', 'cancelled', 'rescheduled', default: 'pending')
- notes (text, nullable)
- completed_at (timestamp, nullable)
- reminder_sent (boolean, default: false)
- sync_status (enum: 'synced', 'pending', 'failed', default: 'synced')
- sync_at (timestamp, nullable)
- created_by (bigint, foreign key -> users.id)
- updated_by (bigint, foreign key -> users.id, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `lead_id`, `customer_id`, `scheduled_date`, `status`, `sync_status`, `created_by`

---

#### 14. `sms_messages` Table
**Purpose**: SMS sending and history

```sql
- id (bigint, primary key, auto increment)
- customer_id (bigint, nullable, foreign key -> customers.id)
- lead_id (bigint, nullable, foreign key -> leads.id)
- recipient_mobile (string, 20)
- message (text)
- template_id (bigint, nullable, foreign key -> sms_templates.id)
- status (enum: 'pending', 'sent', 'failed', 'delivered', default: 'pending')
- provider_response (text, nullable) // Response from SMS gateway
- sent_at (timestamp, nullable)
- delivered_at (timestamp, nullable)
- scheduled_at (timestamp, nullable) // For scheduled SMS
- sync_status (enum: 'synced', 'pending', 'failed', default: 'synced')
- sync_at (timestamp, nullable)
- created_by (bigint, foreign key -> users.id)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `customer_id`, `lead_id`, `recipient_mobile`, `status`, `sent_at`, `scheduled_at`, `sync_status`

---

#### 15. `sms_templates` Table
**Purpose**: Reusable SMS templates

```sql
- id (bigint, primary key, auto increment)
- name (string, 255)
- message (text)
- variables (text, nullable) // JSON array of available variables
- is_active (boolean, default: true)
- created_by (bigint, foreign key -> users.id)
- updated_by (bigint, foreign key -> users.id, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `name`, `is_active`

---

#### 16. `call_logs` Table
**Purpose**: Incoming/outgoing call tracking

```sql
- id (bigint, primary key, auto increment)
- customer_id (bigint, nullable, foreign key -> customers.id)
- lead_id (bigint, nullable, foreign key -> leads.id)
- phone_number (string, 20, index)
- call_type (enum: 'incoming', 'outgoing', 'missed', default: 'incoming')
- call_status (enum: 'ringing', 'answered', 'ended', 'missed', 'rejected', default: 'ringing')
- duration_seconds (integer, nullable)
- started_at (timestamp)
- ended_at (timestamp, nullable)
- recording_path (string, 500, nullable) // Encrypted path to recording (if enabled + consented)
- is_recorded (boolean, default: false)
- recording_consent_status (enum: 'unknown', 'granted', 'denied', default: 'unknown')
- recording_consent_at (timestamp, nullable)
- auto_opened_app (boolean, default: false)
- sync_status (enum: 'synced', 'pending', 'failed', default: 'synced')
- sync_at (timestamp, nullable)
- created_by (bigint, nullable, foreign key -> users.id) // Nullable for auto-detected calls
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `customer_id`, `lead_id`, `phone_number`, `call_type`, `started_at`, `sync_status`

---

#### 17. `call_transcripts` Table
**Purpose**: Speech-to-text transcripts from calls

```sql
- id (bigint, primary key, auto increment)
- call_log_id (bigint, foreign key -> call_logs.id, unique)
- transcript_text (text) // Bangla text from speech-to-text
- original_audio_path (string, 500, nullable)
- language (string, 10, default: 'bn-BD') // Bangla Bangladesh
- confidence_score (decimal, 5, 2, nullable) // 0.00 to 1.00
- is_edited (boolean, default: false)
- edited_text (text, nullable) // If user edited the transcript
- processing_status (enum: 'pending', 'processing', 'completed', 'failed', default: 'pending')
- processed_at (timestamp, nullable)
- processing_mode (enum: 'online_async', 'offline_local', default: 'online_async')
- processing_error (text, nullable)
- sync_status (enum: 'synced', 'pending', 'failed', default: 'synced')
- sync_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `call_log_id`, `processing_status`, `sync_status`

---

#### 18. `sync_queue` Table
**Purpose**: Offline data sync queue

```sql
- id (bigint, primary key, auto increment)
- table_name (string, 100) // e.g., 'leads', 'customers'
- record_uuid (uuid, index) // Client-side stable ID (generated offline, never changes)
- record_id (bigint, nullable) // Server ID (null until created on server)
- action (enum: 'create', 'update', 'delete')
- data (text) // JSON encoded data
- device_id (string, 255, nullable) // Mobile device identifier
- idempotency_key (string, 64, nullable, index) // Prevent duplicate creates on retries
- depends_on_queue_id (bigint, nullable) // For ordering (e.g., pricing depends on lead create)
- entity_version (integer, nullable) // Optimistic locking for high-risk entities
- status (enum: 'pending', 'processing', 'completed', 'failed', default: 'pending')
- error_message (text, nullable)
- retry_count (integer, default: 0)
- max_retries (integer, default: 3)
- next_retry_at (timestamp, nullable)
- last_attempt_at (timestamp, nullable)
- processed_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `table_name`, `record_uuid`, `record_id`, `status`, `device_id`, `idempotency_key`, `next_retry_at`, `created_at`

---

#### 19. `backups` Table
**Purpose**: Google Drive backup tracking

```sql
- id (bigint, primary key, auto increment)
- file_name (string, 255)
- file_path (string, 500) // Local file path
- google_drive_file_id (string, 255, nullable, unique)
- file_size (bigint) // In bytes
- backup_type (enum: 'full', 'incremental', default: 'full')
- status (enum: 'pending', 'uploading', 'completed', 'failed', default: 'pending')
- error_message (text, nullable)
- uploaded_at (timestamp, nullable)
- created_by (bigint, nullable, foreign key -> users.id) // Nullable for automated backups
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `status`, `google_drive_file_id`, `created_at`

**Note**: Keep only last 20 backups, delete older ones

---

#### 20. `saved_filters` Table
**Purpose**: Saved filter presets for advanced filtering

```sql
- id (bigint, primary key, auto increment)
- name (string, 255)
- filter_data (text) // JSON encoded filter criteria
- is_shared (boolean, default: false) // Share with other users
- created_by (bigint, foreign key -> users.id)
- updated_by (bigint, foreign key -> users.id, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `created_by`, `is_shared`

---

#### 21. `reports` Table
**Purpose**: Generated reports

```sql
- id (bigint, primary key, auto increment)
- name (string, 255)
- report_type (enum: 'customer', 'lead', 'sales', 'analytics', 'custom', default: 'custom')
- report_data (text) // JSON encoded report configuration
- file_path (string, 500, nullable) // Generated file path
- file_type (enum: 'pdf', 'excel', 'csv', nullable)
- status (enum: 'pending', 'generating', 'completed', 'failed', default: 'pending')
- generated_at (timestamp, nullable)
- created_by (bigint, foreign key -> users.id)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `report_type`, `status`, `created_by`, `created_at`

---

#### 22. `predictions` Table
**Purpose**: Auto-predicted next month plans

```sql
- id (bigint, primary key, auto increment)
- customer_id (bigint, foreign key -> customers.id)
- lead_id (bigint, nullable, foreign key -> leads.id)
- predicted_date (date)
- prediction_type (enum: 'follow_up', 'conversion', 'contact', default: 'follow_up')
- confidence_score (decimal, 5, 2) // 0.00 to 1.00
- prediction_data (text, nullable) // JSON with prediction details
- is_approved (boolean, default: false)
- approved_by (bigint, nullable, foreign key -> users.id)
- approved_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `customer_id`, `lead_id`, `predicted_date`, `prediction_type`, `is_approved`

---

#### 23. `activity_logs` Table
**Purpose**: System activity tracking for audit

```sql
- id (bigint, primary key, auto increment)
- user_id (bigint, nullable, foreign key -> users.id)
- action (string, 100) // e.g., 'created', 'updated', 'deleted'
- model_type (string, 255) // e.g., 'App\Models\Customer'
- model_id (bigint, nullable)
- description (text, nullable)
- ip_address (string, 45, nullable)
- user_agent (text, nullable)
- created_at (timestamp)
```

**Indexes**: `user_id`, `model_type`, `model_id`, `action`, `created_at`

---

#### 24. `devices` Table (NEW)
**Purpose**: Device binding, session tracking, admin revoke (security layer for long-lived tokens)

```sql
- id (bigint, primary key, auto increment)
- user_id (bigint, foreign key -> users.id)
- device_id (string, 255, index) // stable device identifier
- device_name (string, 255, nullable)
- platform (enum: 'android', 'web', 'ios', default: 'android')
- app_version (string, 50, nullable)
- last_seen_at (timestamp, nullable)
- is_active (boolean, default: true)
- revoked_at (timestamp, nullable)
- revoked_by (bigint, nullable, foreign key -> users.id)
- created_at (timestamp)
- updated_at (timestamp)
```

**Indexes**: `user_id`, `device_id`, `platform`, `last_seen_at`, `revoked_at`

---

#### 25. `user_settings` Table (NEW)
**Purpose**: Per-user toggles (legal + UX) without forcing auto logout

```sql
- id (bigint, primary key, auto increment)
- user_id (bigint, foreign key -> users.id, unique)
- call_recording_enabled (boolean, default: false)
- speech_to_text_enabled (boolean, default: true)
- app_lock_enabled (boolean, default: false) // PIN/biometric gate
- app_lock_timeout_seconds (integer, default: 0) // 0 = immediate, or e.g. 30/60
- created_at (timestamp)
- updated_at (timestamp)
```

---

#### 26. `roles` / `permissions` Tables (NEW)
**Purpose**: Future-proof role control (beyond enum), granular permissions

```sql
roles
- id, name (unique), description (nullable), created_at, updated_at

permissions
- id, name (unique), description (nullable), created_at, updated_at

role_user (pivot)
- role_id (fk), user_id (fk), created_at

permission_role (pivot)
- permission_id (fk), role_id (fk), created_at
```

**Note**: Keep existing `users.role` enum for MVP if you want simplicity; this RBAC layer can be introduced gradually.

---

#### 27. `customer_assignments` Table
**Purpose**: Admin/Manager assigns customers to employees date-wise for calling

```sql
- id (bigint, primary key, auto increment)
- employee_id (bigint, foreign key -> users.id) // User who will call
- customer_id (bigint, foreign key -> customers.id) // Customer to call
- assigned_date (date) // Date when employee should call
- assigned_by (bigint, foreign key -> users.id) // Admin/Manager who assigned
- status (enum: 'pending', 'completed', 'cancelled', 'skipped', default: 'pending')
- priority (enum: 'low', 'medium', 'high', default: 'medium')
- notes (text, nullable)
- completed_at (timestamp, nullable)
- call_log_id (bigint, nullable, foreign key -> call_logs.id) // Link to actual call if made
- sync_status (enum: 'synced', 'pending', 'failed', default: 'synced')
- sync_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `employee_id`, `customer_id`, `assigned_date`, `status`, `assigned_by`, `sync_status`, `(employee_id, assigned_date, status)` composite

---

#### 28. `call_targets` Table
**Purpose**: Set daily/weekly/monthly call targets for employees, track progress

```sql
- id (bigint, primary key, auto increment)
- user_id (bigint, foreign key -> users.id) // Employee
- target_date (date) // Target date (for daily) or start date (for weekly/monthly)
- target_type (enum: 'daily', 'weekly', 'monthly', default: 'daily')
- target_count (integer) // How many calls to make
- completed_count (integer, default: 0) // How many completed
- period_start_date (date, nullable) // For weekly/monthly targets
- period_end_date (date, nullable) // For weekly/monthly targets
- set_by (bigint, foreign key -> users.id) // Admin/Manager who set the target
- is_active (boolean, default: true)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable, soft delete)
```

**Indexes**: `user_id`, `target_date`, `target_type`, `is_active`, `set_by`, `(user_id, target_date, target_type)` composite

---

### Database Relationships Summary

```
users (1) ──→ (many) leads (created_by)
users (1) ──→ (many) customers (created_by)
users (1) ──→ (many) call_logs (created_by)
users (1) ──→ (many) customer_assignments (employee_id)
users (1) ──→ (many) customer_assignments (assigned_by)
users (1) ──→ (many) call_targets (user_id)
users (1) ──→ (many) call_targets (set_by)

customers (1) ──→ (many) leads
customers (1) ──→ (many) customer_notes
customers (1) ──→ (many) follow_ups
customers (1) ──→ (many) sms_messages
customers (1) ──→ (many) call_logs
customers (1) ──→ (many) customer_assignments

leads (1) ──→ (1) pricing
leads (1) ──→ (many) customer_notes
leads (1) ──→ (many) follow_ups
leads (1) ──→ (many) sms_messages
leads (1) ──→ (many) call_logs

call_logs (1) ──→ (1) call_transcripts
call_logs (1) ──→ (many) customer_assignments (call_log_id)

categories (1) ──→ (many) categories (parent_id) // Self-referencing
addresses (1) ──→ (many) addresses (parent_id) // Self-referencing

customer_groups (1) ──→ (many) customers
professions (1) ──→ (many) customers
projects (1) ──→ (many) leads
interested_types (1) ──→ (many) leads
```

---

### Offline Sync Conflict Resolution (MUST DEFINE)

**Why**: Offline-first without strict conflict rules will create silent data corruption.

#### Offline Sync Conflict Flow (Online-First, Offline-Safe, Conflict-Aware)

**High-level architecture (implementation flow)**:

```
User Action (Mobile/Web)
        │
        ▼
Local Store (SQLite / IndexedDB)
(sync_status = pending)
        │
        ▼
sync_queue
(action, data, retry_count)
        │
        ▼
Network Available?
   ┌──────────────┐
   │      NO      │
   │ Stay Offline │
   └──────┬───────┘
          │ YES
          ▼
Sync Worker (client)
        │
        ▼
API /api/v1/sync/...
        │
        ▼
Server Validation + Auth + Device Check
        │
        ▼
Conflict Detector
   ┌──────────────┬──────────────┐
   │  No Conflict  │   Conflict    │
   ▼               ▼
Apply Change     Conflict Resolver
   │               │
   ▼               ▼
Return server     Resolution result
record + version  (server priority / merge / manual)
   │
   ▼
Mark Synced + Update local store
```

---

#### 1) Core Data Rules (Non-negotiable)

- **Server is final authority for IDs**:
  - Offline-created rows must have **`record_uuid`** (client ID).
  - Server returns `{record_id, record_uuid}` mapping; client updates local references.
- **Never silently drop data**:
  - Every rejected record must return an error code + message; keep it visible in UI.
- **Idempotent creates**:
  - Use `sync_queue.idempotency_key` for `create` actions to avoid duplicates on retries.
- **Device awareness**:
  - Every sync request sends `device_id`; server logs and can apply device policies (admin revoke, etc.).

---

#### 2) Conflict Detection Logic (when to treat as conflict)

Conflict is raised when BOTH are true:
- Same entity (`record_uuid` or `record_id`) changed on both sides
- **Local base version is stale** compared to server (recommended: optimistic lock)

**Implementation-ready approach**:
- High-risk tables store `entity_version` (integer).
- Client sends `base_version` (the version it edited).
- If `base_version != server_version` → **conflict**.

Fallback (if you don't implement versioning on day 1):
- Conflict if `local.updated_at < server.updated_at` AND both sides changed the same record.

Applies to:
- `customers`, `leads`, `follow_ups` (high)
- `customer_notes` (low)
- `call_logs` (limited, mostly server priority)

---

#### 3) Conflict Resolution Strategies (Rules)

**Rule A — Last Write Wins (Default for low-risk logs)**  
Use for: notes, remarks, transcripts, SMS logs  
Logic:
- if local is newer → overwrite server
- else → overwrite local

**Rule B — Field-level merge (Preferred for CRM core)**  
Use for: customer profile, lead info, follow-ups  
Logic:
- merge per field (server wins unless field changed locally)
Implementation options:
- Phase 1: conservative merge (only merge non-overlapping fields)
- Phase 2: per-field timestamps OR JSON diff tracking for true field-level

**Rule C — Manual resolution (Rare but critical)**  
Use for: pricing conflicts, lead status mismatch (converted vs lost), ownership reassignment  
Flow:
- server returns `conflict` state + both versions
- client shows UI: **Keep Local / Keep Server**
- decision must be logged in `activity_logs`

---

#### 4) Conflict Resolution Matrix (default policy)

| Table | Strategy |
|------|----------|
| `customers` | Field-level merge |
| `leads` | Field-level + Manual for `status/priority/owner` |
| `pricing` | Manual only (never auto-resolve) |
| `follow_ups` | Field-level merge |
| `customer_notes` | Last write wins |
| `call_logs` | Server priority |
| `call_transcripts` | Server priority (online async) |
| `sms_messages` | Server priority (provider state is source of truth) |

---

#### 5) Sync Queue Processing Rules

**Retry policy**:
- `max_retries = 3`
- Exponential backoff: 1m → 5m → 15m (store in `next_retry_at`)
- After 3 failures:
  - `status = failed`
  - Must be visible in admin panel + in-app warning

**Partial failure handling**:
- Process record-by-record (not all-or-nothing)
- Successful ones marked `completed`, failures remain `pending/failed`

**Ordering**:
- Default: `created_at ASC`
- Use `depends_on_queue_id` to guarantee parent-first (e.g., create lead → then pricing)

---

#### 6) Offline → Online Transition Flow

When network restored:
- SyncQueueService starts automatically
- Picks `pending` rows where `next_retry_at <= now()` (or null)
- Pushes to server
- Applies conflict rules based on server response
- Updates local DB (mark synced + update IDs/versions)

---

#### 7) Multi-device ownership rule

Same user on multiple devices is allowed, but conflicts must be predictable:
- **Server latest** wins by default
- If core fields conflict → manual resolution
- Track `device_id` in sync to aid audit + troubleshooting

---

#### 8) UI states (must show to agent)

| State | Indicator | Meaning |
|------|----------|---------|
| Synced | ✅ Green | Safe |
| Pending | 🟡 Yellow | Waiting for network |
| Conflict | 🔴 Red | Needs merge/manual decision |
| Failed | ⚠️ Orange | Needs retry/manual attention |

---

#### 9) Example scenario (expected behavior)

Case:
- Agent edits customer address offline
- Manager edits same customer online

Result:
- Address → field-level merge
- Notes → last write wins
- Lead status/pricing → manual resolution if conflicting

---

#### 10) Suggested Sync Endpoints (API contract)

- `POST /api/v1/sync/push` (client → server)
  - body: `{device_id, items:[{table, action, record_uuid, record_id?, base_version?, idempotency_key?, data}] }`
- `POST /api/v1/sync/pull` (client ← server)
  - params: `since=timestamp` or `since_version`
- `GET /api/v1/sync/status` (debug/health)

Server returns per item:
- `ok | conflict | rejected`
- `record_id`, `record_uuid`, `server_version`, `server_record` (when needed)

---

### Call Recording Consent & Legal Compliance (MUST DEFINE)

**Policy requirements**:
- Explicit consent capture workflow (per org policy)
- Per-user toggle (`user_settings.call_recording_enabled`)
- Optional: auto-disable recording for unknown numbers
- Visible in-app indicator when recording is active

**Data tracking**:
- `call_logs.recording_consent_status`, `call_logs.recording_consent_at`
- Store recordings encrypted; access controlled by role/permission

---

### Speech-to-Text (Realistic Offline Model)

**Expectation (recommended)**:
- **Offline**: store audio only, queue transcription
- **Online**: async job transcribes Bangla and fills `call_transcripts`

This avoids unreliable offline Bangla STT on low-end Android devices.

### Migration File Naming Convention

```
2024_01_01_000001_create_users_table.php
2024_01_01_000002_create_personal_access_tokens_table.php
2024_01_01_000003_create_categories_table.php
2024_01_01_000004_create_addresses_table.php
2024_01_01_000005_create_customer_groups_table.php
2024_01_01_000006_create_interested_types_table.php
2024_01_01_000007_create_professions_table.php
2024_01_01_000008_create_customers_table.php
2024_01_01_000009_create_projects_table.php
2024_01_01_000010_create_leads_table.php
2024_01_01_000011_create_pricing_table.php
2024_01_01_000012_create_customer_notes_table.php
2024_01_01_000013_create_follow_ups_table.php
2024_01_01_000014_create_sms_templates_table.php
2024_01_01_000015_create_sms_messages_table.php
2024_01_01_000016_create_call_logs_table.php
2024_01_01_000017_create_call_transcripts_table.php
2024_01_01_000018_create_sync_queue_table.php
2024_01_01_000019_create_backups_table.php
2024_01_01_000020_create_saved_filters_table.php
2024_01_01_000021_create_reports_table.php
2024_01_01_000022_create_predictions_table.php
2024_01_01_000023_create_activity_logs_table.php
2024_01_01_000024_create_devices_table.php
2024_01_01_000025_create_user_settings_table.php
2024_01_01_000026_create_roles_table.php
2024_01_01_000027_create_permissions_table.php
2024_01_01_000028_create_role_user_table.php
2024_01_01_000029_create_permission_role_table.php
2024_01_01_000030_create_customer_assignments_table.php
2024_01_01_000031_create_call_targets_table.php
```

---

### Database Seeding Priority

1. **Initial Seeders**:
   - Users (admin account)
   - Customer Groups (default groups)
   - Interested Types (default types)
   - Professions (default profession types)
   - Addresses (Bangladesh divisions, districts)

2. **Development Seeders**:
   - Sample categories
   - Sample projects
   - Sample customers
   - Sample leads

---

## 🗓️ Development Phases

### **Phase 1: Foundation & Core Setup** (4-6 weeks)
**Goal**: Project infrastructure, authentication, and basic database structure

#### Modules:
1. **Project Setup & Configuration**
   - Laravel project initialization
   - Database configuration
   - Environment setup
   - Git repository setup
   - CI/CD pipeline basics

2. **Authentication Module**
   - Login system
   - Token-based authentication (60-year expiry)
   - Secure token storage
   - Auth state persistence
   - Logout functionality

3. **Database Architecture**
   - Core table design
   - Infinite hierarchy tables (categories, addresses)
   - Migration files
   - Seeders for initial data

4. **API Foundation**
   - API route structure
   - Middleware setup
   - Response formatting
   - Error handling

---

### **Phase 2: Core CRM Features** (6-8 weeks)
**Goal**: Basic CRM functionality for customer management

#### Modules:
5. **Category Management Module**
   - Unlimited depth category system
   - Category CRUD operations
   - Category hierarchy API
   - Category tree visualization

6. **Address Management Module**
   - Infinite depth address hierarchy
   - Address CRUD operations
   - Address search & filtering
   - Address tree structure

7. **Customer Group Module**
   - Customer group CRUD
   - Group assignment
   - Group-based filtering

8. **Customer Management Module**
   - Customer CRUD operations
   - Customer profile management
   - Customer search & filtering
   - Customer list with pagination

9. **Interested Types Module**
   - Interested type configuration
   - Customer interest tracking
   - Interest-based filtering

10. **Customer Info Module**
    - Complete customer information fields
    - Target real estate tracking
    - Customer data validation
    - Customer history tracking

---

### **Phase 3: Advanced Features** (6-8 weeks)
**Goal**: Enhanced functionality for real estate operations

#### Modules:
11. **SMS Module**
    - SMS gateway integration
    - Bulk SMS functionality
    - SMS templates
    - SMS history tracking
    - SMS scheduling

12. **Advanced Filtering Module**
    - Multi-criteria filtering
    - Saved filter presets
    - Filter export functionality
    - Dynamic filter builder

13. **Notes & Follow-up Module**
    - Customer notes management
    - Follow-up scheduling
    - Follow-up reminders
    - Follow-up history

14. **Schedule Management Module**
    - Next schedule planning
    - Schedule calendar view
    - Auto-predicted next month plan
    - Schedule notifications

---

### **Phase 4: Mobile App Development** (8-10 weeks)
**Goal**: Native Android app with offline-first architecture

#### Modules:
15. **Mobile App Foundation**
    - Quasar project setup
    - Capacitor configuration
    - App navigation structure
    - State management setup

16. **Mobile Authentication**
    - Login screen
    - Token storage (Secure Storage)
    - Auth state management
    - Auto-login on app restart

17. **Offline-First Architecture**
    - Local database setup (SQLite/IndexedDB)
    - Offline data storage
    - Sync queue management
    - Conflict resolution strategy

18. **Customer Entry Module (Mobile)**
    - Customer entry form
    - Existing customer loader
    - New customer form
    - Quick save functionality

19. **Customer List Module (Mobile)**
    - Customer list view
    - Search & filter
    - Customer detail view
    - Offline data access

20. **Sync Module**
    - Online-first approach
    - Background sync service
    - Conflict detection & resolution
    - Sync status indicator

---

### **Phase 5: Call Integration Feature** (6-8 weeks)
**Goal**: Incoming call detection and auto customer entry

#### Modules:
21. **Call Detection Module**
    - Android TelephonyManager integration
    - Incoming call listener
    - Call state monitoring
    - App auto-launch on call

22. **Call Context Module**
    - Caller number capture
    - Call status display
    - Call duration tracking
    - Call history

23. **Customer Resolver Module**
    - Phone number lookup
    - Existing customer detection
    - Customer data auto-load
    - New customer initialization

24. **Call Entry UI Module**
    - Call-time customer entry screen
    - Quick interest selector
    - One-hand friendly design
    - Call context header

25. **Speech-to-Text Module**
    - Call recording (permission-based)
    - Audio to Bangla text conversion
    - Text auto-population
    - Manual edit capability
    - Offline/online speech processing

26. **Call Logs Module**
    - Call history storage
    - Call transcript storage
    - Call notes management
    - Encrypted storage

---

### **Phase 6: Reporting & Analytics** (4-6 weeks)
**Goal**: Business intelligence and reporting capabilities

#### Modules:
27. **Phase 6.1: Operational Reports (MVP-friendly)**
    - Daily/weekly lead funnel report
    - Follow-up due list report
    - Agent activity report
    - Export (CSV/Excel) and basic date filters

28. **Phase 6.2: Analytics Dashboard (Post-MVP)**
    - Key metrics visualization
    - Trend charts + cohort-style breakdowns
    - Data aggregation layer (optimized queries + caching)

29. **Phase 6.3: Prediction (Post-MVP)**
    - Auto-predicted next month plan (baseline heuristic first)
    - Confidence scoring
    - Human approval workflow

---

### **Phase 7: Backup & Maintenance** (3-4 weeks)
**Goal**: Data backup and system maintenance

#### Modules:
30. **Backup Module**
    - Google Drive API integration
    - Automated backup (2x daily)
    - Backup rotation (last 20 backups)
    - Backup restore functionality
    - Backup status monitoring

31. **System Maintenance Module**
    - Log management
    - Performance monitoring
    - Error tracking
    - System health checks

---

### **Phase 8: Testing & Optimization** (4-6 weeks)
**Goal**: Quality assurance and performance optimization

#### Modules:
32. **Testing Module**
    - Unit tests
    - Integration tests
    - E2E tests
    - Mobile app testing
    - Performance testing

33. **Optimization Module**
    - Database query optimization
    - API response optimization
    - Mobile app performance
    - Offline sync optimization
    - Cache optimization

---

### **Phase 9: Deployment & Launch** (2-3 weeks)
**Goal**: Production deployment and go-live

#### Modules:
34. **Deployment Module**
    - Production server setup
    - SSL configuration
    - Domain configuration
    - Environment optimization

35. **Mobile App Publishing**
    - Android APK/AAB build
    - Google Play Store submission
    - App signing
    - Version management

36. **Documentation Module**
    - User manual
    - Admin guide
    - API documentation
    - Developer documentation

---

## 📊 Development Timeline Summary

| Phase | Duration | Priority | Dependencies |
|-------|----------|----------|--------------|
| Phase 1: Foundation | 4-6 weeks | Critical | None |
| Phase 2: Core CRM | 6-8 weeks | Critical | Phase 1 |
| Phase 3: Advanced Features | 6-8 weeks | High | Phase 2 |
| Phase 4: Mobile App | 8-10 weeks | Critical | Phase 1, 2 |
| Phase 5: Call Integration | 6-8 weeks | High | Phase 4 |
| Phase 6: Reporting | 4-6 weeks | Medium | Phase 2, 3 |
| Phase 7: Backup | 3-4 weeks | Medium | Phase 1 |
| Phase 8: Testing | 4-6 weeks | Critical | All phases |
| Phase 9: Deployment | 2-3 weeks | Critical | Phase 8 |

**Total Estimated Duration**: 48-60 weeks (~12-14 months)

---

## 🎯 Development Priorities

### Must Have (MVP)
- Phase 1: Foundation
- Phase 2: Core CRM (Modules 5-10)
- Phase 4: Mobile App (Modules 15-20)
- Phase 5: Call Integration (Modules 21-26)
- Phase 6.1: Operational Reports (Basic)
- Phase 8: Testing (Basic)

### Should Have
- Phase 3: Advanced Features
- Phase 6: Reporting & Analytics
- Phase 7: Backup

### Nice to Have
- Advanced analytics
- AI-powered predictions
- Multi-language support

---

## 👥 Team Structure Recommendation

### Backend Team
- 1 Senior Laravel Developer
- 1 Mid-level Laravel Developer
- 1 Database Administrator

### Frontend Team
- 1 Senior Vue.js/Quasar Developer
- 1 Mid-level Frontend Developer

### Mobile Team
- 1 Senior Mobile Developer (Quasar/Ionic)
- 1 Android Native Developer (for call integration)

### QA Team
- 1 QA Engineer
- 1 Test Automation Engineer

### DevOps
- 1 DevOps Engineer (part-time)

---

## 🔄 Agile Methodology

- **Sprint Duration**: 2 weeks
- **Sprint Planning**: Every 2 weeks
- **Daily Standups**: 15 minutes
- **Sprint Review**: End of each sprint
- **Retrospective**: End of each sprint

---

## 📝 Notes for Development Team

1. **Offline-First Approach**: Always design with offline capability in mind
2. **Mobile-First Design**: All UI should be mobile-responsive
3. **Security**: Implement proper encryption for sensitive data
4. **Performance**: Optimize for low-end Android devices
5. **Scalability**: Design database and API for future growth
6. **Documentation**: Maintain code documentation throughout development

---

## 🚀 Quick Start Checklist

- [ ] Repository setup
- [ ] Development environment setup
- [ ] Database design finalization
- [ ] API documentation template
- [ ] Design system setup
- [ ] CI/CD pipeline configuration
- [ ] Team onboarding

---

## ✅ Missing Items Checklist (From CTO Review)

- **Auth (60-year token security)**:
  - Device binding via `devices`
  - Admin revoke flow (revoke device/session)
  - Optional app lock (PIN/biometric) via `user_settings` (no forced auto-logout)
- **Offline conflicts**:
  - Define and document conflict strategy (server priority + field merge vs LWW)
  - Add `device_id` and optional `version` policy for high-risk tables (`customers`, `leads`)
- **Call recording compliance**:
  - Consent workflow + per-user toggle + visible indicator
  - Optional: disable recording for unknown numbers
- **Speech-to-text reality**:
  - Offline: store audio only
  - Online: async transcription job updates `call_transcripts`
- **Reporting scope control**:
  - Split into 6.1 operational reports (MVP) vs 6.2 analytics vs 6.3 prediction
- **Performance for infinite trees**:
  - Add `path` + `depth` columns for `categories` and `addresses`
- **API versioning**:
  - Start with `/api/v1/...` endpoints

---

**Document Version**: 1.0  
**Last Updated**: 2024  
**Prepared By**: CTO Office  
**Status**: Draft - Ready for Review


CRM form fields
{
  "lead_source": "",

  "customer": {
    "name": "",
    "mobile": "",
    "email": "",
    "profession": {
      "type": "", 
      "business_type": "",
      "job_title": "",
      "company_name": ""
    }
  },

  "address": {
    "district": "",
    "upazila": "",
    "village": "",
    "nearest_market": "",
    "current_address": ""
  },

  "project": {
    "name": ""
  },

  "customer_requirement": "",

  "pricing": {
    "quoted_price": "",
    "customer_budget": "",
    "agreeable_price": ""
  },

  "preferred_area": "",

  "next_contact_date": "",

  "remarks": "",

  "meta": {
    "created_by_email": "citics493@gmail.com",
    "created_at": ""
  }
}

Google Form Field	Object Path
লিড কোথায় থেকে এসেছে	lead_source
গ্রাহকের নাম	customer.name
মোবাইল নম্বর	customer.mobile
ইমেইল	customer.email
গ্রাহকের পেশা	customer.profession.type
ব্যবসার ধরণ	customer.profession.business_type
কোন পদ এ চাকুরী করেন	customer.profession.job_title
কোন কোম্পানীতে চাকুরী করেন	customer.profession.company_name
জেলা	address.district
উপজেলা	address.upazila
গ্রাম	address.village
কাছাকাছি বাজার	address.nearest_market
বর্তমান ঠিকানা	address.current_address
প্রজেক্ট এর নাম	project.name
কাস্টমার এর চাহিদা	customer_requirement
দাম কত বলেছেন	pricing.quoted_price
কাস্টমার এর বাজেট	pricing.customer_budget
রাজি হবেন কততে	pricing.agreeable_price
কোন এলাকায় চান	preferred_area
পরবর্তী যোগাযোগ	next_contact_date
মতামত	remarks