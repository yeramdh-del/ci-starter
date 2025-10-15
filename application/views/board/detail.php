<div class="post_box">

    <div style="display: flex; justify-content:space-between">
        <h2>게시글 상세</h2>


        <!-- 본인 계정에서만 수정 삭제 가능하도록 설정 -->
        <?php if( $this->session->userdata('user') && $this->session->userdata('user')->name == $board_info->author): ?>
        <div style="display:flex; gap:2px;">
        <a href="/board/edit/<?= $board_info->idx ?>" class="btn-primary">수정</a>
            <a href="/board/delete/<?= $board_info->idx ?>" class="btn-danger" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
        </div>
        <?php endif;?>

    </div>
    
    <div class="post-detail">
        <div class="form-group">
            <label>제목</label>
            <div class="post-content">
                <?= htmlspecialchars($board_info->title) ?>
            </div>
        </div>

        <div class="form-group">
            <label>작성자</label>
            <div class="post-content">
                <?= htmlspecialchars($board_info->author) ?>
            </div>
        </div>

        <div class="form-group">
            <label>내용</label>
            <div class="post-content content-area">
                <!-- <?= nl2br(htmlspecialchars($board_info->content)) ?> -->
                 <?=$board_info->content?>
            </div>
        </div>

        <div style="display:flex; justify-content: center; gap:5px; margin-top: 20px;">
            <a href="/board" class="btn-cancel">목록</a>
        </div>
    </div>

    <!-- 댓글 영역 (추후 개발 예정) -->
    <!-- 
    <div class="comment-section" style="margin-top: 40px;">
        <h3>댓글 <span class="comment-count">(0)</span></h3>
        
        <form action="/board/comment/create" method="post" class="comment-form">
            <input type="hidden" name="post_idx" value="<?= $board_info->idx ?>">
            <div class="form-group">
                <textarea name="comment" placeholder="댓글을 입력하세요" required></textarea>
            </div>
            <button type="submit" class="btn-primary">댓글 등록</button>
        </form>

        <div class="comment-list">
            <div class="comment-item">
                <div class="comment-header">
                    <span class="comment-author">작성자명</span>
                    <span class="comment-date">2024-01-15 14:30</span>
                </div>
                <div class="comment-content">
                    댓글 내용이 여기에 표시됩니다.
                </div>
                <div class="comment-actions">
                    <button class="btn-sm">수정</button>
                    <button class="btn-sm">삭제</button>
                </div>
            </div>
        </div>
    </div>
    -->
</div>



<style>
.post-detail .post-content {
    padding: 15px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    min-height: 40px;
}

.post-detail .content-area {
    min-height: 400px;
}


.btn-danger:hover {
    background-color: #c82333;
}

/* 댓글 스타일 (추후 사용) */
/*
.comment-section {
    border-top: 2px solid #dee2e6;
    padding-top: 20px;
}

.comment-section h3 {
    margin-bottom: 20px;
}

.comment-count {
    color: #6c757d;
    font-size: 0.9em;
}

.comment-form textarea {
    width: 100%;
    min-height: 80px;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    resize: vertical;
}

.comment-list {
    margin-top: 30px;
}

.comment-item {
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    margin-bottom: 10px;
    background-color: #f8f9fa;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 0.9em;
    color: #6c757d;
}

.comment-author {
    font-weight: bold;
    color: #495057;
}

.comment-content {
    margin-bottom: 10px;
    line-height: 1.6;
}

.comment-actions {
    text-align: right;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.85em;
    background-color: #6c757d;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    margin-left: 5px;
}

.btn-sm:hover {
    background-color: #5a6268;
}
*/
</style>