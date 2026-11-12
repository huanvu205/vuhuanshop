<?php
// Khởi tạo các biến mặc định do Controller::view() extract() truyền sang
$categories      = $categories ?? [];
$brands          = $brands ?? [];
$currentCategory = $currentCategory ?? null;
$total           = $total ?? 0;
$sort            = $sort ?? 'newest';
$currentPage     = $currentPage ?? 1;
$totalPages      = $totalPages ?? 1;
$products        = $products ?? [];
$filters         = $filters ?? [];

require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Trang chủ</a></li>
                <li class="breadcrumb-item active">Sản phẩm <?= $currentCategory ? '› ' . e($currentCategory['name']) : '' ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4">
    <div class="row g-3">
        <!-- Sidebar Filter -->
        <div class="col-lg-2 col-md-3 d-none d-md-block">
            <div class="filter-sidebar">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="font-size:15px;font-weight:700;margin:0">Bộ lọc</h5>
                    <a href="<?= url('products') ?>" class="text-danger" style="font-size:12px">Xóa tất cả</a>
                </div>
                
                <form method="GET" action="<?= url('products') ?>">
                    <!-- Category Filter -->
                    <div class="filter-section">
                        <div class="filter-title">Danh mục</div>
                        <?php foreach ($categories as $cat): ?>
                        <label class="filter-option">
                            <input type="radio" name="category" value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'checked' : '' ?>>
                            <?= e($cat['name']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Brand Filter -->
                    <div class="filter-section">
                        <div class="filter-title">Thương hiệu</div>
                        <?php foreach ($brands as $brand): ?>
                        <label class="filter-option">
                            <input type="radio" name="brand" value="<?= $brand['id'] ?>" <?= ($filters['brand_id'] ?? '') == $brand['id'] ? 'checked' : '' ?>>
                            <?= e($brand['name']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="filter-section">
                        <div class="filter-title">Khoảng giá</div>
                        <div class="d-flex gap-2 mb-2">
                            <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ" value="<?= e($filters['min_price'] ?? '') ?>">
                            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến" value="<?= e($filters['max_price'] ?? '') ?>">
                        </div>
                        <div class="d-flex flex-column gap-1">
                            <?php $priceRanges = [['Dưới 2 triệu','',2000000],['2-5 triệu',2000000,5000000],['5-10 triệu',5000000,10000000],['10-20 triệu',10000000,20000000],['Trên 20 triệu',20000000,'']]; ?>
                            <?php foreach ($priceRanges as $pr): ?>
                            <a href="?min_price=<?= $pr[1] ?>&max_price=<?= $pr[2] ?><?= $currentCategory ? '&category=' . $currentCategory['id'] : '' ?>" 
                               style="font-size:12px;color:var(--text-medium);text-decoration:none;padding:4px 0" 
                               class="filter-option"><?= $pr[0] ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-danger btn-sm w-100 mt-2">
                        <i class="fas fa-filter me-1"></i>Lọc sản phẩm
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Products Area -->
        <div class="col-lg-10 col-md-9">
            <!-- Header & Sort -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h1 style="font-size:20px;font-weight:700;margin:0">
                        <?= $currentCategory ? e($currentCategory['name']) : 'Tất cả sản phẩm' ?>
                    </h1>
                    <span style="font-size:13px;color:var(--text-muted)"><?= number_format($total) ?> sản phẩm</span>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <!-- Mobile Filter -->
                    <button class="btn btn-outline-secondary btn-sm d-md-none" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                        <i class="fas fa-filter me-1"></i>Lọc
                    </button>
                    
                    <!-- Sort -->
                    <select class="form-select form-select-sm" style="width:auto" onchange="changeSortOrder(this.value)">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="best_selling" <?= $sort === 'best_selling' ? 'selected' : '' ?>>Bán chạy nhất</option>
                        <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Đánh giá cao</option>
                    </select>
                    
                    <!-- View Mode -->
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary active" id="grid-view-btn" title="Lưới">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="list-view-btn" title="Danh sách">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <div style="font-size:60px;margin-bottom:16px">😔</div>
                <h3 style="font-size:18px;font-weight:600;color:var(--text-medium)">Không tìm thấy sản phẩm</h3>
                <p style="color:var(--text-muted);font-size:14px">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                <a href="<?= url('products') ?>" class="btn btn-danger mt-2">Xem tất cả sản phẩm</a>
            </div>
            <?php else: ?>
            <div class="products-grid" id="products-container">
                <?php foreach ($products as $product): ?>
                <?php include __DIR__ . '/../partials/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="mt-4">
                <?php
                $currentUrl = url('products');
                $queryParams = $_GET;
                
                echo '<nav><ul class="pagination justify-content-center">';
                
                if ($currentPage > 1) {
                    $queryParams['page'] = $currentPage - 1;
                    echo '<li class="page-item"><a class="page-link" href="' . $currentUrl . '?' . http_build_query($queryParams) . '"><i class="fas fa-chevron-left"></i></a></li>';
                }
                
                for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
                    $queryParams['page'] = $i;
                    $active = $i === $currentPage ? ' active' : '';
                    echo '<li class="page-item' . $active . '"><a class="page-link" href="' . $currentUrl . '?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
                }
                
                if ($currentPage < $totalPages) {
                    $queryParams['page'] = $currentPage + 1;
                    echo '<li class="page-item"><a class="page-link" href="' . $currentUrl . '?' . http_build_query($queryParams) . '"><i class="fas fa-chevron-right"></i></a></li>';
                }
                
                echo '</ul></nav>';
                ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Mobile Filter Offcanvas -->
<div class="offcanvas offcanvas-start" id="filterOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Bộ lọc sản phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="<?= url('products') ?>">
            <div class="mb-3">
                <div class="fw-bold mb-2" style="font-size:14px">Danh mục</div>
                <?php foreach ($categories as $cat): ?>
                <label class="filter-option">
                    <input type="radio" name="category" value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'checked' : '' ?>>
                    <?= e($cat['name']) ?>
                </label>
                <?php endforeach; ?>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2" style="font-size:14px">Thương hiệu</div>
                <?php foreach ($brands as $brand): ?>
                <label class="filter-option">
                    <input type="radio" name="brand" value="<?= $brand['id'] ?>" <?= ($filters['brand_id'] ?? '') == $brand['id'] ? 'checked' : '' ?>>
                    <?= e($brand['name']) ?>
                </label>
                <?php endforeach; ?>
            </div>
            <div class="mb-3">
                <div class="fw-bold mb-2" style="font-size:14px">Khoảng giá</div>
                <div class="d-flex gap-2">
                    <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ">
                    <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger flex-1">Áp dụng</button>
                <a href="<?= url('products') ?>" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>
</div>

<script>
function changeSortOrder(sort) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sort);
    window.location.href = url.toString();
}

// View mode toggle
document.getElementById('grid-view-btn')?.addEventListener('click', function() {
    document.getElementById('products-container').className = 'products-grid';
    this.classList.add('active');
    document.getElementById('list-view-btn').classList.remove('active');
});

document.getElementById('list-view-btn')?.addEventListener('click', function() {
    const container = document.getElementById('products-container');
    container.className = '';
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.gap = '12px';
    this.classList.add('active');
    document.getElementById('grid-view-btn').classList.remove('active');
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
