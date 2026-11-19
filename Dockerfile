FROM php:8.2-cli

# Cài đặt các gói phụ thuộc hệ điều hành, thư viện đồ họa và công cụ QPDF
RUN apt-get update && apt-get install -y \
    qpdf \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd \
    && rm -rf /var/lib/apt/lists/*

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Sao chép toàn bộ mã nguồn dự án vào container
COPY . /var/www/html

# Cài đặt các phụ thuộc Composer tối ưu cho môi trường sản xuất
RUN composer install --no-dev --optimize-autoloader

# Khởi tạo và cấp quyền ghi cho các thư mục lưu trữ tạm và kết xuất
RUN mkdir -p /var/www/html/storage/sources \
             /var/www/html/storage/outputs \
             /var/www/html/storage/temp \
             /var/www/html/storage/logs \
    && chmod -R 777 /var/www/html/storage

# Thiết lập múi giờ Việt Nam (UTC+7)
ENV TZ=Asia/Ho_Chi_Minh

# Cổng mặc định của ứng dụng
EXPOSE 8080

# Chạy PHP Built-in Server tương thích biến môi trường PORT của Render
CMD ["sh", "-c", "php -d upload_max_filesize=100M -d post_max_size=100M -d memory_limit=512M -S 0.0.0.0:${PORT:-8080} -t public public/index.php"]
