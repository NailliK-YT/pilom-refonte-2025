<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Taux de TVA') ?></title>
    <link rel="stylesheet" href="<?= base_url('css/products.css') ?>">
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><?= esc($title) ?></h1>
        </div>

        <div class="card">
            <?php if (isset($validation) && $validation->getErrors()): ?>
                <div class="alert alert-error">
                    <?php foreach ($validation->getErrors() as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (session()->has('errors')): ?>
                <div class="alert alert-error">
                    <?php foreach (session('errors') as $error): ?>
                        <p><?= esc($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= current_url() ?>">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="label">Libellé du taux *</label>
                    <input type="text" id="label" name="label"
                        class="form-control <?= isset($validation) && $validation->hasError('label') ? 'error' : '' ?>"
                        value="<?= old('label', $tvaRate['label'] ?? '') ?>" required>
                    <span class="form-hint">Ex: TVA normale, TVA réduite...</span>
                    <?php if (isset($validation) && $validation->hasError('label')): ?>
                        <span class="form-error"><?= $validation->getError('label') ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="rate">Taux (%) *</label>
                    <input type="number" id="rate" name="rate"
                        class="form-control <?= isset($validation) && $validation->hasError('rate') ? 'error' : '' ?>"
                        value="<?= old('rate', $tvaRate['rate'] ?? '') ?>" step="0.01" min="0" max="100" required>
                    <span class="form-hint">Taux entre 0 et 100%</span>
                    <?php if (isset($validation) && $validation->hasError('rate')): ?>
                        <span class="form-error"><?= $validation->getError('rate') ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="is_default" name="is_default" class="form-check-input" value="1"
                            <?= old('is_default', $tvaRate['is_default'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_default">
                            Définir comme taux par défaut
                        </label>
                    </div>
                    <span class="form-hint">Un seul taux peut être défini par défaut</span>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <?= isset($tvaRate) ? 'Modifier' : 'Créer' ?> →
                    </button>
                    <a href="<?= base_url('tva-rates') ?>" class="btn btn-outline">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>