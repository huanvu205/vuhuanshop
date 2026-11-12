<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container py-5">
    <div class="mx-auto" style="max-width: 820px;">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($policy['title']) ?></li>
            </ol>
        </nav>
        <article class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h1 class="h2 fw-bold mb-3"><?= e($policy['title']) ?></h1>
                <p class="lead text-muted mb-4"><?= e($policy['intro']) ?></p>
                <?php foreach ($policy['sections'] as $section): ?>
                    <section class="mb-4">
                        <h2 class="h5 fw-bold"><?= e($section['title']) ?></h2>
                        <p class="mb-0"><?= e($section['text']) ?></p>
                    </section>
                <?php endforeach; ?>
                <p class="mb-0">Hotline: <a href="tel:0336120405">0336.120.405</a> · Email: <a href="mailto:vuhuan123890@gmail.com">vuhuan123890@gmail.com</a></p>
            </div>
        </article>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
