# Ma trận truy vết và kế hoạch kiểm thử

> Đây là test plan, không phải kết quả đã chạy. Điền kết quả thực tế, ngày chạy, môi trường và ảnh/chứng cứ sau kiểm thử.

| UC | BR | Bảng chính | Màn hình | Vị trí mã | Test case |
|---|---|---|---|---|---|
| UC01 | BR auth | users, roles, user_addresses | SCR08 | AuthController, UserController, UserModel, AddressModel | TC01.1–TC01.3 |
| UC02 | BR catalog | products, categories, brands | SCR01–SCR03 | ProductController, ProductModel | TC02.1–TC02.3 |
| UC03 | BR stock | carts, cart_items | SCR04 | CartController, CartModel | TC03.1–TC03.3 |
| UC04 | BR01,02,04 | orders, order_details | SCR05 | CheckoutController, OrderModel | TC04.1–TC04.4 |
| UC05 | BR02,03 | coupons, coupon_uses | SCR05 | CheckoutController, CouponModel | TC05.1–TC05.3 |
| UC06 | BR payment | payments, orders | SCR06 | CheckoutController::vnpayReturn/vnpayIpn | TC06.1–TC06.4 |
| UC07 | BR05,08,09 | orders, order_status_history | SCR07 | OrderController, OrderModel | TC07.1–TC07.3 |
| UC08 | BR06,07 | orders, order_status_history | SCR07 | OrderController::returnRequest, OrderModel | TC08.1–TC08.2 |
| UC09 | BR06 | reviews, order_details, orders | SCR03/SCR07 | ReviewController, ReviewModel | TC09.1–TC09.2 |
| UC10 | BR stock | products, inventory | SCR09 | ProductAdminController, InventoryController | TC10.1–TC10.2 |
| UC11 | BR05,08 | orders, shipping, order_status_history | SCR10 | OrderAdminController, OrderModel | TC11.1–TC11.3 |
| UC12 | BR03 | coupons, coupon_uses | SCR09 | CouponController, CouponModel | TC12.1 |
| UC13 | Access matrix | users | SCR09 | UserAdminController | TC13.1 |
| UC14 | Reporting | orders, order_details | SCR09 | ReportController, DashboardController | TC14.1 |
| UC15 | Legal | settings/news | footer/policy | PolicyController, NewsController | TC15.1 |

## Kịch bản bắt buộc

| ID | Kịch bản và dữ liệu | Kết quả mong đợi | Kết quả thực tế |
|---|---|---|---|
| TC04.1 | Đặt giỏ hợp lệ, đủ tồn, đồng ý chính sách | Tạo đơn, lưu snapshot giá/địa chỉ, giảm tồn một lần | Chưa chạy |
| TC04.2 | Bỏ chọn consent | Không tạo đơn, yêu cầu xác nhận | Chưa chạy |
| TC04.3 | Hai khách mua đồng thời khi còn 1 chiếc | Chỉ một đơn được chấp nhận | Chưa chạy; cần kiểm thử và hoàn thiện atomic stock reservation |
| TC05.1 | Coupon hợp lệ, đạt mức tối thiểu | Giảm đúng số tiền, tổng đúng | Chưa chạy |
| TC05.2 | Coupon hết hạn/vượt lượt/đơn dưới tối thiểu | Không áp dụng | Chưa chạy |
| TC06.1 | VNPAY sandbox thành công | Chữ ký/IPN hợp lệ cập nhật giao dịch và đơn đúng | Chưa chạy |
| TC06.2 | Cổng từ chối hoặc khách hủy | Không đánh dấu đã thanh toán | Chưa chạy |
| TC06.3 | Sửa dữ liệu return/IPN, sai chữ ký | Từ chối, không cập nhật paid | Chưa chạy |
| TC06.4 | Gửi cùng IPN nhiều lần | Không ghi nhận/trừ kho trùng | Chưa chạy; rà idempotency trong DB |
| TC07.1 | Chuyển pending → confirmed → processing → shipping → delivered | Ghi lịch sử + gửi email từng bước | Chưa chạy |
| TC07.2 | Thử chuyển pending → delivered | Từ chối chuyển trạng thái | Chưa chạy |
| TC07.3 | Hủy đơn hợp lệ | Trạng thái cancelled, hoàn tồn một lần | Chưa chạy; kiểm tra chống gọi lặp |
| TC08.1 | Yêu cầu trả hàng khi đơn chưa giao | Từ chối | Chưa chạy |
| TC08.2 | Đơn đã giao gửi yêu cầu đổi trả | Chuyển return_requested, thông báo quản trị | Chưa chạy |
| TC09.1 | Khách không có đơn delivered gửi review | Từ chối | Chưa chạy |
| TC09.2 | Khách mua và nhận hàng gửi review | Lưu pending, chờ duyệt | Chưa chạy |
| TC11.1 | Tài khoản khách truy cập trang admin | Bị từ chối | Chưa chạy |
| TC11.2 | Đổi ID đơn để xem đơn người khác | Bị từ chối | Chưa chạy |
| TC11.3 | Admin xác nhận hoàn tiền sau yêu cầu | Lưu refunded/history; tồn chỉ cập nhật khi hàng được nhập lại theo chính sách | Chưa chạy |
| TCSEC.1 | SQLi/XSS tại tìm kiếm và đánh giá | Input không thực thi; output được escape | Chưa chạy |
| TCSEC.2 | Đăng nhập sai 5 lần trong phiên | Khóa 15 phút cho khóa login+IP trong phiên | Chưa chạy |
| TCRWD.1 | Trang chủ desktop/mobile | Bố cục không vỡ | Chưa chạy |
| TCRWD.2 | Đo tải trang chủ | < 3 giây trong môi trường đã mô tả | Chưa đo |

## Phân quyền tóm tắt

| Chức năng | Khách vãng lai | Khách hàng | Nhân viên | Admin |
|---|---:|---:|---:|---:|
| Duyệt/tìm sản phẩm | Có | Có | Có | Có |
| Giỏ hàng/checkout | Giỏ có thể dùng | Có | Có | Có |
| Đơn của chính mình | Không | Có | Không | Có |
| Đánh giá sau đơn giao | Không | Có | Duyệt | Duyệt |
| Sửa sản phẩm/tồn | Không | Không | Theo guard | Có |
| Quản trị tài khoản/cấu hình | Không | Không | Hạn chế | Có |

## Kiểm tra thực thi trong workspace (2026-11-01)

| Lệnh/kiểm tra | Kết quả thực tế | Phạm vi/giới hạn |
|---|---|---|
| `php -l` trên PHP project (bỏ PHPMailer/vendor/node_modules) | Qua, 198 tệp | Chỉ cú pháp PHP |
| `node --check public/js/main.js` | Qua | Chỉ cú pháp JavaScript |
| `php tests/VnpayRefundSignatureTest.php` | Qua, 4 assertions | Chữ ký giả lập; không gọi VNPay |
| Kết nối SQL Server đang cấu hình | Thất bại: không tìm thấy server/instance | Không chạy migration hay kiểm thử dữ liệu |
| Đăng ký/đăng nhập, sản phẩm, giỏ, checkout, payment/refund, concurrency, pentest, performance | Chưa chạy | Cần database/server và credentials Sandbox phù hợp |
