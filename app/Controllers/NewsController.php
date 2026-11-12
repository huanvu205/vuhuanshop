<?php
class NewsController extends Controller {
    private $newsModel;
    
    public function __construct() {
        parent::__construct();
        $this->newsModel = new NewsModel();
    }
    
    public function index() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $category = sanitize($_GET['category'] ?? '');
        $total = $this->newsModel->countPublished($category);
        $perPage = 9;
        $totalPages = ceil($total / $perPage);
        
        $this->view('news.index', [
            'title' => 'Tin tức công nghệ - ' . APP_NAME,
            'newsList' => $this->newsModel->getPublished($page, $perPage, $category),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'category' => $category,
        ]);
    }
    
    public function detail($slug) {
        $news = $this->newsModel->findBySlug($slug);
        
        if (!$news) {
            http_response_code(404);
            $this->view('errors.404', ['title' => '404 - Không tìm thấy']);
            return;
        }
        
        $this->newsModel->incrementView($news['id']);
        $recentNews = $this->newsModel->getRecent(5);
        
        $this->view('news.detail', [
            'title' => e($news['title']) . ' - ' . APP_NAME,
            'news' => $news,
            'recentNews' => $recentNews,
        ]);
    }
}
?>
