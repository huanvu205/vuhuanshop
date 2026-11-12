<?php
class PolicyController extends Controller {
    /**
     * @param string $key
     * @return void
     */
    private function showPolicy($key) {
        $policies = [
            'warranty' => [
                'title' => 'Chính sách bảo hành',
                'intro' => 'Vũ Huân Shop cam kết mang đến chế độ bảo hành chính hãng uy tín, nhanh chóng và minh bạch cho mọi khách hàng.',
                'sections' => [
                    ['title' => '1. Thời gian bảo hành', 'text' => 'Tất cả điện thoại, máy tính bảng và laptop mới được bảo hành chính hãng 12 tháng. Phụ kiện (cáp, sạc, tai nghe) bảo hành 6 - 12 tháng tùy dòng sản phẩm.'],
                    ['title' => '2. Chính sách 1 đổi 1 trong 30 ngày đầu', 'text' => 'Nếu sản phẩm gặp lỗi phần cứng do nhà sản xuất (mất nguồn, lỗi màn hình, lỗi sóng, lỗi camera), quý khách được đổi ngay sản phẩm mới tương đương 100% trong vòng 30 ngày kể từ ngày nhận hàng.'],
                    ['title' => '3. Điều kiện tiếp nhận bảo hành', 'text' => 'Sản phẩm còn nguyên tem bảo hành, số IMEI/Serial Number trùng khớp với đơn hàng trên hệ thống, không bị rơi vỡ, cấn móp, ngấm nước hoặc can thiệp phần cứng trái phép.'],
                    ['title' => '4. Quy trình xử lý bảo hành', 'text' => 'Quý khách gửi sản phẩm về cửa hàng tại Trực Đại, Trực Ninh, Nam Định hoặc liên hệ Hotline 0336.120.405 để được hỗ trợ tiếp nhận tận nhà qua bưu tá.'],
                ],
            ],
            'returns' => [
                'title' => 'Chính sách đổi trả & Hoàn tiền',
                'intro' => 'Đảm bảo quyền lợi tối đa cho khách hàng khi mua sắm online tại Vũ Huân Shop.',
                'sections' => [
                    ['title' => '1. Thời gian áp dụng', 'text' => 'Quý khách có quyền yêu cầu đổi hàng hoặc trả hàng hoàn tiền trong vòng 7 ngày kể từ khi nhận sản phẩm nếu không ưng ý hoặc sản phẩm giao không đúng mô tả.'],
                    ['title' => '2. Điều kiện đổi trả', 'text' => 'Sản phẩm còn nguyên seal hộp (nếu có), đầy đủ phụ kiện, quà tặng kèm theo và hóa đơn mua hàng. Thiết bị chưa qua sử dụng hoặc kích hoạt tài khoản iCloud/Google cá nhân.'],
                    ['title' => '3. Thời gian hoàn tiền', 'text' => 'Sau khi kỹ thuật viên kiểm tra sản phẩm đạt yêu cầu, số tiền thanh toán sẽ được hoàn trả trực tiếp vào tài khoản ngân hàng của quý khách trong vòng 24 - 48 giờ làm việc.'],
                    ['title' => '4. Chi phí vận chuyển đổi trả', 'text' => 'Vũ Huân Shop chịu 100% phí vận chuyển chiều đi và về nếu lỗi thuộc về shop (giao sai mẫu, lỗi nhà sản xuất). Trường hợp đổi theo nhu cầu cá nhân, quý khách vui lòng thanh toán phí ship theo cước bưu điện.'],
                ],
            ],
            'payment' => [
                'title' => 'Phương thức thanh toán',
                'intro' => 'Vũ Huân Shop hỗ trợ đa dạng phương thức thanh toán an toàn, bảo mật và tiện lợi.',
                'sections' => [
                    ['title' => '1. Thanh toán khi nhận hàng (COD)', 'text' => 'Khách hàng được mở gói hàng kiểm tra ngoại quan (đồng kiểm) cùng shipper trước khi thanh toán tiền mặt cho nhân viên giao hàng.'],
                    ['title' => '2. Chuyển khoản ngân hàng qua mã VietQR', 'text' => 'Mã QR động được sinh tự động theo số tiền và mã đơn hàng của bạn. Bạn chỉ cần mở App ngân hàng (MB Bank, Vietcombank, Techcombank, ACB...) quét mã để chuyển khoản nhanh 24/7 mà không lo nhập sai số tài khoản.'],
                    ['title' => '3. Thanh toán qua cổng VNPAY', 'text' => 'Hỗ trợ thanh toán qua cổng VNPAY (ATM nội địa, thẻ quốc tế Visa/Mastercard, VNPAY-QR) với tiêu chuẩn bảo mật PCI-DSS quốc tế và mã hóa chữ ký HMAC-SHA512 an toàn tuyệt đối.'],
                ],
            ],
            'shipping' => [
                'title' => 'Chính sách vận chuyển & Giao hàng',
                'intro' => 'Vũ Huân Shop hợp tác với các đơn vị vận chuyển uy tín (GHTK, GHN, Viettel Post) nhằm giao hàng nhanh chóng, an toàn và minh bạch đến tận tay khách hàng toàn quốc.',
                'sections' => [
                    ['title' => '1. Phạm vi và thời gian giao hàng', 'text' => 'Giao hàng tận nơi trên toàn quốc (63 tỉnh thành). Thời gian dự kiến: Khu vực nội tỉnh & lân cận: 24 - 48 giờ. Các tỉnh thành khác: 2 - 4 ngày làm việc (không tính Chủ Nhật và ngày lễ).'],
                    ['title' => '2. Biểu phí vận chuyển', 'text' => 'Miễn phí 100% phí vận chuyển cho tất cả đơn hàng có giá trị từ 500.000đ trở lên. Các đơn hàng dưới 500.000đ áp dụng mức phí tiêu chuẩn đồng giá 30.000đ toàn quốc.'],
                    ['title' => '3. Quyền đồng kiểm khi nhận hàng', 'text' => 'Quý khách hoàn toàn được quyền mở kiện hàng kiểm tra ngoại quan (đúng mẫu mã, phụ kiện, không móp méo, trầy xước) cùng nhân viên giao hàng trước khi thanh toán tiền mặt (COD).'],
                    ['title' => '4. Trách nhiệm bồi thường khi phát sinh sự cố vận chuyển', 'text' => 'Nếu kiện hàng bị thất lạc, bể vỡ hoặc hư hỏng trong quá trình vận chuyển, Vũ Huân Shop chịu 100% trách nhiệm gửi ngay sản phẩm thay thế mới hoặc hoàn tiền đầy đủ cho quý khách.'],
                ],
            ],
            'complaints' => [
                'title' => 'Quy trình tiếp nhận & Giải quyết khiếu nại',
                'intro' => 'Vũ Huân Shop luôn đặt sự hài lòng của khách hàng lên hàng đầu và cam kết giải quyết mọi tranh chấp, khiếu nại công bằng, minh bạch theo đúng quy định pháp luật TMĐT.',
                'sections' => [
                    ['title' => '1. Các kênh tiếp nhận khiếu nại', 'text' => 'Khách hàng có thể phản ánh qua: Hotline chăm sóc khách hàng: 0336.120.405 (8h00 - 21h00 hàng ngày), Email: vuhuan123890@gmail.com, hoặc trực tiếp tại cửa hàng Trực Đại, Trực Ninh, Nam Định.'],
                    ['title' => '2. Quy trình 4 bước giải quyết khiếu nại', 'text' => 'Bước 1: Bộ phận CSKH tiếp nhận thông tin và gửi phản hồi xác nhận trong vòng 2 giờ.<br>Bước 2: Bộ phận Kỹ thuật & Quản lý đơn hàng xác minh lịch sử đơn hàng, hình ảnh/video sản phẩm trong 24 giờ.<br>Bước 3: Đưa ra phương án giải quyết thỏa đáng (Đổi mới sản phẩm, hoàn tiền, hoặc hỗ trợ sửa chữa bảo hành).<br>Bước 4: Thực hiện giải quyết dứt điểm và liên hệ thăm hỏi mức độ hài lòng của khách hàng.'],
                    ['title' => '3. Thời hạn giải quyết khiếu nại', 'text' => 'Thời gian giải quyết tối đa không quá 03 ngày làm việc kể từ thời điểm tiếp nhận thông tin khiếu nại hợp lệ.'],
                    ['title' => '4. Cơ chế giải quyết tranh chấp', 'text' => 'Mọi phát sinh tranh chấp đều được ưu tiên thương lượng hòa giải. Trường hợp không đạt được đồng thuận, vụ việc sẽ được đưa ra cơ quan Tòa án có thẩm quyền để giải quyết theo luật pháp Việt Nam.'],
                ],
            ],
            'terms' => [
                'title' => 'Điều khoản dịch vụ',
                'intro' => 'Các điều khoản và điều kiện chi phối việc sử dụng website và giao dịch mua sắm tại Vũ Huân Shop.',
                'sections' => [
                    ['title' => '1. Tài khoản khách hàng', 'text' => 'Khách hàng có trách nhiệm bảo mật thông tin tài khoản, mật khẩu và chịu trách nhiệm với các hoạt động phát sinh dưới tài khoản của mình.'],
                    ['title' => '2. Giá cả và khuyến mãi', 'text' => 'Giá niêm yết trên website là giá bán chính thức đã bao gồm thuế VAT (nếu có). Vũ Huân Shop có quyền điều chỉnh giá và các chương trình khuyến mại tùy theo từng thời điểm mà không cần thông báo trước.'],
                    ['title' => '3. Quyền từ chối đơn hàng', 'text' => 'Chúng tôi có quyền từ chối hoặc hủy đơn hàng đối với các trường hợp nghi ngờ gian lận, phá hoại hệ thống, đầu cơ số lượng lớn hoặc không liên hệ được số điện thoại người nhận.'],
                ],
            ],
            'privacy' => [
                'title' => 'Chính sách bảo mật thông tin',
                'intro' => 'Vũ Huân Shop cam kết bảo vệ tuyệt đối sự riêng tư và dữ liệu cá nhân của khách hàng theo quy định pháp luật Việt Nam (Nghị định 13/2023/NĐ-CP).',
                'sections' => [
                    ['title' => '1. Mục đích thu thập thông tin', 'text' => 'Chúng tôi thu thập họ tên, số điện thoại, email và địa chỉ nhận hàng nhằm phục vụ duy nhất cho việc xác nhận đơn, đóng gói, giao hàng và thực hiện nghĩa vụ bảo hành.'],
                    ['title' => '2. Phạm vi sử dụng thông tin', 'text' => 'Dữ liệu chỉ được chia sẻ cho đơn vị vận chuyển (GHTK, GHN, Viettel Post) để giao kiện hàng tới tay quý khách. Tuyệt đối không bán, trao đổi thông tin khách hàng cho bất kỳ bên thứ ba nào vì mục đích quảng cáo.'],
                    ['title' => '3. Bảo mật thông tin thanh toán', 'text' => 'Website không lưu trữ thông tin thẻ ngân hàng hoặc mật khẩu tài khoản ngân hàng của khách hàng. Mọi giao dịch điện tử đều được mã hóa SSL an toàn qua cổng thanh toán đối tác VNPAY.'],
                    ['title' => '4. Quyền của chủ thể dữ liệu', 'text' => 'Khách hàng có quyền yêu cầu tra cứu, chỉnh sửa hoặc xóa vĩnh viễn dữ liệu tài khoản bất kỳ lúc nào tại mục Hồ sơ cá nhân hoặc liên hệ hỗ trợ.'],
                ],
            ],
            'data-deletion' => [
                'title' => 'Chính sách xóa dữ liệu người dùng (User Data Deletion)',
                'intro' => 'Vũ Huân Shop tôn trọng quyền riêng tư của khách hàng và cam kết tạo điều kiện để bạn quản lý hoặc xóa dữ liệu cá nhân bất kỳ lúc nào.',
                'sections' => [
                    ['title' => '1. Cam kết quyền riêng tư', 'text' => 'Chúng tôi chỉ thu thập các thông tin công khai cơ bản gồm: Họ tên, địa chỉ Email và Ảnh đại diện để tạo tài khoản mua sắm tiện lợi.'],
                    ['title' => '2. Cách tự xóa tài khoản trên Website', 'text' => 'Khách hàng đăng nhập vào tài khoản, truy cập mục Hồ sơ cá nhân -> bấm "Xóa tài khoản của tôi" và nhập mật khẩu xác nhận. Hệ thống sẽ vô hiệu hóa và xóa dữ liệu của bạn ngay lập tức.'],
                    ['title' => '3. Yêu cầu xóa qua kênh hỗ trợ', 'text' => 'Quý khách cũng có thể gửi email về hòm thư: vuhuan123890@gmail.com hoặc gọi Hotline: 0336.120.405 với nội dung "Yêu cầu xóa tài khoản và dữ liệu". Hệ thống sẽ tiếp nhận và xóa vĩnh viễn dữ liệu của bạn trong vòng 24 giờ.'],
                ],
            ],
        ];

        if (!isset($policies[$key])) {
            http_response_code(404);
            $this->view('errors.404', ['title' => '404 - Không tìm thấy']);
            return;
        }

        $this->view('policies.show', [
            'title' => $policies[$key]['title'] . ' - ' . APP_NAME,
            'policy' => $policies[$key],
        ]);
    }

    public function warranty() { $this->showPolicy('warranty'); }
    public function returns() { $this->showPolicy('returns'); }
    public function payment() { $this->showPolicy('payment'); }
    public function terms() { $this->showPolicy('terms'); }
    public function privacy() { $this->showPolicy('privacy'); }
    public function dataDeletion() { $this->showPolicy('data-deletion'); }
    public function shipping() { $this->showPolicy('shipping'); }
    public function complaints() { $this->showPolicy('complaints'); }
}
