<?php
/**
 * ProductAdminController - Quản lý sản phẩm (Admin)
 */
class ProductAdminController extends Controller {
    private $productModel;
    private $categoryModel;
    private $brandModel;
    private $variantUploadError = '';
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->brandModel = new BrandModel();
    }
    
    public function index() {
        $this->requireStaff();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['q'] ?? '');
        $category = sanitize($_GET['category'] ?? '');
        $brand = sanitize($_GET['brand'] ?? '');
        $status = sanitize($_GET['status'] ?? '');
        
        $products = $this->productModel->getAllAdmin($page, 20, $search, $category, $brand, $status);
        $total = $this->productModel->countAdmin($search, $category, $brand, $status);
        $totalPages = ceil($total / 20);
        
        $this->view('admin.products.index', [
            'title' => 'Quản lý sản phẩm - Admin',
            'products' => $products,
            'categories' => $this->categoryModel->getActive(),
            'brands' => $this->brandModel->getActive(),
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'filterCategory' => $category,
            'filterBrand' => $brand,
            'filterStatus' => $status,
        ]);
    }
    
    public function create() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = $this->getProductData();
            $data['sku_variants'] = $this->getSkuVariantRows();
            $errors = $this->validateProduct($data);
            $errors = array_merge($errors, $this->validateSkuVariantRows($data['sku_variants']));
            
            if (!empty($errors)) {
                $this->view('admin.products.create', [
                    'title' => 'Thêm sản phẩm - Admin',
                    'categories' => $this->categoryModel->getActive(),
                    'brands' => $this->brandModel->getActive(),
                    'errors' => $errors,
                    'old' => $data,
                    'skuVariants' => $data['sku_variants'],
                ]);
                return;
            }

            $data['variants_json'] = $this->withProductColorVariants($data['variants_json'] ?? null);
            if ($this->variantUploadError !== '') {
                setFlash('error', $this->variantUploadError);
                $this->redirect(url('admin/products/create'));
                return;
            }
            
            // Upload ảnh chính
            if (!empty($_FILES['main_image']['name'])) {
                $result = uploadImage($_FILES['main_image'], 'products');
                if ($result['success']) $data['main_image'] = $result['path'];
            }
            
            $productId = $this->productModel->create($data);
            
            if ($productId) {
                if (!$this->productModel->saveVariants($productId, $data['sku_variants'])) {
                    setFlash('error', 'Không lưu được danh sách SKU. Kiểm tra mã SKU có bị trùng không.');
                    $this->redirect(url('admin/products/edit/' . $productId));
                    return;
                }
                // Upload ảnh phụ
                $subImageErrors = [];
                if (!empty($_FILES['sub_images']['name'][0])) {
                    foreach ($_FILES['sub_images']['tmp_name'] as $key => $tmpName) {
                        if ($_FILES['sub_images']['error'][$key] === 0) {
                            $file = [
                                'name' => $_FILES['sub_images']['name'][$key],
                                'type' => $_FILES['sub_images']['type'][$key],
                                'tmp_name' => $tmpName,
                                'error' => $_FILES['sub_images']['error'][$key],
                                'size' => $_FILES['sub_images']['size'][$key],
                            ];
                            $result = uploadImage($file, 'products');
                            if ($result['success']) {
                                if (!$this->productModel->addImage($productId, $result['path'])) $subImageErrors[] = $_FILES['sub_images']['name'][$key];
                            } else $subImageErrors[] = $_FILES['sub_images']['name'][$key] . ': ' . ($result['error'] ?? 'lỗi tải ảnh');
                        } elseif ($_FILES['sub_images']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                            $subImageErrors[] = $_FILES['sub_images']['name'][$key] . ': lỗi tải tệp lên';
                        }
                    }
                }
                
                // Thêm thông số kỹ thuật
                if (!empty($_POST['spec_name'])) {
                    foreach ($_POST['spec_name'] as $i => $specName) {
                        if ($specName && isset($_POST['spec_value'][$i])) {
                            $this->productModel->addSpec($productId, $specName, $_POST['spec_value'][$i], $i);
                        }
                    }
                }
                
                if ($subImageErrors) setFlash('error', 'Sản phẩm đã tạo nhưng ảnh phụ chưa tải được: ' . implode(', ', $subImageErrors));
                else setFlash('success', 'Thêm sản phẩm thành công!');
                $this->redirect(url('admin/products'));
            } else {
                setFlash('error', 'Thêm sản phẩm thất bại');
            }
        }
        
        $this->view('admin.products.create', [
            'title' => 'Thêm sản phẩm - Admin',
            'categories' => $this->categoryModel->getActive(),
            'brands' => $this->brandModel->getActive(),
        ]);
    }
    
    public function edit($id) {
        $this->requireAdmin();
        $product = $this->productModel->findById($id);
        
        if (!$product) {
            setFlash('error', 'Sản phẩm không tồn tại');
            $this->redirect(url('admin/products'));
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = $this->getProductData();
            $data['sku_variants'] = $this->getSkuVariantRows();
            $errors = $this->validateProduct($data);
            $errors = array_merge($errors, $this->validateSkuVariantRows($data['sku_variants']));
            
            if (!empty($errors)) {
                $this->view('admin.products.edit', [
                    'title' => 'Sửa sản phẩm - Admin',
                    'product' => $product,
                    'images' => $this->productModel->getImages($id),
                    'specs' => $this->productModel->getSpecs($id),
                    'categories' => $this->categoryModel->getActive(),
                    'brands' => $this->brandModel->getActive(),
                    'errors' => $errors,
                    'skuVariants' => $data['sku_variants'],
                ]);
                return;
            }

            $data['variants_json'] = $this->withProductColorVariants(
                $data['variants_json'] ?? null,
                $product['variants_json'] ?? null
            );
            if ($this->variantUploadError !== '') {
                setFlash('error', $this->variantUploadError);
                $this->redirect(url('admin/products/edit/' . $id));
                return;
            }
            
            // Upload ảnh mới
            if (!empty($_FILES['main_image']['name'])) {
                $result = uploadImage($_FILES['main_image'], 'products');
                if ($result['success']) $data['main_image'] = $result['path'];
            }
            
            $this->productModel->update($id, $data);
            if (!$this->productModel->saveVariants($id, $data['sku_variants'])) {
                setFlash('error', 'Không lưu được danh sách SKU. Kiểm tra mã SKU có bị trùng không.');
                $this->redirect(url('admin/products/edit/' . $id));
                return;
            }
            
            // Upload ảnh phụ
            $subImageErrors = [];
            if (!empty($_FILES['sub_images']['name'][0])) {
                foreach ($_FILES['sub_images']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['sub_images']['error'][$key] === 0) {
                        $file = ['name' => $_FILES['sub_images']['name'][$key], 'type' => $_FILES['sub_images']['type'][$key], 'tmp_name' => $tmpName, 'error' => $_FILES['sub_images']['error'][$key], 'size' => $_FILES['sub_images']['size'][$key]];
                        $result = uploadImage($file, 'products');
                        if ($result['success']) {
                            if (!$this->productModel->addImage($id, $result['path'])) $subImageErrors[] = $_FILES['sub_images']['name'][$key];
                        } else $subImageErrors[] = $_FILES['sub_images']['name'][$key] . ': ' . ($result['error'] ?? 'lỗi tải ảnh');
                    } elseif ($_FILES['sub_images']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                        $subImageErrors[] = $_FILES['sub_images']['name'][$key] . ': lỗi tải tệp lên';
                    }
                }
            }
            
            // Cập nhật thông số kỹ thuật
            $this->productModel->deleteSpecs($id);
            if (!empty($_POST['spec_name'])) {
                foreach ($_POST['spec_name'] as $i => $specName) {
                    if ($specName && isset($_POST['spec_value'][$i])) {
                        $this->productModel->addSpec($id, $specName, $_POST['spec_value'][$i], $i);
                    }
                }
            }
            
            if ($subImageErrors) setFlash('error', 'Sản phẩm đã cập nhật nhưng ảnh phụ chưa tải được: ' . implode(', ', $subImageErrors));
            else setFlash('success', 'Cập nhật sản phẩm thành công!');
            $this->redirect(url('admin/products/edit/' . $id));
        }
        
        $this->view('admin.products.edit', [
            'title' => 'Sửa sản phẩm: ' . e($product['name']) . ' - Admin',
            'product' => $product,
            'images' => $this->productModel->getImages($id),
            'specs' => $this->productModel->getSpecs($id),
            'categories' => $this->categoryModel->getActive(),
            'brands' => $this->brandModel->getActive(),
            'skuVariants' => $this->productModel->getVariants($id, false),
        ]);
    }
    
    public function delete($id) {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->productModel->delete($id);
        setFlash('success', 'Đã xóa sản phẩm');
        $this->redirect(url('admin/products'));
    }
    
    public function deleteImage($id) {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->json(['success'=>false], 405); return; }
        $this->validateCsrf();
        $this->productModel->deleteImage($id);
        $this->json(['success' => true]);
    }
    
    private function getProductData() {
        // Xử lý phiên bản dung lượng
        $capacities = [];
        if (!empty($_POST['variant_capacity_name'])) {
            foreach ($_POST['variant_capacity_name'] as $i => $capName) {
                $capName = trim($capName);
                if ($capName !== '') {
                    $diff = isset($_POST['variant_capacity_diff'][$i]) ? (float)$_POST['variant_capacity_diff'][$i] : 0;
                    $capacities[] = [
                        'name' => $capName,
                        'diff' => $diff
                    ];
                }
            }
        }
        $variantsJson = null;
        if (!empty($capacities)) {
            $variantsJson = json_encode(['capacities' => $capacities], JSON_UNESCAPED_UNICODE);
        }

        return [
            'product_code' => sanitize($this->getInput('product_code', '')),
            'name' => sanitize($this->getInput('name', '')),
            'category_id' => (int)$this->getInput('category_id', 0),
            'brand_id' => (int)$this->getInput('brand_id', 0),
            'price_cost' => (float)$this->getInput('price_cost', 0),
            'price_sale' => (float)$this->getInput('price_sale', 0),
            'price_discount' => $this->getInput('price_discount') ? (float)$this->getInput('price_discount') : null,
            'discount_percent' => (int)$this->getInput('discount_percent', 0),
            'description' => $this->getInput('description', ''),
            'short_description' => sanitize($this->getInput('short_description', '')),
            'stock_quantity' => (int)$this->getInput('stock_quantity', 0),
            'status' => sanitize($this->getInput('status', 'active')),
            'is_featured' => $this->getInput('is_featured') ? 1 : 0,
            'is_hot_deal' => $this->getInput('is_hot_deal') ? 1 : 0,
            'is_best_selling' => $this->getInput('is_best_selling') ? 1 : 0,
            'is_newest' => $this->getInput('is_newest') ? 1 : 0,
            'variants_json' => $variantsJson,
        ];
    }

    private function getSkuVariantRows() {
        $rows = [];
        $ids = $_POST['sku_variant_id'] ?? [];
        $skus = $_POST['sku_variant_sku'] ?? [];
        $names = $_POST['sku_variant_name'] ?? [];
        $attributes = $_POST['sku_variant_attributes'] ?? [];
        $adjustments = $_POST['sku_variant_adjustment'] ?? [];
        $stocks = $_POST['sku_variant_stock'] ?? [];
        foreach ($skus as $i => $sku) {
            $rows[] = [
                'id' => (int)($ids[$i] ?? 0), 'sku' => sanitize($sku),
                'name' => sanitize($names[$i] ?? ''), 'attributes_json' => trim((string)($attributes[$i] ?? '')),
                'price_adjustment' => (float)($adjustments[$i] ?? 0), 'stock_quantity' => (int)($stocks[$i] ?? 0),
            ];
        }
        return $rows;
    }

    private function validateSkuVariantRows(array $rows) {
        $errors = []; $seen = [];
        foreach ($rows as $index => $row) {
            if ($row['sku'] === '' && $row['name'] === '') continue;
            if ($row['sku'] === '' || $row['name'] === '') $errors[] = 'Mỗi biến thể cần có cả SKU và tên.';
            if (!preg_match('/^[A-Za-z0-9._-]{2,80}$/', $row['sku'])) $errors[] = 'SKU chỉ được gồm chữ, số, dấu chấm, gạch dưới hoặc gạch ngang.';
            $normalizedSku = strtolower($row['sku']);
            if (isset($seen[$normalizedSku])) $errors[] = 'SKU bị lặp trong biểu mẫu.';
            $seen[$normalizedSku] = true;
            if ($row['stock_quantity'] < 0) $errors[] = 'Tồn kho SKU không được âm.';
            if ($row['attributes_json'] !== '' && !is_array(json_decode($row['attributes_json'], true))) $errors[] = 'Thuộc tính SKU phải là JSON hợp lệ.';
        }
        return array_values(array_unique($errors));
    }

    private function withProductColorVariants($variantsJson, $existingVariantsJson = null) {
        $variants = json_decode($variantsJson ?: '{}', true);
        if (!is_array($variants)) $variants = [];

        $existing = json_decode($existingVariantsJson ?: '{}', true);
        $allowedExistingImages = [];
        foreach (($existing['colors'] ?? []) as $oldColor) {
            if (!empty($oldColor['image'])) $allowedExistingImages[] = $oldColor['image'];
        }

        $colors = [];
        $names = $_POST['variant_color_name'] ?? [];
        $hexes = $_POST['variant_color_hex'] ?? [];
        $existingImages = $_POST['variant_color_existing_image'] ?? [];
        foreach ($names as $index => $rawName) {
            $name = trim((string)$rawName);
            if ($name === '') continue;

            $image = $existingImages[$index] ?? '';
            if (!in_array($image, $allowedExistingImages, true)) $image = '';

            $uploadError = $_FILES['variant_color_image']['error'][$index] ?? UPLOAD_ERR_NO_FILE;
            if ($uploadError !== UPLOAD_ERR_NO_FILE) {
                $file = [
                    'name' => $_FILES['variant_color_image']['name'][$index] ?? '',
                    'type' => $_FILES['variant_color_image']['type'][$index] ?? '',
                    'tmp_name' => $_FILES['variant_color_image']['tmp_name'][$index] ?? '',
                    'error' => $uploadError,
                    'size' => $_FILES['variant_color_image']['size'][$index] ?? 0,
                ];
                $upload = uploadImage($file, 'products');
                if (!empty($upload['success'])) $image = $upload['path'];
                else $this->variantUploadError = $upload['error'] ?? 'Không tải được ảnh màu lên.';
            }

            if ($image === '') {
                $this->variantUploadError = $this->variantUploadError ?: 'Vui lòng chọn ảnh cho màu: ' . $name;
                continue;
            }
            $hex = $hexes[$index] ?? '#888888';
            if (!preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) $hex = '#888888';
            $colors[] = ['name' => $name, 'hex' => $hex, 'image' => $image];
        }

        $variants['colors'] = $colors;
        return json_encode($variants, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    
    private function validateProduct($data) {
        $errors = [];
        if (empty($data['product_code'])) $errors[] = 'Mã sản phẩm không được để trống';
        if (empty($data['name'])) $errors[] = 'Tên sản phẩm không được để trống';
        if (!$data['category_id']) $errors[] = 'Vui lòng chọn danh mục';
        if (!$data['brand_id']) $errors[] = 'Vui lòng chọn thương hiệu';
        if ($data['price_sale'] <= 0) $errors[] = 'Giá bán phải lớn hơn 0';
        return $errors;
    }
}
?>
