<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="blog-container">
    <!-- Blog Header -->
    <header class="blog-header">
        <div class="blog-header-content">
            <h1>Blog Pilom</h1>
            <p class="blog-tagline">Conseils et astuces pour les entrepreneurs, artisans et indépendants</p>
            
            <!-- Search -->
            <form action="<?= base_url('blog/search') ?>" method="GET" class="blog-search-form">
                <div class="search-input-wrapper">
                    <input type="search" name="q" placeholder="Rechercher un article..." aria-label="Rechercher">
                    <button type="submit" aria-label="Lancer la recherche">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </header>

    <div class="blog-layout">
        <!-- Main Content -->
        <main class="blog-main">
            <?php if (!empty($featuredArticles)): ?>
            <!-- Featured Articles -->
            <section class="featured-articles">
                <h2 class="section-title">Articles à la une</h2>
                <div class="featured-grid">
                    <?php foreach ($featuredArticles as $featured): ?>
                    <article class="featured-card">
                        <?php if (!empty($featured['featured_image_id'])): ?>
                        <div class="featured-image">
                            <img src="<?= base_url('uploads/blog/placeholder.jpg') ?>" 
                                 alt="<?= esc($featured['featured_image_alt'] ?? $featured['title']) ?>"
                                 loading="lazy">
                        </div>
                        <?php endif; ?>
                        <div class="featured-content">
                            <span class="featured-badge">À la une</span>
                            <h3><a href="<?= base_url('blog/' . $featured['slug']) ?>"><?= esc($featured['title']) ?></a></h3>
                            <p><?= esc($featured['excerpt'] ?? substr(strip_tags($featured['content']), 0, 120)) ?>...</p>
                            <div class="featured-meta">
                                <span class="reading-time"><?= $featured['reading_time'] ?> min de lecture</span>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- Latest Articles -->
            <section class="articles-list">
                <h2 class="section-title">Derniers articles</h2>
                
                <?php if (empty($articles)): ?>
                <div class="no-articles">
                    <p>Aucun article pour le moment. Revenez bientôt !</p>
                </div>
                <?php else: ?>
                <div class="articles-grid">
                    <?php foreach ($articles as $article): ?>
                    <article class="article-card" itemscope itemtype="https://schema.org/BlogPosting">
                        <?php if (!empty($article['featured_image_id'])): ?>
                        <div class="article-image">
                            <a href="<?= base_url('blog/' . $article['slug']) ?>">
                                <img src="<?= base_url('uploads/blog/placeholder.jpg') ?>" 
                                     alt="<?= esc($article['featured_image_alt'] ?? $article['title']) ?>"
                                     loading="lazy"
                                     itemprop="image">
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <div class="article-content">
                            <!-- Categories -->
                            <?php if (!empty($article['categories'])): ?>
                            <div class="article-categories">
                                <?php foreach (array_slice($article['categories'], 0, 2) as $cat): ?>
                                <a href="<?= base_url('blog/categorie/' . $cat['slug']) ?>" class="category-tag">
                                    <?= esc($cat['name']) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            
                            <h3 itemprop="headline">
                                <a href="<?= base_url('blog/' . $article['slug']) ?>" itemprop="url">
                                    <?= esc($article['title']) ?>
                                </a>
                            </h3>
                            
                            <p class="article-excerpt" itemprop="description">
                                <?= esc($article['excerpt'] ?? substr(strip_tags($article['content']), 0, 150)) ?>...
                            </p>
                            
                            <footer class="article-footer">
                                <div class="article-meta">
                                    <span class="author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                        <span itemprop="name"><?= esc(trim(($article['first_name'] ?? '') . ' ' . ($article['last_name'] ?? '')) ?: 'Pilom') ?></span>
                                    </span>
                                    <span class="separator">•</span>
                                    <time datetime="<?= $article['published_at'] ?>" itemprop="datePublished">
                                        <?= date('d M Y', strtotime($article['published_at'])) ?>
                                    </time>
                                    <span class="separator">•</span>
                                    <span class="reading-time"><?= $article['reading_time'] ?> min</span>
                                </div>
                                <a href="<?= base_url('blog/' . $article['slug']) ?>" class="read-more" aria-label="Lire <?= esc($article['title']) ?>">
                                    Lire →
                                </a>
                            </footer>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav class="pagination" aria-label="Pagination des articles">
                    <?php if ($currentPage > 1): ?>
                    <a href="<?= base_url('blog?page=' . ($currentPage - 1)) ?>" class="page-link prev">
                        ← Précédent
                    </a>
                    <?php endif; ?>
                    
                    <div class="page-numbers">
                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <a href="<?= base_url('blog?page=' . $i) ?>" 
                           class="page-link <?= $i === $currentPage ? 'active' : '' ?>"
                           <?= $i === $currentPage ? 'aria-current="page"' : '' ?>>
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>
                    </div>
                    
                    <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= base_url('blog?page=' . ($currentPage + 1)) ?>" class="page-link next">
                        Suivant →
                    </a>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>
                <?php endif; ?>
            </section>
        </main>

        <!-- Sidebar -->
        <aside class="blog-sidebar">
            <!-- Newsletter -->
            <div class="sidebar-widget newsletter-widget">
                <h3>📬 Newsletter</h3>
                <p>Recevez nos meilleurs conseils directement dans votre boîte mail.</p>
                <form action="<?= base_url('blog/newsletter/subscribe') ?>" method="POST" class="newsletter-form">
                    <?= csrf_field() ?>
                    <input type="email" name="email" placeholder="Votre email" required>
                    <input type="text" name="honeypot" style="display:none" tabindex="-1">
                    <input type="hidden" name="source" value="blog_sidebar">
                    <button type="submit">S'inscrire</button>
                </form>
            </div>

            <!-- Categories -->
            <div class="sidebar-widget">
                <h3>Catégories</h3>
                <ul class="category-list">
                    <?php foreach ($categories as $category): ?>
                    <li>
                        <a href="<?= base_url('blog/categorie/' . $category['slug']) ?>">
                            <?= esc($category['name']) ?>
                            <span class="count">(<?= $category['article_count'] ?>)</span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Popular Tags -->
            <?php if (!empty($popularTags)): ?>
            <div class="sidebar-widget">
                <h3>Tags populaires</h3>
                <div class="tags-cloud">
                    <?php foreach ($popularTags as $tag): ?>
                    <a href="<?= base_url('blog/tag/' . $tag['slug']) ?>" class="tag">
                        <?= esc($tag['name']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- RSS Feed -->
            <div class="sidebar-widget">
                <h3>Suivez-nous</h3>
                <a href="<?= base_url('blog/feed') ?>" class="rss-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="6.18" cy="17.82" r="2.18"/>
                        <path d="M4 4.44v2.83c7.03 0 12.73 5.7 12.73 12.73h2.83c0-8.59-6.97-15.56-15.56-15.56zm0 5.66v2.83c3.9 0 7.07 3.17 7.07 7.07h2.83c0-5.47-4.43-9.9-9.9-9.9z"/>
                    </svg>
                    Flux RSS
                </a>
            </div>
        </aside>
    </div>
</div>

<?= $this->endSection() ?>
