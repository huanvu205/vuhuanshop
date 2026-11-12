# Triển khai và kế hoạch kinh doanh

## Trạng thái triển khai cần xác nhận

| Hạng mục | Giá trị hiện biết |
|---|---|
| Engine local trong cấu hình | SQL Server (`mssql_ps`), kết nối PowerShell/Windows Authentication |
| Engine hosting trong cấu hình hiện tại | MySQL; không phải migration của database local |
| URL HTTPS công khai | Chưa được cung cấp/xác minh |
| Hosting/VPS, phiên bản PHP/SQL Server | Chưa xác minh trên máy triển khai |
| Callback VNPay Sandbox qua HTTPS | Chưa kiểm thử end-to-end |
| GA4 Measurement ID | Lấy từ `.env`; chưa xác minh trên site công khai |
| Tài khoản demo cho giảng viên | Chưa tạo; không ghi mật khẩu thật trong Git hoặc báo cáo |
| Backup và diễn tập phục hồi | Chưa thực hiện |

## Nâng cấp SQL Server

1. Dừng thao tác checkout và sao lưu database hiện hữu.
2. Khôi phục backup vào database staging riêng.
3. Chạy `database/migrations/20261102_commerce_mssql.sql` trên staging. Migration thêm cấu trúc SKU/biến thể, dữ liệu giao dịch/hoàn tiền, và trigger kiểm soát tồn kho. Không chạy file migration MySQL trên SQL Server.
4. Xác minh các đơn hàng, số lượng tồn, trigger và constraints trên staging; chạy kiểm tra checkout đồng thời.
5. Chỉ triển khai production sau khi staging đạt và có kế hoạch rollback bằng backup. Chưa áp dụng migration trong workspace này vì SQL Server hiện không kết nối được.

Bản triển khai vốn đã dùng MySQL có file riêng database/migrations/20261102_commerce_mysql.sql. Không chạy file SQL Server trên MySQL hoặc ngược lại; file MySQL không dùng để chuyển engine.

## Kế hoạch kinh doanh

Chưa có báo giá, chi phí vận hành, khảo sát, conversion, doanh số hoặc ngân sách thật. Không đưa dự báo giả làm kết quả thực tế. Khi có số liệu, ghi rõ nguồn/ngày lấy; hòa vốn được tính bằng chi phí cố định chia cho lãi gộp trên đơn.

## Checklist phát hành

- [ ] HTTPS và chứng chỉ TLS hoạt động; xác minh `.env` trả 403/404 trên web server thật.
- [ ] Sao lưu và khôi phục SQL Server staging thành công.
- [ ] Migration staging đạt; test tồn kho nhiều người mua cùng lúc đạt.
- [ ] Thanh toán/hoàn tiền Sandbox được xác nhận bằng chữ ký VNPay; không phát sinh giao dịch live.
- [ ] SMTP, GA4 và analytics consent được xác minh trên site thật.
- [ ] Tài khoản demo tối thiểu quyền, có mật khẩu bàn giao riêng và thu hồi sau buổi chấm.
- [ ] Có ảnh chụp website đang chạy, URL, ngày chụp và đối chiếu wireframe.
- [ ] Ghi đầy đủ lỗi còn lại và test chưa chạy trong báo cáo.

Không tích hợp API hãng vận chuyển hoặc chức năng tạo vận đơn.