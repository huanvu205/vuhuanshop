<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
/* ===== WISHLIST PAGE ===== */
.page-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
    padding: 50px 0;
    position: relative;
    overflow: hidden;
}
.page-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(229,57,53,0.12);
}
.page-hero::after {
    content: '';
    position: absolute;
    bottom: -40px; left: 10%;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(229,57,53,0.07);
}
.page-hero .container { position: relative; z-index: 1; }
.page-hero h1 { color: #fff; font-weight: 800; font-size: 2rem; margin-bottom: 8px; }
.page-hero p { color: rgba(255,255,255,0.65); margin: 0; font-size: 0.95rem; }
.page-hero .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(229,57,53,0.2);
    border: 1px solid rgba(229,57,53,0.4);
    color: #ff6b6b;
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 0.82rem;
    font-weight: 600;
    margin-bottom: 14px;
}

/* Wishlist Grid */
.wishlist-section { padding: 50px 0; background: #f8f9fa; min-height: 60vh; }

.wishlist-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 12px;
}
.wishlist-toolbar h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.wishlist-toolbar h2 span {
    background: var(--primary);
    color: #fff;
    font-size: 0.75rem;
    padding: 2px 10px;
    border-radius: 50px;
    font-weight: 700;
}
.btn-clear-all {
    background: transparent;
    border: 1.5px solid #dc3545;
    color: #dc3545;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-clear-all:hover { background: #dc3545; color: #fff; }

/* Wishlist Card */
.wishlist-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 20px rgba(0,0,0,0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid #f5f5f5;
}
.wishlist-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
}
.wishlist-card .card-img-wrap {
    position: relative;
    overflow: hidden;
    background: #f8f8f8;
    aspect-ratio: 1/1;
}
.wishlist-card .card-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 16px;
    transition: transform 0.4s ease;
}
.wishlist-card:hover .card-img-wrap img { transform: scale(1.06); }

/* Badges */
.badge-sale {
    position: absolute;
    top: 12px; left: 12px;
    background: var(--primary);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    z-index: 2;
}
.badge-new {
    background: #2196F3;
}

/* Remove Button */
.btn-remove-wish {
    position: absolute;
    top: 10px; right: 10px;
    width: 34px; height: 34px;
    border-radius: 50%;
    background: #fff;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
    cursor: pointer;
    transition: all 0.2s;
    z-index: 3;
    font-size: 1rem;
}
.btn-remove-wish:hover { color: #dc3545; transform: scale(1.15); }
.btn-remove-wish.removing { animation: pop-out 0.3s forwards; }
@keyframes pop-out {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); }
    100% { transform: scale(0); opacity: 0; }
}

/* Card Body */
.wishlist-card .card-body-wish {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.wishlist-card .brand-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: #bbb;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.wishlist-card .product-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1a1a2e;
    line-height: 1.4;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-decoration: none;
}
.wishlist-card .product-name:hover { color: var(--primary); }

/* Stars */
.stars { display: flex; align-items: center; gap: 3px; margin-bottom: 10px; }
.stars i { font-size: 0.7rem; color: #ffc107; }
.stars span { font-size: 0.72rem; color: #999; margin-left: 4px; }

/* Price */
.price-wrap { margin-bottom: 14px; margin-top: auto; }
.price-current {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--primary);
}
.price-old {
    font-size: 0.82rem;
    color: #bbb;
    text-decoration: line-through;
    margin-left: 6px;
}
.price-discount {
    background: #fff3e0;
    color: #e65100;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    margin-left: 6px;
}

/* Action Buttons */
.card-actions { display: flex; gap: 8px; }
.btn-add-cart {
    flex: 1;
    background: linear-gradient(135deg, var(--primary), #c62828);
    color: #fff;
    border: none;
    padding: 10px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    text-decoration: none;
}
.btn-add-cart:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(229,57,53,0.35);
    color: #fff;
}
.btn-add-cart:active { transform: translateY(0); }
.btn-view-detail {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: 1.5px solid #e8e8e8;
    background: #fff;
    color: #777;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    flex-shrink: 0;
}
.btn-view-detail:hover { border-color: var(--primary); color: var(--primary); }

/* Empty State */
.empty-wishlist {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 30px rgba(0,0,0,0.06);
}
.empty-wishlist .empty-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fff5f5, #ffe0e0);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    font-size: 2.5rem;
}
.empty-wishlist h3 { font-weight: 700; color: #1a1a2e; margin-bottom: 8px; }
.empty-wishlist p { color: #999; margin-bottom: 24px; }
.btn-explore {
    background: linear-gradient(135deg, var(--primary), #c62828);
    color: #fff;
    padding: 12px 32px;
    border-radius: 12px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.25s;
}
.btn-explore:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(229,57,53,0.35); color: #fff; }

/* Breadcrumb */
.breadcrumb-wrap { padding: 12px 0; background: #f8f8f8; border-bottom: 1px solid #efefef; }
.breadcrumb-wrap .breadcrumb { margin: 0; font-size: 0.85rem; }
.breadcrumb-item a { color: var(--primary); text-decoration: none; }
.breadcrumb-item.active { color: #888; }

/* Toast notification */
.wishlist-toast {
    position: fixed;
    bottom: 24px; right: 24px;
    background: #1a1a2e;
    color: #fff;
    padding: 14px 22px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 500;
    box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    transform: translateY(80px);
    opacity: 0;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.wishlist-toast.show { transform: translateY(0); opacity: 1; }
.wishlist-toast i { color: #4CAF50; font-size: 1.1rem; }
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
                <li class="breadcrumb-item active">Sản phẩm yêu thích</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Hero -->
<div class="page-hero">
    <div class="container">
        <div class="hero-badge">
            <i class="fas fa-heart"></i>
            Wishlist
        </div>
        <h1><i class="fas fa-heart me-2" style="color:#ff6b6b;"></i>Sản phẩm yêu thích</h1>
        <p>Những sản phẩm bạn đã lưu lại — đừng để chúng chờ quá lâu!</p>
    </div>
</div>

<!-- Wishlist Section -->
<section class="wishlist-section">
    <div class="container">

        <?php if (!empty($items)): ?>

        <div class="wishlist-toolbar">
            <h2>
                <i class="fas fa-heart text-danger"></i>
                Danh sách yêu thích
                <span><?= count($items) ?> sản phẩm</span>
            </h2>
            <form action="<?= url('wishlist/clear') ?>" method="POST"
                  onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ danh sách yêu thích?')">
                <?= csrfToken() ?>
                <button type="submit" class="btn-clear-all">
                    <i class="fas fa-trash"></i> Xóa tất cả
                </button>
            </form>
        </div>

        <div class="row g-4 row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
            <?php foreach ($items as $item): ?>
            <?php
            $price = $item['price_discount'] ?? $item['price_sale'] ?? 0;
            $originalPrice = $item['price_sale'] ?? 0;
            $hasDiscount = !empty($item['price_discount']) && $item['price_discount'] < $item['price_sale'];
            $disc = $hasDiscount ? discountPercent($originalPrice, $price) : 0;
            ?>
            <div class="col wishlist-item" id="wish-item-<?= $item['product_id'] ?? $item['id'] ?? 0 ?>">
                <div class="wishlist-card">
                    <!-- Image -->
                    <div class="card-img-wrap">
                        <a href="<?= url('products/' . ($item['slug'] ?? $item['product_id'] ?? 0)) ?>">
                            <img src="<?= productImage($item['main_image'] ?? '') ?>"
                                 alt="<?= e($item['name'] ?? '') ?>"
                                 loading="lazy"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiB2aWV3Qm94PSIwIDAgNDAwIDQwMCI+PHJlY3Qgd2lkdGg9IjQwMCIgaGVpZ2h0PSI0MDAiIGZpbGw9IiNmNWY1ZjUiLz48Y2lyY2xlIGN4PSIyMDAiIGN5PSIxNjAiIHI9IjUwIiBmaWxsPSIjZTBlMGUwIi8+PGNpcmNsZSBjeD0iMjAwIiBjeT0iMTYwIiByPSIzMCIgZmlsbD0iIzk5OTk5OSIvPjxwYXRoIGQ9Ik0xNDAgMjIwSDI2MlYyODBoLTEyMlYyMjB6IiBmaWxsPSIjOTk5OTk5Ii8+PC9zdmc+'">
                        </a>

                        <?php if ($disc > 0): ?>
                            <span class="badge-sale">-<?= $disc ?>%</span>
                        <?php endif; ?>

                        <!-- Remove button -->
                        <button class="btn-remove-wish"
                                onclick="removeWishlist(<?= $item['product_id'] ?? $item['id'] ?? 0 ?>, this)"
                                title="Bỏ yêu thích">
                            <i class="fas fa-heart" style="color:#E53935;"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="card-body-wish">
                        <?php if (!empty($item['brand_name'])): ?>
                            <div class="brand-label"><?= e($item['brand_name']) ?></div>
                        <?php endif; ?>

                        <a href="<?= url('products/' . ($item['slug'] ?? $item['product_id'] ?? 0)) ?>"
                           class="product-name">
                           <?= e($item['name'] ?? '') ?>
                        </a>

                        <!-- Stars -->
                        <?php
                        $rating = round($item['rating'] ?? 4.5);
                        ?>
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="<?= $i <= $rating ? 'fas' : 'far' ?> fa-star"></i>
                            <?php endfor; ?>
                            <span>(<?= number_format($item['review_count'] ?? 0) ?>)</span>
                        </div>

                        <!-- Price -->
                        <div class="price-wrap">
                            <span class="price-current"><?= formatPrice($price) ?></span>
                            <?php if ($hasDiscount): ?>
                                <span class="price-old"><?= formatPrice($originalPrice) ?></span>
                                <span class="price-discount">-<?= $disc ?>%</span>
                            <?php endif; ?>
                        </div>

                        <!-- Actions -->
                        <div class="card-actions">
                            <button class="btn-add-cart"
                                    data-product="<?= $item['product_id'] ?? $item['id'] ?? 0 ?>">
                                <i class="fas fa-cart-plus"></i>
                                Thêm vào giỏ
                            </button>
                            <a href="<?= url('products/' . ($item['slug'] ?? $item['product_id'] ?? 0)) ?>"
                               class="btn-view-detail" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Also like section -->
        <div class="text-center mt-5">
            <p class="text-muted mb-3">Khám phá thêm sản phẩm phù hợp với bạn</p>
            <a href="<?= url('products') ?>" class="btn-explore">
                <i class="fas fa-th-large"></i> Xem tất cả sản phẩm
            </a>
        </div>

        <?php else: ?>

        <!-- Empty State -->
        <div class="empty-wishlist">
            <div class="empty-icon">
                <i class="far fa-heart" style="color:#E53935;"></i>
            </div>
            <h3>Danh sách yêu thích trống</h3>
            <p>Bạn chưa lưu sản phẩm nào. Hãy khám phá và thêm những sản phẩm bạn thích!</p>
            <a href="<?= url('products') ?>" class="btn-explore">
                <i class="fas fa-search"></i> Khám phá sản phẩm
            </a>
        </div>

        <?php endif; ?>
    </div>
</section>

<!-- Toast Notification -->
<div class="wishlist-toast" id="wishToast">
    <i class="fas fa-check-circle"></i>
    <span id="wishToastMsg">Đã cập nhật!</span>
</div>

<script>
function showToast(msg, isError = false) {
    const toast = document.getElementById('wishToast');
    const icon = toast.querySelector('i');
    document.getElementById('wishToastMsg').textContent = msg;
    icon.className = isError ? 'fas fa-times-circle' : 'fas fa-check-circle';
    icon.style.color = isError ? '#f44336' : '#4CAF50';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}

function removeWishlist(productId, btn) {
    btn.classList.add('removing');
    const card = document.getElementById('wish-item-' + productId);

    fetch('<?= url('wishlist/remove') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            btn.classList.remove('removing');
            showToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại!', true);
            return;
        }
        setTimeout(() => {
            if (card) {
                card.style.transition = 'all 0.4s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    card.remove();
                    // Cập nhật số đếm trên toolbar nếu còn lại 0 sản phẩm
                    if (data.remaining === 0) {
                        location.reload();
                    }
                }, 400);
            }
            showToast('Đã bỏ khỏi danh sách yêu thích');
        }, 300);
    })
    .catch(() => {
        btn.classList.remove('removing');
        showToast('Có lỗi xảy ra, vui lòng thử lại!', true);
    });
}

</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
