# Project: TOP BEST GLOBAL — National Honors & Voting Portal

## Architecture
- **Framework**: Pure CodeIgniter 4 (PHP 8.1/8.2, Apache, MariaDB)
- **Deployment Targets**: Dual-mode (aaPanel direct `/www/wwwroot/topbestglobal` + Docker container `topbest` port 3240).
- **Database**: MariaDB (`topbestglobal_db`, `tbglobal_user`, `TpaLASHNb3Yw4GeC`) with automatic host detection (`localhost` vs `host.docker.internal`).
- **Media & Storage**: Decoupled cloud/local media uploads directory for horizontal scaling and load balancing.
- **Frontend Design**: Gold Luxury / Deep Blue / Modern Clean palette (`#D4AF37`, `#0A192F`, `#0F2027`), fully responsive, high-prestige National Honors Portal.
- **Voting Engine**: 70% Jury/Expert weighted + 30% Public/Community weighted scoring, multi-tier anti-fraud (Email OTP, Captcha, Rate Limit, Device Fingerprint, Audit Logs), real-time leaderboards & charts.
- **Nomination & Hall of Fame**: 4-stage nomination review pipeline (Sơ khảo -> Thẩm định -> Chung khảo -> Trao giải), Hall of Fame directory, dynamic SVG Digital Award Badges & Certificates.
- **Governance**: Strict <=500 lines per PHP file rule across all custom code.

## Feature Inventory
| # | Feature | Description | Milestone | Source |
|---|---------|-------------|-----------|--------|
| 1 | R1.1 National Honors Branding | Gold Luxury / Deep Blue luxury theme, header, footer, hero banners, honors navigation | M1 | ORIGINAL_REQUEST §R1 |
| 2 | R1.2 Honors Portal Controller & Views | `/honors`, `/honors/categories`, `/honors/seasons`, `/honors/about` views & controller | M1 | ORIGINAL_REQUEST §R1 |
| 3 | R1.3 View Syntax & UI Remediation | Fix syntax errors in `_nav_profile.php`, `voting/list.php`, `voting/detail.php` | M1 | Survey Finding |
| 4 | R2.1 Advanced Voting Engine Core | `VotingEngineService`, `HybridScoringService` 70/30 calculation, `VotingEngineController` | Completed / M3 | ORIGINAL_REQUEST §R2 |
| 5 | R2.2 Anti-Fraud & OTP Security | `AntiFraudSecurityService`, `OtpMailerService`, rate limiting, device fingerprinting, audit logs | Completed / M3 | ORIGINAL_REQUEST §R2 |
| 6 | R2.3 Real-time Leaderboards & Charts | Category rankings, vote percentage bars, real-time public & jury breakdown | Completed / M1 | ORIGINAL_REQUEST §R2 |
| 7 | R3.1 Nomination 4-Stage Workflow | `NominationWorkflowService`, `NominationController`, submission form & status tracking | Completed / M2 | ORIGINAL_REQUEST §R3 |
| 8 | R3.2 Hall of Fame & Digital Certificates | `HallOfFameController`, `CertificateController`, `DigitalCertificateService` SVG badge generation | Completed / M1 | ORIGINAL_REQUEST §R3 |
| 9 | R3.3 Jury Evaluation Portal | `JuryEvaluationController`, `jury/index.php`, `jury/evaluate.php` scoring interfaces | M1 | ORIGINAL_REQUEST §R3 |
| 10 | R3.4 Admin CMS Management | Admin seasons, categories, nominations review, jury management, and sidebar menu | M2 | ORIGINAL_REQUEST §R3 |
| 11 | R4.1 MariaDB Dual Compatibility | aaPanel `localhost` & Docker `host.docker.internal`, credentials `topbestglobal_db` | Completed / M3 | ORIGINAL_REQUEST §R4 |
| 12 | R4.2 Bootstrapping & Auto-Detection | Auto-detect uninitialized DB in `Globals.php` and redirect to `/install/welcome.php` | Completed / M3 | ORIGINAL_REQUEST §R4 |
| 13 | R4.3 SQL Schema Synchronization | Complete 17-table schema synchronized in `install/sql/install_varient.sql` | Completed / M3 | ORIGINAL_REQUEST §R4 |
| 14 | R5.1 Code Governance (<=500 lines) | 100% PHP files under 500 lines enforced by automated gatekeeper | M3 | ORIGINAL_REQUEST §R5 |
| 15 | R5.2 225/225 E2E Automated Test Suite | Comprehensive 8-suite automated test harness passing 100% | M3 | ORIGINAL_REQUEST §Acceptance |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | M1: Portal Frontend Rebranding & UI Polish | Gold Luxury theme in `_header.php`, `index.php`, `services.php`, `about.php`, `HonorsPortalController.php`, `jury/` views, fix view syntax | None | IN_PROGRESS |
| 2 | M2: Admin CMS Modules & Navigation | `AdminAwardSeasonController`, `AdminNominationController`, `AdminJuryController`, admin views, sidebar navigation | M1 | PLANNED |
| 3 | M3: Quality Gate, E2E Verification & Forensic Audit | Run 225/225 E2E tests, PHP syntax lint across all 500+ files, line count gatekeeper, dual Docker/aaPanel validation, Reviewer & Challenger verification, Forensic Audit | M1, M2 | PLANNED |

## Interface Contracts
### Public Portal ↔ Voting Engine
- `VotingEngineController::index()`, `VotingEngineController::category($slug)`, `VotingEngineController::candidate($slug)`
- `VotingApiController::submitVote()`: POST `{candidate_id, email, otp, captcha, fingerprint}` -> JSON `{status, message, total_votes, weighted_score}`
- `VotingApiController::sendOtp()`: POST `{email}` -> JSON `{status, message, token_id}`

### Nomination & Review Workflow
- `NominationWorkflowService::submitNomination($data)` -> creates nomination record in status `submitted` (Sơ khảo)
- `NominationWorkflowService::advanceStage($id, $nextStage)`: `submitted` -> `preliminary` -> `appraisal` -> `final` -> `awarded`
- `JuryEvaluationService::recordScore($juryId, $nominationId, $criteriaScores, $comment)` -> updates jury weighted score (70%)

### Certificate & Hall of Fame
- `DigitalCertificateService::generateSvgBadge($awardTitle, $winnerName, $year, $category)` -> Returns dynamic high-res SVG badge
- `CertificateController::verify($verifyCode)` -> Renders public certificate verification page with cryptographic hash

## Code Layout
- `app/Controllers/HonorsPortalController.php`: Public portal controller (<500 lines).
- `app/Controllers/VotingEngineController.php`: Public voting & candidate pages.
- `app/Controllers/VotingApiController.php`: AJAX API for voting, OTP, leaderboard.
- `app/Controllers/NominationController.php`: Public nomination submission & tracking.
- `app/Controllers/HallOfFameController.php`: Hall of fame showcase & search.
- `app/Controllers/CertificateController.php`: Digital certificate viewer & verification.
- `app/Controllers/JuryEvaluationController.php`: Jury evaluation portal.
- `app/Controllers/AdminAwardSeasonController.php`: Admin award season management.
- `app/Controllers/AdminNominationController.php`: Admin nomination review & scoring.
- `app/Controllers/AdminJuryController.php`: Admin jury judge account & assignment management.
- `app/Services/VotingEngineService.php`: Core voting business logic.
- `app/Services/HybridScoringService.php`: 70% Jury / 30% Public scoring algorithm.
- `app/Services/AntiFraudSecurityService.php`: OTP, rate limit, device fingerprint security.
- `app/Services/NominationWorkflowService.php`: 4-stage review workflow engine.
- `app/Services/DigitalCertificateService.php`: SVG award badge & certificate generator.
- `tests/run_all_e2e_tests.php`: 225/225 automated E2E test runner.
- `tests/gatekeeper_500_lines.php`: 500-line limit validator.
