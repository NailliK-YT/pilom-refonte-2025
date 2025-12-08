/**
 * Pilom Registration Form JavaScript
 * Handles real-time validation, auto-save, and form interactions
 */

class RegistrationForm {
    constructor(step) {
        this.step = step;
        this.debounceTimers = {};
        this.autoSaveInterval = null;
    }

    init() {
        if (this.step === 1) {
            this.initStep1();
        } else if (this.step === 2) {
            this.initStep2();
        } else if (this.step === 3) {
            this.initStep3();
        }

        // Start auto-save (every 30 seconds)
        this.startAutoSave();
    }

    // Step 1: Account Information
    initStep1() {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirm');

        if (emailInput) {
            emailInput.addEventListener('blur', () => this.validateEmail());
            emailInput.addEventListener('input', () => {
                this.debounce('email', () => this.validateEmail(), 500);
            });
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', () => {
                this.validatePassword();
                if (passwordConfirmInput.value) {
                    this.validatePasswordConfirm();
                }
            });
        }

        if (passwordConfirmInput) {
            passwordConfirmInput.addEventListener('input', () => {
                this.validatePasswordConfirm();
            });
        }
    }

    // Validate email via AJAX
    async validateEmail() {
        const emailInput = document.getElementById('email');
        const validationMsg = document.getElementById('emailValidation');
        
        if (!emailInput || !validationMsg) return;

        const email = emailInput.value.trim();

        if (!email) {
            validationMsg.textContent = '';
            emailInput.classList.remove('error', 'success');
            return;
        }

        // Basic format check
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            validationMsg.textContent = 'Format d\'email invalide';
            validationMsg.className = 'validation-message error';
            emailInput.classList.add('error');
            emailInput.classList.remove('success');
            return;
        }

        try {
            const response = await fetch('/register/validate-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `email=${encodeURIComponent(email)}`
            });

            const data = await response.json();

            if (data.valid) {
                validationMsg.textContent = '✓ ' + data.message;
                validationMsg.className = 'validation-message success';
                emailInput.classList.remove('error');
                emailInput.classList.add('success');
            } else {
                validationMsg.textContent = data.message;
                validationMsg.className = 'validation-message error';
                emailInput.classList.add('error');
                emailInput.classList.remove('success');
            }
        } catch (error) {
            console.error('Email validation error:', error);
        }
    }

    // Validate password strength
    async validatePassword() {
        const passwordInput = document.getElementById('password');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');

        if (!passwordInput || !strengthFill || !strengthText) return;

        const password = passwordInput.value;

        if (!password) {
            strengthFill.style.width = '0%';
            strengthFill.className = 'strength-fill';
            strengthText.textContent = '';
            return;
        }

        try {
            const response = await fetch('/register/validate-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `password=${encodeURIComponent(password)}`
            });

            const data = await response.json();

            strengthFill.style.width = data.strength + '%';

            if (data.strength < 50) {
                strengthFill.className = 'strength-fill weak';
                strengthText.textContent = 'Faible - ' + data.messages.join(', ');
                passwordInput.classList.add('error');
                passwordInput.classList.remove('success');
            } else if (data.strength < 100) {
                strengthFill.className = 'strength-fill medium';
                strengthText.textContent = 'Moyen - Ajoutez: ' + data.messages.join(', ');
                passwordInput.classList.remove('error', 'success');
            } else {
                strengthFill.className = 'strength-fill strong';
                strengthText.textContent = 'Fort';
                passwordInput.classList.remove('error');
                passwordInput.classList.add('success');
            }
        } catch (error) {
            console.error('Password validation error:', error);
        }
    }

    // Validate password confirmation
    validatePasswordConfirm() {
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirm');
        const validationMsg = document.getElementById('passwordConfirmValidation');

        if (!passwordInput || !passwordConfirmInput || !validationMsg) return;

        const password = passwordInput.value;
        const passwordConfirm = passwordConfirmInput.value;

        if (!passwordConfirm) {
            validationMsg.textContent = '';
            passwordConfirmInput.classList.remove('error', 'success');
            return;
        }

        if (password === passwordConfirm) {
            validationMsg.textContent = '✓ Les mots de passe correspondent';
            validationMsg.className = 'validation-message success';
            passwordConfirmInput.classList.remove('error');
            passwordConfirmInput.classList.add('success');
        } else {
            validationMsg.textContent = 'Les mots de passe ne correspondent pas';
            validationMsg.className = 'validation-message error';
            passwordConfirmInput.classList.add('error');
            passwordConfirmInput.classList.remove('success');
        }
    }

    // Step 2: Business Sector
    initStep2() {
        // Sector selection is handled in the HTML inline script
        // No additional initialization needed here
    }

    // Step 3: Review and Confirm
    initStep3() {
        const form = document.getElementById('step3Form');
        const submitBtn = form?.querySelector('button[type="submit"]');

        if (form && submitBtn) {
            form.addEventListener('submit', (e) => {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });
        }
    }

    // Auto-save functionality
    startAutoSave() {
        // Save every 30 seconds
        this.autoSaveInterval = setInterval(() => {
            this.saveProgress();
        }, 30000);
    }

    stopAutoSave() {
        if (this.autoSaveInterval) {
            clearInterval(this.autoSaveInterval);
        }
    }

    // Save current progress
    async saveProgress() {
        const formData = this.collectFormData();

        if (!formData || Object.keys(formData).length === 0) {
            return; // Nothing to save
        }

        try {
            const response = await fetch('/register/save-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `step=${this.step}&data=${encodeURIComponent(JSON.stringify(formData))}`
            });

            const data = await response.json();

            if (data.success) {
                console.log('Progress saved:', data.message);
            }
        } catch (error) {
            console.error('Auto-save error:', error);
        }
    }

    // Collect form data based on current step
    collectFormData() {
        const data = {};

        if (this.step === 1) {
            const email = document.getElementById('email')?.value;
            const companyName = document.getElementById('company_name')?.value;
            const password = document.getElementById('password')?.value;

            if (email) data.email = email;
            if (companyName) data.company_name = companyName;
            if (password) data.password = password;
        } else if (this.step === 2) {
            const sectorId = document.getElementById('selectedSector')?.value;
            if (sectorId) data.business_sector_id = sectorId;
        }

        return data;
    }

    // Debounce utility
    debounce(key, func, wait) {
        if (this.debounceTimers[key]) {
            clearTimeout(this.debounceTimers[key]);
        }

        this.debounceTimers[key] = setTimeout(() => {
            func();
            delete this.debounceTimers[key];
        }, wait);
    }
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.registrationForm) {
        window.registrationForm.stopAutoSave();
    }
});
