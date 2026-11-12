<?php
class BannerController extends Controller {
    private $bannerModel;
    public function __construct() { parent::__construct(); $this->bannerModel = new BannerModel(); }
    
    public function index() {
        $this->requireAdmin();
        $this->view('admin.banners.index', ['title' => 'Quản lý banner - Admin', 'banners' => $this->bannerModel->getAll()]);
    }
    
    public function create() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = ['title' => sanitize($this->getInput('title', '')), 'link' => sanitize($this->getInput('link', '')), 'position' => $this->getInput('position', 'home_slider'), 'display_order' => (int)$this->getInput('display_order', 0), 'status' => $this->getInput('status', 'active'), 'start_date' => $this->getInput('start_date') ?: null, 'end_date' => $this->getInput('end_date') ?: null, 'image' => ''];
            if (!empty($_FILES['image']['name'])) {
                $upload = uploadImage($_FILES['image'], 'banners');
                if (!$upload['success']) {
                    setFlash('error', $upload['error'] ?? 'Không thể tải ảnh banner lên');
                    $this->redirect(url('admin/banners/create'));
                    return;
                }
                $data['image'] = $upload['path'];
            }
            $this->bannerModel->create($data);
            setFlash('success', 'Thêm banner thành công');
            $this->redirect(url('admin/banners'));
        }
        $this->view('admin.banners.create', ['title' => 'Thêm banner - Admin']);
    }
    
    public function edit($id) {
        $this->requireAdmin();
        $banner = $this->bannerModel->findById($id);
        if (!$banner) { setFlash('error', 'Banner không tồn tại'); $this->redirect(url('admin/banners')); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $uploadedImage = null;
            if (!empty($_FILES['image']['name'])) {
                $upload = uploadImage($_FILES['image'], 'banners');
                if (!$upload['success']) {
                    setFlash('error', $upload['error'] ?? 'Không thể tải ảnh banner lên');
                    $this->redirect(url('admin/banners/edit/' . $id));
                    return;
                }
                $uploadedImage = $upload['path'];
            }
            $data = ['title' => sanitize($this->getInput('title', '')), 'link' => sanitize($this->getInput('link', '')), 'position' => $this->getInput('position', 'home_slider'), 'display_order' => (int)$this->getInput('display_order', 0), 'status' => $this->getInput('status', 'active'), 'start_date' => $this->getInput('start_date') ?: null, 'end_date' => $this->getInput('end_date') ?: null];
            $this->bannerModel->update($id, $data);
            if ($uploadedImage !== null) {
                $this->bannerModel->updateImage($id, $uploadedImage);
            }
            setFlash('success', 'Cập nhật banner thành công');
            $this->redirect(url('admin/banners'));
        }
        $this->view('admin.banners.edit', ['title' => 'Sửa banner - Admin', 'banner' => $banner]);
    }
    
    public function delete($id) {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->bannerModel->delete($id);
        setFlash('success', 'Đã xóa banner');
        $this->redirect(url('admin/banners'));
    }
}
?>
