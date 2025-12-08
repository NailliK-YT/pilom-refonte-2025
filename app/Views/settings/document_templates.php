<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<div class="settings-section">
    <h2>Personnalisation des documents</h2>
    <p class="section-description">Personnalisez l'apparence et le contenu de vos factures et devis.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<form action="<?= base_url('settings/company/update-documents') ?>" method="post">
    <?= csrf_field() ?>

    <!-- Template Selection -->
    <div class="settings-card">
        <h3>Modèle de document</h3>
        
        <div class="template-selector">
            <?php 
            $templates = [
                'default' => ['name' => 'Classique', 'desc' => 'Design professionnel standard'],
                'modern' => ['name' => 'Moderne', 'desc' => 'Style épuré et contemporain'],
                'minimal' => ['name' => 'Minimaliste', 'desc' => 'Focus sur l\'essentiel'],
            ];
            foreach ($templates as $key => $template): 
            ?>
                <label class="template-option">
                    <input type="radio" name="document_template" value="<?= $key ?>" 
                           <?= ($settings['document_template'] ?? 'default') === $key ? 'checked' : '' ?>>
                    <span class="template-card">
                        <span class="template-preview template-<?= $key ?>"></span>
                        <span class="template-name"><?= $template['name'] ?></span>
                        <span class="template-desc"><?= $template['desc'] ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="document_color_primary">Couleur principale</label>
                <div class="color-input">
                    <input type="color" id="document_color_primary" name="document_color_primary" 
                           value="<?= esc($settings['document_color_primary'] ?? '#4E51C0') ?>">
                    <input type="text" class="form-control" 
                           value="<?= esc($settings['document_color_primary'] ?? '#4E51C0') ?>" 
                           readonly style="width: 100px;">
                </div>
            </div>
            <div class="form-group">
                <label for="document_color_secondary">Couleur secondaire</label>
                <div class="color-input">
                    <input type="color" id="document_color_secondary" name="document_color_secondary" 
                           value="<?= esc($settings['document_color_secondary'] ?? '#1f2937') ?>">
                    <input type="text" class="form-control" 
                           value="<?= esc($settings['document_color_secondary'] ?? '#1f2937') ?>" 
                           readonly style="width: 100px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Numbering -->
    <div class="settings-card">
        <h3>Numérotation des factures</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="invoice_prefix">Préfixe</label>
                <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix" 
                       value="<?= esc($settings['invoice_prefix'] ?? 'FAC-') ?>" placeholder="FAC-">
            </div>
            <div class="form-group">
                <label for="invoice_next_number">Prochain numéro</label>
                <input type="number" class="form-control" id="invoice_next_number" name="invoice_next_number" 
                       value="<?= esc($settings['invoice_next_number'] ?? 1) ?>" min="1">
            </div>
        </div>
        
        <div class="form-group">
            <label for="invoice_number_format">Format de numérotation</label>
            <input type="text" class="form-control" id="invoice_number_format" name="invoice_number_format" 
                   value="<?= esc($settings['invoice_number_format'] ?? '{PREFIX}{YYYY}{NUM:5}') ?>">
            <small class="form-help">
                Variables: {PREFIX}, {YYYY}, {YY}, {MM}, {DD}, {NUM:X} (X = nombre de chiffres)<br>
                Exemple: FAC-{YYYY}-{NUM:5} → FAC-2024-00001
            </small>
        </div>
    </div>

    <!-- Quote Numbering -->
    <div class="settings-card">
        <h3>Numérotation des devis</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="quote_prefix">Préfixe</label>
                <input type="text" class="form-control" id="quote_prefix" name="quote_prefix" 
                       value="<?= esc($settings['quote_prefix'] ?? 'DEV-') ?>" placeholder="DEV-">
            </div>
            <div class="form-group">
                <label for="quote_next_number">Prochain numéro</label>
                <input type="number" class="form-control" id="quote_next_number" name="quote_next_number" 
                       value="<?= esc($settings['quote_next_number'] ?? 1) ?>" min="1">
            </div>
        </div>
        
        <div class="form-group">
            <label for="quote_validity_days">Validité du devis (jours)</label>
            <input type="number" class="form-control" id="quote_validity_days" name="quote_validity_days" 
                   value="<?= esc($settings['quote_validity_days'] ?? 30) ?>" min="1" style="width: 150px;">
        </div>
    </div>

    <!-- Payment Conditions -->
    <div class="settings-card">
        <h3>Conditions de paiement</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="default_payment_terms">Délai de paiement par défaut (jours)</label>
                <input type="number" class="form-control" id="default_payment_terms" name="default_payment_terms" 
                       value="<?= esc($settings['default_payment_terms'] ?? 30) ?>" min="0" style="width: 150px;">
            </div>
            <div class="form-group">
                <label for="late_payment_penalty_rate">Pénalités de retard (%)</label>
                <input type="number" step="0.01" class="form-control" id="late_payment_penalty_rate" 
                       name="late_payment_penalty_rate" 
                       value="<?= esc($settings['late_payment_penalty_rate'] ?? '') ?>" 
                       placeholder="Ex: 3.00" style="width: 150px;">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="early_payment_discount_rate">Escompte paiement anticipé (%)</label>
                <input type="number" step="0.01" class="form-control" id="early_payment_discount_rate" 
                       name="early_payment_discount_rate" 
                       value="<?= esc($settings['early_payment_discount_rate'] ?? '') ?>" 
                       placeholder="Ex: 2.00" style="width: 150px;">
            </div>
            <div class="form-group">
                <label for="early_payment_discount_days">Délai escompte (jours)</label>
                <input type="number" class="form-control" id="early_payment_discount_days" 
                       name="early_payment_discount_days" 
                       value="<?= esc($settings['early_payment_discount_days'] ?? '') ?>" 
                       placeholder="Ex: 10" style="width: 150px;">
            </div>
        </div>
        
        <div class="form-group">
            <label for="payment_conditions_text">Conditions de paiement (texte)</label>
            <textarea class="form-control" id="payment_conditions_text" name="payment_conditions_text" 
                      rows="3" placeholder="Texte affiché sur les documents..."><?= esc($settings['payment_conditions_text'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Footer Texts -->
    <div class="settings-card">
        <h3>Pieds de page</h3>
        
        <div class="form-group">
            <label for="invoice_footer_text">Pied de page des factures</label>
            <textarea class="form-control" id="invoice_footer_text" name="invoice_footer_text" 
                      rows="3" placeholder="Texte affiché en bas de chaque facture..."><?= esc($settings['invoice_footer_text'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="quote_footer_text">Pied de page des devis</label>
            <textarea class="form-control" id="quote_footer_text" name="quote_footer_text" 
                      rows="3" placeholder="Texte affiché en bas de chaque devis..."><?= esc($settings['quote_footer_text'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </div>
</form>

<style>
.template-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.template-option input { display: none; }

.template-card {
    display: flex;
    flex-direction: column;
    padding: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.template-card:hover { border-color: #4E51C0; }

.template-option input:checked + .template-card {
    border-color: #4E51C0;
    background: #f0f4ff;
}

.template-preview {
    height: 80px;
    border-radius: 6px;
    margin-bottom: 12px;
    background: #f3f4f6;
}

.template-preview.template-default { background: linear-gradient(135deg, #4E51C0 0%, #6366f1 100%); }
.template-preview.template-modern { background: linear-gradient(135deg, #1f2937 0%, #374151 100%); }
.template-preview.template-minimal { background: linear-gradient(135deg, #f9fafb 0%, #e5e7eb 100%); }

.template-name {
    font-weight: 600;
    color: #1f2937;
}

.template-desc {
    font-size: 0.875rem;
    color: #6b7280;
}

.color-input {
    display: flex;
    gap: 10px;
    align-items: center;
}

.color-input input[type="color"] {
    width: 50px;
    height: 40px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.form-help {
    display: block;
    margin-top: 8px;
    color: #6b7280;
    font-size: 0.875rem;
}
</style>

<script>
document.querySelectorAll('input[type="color"]').forEach(colorInput => {
    colorInput.addEventListener('input', (e) => {
        e.target.nextElementSibling.value = e.target.value;
    });
});
</script>

<?= $this->endSection() ?>
