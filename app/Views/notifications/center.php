<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="header-left">
        <h1>Centre de notifications</h1>
        <?php if ($unreadCount > 0): ?>
            <span class="unread-badge"><?= $unreadCount ?> non lue<?= $unreadCount > 1 ? 's' : '' ?></span>
        <?php endif; ?>
    </div>
    <div class="header-actions">
        <?php if ($unreadCount > 0): ?>
            <button onclick="markAllAsRead()" class="btn btn-outline">
                Tout marquer comme lu
            </button>
        <?php endif; ?>
        <a href="<?= base_url('notifications/preferences') ?>" class="btn btn-outline">
            <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
            </svg>
            Préférences
        </a>
    </div>
</div>

<div class="notifications-container">
    <?php if (empty($notifications)): ?>
        <div class="empty-state">
            <div class="empty-icon">🔔</div>
            <h3>Aucune notification</h3>
            <p>Vous n'avez pas encore de notifications.</p>
        </div>
    <?php else: ?>
        <div class="notifications-list">
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>" 
                     data-id="<?= $notification['id'] ?>">
                    <div class="notification-icon <?= $notification['type'] ?>">
                        <?php
                        $icons = [
                            'invoice' => '📄',
                            'quote' => '📝',
                            'payment' => '💰',
                            'system' => 'ℹ️',
                            'alert' => '⚠️',
                            'reminder' => '⏰'
                        ];
                        echo $icons[$notification['type']] ?? '🔔';
                        ?>
                    </div>
                    
                    <div class="notification-content">
                        <div class="notification-header">
                            <h4 class="notification-title"><?= esc($notification['title']) ?></h4>
                            <span class="notification-time">
                                <?= humanizeTime($notification['created_at']) ?>
                            </span>
                        </div>
                        <p class="notification-message"><?= esc($notification['message']) ?></p>
                        
                        <div class="notification-actions">
                            <?php if (!empty($notification['link'])): ?>
                                <a href="<?= base_url($notification['link']) ?>" class="btn btn-sm btn-primary">
                                    Voir
                                </a>
                            <?php endif; ?>
                            
                            <?php if (!$notification['is_read']): ?>
                                <button onclick="markAsRead('<?= $notification['id'] ?>')" class="btn btn-sm btn-outline">
                                    Marquer comme lu
                                </button>
                            <?php endif; ?>
                            
                            <form action="<?= base_url('notifications/delete/' . $notification['id']) ?>" method="post" 
                                  style="display: inline;" onsubmit="return confirm('Supprimer cette notification ?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-text">Supprimer</button>
                            </form>
                        </div>
                    </div>
                    
                    <?php if ($notification['priority'] === 'urgent'): ?>
                        <span class="priority-badge urgent">Urgent</span>
                    <?php elseif ($notification['priority'] === 'high'): ?>
                        <span class="priority-badge high">Important</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.unread-badge {
    background: #ef4444;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    margin-left: 15px;
}

.notifications-container {
    max-width: 800px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.notification-item {
    display: flex;
    gap: 16px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    position: relative;
    transition: box-shadow 0.2s;
}

.notification-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.notification-item.unread {
    border-left: 4px solid #4E51C0;
    background: #fafbff;
}

.notification-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.5rem;
    flex-shrink: 0;
    background: #f3f4f6;
}

.notification-icon.invoice { background: #dbeafe; }
.notification-icon.payment { background: #d1fae5; }
.notification-icon.alert { background: #fef3c7; }
.notification-icon.system { background: #e0e7ff; }

.notification-content {
    flex: 1;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.notification-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.notification-time {
    font-size: 0.75rem;
    color: #9ca3af;
    flex-shrink: 0;
}

.notification-message {
    font-size: 0.875rem;
    color: #4b5563;
    line-height: 1.5;
    margin: 0 0 12px;
}

.notification-actions {
    display: flex;
    gap: 8px;
}

.priority-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.priority-badge.urgent {
    background: #fee2e2;
    color: #991b1b;
}

.priority-badge.high {
    background: #fef3c7;
    color: #92400e;
}

.btn-text {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 4px 8px;
}

.btn-text:hover {
    color: #ef4444;
}
</style>

<script>
function markAsRead(id) {
    fetch(`<?= base_url('notifications/mark-read/') ?>${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`[data-id="${id}"]`);
            item.classList.remove('unread');
            item.classList.add('read');
            location.reload(); // Refresh to update count
        }
    });
}

function markAllAsRead() {
    fetch('<?= base_url('notifications/mark-all-read') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>

<?php
// Helper function for human-readable time
function humanizeTime($datetime) {
    $now = time();
    $time = strtotime($datetime);
    $diff = $now - $time;
    
    if ($diff < 60) return 'À l\'instant';
    if ($diff < 3600) return floor($diff / 60) . ' min';
    if ($diff < 86400) return floor($diff / 3600) . ' h';
    if ($diff < 604800) return floor($diff / 86400) . ' j';
    return date('d/m/Y', $time);
}
?>

<?= $this->endSection() ?>
