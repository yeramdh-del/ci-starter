<!-- NOTE: 현재 사용 X -->
<!-- <div class="comment-item" 
    data-user_idx="<?= $comment['user_idx'] ?>" 
    data-idx="<?= $comment['idx'] ?>"
    data-depth="<?= $comment['depth'] ?>"
    >
    
    <div class="comment-header">
        <strong><?= htmlspecialchars($comment['author']) ?></strong>
        <div class="meta">
            <span><?= $comment['created_at'] ?></span>
            <div class="delete-btn">X</div>
        </div>
    </div>

    <div class="comment-content">
        <?= nl2br(htmlspecialchars($comment['content'])) ?>
    </div>

    <button class="reply-btn" type="button">답글 달기</button>

    <!-- 대댓글 등록 폼 -->
    <form class="subCommentForm" method="post" hidden>
        <input type="hidden" name="board_idx" value="<?= $comment['board_idx'] ?>">
        <textarea name="comment" placeholder="답글을 입력하세요" required></textarea>
        <button type="submit">등록</button>
    </form>

    <!-- 자식 댓글 리스트 -->
    <?php if (!empty($comment['children'])): ?>
        <?php foreach ($comment['children'] as $child): ?>
            <?php $this->load->view('/board/comment_item', ['comment' => $child]); ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- 대댓글 더보기 -->
    <?php if (!empty($comment['has_more_children'])): ?>
        <button class="load-more-replies" 
                data-parent-idx="<?= $comment['idx'] ?>" 
                data-depth="<?= $comment['depth'] + 1 ?>"
                data-page="1">
            더보기..
        </button>
    <?php endif; ?>
</div>

 -->
