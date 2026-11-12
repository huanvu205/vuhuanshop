<?php
/**
 * ProductController - Trang sản phẩm khách hàng
 */
class ProductController extends Controller {
    private $productModel;
    private $categoryModel;
    private $brandModel;
    private $reviewModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->brandModel = new BrandModel();
        $this->reviewModel = new ReviewModel();
    }
    
    public function index() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $sort = $_GET['sort'] ?? 'newest';
        
        $filters = [];
        if (!empty($_GET['category'])) $filters['category_id'] = (int)$_GET['category'];
        if (!empty($_GET['brand'])) $filters['brand_id'] = (int)$_GET['brand'];
        if (!empty($_GET['min_price'])) $filters['min_price'] = (float)$_GET['min_price'];
        if (!empty($_GET['max_price'])) $filters['max_price'] = (float)$_GET['max_price'];
        if (!empty($_GET['q'])) $filters['search'] = sanitize($_GET['q']);
        
        $total = $this->productModel->countList($filters);
        $perPage = PRODUCTS_PER_PAGE;
        $totalPages = ceil($total / $perPage);
        
        $category = null;
        if (!empty($filters['category_id'])) {
            $category = $this->categoryModel->findById($filters['category_id']);
        }
        
        $this->view('products.index', [
            'title' => ($category ? $category['name'] . ' - ' : '') . 'Sản phẩm - ' . APP_NAME,
            'products' => $this->productModel->getList($filters, $page, $perPage, $sort),
            'categories' => $this->categoryModel->getActive(),
            'brands' => $this->brandModel->getActive(),
            'currentCategory' => $category,
            'filters' => $filters,
            'sort' => $sort,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }
    
    public function detail($slug) {
        $product = $this->productModel->findBySlug($slug);
        
        if (!$product) {
            http_response_code(404);
            $this->view('errors.404', ['title' => '404 - Không tìm thấy']);
            return;
        }
        
        // Tăng view count
        $this->productModel->incrementView($product['id']);
        
        $images = $this->productModel->getImages($product['id']);
        $specs = $this->productModel->getSpecs($product['id']);
        $reviews = $this->reviewModel->getByProduct($product['id']);
        $reviewCount = $this->reviewModel->countByProduct($product['id']);
        $ratingStats = $this->reviewModel->getRatingStats($product['id']);
        $relatedProducts = $this->productModel->getRelated($product['id'], $product['category_id']);
        $skuVariants = $this->productModel->getVariants($product['id']);
        
        $isWishlisted = false;
        $hasReviewed = false;
        if ($this->isLoggedIn()) {
            $wishlistModel = new WishlistModel();
            $isWishlisted = $wishlistModel->isWishlisted($this->getUserId(), $product['id']);
            $hasReviewed = $this->reviewModel->hasReviewed($this->getUserId(), $product['id']);
        }
        
        $this->view('products.detail', [
            'title' => $product['name'] . ' - ' . APP_NAME,
            'product' => $product,
            'images' => $images,
            'specs' => $specs,
            'reviews' => $reviews,
            'reviewCount' => $reviewCount,
            'ratingStats' => $ratingStats,
            'relatedProducts' => $relatedProducts,
            'skuVariants' => $skuVariants,
            'isWishlisted' => $isWishlisted,
            'hasReviewed' => $hasReviewed,
        ]);
    }
    
    public function search() {
        $keyword = sanitize($_GET['q'] ?? '');
        
        // AJAX search
        if (!empty($_GET['ajax']) || !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            if (mb_strlen($keyword) < 2) {
                $this->json(['products' => []]);
                return;
            }
            
            try {
                $products = $this->productModel->search($keyword, 8);
                $result = [];
                if (is_array($products)) {
                    foreach ($products as $p) {
                        $price = $p['price_discount'] ?? $p['price_sale'];
                        $result[] = [
                            'id' => $p['id'],
                            'name' => e($p['name']),
                            'slug' => $p['slug'],
                            'price' => formatPrice($price),
                            'image' => productImage($p['main_image']),
                            'brand' => e($p['brand_name'] ?? ''),
                            'url' => url('products/' . $p['slug']),
                        ];
                    }
                }
                $this->json(['products' => $result, 'keyword' => e($keyword)]);
            } catch (\Throwable $e) {
                error_log('AJAX Search error: ' . $e->getMessage());
                $this->json(['products' => [], 'error' => $e->getMessage()]);
            }
            return;
        }
        
        // Full search page
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = ['search' => $keyword];
        $total = $this->productModel->countList($filters);
        $perPage = PRODUCTS_PER_PAGE;
        $totalPages = ceil($total / $perPage);
        
        $this->view('products.search', [
            'title' => 'Tìm kiếm: ' . e($keyword) . ' - ' . APP_NAME,
            'keyword' => $keyword,
            'products' => $this->productModel->getList($filters, $page, $perPage),
            'categories' => $this->categoryModel->getActive(),
            'brands' => $this->brandModel->getActive(),
            'total' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
    
    public function byCategory($slug) {
        $category = $this->categoryModel->findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            return;
        }
        
        $_GET['category'] = $category['id'];
        $this->index();
    }
    
    public function byBrand($slug) {
        $brand = $this->brandModel->findBySlug($slug);
        if (!$brand) {
            http_response_code(404);
            return;
        }
        
        $_GET['brand'] = $brand['id'];
        $this->index();
    }
}
?>
