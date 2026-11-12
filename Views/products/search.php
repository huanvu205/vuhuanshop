<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 py-2" style="font-size:0.85rem;">
                <li class="breadcrumb-item"><a href="<?= url() ?>" class="text-decoration-none text-danger">Trang chủ</a></li>
                <li class="breadcrumb-item active">Tìm kiếm</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mt-4 pb-5">
    <div class="row g-3">
        <!-- Sidebar Filter (Desktop) -->
        <div class="col-lg-2 col-md-3 d-none d-md-block">
            <div class="filter-sidebar p-3 bg-white border rounded-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="font-size:14px; font-weight:700; margin:0;">Bộ lọc</h5>
                    <a href="<?= url('search?q=' . urlencode($keyword)) ?>" class="text-danger" style="font-size:11px; text-decoration:none;">Xóa tất cả</a>
                </div>
                
                <form method="GET" action="<?= url('search') ?>">
                    <input type="hidden" name="q" value="<?= e($keyword) ?>">
                    
                    <!-- Category Filter -->
                    <div class="filter-section mb-3">
                        <div class="filter-title fw-bold small text-secondary mb-2" style="font-size:12px;">Danh mục</div>
                        <?php foreach ($categories as $cat): ?>
                        <label class="filter-option d-block small mb-1" style="font-size:13px; cursor:pointer;">
                            <input type="radio" name="category" value="<?= $cat['id'] ?>" <?= ($_GET['category'] ?? '') == $cat['id'] ? 'checked' : '' ?> onchange="this.form.submit()">
                            <?= e($cat['name']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Brand Filter -->
                    <div class="filter-section mb-3">
                        <div class="filter-title fw-bold small text-secondary mb-2" style="font-size:12px;">Thương hiệu</div>
                        <?php foreach ($brands as $brand): ?>
                        <label class="filter-option d-block small mb-1" style="font-size:13px; cursor:pointer;">
                            <input type="radio" name="brand" value="<?= $brand['id'] ?>" <?= ($_GET['brand'] ?? '') == $brand['id'] ? 'checked' : '' ?> onchange="this.form.submit()">
                            <?= e($brand['name']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Products Area -->
        <div class="col-lg-10 col-md-9">
            <!-- Search Title & Results count -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 pb-3 border-bottom">
                <div>
                    <h1 style="font-size:20px; font-weight:700; margin:0;">
                        Kết quả tìm kiếm cho: <span class="text-danger">"<?= e($keyword) ?>"</span>
                    </h1>
                    <span style="font-size:13px; color:var(--text-muted);">Tìm thấy <?= number_format($total) ?> kết quả</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- Mobile Filter Trigger -->
                    <button class="btn btn-outline-secondary btn-sm d-md-none" data-bs-toggle="offcanvas" data-bs-target="#searchFilterOffcanvas">
                        <i class="fas fa-filter me-1"></i>Lọc
                    </button>
                </div>
            </div>
            
            <!-- Products Grid -->
            <?php if (empty($products)): ?>
                <div class="text-center py-5 bg-white border rounded-4 shadow-sm">
                    <div style="font-size:60px; margin-bottom:16px;">🔍</div>
                    <h3 style="font-size:18px; font-weight:600; color:#475569;">Không tìm thấy kết quả nào phù hợp</h3>
                    <p style="color:#64748b; font-size:14px;">Thử sử dụng các từ khóa khác hoặc xóa bớt bộ lọc.</p>
                    <a href="<?= url('products') ?>" class="btn btn-danger mt-2" style="background:var(--primary); border-radius:8px;">Xem tất cả sản phẩm</a>
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
                        $currentUrl = url('search');
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
<div class="offcanvas offcanvas-start" id="searchFilterOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold">Bộ lọc sản phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form method="GET" action="<?= url('search') ?>">
            <input type="hidden" name="q" value="<?= e($keyword) ?>">
            
            <div class="mb-4">
                <div class="fw-bold mb-2 text-secondary" style="font-size:14px;">Danh mục</div>
                <?php foreach ($categories as $cat): ?>
                <label class="filter-option d-block mb-1" style="font-size:14px; cursor:pointer;">
                    <input type="radio" name="category" value="<?= $cat['id'] ?>" <?= ($_GET['category'] ?? '') == $cat['id'] ? 'checked' : '' ?>>
                    <?= e($cat['name']) ?>
                </label>
                <?php endforeach; ?>
            </div>
            
            <div class="mb-4">
                <div class="fw-bold mb-2 text-secondary" style="font-size:14px;">Thương hiệu</div>
                <?php foreach ($brands as $brand): ?>
                <label class="filter-option d-block mb-1" style="font-size:14px; cursor:pointer;">
                    <input type="radio" name="brand" value="<?= $brand['id'] ?>" <?= ($_GET['brand'] ?? '') == $brand['id'] ? 'checked' : '' ?>>
                    <?= e($brand['name']) ?>
                </label>
                <?php endforeach; ?>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger flex-grow-1" style="background:var(--primary); border-radius:8px;">Áp dụng</button>
                <a href="<?= url('search?q=' . urlencode($keyword)) ?>" class="btn btn-outline-secondary" style="border-radius:8px;">Xóa lọc</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
