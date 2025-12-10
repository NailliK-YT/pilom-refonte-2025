<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="blog-container">
    <!-- Breadcrumbs -->
    <nav class="breadcrumbs" aria-label="Fil d'Ariane">
        <ol>
            <?php foreach ($breadcrumbs as $i => $item): ?>
                <li>
                    <?php if ($i < count($breadcrumbs) - 1): ?>
                        <a href="<?= $item['url'] ?>"><?= esc($item['name']) ?></a>
                    <?php else: ?>
                        <span><?= esc($item['name']) ?></span>
                    <?php endif; ?>
                </li>
                <?php if ($i < count($breadcrumbs) - 1): ?>
                    <li class="separator" aria-hidden="true">›</li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>

    <header class="archive-header">
        <h1><?= esc($category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?>
            <p class="archive-description"><?= esc($category['description']) ?></p>
        <?php endif; ?>
        <p class="article-count"><?= $totalArticles ?> article<?= $totalArticles > 1 ? 's' : '' ?></p>
    </header>

    <div class="blog-layout">
        <main class="blog-main">
            <?php if (empty($articles)): ?>
                <div class="no-articles">
                    <p>Aucun article dans cette catégorie pour le moment.</p>
                    <a href="<?= base_url('blog') ?>" class="btn btn-primary">Voir tous les articles</a>
                </div>
            <?php else: ?>
                <div class="articles-grid">
                    <?php foreach ($articles as $article): ?>
                        <article class="article-card">
                            <div class="article-content">
                                <h2>
                                    <a href="<?= base_url('blog/' . $article['slug']) ?>">
                                        <?= esc($article['title']) ?>
                                    </a>
                                </h2>
                                <p class="article-excerpt">
                                    <?= esc($article['excerpt'] ?? substr(strip_tags($article['content']), 0, 150)) ?>...
                                </p>
                                <footer class="article-footer">
                                    <div class="article-meta">
                                        <span
                                            class="author"><?= esc(trim(($article['first_name'] ?? '') . ' ' . ($article['last_name'] ?? '')) ?: 'Pilom') ?></span>
                                        <span class="separator">•</span>
                                        <time datetime="<?= $article['published_at'] ?>">
                                            <?= date('d M Y', strtotime($article['published_at'])) ?>
                                        </time>
                                    </div>
                                    <a href="<?= base_url('blog/' . $article['slug']) ?>" class="read-more">Lire →</a>
                                </footer>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="pagination" aria-label="Pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= base_url('blog/categorie/' . $category['slug'] . '?page=' . ($currentPage - 1)) ?>"
                                class="page-link prev">← Précédent</a>
                        <?php endif; ?>

                        <div class="page-numbers">
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <a href="<?= base_url('blog/categorie/' . $category['slug'] . '?page=' . $i) ?>"
                                    class="page-link <?= $i === $currentPage ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= base_url('blog/categorie/' . $category['slug'] . '?page=' . ($currentPage + 1)) ?>"
                                class="page-link next">Suivant →</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </main>

        <aside class="blog-sidebar">
            <div class="sidebar-widget">
                <h3>Catégories</h3>
                <ul class="category-list">
                    <?php foreach ($categories as $cat): ?>
                        <li class="<?= $cat['id'] === $category['id'] ? 'active' : '' ?>">
                            <a href="<?= base_url('blog/categorie/' . $cat['slug']) ?>">
                                <?= esc($cat['name']) ?>
                                <span class="count">(<?= $cat['article_count'] ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php if (!empty($popularTags)): ?>
                <div class="sidebar-widget">
                    <h3>Tags populaires</h3>
                    <div class="tags-cloud">
                        <?php foreach ($popularTags as $tag): ?>
                            <a href="<?= base_url('blog/tag/' . $tag['slug']) ?>" class="tag"><?= esc($tag['name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</div>

<?= $schema ?? '' ?>
<?= $this->endSection() ?>