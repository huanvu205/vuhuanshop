<?php
$order = $order ?? [];
$emailSent = $emailSent ?? false;
$isPaymentFailed = $isPaymentFailed ?? (($order['payment_status'] ?? '') === 'failed');
$paymentErrorMessage = $paymentErrorMessage ?? null;
?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
/* ===== ORDER SUCCESS PAGE ===== */
.success-page {
    background: var(--bg-light, #f5f5f5);
    min-height: 100vh;
    padding: 48px 0 80px;
}

/* Hero success banner */
.success-hero {
    text-align: center;
    padding: 48px 24px 36px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.success-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4CAF50, #66BB6A, #4CAF50);
    background-size: 200% auto;
    animation: shimmer 2s linear infinite;
}
.success-hero.failed-hero::before {
    background: linear-gradient(90deg, #ef4444, #f97316, #ef4444);
}
@keyframes shimmer {
    0% { background-position: 0% center; }
    100% { background-position: 200% center; }
}

.success-icon-wrap {
    width: 100px; height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4CAF50, #66BB6A);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px;
    box-shadow: 0 8px 32px rgba(76,175,80,0.35);
    animation: bounceIn 0.6s cubic-bezier(0.36, 0.07, 0.19, 0.97);
}
.failed-icon-wrap {
    width: 90px; height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 8px 28px rgba(239,68,68,0.35);
    animation: bounceIn 0.6s cubic-bezier(0.36, 0.07, 0.19, 0.97);
}
.failed-icon-wrap i { font-size: 2.6rem; color: #fff; }

.payment-actions-card {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    border: 1.5px solid #fed7aa;
    box-shadow: 0 4px 16px rgba(249,115,22,0.08);
    text-align: center;
}
.payment-actions-card .action-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #c2410c;
    margin-bottom: 8px;
}
.payment-actions-card .action-desc {
    font-size: 0.9rem;
    color: #475569;
    margin-bottom: 18px;
}
.payment-actions-grid {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-pay-vnpay {
    background: linear-gradient(135deg, #005baa, #0088cc);
    color: #fff !important;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(0,91,170,0.25);
    transition: all 0.2s;
}
.btn-pay-vnpay:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,91,170,0.35); }

.btn-pay-cod {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff !important;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(16,185,129,0.25);
    transition: all 0.2s;
}
.btn-pay-cod:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(16,185,129,0.35); }

.btn-cancel-unpaid {
    background: #f8fafc;
    color: #64748b !important;
    border: 1.5px solid #cbd5e1;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}
.btn-cancel-unpaid:hover { background: #f1f5f9; color: #334155 !important; }
@keyframes bounceIn {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.15); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}
.success-icon-wrap i { font-size: 2.8rem; color: #fff; }

.success-hero h1 {
    font-size: 1.9rem;
    font-weight: 800;
    color: #1a1a1a;
    margin-bottom: 10px;
}
.success-hero .subtitle {
    font-size: 1rem;
    color: #666;
    margin-bottom: 24px;
}

/* Order number badge */
.order-number-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    background: linear-gradient(135deg, #f0fff4, #e8f5e9);
    border: 1.5px dashed #66BB6A;
    border-radius: 12px;
    padding: 12px 20px;
    margin-bottom: 16px;
    max-width: 100%;
    box-sizing: border-box;
}
.order-number-badge .label { font-size: 0.82rem; color: #666; margin-bottom: 2px; }
.order-number-badge .number { 
    font-size: clamp(0.92rem, 3.8vw, 1.3rem); 
    font-weight: 800; 
    color: #2e7d32; 
    letter-spacing: 0.5px;
    word-break: break-all;
    overflow-wrap: anywhere;
    line-height: 1.3;
}
.order-number-badge .copy-btn {
    background: none; border: none; cursor: pointer;
    color: #4CAF50; font-size: 1.15rem; padding: 6px;
    border-radius: 6px; transition: all 0.2s;
    flex-shrink: 0;
}
.order-number-badge .copy-btn:hover { background: rgba(76,175,80,0.1); }

.success-hero .email-note {
    font-size: 0.83rem;
    color: #888;
}
.success-hero .email-note strong { color: #333; }

/* Info cards grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 576px) { .info-grid { grid-template-columns: 1fr; } }

.info-card {
    background: #fff;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border-left: 4px solid transparent;
    transition: transform 0.2s;
}
.info-card:hover { transform: translateY(-2px); }
.info-card.delivery { border-left-color: #2196F3; }
.info-card.payment-card { border-left-color: #FF9800; }
.info-card.shipping { border-left-color: #9C27B0; }
.info-card.total-card { border-left-color: #E53935; }

.info-card .card-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem;
    margin-bottom: 10px;
}
.info-card.delivery .card-icon { background: #e3f2fd; color: #1565c0; }
.info-card.payment-card .card-icon { background: #fff3e0; color: #e65100; }
.info-card.shipping .card-icon { background: #f3e5f5; color: #7b1fa2; }
.info-card.total-card .card-icon { background: #ffebee; color: #c62828; }

.info-card .card-label { font-size: 0.75rem; color: #888; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
.info-card .card-value { font-size: 0.95rem; font-weight: 700; color: #222; }
.info-card .card-sub { font-size: 0.78rem; color: #888; margin-top: 3px; }

/* Order items section */
.order-section {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    overflow: hidden;
    margin-bottom: 24px;
}
.order-section-header {
    display: flex; align-items: center; gap: 12px;
    padding: 18px 24px;
    border-bottom: 1px solid #f5f5f5;
    background: #fafafa;
}
.order-section-header h5 { margin: 0; font-size: 1rem; font-weight: 700; color: #222; }
.order-section-body { padding: 0 24px; }

.order-product-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 0;
    border-bottom: 1px solid #f5f5f5;
    animation: slideIn 0.4s ease both;
}
.order-product-row:last-child { border-bottom: none; }
@keyframes slideIn {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
}
.prod-img {
    width: 66px; height: 66px;
    border-radius: 12px;
    object-fit: cover;
    border: 1px solid #f0f0f0;
    background: #f8f8f8;
    display: flex; align-items: center; justify-content: center;
    color: #ccc; font-size: 1.6rem;
    flex-shrink: 0;
}
.prod-img img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }
.prod-info { flex: 1; }
.prod-name { font-size: 0.9rem; font-weight: 700; color: #222; line-height: 1.4; }
.prod-variant { font-size: 0.78rem; color: #888; margin-top: 3px; }
.prod-qty { display: inline-block; margin-top: 5px; font-size: 0.78rem; color: #fff; background: #888; border-radius: 20px; padding: 2px 8px; }
.prod-price-wrap { text-align: right; }
.prod-unit-price { font-size: 0.78rem; color: #888; }
.prod-subtotal { font-size: 1rem; font-weight: 800; color: var(--primary, #E53935); }

/* Price summary final */
.final-summary {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    padding: 24px;
    margin-bottom: 24px;
}
.sum-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 9px 0;
    font-size: 0.9rem;
    color: #555;
    border-bottom: 1px dashed #f0f0f0;
}
.sum-row:last-of-type { border-bottom: none; }
.sum-row .val { font-weight: 600; color: #222; }
.sum-row .val.green { color: #4CAF50; }
.sum-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
    padding: 16px 20px;
    background: linear-gradient(135deg, #fff5f5, #fff0f0);
    border-radius: 14px;
    border: 1.5px solid rgba(229,57,53,0.2);
}
.sum-total .lbl { font-size: 1rem; font-weight: 700; color: #333; }
.sum-total .amt { font-size: 1.5rem; font-weight: 900; color: var(--primary, #E53935); }

/* Action buttons */
.action-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-view-order {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 32px;
    background: linear-gradient(135deg, #E53935, #c62828);
    color: #fff;
    border: none; border-radius: 14px;
    font-size: 0.95rem; font-weight: 700;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 5px 18px rgba(229,57,53,0.3);
}
.btn-view-order:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(229,57,53,0.4); color: #fff; }

.btn-continue-shopping {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 32px;
    background: #fff;
    color: #333;
    border: 2px solid #e0e0e0;
    border-radius: 14px;
    font-size: 0.95rem; font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
}
.btn-continue-shopping:hover { border-color: var(--primary, #E53935); color: var(--primary, #E53935); transform: translateY(-2px); }

/* Confetti animation */
.confetti-container {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    pointer-events: none; z-index: 9999;
    overflow: hidden;
}
.confetti-piece {
    position: absolute;
    width: 10px; height: 10px;
    animation: confettiFall linear forwards;
    border-radius: 2px;
}
@keyframes confettiFall {
    0% { transform: translateY(-20px) rotate(0deg); opacity: 1; }
    100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
}

/* Status steps */
.delivery-steps {
    display: flex;
    justify-content: space-between;
    position: relative;
    padding: 20px 24px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    margin-bottom: 24px;
    overflow-x: auto;
}
.delivery-steps::before {
    content: '';
    position: absolute;
    top: 44px;
    left: 60px; right: 60px;
    height: 2px;
    background: #e0e0e0;
    z-index: 0;
}
.delivery-step {
    display: flex; flex-direction: column; align-items: center;
    gap: 8px; position: relative; z-index: 1;
    min-width: 80px;
}
.ds-icon {
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
}
.ds-icon.done { background: #4CAF50; color: #fff; box-shadow: 0 3px 10px rgba(76,175,80,0.3); }
.ds-icon.active { background: var(--primary, #E53935); color: #fff; box-shadow: 0 3px 10px rgba(229,57,53,0.3); animation: pulse 1.5s infinite; }
.ds-icon.pending { background: #e0e0e0; color: #bbb; }
@keyframes pulse {
    0%, 100% { box-shadow: 0 3px 10px rgba(229,57,53,0.3); }
    50% { box-shadow: 0 5px 20px rgba(229,57,53,0.5); }
}
.ds-label { font-size: 0.72rem; font-weight: 600; color: #555; text-align: center; white-space: nowrap; }
.ds-label.done { color: #4CAF50; }
.ds-label.active { color: var(--primary, #E53935); }

@media (max-width: 576px) {
    .success-hero {
        padding: 28px 14px 22px;
        border-radius: 16px;
    }
    .order-number-badge {
        width: 100%;
        padding: 10px 12px;
        gap: 8px;
    }
}
</style>

<!-- Confetti container -->
<div class="confetti-container" id="confetti"></div>

<div class="success-page">
    <div class="container" style="max-width: 780px;">

        <?php if ($isPaymentFailed): ?>
            <!-- Payment Failed / Cancelled Hero -->
            <div class="success-hero failed-hero">
                <div class="failed-icon-wrap">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 style="color: #b91c1c;">Thanh toán chưa hoàn tất!</h1>
                <p class="subtitle" style="font-size:1.05rem; font-weight:500; color: #b91c1c; margin-bottom: 20px;">
                    <?= e($paymentErrorMessage ?? 'Giao dịch qua VNPAY đã bị hủy hoặc thanh toán không thành công.') ?>
                    <br><span style="color:#64748b; font-size:0.95rem; font-weight:400;">Đơn hàng của bạn đã được ghi nhận ở trạng thái <strong>Chờ thanh toán</strong>. Bạn có thể chọn thanh toán lại hoặc chuyển sang hình thức khác bên dưới:</span>
                </p>

                <div class="order-number-badge" style="border-color: #fca5a5; background: #fff5f5;">
                    <div style="min-width: 0; flex: 1;">
                        <div class="label" style="color: #991b1b;">Mã đơn hàng</div>
                        <div class="number" id="order-number" style="color: #b91c1c;">#<?= e($order['order_number'] ?? 'TS20240001') ?></div>
                    </div>
                    <button class="copy-btn" onclick="copyOrderNumber()" title="Sao chép mã đơn" style="color: #b91c1c;">
                        <i class="far fa-copy"></i>
                    </button>
                </div>

                <div class="payment-actions-card">
                    <div class="action-title"><i class="fas fa-credit-card me-2"></i>Chọn phương án tiếp tục cho đơn hàng:</div>
                    <div class="action-desc">Bạn có thể thử thanh toán lại với VNPAY, chuyển sang nhận hàng rồi trả tiền mặt (COD), hoặc hủy đơn.</div>
                    <div class="payment-actions-grid">
                        <a href="<?= url('checkout/retryPayment/' . ($order['id'] ?? '')) ?>" class="btn-pay-vnpay">
                            <i class="fas fa-redo"></i> Thanh toán lại qua VNPAY
                        </a>
                        <a href="<?= url('checkout/changeToCod/' . ($order['id'] ?? '')) ?>" class="btn-pay-cod" onclick="return confirm('Chuyển đơn sang nhận hàng thanh toán tiền mặt (COD)? Shop sẽ liên hệ xác nhận và giao hàng tận nơi.');">
                            <i class="fas fa-truck"></i> Chuyển sang nhận hàng trả tiền (COD)
                        </a>
                        <a href="<?= url('checkout/cancelUnpaidOrder/' . ($order['id'] ?? '')) ?>" class="btn-cancel-unpaid" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                            <i class="fas fa-times"></i> Hủy đơn hàng này
                        </a>
                    </div>
                </div>
            </div>

            <!-- Delivery progress for unpaid order -->
            <div class="delivery-steps">
                <div class="delivery-step">
                    <div class="ds-icon done"><i class="fas fa-check"></i></div>
                    <span class="ds-label done">Tạo đơn</span>
                </div>
                <div class="delivery-step">
                    <div class="ds-icon active" style="background:#ef4444; box-shadow:0 3px 10px rgba(239,68,68,0.3);"><i class="fas fa-clock"></i></div>
                    <span class="ds-label active" style="color:#ef4444;">Chờ thanh toán</span>
                </div>
                <div class="delivery-step">
                    <div class="ds-icon pending"><i class="fas fa-box"></i></div>
                    <span class="ds-label">Đóng gói</span>
                </div>
                <div class="delivery-step">
                    <div class="ds-icon pending"><i class="fas fa-shipping-fast"></i></div>
                    <span class="ds-label">Vận chuyển</span>
                </div>
                <div class="delivery-step">
                    <div class="ds-icon pending"><i class="fas fa-home"></i></div>
                    <span class="ds-label">Giao hàng</span>
                </div>
            </div>
        <?php else: ?>
            <!-- Success Hero -->
            <div class="success-hero">
                <div class="success-icon-wrap">
                    <i class="fas fa-check"></i>
                </div>
                <h1>Đặt hàng thành công! 🎉</h1>
                <p class="subtitle" style="font-size:1.05rem; font-weight:500; color: #2e7d32; margin-bottom: 24px;">
                    Chúc mừng anh/chị <strong><?= e($order['ship_name'] ?? 'quý khách') ?></strong> đã đặt hàng thành công!
                    <br>Vũ Huân Shop xin chân thành cảm ơn quý khách.
                </p>

                <div class="order-number-badge">
                    <div style="min-width: 0; flex: 1;">
                        <div class="label">Mã đơn hàng</div>
                        <div class="number" id="order-number">#<?= e($order['order_number'] ?? 'TS20240001') ?></div>
                    </div>
                    <button class="copy-btn" onclick="copyOrderNumber()" title="Sao chép mã đơn">
                        <i class="far fa-copy"></i>
                    </button>
                </div>

                <?php if ($emailSent === true): ?>
                    <p class="email-note">
                        <i class="fas fa-envelope me-1 text-success"></i>
                        Xác nhận đơn hàng đã được gửi tới <strong><?= e($order['ship_email'] ?? '') ?></strong>
                    </p>
                <?php elseif ($emailSent === false): ?>
                    <p class="email-note text-danger">
                        <i class="fas fa-triangle-exclamation me-1"></i>
                        Đơn hàng đã được ghi nhận nhưng email xác nhận chưa gửi được. Vui lòng liên hệ cửa hàng nếu cần hỗ trợ.
                    </p>
                <?php else: ?>
                    <p class="email-note">
                        <i class="fas fa-envelope me-1"></i>
                        Thông tin email nhận xác nhận: <strong><?= e($order['ship_email'] ?? '') ?></strong>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Delivery progress -->
            <div class="delivery-steps">
                <div class="delivery-step">
                    <div class="ds-icon done"><i class="fas fa-check"></i></div>
                    <span class="ds-label done">Đặt hàng</span>
                </div>
                <div class="delivery-step">
                    <div class="ds-icon active"><i class="fas fa-clipboard-check"></i></div>
                    <span class="ds-label active">Xác nhận</span>
                </div>
                <div class="delivery-step">
                    <div class="ds-icon pending"><i class="fas fa-box"></i></div>
                    <span class="ds-label">Đóng gói</span>
                </div>
                <div class="delivery-step">
                    <div class="ds-icon pending"><i class="fas fa-shipping-fast"></i></div>
                    <span class="ds-label">Vận chuyển</span>
                </div>
                <div class="delivery-step">
                    <div class="ds-icon pending"><i class="fas fa-home"></i></div>
                    <span class="ds-label">Giao hàng</span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Bank Transfer VietQR Card -->
        <?php if (($order['payment_method'] ?? '') === 'bank_transfer'): ?>
        <div class="checkout-card mt-4 p-4 text-center border-start-0" style="border-top: 4px solid #FF9800; background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.06); margin-bottom:24px;">
            <h5 class="fw-bold mb-3" style="color: #e65100;">
                <i class="fas fa-qrcode me-2"></i>Thanh toán qua QR Ngân hàng (VietQR)
            </h5>
            <p class="text-muted small mb-4">
                Vui lòng quét mã QR dưới đây hoặc chuyển khoản theo thông tin tài khoản bên cạnh để hoàn tất thanh toán cho đơn hàng.
            </p>
            
            <div class="row align-items-center g-4">
                <div class="col-md-5 text-center border-end">
                    <?php
                    $vietqr_url = "https://img.vietqr.io/image/MB-1204200589999-compact2.png?amount=" . $order['final_amount'] . "&addInfo=THANHTOAN%20" . $order['order_number'] . "&accountName=VU%20VAN%20HUAN";
                    ?>
                    <img src="<?= $vietqr_url ?>" alt="Mã QR chuyển khoản VietQR" style="max-width: 210px; width: 100%; border: 1px solid #eee; border-radius: 12px; padding: 8px; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div class="text-muted small mt-2"><i class="fas fa-mobile-alt me-1"></i>Mở App ngân hàng quét mã QR</div>
                    <div class="small mt-1" style="color: #64748b; font-size: 0.78rem;">(Lưu ý: Trong App ngân hàng, chọn mục <strong>Chuyển tiền &gt; Quét QR</strong>)</div>
                </div>
                
                <div class="col-md-7 text-start">
                    <div style="font-size:0.9rem; line-height:1.8;">
                        <div class="mb-2"><strong>Ngân hàng:</strong> Ngân hàng Quân Đội (MB Bank)</div>
                        <div class="mb-2"><strong>Số tài khoản:</strong> <span class="fw-bold text-primary" style="font-size:1.05rem;" id="transfer-acc">1204200589999</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 ms-2" onclick="copyTransfer('1204200589999')">Sao chép</button>
                        </div>
                        <div class="mb-2"><strong>Chủ tài khoản:</strong> VŨ VĂN HUÂN</div>
                        <div class="mb-2"><strong>Số tiền:</strong> <span class="fw-bold text-danger" style="font-size:1.05rem;"><?= formatPrice($order['final_amount']) ?></span></div>
                        <div class="mb-0"><strong>Nội dung:</strong> <span class="fw-bold text-success" id="transfer-content">THANHTOAN <?= e($order['order_number']) ?></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 ms-2" onclick="copyTransfer('THANHTOAN <?= e($order['order_number']) ?>')">Sao chép</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- MoMo Transfer Card -->
        <?php if (($order['payment_method'] ?? '') === 'momo'): ?>
        <div class="checkout-card mt-4 p-4 text-center border-start-0" style="border-top: 4px solid #ae006b; background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.06); margin-bottom:24px;">
            <h5 class="fw-bold mb-3" style="color: #ae006b;">
                <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="MoMo" style="height: 24px; vertical-align: middle; margin-right: 8px;">Thanh toán qua Ví MoMo
            </h5>
            <p class="text-muted small mb-4">
                Vui lòng quét mã QR dưới đây bằng ứng dụng MoMo hoặc chuyển tiền theo số điện thoại bên cạnh để hoàn tất thanh toán.
            </p>
            
            <div class="row align-items-center g-4">
                <div class="col-md-5 text-center border-end">
                    <?php
                    $momo_qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://nhantien.momo.vn/0918841790";
                    ?>
                    <img src="<?= $momo_qr_url ?>" alt="Mã QR MoMo" style="max-width: 200px; width: 100%; border: 1px solid #eee; border-radius: 12px; padding: 10px; background: #fff;">
                    <div class="text-muted small mt-2"><i class="fas fa-mobile-alt me-1"></i>Mở App MoMo quét mã QR</div>
                </div>
                
                <div class="col-md-7 text-start">
                    <div style="font-size:0.9rem; line-height:1.8;">
                        <div class="mb-2"><strong>Hình thức:</strong> Chuyển tiền qua Số điện thoại Ví MoMo</div>
                        <div class="mb-2"><strong>Số điện thoại:</strong> <span class="fw-bold text-primary" style="font-size:1.05rem;" id="momo-phone">0918841790</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 ms-2" onclick="copyTransfer('0918841790')">Sao chép</button>
                        </div>
                        <div class="mb-2"><strong>Chủ tài khoản:</strong> VU VAN HUAN</div>
                        <div class="mb-2"><strong>Số tiền:</strong> <span class="fw-bold text-danger" style="font-size:1.05rem;"><?= formatPrice($order['final_amount']) ?></span></div>
                        <div class="mb-0"><strong>Nội dung:</strong> <span class="fw-bold text-success" id="transfer-content">THANHTOAN <?= e($order['order_number']) ?></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 ms-2" onclick="copyTransfer('THANHTOAN <?= e($order['order_number']) ?>')">Sao chép</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <script>
        function copyTransfer(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Đã sao chép thành công!');
            });
        }
        </script>

        <!-- Info cards grid -->
        <div class="info-grid">
            <div class="info-card delivery">
                <div class="card-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="card-label">Giao đến</div>
                <div class="card-value"><?= e($order['ship_name'] ?? 'Khách hàng') ?></div>
                <div class="card-sub"><?= e($order['ship_phone'] ?? '') ?> &bull; <?= e(formatOrderAddress($order)) ?></div>
            </div>
            <div class="info-card payment-card">
                <div class="card-icon"><i class="fas fa-wallet"></i></div>
                <div class="card-label">Thanh toán</div>
                <div class="card-value">
                    <?php
                    $payLabels = [
                        'cod' => 'Thanh toán khi nhận',
                        'bank_transfer' => 'Chuyển khoản',
                        'momo' => 'Ví MoMo',
                        'vnpay' => 'VNPAY',
                    ];
                    echo e($payLabels[$order['payment_method'] ?? 'cod'] ?? 'COD');
                    ?>
                </div>
                <div class="card-sub">
                    <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                        <span class="text-success"><i class="fas fa-check-circle me-1"></i>Đã thanh toán</span>
                    <?php elseif ($isPaymentFailed): ?>
                        <span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i>Chưa thanh toán (Thất bại/Hủy)</span>
                    <?php else: ?>
                        <span class="text-warning"><i class="fas fa-clock me-1"></i>Chờ thanh toán</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="info-card shipping">
                <div class="card-icon"><i class="fas fa-truck"></i></div>
                <div class="card-label">Dự kiến giao</div>
                <div class="card-value">2 - 3 ngày làm việc</div>
                <div class="card-sub">
                    <?= date('d/m/Y', strtotime('+2 days')) ?> - <?= date('d/m/Y', strtotime('+4 days')) ?>
                </div>
            </div>
            <div class="info-card total-card">
                <div class="card-icon"><i class="fas fa-receipt"></i></div>
                <div class="card-label">Tổng tiền</div>
                <div class="card-value" style="color: var(--primary, #E53935); font-size: 1.15rem;">
                    <?= formatPrice($order['final_amount'] ?? 0) ?>
                </div>
                <div class="card-sub">Đã bao gồm VAT & phí ship</div>
            </div>
        </div>

        <!-- Order items -->
        <div class="order-section">
            <div class="order-section-header">
                <i class="fas fa-shopping-bag text-primary"></i>
                <h5>Sản phẩm đã đặt</h5>
                <span class="badge bg-primary-subtle text-primary ms-auto" style="font-size:0.78rem;"><?= count($orderDetails ?? []) ?> sản phẩm</span>
            </div>
            <div class="order-section-body">
                <?php if (!empty($orderDetails)): ?>
                    <?php foreach ($orderDetails as $i => $item): ?>
                    <div class="order-product-row" style="animation-delay: <?= $i * 0.08 ?>s;">
                        <div class="prod-img">
                            <?php if (!empty($item['main_image'])): ?>
                                <img src="<?= productImage($item['main_image']) ?>" alt="<?= e($item['product_name']) ?>" onerror="this.onerror=null;this.src='<?= APP_URL ?>/public/images/no-image.png'">
                            <?php else: ?>
                                <i class="fas fa-mobile-alt"></i>
                            <?php endif; ?>
                        </div>
                        <div class="prod-info">
                            <div class="prod-name"><?= e($item['product_name']) ?></div>
                            <?php if (!empty($item['variant'])): ?>
                                <div class="prod-variant"><?= e($item['variant']) ?></div>
                            <?php endif; ?>
                            <span class="prod-qty">x<?= (int)$item['quantity'] ?></span>
                        </div>
                        <div class="prod-price-wrap">
                            <div class="prod-unit-price"><?= formatPrice($item['price'] ?? 0) ?>/cái</div>
                            <div class="prod-subtotal"><?= formatPrice($item['subtotal'] ?? 0) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                        Không có sản phẩm
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Final price summary -->
        <div class="final-summary">
            <h6 class="fw-bold mb-16" style="color:#222;margin-bottom:14px;">
                <i class="fas fa-calculator me-2 text-muted"></i>Chi tiết giá trị
            </h6>
            <div class="sum-row">
                <span>Tạm tính</span>
                <span class="val"><?= formatPrice($order['total_amount'] ?? 0) ?></span>
            </div>
            <?php if (($order['discount_amount'] ?? 0) > 0): ?>
            <div class="sum-row">
                <span><i class="fas fa-tag me-1 text-success"></i>Giảm giá</span>
                <span class="val green">-<?= formatPrice($order['discount_amount']) ?></span>
            </div>
            <?php endif; ?>
            <div class="sum-row">
                <span><i class="fas fa-truck me-1 text-muted"></i>Phí vận chuyển</span>
                <span class="val">
                    <?php if (($order['shipping_fee'] ?? 0) == 0): ?>
                        <span class="text-success">Miễn phí</span>
                    <?php else: ?>
                        <?= formatPrice($order['shipping_fee']) ?>
                    <?php endif; ?>
                </span>
            </div>
            <div class="sum-total">
                <span class="lbl"><i class="fas fa-money-bill-wave me-2 text-danger"></i>Tổng thanh toán</span>
                <span class="amt"><?= formatPrice($order['final_amount'] ?? 0) ?></span>
            </div>
        </div>

        <!-- Action buttons -->
        <div class="action-buttons">
            <a href="<?= url('orders/' . ($order['id'] ?? '')) ?>" class="btn-view-order">
                <i class="fas fa-box"></i>
                Xem chi tiết đơn hàng
            </a>
            <a href="<?= url('orders/invoice/' . ($order['id'] ?? '')) ?>" target="_blank" class="btn btn-outline-danger" style="display:inline-flex; align-items:center; gap:8px; padding:14px 32px; border-radius:14px; font-size:0.95rem; font-weight:700; text-decoration:none; border: 2.5px solid var(--primary); color: var(--primary);">
                <i class="fas fa-file-invoice"></i>
                Xuất hóa đơn
            </a>
            <a href="<?= url() ?>" class="btn-continue-shopping">
                <i class="fas fa-arrow-left"></i>
                Tiếp tục mua sắm
            </a>
        </div>

        <!-- Support note -->
        <div class="text-center mt-4 text-muted" style="font-size:0.83rem;">
            <i class="fas fa-headset me-1"></i>
            Cần hỗ trợ? Gọi <a href="tel:0336120405" style="color:var(--primary);font-weight:600;">0336.120.405</a> hoặc chat với chúng tôi.
        </div>

    </div>
</div>

<script>
// Confetti animation
<?php if (!$isPaymentFailed): ?>
(function spawnConfetti() {
    const container = document.getElementById('confetti');
    if (!container) return;
    const colors = ['#E53935','#4CAF50','#2196F3','#FF9800','#9C27B0','#FFD600'];
    for (let i = 0; i < 80; i++) {
        const piece = document.createElement('div');
        piece.className = 'confetti-piece';
        piece.style.left = Math.random() * 100 + 'vw';
        piece.style.top = '-10px';
        piece.style.background = colors[Math.floor(Math.random() * colors.length)];
        piece.style.width = (Math.random() * 8 + 6) + 'px';
        piece.style.height = (Math.random() * 8 + 6) + 'px';
        piece.style.animationDuration = (Math.random() * 2 + 2) + 's';
        piece.style.animationDelay = Math.random() * 1.5 + 's';
        piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
        container.appendChild(piece);
    }
    setTimeout(() => { container.innerHTML = ''; }, 5000);
})();
<?php endif; ?>

// Copy order number
function copyOrderNumber() {
    const num = document.getElementById('order-number').textContent.trim();
    navigator.clipboard.writeText(num).then(() => {
        const btn = document.querySelector('.copy-btn');
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.style.color = '#4CAF50';
        setTimeout(() => { btn.innerHTML = '<i class="far fa-copy"></i>'; btn.style.color = ''; }, 2000);
    });
}

// Animate cards in sequence
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.info-card');
    cards.forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(16px)';
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 300 + i * 100);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

