<?php
class CategoryController extends Controller {
    private $categoryModel;
    public function __construct() { parent::__construct(); $this->categoryModel = new CategoryModel(); }
    
    public function index() {
        $this->requireAdmin();
        $search = sanitize($_GET['q'] ?? '');
        $this->view('admin.categories.index', [
            'title' => 'Quản lý danh mục - Admin',
            'categories' => $this->categoryModel->getAll(null),
            'search' => $search,
        ]);
    }
    
    public function create() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = ['name' => sanitize($this->getInput('name', '')), 'description' => sanitize($this->getInput('description', '')), 'parent_id' => $this->getInput('parent_id') ?: null, 'display_order' => (int)$this->getInput('display_order', 0), 'status' => $this->getInput('status', 'active')];
            if (!empty($_FILES['image']['name'])) { $r = uploadImage($_FILES['image'], 'categories'); if ($r['success']) $data['image'] = $r['path']; }
            $this->categoryModel->create($data);
            setFlash('success', 'Thêm danh mục thành công');
            $this->redirect(url('admin/categories'));
        }
        $this->view('admin.categories.create', ['title' => 'Thêm danh mục - Admin', 'categories' => $this->categoryModel->getActive()]);
    }
    
    public function edit($id) {
        $this->requireAdmin();
        $category = $this->categoryModel->findById($id);
        if (!$category) { setFlash('error', 'Danh mục không tồn tại'); $this->redirect(url('admin/categories')); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = ['name' => sanitize($this->getInput('name', '')), 'description' => sanitize($this->getInput('description', '')), 'parent_id' => $this->getInput('parent_id') ?: null, 'display_order' => (int)$this->getInput('display_order', 0), 'status' => $this->getInput('status', 'active')];
            // Xử lý upload ảnh danh mục mới
            if (!empty($_FILES['image']['name'])) {
                $r = uploadImage($_FILES['image'], 'categories');
                if ($r['success']) {
                    $data['image'] = $r['path'];
                } else {
                    setFlash('error', $r['error'] ?? 'Lỗi upload ảnh danh mục');
                }
            }
            $this->categoryModel->update($id, $data);
            setFlash('success', 'Cập nhật danh mục thành công');
            $this->redirect(url('admin/categories'));
        }
        $this->view('admin.categories.edit', ['title' => 'Sửa danh mục - Admin', 'category' => $category, 'categories' => $this->categoryModel->getActive()]);
    }
    
    public function delete($id) {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->categoryModel->delete($id);
        setFlash('success', 'Đã xóa danh mục');
        $this->redirect(url('admin/categories'));
    }
}
?>
