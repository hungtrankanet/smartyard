# E2E Test Infra: Suntransco Smart Member Management Module

## Test Philosophy
- Opaque-box, requirement-driven automated verification.
- Methodology: 4-Tier Test Strategy (Feature Coverage, Boundary/Corner, Cross-Feature, Real-World Workload).
- Tests must verify DB migrations, seeds, AI OCR service fallback & extraction, FilePond upload flow, member CRUD, 2-column OCR confirmation, headless verification cURL requests, cron runner & 6-month scheduling, and admin navigation.

## Feature Inventory
| # | Feature | Source (requirement) | Tier 1 | Tier 2 | Tier 3 | Tier 4 |
|---|---------|---------------------|:------:|:------:|:------:|:------:|
| 1 | DB Schema & 15 Industry Seeds | ORIGINAL_REQUEST §R1 | 5 | 5 | ✓ | ✓ |
| 2 | Model Layer & FK Relations | ORIGINAL_REQUEST §R1 | 5 | 5 | ✓ | ✓ |
| 3 | AI OCR Gemini & JSON Stub | ORIGINAL_REQUEST §R2 | 5 | 5 | ✓ | ✓ |
| 4 | FilePond JS Batch Upload Flow | ORIGINAL_REQUEST §R2 | 5 | 5 | ✓ | ✓ |
| 5 | Confirm OCR & Save Member (2-Col) | ORIGINAL_REQUEST §R3 | 5 | 5 | ✓ | ✓ |
| 6 | Headless Business Verification | ORIGINAL_REQUEST §R4 | 5 | 5 | ✓ | ✓ |
| 7 | Admin Member CRUD Views (index, form, detail) | ORIGINAL_REQUEST §R5 | 5 | 5 | ✓ | ✓ |
| 8 | Cron 6-Month Auto-Verify & Email | ORIGINAL_REQUEST §R6 | 5 | 5 | ✓ | ✓ |
| 9 | Routing & Admin Sidebar Navigation | ORIGINAL_REQUEST §R7 | 5 | 5 | ✓ | ✓ |

## Test Architecture
- **Automated Runner**: `tests/test_member_module.php` (CLI test harness executed via PHP CLI / spark).
- **Pass/Fail Semantics**: All assertions must pass (exit code 0).
- **Test Categories**:
  - **Tier 1 (Feature Coverage)**: Individual component verification (DB tables exist, 15 industries present, models instantiate and query correctly, OCR service stub returns 10 fields, BusinessVerifyService parses Google Maps signals, CRUD operations succeed, Cron method processes due members).
  - **Tier 2 (Boundary & Corner Cases)**: Empty/invalid inputs, missing Gemini key fallback, fanpage 404 vs 200, closed signal detection, duplicate cards, unverified member handling, expired vs unexpired cron query filtering.
  - **Tier 3 (Cross-Feature Combinations)**: Full workflow from FilePond upload -> OCR extraction -> 2-column confirmation -> DB save with `next_verify_at = NOW()+6m` -> instant verification -> verify logs updated -> cron execution.
  - **Tier 4 (Real-World Scenarios)**: High-load simulation of multi-card batch imports, concurrent member searches, filtering by industry & verify_status, and automated email trigger on closed business status.

## Coverage Thresholds
- Tier 1: >= 5 per feature
- Tier 2: >= 5 per feature
- Tier 3: >= 9 pairwise / cross-feature workflow tests
- Tier 4: >= 5 realistic business workload scenarios
