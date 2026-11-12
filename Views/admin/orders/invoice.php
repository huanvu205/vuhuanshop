<?php
/**
 * @var array $order
 * @var array $orderDetails
 */
$order = $order ?? [];
$orderDetails = $orderDetails ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #<?= e($order['order_number'] ?? '') ?> - Vũ Huân Shop</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts for digital signature & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            background: #f4f6f9;
            padding: 30px 15px;
            margin: 0;
        }
        .invoice-box {
            max-width: 820px;
            margin: auto;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 36px;
            border-radius: 12px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: 800;
            color: #e53935;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }
        .table-invoice th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 700;
            font-size: 13px;
            color: #475569;
            padding: 10px 12px;
            white-space: nowrap;
        }
        .table-invoice td {
            vertical-align: middle;
            font-size: 13px;
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .total-row td {
            font-weight: 600;
            padding: 6px 8px;
            font-size: 13.5px;
        }
        .total-row.grand-total td {
            font-size: 16px;
            color: #e53935;
            font-weight: 800;
            border-top: 2px solid #fee2e2 !important;
            padding-top: 10px;
        }
        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding: 8px 6px;
                background: #f5f5f5;
            }
            .invoice-box {
                padding: 16px 12px;
                border-radius: 8px;
                box-shadow: none;
                border: 1px solid #e0e0e0;
            }
            .invoice-title {
                font-size: 20px;
            }
            .table-invoice th {
                font-size: 12px;
                padding: 8px 6px;
            }
            .table-invoice td {
                font-size: 12px;
                padding: 8px 6px;
            }
            .table-invoice th.col-stt,
            .table-invoice td.col-stt {
                width: 36px;
                padding-left: 4px;
                padding-right: 4px;
                text-align: center;
            }
            .table-invoice th.col-name,
            .table-invoice td.col-name {
                min-width: 130px;
            }
            .table-invoice th.col-qty,
            .table-invoice td.col-qty {
                width: 44px;
                text-align: center;
            }
            .table-invoice th.col-price,
            .table-invoice td.col-price {
                min-width: 75px;
                white-space: nowrap;
            }
            .table-invoice th.col-total,
            .table-invoice td.col-total {
                min-width: 85px;
                white-space: nowrap;
            }
            .sig-block {
                margin-top: 25px !important;
                padding-top: 15px !important;
            }
        }

        @media print {
            body { padding: 0; background: #fff; }
            .invoice-box { border: none; box-shadow: none; padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <!-- Top action bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print pb-3 border-bottom">
        <a href="javascript:window.history.back()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
        <button onclick="window.print()" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" style="background:#e53935; border:none;">
            <i class="fas fa-print me-1"></i> In / Xuất PDF
        </button>
    </div>

    <!-- Company header -->
    <div class="row mb-4">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-mobile-alt text-danger fs-3"></i>
                <h1 class="invoice-title mb-0">Vũ Huân Shop</h1>
            </div>
            <p class="text-muted mb-1 small">Hệ thống bán lẻ điện thoại & công nghệ uy tín</p>
            <p class="text-muted small mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> Địa chỉ: Trực Đại, Trực Ninh, Nam Định</p>
            <p class="text-muted small mb-0"><i class="fas fa-phone-alt text-danger me-1"></i> Hotline: 0336.120.405 | <i class="fas fa-envelope text-danger me-1"></i> vuhuan123890@gmail.com</p>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <div class="p-2 p-md-0 rounded bg-light bg-md-transparent">
                <h3 class="fw-bold text-dark mb-2" style="letter-spacing: 0.5px;">HÓA ĐƠN BÁN HÀNG</h3>
                <p class="mb-1">Mã đơn: <strong class="text-danger">#<?= e($order['order_number'] ?? '') ?></strong></p>
                <p class="text-muted small mb-1">Ngày đặt: <?= !empty($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : date('d/m/Y H:i') ?></p>
                <p class="text-muted small mb-1">Ngày in: <span id="current-export-time"><?= date('d/m/Y H:i') ?></span></p>
                <p class="text-muted small mb-0">
                    Trạng thái: 
                    <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle badge-status">Đã thanh toán</span>
                    <?php else: ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle badge-status">Chưa thanh toán</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Customer & Order Details -->
    <div class="row mb-4 g-3">
        <div class="col-12 col-md-6">
            <div class="p-3 border rounded-3 h-100 bg-white shadow-none">
                <h6 class="fw-bold text-uppercase text-secondary border-bottom pb-2 mb-2" style="font-size:12px; letter-spacing:0.5px;">
                    <i class="fas fa-user text-primary me-1"></i> Thông tin khách nhận
                </h6>
                <p class="mb-1">Họ tên: <strong><?= e($order['ship_name'] ?? 'Khách vãng lai') ?></strong></p>
                <p class="mb-1 small">Điện thoại: <strong><?= e($order['ship_phone'] ?? 'N/A') ?></strong></p>
                <?php if (!empty($order['ship_email'])): ?>
                    <p class="mb-1 small">Email: <?= e($order['ship_email']) ?></p>
                <?php endif; ?>
                <p class="mb-0 small text-muted">Địa chỉ: <?= e(formatOrderAddress($order)) ?></p>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="p-3 border rounded-3 h-100 bg-white shadow-none">
                <h6 class="fw-bold text-uppercase text-secondary border-bottom pb-2 mb-2" style="font-size:12px; letter-spacing:0.5px;">
                    <i class="fas fa-receipt text-primary me-1"></i> Thông tin đơn hàng
                </h6>
                <p class="mb-1 small">Phương thức TT: <strong class="text-uppercase"><?= e($order['payment_method'] ?? 'COD') ?></strong></p>
                <p class="mb-1 small">Vận chuyển: <strong>Giao hàng tận nơi</strong></p>
                <p class="mb-0 small text-muted">Ghi chú: <?= !empty($order['customer_note']) ? e($order['customer_note']) : 'Không có' ?></p>
            </div>
        </div>
    </div>

    <!-- Product Table inside table-responsive -->
    <div class="table-responsive mb-4">
        <table class="table table-invoice mb-0">
            <thead>
                <tr>
                    <th class="col-stt">STT</th>
                    <th class="col-name">Sản phẩm</th>
                    <th class="col-qty text-center">SL</th>
                    <th class="col-price text-end">Đơn giá</th>
                    <th class="col-total text-end">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orderDetails)): ?>
                    <?php foreach ($orderDetails as $i => $item): ?>
                        <tr>
                            <td class="col-stt text-center text-muted"><?= $i + 1 ?></td>
                            <td class="col-name">
                                <div class="fw-bold text-dark"><?= e($item['product_name']) ?></div>
                                <?php if (!empty($item['variant']) && strpos($item['product_name'], $item['variant']) === false): ?>
                                    <div class="small mt-1 text-danger font-monospace" style="font-size:11.5px; font-weight:600;">
                                        <i class="fas fa-tag me-1"></i><?= e($item['variant']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="col-qty text-center fw-bold"><?= $item['quantity'] ?></td>
                            <td class="col-price text-end"><?= formatPrice($item['price']) ?></td>
                            <td class="col-total text-end fw-bold text-dark"><?= formatPrice($item['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">Không có thông tin sản phẩm</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Totals receipt -->
    <div class="row justify-content-end mb-4">
        <div class="col-12 col-sm-8 col-md-5">
            <div class="p-2 border rounded-3 bg-light-subtle">
                <table class="table table-sm table-borderless mb-0">
                    <tr class="total-row">
                        <td class="text-muted">Cộng tiền hàng:</td>
                        <td class="text-end fw-bold"><?= formatPrice($order['total_amount'] ?? 0) ?></td>
                    </tr>
                    <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                        <tr class="total-row text-success">
                            <td>Chiết khấu voucher:</td>
                            <td class="text-end fw-bold">-<?= formatPrice($order['discount_amount']) ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td class="text-muted">Phí vận chuyển:</td>
                        <td class="text-end"><?= ($order['shipping_fee'] ?? 0) > 0 ? formatPrice($order['shipping_fee']) : '<span class="text-success fw-bold">Miễn phí</span>' ?></td>
                    </tr>
                    <tr class="total-row grand-total border-top">
                        <td>Tổng thanh toán:</td>
                        <td class="text-end"><?= formatPrice($order['final_amount'] ?? 0) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer signature -->
    <div class="row sig-block mt-4 pt-3 text-center">
        <div class="col-6">
            <p class="mb-4 small text-muted">Khách hàng nhận hàng<br><span style="font-size:11px;">(Ký, ghi rõ họ tên)</span></p>
        </div>
        <div class="col-6">
            <p class="mb-1 small text-muted">Người lập phiếu<br><span style="font-size:11px;">(Ký, ghi rõ họ tên)</span></p>
            <div class="my-1" style="font-family: 'Dancing Script', 'Brush Script MT', cursive, sans-serif; font-size: 1.8rem; color: #1a237e; font-weight: 700; transform: rotate(-5deg); display: inline-block;">
                Huan
            </div>
            <div class="text-muted small mb-1" style="font-size: 0.72rem; font-style: italic;">(Đã ký điện tử)</div>
            <strong class="d-block text-dark" style="font-size:13px;">Vũ Văn Huân</strong>
        </div>
    </div>
</div>

<script>
    // Set client side date and time for invoice print
    window.addEventListener('DOMContentLoaded', () => {
        const now = new Date();
        const formattedDate = now.toLocaleDateString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        const formattedTime = now.toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });
        const el = document.getElementById('current-export-time');
        if (el) {
            el.innerText = `${formattedDate} ${formattedTime}`;
        }
        
        // Only auto-print on desktop if requested, avoid annoying popup locking mobile screens
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent) || window.innerWidth < 768;
        const urlParams = new URLSearchParams(window.location.search);
        if (!isMobile && urlParams.get('autoprint') !== '0') {
            setTimeout(() => {
                window.print();
            }, 800);
        }
    });
</script>
</body>
</html>
