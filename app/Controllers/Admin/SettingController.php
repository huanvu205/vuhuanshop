<?php
/**
 * SettingController - Quản lý cài đặt hệ thống (Admin)
 */
class SettingController extends Controller {
    private $settingModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireAdmin();
        $this->settingModel = new SettingModel();
    }
    
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $flashSaleEnd = sanitize($this->getInput('flash_sale_end', ''));
            if (!empty($flashSaleEnd)) {
                // Định dạng chuẩn yyyy-MM-dd HH:mm:ss
                $flashSaleEndFormatted = date('Y-m-d H:i:s', strtotime($flashSaleEnd));
                $this->settingModel->set('flash_sale_end', $flashSaleEndFormatted);
            }
            
            $keys = [
                'shop_hotline', 'shop_email', 'shop_address', 'shop_facebook', 
                'shipping_fee_standard', 'shipping_fee_free_amount',
                'telegram_bot_token', 'telegram_chat_id', 'telegram_notify_order',
                'telegram_api_url'
            ];
            foreach ($keys as $key) {
                $val = sanitize($this->getInput($key, ''));
                $this->settingModel->set($key, $val);
            }
            
            setFlash('success', 'Cập nhật cài đặt hệ thống thành công!');
            $this->redirect(url('admin/settings'));
            return;
        }
        
        $flashSaleEnd = $this->settingModel->get('flash_sale_end', date('Y-m-d 23:59:59'));
        // Chuyển đổi định dạng sang datetime-local (Y-m-d\TH:i)
        $flashSaleEndLocal = date('Y-m-d\TH:i', strtotime($flashSaleEnd));
        
        $this->view('admin.settings.index', [
            'title' => 'Cài đặt hệ thống - Admin',
            'flashSaleEnd' => $flashSaleEndLocal,
            'shop_hotline' => $this->settingModel->get('shop_hotline', '0336.120.405'),
            'shop_email' => $this->settingModel->get('shop_email', 'vuhuan123890@gmail.com'),
            'shop_address' => $this->settingModel->get('shop_address', '100+ Cửa hàng toàn quốc'),
            'shop_facebook' => $this->settingModel->get('shop_facebook', 'https://facebook.com'),
            'shipping_fee_standard' => $this->settingModel->get('shipping_fee_standard', '30000'),
            'shipping_fee_free_amount' => $this->settingModel->get('shipping_fee_free_amount', '500000'),
            'telegram_bot_token' => $this->settingModel->get('telegram_bot_token', ''),
            'telegram_chat_id' => $this->settingModel->get('telegram_chat_id', ''),
            'telegram_notify_order' => $this->settingModel->get('telegram_notify_order', '1'),
            'telegram_api_url' => $this->settingModel->get('telegram_api_url', defined('TELEGRAM_API_URL') ? TELEGRAM_API_URL : 'https://telegram-proxy.vuhuan123890.workers.dev'),
        ]);
    }

    /**
     * Gửi tin nhắn thử nghiệm tới Telegram
     */
    public function testTelegram() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url('admin/settings'));
            return;
        }
        $this->validateCsrf();

        $token = sanitize($this->getInput('token', ''));
        $chatId = sanitize($this->getInput('chat_id', ''));
        $apiUrl = sanitize($this->getInput('api_url', ''));

        if (!empty($apiUrl)) {
            $this->settingModel->set('telegram_api_url', $apiUrl);
        }
        if (!empty($token)) {
            $this->settingModel->set('telegram_bot_token', $token);
        }
        if (!empty($chatId)) {
            $this->settingModel->set('telegram_chat_id', $chatId);
        }
        // Làm mới cache cấu hình
        getSetting('', '', true);

        if (empty($token) || empty($chatId)) {
            setFlash('error', 'Vui lòng nhập đầy đủ Telegram Bot Token và Chat ID trước khi test.');
            $this->redirect(url('admin/settings'));
            return;
        }

        $testMsg = "🚀 <b>THÔNG BÁO TEST TỪ VŨ HUÂN SHOP</b>\n"
                 . "━━━━━━━━━━━━━━━━━━━━\n"
                 . "✅ Kết nối Telegram Bot thành công mỹ mãn!\n"
                 . "⏰ Thời gian: " . date('d/m/Y H:i:s') . "\n"
                 . "🔔 Kể từ bây giờ, mọi đơn hàng mới trên web sẽ tự động gửi thông báo về đây ngay lập tức.";

        $res = sendTelegramNotification($testMsg, $token, $chatId, $apiUrl);
        
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($res);
            exit;
        }

        if ($res['success']) {
            setFlash('success', 'Gửi tin nhắn test thành công! Hãy kiểm tra ứng dụng Telegram của bạn.');
        } else {
            setFlash('error', 'Lỗi khi gửi Telegram: ' . $res['message']);
        }

        $this->redirect(url('admin/settings'));
    }
}
?>
