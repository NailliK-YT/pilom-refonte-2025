<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Produit') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/products.css') ?>">
<style>
    /* Modern Product Form Styles */
    .product-form-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 32px 24px;
    }

    .form-header {
        background: linear-gradient(135deg, #4e51c0 0%, #3d3e9f 100%);
        color: white;
        padding: 32px;
        border-radius: 16px 16px 0 0;
        margin: -24px -24px 32px -24px;
        box-shadow: 0 4px 20px rgba(78, 81, 192, 0.3);
    }

    .form-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 14px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 32px;
        margin-top: 32px;
    }

    .form-main {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .form-section {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }

    .form-section:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-color: #e0e0e0;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f5f5f5;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #4e51c0 0%, #5a5dc7 100%);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 12px rgba(78, 81, 192, 0.2);
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-field {
        position: relative;
    }

    .form-field label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #34495e;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .required-badge {
        color: #e74c3c;
        font-size: 12px;
    }

    .form-field input,
    .form-field select,
    .form-field textarea {
        width: 100%;
        padding: 14px 16px;
        font-size: 15px;
        border: 2px solid #e8ecef;
        border-radius: 10px;
        background: #fafbfc;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .form-field input:focus,
    .form-field select:focus,
    .form-field textarea:focus {
        outline: none;
        border-color: #4e51c0;
        background: white;
        box-shadow: 0 0 0 4px rgba(78, 81, 192, 0.05);
    }

    .form-field input.error,
    .form-field select.error,
    .form-field textarea.error {
        border-color: #e74c3c;
    }

    .form-field textarea {
        min-height: 120px;
        resize: vertical;
        line-height: 1.6;
    }

    /* Price Preview Card */
    .price-preview-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 2px solid #e8ecef;
        border-radius: 16px;
        padding: 24px;
        margin-top: 20px;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .price-row:last-child {
        border-bottom: none;
        padding-top: 16px;
        margin-top: 8px;
        border-top: 2px solid #4e51c0;
    }

    .price-label {
        font-size: 14px;
        color: #7f8c8d;
        font-weight: 500;
    }

    .price-value {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
    }

    .price-value.highlight {
        font-size: 24px;
        color: #4e51c0;
    }

    /* Price Tiers */
    .price-tier-item {
        display: flex;
        gap: 12px;
        align-items: center;
        background: #f8f9fa;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 12px;
        border: 2px solid #e8ecef;
        transition: all 0.2s ease;
    }

    .price-tier-item:hover {
        border-color: #4e51c0;
        background: #fafbfc;
    }

    .price-tier-item input {
        flex: 1;
        padding: 12px 14px;
        border: 2px solid #e8ecef;
        border-radius: 10px;
        font-size: 14px;
    }

    .tier-remove-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: none;
        background: #fee;
        color: #e74c3c;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .tier-remove-btn:hover {
        background: #e74c3c;
        color: white;
        transform: scale(1.05);
    }

    .add-tier-btn {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #1fc187 0%, #19a170 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .add-tier-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(31, 193, 135, 0.3);
    }

    /* Sidebar */
    .form-sidebar {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Image Upload */
    .image-upload-card {
        background: white;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        border: 1px solid #f0f0f0;
    }

    .image-upload-area {
        position: relative;
        width: 100%;
        aspect-ratio: 1;
        border: 3px dashed #cbd5e0;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .image-upload-area:hover {
        border-color: #4e51c0;
        background: #fafbfc;
    }

    .image-upload-area.dragover {
        border-color: #1fc187;
        background: #f0fdf4;
    }

    .upload-placeholder {
        text-align: center;
        padding: 20px;
    }

    .upload-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #4e51c0 0%, #5a5dc7 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin: 0 auto 16px;
    }

    .upload-text {
        font-size: 15px;
        font-weight: 600;
        color: #34495e;
        margin-bottom: 4px;
    }

    .upload-hint {
        font-size: 13px;
        color: #95a5a6;
    }

    .image-preview-wrapper {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .image-preview-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-remove-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 36px;
        height: 36px;
        background: rgba(231, 76, 60, 0.95);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .image-preview-wrapper:hover .image-remove-btn {
        opacity: 1;
    }

    .image-remove-btn:hover {
        background: #c0392b;
        transform: scale(1.1);
    }

    /* Quick Tips Card */
    .quick-tips-card {
        background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
        border: 2px solid #ffd966;
        border-radius: 16px;
        padding: 24px;
    }

    .quick-tips-card h3 {
        font-size: 16px;
        font-weight: 700;
        color: #856404;
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tip-item {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
        font-size: 13px;
        color: #7f6e00;
        line-height: 1.5;
    }

    .tip-icon {
        color: #ffc107;
        font-size: 16px;
        flex-shrink: 0;
    }

    /* Action Buttons */
    .form-actions {
        display: flex;
        gap: 16px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #f5f5f5;
    }

    .btn-submit {
        flex: 1;
        padding: 18px 32px;
        background: linear-gradient(135deg, #4e51c0 0%, #3d3e9f 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 4px 16px rgba(78, 81, 192, 0.3);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(78, 81, 192, 0.4);
    }

    .btn-cancel {
        padding: 18px 32px;
        background: white;
        color: #7f8c8d;
        border: 2px solid #e8ecef;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-cancel:hover {
        border-color: #cbd5e0;
        background: #f8f9fa;
    }

    /* Error Alert */
    .alert-error {
        background: #fee;
        border-left: 4px solid #e74c3c;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
    }

    .alert-error p {
        color: #c0392b;
        font-size: 14px;
        margin: 4px 0;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .product-form-container {
            padding: 16px 12px;
        }

        .form-header {
            padding: 24px;
            margin: -16px -16px 24px -16px;
        }

        .form-section {
            padding: 20px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="product-form-container">
    <div class="form-section">
        <div class="form-header">
            <h1>
                <span>📦</span>
                <?= esc($title) ?>
            </h1>
            <p><?= isset($product) ? 'Modifiez les informations du produit' : 'Créez un nouveau produit pour votre catalogue' ?></p>
        </div>

        <?php if (isset($validation) && $validation->getErrors()): ?>
            <div class="alert-error">
                <?php foreach ($validation->getErrors() as $error): ?>
                    <p>❌ <?= esc($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= current_url() ?>" enctype="multipart/form-data" id="productForm">
            <?= csrf_field() ?>

            <div class="form-grid">
                <div class="form-main">
                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">ℹ️</div>
                            <h2 class="section-title">Informations de base</h2>
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="name">
                                    Nom du produit
                                    <span class="required-badge">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name"
                                    value="<?= old('name', $product['name'] ?? '') ?>"
                                    placeholder="Ex: MacBook Pro 14 pouces"
                                    required
                                >
                            </div>

                            <div class="form-field">
                                <label for="reference">
                                    Référence
                                    <span class="required-badge">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="reference" 
                                    name="reference"
                                    value="<?= old('reference', $product['reference'] ?? '') ?>"
                                    placeholder="Ex: MBP-14-2024"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="description">
                                Description
                            </label>
                            <textarea 
                                id="description" 
                                name="description"
                                placeholder="Décrivez votre produit en détail..."
                            ><?= old('description', $product['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="category_id">
                                    Catégorie
                                    <span class="required-badge">*</span>
                                </label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    <?php if (isset($categories)): ?>
                                        <?php foreach ($categories as $id => $name): ?>
                                            <option value="<?= esc($id) ?>" <?= old('category_id', $product['category_id'] ?? '') == $id ? 'selected' : '' ?>>
                                                <?= esc($name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="tva_id">
                                    Taux de TVA
                                    <span class="required-badge">*</span>
                                </label>
                                <select id="tva_id" name="tva_id" required>
                                    <?php if (isset($tvaRates)): ?>
                                        <?php foreach ($tvaRates as $rate): ?>
                                            <option 
                                                value="<?= esc($rate['id']) ?>"
                                                data-rate="<?= esc($rate['rate']) ?>"
                                                <?= old('tva_id', $product['tva_id'] ?? '') == $rate['id'] ? 'selected' : '' ?>
                                            >
                                                <?= esc($rate['label']) ?> (<?= $rate['rate'] ?>%)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">💰</div>
                            <h2 class="section-title">Tarification</h2>
                        </div>

                        <div class="form-field">
                            <label for="price_ht">
                                Prix HT (€)
                                <span class="required-badge">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="price_ht" 
                                name="price_ht"
                                step="0.01"
                                min="0"
                                value="<?= old('price_ht', $product['price_ht'] ?? '') ?>"
                                placeholder="0.00"
                                required
                            >
                        </div>

                        <div class="price-preview-card">
                            <div class="price-row">
                                <span class="price-label">Prix HT</span>
                                <span class="price-value" id="display-ht">0,00 €</span>
                            </div>
                            <div class="price-row">
                                <span class="price-label">TVA</span>
                                <span class="price-value" id="display-tva">0,00 €</span>
                            </div>
                            <div class="price-row">
                                <span class="price-label">Prix TTC</span>
                                <span class="price-value highlight" id="display-ttc">0,00 €</span>
                            </div>
                        </div>
                    </div>

                    <!-- Volume Pricing Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon">📊</div>
                            <h2 class="section-title">Prix dégressifs</h2>
                        </div>

                        <p style="color: #7f8c8d; font-size: 14px; margin-bottom: 20px;">
                            Définissez des tarifs préférentiels en fonction de la quantité commandée
                        </p>

                        <div id="price-tiers">
                            <?php if (isset($priceTiers) && !empty($priceTiers)): ?>
                                <?php foreach ($priceTiers as $tier): ?>
                                    <div class="price-tier-item">
                                        <input 
                                            type="number" 
                                            name="tier_quantity[]"
                                            placeholder="Quantité min"
                                            min="1"
                                            value="<?= $tier['min_quantity'] ?>"
                                        >
                                        <input 
                                            type="number" 
                                            name="tier_price[]"
                                            placeholder="Prix HT"
                                            step="0.01"
                                            min="0"
                                            value="<?= $tier['price_ht'] ?>"
                                        >
                                        <button type="button" class="tier-remove-btn" onclick="this.parentElement.remove()">
                                            🗑️
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <button type="button" class="add-tier-btn" onclick="addPriceTier()">
                            <span>➕</span>
                            Ajouter un palier
                        </button>
                    </div>
                </div>

                <div class="form-sidebar">
                    <!-- Image Upload Section -->
                    <div class="image-upload-card">
                        <div class="section-header">
                            <div class="section-icon">🖼️</div>
                            <h2 class="section-title">Image</h2>
                        </div>

                        <input 
                            type="file" 
                            id="product_image" 
                            name="product_image"
                            accept="image/*"
                            style="display: none;"
                            onchange="handleImageSelect(this)"
                        >

                        <div 
                            class="image-upload-area" 
                            id="imageUploadArea"
                            onclick="document.getElementById('product_image').click()"
                        >
                            <?php if (isset($product['image_path']) && !empty($product['image_path'])): ?>
                                <div class="image-preview-wrapper" id="imagePreview">
                                    <img src="<?= base_url('writable/uploads/' . $product['image_path']) ?>" alt="Aperçu">
                                    <button type="button" class="image-remove-btn" onclick="removeImage(event)">
                                        ✕
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <div class="upload-icon">📷</div>
                                    <p class="upload-text">Cliquez pour ajouter une image</p>
                                    <p class="upload-hint">JPG, PNG ou WEBP • Max 5 MB</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Tips Card -->
                    <div class="quick-tips-card">
                        <h3>💡 Conseils pratiques</h3>
                        <div class="tip-item">
                            <span class="tip-icon">✓</span>
                            <span>Utilisez une référence unique et explicite</span>
                        </div>
                        <div class="tip-item">
                            <span class="tip-icon">✓</span>
                            <span>Rédigez une description détaillée pour vos clients</span>
                        </div>
                        <div class="tip-item">
                            <span class="tip-icon">✓</span>
                            <span>Les prix dégressifs encouragent les achats en volume</span>
                        </div>
                        <div class="tip-item">
                            <span class="tip-icon">✓</span>
                            <span>Privilégiez des images de qualité (min 800x800px)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?= base_url('products') ?>" class="btn-cancel">
                    <span>←</span>
                    Annuler
                </a>
                <button type="submit" class="btn-submit">
                    <span><?= isset($product) ? '💾' : '✨' ?></span>
                    <?= isset($product) ? 'Enregistrer les modifications' : 'Créer le produit' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Price calculation
function updatePriceDisplay() {
    const priceHT = parseFloat(document.getElementById('price_ht').value) || 0;
    const tvaSelect = document.getElementById('tva_id');
    const tvaRate = parseFloat(tvaSelect.options[tvaSelect.selectedIndex]?.dataset.rate || 0);
    
    const tvaAmount = priceHT * (tvaRate / 100);
    const priceTTC = priceHT + tvaAmount;
    
    document.getElementById('display-ht').textContent = priceHT.toLocaleString('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + ' €';
    
    document.getElementById('display-tva').textContent = tvaAmount.toLocaleString('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + ' €';
    
    document.getElementById('display-ttc').textContent = priceTTC.toLocaleString('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + ' €';
}

document.getElementById('price_ht').addEventListener('input', updatePriceDisplay);
document.getElementById('tva_id').addEventListener('change', updatePriceDisplay);

// Initialize price display
document.addEventListener('DOMContentLoaded', updatePriceDisplay);

// Add price tier
function addPriceTier() {
    const container = document.getElementById('price-tiers');
    const tierHtml = `
        <div class="price-tier-item">
            <input type="number" name="tier_quantity[]" placeholder="Quantité min" min="1">
            <input type="number" name="tier_price[]" placeholder="Prix HT" step="0.01" min="0">
            <button type="button" class="tier-remove-btn" onclick="this.parentElement.remove()">
                🗑️
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', tierHtml);
}

// Image handling
function handleImageSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('L\'image est trop volumineuse. Taille maximum : 5 MB');
            return;
        }
        
        // Validate file type
        if (!file.type.match('image.*')) {
            alert('Veuillez sélectionner une image valide');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const uploadArea = document.getElementById('imageUploadArea');
            uploadArea.innerHTML = `
                <div class="image-preview-wrapper" id="imagePreview">
                    <img src="${e.target.result}" alt="Aperçu">
                    <button type="button" class="image-remove-btn" onclick="removeImage(event)">
                        ✕
                    </button>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
}

function removeImage(event) {
    event.stopPropagation();
    event.preventDefault();
    
    document.getElementById('product_image').value = '';
    
    const uploadArea = document.getElementById('imageUploadArea');
    uploadArea.innerHTML = `
        <div class="upload-placeholder" id="uploadPlaceholder">
            <div class="upload-icon">📷</div>
            <p class="upload-text">Cliquez pour ajouter une image</p>
            <p class="upload-hint">JPG, PNG ou WEBP • Max 5 MB</p>
        </div>
    `;
}

// Drag & drop support
const uploadArea = document.getElementById('imageUploadArea');

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('product_image').files = files;
        handleImageSelect(document.getElementById('product_image'));
    }
});
</script>
<?= $this->endSection() ?>
