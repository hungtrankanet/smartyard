# Project: Suntransco CodeIgniter 4 Smart Member Management Module

## Architecture
- **Framework**: CodeIgniter 4 (v4.5.7), PHP 8.1+ / Apache / MySQL 8.0 (`suntransco_db`)
- **Controller Pattern**: Extends `BaseAdminController`, uses `view('admin/includes/_header', $data)`, `view('admin/members/...', $data)`, `view('admin/includes/_footer')`.
- **Auth & Permissions**: Uses CodeIgniter 4 `['filter' => 'auth']` and `checkPermission('members')` or admin auth checking.
- **Data Models**:
  - `App\Models\IndustryTypeModel` (table: `industry_types`)
  - `App\Models\MemberModel` (table: `members`)
  - `App\Models\MemberCardModel` (table: `member_cards`)
  - `App\Models\MemberVerifyLogModel` (table: `member_verify_logs`)
- **Services & Libraries**:
  - `App\Libraries\OcrService`: Gemini 1.5 Flash Vision API integration via cURL + base64 image + JSON mode, fallback mock stub when `GEMINI_API_KEY` is not present.
  - `App\Libraries\BusinessVerifyService`: Pure PHP cURL + DOM parsing / regex search signals for Google Maps & Fanpage, sequential with `sleep(2)` throttling.
- **Frontend / UX**:
  - FilePond JS via CDN (Drag & drop, multi-file, front/back grouping selector).
  - 2-Column Responsive Layout for OCR confirmation (zoomable image on left, editable form on right).
  - Bootstrap 3 / AdminLTE UI matching existing Varient admin theme.
- **Scalability & Concurrency**:
  - Max 500 lines per file constraint strictly enforced.
  - DB index optimization on `industry_type_id`, `verify_status`, `member_type`, `next_verify_at`, `status`.
  - Media decoupled under `public/uploads/cards/` (docker volume ready).

## Feature Inventory
| # | Feature | Description | Milestone | Source | Status |
|---|---------|-------------|-----------|--------|--------|
| 1 | DB Migration & Seeds | 4 tables (`industry_types`, `members`, `member_cards`, `member_verify_logs`) + 15 seed industries | M1 | ORIGINAL_REQUEST §R1 | DONE |
| 2 | Models Layer | 4 CI4 Models with validation, relationships, and helper queries | M1 | ORIGINAL_REQUEST §R1 | DONE |
| 3 | AI OCR Service | `OcrService.php` with Gemini 1.5 Flash Vision API & JSON stub fallback | M2 | ORIGINAL_REQUEST §R2 | DONE |
| 4 | FilePond Batch Upload UI | `upload_cards.php` with FilePond CDN, front/back selection, sequential AJAX upload | M2 | ORIGINAL_REQUEST §R2 | DONE |
| 5 | Confirm OCR & Save Member | `confirm_ocr.php` 2-column zoom UI, industry/type dropdowns, save with next_verify_at = NOW()+6m | M3 | ORIGINAL_REQUEST §R3 | DONE |
| 6 | Headless Business Verification | `BusinessVerifyService.php` with Google Maps & Fanpage crawler, sequential sleep(2), verify logs | M4 | ORIGINAL_REQUEST §R4 | DONE |
| 7 | Admin CRUD Views & UI | `index.php` (20 pagination, filters, search), `form.php` (create/edit), `detail.php` (timeline & verify button) | M5 | ORIGINAL_REQUEST §R5 | DONE |
| 8 | Routing & Sidebar Navigation | Routes in `Routes.php` with auth filter, sidebar menu in `_header.php` | M5 | ORIGINAL_REQUEST §R7 | DONE |
| 9 | Cron Job & Email Alert | `CronController::verifyMembers()`, 6-month scheduling, closed alert email, token-secured route | M6 | ORIGINAL_REQUEST §R6 | DONE |
| 10 | E2E Automated Test Suite | Comprehensive test suite covering all tiers and acceptance criteria | M_E2E | ORIGINAL_REQUEST §Verification | DONE |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | M1_Database_Models | 4 Migration files, 15 Industry seeds, 4 Model classes | none | DONE |
| 2 | M2_AI_OCR_Upload | `OcrService.php`, `upload_cards.php`, AJAX upload controller | M1 | DONE |
| 3 | M3_Confirm_OCR_Save | `confirm_ocr.php`, Member creation/saving & card attachment | M1, M2 | DONE |
| 4 | M4_Business_Verification | `BusinessVerifyService.php`, "Xác Minh Ngay" manual verify endpoint | M1 | DONE |
| 5 | M5_Admin_CRUD_Nav | `MemberController.php`, `index.php`, `form.php`, `detail.php`, `Routes.php`, `_header.php` | M1, M2, M3, M4 | DONE |
| 6 | M6_Cron_Job | `CronController::verifyMembers()`, token security, email alert | M1, M4 | DONE |
| 7 | M_E2E_Final_Suite | Automated test suite execution, 100% pass verification | M1, M2, M3, M4, M5, M6 | DONE |

## Interface Contracts
### `App\Libraries\OcrService`
- `extractBusinessCard(string|array $imagePaths): array`
  - Input: single image path or `['front' => string, 'back' => string]`
  - Output: `['company_name' => '', 'tax_code' => '', 'address' => '', 'city' => '', 'website' => '', 'fanpage' => '', 'phone' => '', 'email' => '', 'representative_name' => '', 'position' => '']`

### `App\Libraries\BusinessVerifyService`
- `verifyMember(int $memberId): array`
  - Runs Google Maps check & Fanpage check sequentially with `sleep(2)`.
  - Logs results to `member_verify_logs`.
  - Updates `members.verify_status` ('verified' | 'unverified' | 'failed') and `members.last_verified_at`.
  - Returns `['status' => 'verified'|'unverified'|'failed', 'maps_result' => string, 'fanpage_result' => string, 'details' => array]`
- `verifyBatch(array $memberIds): array`
  - Iterates through members sequentially with `sleep(2)` between checks.

### `App\Controllers\CronController::verifyMembers()`
- Query parameter: `?token={CRON_SECRET_TOKEN}` (default fallback token configured in `.env` / app config)
- Scans `members` where `next_verify_at <= NOW()` AND `status = 1`
- Runs `BusinessVerifyService`, updates `next_verify_at = NOW() + 6 months`
- Sends alert email if result indicates `closed`

## Code Layout
- `app/Database/Migrations/`
  - `2026-08-16-000001_CreateIndustryTypes.php`
  - `2026-08-16-000002_CreateMembers.php`
  - `2026-08-16-000003_CreateMemberCards.php`
  - `2026-08-16-000004_CreateMemberVerifyLogs.php`
- `app/Models/`
  - `IndustryTypeModel.php`
  - `MemberModel.php`
  - `MemberCardModel.php`
  - `MemberVerifyLogModel.php`
- `app/Libraries/`
  - `OcrService.php`
  - `BusinessVerifyService.php`
- `app/Controllers/`
  - `MemberController.php`
  - `CronController.php` (with `verifyMembers()`)
- `app/Views/admin/members/`
  - `index.php`
  - `form.php`
  - `upload_cards.php`
  - `confirm_ocr.php`
  - `detail.php`
- `app/Views/admin/includes/`
  - `_header.php` (sidebar navigation)
- `app/Views/email/`
  - `email_business_closed_alert.php`
- `app/Config/`
  - `Routes.php` (admin routes & cron route)
- `tests/`
  - `test_member_module.php`
