/**
 * Company Settings JavaScript
 * Handles logo uploads, invoice preview, and settings management
 */

// Base URL helper
const BASE_URL = window.location.origin + '/';

document.addEventListener('DOMContentLoaded', function () {
    // Logo upload
    initLogoUpload();

    // Invoice preview
    initInvoicePreview();

    // Form enhancements
    initSettingsValidation();
});

/**
 * Initialize logo upload
 */
function initLogoUpload() {
    const logoUpload = document.getElementById('logoUpload');
    const logoPreview = document.getElementById('logoPreview');
    const deleteLogoBtn = document.getElementById('deleteLogo');
    const logoPreviewContainer = document.querySelector('.logo-preview');

    if (logoUpload) {
        logoUpload.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file
            if (!file.type.match('image.*')) {
                showMessage('Veuillez sélectionner une image valide.', 'danger');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showMessage('La taille de l\'image ne doit pas dépasser 5MB.', 'danger');
                return;
            }

            // Preview image with animation
            const reader = new FileReader();
            reader.onload = function (e) {
                // Add fade animation
                if (logoPreviewContainer) {
                    logoPreviewContainer.style.opacity = '0.5';
                    logoPreviewContainer.style.transition = 'opacity 0.3s ease';
                }

                setTimeout(() => {
                    // Replace placeholder or existing image
                    const existingImg = logoPreviewContainer.querySelector('img');
                    const placeholder = logoPreviewContainer.querySelector('.logo-placeholder');

                    if (existingImg) {
                        existingImg.src = e.target.result;
                    } else if (placeholder) {
                        // Create new image element
                        const newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.alt = 'Logo de l\'entreprise';
                        newImg.id = 'logoPreview';
                        placeholder.replaceWith(newImg);
                    }

                    logoPreviewContainer.style.opacity = '1';
                }, 300);
            };
            reader.readAsDataURL(file);

            // Note: Don't upload automatically, let form submission handle it
            // Upload via AJAX - Option: Uncomment if you want immediate upload
            // uploadLogo(file);
        });
    }

    if (deleteLogoBtn) {
        deleteLogoBtn.addEventListener('click', function () {
            if (!confirm('❌ Voulez-vous vraiment supprimer le logo ?')) {
                return;
            }

            // Add loading state
            this.classList.add('loading');
            this.disabled = true;

            fetch(BASE_URL + 'settings/company/delete-logo', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('✓ Logo supprimé avec succès !', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        this.classList.remove('loading');
                        this.disabled = false;
                        showMessage(data.error || 'Erreur lors de la suppression.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.classList.remove('loading');
                    this.disabled = false;
                    showMessage('Erreur de connexion.', 'danger');
                });
        });
    }
}

/**
 * Upload logo file
 */
function uploadLogo(file) {
    const formData = new FormData();
    formData.append('logo', file);

    fetch(BASE_URL + 'settings/company/upload-logo', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Logo mis à jour avec succès !', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(data.error || 'Erreur lors du téléchargement.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur de connexion.');
        });
}

/**
 * Initialize invoice preview
 */
function initInvoicePreview() {
    const prefixInput = document.getElementById('invoice_prefix');
    const numberInput = document.getElementById('invoice_next_number');
    const preview = document.getElementById('invoicePreview');

    if (prefixInput && numberInput && preview) {
        const updatePreview = () => {
            const prefix = prefixInput.value || 'INV';
            const number = numberInput.value || '1';
            const paddedNumber = String(number).padStart(4, '0');
            preview.textContent = `${prefix}-${paddedNumber}`;
        };

        prefixInput.addEventListener('input', updatePreview);
        numberInput.addEventListener('input', updatePreview);
    }
}

/**
 * Initialize settings validation
 */
function initSettingsValidation() {
    // SIRET validation
    const siretInput = document.getElementById('siret');
    if (siretInput) {
        siretInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');

            if (this.value.length === 14) {
                if (validateSiret(this.value)) {
                    this.style.borderColor = '#16a34a';
                } else {
                    this.style.borderColor = '#dc2626';
                }
            } else {
                this.style.borderColor = '#ddd';
            }
        });
    }

    // IBAN validation
    const ibanInput = document.getElementById('iban');
    if (ibanInput) {
        ibanInput.addEventListener('input', function () {
            // Remove spaces and convert to uppercase
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });

        ibanInput.addEventListener('blur', function () {
            if (this.value) {
                // Add space every 4 characters for readability
                this.value = this.value.match(/.{1,4}/g).join(' ');
            }
        });
    }

    // BIC validation
    const bicInput = document.getElementById('bic');
    if (bicInput) {
        bicInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    }
}

/**
 * Validate SIRET using Luhn algorithm
 */
function validateSiret(siret) {
    if (siret.length !== 14 || !/^\d+$/.test(siret)) {
        return false;
    }

    let sum = 0;
    for (let i = 0; i < 14; i++) {
        let digit = parseInt(siret[i]);
        if (i % 2 === 1) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }
        sum += digit;
    }

    return sum % 10 === 0;
}

/**
 * Show message notification
 */
function showMessage(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;

    const content = document.querySelector('.settings-content');
    if (content) {
        content.insertBefore(alertDiv, content.firstChild);
        setTimeout(() => alertDiv.remove(), 5000);
    }
}
