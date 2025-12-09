<?= $this->extend('layouts/public') ?>

<?php
// Generate Schema.org FAQPage structured data
helper('seo');
$faqSchema = schema_faq($faqs ?? []);
?>

<?= $this->section('content') ?>

<!-- Schema.org FAQPage -->
<?= $faqSchema ?>

<div class="page-header">
    <h1>Questions Fréquentes</h1>
    <p class="page-subtitle">Trouvez rapidement les réponses à vos questions</p>
</div>

<div class="faq-content">
    <div class="faq-search">
        <input type="text" id="faq-search" placeholder="Rechercher une question..." class="form-control">
    </div>

    <div class="faq-list">
        <?php foreach ($faqs as $index => $faq): ?>
            <div class="faq-item" data-index="<?= $index ?>">
                <button class="faq-question" onclick="toggleFaq(<?= $index ?>)">
                    <span><?= esc($faq['question']) ?></span>
                    <svg class="faq-icon" viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="faq-answer" id="faq-answer-<?= $index ?>">
                    <p><?= esc($faq['answer']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="faq-cta">
        <h2>Vous n'avez pas trouvé votre réponse ?</h2>
        <p>Notre équipe est là pour vous aider.</p>
        <a href="<?= base_url('contact') ?>" class="btn btn-primary">Nous contacter</a>
    </div>
</div>

<script>
    function toggleFaq(index) {
        const item = document.querySelector(`.faq-item[data-index="${index}"]`);
        const answer = document.getElementById(`faq-answer-${index}`);

        item.classList.toggle('active');

        if (item.classList.contains('active')) {
            answer.style.maxHeight = answer.scrollHeight + 'px';
        } else {
            answer.style.maxHeight = '0';
        }
    }

    // Search functionality
    document.getElementById('faq-search').addEventListener('input', function (e) {
        const searchTerm = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.faq-item');

        items.forEach(item => {
            const question = item.querySelector('.faq-question span').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();

            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

<?= $this->endSection() ?>