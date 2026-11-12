<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
/* ===== CHECKOUT PAGE ===== */
.checkout-page {
    background: var(--bg-light, #f5f5f5);
    min-height: 100vh;
    padding: 32px 0 60px;
}

/* Breadcrumb */
.checkout-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 28px;
    font-size: 0.875rem;
    color: #888;
}
.checkout-breadcrumb a { color: var(--primary, #E53935); text-decoration: none; }
.checkout-breadcrumb a:hover { text-decoration: underline; }
.checkout-breadcrumb .sep { color: #ccc; }
.checkout-breadcrumb .current { color: #333; font-weight: 600; }

/* Steps indicator */
.checkout-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 36px;
}
.checkout-step {
    display: flex;
    align-items: center;
    gap: 10px;
}
.step-circle {
    width: 38px; height: 38px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.95rem;
    transition: all 0.3s;
}
.step-circle.active {
    background: var(--primary, #E53935);
    color: #fff;
    box-shadow: 0 4px 15px rgba(229,57,53,0.35);
}
.step-circle.done {
    background: #4CAF50;
    color: #fff;
}
.step-circle.inactive {
    background: #e0e0e0;
    color: #999;
}
.step-label {
    font-size: 0.82rem;
    font-weight: 600;
    color: #555;
}
.step-label.active { color: var(--primary, #E53935); }
.step-connector {
    width: 60px; height: 2px;
    background: #e0e0e0;
    margin: 0 8px;
}
.step-connector.done { background: #4CAF50; }

/* Card sections */
.checkout-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    overflow: hidden;
    margin-bottom: 20px;
}
.checkout-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 24px;
    border-bottom: 1px solid var(--border-light, #f0f0f0);
    background: linear-gradient(135deg, #fff 0%, #fff8f8 100%);
}
.checkout-card-header .icon-wrap {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: rgba(229,57,53,0.1);
    display: flex; align-items: center; justify-content: center;
    color: var(--primary, #E53935);
    font-size: 1.1rem;
}
.checkout-card-header h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #222;
}
.checkout-card-body { padding: 24px; }

/* Form styling */
.form-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #444;
    margin-bottom: 6px;
}
.form-label .required { color: var(--primary, #E53935); margin-left: 2px; }
.form-control, .form-select {
    border: 1.5px solid #e8e8e8;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 0.9rem;
    transition: all 0.2s;
    background: #fafafa;
}
.form-control:focus, .form-select:focus {
    border-color: var(--primary, #E53935);
    box-shadow: 0 0 0 3px rgba(229,57,53,0.1);
    background: #fff;
}
.form-control::placeholder { color: #bbb; }

/* Payment methods */
.payment-methods { display: flex; flex-direction: column; gap: 12px; }
.payment-method-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.25s;
    position: relative;
    background: #fafafa;
}
.payment-method-card:hover {
    border-color: rgba(229,57,53,0.4);
    background: #fff8f8;
    transform: translateX(3px);
}
.payment-method-card.selected {
    border-color: var(--primary, #E53935);
    background: #fff5f5;
    box-shadow: 0 3px 12px rgba(229,57,53,0.15);
}
.payment-method-card input[type="radio"] { display: none; }
.payment-radio-custom {
    width: 20px; height: 20px;
    border: 2px solid #ccc;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.payment-method-card.selected .payment-radio-custom {
    border-color: var(--primary, #E53935);
    background: var(--primary, #E53935);
}
.payment-method-card.selected .payment-radio-custom::after {
    content: '';
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #fff;
}
.payment-icon {
    width: 48px; height: 32px;
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
    font-weight: 800;
}
.payment-icon.cod { background: #fff3e0; color: #e65100; }
.payment-icon.bank { background: #e3f2fd; color: #1565c0; }
.payment-icon.momo { background: linear-gradient(135deg, #ae006b, #d5006d); color: #fff; font-size: 0.7rem; letter-spacing: -0.5px; }
.payment-icon.vnpay { background: linear-gradient(135deg, #005a8e, #0070b9); color: #fff; font-size: 0.65rem; font-weight: 900; }
.payment-method-info { flex: 1; }
.payment-method-info strong { display: block; font-size: 0.92rem; color: #222 !important; margin-bottom: 2px; }
.payment-method-info small { color: #666 !important; font-size: 0.78rem; }
.payment-badge {
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 600;
}

/* Order review items */
.order-item-review {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 0;
    border-bottom: 1px solid #f5f5f5;
}
.order-item-review:last-child { border-bottom: none; }
.order-item-img {
    width: 60px; height: 60px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid #f0f0f0;
    background: #f5f5f5;
    display: flex; align-items: center; justify-content: center;
    color: #ccc; font-size: 1.5rem;
    flex-shrink: 0;
}
.order-item-img img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; }
.order-item-name { flex: 1; font-size: 0.88rem; font-weight: 600; color: #333; line-height: 1.4; }
.order-item-qty { font-size: 0.8rem; color: #888; margin-top: 3px; }
.order-item-price { font-weight: 700; color: var(--primary, #E53935); font-size: 0.92rem; white-space: nowrap; }

/* Price summary */
.price-summary { width: 100%; }
.price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 9px 0;
    font-size: 0.9rem;
    color: #555;
    border-bottom: 1px dashed #f0f0f0;
}
.price-row:last-child { border-bottom: none; }
.price-row .label { display: flex; align-items: center; gap: 6px; }
.price-row .value { font-weight: 600; color: #222; }
.price-row .discount { color: #4CAF50; }
.price-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    background: linear-gradient(135deg, #fff5f5, #fff8f8);
    border-radius: 12px;
    margin-top: 12px;
    border: 1.5px solid rgba(229,57,53,0.15);
}
.price-total-row .total-label { font-weight: 700; color: #222; font-size: 1rem; }
.price-total-row .total-value { font-size: 1.4rem; font-weight: 800; color: var(--primary, #E53935); }

/* Coupon input */
.coupon-wrap {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
}
.coupon-wrap .form-control { border-radius: 10px; flex: 1; }
.btn-coupon {
    background: transparent;
    border: 2px solid var(--primary, #E53935);
    color: var(--primary, #E53935);
    border-radius: 10px;
    padding: 8px 18px;
    font-weight: 600;
    font-size: 0.85rem;
    white-space: nowrap;
    transition: all 0.2s;
}
.btn-coupon:hover { background: var(--primary, #E53935); color: #fff; }

/* Submit button */
.btn-place-order {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #E53935, #c62828);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: 0.3px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 6px 20px rgba(229,57,53,0.35);
    margin-top: 8px;
}
.btn-place-order:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(229,57,53,0.45);
    color: #fff;
}
.btn-place-order:active { transform: translateY(0); }
.btn-place-order i { font-size: 1.1rem; }

/* Security badges */
.security-badges {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #f0f0f0;
}
.security-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: #888;
}
.security-badge i { color: #4CAF50; }

/* Sidebar sticky */
.checkout-sidebar { position: sticky; top: 20px; }

/* Responsive */
@media (max-width: 991px) {
    .checkout-sidebar { position: static; }
    .step-connector { width: 40px; }
}
@media (max-width: 576px) {
    .checkout-card-body { padding: 16px; }
    .payment-method-card { padding: 12px 14px; }
    .step-label { display: none; }
}
</style>

<div class="checkout-page">
    <div class="container">

        <!-- Breadcrumb -->
        <div class="checkout-breadcrumb">
            <a href="<?= url() ?>"><i class="fas fa-home"></i> Trang chủ</a>
            <span class="sep"><i class="fas fa-chevron-right fa-xs"></i></span>
            <a href="<?= url('cart') ?>">Giỏ hàng</a>
            <span class="sep"><i class="fas fa-chevron-right fa-xs"></i></span>
            <span class="current">Thanh toán</span>
        </div>

        <!-- Steps -->
        <div class="checkout-steps">
            <div class="checkout-step">
                <div class="step-circle done"><i class="fas fa-check"></i></div>
                <span class="step-label">Giỏ hàng</span>
            </div>
            <div class="step-connector done"></div>
            <div class="checkout-step">
                <div class="step-circle active">2</div>
                <span class="step-label active">Thanh toán</span>
            </div>
            <div class="step-connector"></div>
            <div class="checkout-step">
                <div class="step-circle inactive">3</div>
                <span class="step-label">Hoàn tất</span>
            </div>
        </div>

        <form action="<?= url('checkout/process') ?>" method="POST" id="checkout-form">
            <?= csrfToken() ?>

            <div class="row g-4">
                <!-- LEFT: Form -->
                <div class="col-lg-7">

                    <!-- Shipping info -->
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <div class="icon-wrap"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h5>Thông tin giao hàng</h5>
                                <small class="text-muted">Vui lòng điền đầy đủ thông tin nhận hàng</small>
                            </div>
                        </div>
                        <div class="checkout-card-body">
                            <?php
                            $savedAddress = trim($user['address'] ?? '');
                            $savedCity = trim($user['city'] ?? '');
                            $savedPhone = trim($user['phone'] ?? '');
                            $hasSavedProfileAddress = !empty($savedAddress) || !empty($savedCity);
                            ?>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label">Họ và tên <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="ship_name"
                                           value="<?= e($user['full_name'] ?? '') ?>"
                                           placeholder="Nguyễn Văn A" required id="ship_name">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Số điện thoại <span class="required">*</span></label>
                                    <input type="tel" class="form-control" name="ship_phone"
                                           value="<?= e($savedPhone) ?>"
                                           placeholder="0912 345 678" required id="ship_phone"
                                           pattern="[0-9]{10,11}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email <span class="required">*</span></label>
                                    <input type="email" class="form-control" name="ship_email"
                                           value="<?= e($user['email'] ?? '') ?>"
                                           placeholder="email@example.com" required id="ship_email">
                                </div>

                                <div class="col-12">
                                    <label class="form-label mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i>Địa chỉ nhận hàng <span class="required">*</span></label>
                                    <?php if (!empty($savedAddress) || !empty($savedCity)): ?>
                                    <div class="p-2 px-3 rounded-2 mb-2 bg-light border text-secondary small">
                                        <i class="fas fa-info-circle text-primary me-1"></i>Vui lòng chọn hoặc xác nhận chính xác Phường/Xã, Quận/Huyện, Tỉnh/TP để giao hàng nhanh chóng.
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Tỉnh / Thành phố <span class="required">*</span></label>
                                    <select class="form-select" name="ship_city" id="ship_city" required>
                                        <option value="">-- Đang tải Tỉnh / TP... --</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">Quận / Huyện <span class="required">*</span></label>
                                    <select class="form-select" name="ship_district" id="ship_district" required disabled>
                                        <option value="">-- Chọn Quận / Huyện --</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">Phường / Xã <span class="required">*</span></label>
                                    <select class="form-select" name="ship_ward" id="ship_ward" required disabled>
                                        <option value="">-- Chọn Phường / Xã --</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Địa chỉ cụ thể (Số nhà, ngõ/đường) <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="ship_address"
                                           placeholder="Số nhà, ngõ/ngách, tên đường..." required id="ship_address"
                                           value="<?= e($savedAddress) ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Ghi chú đơn hàng</label>
                                    <textarea class="form-control" name="customer_note" rows="3"
                                              placeholder="Ghi chú thêm cho shipper (giao giờ nào, để trước cửa...)" id="customer_note"
                                              style="resize: none;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment methods -->
                    <div class="checkout-card">
                        <div class="checkout-card-header">
                            <div class="icon-wrap"><i class="fas fa-credit-card"></i></div>
                            <div>
                                <h5>Phương thức thanh toán</h5>
                                <small class="text-muted">Chọn hình thức thanh toán phù hợp</small>
                            </div>
                        </div>
                        <div class="checkout-card-body">
                            <div class="payment-methods" id="payment-methods">

                                <!-- COD -->
                                <label class="payment-method-card selected" for="pay_cod" onclick="selectPayment(this)">
                                    <input type="radio" name="payment_method" value="cod" id="pay_cod" checked>
                                    <div class="payment-radio-custom"></div>
                                    <div class="payment-icon cod">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="payment-method-info">
                                        <strong>Thanh toán khi nhận hàng (COD)</strong>
                                        <small>Kiểm tra hàng trước khi thanh toán. Miễn phí.</small>
                                    </div>
                                    <span class="payment-badge badge bg-success-subtle text-success">Phổ biến</span>
                                </label>

                                <!-- Bank Transfer -->
                                <label class="payment-method-card" for="pay_bank" onclick="selectPayment(this)">
                                    <input type="radio" name="payment_method" value="bank_transfer" id="pay_bank">
                                    <div class="payment-radio-custom"></div>
                                    <div class="payment-icon bank">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <div class="payment-method-info">
                                        <strong>Chuyển khoản ngân hàng</strong>
                                        <small>Chuyển khoản theo thông tin sau khi đặt hàng.</small>
                                    </div>
                                </label>

                                <!-- MoMo -->
                                <label class="payment-method-card" for="pay_momo" onclick="selectPayment(this)">
                                    <input type="radio" name="payment_method" value="momo" id="pay_momo">
                                    <div class="payment-radio-custom"></div>
                                    <div class="payment-icon momo">MoMo</div>
                                    <div class="payment-method-info">
                                        <strong>Ví MoMo</strong>
                                        <small>Thanh toán nhanh qua ứng dụng MoMo.</small>
                                    </div>
                                    <span class="payment-badge badge bg-danger-subtle text-danger">-2% cho đơn từ 500K</span>
                                </label>

                                <!-- VNPAY -->
                                <label class="payment-method-card" for="pay_vnpay" onclick="selectPayment(this)">
                                    <input type="radio" name="payment_method" value="vnpay" id="pay_vnpay">
                                    <div class="payment-radio-custom"></div>
                                    <div class="payment-icon vnpay">VNPay</div>
                                    <div class="payment-method-info">
                                        <strong>VNPAY / Thẻ ATM / Visa</strong>
                                        <small>Thanh toán qua cổng VNPAY – bảo mật tuyệt đối.</small>
                                    </div>
                                </label>

                            </div>

                            <!-- Bank info (shown when bank_transfer selected) -->
                            <div id="bank-info" class="mt-3 p-3 rounded-3" style="background:#f0f7ff;border:1px solid #c5dff8;display:none;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-info-circle text-primary"></i>
                                    <strong style="font-size:0.85rem;color:#1565c0;">Thông tin chuyển khoản</strong>
                                </div>
                                <table style="font-size:0.85rem;width:100%;">
                                    <tr><td class="pe-3 py-1 text-muted">Ngân hàng:</td><td><strong>Ngân hàng Quân Đội (MB Bank)</strong></td></tr>
                                    <tr><td class="pe-3 py-1 text-muted">Số tài khoản:</td><td><strong>1204200589999</strong> <button type="button" class="btn btn-sm py-0 px-1" onclick="copyText('1204200589999')"><i class="far fa-copy text-primary"></i></button></td></tr>
                                    <tr><td class="pe-3 py-1 text-muted">Chủ tài khoản:</td><td><strong>VŨ VĂN HUÂN</strong></td></tr>
                                    <tr><td class="pe-3 py-1 text-muted">Nội dung:</td><td><strong>THANHTOAN [Số đơn hàng]</strong></td></tr>
                                </table>
                            </div>
                            
                            <!-- MoMo info (shown when momo selected) -->
                            <div id="momo-info" class="mt-3 p-3 rounded-3" style="background:#fff0f5;border:1px solid #ffb6c1;display:none;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-info-circle text-danger"></i>
                                    <strong style="font-size:0.85rem;color:#ae006b;">Thông tin chuyển khoản MoMo</strong>
                                </div>
                                <table style="font-size:0.85rem;width:100%;">
                                    <tr><td class="pe-3 py-1 text-muted">Ví điện tử:</td><td><strong>MoMo</strong></td></tr>
                                    <tr><td class="pe-3 py-1 text-muted">Số điện thoại:</td><td><strong>0918841790</strong> <button type="button" class="btn btn-sm py-0 px-1" onclick="copyText('0918841790')"><i class="far fa-copy text-danger"></i></button></td></tr>
                                    <tr><td class="pe-3 py-1 text-muted">Chủ tài khoản:</td><td><strong>VU VAN HUAN</strong></td></tr>
                                    <tr><td class="pe-3 py-1 text-muted">Nội dung:</td><td><strong>THANHTOAN [Số đơn hàng]</strong></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT: Sidebar -->
                <div class="col-lg-5">
                    <div class="checkout-sidebar">

                        <!-- Order items review -->
                        <div class="checkout-card">
                            <div class="checkout-card-header">
                                <div class="icon-wrap"><i class="fas fa-shopping-bag"></i></div>
                                <div>
                                    <h5>Sản phẩm trong đơn</h5>
                                    <small class="text-muted"><?= count($cartItems ?? []) ?> sản phẩm</small>
                                </div>
                                <a href="<?= url('cart') ?>" class="btn btn-sm ms-auto" style="background:#fff0f0;color:var(--primary);border:none;font-size:0.8rem;border-radius:8px;padding:5px 12px;">
                                    <i class="fas fa-edit me-1"></i>Sửa
                                </a>
                            </div>
                            <div class="checkout-card-body" style="max-height:320px;overflow-y:auto;">
                                <?php if (!empty($cartItems)): ?>
                                    <?php foreach ($cartItems as $item): ?>
                                    <div class="order-item-review">
                                        <div class="order-item-img">
                                            <?php if (!empty($item['main_image'])): ?>
                                                <img src="<?= productImage($item['main_image']) ?>" alt="<?= e($item['product_name']) ?>" onerror="this.onerror=null;this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiB2aWV3Qm94PSIwIDAgNDAwIDQwMCI+PHJlY3Qgd2lkdGg9IjQwMCIgaGVpZ2h0PSI0MDAiIGZpbGw9IiNmNWY1ZjUiLz48Y2lyY2xlIGN4PSIyMDAiIGN5PSIxNjAiIHI9IjUwIiBmaWxsPSIjZTBlMGUwIi8+PGNpcmNsZSBjeD0iMjAwIiBjeT0iMTYwIiByPSIzMCIgZmlsbD0iIzk5OTk5OSIvPjxwYXRoIGQ9Ik0xNDAgMjIwSDI2MlYyODBoLTEyMlYyMjB6IiBmaWxsPSIjOTk5OTk5Ii8+PC9zdmc+'">
                                            <?php else: ?>
                                                <i class="fas fa-box"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1" style="flex:1;">
                                            <div class="order-item-name"><?= e($item['product_name']) ?></div>
                                            <?php if (!empty($item['variant'])): ?>
                                                <div style="font-size:0.75rem;color:#888;margin-top:2px;"><?= e($item['variant']) ?></div>
                                            <?php endif; ?>
                                            <div class="order-item-qty">SL: <?= (int)$item['quantity'] ?></div>
                                        </div>
                                        <div class="order-item-price"><?= formatPrice($item['subtotal'] ?? 0) ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                        Giỏ hàng trống
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Coupon -->
                        <div class="checkout-card">
                            <div class="checkout-card-header">
                                <div class="icon-wrap"><i class="fas fa-tag"></i></div>
                                <h5>Mã giảm giá</h5>
                            </div>
                            <div class="checkout-card-body">
                                <div class="coupon-wrap">
                                    <input type="text" class="form-control" name="coupon_code"
                                           id="coupon-input" placeholder="Nhập mã giảm giá..."
                                           value="<?= e($_SESSION['coupon']['code'] ?? '') ?>"
                                           <?= isset($_SESSION['coupon']) ? 'readonly style="background:#f5f5f5;"' : '' ?>
                                           style="text-transform:uppercase">
                                    <?php if (isset($_SESSION['coupon'])): ?>
                                        <button type="button" class="btn btn-outline-secondary" onclick="removeCoupon()" style="border-radius:10px; padding: 8px 18px; font-weight:600; font-size:0.85rem;">
                                            Hủy
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn-coupon" onclick="applyCoupon()">
                                            Áp dụng
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div id="coupon-message" class="small" style="display:none;"></div>
                            </div>
                        </div>

                        <!-- Price summary -->
                        <div class="checkout-card">
                            <div class="checkout-card-header">
                                <div class="icon-wrap"><i class="fas fa-receipt"></i></div>
                                <h5>Tóm tắt đơn hàng</h5>
                            </div>
                            <div class="checkout-card-body">
                                <div class="price-summary">
                                    <div class="price-row">
                                        <span class="label"><i class="fas fa-box fa-xs text-muted"></i> Tạm tính</span>
                                        <span class="value"><?= formatPrice($subtotal ?? 0) ?></span>
                                    </div>
                                    <div class="price-row <?= empty($discount) ? 'd-none' : '' ?>" id="checkout-discount-row">
                                        <span class="label"><i class="fas fa-tag fa-xs text-muted"></i> Giảm giá (<span id="checkout-coupon-code"><?= e($_SESSION['coupon']['code'] ?? '') ?></span>)</span>
                                        <span class="value discount" id="checkout-discount-val">-<?= formatPrice($discount ?? 0) ?></span>
                                    </div>
                                    <div class="price-row">
                                        <span class="label"><i class="fas fa-truck fa-xs text-muted"></i> Phí vận chuyển</span>
                                        <span class="value" id="shipping-fee-display">
                                            <?php if (($shippingFee ?? 0) == 0): ?>
                                                <span class="text-success fw-bold">Miễn phí</span>
                                            <?php else: ?>
                                                <?= formatPrice($shippingFee ?? 0) ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="price-total-row">
                                    <span class="total-label">Tổng thanh toán</span>
                                    <span class="total-value" id="checkout-total-val"><?= formatPrice($finalAmount ?? 0) ?></span>
                                </div>

                                <p class="text-muted text-center mt-2 mb-0" style="font-size:0.77rem;">
                                    <i class="fas fa-info-circle me-1"></i>Giá đã bao gồm VAT
                                </p>

                                <button type="submit" class="btn-place-order" id="submit-order">
                                    <i class="fas fa-lock"></i>
                                    <span>Đặt hàng ngay</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>

                                <div class="security-badges">
                                    <div class="security-badge">
                                        <i class="fas fa-shield-alt"></i>
                                        <span>Bảo mật SSL</span>
                                    </div>
                                    <div class="security-badge">
                                        <i class="fas fa-undo"></i>
                                        <span>Đổi trả 7 ngày</span>
                                    </div>
                                    <div class="security-badge">
                                        <i class="fas fa-headset"></i>
                                        <span>Hỗ trợ 24/7</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
function selectPayment(label) {
    document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('selected'));
    label.classList.add('selected');
    label.querySelector('input').checked = true;

    const val = label.querySelector('input').value;
    const bankInfo = document.getElementById('bank-info');
    const momoInfo = document.getElementById('momo-info');

    if (val === 'bank_transfer') {
        bankInfo.style.display = 'block';
        bankInfo.style.animation = 'fadeIn 0.3s ease';
        momoInfo.style.display = 'none';
    } else if (val === 'momo') {
        momoInfo.style.display = 'block';
        momoInfo.style.animation = 'fadeIn 0.3s ease';
        bankInfo.style.display = 'none';
    } else {
        bankInfo.style.display = 'none';
        momoInfo.style.display = 'none';
    }
}

// Apply coupon
function applyCoupon() {
    const code = document.getElementById('coupon-input').value.trim();
    const msgEl = document.getElementById('coupon-message');
    if (!code) {
        msgEl.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Vui lòng nhập mã giảm giá.</span>';
        msgEl.style.display = 'block';
        return;
    }
    
    const amount = <?= (float)($subtotal ?? 0) ?>;
    const shipping = <?= (float)($shippingFee ?? 0) ?>;
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
    
    // AJAX call
    fetch('<?= url('checkout/apply-coupon') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            coupon_code: code,
            order_amount: amount,
            csrf_token: csrfToken
        })
    })
    .then(r => r.json())
    .then(data => {
        msgEl.style.display = 'block';
        if (data.success) {
            msgEl.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>' + data.message + '</span>';
            
            // Update summary dynamically
            const discountVal = parseFloat(data.discountRaw || 0);
            const totalVal = amount + shipping - discountVal;
            
            const discountRow = document.getElementById('checkout-discount-row');
            if (discountRow) {
                discountRow.classList.remove('d-none');
            }
            const couponCodeEl = document.getElementById('checkout-coupon-code');
            if (couponCodeEl) {
                couponCodeEl.textContent = data.couponCode;
            }
            const discountValEl = document.getElementById('checkout-discount-val');
            if (discountValEl) {
                discountValEl.textContent = '-' + data.discount;
            }
            const totalValEl = document.getElementById('checkout-total-val');
            if (totalValEl) {
                totalValEl.textContent = formatPrice(totalVal);
            }
            setTimeout(() => location.reload(), 1000);
        } else {
            msgEl.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i>' + (data.message || 'Mã không hợp lệ.') + '</span>';
        }
    })
    .catch(() => {
        msgEl.innerHTML = '<span class="text-danger">Lỗi kết nối, thử lại sau.</span>';
        msgEl.style.display = 'block';
    });
}

function removeCoupon() {
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
    fetch('<?= url('checkout/remove-coupon') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            csrf_token: csrfToken
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Copy text helper
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        // simple feedback
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="fas fa-check text-success"></i>';
        setTimeout(() => { btn.innerHTML = '<i class="far fa-copy text-primary"></i>'; }, 1500);
    });
}

// Submit form with loading state & bfcache / Back button restore
const checkoutForm = document.getElementById('checkout-form');
const submitOrderBtn = document.getElementById('submit-order');

if (submitOrderBtn) {
    submitOrderBtn.dataset.originalHtml = submitOrderBtn.innerHTML;
}

function resetSubmitOrderBtn() {
    if (submitOrderBtn) {
        submitOrderBtn.disabled = false;
        if (submitOrderBtn.dataset.originalHtml) {
            submitOrderBtn.innerHTML = submitOrderBtn.dataset.originalHtml;
        }
    }
}

if (checkoutForm && submitOrderBtn) {
    checkoutForm.addEventListener('submit', function(e) {
        if (checkoutForm.checkValidity && !checkoutForm.checkValidity()) {
            return;
        }
        submitOrderBtn.dataset.originalHtml = submitOrderBtn.innerHTML;
        submitOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang xử lý...';
        submitOrderBtn.disabled = true;
    });
}

// Reset button when navigated back via bfcache (Back/Forward Cache)
window.addEventListener('pageshow', function(event) {
    resetSubmitOrderBtn();
});

// Also reset on visibility change or popstate
window.addEventListener('popstate', resetSubmitOrderBtn);
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        resetSubmitOrderBtn();
    }
});

// Load Vietnam Administrative Divisions (Provinces -> Districts -> Wards)
(function initAddressSelector() {
    const citySelect = document.getElementById('ship_city');
    const districtSelect = document.getElementById('ship_district');
    const wardSelect = document.getElementById('ship_ward');
    if (!citySelect || !districtSelect || !wardSelect) return;

    let provincesData = [];
    const savedCity = "<?= addslashes($user['city'] ?? '') ?>";
    const savedDistrict = "<?= addslashes($user['district'] ?? '') ?>";
    const savedWard = "<?= addslashes($user['ward'] ?? '') ?>";

    let controller = null;
    let timeoutId = null;
    if (typeof AbortController !== 'undefined') {
        controller = new AbortController();
        timeoutId = setTimeout(() => controller.abort(), 4000);
    }

    fetch('https://provinces.open-api.vn/api/?depth=3', controller ? { signal: controller.signal } : {})
        .then(res => res.json())
        .then(data => {
            if (timeoutId) clearTimeout(timeoutId);
            provincesData = data;
            citySelect.innerHTML = '<option value="">-- Chọn Tỉnh / Thành phố --</option>';
            data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.name;
                opt.textContent = p.name;
                opt.dataset.code = p.code;
                citySelect.appendChild(opt);
            });

            // Check if user has saved city
            if (savedCity) {
                for (let opt of citySelect.options) {
                    if (opt.value === savedCity || opt.value.toLowerCase().includes(savedCity.toLowerCase()) || savedCity.toLowerCase().includes(opt.value.toLowerCase())) {
                        citySelect.value = opt.value;
                        citySelect.dispatchEvent(new Event('change'));
                        break;
                    }
                }
            }
        })
        .catch(err => {
            if (timeoutId) clearTimeout(timeoutId);
            console.warn('Cannot load provinces API, falling back to manual inputs', err);
            citySelect.outerHTML = '<input type="text" class="form-control" name="ship_city" id="ship_city" placeholder="Nhập Tỉnh/Thành phố" value="' + (savedCity || '') + '" required>';
            districtSelect.outerHTML = '<input type="text" class="form-control" name="ship_district" id="ship_district" placeholder="Nhập Quận/Huyện" value="' + (savedDistrict || '') + '" required>';
            wardSelect.outerHTML = '<input type="text" class="form-control" name="ship_ward" id="ship_ward" placeholder="Nhập Phường/Xã" value="' + (savedWard || '') + '" required>';
        });

    citySelect.addEventListener('change', function() {
        const selectedName = this.value;
        districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
        wardSelect.disabled = true;

        if (!selectedName) {
            districtSelect.disabled = true;
            return;
        }

        const province = provincesData.find(p => p.name === selectedName);
        if (province && province.districts) {
            province.districts.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.name;
                opt.textContent = d.name;
                opt.dataset.code = d.code;
                districtSelect.appendChild(opt);
            });
            districtSelect.disabled = false;

            if (savedDistrict) {
                for (let opt of districtSelect.options) {
                    if (opt.value === savedDistrict || opt.value.toLowerCase().includes(savedDistrict.toLowerCase()) || savedDistrict.toLowerCase().includes(opt.value.toLowerCase())) {
                        districtSelect.value = opt.value;
                        districtSelect.dispatchEvent(new Event('change'));
                        break;
                    }
                }
            }
        }
    });

    districtSelect.addEventListener('change', function() {
        const selectedCityName = citySelect.value;
        const selectedDistName = this.value;
        wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

        if (!selectedDistName) {
            wardSelect.disabled = true;
            return;
        }

        const province = provincesData.find(p => p.name === selectedCityName);
        if (province) {
            const district = province.districts.find(d => d.name === selectedDistName);
            if (district && district.wards) {
                district.wards.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.name;
                    opt.textContent = w.name;
                    wardSelect.appendChild(opt);
                });
                wardSelect.disabled = false;

                if (savedWard) {
                    for (let opt of wardSelect.options) {
                        if (opt.value === savedWard || opt.value.toLowerCase().includes(savedWard.toLowerCase()) || savedWard.toLowerCase().includes(opt.value.toLowerCase())) {
                            wardSelect.value = opt.value;
                            break;
                        }
                    }
                }
            }
        }
    });
})();

// Animate on load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.checkout-card').forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, i * 80);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
