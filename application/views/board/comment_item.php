<div class="comment-item" data-user_idx = <?=$comment['user_idx']?> data-idx="<?= $comment['idx'] ?>" data-depth="<?= $comment['depth'] ?>" style="margin-left: 30px; border-left: 1px solid #ccc; padding-left: 10px; margin-bottom: 10px;">
    <div class="comment-header">
        <strong><?= htmlspecialchars($comment['author']) ?></strong>
        <div style="display:flex; gap:3px;">
            <span style="color:#777; font-size: 0.9em; margin-left:10px;"><?= $comment['created_at'] ?></span>
             <div class="delete-btn" style="cursor:pointer;">X</div>
        </div>
    </div>

    <div class="comment-content" style="margin: 5px 0;">
        <?= nl2br(htmlspecialchars($comment['content'])) ?>
    </div>

    <button class="reply-btn" type="button">답글 달기</button>

    <!-- 대댓글 등록 폼 -->
    <form class="subCommentForm" method="post" hidden style="margin-top:5px;">
        <input type="hidden" name="board_idx" value="<?= $comment['board_idx'] ?>">
        <textarea name="comment" placeholder="답글을 입력하세요" required style="width:100%; max-height: 100px;"></textarea>
        <button type="submit">등록</button>
    </form>

    <!-- 자식 댓글 리스트 -->
    <?php if (!empty($comment['children'])): ?>
        <?php foreach ($comment['children'] as $child): ?>
            <?php $this->load->view('/board/comment_item', ['comment' => $child]); ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- 대댓글 더보기 버튼 -->
    <?php if (!empty($comment['has_more_children'])): ?>
        <button class="load-more-replies" 
                data-parent-idx="<?= $comment['idx'] ?>" 
                data-depth="<?= $comment['depth'] + 1 ?>" 
                data-page="1">
            더보기..
        </button>

    <?php endif; ?>
</div>


