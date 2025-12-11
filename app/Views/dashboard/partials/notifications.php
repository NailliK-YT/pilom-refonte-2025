<!-- Notifications Section -->
<div class="card notifications-card">
    <div class="card-header">
        <h3 class="card-title">
            Notifications
            <?php if (!empty($notifications)): ?>
                <span class="notification-count"><?= count($notifications) ?></span>
            <?php endif; ?>
        </h3>
        <a href="<?= base_url('notifications/center') ?>" class="card-link">Tout voir</a>
    </div>
    
    <div class="notifications-list" id="notifications-list">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
                <?php
                $iconClass = 'notif-icon-default';
                $icon = '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>';
                
                switch ($notification['type']) {
                    case 'invoice':
                        $iconClass = 'notif-icon-primary';
                        $icon = '<path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>';
                        break;
                    case 'payment':
                        $iconClass = 'notif-icon-success';
                        $icon = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>';
                        break;
                    case 'alert':
                        $iconClass = 'notif-icon-warning';
                        $icon = '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>';
                        break;
                    case 'system':
                        $iconClass = 'notif-icon-info';
                        break;
                }
                
                $priorityClass = '';
                if ($notification['priority'] === 'urgent') $priorityClass = 'notif-urgent';
                elseif ($notification['priority'] === 'high') $priorityClass = 'notif-high';
                ?>
                <div class="notification-item <?= $priorityClass ?>" data-id="<?= esc($notification['id']) ?>">
                    <div class="notif-icon <?= $iconClass ?>">
                        <svg viewBox="0 0 20 20" fill="currentColor"><?= $icon ?></svg>
                    </div>
                    <div class="notif-content">
                        <span class="notif-title"><?= esc($notification['title']) ?></span>
                        <span class="notif-message"><?= esc($notification['message']) ?></span>
                        <span class="notif-time"><?= date('d/m H:i', strtotime($notification['created_at'])) ?></span>
                    </div>
                    <?php if ($notification['link']): ?>
                        <a href="<?= base_url(ltrim($notification['link'], '/')) ?>" class="notif-link">
                            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-notifications">
                <svg viewBox="0 0 20 20" fill="currentColor" class="empty-icon">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>
                <p>Aucune notification</p>
            </div>
        <?php endif; ?>
    </div>
</div>
