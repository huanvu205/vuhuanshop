<?php
function adminStatusBadge($status) {
    $status = $status ?: 'unknown';
    $map = [
        'active' => 'success',
        'inactive' => 'secondary',
        'pending' => 'warning',
        'confirmed' => 'info',
        'processing' => 'primary',
        'shipping' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        'paid' => 'success',
        'unpaid' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'draft' => 'secondary',
        'published' => 'success',
        'banned' => 'danger',
    ];
    $class = $map[$status] ?? 'secondary';
    return '<span class="badge bg-' . $class . '-subtle text-' . $class . ' border">' . e(ucfirst($status)) . '</span>';
}

function adminHeader($heading, $subtitle = '', $createUrl = null, $createText = 'Them moi') {
    require __DIR__ . '/layouts/header.php';
    echo '<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">';
    echo '<div><h4 class="mb-0 text-dark fw-bold">' . e($heading) . '</h4>';
    if ($subtitle) echo '<span class="text-muted small">' . e($subtitle) . '</span>';
    echo '</div>';
    if ($createUrl) {
        echo '<a href="' . e($createUrl) . '" class="btn btn-danger text-white px-4 fw-semibold" style="border-radius:10px;border:none"><i class="fas fa-plus me-1"></i>' . e($createText) . '</a>';
    }
    echo '</div>';
}

function adminFooter() {
    require __DIR__ . '/layouts/footer.php';
}

function adminPagination($currentPage, $totalPages, $baseUrl) {
    if (($totalPages ?? 0) <= 1) return;
    echo '<div class="mt-4">' . pagination((int)$currentPage, (int)$totalPages, $baseUrl) . '</div>';
}
?>
