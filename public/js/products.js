// Pilom - Products Module JavaScript
// Gestion des interactions dynamiques

// Aperçu d'image lors de l'upload
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Aperçu">`;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Calcul automatique du prix TTC
function calculatePriceTTC() {
    const priceHT = parseFloat(document.getElementById('price_ht')?.value) || 0;
    const tvaSelect = document.getElementById('tva_id');
    const selectedOption = tvaSelect?.options[tvaSelect.selectedIndex];
    const tvaRate = parseFloat(selectedOption?.dataset.rate) || 0;
    
    const tvaAmount = priceHT * (tvaRate / 100);
    const priceTTC = priceHT + tvaAmount;
    
    // Mettre à jour l'affichage
    document.getElementById('display-ht').textContent = formatPrice(priceHT);
    document.getElementById('display-tva').textContent = formatPrice(tvaAmount);
    document.getElementById('display-ttc').textContent = formatPrice(priceTTC);
}

// Formatage du prix
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}

// Ajouter un palier de prix dégressif
function addPriceTier() {
    const container = document.getElementById('price-tiers');
    const row = document.createElement('div');
    row.className = 'd-flex gap-2 mb-2 price-tier-row';
    row.innerHTML = `
        <input type="number" name="tier_quantity[]" class="form-control" 
               placeholder="Quantité min" min="1">
        <input type="number" name="tier_price[]" class="form-control" 
               placeholder="Prix HT" step="0.01" min="0">
        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Supprimer</button>
    `;
    container.appendChild(row);
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Écouter les changements de prix et TVA
    const priceInput = document.getElementById('price_ht');
    const tvaSelect = document.getElementById('tva_id');
    
    if (priceInput && tvaSelect) {
        priceInput.addEventListener('input', calculatePriceTTC);
        tvaSelect.addEventListener('change', calculatePriceTTC);
        
        // Calculer initialement
        calculatePriceTTC();
    }
    
    // Validation du formulaire
    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const reference = document.getElementById('reference').value.trim();
            const priceHT = parseFloat(document.getElementById('price_ht').value);
            
            if (!name || name.length < 3) {
                alert('Le nom du produit doit contenir au moins 3 caractères');
                e.preventDefault();
                return false;
            }
            
            if (!reference) {
                alert('La référence est obligatoire');
                e.preventDefault();
                return false;
            }
            
            if (!priceHT || priceHT < 0) {
                alert('Le prix HT doit être positif');
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Animation des boutons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

// Confirmation de suppression
function confirmDelete(message) {
    return confirm(message || 'Êtes-vous sûr de vouloir supprimer cet élément ?');
}
