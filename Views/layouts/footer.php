<!-- Back to Top -->
<button class="back-to-top" title="Lên đầu trang"><i class="fas fa-chevron-up"></i></button>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <!-- About -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-logo"><i class="fas fa-mobile-alt me-1"></i>Vũ Huân <span>Shop</span></div>
                <p class="footer-desc">
                    Chuỗi bán lẻ điện thoại, laptop, tablet, đồng hồ thông minh và phụ kiện công nghệ hàng đầu Việt Nam. Cam kết hàng chính hãng, giá tốt nhất, dịch vụ tận tâm.
                </p>
                <?php
                $facebookUrl = trim((string)getSetting('shop_facebook', ''));
                $facebookParts = parse_url($facebookUrl);
                $facebookHost = strtolower($facebookParts['host'] ?? '');
                $facebookPath = trim($facebookParts['path'] ?? '', '/');
                ?>
                <?php if (($facebookParts['scheme'] ?? '') === 'https' && in_array($facebookHost, ['facebook.com', 'www.facebook.com'], true) && $facebookPath !== ''): ?>
                    <div class="social-links">
                        <a href="<?= e($facebookUrl) ?>" target="_blank" rel="noopener noreferrer" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6>Sản phẩm</h6>
                <ul class="footer-links">
                    <li><a href="<?= url('products') ?>?category=1"><i class="fas fa-chevron-right"></i>Điện thoại</a></li>
                    <li><a href="<?= url('products') ?>?category=2"><i class="fas fa-chevron-right"></i>Laptop</a></li>
                    <li><a href="<?= url('products') ?>?category=3"><i class="fas fa-chevron-right"></i>Máy tính bảng</a></li>
                    <li><a href="<?= url('products') ?>?category=4"><i class="fas fa-chevron-right"></i>Đồng hồ thông minh</a></li>
                    <li><a href="<?= url('products') ?>?category=5"><i class="fas fa-chevron-right"></i>Phụ kiện</a></li>
                </ul>
            </div>
            
            <!-- Support -->
            <div class="col-lg-2 col-md-6">
                <h6>Chính sách & Hỗ trợ</h6>
                <ul class="footer-links">
                    <li><a href="<?= url('policies/warranty') ?>"><i class="fas fa-chevron-right"></i>Chính sách bảo hành</a></li>
                    <li><a href="<?= url('policies/returns') ?>"><i class="fas fa-chevron-right"></i>Chính sách đổi trả</a></li>
                    <li><a href="<?= url('policies/shipping') ?>"><i class="fas fa-chevron-right"></i>Giao hàng & Vận chuyển</a></li>
                    <li><a href="<?= url('policies/payment') ?>"><i class="fas fa-chevron-right"></i>Phương thức thanh toán</a></li>
                    <li><a href="<?= url('policies/complaints') ?>"><i class="fas fa-chevron-right"></i>Giải quyết khiếu nại</a></li>
                    <li><a href="<?= url('policies/terms') ?>"><i class="fas fa-chevron-right"></i>Điều khoản sử dụng</a></li>
                    <li><a href="<?= url('policies/privacy') ?>"><i class="fas fa-chevron-right"></i>Chính sách bảo mật</a></li>
                    <li><a href="<?= url('warranty') ?>"><i class="fas fa-chevron-right"></i>Tra cứu bảo hành</a></li>
                    <li><a href="<?= url('orders/track') ?>"><i class="fas fa-chevron-right"></i>Theo dõi đơn hàng</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div class="col-lg-4 col-md-6">
                <h6>Liên hệ</h6>
                <div class="footer-contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?= e(getSetting('shop_address', 'Trực Đại, Trực Ninh, Nam Định')) ?></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-phone"></i>
                    <span>Hotline: <a href="tel:<?= str_replace(['.', ' ', '-'], '', getSetting('shop_hotline', '0336.120.405')) ?>" style="color:rgba(255,255,255,0.8)"><?= e(getSetting('shop_hotline', '0336.120.405')) ?></a></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-envelope"></i>
                    <span><a href="mailto:<?= e(getSetting('shop_email', 'vuhuan123890@gmail.com')) ?>" style="color:rgba(255,255,255,0.8)"><?= e(getSetting('shop_email', 'vuhuan123890@gmail.com')) ?></a></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-clock"></i>
                    <span>Thứ 2 - Chủ nhật: 8:00 - 21:00</span>
                </div>
                
                <!-- Newsletter -->
                <div class="mt-3">
                    <p style="font-size:13px;color:rgba(255,255,255,0.6);margin-bottom:8px">Đăng ký nhận ưu đãi:</p>
                    <div class="d-flex gap-2">
                        <input type="email" id="newsletter-email" class="form-control" placeholder="Email của bạn" style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.2);color:white;font-size:13px">
                        <button id="newsletter-btn" class="btn btn-danger btn-sm" style="white-space:nowrap">Đăng ký</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trust Badges -->
        <div class="row mt-4 pt-3" style="border-top:1px solid rgba(255,255,255,0.08)">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-4 justify-content-center justify-content-md-start">
                    <div class="d-flex align-items-center gap-2" style="font-size:13px;color:rgba(255,255,255,0.7)">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="fas fa-shield-alt" style="color:#FFD600"></i></div>
                        <span>Hàng chính hãng<br><small style="color:rgba(255,255,255,0.5)">100% bảo đảm</small></span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="font-size:13px;color:rgba(255,255,255,0.7)">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="fas fa-truck" style="color:#FFD600"></i></div>
                        <span>Giao hàng nhanh<br><small style="color:rgba(255,255,255,0.5)">Trong ngày</small></span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="font-size:13px;color:rgba(255,255,255,0.7)">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="fas fa-undo" style="color:#FFD600"></i></div>
                        <span>Đổi trả dễ dàng<br><small style="color:rgba(255,255,255,0.5)">Theo điều kiện sản phẩm</small></span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="font-size:13px;color:rgba(255,255,255,0.7)">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="fas fa-headset" style="color:#FFD600"></i></div>
                        <span>Hỗ trợ 24/7<br><small style="color:rgba(255,255,255,0.5)">Sẵn sàng</small></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Legal & Ministry of Industry and Trade Info -->
        <div class="row mt-4 pt-3" style="border-top:1px solid rgba(255,255,255,0.08); font-size:12px; color:rgba(255,255,255,0.55); line-height: 1.7;">
            <div class="col-md-9">
                <p class="mb-1"><strong>HỘ KINH DOANH VŨ HUÂN STORE</strong></p>
                <p class="mb-1">Địa chỉ: Trực Đại, Trực Ninh, Tỉnh Nam Định, Việt Nam · Hotline: 0336.120.405 · Email: vuhuan123890@gmail.com</p>
                <p class="mb-0">Giấy chứng nhận đăng ký kinh doanh số: 07A8009282 do UBND Huyện Trực Ninh cấp ngày 15/01/2024 · Mã số thuế: 8692817291 · Người đại diện: <strong>Vũ Văn Huân</strong></p>
            </div>
            <div class="col-md-3 text-md-end text-start mt-2 mt-md-0">
                <a href="http://online.gov.vn" target="_blank" rel="noopener noreferrer" title="Website đã thông báo với Bộ Công Thương">
                    <img src="https://webmedia.com.vn/images/2021/09/logo-da-thong-bao-bo-cong-thuong-mau-xanh.png" 
                         alt="Đã thông báo Bộ Công Thương" 
                         style="height: 44px; filter: brightness(1.05);"
                         onerror="this.style.display='none'">
                </a>
            </div>
        </div>

        <!-- Bottom -->
        <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.08);">
            <span>© <?= date('Y') ?> Vũ Huân Shop. Tất cả quyền được bảo lưu.</span>
            <div class="d-flex gap-3">
                <a href="<?= url('policies/terms') ?>" style="color:rgba(255,255,255,0.6)">Điều khoản sử dụng</a>
                <a href="<?= url('policies/privacy') ?>" style="color:rgba(255,255,255,0.6)">Chính sách bảo mật</a>
                <a href="<?= url('policies/returns') ?>" style="color:rgba(255,255,255,0.6)">Chính sách đổi trả</a>
                <a href="<?= url('policies/warranty') ?>" style="color:rgba(255,255,255,0.6)">Chính sách bảo hành</a>
            </div>
            <div class="d-flex gap-2">
                <img src="https://img.icons8.com/color/48/mastercard.png" height="24" alt="Mastercard">
                <img src="https://img.icons8.com/color/48/visa.png" height="24" alt="Visa">
                <img src="https://img.icons8.com/color/48/vietnam.png" height="24" alt="VietQR Napas247" title="Thanh toán VietQR Napas247">
            </div>
        </div>
    </div>
</footer>

<!-- Mobile Bottom App Bar (5 buttons) -->
<div class="mobile-bottom-nav d-lg-none">
    <a href="<?= url() ?>" class="bottom-nav-item">
        <i class="fas fa-home"></i>
        <span>Trang chủ</span>
    </a>
    <a href="#offcanvasCategories" data-bs-toggle="offcanvas" class="bottom-nav-item">
        <i class="fas fa-th-large"></i>
        <span>Danh mục</span>
    </a>
    <a href="<?= url('cart') ?>" class="bottom-nav-item position-relative">
        <i class="fas fa-shopping-cart"></i>
        <span class="bottom-nav-cart-badge cart-count" style="<?= getCartCount() > 0 ? '' : 'display:none' ?>"><?= getCartCount() ?></span>
        <span>Giỏ hàng</span>
    </a>
    <a href="<?= url('warranty') ?>" class="bottom-nav-item">
        <i class="fas fa-shield-alt"></i>
        <span>Bảo hành</span>
    </a>
    <a href="https://zalo.me/0918841790" target="_blank" rel="noopener noreferrer" class="bottom-nav-item">
        <i class="fas fa-comment-dots"></i>
        <span>Chat Zalo</span>
    </a>
</div>

<!-- Offcanvas Danh Mục Nhanh Mobile -->
<div class="offcanvas offcanvas-bottom rounded-top-4" tabindex="-1" id="offcanvasCategories" aria-labelledby="offcanvasCategoriesLabel" style="height: 72vh;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title fw-bold text-dark d-flex align-items-center gap-2" id="offcanvasCategoriesLabel">
            <span style="width:32px; height:32px; border-radius:8px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:14px;"><i class="fas fa-th-large"></i></span>
            Danh Mục Sản Phẩm
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        <div class="row g-3">
            <div class="col-6">
                <a href="<?= url('products') ?>?category=1" class="d-flex align-items-center gap-3 p-3 rounded-3 text-dark text-decoration-none border bg-light shadow-sm" style="transition:all 0.2s;">
                    <div style="width:42px;height:42px;border-radius:10px;background:#fee2e2;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fas fa-mobile-alt"></i></div>
                    <div>
                        <div class="fw-bold small">Điện thoại</div>
                        <span class="text-muted" style="font-size:11px;">iPhone, Samsung...</span>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="<?= url('products') ?>?category=2" class="d-flex align-items-center gap-3 p-3 rounded-3 text-dark text-decoration-none border bg-light shadow-sm" style="transition:all 0.2s;">
                    <div style="width:42px;height:42px;border-radius:10px;background:#e0e7ff;color:#4f46e5;display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fas fa-laptop"></i></div>
                    <div>
                        <div class="fw-bold small">Laptop</div>
                        <span class="text-muted" style="font-size:11px;">MacBook, Gaming</span>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="<?= url('products') ?>?category=3" class="d-flex align-items-center gap-3 p-3 rounded-3 text-dark text-decoration-none border bg-light shadow-sm" style="transition:all 0.2s;">
                    <div style="width:42px;height:42px;border-radius:10px;background:#dbeafe;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fas fa-tablet-alt"></i></div>
                    <div>
                        <div class="fw-bold small">Tablet</div>
                        <span class="text-muted" style="font-size:11px;">iPad, Galaxy Tab</span>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="<?= url('products') ?>?category=4" class="d-flex align-items-center gap-3 p-3 rounded-3 text-dark text-decoration-none border bg-light shadow-sm" style="transition:all 0.2s;">
                    <div style="width:42px;height:42px;border-radius:10px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="fw-bold small">Đồng hồ</div>
                        <span class="text-muted" style="font-size:11px;">Apple Watch...</span>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="<?= url('products') ?>?category=5" class="d-flex align-items-center gap-3 p-3 rounded-3 text-dark text-decoration-none border bg-light shadow-sm" style="transition:all 0.2s;">
                    <div style="width:42px;height:42px;border-radius:10px;background:#f3e8ff;color:#9333ea;display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fas fa-headphones"></i></div>
                    <div>
                        <div class="fw-bold small">Phụ kiện</div>
                        <span class="text-muted" style="font-size:11px;">Sạc, Cáp, Tai nghe</span>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="<?= url('warranty') ?>" class="d-flex align-items-center gap-3 p-3 rounded-3 text-dark text-decoration-none border bg-light shadow-sm" style="transition:all 0.2s;">
                    <div style="width:42px;height:42px;border-radius:10px;background:#dcfce7;color:#16a34a;display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fas fa-shield-alt"></i></div>
                    <div>
                        <div class="fw-bold small">Tra cứu BH</div>
                        <span class="text-muted" style="font-size:11px;">Bảo hành điện tử</span>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="<?= url('news') ?>" class="d-flex align-items-center gap-3 p-3 rounded-3 text-dark text-decoration-none border bg-light shadow-sm" style="transition:all 0.2s;">
                    <div style="width:42px;height:42px;border-radius:10px;background:#ffedd5;color:#ea580c;display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fas fa-newspaper"></i></div>
                    <div>
                        <div class="fw-bold small">Tin tức</div>
                        <span class="text-muted" style="font-size:11px;">Mẹo & Đánh giá</span>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="<?= url('orders/track') ?>" class="d-flex align-items-center gap-3 p-3 rounded-3 text-dark text-decoration-none border bg-light shadow-sm" style="transition:all 0.2s;">
                    <div style="width:42px;height:42px;border-radius:10px;background:#cffafe;color:#0891b2;display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fas fa-truck"></i></div>
                    <div>
                        <div class="fw-bold small">Tra đơn</div>
                        <span class="text-muted" style="font-size:11px;">Vận chuyển nhanh</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Floating Contact Widgets (Hotline & Zalo) -->
<div class="floating-contact-buttons" style="position:fixed; bottom:24px; left:24px; z-index:9998; display:flex; flex-direction:column; gap:12px;">
    <!-- Zalo Button -->
    <a href="https://zalo.me/0918841790" target="_blank" rel="noopener noreferrer" class="btn-floating-zalo" title="Chat qua Zalo: 0918.841.790" style="width:48px; height:48px; border-radius:50%; background:#0068FF; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 16px rgba(0,104,255,0.4); text-decoration:none; transition:all 0.3s; position:relative;">
        <span style="font-weight:900; font-size:12px; color:#fff; letter-spacing:-0.5px;">Zalo</span>
    </a>

    <!-- Hotline Button with pulse ring -->
    <a href="tel:0336120405" class="btn-floating-hotline" title="Gọi Hotline: 0336.120.405" style="width:48px; height:48px; border-radius:50%; background:#E53935; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 16px rgba(229,57,53,0.45); text-decoration:none; color:#fff; font-size:20px; transition:all 0.3s;">
        <i class="fas fa-phone-alt"></i>
    </a>
</div>

<style>
/* Bottom App Bar Mobile Styles */
.mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 58px;
    background: #ffffff;
    box-shadow: 0 -2px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: space-around;
    z-index: 1040;
    border-top: 1px solid #f1f5f9;
}
.bottom-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #64748b;
    text-decoration: none;
    font-size: 11px;
    font-weight: 600;
    gap: 3px;
    flex: 1;
    height: 100%;
    transition: all 0.2s;
    user-select: none;
}
.bottom-nav-item i {
    font-size: 18px;
}
.bottom-nav-item:hover, .bottom-nav-item:active {
    color: #E53935;
}
.bottom-nav-cart-badge {
    position: absolute;
    top: 6px;
    right: calc(50% - 18px);
    background: #E53935;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    border-radius: 50%;
    min-width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    border: 1.5px solid #fff;
}
@media (max-width: 991px) {
    body {
        padding-bottom: 64px !important;
    }
    .floating-contact-buttons {
        bottom: 74px !important;
    }
}
.btn-floating-hotline:hover, .btn-floating-zalo:hover {
    transform: scale(1.1);
}
@keyframes pulseRing {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 14px rgba(229, 57, 53, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(229, 57, 53, 0); }
}
.btn-floating-hotline {
    animation: pulseRing 2s infinite;
}
</style>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Main JS -->
<script src="<?= APP_URL ?>/public/js/main.js?v=<?= time() ?>"></script>
</body>
</html>
