<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<article class="blog-article" itemscope itemtype="https://schema.org/BlogPosting">
    <!-- Breadcrumbs -->
    <nav class="breadcrumbs" aria-label="Fil d'Ariane">
        <ol itemscope itemtype="https://schema.org/BreadcrumbList">
            <?php foreach ($breadcrumbs as $i => $item): ?>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <?php if ($i < count($breadcrumbs) - 1): ?>
                        <a itemprop="item" href="<?= $item['url'] ?>">
                            <span itemprop="name"><?= esc($item['name']) ?></span>
                        </a>
                    <?php else: ?>
                        <span itemprop="name"><?= esc($item['name']) ?></span>
                    <?php endif; ?>
                    <meta itemprop="position" content="<?= $i + 1 ?>">
                </li>
                <?php if ($i < count($breadcrumbs) - 1): ?>
                    <li class="separator" aria-hidden="true">›</li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>

    <div class="article-layout">
        <!-- Main Article -->
        <div class="article-main">
            <!-- Header -->
            <header class="article-header">
                <!-- Categories -->
                <?php if (!empty($categories)): ?>
                    <div class="article-categories">
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?= base_url('blog/categorie/' . $cat['slug']) ?>" class="category-badge">
                                <?= esc($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h1 itemprop="headline"><?= esc($article['title']) ?></h1>

                <div class="article-meta">
                    <div class="author-info" itemprop="author" itemscope itemtype="https://schema.org/Person">
                        <?php if (!empty($article['author_avatar'])): ?>
                            <img src="<?= base_url($article['author_avatar']) ?>" alt="" class="author-avatar">
                        <?php else: ?>
                            <div class="author-avatar-placeholder">
                                <?= strtoupper(substr($article['first_name'] ?? 'P', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="author-details">
                            <span class="author-name" itemprop="name">
                                <?= esc(trim(($article['first_name'] ?? '') . ' ' . ($article['last_name'] ?? '')) ?: 'Pilom') ?>
                            </span>
                            <div class="article-date-info">
                                <time datetime="<?= $article['published_at'] ?>" itemprop="datePublished">
                                    <?= date('d F Y', strtotime($article['published_at'])) ?>
                                </time>
                                <span class="separator">•</span>
                                <span class="reading-time"><?= $article['reading_time'] ?> min de lecture</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Featured Image -->
            <?php if (!empty($article['featured_image_id'])): ?>
                <figure class="featured-image">
                    <img src="<?= base_url('uploads/blog/placeholder.jpg') ?>"
                        alt="<?= esc($article['featured_image_alt'] ?? $article['title']) ?>" itemprop="image">
                    <?php if (!empty($article['featured_image_alt'])): ?>
                        <figcaption><?= esc($article['featured_image_alt']) ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endif; ?>

            <!-- Content -->
            <div class="article-content prose" itemprop="articleBody">
                <?= $article['content'] ?>
            </div>

            <!-- Tags -->
            <?php if (!empty($tags)): ?>
                <div class="article-tags">
                    <span class="tags-label">Tags :</span>
                    <?php foreach ($tags as $tag): ?>
                        <a href="<?= base_url('blog/tag/' . $tag['slug']) ?>" class="tag" rel="tag">
                            #<?= esc($tag['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Social Share -->
            <div class="social-share">
                <span class="share-label">Partager :</span>
                <div class="share-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>"
                        target="_blank" rel="noopener noreferrer" class="share-btn facebook"
                        aria-label="Partager sur Facebook">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>&text=<?= urlencode($article['title']) ?>"
                        target="_blank" rel="noopener noreferrer" class="share-btn twitter"
                        aria-label="Partager sur Twitter">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(current_url()) ?>&title=<?= urlencode($article['title']) ?>"
                        target="_blank" rel="noopener noreferrer" class="share-btn linkedin"
                        aria-label="Partager sur LinkedIn">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                        </svg>
                    </a>
                    <button
                        onclick="navigator.clipboard.writeText('<?= current_url() ?>'); this.classList.add('copied');"
                        class="share-btn copy" aria-label="Copier le lien">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" />
                            <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Author Bio -->
            <div class="author-bio">
                <div class="author-bio-avatar">
                    <?php if (!empty($article['author_avatar'])): ?>
                        <img src="<?= base_url($article['author_avatar']) ?>" alt="">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <?= strtoupper(substr($article['first_name'] ?? 'P', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="author-bio-content">
                    <h4>À propos de l'auteur</h4>
                    <p class="author-name">
                        <?= esc(trim(($article['first_name'] ?? '') . ' ' . ($article['last_name'] ?? '')) ?: 'Équipe Pilom') ?>
                    </p>
                    <p class="author-description">Expert en gestion d'entreprise et outils de productivité pour les
                        professionnels indépendants.</p>
                </div>
            </div>

            <!-- Related Articles -->
            <?php if (!empty($relatedArticles)): ?>
                <section class="related-articles">
                    <h2>Articles similaires</h2>
                    <div class="related-grid">
                        <?php foreach ($relatedArticles as $related): ?>
                            <article class="related-card">
                                <h3>
                                    <a href="<?= base_url('blog/' . $related['slug']) ?>">
                                        <?= esc($related['title']) ?>
                                    </a>
                                </h3>
                                <p><?= esc(substr(strip_tags($related['content']), 0, 100)) ?>...</p>
                                <span class="reading-time"><?= $related['reading_time'] ?> min</span>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Comments Section -->
            <?php if ($article['allow_comments']): ?>
                <section class="comments-section" id="comments">
                    <h2>Commentaires (<?= $commentCount ?>)</h2>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Comment Form -->
                    <div class="comment-form-wrapper" id="comment-form">
                        <h3>Laissez un commentaire</h3>
                        <form action="<?= base_url('blog/' . $article['slug'] . '/comment') ?>" method="POST"
                            class="comment-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="form_load_time" value="<?= $formLoadTime ?>">
                            <input type="hidden" name="parent_id" id="parent_id" value="">
                            <input type="text" name="honeypot" style="display:none" tabindex="-1" autocomplete="off">

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="author_name">Nom *</label>
                                    <input type="text" id="author_name" name="author_name" required
                                        value="<?= old('author_name') ?>" maxlength="100">
                                </div>
                                <div class="form-group">
                                    <label for="author_email">Email * <small>(ne sera pas publié)</small></label>
                                    <input type="email" id="author_email" name="author_email" required
                                        value="<?= old('author_email') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="author_website">Site web <small>(optionnel)</small></label>
                                <input type="url" id="author_website" name="author_website"
                                    value="<?= old('author_website') ?>" placeholder="https://">
                            </div>

                            <div class="form-group">
                                <label for="content">Commentaire *</label>
                                <textarea id="content" name="content" rows="5" required minlength="10"
                                    maxlength="2000"><?= old('content') ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Publier le commentaire</button>
                            <p class="form-note">Les commentaires sont modérés avant publication.</p>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <?php if (!empty($comments)): ?>
                        <div class="comments-list">
                            <?php foreach ($comments as $comment): ?>
                                <?= view('blog/partials/comment', ['comment' => $comment, 'depth' => 0]) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($commentCount === 0): ?>
                        <p class="no-comments">Soyez le premier à commenter cet article !</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside class="article-sidebar">
            <!-- Table of Contents could go here -->

            <!-- Newsletter -->
            <div class="sidebar-widget newsletter-widget sticky">
                <h3>📬 Newsletter</h3>
                <p>Recevez nos derniers articles par email.</p>
                <form action="<?= base_url('blog/newsletter/subscribe') ?>" method="POST" class="newsletter-form">
                    <?= csrf_field() ?>
                    <input type="email" name="email" placeholder="Votre email" required>
                    <input type="text" name="honeypot" style="display:none" tabindex="-1">
                    <input type="hidden" name="source" value="article_sidebar">
                    <button type="submit">S'inscrire</button>
                </form>
            </div>

            <!-- Categories -->
            <div class="sidebar-widget">
                <h3>Catégories</h3>
                <ul class="category-list">
                    <?php foreach ($sidebarCategories as $category): ?>
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
        </aside>
    </div>
</article>

<!-- Schema.org -->
<?= $schema ?? '' ?>

<script>
    // Reply to comment
    function replyTo(commentId, authorName) {
        document.getElementById('parent_id').value = commentId;
        document.querySelector('.comment-form-wrapper h3').textContent = 'Répondre à ' + authorName;
        document.getElementById('comment-form').scrollIntoView({ behavior: 'smooth' });
        document.getElementById('content').focus();
    }

    // Cancel reply
    function cancelReply() {
        document.getElementById('parent_id').value = '';
        document.querySelector('.comment-form-wrapper h3').textContent = 'Laissez un commentaire';
    }
</script>

<?= $this->endSection() ?>