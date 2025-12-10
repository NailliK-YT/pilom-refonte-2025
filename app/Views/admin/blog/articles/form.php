<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="admin-blog-form">
    <form action="<?= $isEdit ? base_url('admin/blog/update/' . $article['id']) : base_url('admin/blog/store') ?>"
        method="POST" id="article-form">
        <?= csrf_field() ?>

        <div class="page-header">
            <div class="header-left">
                <a href="<?= base_url('admin/blog') ?>" class="back-link">← Retour</a>
                <h1><?= $isEdit ? 'Modifier l\'article' : 'Nouvel Article' ?></h1>
            </div>
            <div class="header-actions">
                <button type="submit" name="status" value="draft" class="btn btn-outline">Enregistrer brouillon</button>
                <button type="submit" name="status" value="published" class="btn btn-primary">Publier</button>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-layout">
            <!-- Main Content -->
            <div class="form-main">
                <!-- Title -->
                <div class="form-group">
                    <label for="title">Titre *</label>
                    <input type="text" id="title" name="title" required
                        value="<?= esc($article['title'] ?? old('title')) ?>" placeholder="Titre de l'article"
                        class="form-control large">
                </div>

                <!-- Slug -->
                <div class="form-group">
                    <label for="slug">URL (slug)</label>
                    <div class="input-group">
                        <span class="input-prefix"><?= base_url('blog/') ?></span>
                        <input type="text" id="slug" name="slug" value="<?= esc($article['slug'] ?? old('slug')) ?>"
                            placeholder="url-de-l-article">
                    </div>
                    <small class="form-text">Laissez vide pour générer automatiquement</small>
                </div>

                <!-- Content (WYSIWYG) -->
                <div class="form-group">
                    <label for="content">Contenu *</label>
                    <textarea id="content" name="content" rows="20" required
                        class="form-control wysiwyg"><?= $article['content'] ?? old('content') ?></textarea>
                </div>

                <!-- Excerpt -->
                <div class="form-group">
                    <label for="excerpt">Extrait</label>
                    <textarea id="excerpt" name="excerpt" rows="3"
                        placeholder="Résumé de l'article (affiché dans les listes)"
                        class="form-control"><?= esc($article['excerpt'] ?? old('excerpt')) ?></textarea>
                    <small class="form-text">Si vide, un extrait sera généré automatiquement</small>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="form-sidebar">
                <!-- Status -->
                <div class="sidebar-card">
                    <h3>Publication</h3>
                    <div class="form-group">
                        <label for="status">Statut</label>
                        <select id="status" name="status" class="form-control">
                            <option value="draft" <?= ($article['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>
                                Brouillon</option>
                            <option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>
                                Publié</option>
                            <option value="scheduled" <?= ($article['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>
                                Programmé</option>
                        </select>
                    </div>
                    <div class="form-group" id="schedule-date"
                        style="display: <?= ($article['status'] ?? '') === 'scheduled' ? 'block' : 'none' ?>">
                        <label for="published_at">Date de publication</label>
                        <input type="datetime-local" id="published_at" name="published_at" class="form-control"
                            value="<?= isset($article['published_at']) ? date('Y-m-d\TH:i', strtotime($article['published_at'])) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= ($article['is_featured'] ?? false) ? 'checked' : '' ?>>
                            Article à la une
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="allow_comments" value="1" <?= ($article['allow_comments'] ?? true) ? 'checked' : '' ?>>
                            Autoriser les commentaires
                        </label>
                    </div>
                </div>

                <!-- Categories -->
                <div class="sidebar-card">
                    <h3>Catégories</h3>
                    <div class="checkbox-list">
                        <?php foreach ($categories as $category): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="categories[]" value="<?= $category['id'] ?>"
                                    <?= in_array($category['id'], $selectedCategories) ? 'checked' : '' ?>>
                                <?= esc($category['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Tags -->
                <div class="sidebar-card">
                    <h3>Tags</h3>
                    <input type="text" id="tags_input" name="tags_input" class="form-control"
                        value="<?= esc(implode(', ', array_column($selectedTags, 'name'))) ?>"
                        placeholder="tag1, tag2, tag3">
                    <small class="form-text">Séparez les tags par des virgules</small>
                </div>

                <!-- Featured Image -->
                <div class="sidebar-card">
                    <h3>Image mise en avant</h3>
                    <div id="featured-image-preview" class="image-preview">
                        <?php if (!empty($article['featured_image_id'])): ?>
                            <img src="<?= base_url('uploads/blog/placeholder.jpg') ?>" alt="">
                            <button type="button" class="remove-image" onclick="removeFeaturedImage()">×</button>
                        <?php else: ?>
                            <div class="placeholder">Aucune image</div>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" id="featured_image_id" name="featured_image_id"
                        value="<?= $article['featured_image_id'] ?? '' ?>">
                    <button type="button" class="btn btn-outline btn-sm" onclick="openMediaLibrary()">
                        Choisir une image
                    </button>
                    <div class="form-group mt-2">
                        <label for="featured_image_alt">Texte alternatif</label>
                        <input type="text" id="featured_image_alt" name="featured_image_alt" class="form-control"
                            value="<?= esc($article['featured_image_alt'] ?? '') ?>"
                            placeholder="Description de l'image">
                    </div>
                </div>

                <!-- SEO -->
                <div class="sidebar-card">
                    <h3>SEO
                        <?php if (isset($seoScore)): ?>
                            <span
                                class="seo-grade grade-<?= strtolower($seoScore['grade']) ?>"><?= $seoScore['grade'] ?></span>
                        <?php endif; ?>
                    </h3>
                    <div class="form-group">
                        <label for="meta_title">Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title" class="form-control"
                            value="<?= esc($article['meta_title'] ?? '') ?>" maxlength="70"
                            placeholder="Titulo SEO (50-60 caractères)">
                        <div class="char-count"><span id="meta_title_count">0</span>/70</div>
                    </div>
                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" rows="2" class="form-control"
                            maxlength="160"
                            placeholder="Description SEO (150-160 caractères)"><?= esc($article['meta_description'] ?? '') ?></textarea>
                        <div class="char-count"><span id="meta_desc_count">0</span>/160</div>
                    </div>
                    <div class="form-group">
                        <label for="meta_keywords">Keywords</label>
                        <input type="text" id="meta_keywords" name="meta_keywords" class="form-control"
                            value="<?= esc($article['meta_keywords'] ?? '') ?>" placeholder="mot-clé1, mot-clé2">
                    </div>

                    <?php if (isset($seoScore) && !empty($seoScore['issues'])): ?>
                        <div class="seo-issues">
                            <h4>Problèmes</h4>
                            <ul>
                                <?php foreach ($seoScore['issues'] as $issue): ?>
                                    <li class="issue"><?= esc($issue) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($seoScore) && !empty($seoScore['suggestions'])): ?>
                        <div class="seo-suggestions">
                            <h4>Suggestions</h4>
                            <ul>
                                <?php foreach ($seoScore['suggestions'] as $suggestion): ?>
                                    <li class="suggestion"><?= esc($suggestion) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Social -->
                <div class="sidebar-card collapsed">
                    <h3 class="collapsible" onclick="this.parentElement.classList.toggle('collapsed')">
                        Open Graph / Twitter
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </h3>
                    <div class="collapse-content">
                        <div class="form-group">
                            <label for="og_title">OG Title</label>
                            <input type="text" id="og_title" name="og_title" class="form-control"
                                value="<?= esc($article['og_title'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="og_description">OG Description</label>
                            <textarea id="og_description" name="og_description" rows="2"
                                class="form-control"><?= esc($article['og_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <?php if ($isEdit): ?>
                    <!-- Versions -->
                    <div class="sidebar-card">
                        <h3>Historique</h3>
                        <a href="<?= base_url('admin/blog/versions/' . $article['id']) ?>"
                            class="btn btn-outline btn-sm btn-block">
                            Voir les versions
                        </a>
                        <div class="form-group mt-2">
                            <label for="change_summary">Note de modification</label>
                            <input type="text" id="change_summary" name="change_summary" class="form-control"
                                placeholder="Description des changements">
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
    // Status change handler
    document.getElementById('status').addEventListener('change', function () {
        document.getElementById('schedule-date').style.display = this.value === 'scheduled' ? 'block' : 'none';
    });

    // Character counters
    function updateCharCount(input, countEl) {
        document.getElementById(countEl).textContent = input.value.length;
    }
    document.getElementById('meta_title').addEventListener('input', function () {
        updateCharCount(this, 'meta_title_count');
    });
    document.getElementById('meta_description').addEventListener('input', function () {
        updateCharCount(this, 'meta_desc_count');
    });
    // Initial counts
    updateCharCount(document.getElementById('meta_title'), 'meta_title_count');
    updateCharCount(document.getElementById('meta_description'), 'meta_desc_count');

    // Featured image
    function removeFeaturedImage() {
        document.getElementById('featured_image_id').value = '';
        document.getElementById('featured-image-preview').innerHTML = '<div class="placeholder">Aucune image</div>';
    }

    function openMediaLibrary() {
        alert('Bibliothèque média - Fonctionnalité à venir. Pour l\'instant, uploadez les images via le contenu.');
    }
</script>

<!-- TinyMCE WYSIWYG Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script src="<?= base_url('js/blog-editor.js') ?>"></script>

<?= $this->endSection() ?>