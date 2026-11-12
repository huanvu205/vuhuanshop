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
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link" title="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-link" title="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link" title="Zalo"><i class="fas fa-comment"></i></a>
                </div>
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
                <h6>Hỗ trợ</h6>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-chevron-right"></i>Chính sách bảo hành</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i>Chính sách đổi trả</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i>Phương thức thanh toán</a></li>
                    <li><a href="<?= url('orders/track') ?>"><i class="fas fa-chevron-right"></i>Theo dõi đơn hàng</a></li>
                    <li><a href="<?= url('news') ?>"><i class="fas fa-chevron-right"></i>Tin tức công nghệ</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div class="col-lg-4 col-md-6">
                <h6>Liên hệ</h6>
                <div class="footer-contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Trực Đại, Trực Ninh, Nam Định</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-phone"></i>
                    <span>Hotline: <a href="tel:0336120405" style="color:rgba(255,255,255,0.8)">0336.120.405</a></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-envelope"></i>
                    <span><a href="mailto:vuhuan123890@gmail.com" style="color:rgba(255,255,255,0.8)">vuhuan123890@gmail.com</a></span>
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
                        <span>Đổi trả 30 ngày<br><small style="color:rgba(255,255,255,0.5)">Miễn phí</small></span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="font-size:13px;color:rgba(255,255,255,0.7)">
                        <div style="width:36px;height:36px;background:rgba(255,255,255,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="fas fa-headset" style="color:#FFD600"></i></div>
                        <span>Hỗ trợ 24/7<br><small style="color:rgba(255,255,255,0.5)">Sẵn sàng</small></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom -->
        <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>© <?= date('Y') ?> Vũ Huân Shop. Tất cả quyền được bảo lưu.</span>
            <div class="d-flex gap-3">
                <a href="#" style="color:rgba(255,255,255,0.5)">Điều khoản sử dụng</a>
                <a href="#" style="color:rgba(255,255,255,0.5)">Chính sách bảo mật</a>
            </div>
            <div class="d-flex gap-2">
                <img src="https://img.icons8.com/color/48/mastercard.png" height="24" alt="Mastercard">
                <img src="https://img.icons8.com/color/48/visa.png" height="24" alt="Visa">
            </div>
        </div>
    </div>
</footer>

<!-- Toast Container (JS will inject here) -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Main JS -->
<script src="<?= APP_URL ?>/public/js/main.js?v=<?= time() ?>"></script>
</body>
</html>
