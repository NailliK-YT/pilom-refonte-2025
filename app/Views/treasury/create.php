<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="header-left">
        <a href="<?= base_url('treasury') ?>" class="back-link">← Retour à la trésorerie</a>
        <h1>Nouvelle entrée</h1>
    </div>
</div>

<div class="form-container">
    <form action="<?= base_url('treasury/store') ?>" method="post" class="treasury-form">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label for="type">Type de mouvement <span class="required">*</span></label>
            <div class="type-selector">
                <label class="type-option">
                    <input type="radio" name="type" value="entry" <?= old('type', 'entry') === 'entry' ? 'checked' : '' ?>>
                    <span class="type-card entry">
                        <span class="type-icon">📈</span>
                        <span class="type-label">Entrée</span>
                        <span class="type-desc">Argent reçu</span>
                    </span>
                </label>
                <label class="type-option">
                    <input type="radio" name="type" value="exit" <?= old('type') === 'exit' ? 'checked' : '' ?>>
                    <span class="type-card exit">
                        <span class="type-icon">📉</span>
                        <span class="type-label">Sortie</span>
                        <span class="type-desc">Argent dépensé</span>
                    </span>
                </label>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="amount">Montant <span class="required">*</span></label>
                <div class="input-with-suffix">
                    <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" 
                           value="<?= old('amount') ?>" required placeholder="0.00">
                    <span class="input-suffix">€</span>
                </div>
            </div>

            <div class="form-group">
                <label for="transaction_date">Date <span class="required">*</span></label>
                <input type="date" class="form-control" id="transaction_date" name="transaction_date" 
                       value="<?= old('transaction_date', date('Y-m-d')) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="category">Catégorie</label>
            <select class="form-control" id="category" name="category">
                <option value="other" <?= old('category') === 'other' ? 'selected' : '' ?>>Autre</option>
                <option value="invoice" <?= old('category') === 'invoice' ? 'selected' : '' ?>>Facture client</option>
                <option value="expense" <?= old('category') === 'expense' ? 'selected' : '' ?>>Dépense</option>
                <option value="salary" <?= old('category') === 'salary' ? 'selected' : '' ?>>Salaire</option>
                <option value="tax" <?= old('category') === 'tax' ? 'selected' : '' ?>>Impôts/Taxes</option>
                <option value="loan" <?= old('category') === 'loan' ? 'selected' : '' ?>>Prêt/Emprunt</option>
                <option value="investment" <?= old('category') === 'investment' ? 'selected' : '' ?>>Investissement</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" 
                      placeholder="Description optionnelle..."><?= old('description') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="<?= base_url('treasury') ?>" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>

<style>
.form-container {
    max-width: 600px;
    background: white;
    padding: 30px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.type-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.type-option input {
    display: none;
}

.type-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.type-card:hover {
    border-color: #4E51C0;
}

.type-option input:checked + .type-card {
    border-color: #4E51C0;
    background: #f0f4ff;
}

.type-card.entry .type-icon { color: #10b981; }
.type-card.exit .type-icon { color: #ef4444; }

.type-icon {
    font-size: 2rem;
    margin-bottom: 8px;
}

.type-label {
    font-weight: 600;
    color: #1f2937;
}

.type-desc {
    font-size: 0.875rem;
    color: #6b7280;
}

.input-with-suffix {
    position: relative;
}

.input-with-suffix .form-control {
    padding-right: 40px;
}

.input-suffix {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-weight: 500;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.back-link {
    color: #6b7280;
    text-decoration: none;
    font-size: 0.875rem;
    margin-bottom: 8px;
    display: inline-block;
}

.back-link:hover {
    color: #4E51C0;
}
</style>

<?= $this->endSection() ?>
