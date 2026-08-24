# SMART YARD PETRO — Visual Warehouse Management & Area Allocation Platform

Nền tảng quản lý trực quan hệ thống kho dầu khí & logistics đa khu vực:
* **Sơ đồ kho 2D trực quan**: Tương tác zoom, pan, hiển thị đa khu vực (Miền Bắc, Miền Trung, Miền Nam).
* **Hình ảnh đại diện kho 3D**: Nhận diện trực quan cấu trúc cố định cửa và khung kho.
* **Quản lý diện tích đa tầng**: `Total Area`, `Allocated Area`, `Used Area`, `Available Area`.
* **Mã màu trạng thái động**: Tự động cảnh báo theo ngưỡng lấp đầy (0-30% Xanh, 30-60% Vàng, 60-80% Cam, >80% Đỏ).
* **Quản lý lô hàng theo dự án**: Nhập/Xuất kho Atomic kiểm soát chặt chẽ giới hạn diện tích khả dụng.
* **Mô hình phân quyền Scope RBAC**: Phân quyền theo cấp `User` -> `Role` -> `Warehouse Scope` -> `Area Allocation`.
* **Dashboard điều hành & Chế độ Sảnh Kiosk**: Tối ưu hiển thị cho màn hình lớn và màn hình cảm ứng sảnh.
* **AI Assistant hỗ trợ truy vấn**: Phân tích gợi ý kho phù hợp với bộ lọc RBAC Scope Guardrail nghiêm ngặt.

---

## 🏗️ Kiến Trúc Hệ Thống (High Availability & Scalability)
- **Framework**: CodeIgniter 4 (PHP 8.1+)
- **Concurrency**: Thiết kế tối ưu hóa chịu tải cho hơn **10.000 truy cập đồng thời** với Indexing thông minh và Atomic Transactions.
- **Tách biệt Database & Media Storage**: Media và file đính kèm được tách rời mã nguồn giúp scale ngang dễ dàng.
- **Docker Containerization**: Đóng gói container độc lập sẵn sàng chạy sau Load Balancer (Nginx / ALB / Traefik).
- **Code Governance**: 100% file mã nguồn tuân thủ giới hạn $\le 500$ dòng.

---

## 🚀 Cài Đặt & Khởi Chạy Nhanh với Docker

```bash
# 1. Clone repository
git clone https://github.com/hungtrankanet/smartyard.git
cd smartyard

# 2. Khởi chạy Docker containers
docker compose up -d --build

# 3. Chạy bộ kiểm thử tự động E2E
php tests/SmartYard/run_smartyard_e2e_tests.php
```

---

## 📊 Các Module Tính Năng Chính
| Module | URL | Mô Tả |
|---|---|---|
| **Sơ đồ 2D & 3D** | `/smartyard/map` | Bản đồ kho tương tác, xem ảnh 3D và danh sách lô |
| **Dashboard Điều Hành** | `/smartyard/dashboard` | Thống kê KPI diện tích, tỷ lệ lấp đầy, dự án |
| **Chế độ Sảnh Kiosk** | `/smartyard/kiosk` | Giao diện toàn màn hình tự động làm mới cho Touchscreen |
| **Nhập Lô Hàng** | `/smartyard/inventory/import` | Form nhập lô với live validator diện tích khả dụng |
| **Xuất Lô Hàng** | `/smartyard/inventory/export` | Form xuất lô hoàn trả diện tích kho |
| **Lô Hàng Hiện Hữu** | `/smartyard/inventory/lots` | Danh sách lô hàng lọc theo kho & dự án |
| **Lịch Sử Giao Dịch** | `/smartyard/inventory/transactions` | Log giao dịch bất biến phục vụ kiểm toán |
| **Phân Quyền Scope** | `/smartyard/admin/scopes` | Gán phạm vi kho cho tài khoản |
| **Cấu Hình Ngưỡng** | `/smartyard/admin/settings` | Điều chỉnh tỷ lệ phần trăm các mức màu trạng thái |
| **Nhập Excel Hàng Loạt** | `/smartyard/admin/excel-import` | Quy trình 5 bước nạp dữ liệu ban đầu |

---

## 🧪 Kiểm Thử Tự Động (E2E Test Harness)
Toàn bộ quy trình nghiệp vụ được xác minh qua test runner:
```bash
php tests/SmartYard/run_smartyard_e2e_tests.php
```
Kết quả: **15/15 PASS (100%)**.

© 2026 Smart Yard Petro. All rights reserved.
