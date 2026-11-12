<?php
class NewsAdminController extends Controller {
    private $newsModel;
    public function __construct() { parent::__construct(); $this->newsModel = new NewsModel(); }
    
    public function index() {
        $this->requireStaff();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = sanitize($_GET['q'] ?? '');
        $status = sanitize($_GET['status'] ?? '');
        $total = $this->newsModel->count($search, $status);
        $this->view('admin.news.index', ['title' => 'Quản lý tin tức - Admin', 'newsList' => $this->newsModel->getAll($page, 20, $search, $status), 'search' => $search, 'filterStatus' => $status, 'currentPage' => $page, 'totalPages' => ceil($total / 20), 'total' => $total]);
    }
    
    public function create() {
        $this->requireStaff();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = ['title' => sanitize($this->getInput('title', '')), 'content' => $this->getInput('content', ''), 'excerpt' => sanitize($this->getInput('excerpt', '')), 'category' => sanitize($this->getInput('category', 'general')), 'status' => $this->getInput('status', 'draft'), 'author_id' => $this->getUserId()];
            if (empty($_FILES['featured_image']['name'])) {
                setFlash('error', 'Vui lòng chọn ảnh đại diện cho bài viết');
                $this->redirect(url('admin/news/create'));
                return;
            }
            $upload = uploadImage($_FILES['featured_image'], 'news');
            if (!$upload['success']) {
                setFlash('error', $upload['error'] ?? 'Không thể tải ảnh bài viết lên');
                $this->redirect(url('admin/news/create'));
                return;
            }
            $data['featured_image'] = $upload['path'];
            $this->newsModel->create($data);
            setFlash('success', 'Thêm bài viết thành công');
            $this->redirect(url('admin/news'));
        }
        $this->view('admin.news.create', ['title' => 'Thêm bài viết - Admin']);
    }
    
    public function edit($id) {
        $this->requireStaff();
        $news = $this->newsModel->findById($id);
        if (!$news) { setFlash('error', 'Bài viết không tồn tại'); $this->redirect(url('admin/news')); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $data = ['title' => sanitize($this->getInput('title', '')), 'content' => $this->getInput('content', ''), 'excerpt' => sanitize($this->getInput('excerpt', '')), 'category' => sanitize($this->getInput('category', 'general')), 'status' => $this->getInput('status', 'draft')];
            $uploadedImage = null;
            if (!empty($_FILES['featured_image']['name'])) {
                $upload = uploadImage($_FILES['featured_image'], 'news');
                if (!$upload['success']) {
                    setFlash('error', $upload['error'] ?? 'Không thể tải ảnh bài viết lên');
                    $this->redirect(url('admin/news/edit/' . $id));
                    return;
                }
                $uploadedImage = $upload['path'];
            } elseif (empty($news['featured_image'])) {
                setFlash('error', 'Bài viết chưa có ảnh đại diện. Vui lòng chọn ảnh trước khi lưu');
                $this->redirect(url('admin/news/edit/' . $id));
                return;
            }
            $data['featured_image'] = $uploadedImage ?? $news['featured_image'];
            $this->newsModel->update($id, $data);
            setFlash('success', 'Cập nhật bài viết thành công');
            $this->redirect(url('admin/news'));
        }
        $this->view('admin.news.edit', ['title' => 'Sửa bài viết - Admin', 'news' => $news]);
    }
    
    public function delete($id) {
        $this->requireStaff();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        $this->validateCsrf();
        $this->newsModel->delete($id);
        setFlash('success', 'Đã xóa bài viết');
        $this->redirect(url('admin/news'));
    }
}
?>
