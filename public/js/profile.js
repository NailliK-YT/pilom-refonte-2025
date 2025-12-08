/**
 * Profile & Settings JavaScript
 * Handles photo uploads, password validation, and form interactions
 */

// Base URL helper
const BASE_URL = window.location.origin + '/';

document.addEventListener('DOMContentLoaded', function () {
    // Profile photo upload
    initPhotoUpload();

    // Password strength checker
    initPasswordStrength();

    // Form validation
    initFormValidation();

    // Auto-save indicators
    initAutoSave();
});

/**
 * Initialize profile photo upload
 */
function initPhotoUpload() {
    const photoUpload = document.getElementById('photoUpload');
    const photoPreview = document.getElementById('profilePhotoPreview');
    const deletePhotoBtn = document.getElementById('deletePhoto');

    if (photoUpload) {
        photoUpload.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file
            if (!file.type.match('image.*')) {
                alert('Veuillez sélectionner une image.');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('La taille de l\'image ne doit pas dépasser 2MB.');
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = function (e) {
                if (photoPreview) {
                    photoPreview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);

            // Upload via AJAX
            uploadPhoto(file);
        });
    }

    if (deletePhotoBtn) {
        deletePhotoBtn.addEventListener('click', function () {
            if (!confirm('Voulez-vous vraiment supprimer votre photo de profil ?')) {
                return;
            }

            fetch(BASE_URL + 'profile/delete-photo', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Erreur lors de la suppression.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur de connexion.');
                });
        });
    }
}

/**
 * Upload photo file
 */
function uploadPhoto(file) {
    const formData = new FormData();
    formData.append('photo', file);

    fetch(BASE_URL + 'profile/upload-photo', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Photo mise à jour avec succès !', 'success');
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
 * Initialize password strength checker
 */
function initPasswordStrength() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');

    if (newPassword) {
        newPassword.addEventListener('input', function () {
            const password = this.value;
            const strength = calculatePasswordStrength(password);

            // Update strength bar
            if (strengthFill && strengthText) {
                strengthFill.className = 'strength-fill ' + strength.class;
                strengthText.textContent = strength.text;
            }

            // Update requirements
            updatePasswordRequirements(password);
        });
    }

    if (confirmPassword && newPassword) {
        confirmPassword.addEventListener('input', function () {
            const message = document.getElementById('confirmMessage');
            if (this.value && this.value !== newPassword.value) {
                message.textContent = 'Les mots de passe ne correspondent pas.';
                message.style.color = '#dc2626';
            } else if (this.value === newPassword.value) {
                message.textContent = 'Les mots de passe correspondent.';
                message.style.color = '#16a34a';
            } else {
                message.textContent = '';
            }
        });
    }
}

/**
 * Calculate password strength
 */
function calculatePasswordStrength(password) {
    let score = 0;

    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;

    if (score <= 2) {
        return { class: 'weak', text: 'Faible' };
    } else if (score <= 4) {
        return { class: 'medium', text: 'Moyen' };
    } else {
        return { class: 'strong', text: 'Fort' };
    }
}

/**
 * Update password requirements checklist
 */
function updatePasswordRequirements(password) {
    const requirements = {
        'req-length': password.length >= 8,
        'req-uppercase': /[A-Z]/.test(password),
        'req-lowercase': /[a-z]/.test(password),
        'req-number': /[0-9]/.test(password)
    };

    for (const [id, valid] of Object.entries(requirements)) {
        const element = document.getElementById(id);
        if (element) {
            const icon = element.querySelector('.req-icon');
            if (valid) {
                icon.textContent = '✓';
                icon.classList.add('valid');
                icon.style.color = '#16a34a';
            } else {
                icon.textContent = '⚪';
                icon.classList.remove('valid');
                icon.style.color = '#ccc';
            }
        }
    }
}

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            // Add any custom validation here
        });
    });

    // SIRET real-time formatting
    const siretInput = document.getElementById('siret');
    if (siretInput) {
        siretInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
        });
    }

    // Phone formatting
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('blur', function () {
            this.value = formatPhoneNumber(this.value);
        });
    });

    // IBAN formatting
    const ibanInput = document.getElementById('iban');
    if (ibanInput) {
        ibanInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    }
}

/**
 * Format phone number
 */
function formatPhoneNumber(phone) {
    const cleaned = phone.replace(/\D/g, '');
    if (cleaned.length === 10) {
        return cleaned.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
    }
    return phone;
}

/**
 * Initialize auto-save indicators
 */
function initAutoSave() {
    // Could add auto-save functionality for draft settings
}

/**
 * Show message notification
 */
function showMessage(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;

    const content = document.querySelector('.profile-content, .settings-content');
    if (content) {
        content.insertBefore(alertDiv, content.firstChild);
        setTimeout(() => alertDiv.remove(), 5000);
    }
}
