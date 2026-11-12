# Vũ Huân Shop – Website thương mại điện tử

Website PHP theo mô hình MVC, dùng MySQL trên hosting hoặc SQL Server ở môi trường phát triển. Các hồ sơ phân tích, thiết kế, truy vết, kiểm thử và wireframe nằm trong [`docs/project`](docs/project/).

## Chạy local

1. Cài PHP 8+ với PDO (MySQL hoặc SQL Server), web server có rewrite URL và SQL Server/MySQL.
2. Tạo database theo `database/schema.sql` (MySQL) hoặc `schema_mssql.sql` (SQL Server). Với DB MySQL hiện hữu, ứng dụng tự bổ sung một số bảng/cột; xem thêm `database/migrations/20261101_project_requirements.sql`.
3. Sao chép `.env.example` thành `.env`, điền cấu hình môi trường riêng. Không đưa mật khẩu/khóa thật vào Git.
4. Cấu hình host trỏ vào thư mục dự án, bật rewrite theo `.htaccess`, rồi truy cập `index.php` qua host local.
5. Tạo tài khoản quản trị theo hướng dẫn trong [CONFIGURATION.md](CONFIGURATION.md). Không dùng tài khoản mẫu ở môi trường công khai.

## Cấu hình dịch vụ

Chi tiết biến môi trường có trong `.env.example` và `CONFIGURATION.md`. Thanh toán VNPAY chỉ dùng sandbox khi đã cấu hình callback công khai qua HTTPS. Email cần SMTP hoạt động. Google Analytics cần cung cấp `GA_MEASUREMENT_ID`.

## Nâng cấp database hiện hữu

MySQL tự chạy migration cho lịch sử đơn và sổ địa chỉ. Với SQL Server, áp dụng `database/migrations/20261101_project_requirements_mssql.sql` trước khi dùng các chức năng mới.

## Giới hạn còn lại

Tích hợp vận chuyển nội địa và hoàn tiền tự động qua cổng chưa có. Tồn kho biến thể chưa được tách SKU/tồn theo từng biến thể. Tài liệu trong `docs/project` có các trường cần nhóm điền và các giả định cần xác minh trước khi nộp.
