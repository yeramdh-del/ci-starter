<!-- jQuery CDN 추가 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


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

    <!-- 댓글 영역 -->
    <div class="comment-section">
        <h3>댓글</h3>

        <form id="commentForm" method="post">
            <input type="hidden" name="board_idx" value="<?= $board_info->idx ?>">
            <textarea name="comment" placeholder="댓글을 입력하세요" required></textarea>
            <button type="submit">댓글 등록</button>
        </form>

        <hr>

        <div id="comment_list"><!-- 댓글 리스트가 여기에 로드됩니다 --></div>
        <button id="load_more_top_comments" data-page="1" style="display:none;">더보기</button>
    </div>
    
   
</div>


<script>


    $(function () {
        const boardIdx = <?= json_encode($board_info->idx) ?>;

        // 최상위 댓글 불러오기 함수 (페이지네이션 용 더보기)
        function loadTopComments(page = 1) {
            $.ajax({
                url: '<?= site_url("Comment/load_top_comments") ?>',
                method: 'GET',
                data: { board_idx: boardIdx, page: page },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#comment_list').append(res.html);
                        if (res.has_more) {
                            $('#load_more_top_comments').data('page', page + 1).show();
                        } else {
                            $('#load_more_top_comments').hide();
                        }
                    } else {
                        alert('댓글 불러오기 실패');
                    }
                },
                error: function() {
                    alert('서버 오류 발생');
                }
            });
        }

        loadTopComments();

        $('#load_more_top_comments').on('click', function() {
            const nextPage = $(this).data('page');
            loadTopComments(nextPage);
        });

        // 최상위 댓글 등록
        $('#commentForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajax({
                url: '<?= site_url("Comment/create") ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        // alert('댓글이 등록되었습니다.');
                        $('#comment_list').empty();
                        $('#load_more_top_comments').hide();
                        loadTopComments();
                        $('#commentForm')[0].reset();
                    } else {
                        alert(res.message);
                    }
                },
                error: function() {
                    alert('서버 오류 발생');
                }
            });
        });

        // 답글 폼 토글
        $(document).on('click', '.reply-btn', function () {
            $(this).siblings('.subCommentForm').toggle();
        });

        // 대댓글 등록
        $(document).on('submit', '.subCommentForm', function (e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            const commentItem = $(form).closest('.comment-item');
            const parentIdx = commentItem.data('idx');
            const depth = parseInt(commentItem.data('depth')) + 1;

            formData.append('parent_idx', parentIdx);
            formData.append('depth', depth);

            $.ajax({
                url: '<?= site_url("Comment/create") ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        // alert('답글이 등록되었습니다.');
                        $('#comment_list').empty();
                        $('#load_more_top_comments').hide();
                        loadTopComments();
                    } else {
                        alert(res.message);
                    }
                },
                error: function () {
                    alert('서버 오류 발생');
                }
            });
        });


        //NOTE: 댓글 - 댓글 삭제
        $(document).on('click', '.delete-btn', function(){
            if(!confirm("정말삭제하시겠습니까?")) return;

            
            const commentItem = $(this).closest('.comment-item');
            const commentIdx = commentItem.data('idx');
            const commentUserIdx = commentItem.data('user_idx');
            
            $.ajax({
                url: '<?= site_url("Comment/delete") ?>',
                method : 'POST',
                data:  {
                    user_idx : commentUserIdx,
                    idx : commentIdx,
                },
                dataType: 'json',
                success:function (res) {
                    
                    if(res.success){
                        commentItem.remove(); // 화면에서 제거
                    }else{
                        alert(res.message);
                    }
                },
                error: function() {
                   alert('서버 오류가 발생했습니다.');
                }
            });

        });
        

        // 대댓글 더보기 클릭
        $(document).on('click', '.load-more-replies', function () {
            const $btn = $(this);
            const parentIdx = $btn.data('parent-idx');
            const page = $btn.data('page');

            $.ajax({
                url: '<?= site_url("Comment/load_replies") ?>',
                method: 'GET',
                data: { parent_idx: parentIdx, page: page },
                dataType: 'json',
                success: function (res) {
                    if (res.success && res.html) {
                        $btn.before(res.html);
                        $btn.data('page', page + 1);
                        if (!res.has_more) {
                            $btn.remove();
                        }
                    } else {
                        $btn.remove();
                    }
                },
                error: function () {
                    alert('서버 오류 발생');
                }
            });
        });
        
    });

</script>

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



/* 댓글 전체 영역 */
.comment-section {
    margin-top: 30px;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
}

.comment-section h3 {
    margin-bottom: 15px;
}

/* 댓글 작성 폼 */
#commentForm textarea {
    width: 100%;
    height: 80px;
    resize: none;
    padding: 10px;
    font-size: 14px;
    border-radius: 5px;
    border: 1px solid #ccc;
    margin-bottom: 10px;
}

#commentForm button {
    padding: 8px 16px;
    font-size: 14px;
    background-color: #333;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

/* 댓글 아이템 */
.comment-item {
    border-left: 2px solid #ccc;
    padding-left: 15px;
    margin-bottom: 20px;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    margin-bottom: 5px;
}

.comment-header strong {
    font-weight: bold;
}

.comment-header .meta {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #888;
    font-size: 0.9em;
}

.delete-btn {
    color: #999;
    font-weight: bold;
    cursor: pointer;
    padding: 2px 5px;
}

.delete-btn:hover {
    color: red;
}

/* 댓글 내용 */
.comment-content {
    font-size: 14px;
    line-height: 1.4;
    margin: 5px 0 10px 0;
}

/* 대댓글 등록폼 */
.subCommentForm {
    margin-top: 10px;
}

.subCommentForm textarea {
    width: 100%;
    max-height: 100px;
    resize: none;
    padding: 8px;
    font-size: 13px;
    border-radius: 5px;
    border: 1px solid #ccc;
    margin-bottom: 5px;
}

.subCommentForm button {
    padding: 5px 10px;
    font-size: 13px;
    background-color: #555;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

/* 버튼 */
.reply-btn, .load-more-replies, #load_more_top_comments {
    padding: 5px 10px;
    font-size: 13px;
    background-color: #eee;
    border: 1px solid #ccc;
    border-radius: 3px;
    cursor: pointer;
    margin-top: 5px;
}

.reply-btn:hover, .load-more-replies:hover, #load_more_top_comments:hover {
    background-color: #ddd;
}


</style>