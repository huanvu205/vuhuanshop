<?php
/** VNPay refund client restricted to the official sandbox endpoint. */
class VnpayService {
    const REFUND_URL = 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction';

    public static function isConfiguredForSandbox() {
        $paymentUrl = defined('VNPAY_URL') ? parse_url(VNPAY_URL, PHP_URL_HOST) : null;
        return defined('VNPAY_TMN_CODE') && VNPAY_TMN_CODE !== ''
            && defined('VNPAY_HASH_SECRET') && VNPAY_HASH_SECRET !== ''
            && $paymentUrl === 'sandbox.vnpayment.vn'
            && function_exists('curl_init');
    }

    public static function refund(array $input) {
        if (!self::isConfiguredForSandbox()) {
            return ['verified' => false, 'state' => 'rejected', 'error' => 'VNPay Sandbox chưa được cấu hình hoặc PHP thiếu cURL.'];
        }
        $required = ['vnp_RequestId','vnp_TransactionType','vnp_TxnRef','vnp_Amount','vnp_TransactionNo','vnp_TransactionDate','vnp_CreateBy','vnp_OrderInfo'];
        foreach ($required as $key) if (!isset($input[$key]) || $input[$key] === '') return ['verified'=>false,'state'=>'rejected','error'=>'Thiếu dữ liệu hoàn tiền: '.$key];

        $fields = [
            'vnp_RequestId' => (string)$input['vnp_RequestId'],
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'refund',
            'vnp_TmnCode' => VNPAY_TMN_CODE,
            'vnp_TransactionType' => (string)$input['vnp_TransactionType'],
            'vnp_TxnRef' => (string)$input['vnp_TxnRef'],
            'vnp_Amount' => (string)((int)$input['vnp_Amount']),
            'vnp_TransactionNo' => (string)$input['vnp_TransactionNo'],
            'vnp_TransactionDate' => (string)$input['vnp_TransactionDate'],
            'vnp_CreateBy' => mb_substr((string)$input['vnp_CreateBy'], 0, 100),
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_IpAddr' => self::getServerIp(),
            'vnp_OrderInfo' => mb_substr((string)$input['vnp_OrderInfo'], 0, 200),
        ];
        $signFields = ['vnp_RequestId','vnp_Version','vnp_Command','vnp_TmnCode','vnp_TransactionType','vnp_TxnRef','vnp_Amount','vnp_TransactionNo','vnp_TransactionDate','vnp_CreateBy','vnp_CreateDate','vnp_IpAddr','vnp_OrderInfo'];
        $fields['vnp_SecureHash'] = hash_hmac('sha512', implode('|', array_map(function($key) use ($fields) { return $fields[$key]; }, $signFields)), VNPAY_HASH_SECRET);

        $ch = curl_init(self::REFUND_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $body = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($body === false || $httpCode < 200 || $httpCode >= 300) {
            return ['verified'=>false,'state'=>'unknown','error'=>'Không nhận được phản hồi chắc chắn từ VNPay. '.$curlError];
        }
        $response = json_decode($body, true);
        if (!is_array($response) || !self::verifyRefundResponse($response)) {
            return ['verified'=>false,'state'=>'unknown','error'=>'Phản hồi VNPay không hợp lệ hoặc chữ ký sai.','response'=>$body];
        }
        $responseCode = (string)($response['vnp_ResponseCode'] ?? '');
        $transactionStatus = (string)($response['vnp_TransactionStatus'] ?? '');
        if ($responseCode === '00' && $transactionStatus === '00') $state = 'succeeded';
        elseif ($responseCode === '00' && in_array($transactionStatus, ['05','06'], true)) $state = 'processing';
        elseif ($responseCode === '94') $state = 'unknown';
        else $state = 'rejected';
        return ['verified'=>true,'state'=>$state,'response'=>$response];
    }

    public static function verifyRefundResponse(array $data) {
        if (!defined('VNPAY_HASH_SECRET') || VNPAY_HASH_SECRET === '' || empty($data['vnp_SecureHash'])) return false;
        $keys = ['vnp_ResponseId','vnp_Command','vnp_ResponseCode','vnp_Message','vnp_TmnCode','vnp_TxnRef','vnp_Amount','vnp_BankCode','vnp_PayDate','vnp_TransactionNo','vnp_TransactionType','vnp_TransactionStatus','vnp_OrderInfo'];
        $parts = [];
        foreach ($keys as $key) $parts[] = (string)($data[$key] ?? '');
        $expected = hash_hmac('sha512', implode('|', $parts), VNPAY_HASH_SECRET);
        return hash_equals($expected, (string)$data['vnp_SecureHash']);
    }

    private static function getServerIp() {
        $ip = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
    }
}
