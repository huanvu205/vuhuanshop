<?php
define('VNPAY_HASH_SECRET', 'local-test-secret-never-used-for-network');
define('VNPAY_TMN_CODE', 'TESTCODE');
define('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
require_once __DIR__ . '/../app/Services/VnpayService.php';

function expect($condition, $message) {
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

$response = [
    'vnp_ResponseId'=>'response-1', 'vnp_Command'=>'refund', 'vnp_ResponseCode'=>'00',
    'vnp_Message'=>'Success', 'vnp_TmnCode'=>'TESTCODE', 'vnp_TxnRef'=>'ORDER_123',
    'vnp_Amount'=>'10000000', 'vnp_BankCode'=>'', 'vnp_PayDate'=>'',
    'vnp_TransactionNo'=>'123456', 'vnp_TransactionType'=>'02',
    'vnp_TransactionStatus'=>'00', 'vnp_OrderInfo'=>'Refund test',
];
$signKeys = ['vnp_ResponseId','vnp_Command','vnp_ResponseCode','vnp_Message','vnp_TmnCode','vnp_TxnRef','vnp_Amount','vnp_BankCode','vnp_PayDate','vnp_TransactionNo','vnp_TransactionType','vnp_TransactionStatus','vnp_OrderInfo'];
$response['vnp_SecureHash'] = hash_hmac('sha512', implode('|', array_map(function($key) use ($response) { return $response[$key]; }, $signKeys)), VNPAY_HASH_SECRET);
expect(VnpayService::verifyRefundResponse($response), 'accept a correctly signed response');
$tampered = $response;
$tampered['vnp_Amount'] = '10000001';
expect(!VnpayService::verifyRefundResponse($tampered), 'reject a modified amount');
unset($response['vnp_SecureHash']);
expect(!VnpayService::verifyRefundResponse($response), 'reject a response without a signature');
expect(VnpayService::isConfiguredForSandbox(), 'allow only the configured sandbox host');
echo "VNPay refund signature tests passed (4 assertions).\n";
