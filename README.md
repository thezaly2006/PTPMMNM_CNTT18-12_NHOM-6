# PDF WORKSPACE — CÔNG CỤ XỬ LÝ, GHÉP & TÁCH PDF TRÊN TRÌNH DUYỆT

> **Đồ án môn học:** Phát triển phần mềm mã nguồn mở  
> **Sinh viên thực hiện:** Đề tài 12  
> **Nền tảng triển khai:** Ubuntu Linux (WSL / Server 22.04 / 24.04 / 26.04)  
> **Công nghệ:** PHP 8.x, MariaDB / MySQL, Mozilla PDF.js, SortableJS, Bootstrap 5.3, FPDI & QPDF

---

## 1. Giới thiệu tổng quan

**PDF Workspace** là một ứng dụng web mã nguồn mở cho phép người dùng xem trước, sắp xếp thứ tự, xoay góc, ghép và tách các tài liệu PDF trực tiếp trên trình duyệt web. 

Khác với các ứng dụng mô phỏng giao diện thông thường, **PDF Workspace** sở hữu backend engine xử lý PDF thật 100%, bảo đảm hai yêu cầu kỹ thuật cốt lõi:
1. **Quản lý thứ tự và nguồn gốc trang:** Dữ liệu trang đầu ra liên kết chặt chẽ với ID tài liệu nguồn, số trang gốc, thứ tự sắp xếp và góc xoay.
2. **Bản chụp bất biến (Immutable Snapshot):** Mọi lần xuất tài liệu đều được đóng băng thông tin (`export_job_pages`). Dù người dùng có tiếp tục sửa đổi kế hoạch sau đó, các bản đã xuất trước đó vẫn giữ nguyên tính độc lập và toàn vẹn dữ liệu.

---

## 2. Kiến trúc hệ thống & Công nghệ sử dụng

Ứng dụng được thiết kế theo kiến trúc **Service-Oriented MVC** (Mô hình MVC hướng dịch vụ) thuần PHP 8.x hướng đối tượng hiện đại, không dùng framework cồng kềnh để đảm bảo tốc độ tối đa và kiểm soát tuyệt đối luồng dữ liệu.

```
pdf-workspace/
├── app/
│   ├── Controllers/        # BaseController, AuthController, DashboardController,
│   │                       # DocumentController, EditorController, ExportController, TestController
│   ├── Models/             # User, Document, EditPlan, ExportJob, Output
│   ├── Services/           # DocumentService (Kiểm tra & Nạp file), PdfEngineService (Ghép/Tách/Xoay)
│   ├── Middleware/         # AuthMiddleware, GuestMiddleware, CsrfMiddleware
│   └── Helpers/            # Database (PDO Singleton), Env (Đọc file cấu hình), Router, functions
├── config/                 # app.php, database.php
├── database/
│   ├── schema.sql          # Cấu trúc 8 bảng quan hệ chuẩn InnoDB, UTF-8
│   ├── seed.sql            # Dữ liệu tài khoản thử nghiệm ban đầu
│   └── generate_test_samples.php # Script tạo 6 file PDF mẫu
├── public/
│   ├── .htaccess           # Apache URL rewriting
│   └── index.php           # Front Controller duy nhất
├── resources/
│   └── views/              # Layouts, Auth, Dashboard, Documents, Editor, Merge, Split, Exports, Tests
├── storage/                # Thư mục lưu trữ tách biệt ngoài web root
│   ├── sources/            # Tệp gốc của người dùng (Chỉ đọc)
│   ├── outputs/            # Tệp PDF thành phẩm sau khi xuất
│   ├── temp/               # Tệp tạm thời trong quá trình xử lý (Tự dọn dẹp)
│   ├── logs/               # Nhật ký hệ thống & lỗi Database
│   └── test_samples/       # Bộ 6 tệp PDF mẫu phục vụ kiểm thử
├── tests/
│   └── run_tests.php       # Bộ 5 bài kiểm thử nghiệm thu tự động
├── .env                    # Biến môi trường hệ thống
├── composer.json           # Cấu hình nạp gói FPDI và FPDF
└── README.md               # Tài liệu thuyết minh đồ án
```

---

## 3. Hướng dẫn cài đặt & Triển khai trên Ubuntu WSL

### Bước 1: Cài đặt các gói phần mềm cần thiết
Mở terminal Ubuntu và chạy:

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y apache2 libapache2-mod-php \
    php php-cli php-mysql php-mbstring php-xml php-curl php-zip php-gd \
    mariadb-server \
    composer \
    qpdf
```

### Bước 2: Khởi động dịch vụ Cơ sở dữ liệu
```bash
sudo service mysql start
```

### Bước 3: Di chuyển vào thư mục dự án và cài đặt thư viện
```bash
cd /mnt/d/PhatTrienPhanMemMaNguonMo/DeTai12
composer install
chmod -R 777 storage/
```

### Bước 4: Khởi tạo Cơ sở dữ liệu và Cấp quyền
```bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS pdf_workspace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql < database/schema.sql
sudo mysql < database/seed.sql
sudo mysql -e "CREATE USER IF NOT EXISTS 'pdfuser'@'localhost' IDENTIFIED BY 'password123'; GRANT ALL PRIVILEGES ON pdf_workspace.* TO 'pdfuser'@'localhost'; FLUSH PRIVILEGES;"
```

### Bước 5: Khởi chạy máy chủ
```bash
php -S 0.0.0.0:8080 -t public
```

Truy cập trên trình duyệt Windows: 👉 **http://localhost:8080**

---

## 4. Tài khoản thử nghiệm mặc định

- **Email:** `demo@pdfworkspace.test`
- **Mật khẩu:** `Password@123`

*(Bạn cũng có thể tự tạo tài khoản mới bất kỳ qua trang Đăng ký)*

---

## 5. Bộ 5 bài kiểm thử nghiệm thu bắt buộc

Ứng dụng đi kèm bộ sinh dữ liệu 6 tệp PDF mẫu (`database/generate_test_samples.php`) gồm:
- **Tệp A:** 10 trang dọc (Portrait A4)
- **Tệp B:** 10 trang ngang (Landscape A4)
- **Tệp C:** 10 trang kích thước hỗn hợp (Letter, A4, Legal xen kẽ)
- **Tệp D:** 10 trang tiêu chuẩn
- **Tệp E:** Tệp rỗng (0 bytes)
- **Tệp F:** Tệp hỏng (Corrupt PDF)

### Chạy kiểm thử tự động (Có 2 cách):

#### Cách 1: Chạy trực tiếp từ giao diện Web
Đăng nhập vào hệ thống &rarr; Vào menu **"Kiểm thử nghiệm thu"** (`/tests`) &rarr; Bấm nút **"Chạy kiểm thử ngay"**. Hệ thống sẽ thực thi tự động và hiển thị kết quả trực quan trên màn hình.

#### Cách 2: Chạy từ Terminal Linux
Tại thư mục dự án, gõ:
```bash
php database/generate_test_samples.php
php tests/run_tests.php
```

### Nội dung 5 bài test:
1. **Test 1:** Ghép đúng 3 trang theo thứ tự [A2, B1, A1]. Kiểm tra tệp xuất ra đúng 3 trang và bảng đối chiếu snapshot khớp chính xác.
2. **Test 2:** Xoay 1 trang 90 độ và loại trừ 1 trang khác. Kiểm tra chỉ trang được yêu cầu mới đổi góc, trang bị loại biến mất khỏi file xuất.
3. **Test 3:** Tách tài liệu thành 2 nhóm độc lập; thử chọn trang 99 (không tồn tại) &rarr; hệ thống bắt lỗi và chặn chính xác.
4. **Test 4:** Nạp tệp rỗng (0B) và tệp hỏng &rarr; hệ thống phát hiện và từ chối an toàn, không tạo tệp kết quả dở dang.
5. **Test 5:** Kiểm tra phân quyền đa tài khoản (chặn triệt để User B truy cập tệp User A) và tính bất biến của Snapshot sau khi chỉnh sửa kế hoạch.

---

## 6. Kịch bản Demo bảo vệ đồ án trước Giảng viên

Khi bảo vệ trước Hội đồng, bạn nên trình bày theo kịch bản 4 bước sau:

### Bước 1: Giới thiệu kiến trúc & Đăng nhập (2 phút)
- Mở trang `http://localhost:8080/login`.
- Giới thiệu: Ứng dụng xây dựng trên Ubuntu WSL, sử dụng PHP 8.x kết hợp MySQL, không dùng framework nặng, bảo mật session bằng `HttpOnly`, `SameSite=Lax`, chống CSRF Token trên mọi form.
- Đăng nhập bằng tài khoản `demo@pdfworkspace.test`.

### Bước 2: Quản lý tài liệu & Tải lên đa luồng (2 phút)
- Vào mục **"Tài liệu của tôi"**.
- Bấm **"Tải PDF lên"**, chọn cùng lúc 2 file PDF. Cho giảng viên thấy thanh tiến trình tải lên mượt mà.
- Giải thích: Tệp tải lên được kiểm tra MIME type thực tế, tệp rỗng, tệp hỏng; tên file lưu trữ trên ổ cứng được mã hóa UUID ngẫu nhiên để chống tấn công ghi đè và Directory Traversal.

### Bước 3: PDF Editor & Quản lý trang trực quan (4 phút - Trọng tâm!)
- Bấm **"Chỉnh sửa"** một file PDF để mở màn hình **PDF Editor**.
- **Kéo thả trang:** Dùng chuột kéo thẻ trang đổi vị trí (thứ tự tự động cập nhật).
- **Xoay trang:** Bấm nút xoay 90° để thấy trang xoay ngay trên Canvas PDF.js.
- **Hoàn tác:** Bấm nút **"Hoàn tác"** (`Ctrl + Z`) để giảng viên thấy thao tác được khôi phục.
- **Ghép trang từ tệp khác:** Bấm `+ Thêm tất cả` ở danh sách tệp nguồn bên trái để đưa thêm trang từ tài liệu khác vào.
- Bấm **"Lưu kế hoạch"** (`Ctrl + S`) &rarr; Bấm **"Xuất PDF"** &rarr; Tải về file kết quả thật và mở lên để chứng minh trang được xoay và sắp xếp đúng như trên giao diện.

### Bước 4: Kiểm tra tính bất biến của Snapshot & Lịch sử đối chiếu (2 phút)
- Vào mục **"Lịch sử xuất file"** (`/exports`).
- Bấm nút **"Đối chiếu"** để xem **Bảng đối chiếu nguồn trang (Snapshot)**.
- Giải thích với giảng viên: Bảng đối chiếu này đọc từ `export_job_pages` đã được đóng băng tại thời điểm xuất. Dù sau này người dùng có xóa file nguồn hay sửa Editor thì lịch sử xuất vẫn được bảo toàn nguyên vẹn.

### Bước 5: Trình diễn 5 bài test tự động (1 phút)
- Vào mục **"Kiểm thử nghiệm thu"** (`/tests`) &rarr; Bấm **"Chạy kiểm thử ngay"** &rarr; Cho giảng viên xem kết quả **5/5 PASS (100%)**.

---

## 7. Các câu hỏi giảng viên hay hỏi & Cách trả lời

**Câu 1: "Tại sao em không dùng luôn thumbnail trên giao diện để ghép PDF?"**
> *Trả lời:* "Dạ thưa thầy/cô, giao diện chỉ làm nhiệm vụ hiển thị trực quan cho người dùng. Nếu chỉ dựa vào mảng thumbnail ở frontend thì dữ liệu rất dễ bị can thiệp qua inspect hoặc giả mạo request. Hệ thống của em lưu trữ cấu trúc trang chặt chẽ trong database (`document_id`, `source_page_number`, `output_order`, `rotation_degrees`) và backend PHP sẽ đọc trực tiếp từ tệp gốc để xuất ra file thật."

**Câu 2: "Bản chụp bất biến (Immutable Snapshot) của em hoạt động thế nào?"**
> *Trả lời:* "Dạ, khi người dùng bấm Xuất PDF, trước khi tạo file, hệ thống sẽ thực hiện sao chép toàn bộ danh sách trang hiện tại vào bảng `export_job_pages` cùng với tên tệp nguồn tại thời điểm đó. Khi hoàn tất, tệp kết quả được lưu vào `storage/outputs/` với tên hash riêng. Dù sau này người dùng có xóa trang hoặc đổi tên tài liệu thì bản xuất trước đó vẫn giữ nguyên dữ liệu đối chiếu ban đầu."

**Câu 3: "Hệ thống chống người dùng xem lén file của nhau (IDOR) bằng cách nào?"**
> *Trả lời:* "Dạ, tất cả file PDF được lưu trong thư mục `storage/` nằm ngoài thư mục `public/`, web server không thể truy cập trực tiếp. Mọi thao tác tải file đều phải đi qua Controller, kiểm tra `auth_id()` khớp với `user_id` trong database, hoặc thông qua mã Token ngẫu nhiên 64 ký tự của bản xuất."

---

## 8. Hướng dẫn Sao lưu & Phục hồi hệ thống

### Sao lưu Cơ sở dữ liệu và Tệp lưu trữ:
```bash
# 1. Sao lưu database
mysqldump -u pdfuser -p pdf_workspace > backup_pdf_workspace_$(date +%F).sql

# 2. Sao lưu các tệp tài liệu
tar -czvf backup_storage_$(date +%F).tar.gz storage/sources/ storage/outputs/
```

### Phục hồi hệ thống:
```bash
# 1. Phục hồi database
mysql -u pdfuser -p pdf_workspace < backup_pdf_workspace_*.sql

# 2. Phục hồi tệp tài liệu
tar -xzvf backup_storage_*.tar.gz
```
