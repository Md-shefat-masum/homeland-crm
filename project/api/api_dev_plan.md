# API Development Execution Plan
## Step-by-Step File Creation & Module Development

**Architecture**: Route → Controller → Action → Return Data  
**No Repository/Factory/Collection** - Simple & Direct

---

## 📁 Project Structure Overview

```
project/api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php (✅ Already exists)
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── AddressController.php
│   │   │   │   ├── CustomerGroupController.php
│   │   │   │   ├── InterestedTypeController.php
│   │   │   │   ├── ProfessionController.php
│   │   │   │   ├── CustomerController.php
│   │   │   │   ├── ProjectController.php
│   │   │   │   ├── LeadController.php
│   │   │   │   ├── PricingController.php
│   │   │   │   ├── CustomerNoteController.php
│   │   │   │   ├── FollowUpController.php
│   │   │   │   ├── SmsTemplateController.php
│   │   │   │   ├── SmsMessageController.php
│   │   │   │   ├── CallLogController.php
│   │   │   │   ├── CallTranscriptController.php
│   │   │   │   ├── SyncController.php
│   │   │   │   ├── BackupController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── DashboardController.php
│   │   │   └── Controller.php (✅ Already exists)
│   │   └── Actions/ (New folder - for business logic)
│   │       ├── Auth/
│   │       ├── Category/
│   │       ├── Address/
│   │       ├── CustomerGroup/
│   │       ├── InterestedType/
│   │       ├── Profession/
│   │       ├── Customer/
│   │       ├── Project/
│   │       ├── Lead/
│   │       ├── Pricing/
│   │       ├── CustomerNote/
│   │       ├── FollowUp/
│   │       ├── SmsTemplate/
│   │       ├── SmsMessage/
│   │       ├── CallLog/
│   │       ├── CallTranscript/
│   │       ├── Sync/
│   │       ├── Backup/
│   │       ├── Report/
│   │       └── Dashboard/
│   └── Models/
│       ├── User.php (✅ Already exists)
│       ├── Category.php
│       ├── Address.php
│       ├── CustomerGroup.php
│       ├── InterestedType.php
│       ├── Profession.php
│       ├── Customer.php
│       ├── Project.php
│       ├── Lead.php
│       ├── Pricing.php
│       ├── CustomerNote.php
│       ├── FollowUp.php
│       ├── SmsTemplate.php
│       ├── SmsMessage.php
│       ├── CallLog.php
│       ├── CallTranscript.php
│       ├── CustomerAssignment.php
│       ├── CallTarget.php
│       ├── SyncQueue.php
│       ├── Backup.php
│       ├── SavedFilter.php
│       ├── Report.php
│       ├── Prediction.php
│       ├── ActivityLog.php
│       ├── Device.php
│       ├── UserSetting.php
│       ├── Role.php
│       └── Permission.php
├── database/
│   └── migrations/
│       ├── 2024_01_01_000001_create_users_table.php (✅ Already exists)
│       ├── 2024_01_01_000002_create_personal_access_tokens_table.php (✅ Already exists)
│       ├── 2024_01_01_000003_create_categories_table.php
│       ├── 2024_01_01_000004_create_addresses_table.php
│       ├── 2024_01_01_000005_create_customer_groups_table.php
│       ├── 2024_01_01_000006_create_interested_types_table.php
│       ├── 2024_01_01_000007_create_professions_table.php
│       ├── 2024_01_01_000008_create_customers_table.php
│       ├── 2024_01_01_000009_create_projects_table.php
│       ├── 2024_01_01_000010_create_leads_table.php
│       ├── 2024_01_01_000011_create_pricing_table.php
│       ├── 2024_01_01_000012_create_customer_notes_table.php
│       ├── 2024_01_01_000013_create_follow_ups_table.php
│       ├── 2024_01_01_000014_create_sms_templates_table.php
│       ├── 2024_01_01_000015_create_sms_messages_table.php
│       ├── 2024_01_01_000016_create_call_logs_table.php
│       ├── 2024_01_01_000017_create_call_transcripts_table.php
│       ├── 2024_01_01_000018_create_sync_queue_table.php
│       ├── 2024_01_01_000019_create_backups_table.php
│       ├── 2024_01_01_000020_create_saved_filters_table.php
│       ├── 2024_01_01_000021_create_reports_table.php
│       ├── 2024_01_01_000022_create_predictions_table.php
│       ├── 2024_01_01_000023_create_activity_logs_table.php
│       ├── 2024_01_01_000030_create_customer_assignments_table.php
│       ├── 2024_01_01_000031_create_call_targets_table.php
│       ├── 2024_01_01_000024_create_devices_table.php
│       ├── 2024_01_01_000025_create_user_settings_table.php
│       ├── 2024_01_01_000026_create_roles_table.php
│       ├── 2024_01_01_000027_create_permissions_table.php
│       ├── 2024_01_01_000028_create_role_user_table.php
│       └── 2024_01_01_000029_create_permission_role_table.php
└── routes/
    └── api.php (✅ Already exists - need to add routes)
```

---

## 🎯 Development Order (Step-by-Step)

### **Phase 1: Foundation Setup** (Week 1)

#### Step 1.1: Create Actions Folder Structure
**Files to create:**
```
app/Http/Actions/
app/Http/Actions/Auth/
app/Http/Actions/Category/
app/Http/Actions/Address/
app/Http/Actions/CustomerGroup/
app/Http/Actions/InterestedType/
app/Http/Actions/Profession/
app/Http/Actions/Customer/
app/Http/Actions/Project/
app/Http/Actions/Lead/
app/Http/Actions/Pricing/
app/Http/Actions/CustomerNote/
app/Http/Actions/FollowUp/
app/Http/Actions/SmsTemplate/
app/Http/Actions/SmsMessage/
app/Http/Actions/CallLog/
app/Http/Actions/CallTranscript/
app/Http/Actions/Sync/
app/Http/Actions/Backup/
app/Http/Actions/Report/
app/Http/Actions/Dashboard/
```

---

### **Phase 2: Core Modules** (Week 2-3)

#### Step 2.1: Category Module
**Files to create:**
1. `database/migrations/2024_01_01_000003_create_categories_table.php`
2. `app/Models/Category.php`
3. `app/Http/Actions/Category/GetCategoryTreeAction.php`
4. `app/Http/Actions/Category/CreateCategoryAction.php`
5. `app/Http/Actions/Category/UpdateCategoryAction.php`
6. `app/Http/Actions/Category/DeleteCategoryAction.php`
7. `app/Http/Controllers/Api/CategoryController.php`
8. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/categories` - List all categories (tree)
- `GET /api/v1/categories/{id}` - Get single category
- `POST /api/v1/categories` - Create category
- `PUT /api/v1/categories/{id}` - Update category
- `DELETE /api/v1/categories/{id}` - Delete category

---

#### Step 2.2: Address Module
**Files to create:**
1. `database/migrations/2024_01_01_000004_create_addresses_table.php`
2. `app/Models/Address.php`
3. `app/Http/Actions/Address/GetAddressTreeAction.php`
4. `app/Http/Actions/Address/CreateAddressAction.php`
5. `app/Http/Actions/Address/UpdateAddressAction.php`
6. `app/Http/Actions/Address/DeleteAddressAction.php`
7. `app/Http/Actions/Address/SearchAddressAction.php`
8. `app/Http/Controllers/Api/AddressController.php`
9. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/addresses` - List all addresses (tree)
- `GET /api/v1/addresses/{id}` - Get single address
- `GET /api/v1/addresses/search?q={query}` - Search addresses
- `POST /api/v1/addresses` - Create address
- `PUT /api/v1/addresses/{id}` - Update address
- `DELETE /api/v1/addresses/{id}` - Delete address

---

#### Step 2.3: Customer Group Module
**Files to create:**
1. `database/migrations/2024_01_01_000005_create_customer_groups_table.php`
2. `app/Models/CustomerGroup.php`
3. `app/Http/Actions/CustomerGroup/GetCustomerGroupsAction.php`
4. `app/Http/Actions/CustomerGroup/CreateCustomerGroupAction.php`
5. `app/Http/Actions/CustomerGroup/UpdateCustomerGroupAction.php`
6. `app/Http/Actions/CustomerGroup/DeleteCustomerGroupAction.php`
7. `app/Http/Controllers/Api/CustomerGroupController.php`
8. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/customer-groups` - List all groups
- `GET /api/v1/customer-groups/{id}` - Get single group
- `POST /api/v1/customer-groups` - Create group
- `PUT /api/v1/customer-groups/{id}` - Update group
- `DELETE /api/v1/customer-groups/{id}` - Delete group

---

#### Step 2.4: Interested Type Module
**Files to create:**
1. `database/migrations/2024_01_01_000006_create_interested_types_table.php`
2. `app/Models/InterestedType.php`
3. `app/Http/Actions/InterestedType/GetInterestedTypesAction.php`
4. `app/Http/Actions/InterestedType/CreateInterestedTypeAction.php`
5. `app/Http/Actions/InterestedType/UpdateInterestedTypeAction.php`
6. `app/Http/Actions/InterestedType/DeleteInterestedTypeAction.php`
7. `app/Http/Controllers/Api/InterestedTypeController.php`
8. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/interested-types` - List all types
- `GET /api/v1/interested-types/{id}` - Get single type
- `POST /api/v1/interested-types` - Create type
- `PUT /api/v1/interested-types/{id}` - Update type
- `DELETE /api/v1/interested-types/{id}` - Delete type

---

#### Step 2.5: Profession Module
**Files to create:**
1. `database/migrations/2024_01_01_000007_create_professions_table.php`
2. `app/Models/Profession.php`
3. `app/Http/Actions/Profession/GetProfessionsAction.php`
4. `app/Http/Actions/Profession/CreateProfessionAction.php`
5. `app/Http/Actions/Profession/UpdateProfessionAction.php`
6. `app/Http/Actions/Profession/DeleteProfessionAction.php`
7. `app/Http/Controllers/Api/ProfessionController.php`
8. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/professions` - List all professions
- `GET /api/v1/professions/{id}` - Get single profession
- `POST /api/v1/professions` - Create profession
- `PUT /api/v1/professions/{id}` - Update profession
- `DELETE /api/v1/professions/{id}` - Delete profession

---

### **Phase 3: Customer & Lead Modules** (Week 4-5)

#### Step 3.1: Customer Module
**Files to create:**
1. `database/migrations/2024_01_01_000008_create_customers_table.php`
2. `app/Models/Customer.php`
3. `app/Http/Actions/Customer/GetCustomersAction.php`
4. `app/Http/Actions/Customer/GetCustomerAction.php`
5. `app/Http/Actions/Customer/CreateCustomerAction.php`
6. `app/Http/Actions/Customer/UpdateCustomerAction.php`
7. `app/Http/Actions/Customer/DeleteCustomerAction.php`
8. `app/Http/Actions/Customer/SearchCustomerAction.php`
9. `app/Http/Controllers/Api/CustomerController.php`
10. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/customers` - List customers (with filters)
- `GET /api/v1/customers/{id}` - Get single customer
- `GET /api/v1/customers/search?q={query}` - Search customers
- `POST /api/v1/customers` - Create customer
- `PUT /api/v1/customers/{id}` - Update customer
- `DELETE /api/v1/customers/{id}` - Delete customer

---

#### Step 3.2: Project Module
**Files to create:**
1. `database/migrations/2024_01_01_000009_create_projects_table.php`
2. `app/Models/Project.php`
3. `app/Http/Actions/Project/GetProjectsAction.php`
4. `app/Http/Actions/Project/GetProjectAction.php`
5. `app/Http/Actions/Project/CreateProjectAction.php`
6. `app/Http/Actions/Project/UpdateProjectAction.php`
7. `app/Http/Actions/Project/DeleteProjectAction.php`
8. `app/Http/Controllers/Api/ProjectController.php`
9. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/projects` - List projects
- `GET /api/v1/projects/{id}` - Get single project
- `POST /api/v1/projects` - Create project
- `PUT /api/v1/projects/{id}` - Update project
- `DELETE /api/v1/projects/{id}` - Delete project

---

#### Step 3.3: Lead Module
**Files to create:**
1. `database/migrations/2024_01_01_000010_create_leads_table.php`
2. `app/Models/Lead.php`
3. `app/Http/Actions/Lead/GetLeadsAction.php`
4. `app/Http/Actions/Lead/GetLeadAction.php`
5. `app/Http/Actions/Lead/CreateLeadAction.php`
6. `app/Http/Actions/Lead/UpdateLeadAction.php`
7. `app/Http/Actions/Lead/DeleteLeadAction.php`
8. `app/Http/Actions/Lead/FilterLeadsAction.php`
9. `app/Http/Controllers/Api/LeadController.php`
10. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/leads` - List leads (with filters)
- `GET /api/v1/leads/{id}` - Get single lead
- `POST /api/v1/leads` - Create lead
- `PUT /api/v1/leads/{id}` - Update lead
- `DELETE /api/v1/leads/{id}` - Delete lead

---

#### Step 3.4: Pricing Module
**Files to create:**
1. `database/migrations/2024_01_01_000011_create_pricing_table.php`
2. `app/Models/Pricing.php`
3. `app/Http/Actions/Pricing/GetPricingAction.php`
4. `app/Http/Actions/Pricing/CreatePricingAction.php`
5. `app/Http/Actions/Pricing/UpdatePricingAction.php`
6. `app/Http/Controllers/Api/PricingController.php`
7. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/leads/{leadId}/pricing` - Get pricing for lead
- `POST /api/v1/leads/{leadId}/pricing` - Create/Update pricing
- `PUT /api/v1/pricing/{id}` - Update pricing

---

### **Phase 3.5: Customer Assignment & Call Target Module** (Week 5-6)
**Goal**: Admin/Manager assigns customers to employees date-wise, employees see daily call list and targets

#### Step 3.5.1: Customer Assignment Module
**Files to create:**
1. `database/migrations/2024_01_01_000030_create_customer_assignments_table.php`
2. `app/Models/CustomerAssignment.php`
3. `app/Http/Actions/CustomerAssignment/GetCustomerAssignmentsAction.php`
4. `app/Http/Actions/CustomerAssignment/GetMyAssignmentsAction.php` (for employee)
5. `app/Http/Actions/CustomerAssignment/CreateCustomerAssignmentAction.php`
6. `app/Http/Actions/CustomerAssignment/BulkAssignCustomersAction.php`
7. `app/Http/Actions/CustomerAssignment/UpdateCustomerAssignmentAction.php`
8. `app/Http/Actions/CustomerAssignment/CompleteAssignmentAction.php`
9. `app/Http/Actions/CustomerAssignment/DeleteCustomerAssignmentAction.php`
10. `app/Http/Controllers/Api/CustomerAssignmentController.php`
11. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/customer-assignments` - List all assignments (admin/manager)
- `GET /api/v1/customer-assignments/my-assignments` - Get my assignments (employee)
- `GET /api/v1/customer-assignments/my-assignments/today` - Get today's assignments (employee)
- `GET /api/v1/customer-assignments/{id}` - Get single assignment
- `POST /api/v1/customer-assignments` - Create assignment
- `POST /api/v1/customer-assignments/bulk` - Bulk assign customers
- `PUT /api/v1/customer-assignments/{id}` - Update assignment
- `POST /api/v1/customer-assignments/{id}/complete` - Mark as completed
- `DELETE /api/v1/customer-assignments/{id}` - Delete assignment

---

#### Step 3.5.2: Call Target Module
**Files to create:**
1. `database/migrations/2024_01_01_000031_create_call_targets_table.php`
2. `app/Models/CallTarget.php`
3. `app/Http/Actions/CallTarget/GetCallTargetsAction.php`
4. `app/Http/Actions/CallTarget/GetMyTargetAction.php` (for employee)
5. `app/Http/Actions/CallTarget/CreateCallTargetAction.php`
6. `app/Http/Actions/CallTarget/UpdateCallTargetAction.php`
7. `app/Http/Actions/CallTarget/GetTargetProgressAction.php`
8. `app/Http/Controllers/Api/CallTargetController.php`
9. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/call-targets` - List all targets (admin/manager)
- `GET /api/v1/call-targets/my-target` - Get my target for today/current period (employee)
- `GET /api/v1/call-targets/my-target/progress` - Get my target progress (employee)
- `GET /api/v1/call-targets/{id}` - Get single target
- `POST /api/v1/call-targets` - Create target
- `PUT /api/v1/call-targets/{id}` - Update target
- `GET /api/v1/call-targets/user/{userId}/date/{date}` - Get target for specific user and date

---

### **Phase 4: Communication & Follow-up** (Week 6-7)

#### Step 4.1: Customer Note Module
**Files to create:**
1. `database/migrations/2024_01_01_000012_create_customer_notes_table.php`
2. `app/Models/CustomerNote.php`
3. `app/Http/Actions/CustomerNote/GetCustomerNotesAction.php`
4. `app/Http/Actions/CustomerNote/CreateCustomerNoteAction.php`
5. `app/Http/Actions/CustomerNote/UpdateCustomerNoteAction.php`
6. `app/Http/Actions/CustomerNote/DeleteCustomerNoteAction.php`
7. `app/Http/Controllers/Api/CustomerNoteController.php`
8. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/customers/{customerId}/notes` - Get customer notes
- `GET /api/v1/leads/{leadId}/notes` - Get lead notes
- `POST /api/v1/customer-notes` - Create note
- `PUT /api/v1/customer-notes/{id}` - Update note
- `DELETE /api/v1/customer-notes/{id}` - Delete note

---

#### Step 4.2: Follow-up Module
**Files to create:**
1. `database/migrations/2024_01_01_000013_create_follow_ups_table.php`
2. `app/Models/FollowUp.php`
3. `app/Http/Actions/FollowUp/GetFollowUpsAction.php`
4. `app/Http/Actions/FollowUp/GetFollowUpAction.php`
5. `app/Http/Actions/FollowUp/CreateFollowUpAction.php`
6. `app/Http/Actions/FollowUp/UpdateFollowUpAction.php`
7. `app/Http/Actions/FollowUp/CompleteFollowUpAction.php`
8. `app/Http/Actions/FollowUp/DeleteFollowUpAction.php`
9. `app/Http/Controllers/Api/FollowUpController.php`
10. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/follow-ups` - List follow-ups (with filters)
- `GET /api/v1/follow-ups/{id}` - Get single follow-up
- `POST /api/v1/follow-ups` - Create follow-up
- `PUT /api/v1/follow-ups/{id}` - Update follow-up
- `POST /api/v1/follow-ups/{id}/complete` - Mark as completed
- `DELETE /api/v1/follow-ups/{id}` - Delete follow-up

---

#### Step 4.3: SMS Template Module
**Files to create:**
1. `database/migrations/2024_01_01_000014_create_sms_templates_table.php`
2. `app/Models/SmsTemplate.php`
3. `app/Http/Actions/SmsTemplate/GetSmsTemplatesAction.php`
4. `app/Http/Actions/SmsTemplate/GetSmsTemplateAction.php`
5. `app/Http/Actions/SmsTemplate/CreateSmsTemplateAction.php`
6. `app/Http/Actions/SmsTemplate/UpdateSmsTemplateAction.php`
7. `app/Http/Actions/SmsTemplate/DeleteSmsTemplateAction.php`
8. `app/Http/Controllers/Api/SmsTemplateController.php`
9. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/sms-templates` - List templates
- `GET /api/v1/sms-templates/{id}` - Get single template
- `POST /api/v1/sms-templates` - Create template
- `PUT /api/v1/sms-templates/{id}` - Update template
- `DELETE /api/v1/sms-templates/{id}` - Delete template

---

#### Step 4.4: SMS Message Module
**Files to create:**
1. `database/migrations/2024_01_01_000015_create_sms_messages_table.php`
2. `app/Models/SmsMessage.php`
3. `app/Http/Actions/SmsMessage/GetSmsMessagesAction.php`
4. `app/Http/Actions/SmsMessage/SendSmsAction.php`
5. `app/Http/Actions/SmsMessage/SendBulkSmsAction.php`
6. `app/Http/Actions/SmsMessage/GetSmsStatusAction.php`
7. `app/Http/Controllers/Api/SmsMessageController.php`
8. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/sms-messages` - List SMS messages
- `POST /api/v1/sms-messages/send` - Send single SMS
- `POST /api/v1/sms-messages/send-bulk` - Send bulk SMS
- `GET /api/v1/sms-messages/{id}/status` - Get SMS status

---

### **Phase 5: Call Integration** (Week 8-9)

#### Step 5.1: Call Log Module
**Files to create:**
1. `database/migrations/2024_01_01_000016_create_call_logs_table.php`
2. `app/Models/CallLog.php`
3. `app/Http/Actions/CallLog/GetCallLogsAction.php`
4. `app/Http/Actions/CallLog/GetCallLogAction.php`
5. `app/Http/Actions/CallLog/CreateCallLogAction.php`
6. `app/Http/Actions/CallLog/UpdateCallLogAction.php`
7. `app/Http/Actions/CallLog/GetCustomerByPhoneAction.php`
8. `app/Http/Controllers/Api/CallLogController.php`
9. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/call-logs` - List call logs
- `GET /api/v1/call-logs/{id}` - Get single call log
- `POST /api/v1/call-logs` - Create call log
- `PUT /api/v1/call-logs/{id}` - Update call log
- `GET /api/v1/call-logs/phone/{phoneNumber}` - Get customer by phone

---

#### Step 5.2: Call Transcript Module
**Files to create:**
1. `database/migrations/2024_01_01_000017_create_call_transcripts_table.php`
2. `app/Models/CallTranscript.php`
3. `app/Http/Actions/CallTranscript/GetCallTranscriptAction.php`
4. `app/Http/Actions/CallTranscript/CreateCallTranscriptAction.php`
5. `app/Http/Actions/CallTranscript/UpdateCallTranscriptAction.php`
6. `app/Http/Actions/CallTranscript/ProcessAudioAction.php`
7. `app/Http/Controllers/Api/CallTranscriptController.php`
8. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/call-logs/{callLogId}/transcript` - Get transcript
- `POST /api/v1/call-transcripts` - Create transcript
- `PUT /api/v1/call-transcripts/{id}` - Update transcript
- `POST /api/v1/call-transcripts/process-audio` - Process audio to text

---

### **Phase 6: Sync & System** (Week 10-11)

#### Step 6.1: Sync Module
**Files to create:**
1. `database/migrations/2024_01_01_000018_create_sync_queue_table.php`
2. `app/Models/SyncQueue.php`
3. `app/Http/Actions/Sync/PushSyncAction.php`
4. `app/Http/Actions/Sync/PullSyncAction.php`
5. `app/Http/Actions/Sync/GetSyncStatusAction.php`
6. `app/Http/Actions/Sync/ResolveConflictAction.php`
7. `app/Http/Controllers/Api/SyncController.php`
8. Add routes in `routes/api.php`

**Routes:**
- `POST /api/v1/sync/push` - Push offline data
- `POST /api/v1/sync/pull` - Pull server data
- `GET /api/v1/sync/status` - Get sync status
- `POST /api/v1/sync/resolve-conflict` - Resolve conflict

---

#### Step 6.2: Backup Module
**Files to create:**
1. `database/migrations/2024_01_01_000019_create_backups_table.php`
2. `app/Models/Backup.php`
3. `app/Http/Actions/Backup/CreateBackupAction.php`
4. `app/Http/Actions/Backup/GetBackupsAction.php`
5. `app/Http/Actions/Backup/UploadToDriveAction.php`
6. `app/Http/Actions/Backup/RestoreBackupAction.php`
7. `app/Http/Controllers/Api/BackupController.php`
8. Add routes in `routes/api.php`

**Routes:**
- `POST /api/v1/backups/create` - Create backup
- `GET /api/v1/backups` - List backups
- `POST /api/v1/backups/{id}/upload` - Upload to Google Drive
- `POST /api/v1/backups/{id}/restore` - Restore backup

---

### **Phase 7: Reporting & Dashboard** (Week 12)

#### Step 7.1: Report Module
**Files to create:**
1. `database/migrations/2024_01_01_000021_create_reports_table.php`
2. `app/Models/Report.php`
3. `app/Http/Actions/Report/GetReportsAction.php`
4. `app/Http/Actions/Report/GenerateReportAction.php`
5. `app/Http/Actions/Report/ExportReportAction.php`
6. `app/Http/Controllers/Api/ReportController.php`
7. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/reports` - List reports
- `POST /api/v1/reports/generate` - Generate report
- `GET /api/v1/reports/{id}/export` - Export report

---

#### Step 7.2: Dashboard Module
**Files to create:**
1. `app/Http/Actions/Dashboard/GetDashboardStatsAction.php`
2. `app/Http/Actions/Dashboard/GetLeadStatsAction.php`
3. `app/Http/Actions/Dashboard/GetCustomerStatsAction.php`
4. `app/Http/Actions/Dashboard/GetFollowUpStatsAction.php`
5. `app/Http/Controllers/Api/DashboardController.php`
6. Add routes in `routes/api.php`

**Routes:**
- `GET /api/v1/dashboard/stats` - Get dashboard statistics
- `GET /api/v1/dashboard/leads` - Get lead statistics
- `GET /api/v1/dashboard/customers` - Get customer statistics
- `GET /api/v1/dashboard/follow-ups` - Get follow-up statistics

---

### **Phase 8: Additional Tables** (Week 13)

#### Step 8.1: Supporting Tables
**Files to create:**
1. `database/migrations/2024_01_01_000020_create_saved_filters_table.php`
2. `database/migrations/2024_01_01_000022_create_predictions_table.php`
3. `database/migrations/2024_01_01_000023_create_activity_logs_table.php`
4. `database/migrations/2024_01_01_000024_create_devices_table.php`
5. `database/migrations/2024_01_01_000025_create_user_settings_table.php`
6. `database/migrations/2024_01_01_000026_create_roles_table.php`
7. `database/migrations/2024_01_01_000027_create_permissions_table.php`
8. `database/migrations/2024_01_01_000028_create_role_user_table.php`
9. `database/migrations/2024_01_01_000029_create_permission_role_table.php`

**Models to create:**
- `app/Models/SavedFilter.php`
- `app/Models/Prediction.php`
- `app/Models/ActivityLog.php`
- `app/Models/Device.php`
- `app/Models/UserSetting.php`
- `app/Models/Role.php`
- `app/Models/Permission.php`

---

## 📋 Development Checklist Template

For each module, follow this checklist:

### Migration
- [ ] Create migration file
- [ ] Define table structure
- [ ] Add indexes
- [ ] Add foreign keys
- [ ] Test migration (up/down)

### Model
- [ ] Create model file
- [ ] Add fillable fields
- [ ] Add relationships
- [ ] Add soft deletes (if needed)
- [ ] Add casts (if needed)

### Actions
- [ ] Create action folder
- [ ] Create Get action (list/single)
- [ ] Create Create action
- [ ] Create Update action
- [ ] Create Delete action (if needed)
- [ ] Add validation
- [ ] Add error handling

### Controller
- [ ] Create controller file
- [ ] Add methods (index, show, store, update, destroy)
- [ ] Call actions from controller
- [ ] Return JSON response

### Routes
- [ ] Add routes in `routes/api.php`
- [ ] Add middleware (auth:api)
- [ ] Test routes with Postman

---

## 🎯 Quick Reference: File Creation Order

**Week 1:**
1. Create Actions folder structure
2. Category module (migration → model → actions → controller → routes)
3. Address module

**Week 2:**
4. Customer Group module
5. Interested Type module
6. Profession module

**Week 3:**
7. Customer module
8. Project module

**Week 4:**
9. Lead module
10. Pricing module

**Week 5:**
11. Customer Note module
12. Follow-up module

**Week 7:**
15. SMS Template module
16. SMS Message module

**Week 8:**
17. Call Log module
18. Call Transcript module

**Week 9:**
19. Sync module
20. Backup module

**Week 10:**
21. Report module
22. Dashboard module
23. Supporting tables & models

---

## 📝 Notes

1. **Always follow**: Route → Controller → Action → Return Data
2. **No business logic in Controller** - All in Actions
3. **No Repository pattern** - Direct DB queries in Actions
4. **Use Eloquent ORM** - Simple and direct
5. **API Versioning**: All routes under `/api/v1/`
6. **Authentication**: Use `auth:api` middleware (JWT)
7. **Response Format**: Always return JSON
8. **Error Handling**: Use try-catch in Actions, return error response

---

**Total Files to Create**: ~150+ files  
**Estimated Time**: 12-13 weeks  
**Start Date**: ___________  
**Target Completion**: ___________

