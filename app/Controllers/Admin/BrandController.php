<?php
class BrandController extends Controller {
    private $brandModel;
    public function __construct() { parent::__construct(); $this->brandModel = new BrandModel(); }
    
    public function index() {
        $this->requireAdmin();
        $this->view('admin.brands.index', ['title' => 'Quản lý thương hiệu - Admin', 'brands' => $this->brandModel->getAll()]);
    }
    
    public function create() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = ['name' => sanitize($this->getInput('name', '')), 'description' => sanitize($this->getInput('description', '')), 'website' => sanitize($this->getInput('website', '')), 'country' => sanitize($this->getInput('country', '')), 'display_order' => (int)$this->getInput('display_order', 0), 'status' => $this->getInput('status', 'active')];
            $brandId = $this->brandModel->create($data);
            if (!empty($_FILES['logo']['name'])) { $r = uploadImage($_FILES['logo'], 'brands'); if ($r['success']) $this->brandModel->updateLogo($brandId, $r['path']); }
            setFlash('success', 'Thêm thương hiệu thành công');
            $this->redirect(url('admin/brands'));
        }
        $this->view('admin.brands.create', ['title' => 'Thêm thương hiệu - Admin']);
    }
    
    public function edit($id) {
        $this->requireAdmin();
        $brand = $this->brandModel->findById($id);
        if (!$brand) { setFlash('error', 'Thương hiệu không tồn tại'); $this->redirect(url('admin/brands')); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = ['name' => sanitize($this->getInput('name', '')), 'description' => sanitize($this->getInput('description', '')), 'website' => sanitize($this->getInput('website', '')), 'country' => sanitize($this->getInput('country', '')), 'display_order' => (int)$this->getInput('display_order', 0), 'status' => $this->getInput('status', 'active')];
            $this->brandModel->update($id, $data);
            if (!empty($_FILES['logo']['name'])) { $r = uploadImage($_FILES['logo'], 'brands'); if ($r['success']) $this->brandModel->updateLogo($id, $r['path']); }
            setFlash('success', 'Cập nhật thương hiệu thành công');
            $this->redirect(url('admin/brands'));
        }
        $this->view('admin.brands.edit', ['title' => 'Sửa thương hiệu - Admin', 'brand' => $brand]);
    }
    
    public function delete($id) {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->brandModel->delete($id);
        setFlash('success', 'Đã xóa thương hiệu');
        $this->redirect(url('admin/brands'));
    }
}
?>
