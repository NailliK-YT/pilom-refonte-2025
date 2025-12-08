<?= $this->extend('settings/layout') ?>

<?= $this->section('settings_content') ?>

<div class="card-header">
    <h2 class="card-title">Paramètres de facturation</h2>
</div>

<form action="<?= base_url('settings/company/update-invoicing') ?>" method="post">
    <?= csrf_field() ?>

    <h3>TVA et Taxes</h3>

    <div class="form-group">
        <label for="default_vat_rate">Taux de TVA par défaut</label>
        <select class="form-control" id="default_vat_rate" name="default_vat_rate" required>
            <?php foreach ($tvaRates as $rate): ?>
                <option value="<?= $rate['rate'] ?>" <?= ($settings['default_vat_rate'] ?? 20.0) == $rate['rate'] ? 'selected' : '' ?>>
                    <?= esc($rate['label']) ?> (<?= $rate['rate'] ?>%)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <hr>

    <h3>Numérotation</h3>

    <div class="form-row">
        <div class="form-group">
            <label for="invoice_prefix">Préfixe des factures <span class="required">*</span></label>
            <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix"
                value="<?= esc($settings['invoice_prefix'] ?? 'FAC-') ?>" required maxlength="10"
                pattern="[A-Za-z0-9]+">
            <small class="form-text">Lettres et chiffres uniquement</small>
        </div>

        <div class="form-group">
            <label for="invoice_next_number">Prochain numéro <span class="required">*</span></label>
            <input type="number" class="form-control" id="invoice_next_number" name="invoice_next_number"
                value="<?= esc($settings['invoice_next_number'] ?? 1) ?>" required min="1">
        </div>
    </div>

    <div class="invoice-preview">
        <strong>Aperçu :</strong>
        <span id="invoicePreview">
            <?= esc($settings['invoice_prefix'] ?? 'INV') ?>-<?= str_pad($settings['invoice_next_number'] ?? 1, 4, '0', STR_PAD_LEFT) ?>
        </span>
    </div>

    <hr>

    <h3>Informations bancaires</h3>

    <div class="form-group">
        <label for="iban">IBAN</label>
        <input type="text" class="form-control" id="iban" name="iban"
            value="<?= esc($settings['iban'] ?? old('iban')) ?>" placeholder="FR76 1234 5678 9012 3456 7890 123"
            maxlength="34">
        <small class="form-text">Numéro de compte international</small>
    </div>

    <div class="form-group">
        <label for="bic">BIC / SWIFT</label>
        <input type="text" class="form-control" id="bic" name="bic"
            value="<?= esc($settings['bic'] ?? old('bic')) ?>" placeholder="BNPAFRPPXXX" maxlength="11">
        <small class="form-text">Code d'identification bancaire</small>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            Enregistrer les paramètres
        </button>
    </div>
</form>

<?= $this->endSection() ?>