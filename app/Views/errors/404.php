<?php
// Set $title for header
$title   = '404 - Trang không tìm thấy';
$metaDesc = 'Trang bạn tìm kiếm không tồn tại hoặc đã bị di chuyển - ' . APP_NAME;
require __DIR__ . '/../layouts/header.php';
?>

<style>
    /* ===== 404 PAGE STYLES ===== */
    .error-404-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 16px;
        position: relative;
        overflow: hidden;
    }

    /* Subtle background pattern */
    .error-404-wrapper::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 2px 2px, rgba(198,40,40,0.06) 1px, transparent 0);
        background-size: 32px 32px;
        pointer-events: none;
    }

    /* Decorative circles */
    .e404-deco {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }
    .e404-deco-1 {
        width: 400px; height: 400px;
        top: -100px; right: -100px;
        background: radial-gradient(circle, rgba(198,40,40,0.06) 0%, transparent 70%);
    }
    .e404-deco-2 {
        width: 300px; height: 300px;
        bottom: -80px; left: -80px;
        background: radial-gradient(circle, rgba(198,40,40,0.04) 0%, transparent 70%);
    }
    .e404-deco-3 {
        width: 200px; height: 200px;
        top: 30%; left: 5%;
        background: radial-gradient(circle, rgba(198,40,40,0.03) 0%, transparent 70%);
    }

    /* Main content */
    .error-404-content {
        position: relative;
        z-index: 1;
        text-align: center;
        max-width: 680px;
        width: 100%;
    }

    /* Big 404 number */
    .e404-number {
        font-size: clamp(6rem, 20vw, 10rem);
        font-weight: 900;
        line-height: 1;
        letter-spacing: -4px;
        background: linear-gradient(135deg, #c62828 0%, #e53935 40%, #ef5350 70%, #ffcdd2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: block;
        margin-bottom: 0;
        position: relative;
        user-select: none;
        animation: numberFloat 3s ease-in-out infinite;
    }

    @keyframes numberFloat {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-10px); }
    }

    /* Glitch effect on hover */
    .e404-number:hover {
        animation: glitch 0.5s linear;
    }
    @keyframes glitch {
        0%   { text-shadow: none; }
        20%  { text-shadow: -3px 0 #ef4444, 3px 0 #3b82f6; letter-spacing: -2px; }
        40%  { text-shadow: 3px 0 #ef4444, -3px 0 #3b82f6; letter-spacing: -6px; }
        60%  { text-shadow: -2px 0 #ef4444, 2px 0 #3b82f6; letter-spacing: -4px; }
        80%  { text-shadow: none; letter-spacing: -4px; }
        100% { text-shadow: none; letter-spacing: -4px; }
    }

    /* Illustration below number */
    .e404-illustration {
        margin: -20px auto 24px;
        width: 180px;
        height: 120px;
        position: relative;
    }

    .e404-illustration svg {
        width: 100%;
        height: 100%;
    }

    /* Text */
    .e404-title {
        font-size: 1.625rem;
        font-weight: 800;
        color: #1a1a2e;
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }

    .e404-desc {
        font-size: 1rem;
        color: #6b7280;
        line-height: 1.7;
        margin-bottom: 36px;
        max-width: 480px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Action buttons */
    .e404-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 48px;
    }

    .btn-e404-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 13px 28px;
        font-size: 0.9375rem;
        font-weight: 700;
        font-family: inherit;
        background: linear-gradient(135deg, #e53935 0%, #c62828 100%);
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.25s;
        position: relative;
        overflow: hidden;
    }
    .btn-e404-primary::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
        opacity: 0;
        transition: opacity 0.25s;
    }
    .btn-e404-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(198,40,40,0.35);
        color: white;
    }
    .btn-e404-primary:hover::before { opacity: 1; }
    .btn-e404-primary:active { transform: translateY(0); }

    .btn-e404-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 13px 28px;
        font-size: 0.9375rem;
        font-weight: 600;
        font-family: inherit;
        background: white;
        color: #374151;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.25s;
    }
    .btn-e404-secondary:hover {
        border-color: #c62828;
        color: #c62828;
        background: #fff5f5;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    }
    .btn-e404-secondary:active { transform: translateY(0); }

    /* Search box */
    .e404-search {
        max-width: 420px;
        margin: 0 auto 48px;
    }

    .e404-search h6 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 10px;
    }

    .e404-search-input-wrap {
        display: flex;
        gap: 0;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .e404-search-input-wrap:focus-within {
        border-color: #c62828;
        box-shadow: 0 0 0 3px rgba(198,40,40,0.09);
    }

    .e404-search-input-wrap input {
        flex: 1;
        padding: 12px 16px;
        border: none;
        outline: none;
        font-size: 0.9rem;
        font-family: inherit;
        color: #1f2937;
        background: white;
    }
    .e404-search-input-wrap input::placeholder { color: #9ca3af; }

    .e404-search-btn {
        padding: 12px 20px;
        background: linear-gradient(135deg, #e53935, #c62828);
        border: none;
        color: white;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.2s;
    }
    .e404-search-btn:hover { background: linear-gradient(135deg, #c62828, #9b1c1c); }

    /* Popular links */
    .e404-popular {
        margin-bottom: 48px;
    }

    .e404-popular h6 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #9ca3af;
        margin-bottom: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .e404-popular-links {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }

    .e404-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        background: white;
        border: 1.5px solid #e5e7eb;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        text-decoration: none;
        transition: all 0.2s;
    }
    .e404-tag:hover {
        border-color: #c62828;
        background: #fff5f5;
        color: #c62828;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .e404-tag i { font-size: 11px; color: #c62828; }

    /* Stats ribbon */
    .e404-stats-ribbon {
        display: flex;
        justify-content: center;
        gap: 0;
        flex-wrap: wrap;
        max-width: 500px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #f3f4f6;
        overflow: hidden;
    }

    .e404-stat {
        flex: 1;
        min-width: 120px;
        padding: 16px 20px;
        text-align: center;
        border-right: 1px solid #f3f4f6;
    }
    .e404-stat:last-child { border-right: none; }

    .e404-stat-num {
        font-size: 1.25rem;
        font-weight: 800;
        color: #c62828;
        display: block;
        margin-bottom: 2px;
    }
    .e404-stat-label {
        font-size: 11px;
        color: #9ca3af;
    }

    /* Responsive */
    @media (max-width: 575.98px) {
        .e404-actions { flex-direction: column; align-items: center; }
        .btn-e404-primary, .btn-e404-secondary { width: 100%; max-width: 300px; justify-content: center; }
        .e404-stats-ribbon { border-radius: 12px; }
        .e404-stat { min-width: 100px; padding: 12px 16px; }
        .e404-number { letter-spacing: -2px; }
    }
</style>

<div class="error-404-wrapper">
    <div class="e404-deco e404-deco-1"></div>
    <div class="e404-deco e404-deco-2"></div>
    <div class="e404-deco e404-deco-3"></div>

    <div class="error-404-content">

        <!-- Big 404 -->
        <span class="e404-number" title="404">404</span>

        <!-- SVG Illustration -->
        <div class="e404-illustration">
            <svg viewBox="0 0 180 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Ground -->
                <ellipse cx="90" cy="105" rx="70" ry="8" fill="rgba(198,40,40,0.08)"/>
                <!-- Robot body -->
                <rect x="60" y="50" width="60" height="50" rx="10" fill="#fee2e2" stroke="#fca5a5" stroke-width="2"/>
                <!-- Robot head -->
                <rect x="68" y="24" width="44" height="34" rx="8" fill="#fef2f2" stroke="#fca5a5" stroke-width="2"/>
                <!-- Eyes -->
                <circle cx="80" cy="38" r="6" fill="white" stroke="#fca5a5" stroke-width="1.5"/>
                <circle cx="100" cy="38" r="6" fill="white" stroke="#fca5a5" stroke-width="1.5"/>
                <!-- Sad eyes -->
                <circle cx="80" cy="39" r="3" fill="#c62828"/>
                <circle cx="100" cy="39" r="3" fill="#c62828"/>
                <!-- X on eyes to indicate error -->
                <line x1="77" y1="35" x2="83" y2="41" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="83" y1="35" x2="77" y2="41" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="97" y1="35" x2="103" y2="41" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="103" y1="35" x2="97" y2="41" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                <!-- Mouth (sad) -->
                <path d="M79 48 Q90 44 101 48" stroke="#fca5a5" stroke-width="2" fill="none" stroke-linecap="round"/>
                <!-- Antenna -->
                <line x1="90" y1="24" x2="90" y2="14" stroke="#fca5a5" stroke-width="2" stroke-linecap="round"/>
                <circle cx="90" cy="11" r="4" fill="#c62828"/>
                <!-- Arms -->
                <rect x="40" y="55" width="22" height="10" rx="5" fill="#fee2e2" stroke="#fca5a5" stroke-width="1.5"/>
                <rect x="118" y="55" width="22" height="10" rx="5" fill="#fee2e2" stroke="#fca5a5" stroke-width="1.5"/>
                <!-- Question marks floating -->
                <text x="20" y="45" fill="rgba(198,40,40,0.3)" font-size="16" font-weight="bold" font-family="Arial">?</text>
                <text x="145" y="40" fill="rgba(198,40,40,0.25)" font-size="20" font-weight="bold" font-family="Arial">?</text>
                <text x="155" y="65" fill="rgba(198,40,40,0.2)" font-size="12" font-weight="bold" font-family="Arial">?</text>
                <!-- Legs -->
                <rect x="72" y="98" width="14" height="14" rx="4" fill="#fca5a5"/>
                <rect x="94" y="98" width="14" height="14" rx="4" fill="#fca5a5"/>
            </svg>
        </div>

        <!-- Text -->
        <h1 class="e404-title">Ôi! Trang này không tồn tại 😅</h1>
        <p class="e404-desc">
            Trang bạn đang tìm kiếm có thể đã bị xóa, đổi tên hoặc tạm thời không khả dụng.
            Đừng lo — hãy thử tìm kiếm hoặc quay lại trang chủ để tiếp tục mua sắm nhé!
        </p>

        <!-- Action Buttons -->
        <div class="e404-actions">
            <a href="<?= url() ?>" class="btn-e404-primary">
                <i class="fas fa-home"></i>
                Về trang chủ
            </a>
            <a href="javascript:history.back()" class="btn-e404-secondary">
                <i class="fas fa-arrow-left"></i>
                Quay lại
            </a>
        </div>

        <!-- Search -->
        <div class="e404-search">
            <h6>Hoặc tìm kiếm sản phẩm bạn cần:</h6>
            <div class="e404-search-input-wrap">
                <input
                    type="text"
                    id="e404-search-input"
                    placeholder="Tìm iPhone, Samsung, Laptop..."
                    autocomplete="off"
                >
                <button class="e404-search-btn" id="e404-search-btn" title="Tìm kiếm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Popular links -->
        <div class="e404-popular">
            <h6>Danh mục phổ biến</h6>
            <div class="e404-popular-links">
                <a href="<?= url('products') ?>?category=1" class="e404-tag">
                    <i class="fas fa-mobile-alt"></i> Điện thoại
                </a>
                <a href="<?= url('products') ?>?category=2" class="e404-tag">
                    <i class="fas fa-laptop"></i> Laptop
                </a>
                <a href="<?= url('products') ?>?category=3" class="e404-tag">
                    <i class="fas fa-tablet-alt"></i> Máy tính bảng
                </a>
                <a href="<?= url('products') ?>?category=4" class="e404-tag">
                    <i class="fas fa-clock"></i> Đồng hồ thông minh
                </a>
                <a href="<?= url('products') ?>?category=5" class="e404-tag">
                    <i class="fas fa-headphones"></i> Phụ kiện
                </a>
                <a href="<?= url('products') ?>?sale=1" class="e404-tag">
                    <i class="fas fa-tags"></i> Khuyến mãi
                </a>
                <a href="<?= url('products') ?>?new=1" class="e404-tag">
                    <i class="fas fa-star"></i> Sản phẩm mới
                </a>
                <a href="<?= url('orders/track') ?>" class="e404-tag">
                    <i class="fas fa-truck"></i> Theo dõi đơn hàng
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="e404-stats-ribbon">
            <div class="e404-stat">
                <span class="e404-stat-num">10K+</span>
                <span class="e404-stat-label">Sản phẩm</span>
            </div>
            <div class="e404-stat">
                <span class="e404-stat-num">500K+</span>
                <span class="e404-stat-label">Khách hàng</span>
            </div>
            <div class="e404-stat">
                <span class="e404-stat-num">100+</span>
                <span class="e404-stat-label">Cửa hàng</span>
            </div>
            <div class="e404-stat">
                <span class="e404-stat-num">24/7</span>
                <span class="e404-stat-label">Hỗ trợ</span>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    'use strict';

    // ── Search functionality ──
    var searchInput = document.getElementById('e404-search-input');
    var searchBtn   = document.getElementById('e404-search-btn');
    var searchUrl   = '<?= url('search') ?>';

    function doSearch() {
        var q = searchInput ? searchInput.value.trim() : '';
        if (q) {
            window.location.href = searchUrl + '?q=' + encodeURIComponent(q);
        }
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', doSearch);
    }
    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') doSearch();
        });
        // Auto focus search
        setTimeout(function () { searchInput.focus(); }, 600);
    }

    // ── Number glitch on click ──
    var num = document.querySelector('.e404-number');
    if (num) {
        num.addEventListener('click', function () {
            this.style.animation = 'none';
            var el = this;
            setTimeout(function () {
                el.style.animation = '';
            }, 10);
        });
    }

    // ── Animate stats on scroll/load ──
    function animateCounter(el, target, suffix) {
        var start = 0;
        var duration = 1500;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3); // ease out cubic
            var current = Math.floor(eased * target);
            el.textContent = (current >= 1000 ? (current / 1000).toFixed(0) + 'K' : current) + suffix;
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // Trigger animation when in view
    var observed = false;
    var ribbon = document.querySelector('.e404-stats-ribbon');
    if (ribbon && 'IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            if (entries[0].isIntersecting && !observed) {
                observed = true;
                io.disconnect();
            }
        }, { threshold: 0.3 });
        io.observe(ribbon);
    }

})();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
