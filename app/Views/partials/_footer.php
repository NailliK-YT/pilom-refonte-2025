<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Navigation</h3>
                <?= render_footer($footer_items ?? []) ?>
            </div>
            <div class="footer-section">
                <h3>Informations</h3>
                <p>&copy; <?= date('Y') ?> Mon Entreprise. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</footer>