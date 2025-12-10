<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="blog-container">
    <header class="search-header">
        <h1>Résultats de recherche</h1>
        <p class="search-query">pour "<strong><?= esc($query) ?></strong>"</p>
        <p class="article-count"><?= $totalArticles ?> résultat<?= $totalArticles > 1 ? 's' : '' ?></p>

        <form action="<?= base_url('blog/search') ?>" method="GET" class="blog-search-form inline">
            <div class="search-input-wrapper">
                <input type="search" name="q" value="<?= esc($query) ?>" placeholder="Nouvelle recherche..." required>
                <button type="submit" aria-label="Rechercher">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
            </div>
        </form>
    </header>

    <div class="blog-layout">
        <main class="blog-main">
            <?php if (empty($articles)): ?>
                <div class="no-articles">
                    <p>Aucun article ne correspond à votre recherche.</p>
                    <div class="search-suggestions">
                        <h3>Suggestions :</h3>
                        <ul>
                            <li>Vérifiez l'orthographe des mots-clés</li>
                            <li>Essayez des termes plus généraux</li>
                            <li>Essayez différents mots ayant le même sens</li>
                        </ul>
                    </div>
                    <a href="<?= base_url('blog') ?>" class="btn btn-primary">Voir tous les articles</a>
                </div>
            <?php else: ?>
                <div class="articles-grid">
                    <?php foreach ($articles as $article): ?>
                        <article class="article-card">
                            <div class="article-content">
                                <h2><a href="<?= base_url('blog/' . $article['slug']) ?>"><?= esc($article['title']) ?></a></h2>
                                <p class="article-excerpt">
                                    <?= esc($article['excerpt'] ?? substr(strip_tags($article['content']), 0, 150)) ?>...</p>
                                <footer class="article-footer">
                                    <div class="article-meta">
                                        <span
                                            class="author"><?= esc(trim(($article['first_name'] ?? '') . ' ' . ($article['last_name'] ?? '')) ?: 'Pilom') ?></span>
                                        <span class="separator">•</span>
                                        <time
                                            datetime="<?= $article['published_at'] ?>"><?= date('d M Y', strtotime($article['published_at'])) ?></time>
                                    </div>
                                    <a href="<?= base_url('blog/' . $article['slug']) ?>" class="read-more">Lire →</a>
                                </footer>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <aside class="blog-sidebar">
            <div class="sidebar-widget">
                <h3>Catégories</h3>
                <ul class="category-list">
                    <?php foreach ($categories as $cat): ?>
                        <li><a href="<?= base_url('blog/categorie/' . $cat['slug']) ?>"><?= esc($cat['name']) ?> <span
                                    class="count">(<?= $cat['article_count'] ?>)</span></a></li>
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
<?= $this->endSection() ?>