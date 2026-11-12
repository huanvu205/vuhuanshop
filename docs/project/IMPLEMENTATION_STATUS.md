# Tình trạng hoàn thiện VuHuanShop

## Thành viên nhóm

- Vũ Văn Huân
- Lò Anh Tuấn
- Phạm Tùng Dương
- Nguyễn Trí Sơn
- Phạm Văn Dương

Vai trò và tỷ lệ đóng góp chưa được cung cấp; nhóm cần xác nhận trước khi nộp.

## Đã triển khai trong workspace

- Tích hợp luồng yêu cầu hoàn tiền VNPay qua endpoint Sandbox, xác thực chữ ký phản hồi, ghi sổ yêu cầu/kết quả, chống yêu cầu đang xử lý trùng đơn và chỉ đánh dấu hoàn tiền khi nhận được phản hồi thành công có chữ ký hợp lệ. Có loại hoàn toàn phần và một phần.
- IPN thanh toán kiểm tra chữ ký, mã merchant, mã đơn/giao dịch, số tiền và bản ghi lần thanh toán; trang return không tự xác nhận đơn đã thanh toán.
- Thêm quản lý biến thể/SKU và tồn kho riêng, chọn SKU trên sản phẩm/giỏ/checkout, tính giá từ dữ liệu server và lưu SKU vào chi tiết đơn.
- SQL Server tạo đơn và trừ tồn qua transaction/trigger có kiểm tra tồn trước khi cập nhật; hủy đơn và xác nhận hàng trả lại hoàn tồn theo transaction trạng thái.
- Ghi lịch sử trạng thái và người thao tác; kiểm tra luồng trạng thái. Hoàn tiền không thể bị đặt thủ công từ form trạng thái chung.
- Bổ sung CSRF cho thao tác giỏ, mã giảm giá, mua ngay, wishlist, xóa/duyệt nội dung quản trị, xóa ảnh sản phẩm và các hành động thanh toán/hủy đơn; các hành động thay đổi dữ liệu chuyển sang POST.
- Thông tin cấu hình nhạy cảm được đọc từ `.env` đã có trong môi trường làm việc; giá trị không được luân chuyển. Bản triển khai cần cấu hình các biến tương ứng trong môi trường riêng.
- Cập nhật README, hướng dẫn triển khai và mẫu hồ sơ; danh sách thành viên đã được điền.

## Migration

- `database/migrations/20261102_commerce_mssql.sql` là migration cộng thêm cho SQL Server; không xóa dữ liệu và không chuyển hệ quản trị.
- Có migration MySQL riêng chỉ dành cho hệ thống vốn đã chạy MySQL; không phải hướng dẫn đổi SQL Server sang MySQL.
- Chưa áp dụng migration lên database hiện hữu vì kết nối SQL Server trong môi trường làm việc không thành công. Cần backup và chạy migration trên bản sao trước khi triển khai thật.

## Kiểm tra đã chạy

- `php -l`: 198 file PHP trong dự án (bỏ PHPMailer/vendor/node_modules), đều qua cú pháp.
- `node --check public/js/main.js`: qua.
- `php tests/VnpayRefundSignatureTest.php`: qua 4 assertion chữ ký (đúng, dữ liệu bị sửa, thiếu chữ ký, kiểm tra cấu hình Sandbox).
- Kết nối SQL Server không thành công (lỗi không tìm thấy server/instance); do đó không chạy migration, truy vấn tích hợp, kiểm thử giao dịch đồng thời hoặc kiểm thử dữ liệu thực.

## Chưa thể xác nhận hoặc cần dữ liệu thực

- Chưa chạy thanh toán/hoàn tiền VNPay Sandbox end-to-end; cần merchant sandbox hoạt động, URL callback HTTPS công khai và xác nhận cấu hình IPN.
- Chưa kiểm thử đăng ký, đăng nhập, quản trị sản phẩm, giỏ hàng, checkout, tồn kho cạnh tranh, thanh toán và hoàn tiền trên database. Chưa thực hiện pentest hay đo hiệu năng; không xem lint là bằng chứng các chức năng hoạt động.
- Chưa có khảo sát người dùng, persona dựa trên khảo sát, số liệu kinh doanh, ảnh website đang chạy, URL HTTPS đã xác minh, tài khoản demo hay vai trò từng thành viên. Những nội dung này không được tự tạo.
- Các tài liệu phân tích, sơ đồ, wireframe/mockup hiện có trong `docs/project` là tài liệu thiết kế nội bộ; cần đối chiếu với giao diện triển khai và quy trình thực tế trước khi dùng như bằng chứng nghiệm thu.
- Chưa tạo bản báo cáo Word/PDF 30 trang cuối cùng. Báo cáo chỉ có thể hoàn thiện để nộp sau khi nhóm bổ sung dữ liệu, ảnh chụp thật, URL, tài khoản thử nghiệm và kết quả kiểm thử.
