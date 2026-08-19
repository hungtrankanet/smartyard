# TOP BEST GLOBAL — Logistics & B2B Enterprise Platform

Hệ thống nền tảng số hóa E-Carrier, quản lý chuỗi cung ứng logistics đa phương thức, sàn kết nối đối tác B2B và bản tin thương mại quốc tế.

---

## 🏗️ Kiến Trúc Hệ Thống (Architecture & High Availability)
- **Framework**: CodeIgniter 4 (PHP 8.1+)
- **Containerization**: Docker & Docker Compose (tách biệt Web Container, MySQL 8 Container, phpMyAdmin)
- **Scalability**: Thiết kế phân tách Database, Media Uploads và Application Core sẵn sàng tích hợp Load Balancer (Nginx / HAProxy / Cloudflare) chịu tải 10,000+ kết nối đồng thời.
- **Background Jobs**: Quét định kỳ tự động xác minh pháp nhân qua Cron Token an toàn.

---

## 🚀 CI/CD Tự Động Triển Khai (GitHub Actions -> VPS)
Dự án đã tích hợp sẵn GitHub Actions workflow tại [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml). Mỗi khi bạn `git push` lên nhánh `main`, GitHub sẽ tự động kiểm tra cú pháp và kết nối SSH tới VPS để tự động pull code và khởi động lại Docker!

### 🔑 Cấu hình Secrets trên GitHub:
Vào **GitHub Repository** &rarr; **Settings** &rarr; **Secrets and variables** &rarr; **Actions** &rarr; **New repository secret**:
1. `VPS_HOST`: Địa chỉ IP hoặc Domain VPS (VD: `103.x.x.x`).
2. `VPS_USERNAME`: Tên người dùng SSH (VD: `root` hoặc `ubuntu`).
3. `VPS_SSH_KEY` (hoặc `VPS_PASSWORD`): Khóa SSH Private Key hoặc mật khẩu VPS.
4. `VPS_PORT`: Cổng SSH (mặc định `22`).
5. `VPS_TARGET_DIR`: Thư mục lưu dự án trên VPS (mặc định `/var/www/topbestglobal`).

---

## 🛠️ Hướng Dẫn Triển Khai Thủ Công Trên VPS (Manual Setup)

### 1. Chuẩn Bị Trên VPS
Yêu cầu: VPS Linux (Ubuntu 20.04/22.04 LTS hoặc Debian), đã cài đặt **Docker** & **Docker Compose** và **Git**.

```bash
# Clone repository về VPS
git clone https://github.com/hungtrankanet/topbestglobal.git /var/www/topbestglobal
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

- **Web Application (Container Proxy)**: `http://<YOUR_VPS_IP>:3240`
- **phpMyAdmin**: `http://<YOUR_VPS_IP>:8001`

---

## 🌐 Cấu Hình Nginx Reverse Proxy (Khuyên Dùng cho Production)
Để chạy domain chính thức kèm SSL (HTTPS), bạn cấu hình Nginx trên VPS như sau:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;

    location / {
        proxy_pass http://127.0.0.1:3240;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        client_max_body_size 100M;
    }
}
```

Cài đặt chứng chỉ SSL miễn phí với Certbot:
```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

---

## 🗄️ Khởi Tạo Cơ Sở Dữ Liệu Tự Động (Auto Database Initialization)
Khi chạy `docker compose up -d`, Docker MySQL Container sẽ **tự động nạp toàn bộ cấu trúc & tài khoản quản trị**:
1. `install/sql/install_varient.sql` (Cơ sở dữ liệu cốt lõi).
2. `migrate_members.sql` (Cấu trúc B2B Member Portal & Business Verification).
3. `init_db.sql` (Kích hoạt Theme TOP BEST GLOBAL & tạo tài khoản Super Admin).

### 🔑 Thông Tin Đăng Nhập Mặc Định:
- **Đường dẫn đăng nhập Admin**: `http://<YOUR_DOMAIN_OR_IP>:3240/admin/login`
- **Email**: `admin@topbestglobal.com` (hoặc `admin@gmail.com`)
- **Mật khẩu**: `TopBestGlobal@2026`

*(Nếu cần reset mật khẩu nhanh từ xa, truy cập: `http://<YOUR_DOMAIN_OR_IP>:3240/resetAdminCredentials?key=topbestglobal_secret_reset_2026`)*

---

## ⏰ Cấu Hình Cron Jobs Trên VPS
Thêm tác vụ cron quét định kỳ tự động cho đối tác hội viên (chạy lúc 00:00 ngày mùng 1 hàng tháng):

```bash
crontab -e
```
Thêm dòng sau:
```cron
0 0 1 * * curl -s "http://127.0.0.1:3240/cron/verify-members?token=topbestglobal_cron_verify_token_2026" > /dev/null 2>&1
```

---

## 🛡️ Bản Quyền & Giấy Phép
© 2026 TOP BEST GLOBAL Corporation. All rights reserved.
