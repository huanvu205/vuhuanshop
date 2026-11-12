<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #<?php echo htmlspecialchars($order['order_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #eeeeee;
        }
        .header {
            background-color: #e53935;
            color: #ffffff;
            padding: 25px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 20px 15px;
        }
        .greeting {
            font-size: 15px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .order-info {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #e53935;
        }
        .info-row {
            margin-bottom: 8px;
            font-size: 13px;
            line-height: 1.4;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-weight: bold;
            color: #555555;
            display: inline-block;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th {
            background-color: #f1f1f1;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            color: #555555;
            border-bottom: 2px solid #dddddd;
        }
        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #eeeeee;
            font-size: 13px;
            vertical-align: middle;
        }
        .details-table td.qty {
            text-align: center;
        }
        .details-table td.price {
            text-align: right;
            font-weight: bold;
        }
        .product-meta-price {
            font-size: 11px;
            color: #777777;
            margin-top: 2px;
        }
        .summary-container {
            border-top: 2px solid #eeeeee;
            padding-top: 12px;
            margin-top: 15px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 5px 0;
            font-size: 13px;
        }
        .summary-table td.label {
            text-align: right;
            padding-right: 15px;
            color: #666666;
        }
        .summary-table td.value {
            text-align: right;
            font-weight: bold;
            width: 40%;
        }
        .summary-table tr.total td {
            font-size: 16px;
            font-weight: bold;
            color: #e53935;
            border-top: 1px solid #eeeeee;
            padding-top: 10px;
        }
        .footer {
            background-color: #f1f1f1;
            padding: 20px 15px;
            text-align: center;
            font-size: 11px;
            color: #777777;
            border-top: 1px solid #eeeeee;
            line-height: 1.5;
        }
        .footer p {
            margin: 4px 0;
        }

        /* Mobile Responsive Styles */
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0 auto !important;
                border-radius: 0 !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .header {
                padding: 20px 15px !important;
            }
            .header h1 {
                font-size: 20px !important;
            }
            .content {
                padding: 15px 10px !important;
            }
            .greeting {
                font-size: 14px !important;
            }
            .details-table th, .details-table td {
                padding: 8px 6px !important;
                font-size: 12px !important;
            }
            .details-table td.qty {
                width: 12% !important;
            }
            .details-table td.price {
                width: 33% !important;
            }
            .summary-table td {
                font-size: 12px !important;
            }
            .summary-table tr.total td {
                font-size: 15px !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>VŨ HUÂN SHOP</h1>
            <p>Xác nhận đơn hàng #<?php echo htmlspecialchars($order['order_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Chào <strong><?php echo htmlspecialchars($order['ship_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>,<br>
                Cảm ơn bạn đã mua hàng tại Vũ Huân Shop. Dưới đây là thông tin chi tiết đơn hàng của bạn.
            </div>
            
            <div class="order-info">
                <div class="info-row">
                    <span class="info-label">Mã đơn hàng:</span> 
                    <strong>#<?php echo htmlspecialchars($order['order_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <div class="info-row">
                    <span class="info-label">Ngày đặt hàng:</span> 
                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Người nhận:</span> 
                    <?php echo htmlspecialchars($order['ship_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Số điện thoại:</span> 
                    <?php echo htmlspecialchars($order['ship_phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Địa chỉ giao:</span> 
                    <?php 
                        $address_parts = array_filter([
                            $order['ship_address'] ?? '',
                            $order['ship_ward'] ?? '',
                            $order['ship_district'] ?? '',
                            $order['ship_city'] ?? ''
                        ]);
                        echo htmlspecialchars(implode(', ', $address_parts), ENT_QUOTES, 'UTF-8'); 
                    ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Phương thức TT:</span> 
                    <span style="text-transform: uppercase;"><?php echo htmlspecialchars($order['payment_method'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái TT:</span> 
                    <?php 
                        if (($order['payment_status'] ?? '') === 'paid') {
                            echo '<span style="color: #4CAF50; font-weight: bold;">Đã thanh toán</span>';
                        } else {
                            echo '<span style="color: #f57c00; font-weight: bold;">Chưa thanh toán</span>';
                        }
                    ?>
                </div>
                <?php if (!empty($order['customer_note'])): ?>
                <div class="info-row" style="margin-top: 6px;">
                    <span class="info-label">Ghi chú:</span> 
                    <em><?php echo htmlspecialchars($order['customer_note'], ENT_QUOTES, 'UTF-8'); ?></em>
                </div>
                <?php endif; ?>
            </div>
            
            <table class="details-table">
                <thead>
                    <tr>
                        <th style="width: 55%;">Sản phẩm</th>
                        <th style="width: 15%; text-align: center;">SL</th>
                        <th style="width: 30%; text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderDetails as $item): ?>
                    <tr>
                        <td>
                            <div style="font-weight: bold; color: #111111;"><?php echo htmlspecialchars($item['product_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php if (!empty($item['variant'])): ?>
                            <div style="font-size: 11px; color: #e53935; font-weight: 600; margin-top: 2px;">Phân loại: <?php echo htmlspecialchars($item['variant'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>
                            <div class="product-meta-price">Đơn giá: <?php echo number_format($item['price'] ?? 0, 0, ',', '.') . '₫'; ?></div>
                        </td>
                        <td class="qty"><?php echo htmlspecialchars($item['quantity'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="price"><?php echo number_format($item['subtotal'] ?? 0, 0, ',', '.') . '₫'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="summary-container">
                <table class="summary-table">
                    <tr>
                        <td class="label">Cộng tiền hàng:</td>
                        <td class="value"><?php echo number_format($order['total_amount'] ?? 0, 0, ',', '.') . '₫'; ?></td>
                    </tr>
                    <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                    <tr>
                        <td class="label" style="color: #4CAF50;">Chiết khấu:</td>
                        <td class="value" style="color: #4CAF50;">-<?php echo number_format($order['discount_amount'], 0, ',', '.') . '₫'; ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="label">Phí vận chuyển:</td>
                        <td class="value">
                            <?php 
                                if (($order['shipping_fee'] ?? 0) > 0) {
                                    echo number_format($order['shipping_fee'], 0, ',', '.') . '₫';
                                } else {
                                    echo 'Miễn phí';
                                }
                            ?>
                        </td>
                    </tr>
                    <tr class="total">
                        <td class="label">Tổng thanh toán:</td>
                        <td class="value"><?php echo number_format($order['final_amount'] ?? 0, 0, ',', '.') . '₫'; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Vũ Huân Shop</strong> - Hệ thống bán lẻ điện thoại & công nghệ uy tín</p>
            <p>Địa chỉ: Trực Đại, Trực Ninh, Nam Định</p>
            <p>Hotline: 0336.120.405 | Email: vuhuan123890@gmail.com</p>
            <p>Cảm ơn quý khách đã tin tưởng lựa chọn sản phẩm của chúng tôi!</p>
        </div>
    </div>
</body>
</html>
