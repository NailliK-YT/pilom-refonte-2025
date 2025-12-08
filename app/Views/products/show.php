<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($product['name'] ?? 'Produit') ?></title>
    <link rel="stylesheet" href="<?= base_url('css/products.css') ?>">
    <style>
        .product-image {
            max-width: 400px;
            width: 100%;
            border-radius: 8px;
        }

        .breadcrumb {
            font-size: 14px;
            color: #999;
            margin-bottom: 16px;
        }

        .breadcrumb a {
            color: #4e51c0;
            text-decoration: none;
        }

        .price-box {
            background: linear-gradient(135deg, #4e51c0 0%, #3d3e9f 100%);
            color: white;
            padding: 24px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
            <div class="breadcrumb">
                <?php foreach ($breadcrumb as $index => $crumb): ?>
                    <?= $index > 0 ? ' > ' : '' ?>
                    <a href="<?= base_url('categories') ?>"><?= esc($crumb['name']) ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1 class="page-title"><?= esc($product['name']) ?></h1>
            <div class="d-flex gap-2">
                <a href="<?= base_url('products/edit/' . $product['id']) ?>" class="btn btn-primary">
                    Modifier →
                </a>
                <a href="<?= base_url('products') ?>" class="btn btn-outline">
                    Retour
                </a>
            </div>
        </div>

        <div class="d-flex gap-2" style="align-items: flex-start;">
            <!-- Colonne principale -->
            <div style="flex: 2;">
                <div class="card">
                    <?php if (!empty($product['image_path'])): ?>
                        <img src="<?= base_url('writable/uploads/' . $product['image_path']) ?>"
                            alt="<?= esc($product['name']) ?>" class="product-image">
                    <?php endif; ?>

                    <h2 class="mt-3">Détails</h2>
                    <table style="width: 100%; margin-top: 12px;">
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Référence:</td>
                            <td style="padding: 8px 0;"><?= esc($product['reference']) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Catégorie:</td>
                            <td style="padding: 8px 0;"><?= esc($product['category_name'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Statut:</td>
                            <td style="padding: 8px 0;">
                                <?php if ($product['is_archived'] ?? false): ?>
                                    <span class="badge badge-danger">Archivé</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Actif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: 600;">Créé le:</td>
                            <td style="padding: 8px 0;"><?= date('d/m/Y à H:i', strtotime($product['created_at'])) ?>
                            </td>
                        </tr>
                    </table>

                    <?php if (!empty($product['description'])): ?>
                        <h3 class="mt-3">Description</h3>
                        <p><?= nl2br(esc($product['description'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Colonne prix -->
            <div style="flex: 1;">
                <div class="price-box">
                    <h3 style="margin: 0 0 16px 0;">Prix</h3>
                    <div style="margin-bottom: 8px;">
                        <span style="opacity: 0.9;">Prix HT</span><br>
                        <strong style="font-size: 18px;"><?= number_format($product['price_ht'], 2, ',', ' ') ?>
                            €</strong>
                    </div>
                    <div style="margin-bottom: 8px;">
                        <span style="opacity: 0.9;">TVA (<?= $product['tva_rate'] ?? 'N/A' ?>%)</span><br>
                        <span
                            style="font-size: 16px;"><?= number_format(($product['price_ht'] * ($product['tva_rate'] ?? 20) / 100), 2, ',', ' ') ?>
                            €</span>
                    </div>
                    <div style="border-top: 1px solid rgba(255,255,255,0.3); padding-top: 12px; margin-top: 12px;">
                        <span style="opacity: 0.9;">Prix TTC</span><br>
                        <strong style="font-size: 28px;"><?= number_format($priceTTC ?? 0, 2, ',', ' ') ?> €</strong>
                    </div>
                </div>

                <?php if (isset($priceTiers) && !empty($priceTiers)): ?>
                    <div class="card">
                        <h3>Prix dégressifs</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Quantité min</th>
                                    <th>Prix HT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($priceTiers as $tier): ?>
                                    <tr>
                                        <td><?= $tier['min_quantity'] ?></td>
                                        <td><strong><?= number_format($tier['price_ht'], 2, ',', ' ') ?> €</strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>