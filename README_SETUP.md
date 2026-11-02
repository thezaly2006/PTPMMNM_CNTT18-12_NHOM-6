# HƯỚNG DẪN CÀI ĐẶT & CHẠY DỰ ÁN TRÊN UBUNTU 26.04 WSL

Tài liệu này hướng dẫn bạn cài đặt môi trường và khởi chạy ứng dụng **PDF Workspace** trên Ubuntu 26.04 (hoặc bất kỳ bản phân phối Ubuntu nào trên WSL/Linux).

---

## BƯỚC 1: Cập nhật hệ thống & Cài đặt các gói cần thiết

Mở terminal Ubuntu WSL và chạy lệnh sau:

```bash
sudo apt update && sudo apt upgrade -y
```

Cài đặt Apache, PHP 8.x, các extension quan trọng, công cụ xử lý PDF `qpdf`, MariaDB và Composer:

```bash
sudo apt install -y apache2 libapache2-mod-php \
    php php-cli php-fpm php-mysql php-mbstring php-xml php-curl php-zip php-gd \
    mariadb-server \
    composer \
    qpdf
```

> **Giải thích:**
> - `php-mysql`: Cho phép PHP kết nối tới MySQL/MariaDB qua PDO.
> - `php-mbstring`, `php-xml`: Hỗ trợ xử lý chuỗi UTF-8 tiếng Việt và tài liệu XML/PDF.
> - `qpdf`: Công cụ mã nguồn mở cực kỳ mạnh mẽ trên Linux giúp giải mã và chuẩn hóa các luồng dữ liệu PDF v1.5+ để thư viện PHP FPDI xử lý không bị lỗi.
> - `mariadb-server`: Hệ quản trị cơ sở dữ liệu quan hệ mã nguồn mở.

---

## BƯỚC 2: Khởi động dịch vụ MySQL / MariaDB

```bash
sudo service mysql start
```

Kiểm tra trạng thái dịch vụ:

```bash
sudo service mysql status
```

---

## BƯỚC 3: Di chuyển vào thư mục dự án

Thư mục Windows `d:\PhatTrienPhanMemMaNguonMo\DeTai12` được gắn (mount) tự động trong WSL tại:

```bash
cd /mnt/d/PhatTrienPhanMemMaNguonMo/DeTai12
```

---

## BƯỚC 4: Cài đặt thư viện PHP bằng Composer

Tại thư mục dự án `/mnt/d/PhatTrienPhanMemMaNguonMo/DeTai12`, chạy lệnh:

```bash
composer install
```

Lệnh này sẽ tải thư viện `setasign/fpdi` và `setasign/fpdf` vào thư mục `vendor/`.

---

## BƯỚC 5: Khởi tạo Cơ sở dữ liệu và Dữ liệu mẫu

Chạy 2 lệnh sau để tạo 8 bảng và nạp tài khoản thử nghiệm:

```bash
sudo mysql < database/schema.sql
sudo mysql < database/seed.sql
```

Tạo một tài khoản người dùng database riêng (hoặc cấp quyền cho root) để kết nối từ ứng dụng:

```bash
sudo mysql -e "CREATE USER IF NOT EXISTS 'pdfuser'@'localhost' IDENTIFIED BY 'password123';"
sudo mysql -e "GRANT ALL PRIVILEGES ON pdf_workspace.* TO 'pdfuser'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

Sau đó, chỉnh sửa file `.env` nếu cần (hoặc dùng tài khoản `pdfuser` vừa tạo):

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pdf_workspace
DB_USERNAME=pdfuser
DB_PASSWORD=password123
```

---

## BƯỚC 6: Phân quyền thư mục lưu trữ (Storage)

Đảm bảo thư mục lưu file và log có quyền ghi:

```bash
chmod -R 777 storage/
```

---

## BƯỚC 7: Khởi chạy ứng dụng

Bạn có 2 cách chạy:

### Cách 1: Sử dụng máy chủ tích hợp PHP (Nhanh nhất cho kiểm thử & demo)

Tại thư mục `/mnt/d/PhatTrienPhanMemMaNguonMo/DeTai12`, gõ:

```bash
php -d upload_max_filesize=100M -d post_max_size=100M -d memory_limit=512M -S 0.0.0.0:8080 -t public
```

*(Hoặc chạy nhanh file script: `bash start.sh`)*

Sau đó mở trình duyệt trên Windows truy cập:
👉 **http://localhost:8080**

### Cách 2: Cấu hình qua Apache VirtualHost

1. Tạo file cấu hình VirtualHost:
```bash
sudo nano /etc/apache2/sites-available/pdf-workspace.conf
```

2. Dán nội dung sau:
```apache
<VirtualHost *:80>
    ServerName pdf-workspace.local
    DocumentRoot /mnt/d/PhatTrienPhanMemMaNguonMo/DeTai12/public

    <Directory /mnt/d/PhatTrienPhanMemMaNguonMo/DeTai12/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/pdf_workspace_error.log
    CustomLog ${APACHE_LOG_DIR}/pdf_workspace_access.log combined
</VirtualHost>
```

3. Bật module rewrite và kích hoạt site:
```bash
sudo a2enmod rewrite
sudo a2ensite pdf-workspace.conf
sudo service apache2 restart
```

4. Thêm `127.0.0.1 pdf-workspace.local` vào file `hosts` của Windows và truy cập `http://pdf-workspace.local`.
