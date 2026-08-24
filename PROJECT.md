# Project: SMART YARD PETRO — Visual Warehouse & Area Allocation Platform

## Architecture & Core Directives
- **Framework**: CodeIgniter 4 (PHP 8.1+, MariaDB/MySQL 8)
- **Deployment Targets**: Docker multi-container (`web`, `mariadb`, `phpmyadmin`) ready for load-balanced scaling.
- **Database**: High-concurrency relational schema (`install/sql/smartyard_schema.sql`) with optimized compound indexes.
- **Media & Storage**: Decoupled media uploads directory for independent horizontal scaling.
- **Scope RBAC**: Multi-tier permission isolation (`User` -> `Role` -> `Warehouse Scope` -> `Area Allocation`).
- **Atomic Concurrency Engine**: Atomic database transactions ensuring `used_area <= allocated_area` and `export_area <= lot.remaining_area`.
- **2D Map & 3D Preview**: Interactive multi-region 2D canvas with real-time color threshold mapping and fixed 3D representative warehouse view.
- **AI Assistant**: Natural language query engine strictly protected by RBAC context filtering.
- **Governance**: 100% codebase strictly <= 500 lines per file.

## Feature Inventory
| # | Feature | Scope | Status |
|---|---------|-------|--------|
| 1 | RBAC Scope Authorization | `SmartYardRbacService`, `SmartYardUserScopeModel` | Completed |
| 2 | 2D Multi-Region Map Canvas | `SmartYardMapController`, `map/index.php` | Completed |
| 3 | Warehouse 3D Representative View | Modal detail drawer, 3D representative asset | Completed |
| 4 | Atomic Lot Import Engine | `SmartYardInventoryService::importLot()`, `import.php` | Completed |
| 5 | Atomic Lot Export Engine | `SmartYardInventoryService::exportLot()`, `export.php` | Completed |
| 6 | Immutable Audit & Transactions | `SmartYardTransactionModel`, `transactions.php` | Completed |
| 7 | Executive Management Dashboard | `SmartYardDashboardController`, `dashboard/index.php` | Completed |
| 8 | Large Screen / Touchscreen Kiosk | `dashboard/kiosk.php` (Readonly, auto-refresh) | Completed |
| 9 | AI Assistant with RBAC Guardrail | `SmartYardAiService`, floating interactive widget | Completed |
| 10 | Dynamic Color Threshold Config | `SmartYardAdminController`, `settings.php` | Completed |
| 11 | Initial Excel Batch Import | `admin/excel_import.php` (5-stage workflow) | Completed |
| 12 | 15/15 Automated E2E Test Suite | `tests/SmartYard/run_smartyard_e2e_tests.php` | Completed |
