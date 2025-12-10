<div class="comment" id="comment-<?= $comment['id'] ?>" style="margin-left: <?= min($depth * 30, 60) ?>px">
    <div class="comment-header">
        <div class="comment-author">
            <div class="comment-avatar">
                <?= strtoupper(substr($comment['author_name'], 0, 1)) ?>
            </div>
            <div class="comment-info">
                <span class="author-name">
                    <?php if (!empty($comment['author_website'])): ?>
                        <a href="<?= esc($comment['author_website']) ?>" target="_blank" rel="nofollow noopener">
                            <?= esc($comment['author_name']) ?>
                        </a>
                    <?php else: ?>
                        <?= esc($comment['author_name']) ?>
                    <?php endif; ?>
                </span>
                <time datetime="<?= $comment['created_at'] ?>">
                    <?= date('d M Y à H:i', strtotime($comment['created_at'])) ?>
                </time>
            </div>
        </div>
        <?php if ($depth < 3): ?>
            <button type="button" class="reply-btn"
                onclick="replyTo('<?= $comment['id'] ?>', '<?= esc($comment['author_name']) ?>')">
                Répondre
            </button>
        <?php endif; ?>
    </div>
    <div class="comment-content">
        <?= nl2br(esc($comment['content'])) ?>
    </div>

    <?php if (!empty($comment['replies'])): ?>
        <div class="comment-replies">
            <?php foreach ($comment['replies'] as $reply): ?>
                <?= view('blog/partials/comment', ['comment' => $reply, 'depth' => $depth + 1]) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>