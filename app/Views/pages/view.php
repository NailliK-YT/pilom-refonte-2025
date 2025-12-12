<?= $this->extend('layouts/public') ?>

<?= $this->section('styles') ?>
<style>
	h1 {
		color: white;
	}

	.container ol,	
	.container ol li {
		list-style: none;
		color: #666;
	}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Fil d'Ariane -->
<?php if (!empty($breadcrumb)): ?>
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px 20px 0;">
        <?= render_breadcrumb($breadcrumb) ?>
    </div>
<?php endif; ?>

<!-- Contenu de la page -->
<article>
    <div class="content">
        <?= $page['content'] ?>
    </div>
</article>

<?= $this->endSection() ?>