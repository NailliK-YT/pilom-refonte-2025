<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Catégorie') ?></title>
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
                    <label class="form-label" for="name">Nom de la catégorie *</label>
                    <input type="text" id="name" name="name"
                        class="form-control <?= isset($validation) && $validation->hasError('name') ? 'error' : '' ?>"
                        value="<?= old('name', $category['name'] ?? '') ?>" required>
                    <?php if (isset($validation) && $validation->hasError('name')): ?>
                        <span class="form-error"><?= $validation->getError('name') ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control"
                        rows="4"><?= old('description', $category['description'] ?? '') ?></textarea>
                    <span class="form-hint">Description optionnelle de la catégorie</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="parent_id">Catégorie parente</label>
                    <select id="parent_id" name="parent_id" class="form-control">
                        <option value="">-- Aucune (catégorie racine) --</option>
                        <?php if (isset($categoriesForSelect) && !empty($categoriesForSelect)): ?>
                            <?php foreach ($categoriesForSelect as $id => $name): ?>
                                <option value="<?= esc($id) ?>" <?= old('parent_id', $category['parent_id'] ?? '') == $id ? 'selected' : '' ?>>
                                    <?= esc($name) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <span class="form-hint">Sélectionnez une catégorie parente pour créer une sous-catégorie</span>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <?= isset($category) && !empty($category['id']) ? 'Modifier' : 'Créer' ?> →
                    </button>
                    <a href="<?= base_url('categories') ?>" class="btn btn-outline">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>