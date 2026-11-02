#!/usr/bin/env bash

# Thiết lập múi giờ Việt Nam (UTC+7)
export TZ="Asia/Ho_Chi_Minh"

# Khởi động dịch vụ cơ sở dữ liệu nếu chưa chạy
sudo service mysql start

echo "=========================================================="
echo "   KHỞI ĐỘNG MÁY CHỦ PDF WORKSPACE (CỔNG 8080)            "
echo "   - Giới hạn tải lên tối đa: 100 MB                     "
echo "   - Bộ nhớ tối đa: 512 MB                               "
echo "   - URL: http://localhost:8080                          "
echo "=========================================================="

php -d upload_max_filesize=100M -d post_max_size=100M -d memory_limit=512M -S 0.0.0.0:8080 -t public public/index.php
