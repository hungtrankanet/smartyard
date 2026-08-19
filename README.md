# TOP BEST GLOBAL — Logistics & B2B Enterprise Platform

Hệ thống nền tảng số hóa E-Carrier, quản lý chuỗi cung ứng logistics đa phương thức, sàn kết nối đối tác B2B và bản tin thương mại quốc tế.

---

## 🏗️ Kiến Trúc Hệ Thống (Architecture & High Availability)
- **Framework**: CodeIgniter 4 (PHP 8.1+)
- **Containerization**: Docker & Docker Compose (tách biệt Web Container, MySQL 8 Container, phpMyAdmin)
- **Scalability**: Thiết kế phân tách Database, Media Uploads và Application Core sẵn sàng tích hợp Load Balancer (Nginx / HAProxy / Cloudflare) chịu tải 10,000+ kết nối đồng thời.
- **Background Jobs**: Quét định kỳ tự động xác minh pháp nhân qua Cron Token an toàn.

---

## 🚀 Hướng Dẫn Triển Khai Nhanh Trên VPS (Deployment Guide)

### 1. Chuẩn Bị Trên VPS
Yêu cầu: VPS Linux (Ubuntu 20.04/22.04 LTS hoặc Debian), đã cài đặt **Docker** & **Docker Compose** và **Git**.

```bash
# Clone repository về VPS
git clone https://github.com/your-username/topbestglobal.git /var/www/topbestglobal
cd /var/www/topbestglobal

# Cấu hình file môi trường từ template
cp .env.example .env
nano .env # Thay đổi mật khẩu DB, APP BaseURL và domain của bạn
```

### 2. Phân Quyền Thư Mục Writable & Uploads
```bash
chmod -R 777 writable uploads
```

### 3. Khởi Chạy Container Docker
```bash
docker compose up -d --build
```

Kiểm tra trạng thái container:
```bash
docker compose ps
```

- **Web Application**: `http://<YOUR_VPS_IP>:8080`
- **phpMyAdmin**: `http://<YOUR_VPS_IP>:8081`

---

## 🗄️ Khởi Tạo Cơ Sở Dữ Liệu (Database Initialization)
1. Truy cập phpMyAdmin tại cổng `8081` hoặc kết nối MySQL CLI.
2. Import file database khởi tạo:
   - `install/sql/install_varient.sql` (Schema và dữ liệu nền tảng).
   - `migrate_members.sql` (Cấu trúc B2B Member Portal & Business Verification).
3. Reset hoặc thiết lập tài khoản Admin đầu tiên bằng lệnh:
   `GET http://<YOUR_DOMAIN>:8080/resetAdminCredentials?key=topbestglobal_secret_reset_2026`

---

## ⏰ Cấu Hình Cron Jobs Trên VPS
Thêm tác vụ cron quét định kỳ tự động cho đối tác hội viên (chạy lúc 00:00 ngày mùng 1 hàng tháng):

```bash
crontab -e
```
Thêm dòng sau:
```cron
0 0 1 * * curl -s "http://127.0.0.1:8080/cron/verify-members?token=topbestglobal_cron_verify_token_2026" > /dev/null 2>&1
```

---

## 🛡️ Bản Quyền & Giấy Phép
© 2026 TOP BEST GLOBAL Corporation. All rights reserved.
