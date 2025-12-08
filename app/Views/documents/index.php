<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('title') ?>Mes Documents<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb">
            <a href="<?= base_url('documents') ?>">Documents</a>
            <?php foreach ($breadcrumbs as $folder): ?>
                <span>/</span>
                <a href="<?= base_url('documents?folder=' . $folder['id']) ?>"><?= esc($folder['name']) ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
    <div class="page-header-right">
        <a href="<?= base_url('documents/search') ?>" class="btn btn-secondary">
            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
            Rechercher
        </a>
        <a href="<?= base_url('documents/create-folder?parent=' . ($currentFolder['id'] ?? '')) ?>" class="btn btn-secondary">
            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V8a2 2 0 00-2-2h-5L9 4H4zm7 5a1 1 0 10-2 0v1H8a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"/>
            </svg>
            Nouveau dossier
        </a>
        <a href="<?= base_url('documents/upload?folder=' . ($currentFolder['id'] ?? '')) ?>" class="btn btn-primary">
            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            Uploader
        </a>
    </div>
</div>

<div class="documents-grid">
    <!-- Folders -->
    <?php foreach ($folders as $folder): ?>
    <div class="document-card folder">
        <div class="document-icon">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
            </svg>
        </div>
        <div class="document-info">
            <a href="<?= base_url('documents?folder=' . $folder['id']) ?>" class="document-name"><?= esc($folder['name']) ?></a>
            <span class="document-meta">Dossier</span>
        </div>
        <div class="document-actions">
            <form action="<?= base_url('documents/delete-folder/' . $folder['id']) ?>" method="post" onsubmit="return confirm('Supprimer ce dossier?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn-icon btn-danger" title="Supprimer">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Documents -->
    <?php foreach ($documents as $doc): ?>
    <div class="document-card file">
        <div class="document-icon">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="document-info">
            <span class="document-name"><?= esc($doc['name']) ?></span>
            <span class="document-meta"><?= esc($doc['mime_type']) ?> - <?= number_format($doc['size'] / 1024, 1) ?> Ko</span>
        </div>
        <div class="document-actions">
            <a href="<?= base_url('documents/download/' . $doc['id']) ?>" class="btn-icon" title="Télécharger">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
            <button type="button" class="btn-icon" title="Partager" onclick="shareDocument(<?= $doc['id'] ?>)">
                <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                    <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/>
                </svg>
            </button>
            <form action="<?= base_url('documents/delete/' . $doc['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Supprimer ce document?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn-icon btn-danger" title="Supprimer">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($folders) && empty($documents)): ?>
    <div class="empty-state">
        <svg viewBox="0 0 20 20" fill="currentColor" width="48" height="48">
            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
        </svg>
        <p>Aucun document dans ce dossier</p>
        <a href="<?= base_url('documents/upload?folder=' . ($currentFolder['id'] ?? '')) ?>" class="btn btn-primary">Uploader un document</a>
    </div>
    <?php endif; ?>
</div>

<!-- Share Modal -->
<div id="shareModal" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Lien de partage</h3>
        <input type="text" id="shareUrl" readonly class="form-control">
        <div class="modal-actions">
            <button onclick="copyShareUrl()" class="btn btn-primary">Copier</button>
            <button onclick="closeShareModal()" class="btn btn-secondary">Fermer</button>
        </div>
    </div>
</div>

<script>
function shareDocument(docId) {
    fetch('<?= base_url('documents/share/') ?>' + docId, { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (data.url) {
                document.getElementById('shareUrl').value = data.url;
                document.getElementById('shareModal').style.display = 'flex';
            }
        });
}

function copyShareUrl() {
    const input = document.getElementById('shareUrl');
    input.select();
    document.execCommand('copy');
    alert('Lien copié!');
}

function closeShareModal() {
    document.getElementById('shareModal').style.display = 'none';
}
</script>

<style>
.documents-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; padding: 1rem 0; }
.document-card { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-card); border-radius: 8px; border: 1px solid var(--border-color); }
.document-card:hover { border-color: var(--primary); }
.document-icon svg { width: 40px; height: 40px; color: var(--primary); }
.folder .document-icon svg { color: #f59e0b; }
.document-info { flex: 1; min-width: 0; }
.document-name { display: block; font-weight: 500; color: var(--text-primary); text-decoration: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.document-meta { font-size: 0.75rem; color: var(--text-muted); }
.document-actions { display: flex; gap: 0.5rem; }
.btn-icon { padding: 0.5rem; background: none; border: none; cursor: pointer; color: var(--text-muted); border-radius: 4px; }
.btn-icon:hover { background: var(--bg-hover); color: var(--primary); }
.btn-icon.btn-danger:hover { color: var(--danger); }
.modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-content { background: var(--bg-card); padding: 2rem; border-radius: 12px; min-width: 400px; }
.modal-actions { display: flex; gap: 1rem; margin-top: 1rem; justify-content: flex-end; }
.empty-state { grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-muted); }
.empty-state svg { opacity: 0.5; margin-bottom: 1rem; }
</style>
<?= $this->endSection() ?>
