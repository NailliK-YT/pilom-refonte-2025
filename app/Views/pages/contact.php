<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Contactez-nous</h1>
    <p class="page-subtitle">Une question ? Nous sommes là pour vous aider.</p>
</div>

<div class="contact-content">
    <div class="contact-grid">
        <div class="contact-info">
            <h2>Informations de contact</h2>
            
            <div class="contact-item">
                <div class="contact-icon">📧</div>
                <div>
                    <h3>Email</h3>
                    <p><a href="mailto:contact@pilom.fr">contact@pilom.fr</a></p>
                </div>
            </div>
            
            <div class="contact-item">
                <div class="contact-icon">📞</div>
                <div>
                    <h3>Téléphone</h3>
                    <p>+33 1 23 45 67 89</p>
                    <small>Du lundi au vendredi, 9h-18h</small>
                </div>
            </div>
            
            <div class="contact-item">
                <div class="contact-icon">📍</div>
                <div>
                    <h3>Adresse</h3>
                    <p>123 Avenue des Entrepreneurs<br>75001 Paris, France</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">⏱️</div>
                <div>
                    <h3>Temps de réponse</h3>
                    <p>Nous répondons généralement sous 24h ouvrées.</p>
                </div>
            </div>
        </div>

        <div class="contact-form-container">
            <h2>Envoyez-nous un message</h2>
            
            <form action="<?= base_url('contact') ?>" method="post" class="contact-form">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="name">Nom complet <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?= old('name') ?>" required placeholder="Votre nom">
                    <?php if (isset($validation) && $validation->hasError('name')): ?>
                        <span class="error-text"><?= $validation->getError('name') ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?= old('email') ?>" required placeholder="votre@email.com">
                    <?php if (isset($validation) && $validation->hasError('email')): ?>
                        <span class="error-text"><?= $validation->getError('email') ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="subject">Sujet <span class="required">*</span></label>
                    <input type="text" id="subject" name="subject" class="form-control" 
                           value="<?= old('subject') ?>" required placeholder="Objet de votre message">
                    <?php if (isset($validation) && $validation->hasError('subject')): ?>
                        <span class="error-text"><?= $validation->getError('subject') ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="message">Message <span class="required">*</span></label>
                    <textarea id="message" name="message" class="form-control" rows="6" 
                              required placeholder="Votre message..."><?= old('message') ?></textarea>
                    <?php if (isset($validation) && $validation->hasError('message')): ?>
                        <span class="error-text"><?= $validation->getError('message') ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    Envoyer le message
                </button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
