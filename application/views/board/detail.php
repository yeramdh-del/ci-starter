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

    <!-- 댓글 영역 (추후 개발 예정) -->
    
    <!-- NOTE: 임시 1차 개발 내용 -->
    <!-- <div class="comment-section" style="margin-top: 40px;">
        <h3>댓글</span></h3>
                
        <form id="commentForm" method="post">
            <input type="hidden" name="board_idx" value="<?= $board_info->idx ?>">
            <div class="form-group" style="margin-bottom:0px;">
                <textarea name="comment" placeholder="댓글을 입력하세요" required style="max-height: 100px;"></textarea>
            </div>
            <button type="submit">댓글 등록</button>
        </form>

        <div class="comment-list" id="comment_list">
            <div class="comment-item">
                <div class="comment-header">
                    <span class="comment-author">작성자명</span>
                    <span class="comment-date">2024-01-15 14:30</span>
                </div>
                <div class="comment-content">
                    댓글 내용이 여기에 표시됩니다.
                </div>
                <div class="comment-actions">
                    <button class="btn-sm">삭제</button>
                </div>
            </div>
        </div>
    </div> -->
    
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

//NOTE: 1차 구축
//     $('#commentForm').on('submit', function(e) {
//         e.preventDefault();

//         var form = $(this)[0];
//         var formData = new FormData(form);

//         $.ajax({
//             url: '<?= site_url("comment/create") ?>',
//             type: 'POST',
//             data: formData,
//             processData: false, // FormData 사용 시 false
//             contentType: false, // FormData 사용 시 false
//             dataType: 'json',
//             success: function(data) {
//                 if (data.success) {

//                     //컴포넌트로 처리
//                     var commentItem = `
//                         <div class="comment-item" data-id="${data.comment.id}">
//                             <div class="comment-header">
//                                 <span class="comment-author">${data.comment.author}</span>
//                                 <span class="comment-date">${data.comment.date}</span>
//                             </div>

//                             <div class="comment-content">
//                                 ${data.comment.content}
//                             </div>

//                             <div class="comment-actions">
//                                 <button class="btn-sm delete-comment-btn" >답글 달기</button>
//                                 <button class="btn-sm delete-comment-btn">삭제</button>
//                             </div>

//                             <form id="subCommentForm" method="post" hidden style="padding-top:10px">
//                                 <input type="hidden" name="board_idx" value="<?= $board_info->idx ?>">
//                                 <div class="form-group" style="margin-bottom:0px;">
//                                     <textarea name="comment" placeholder="댓글을 입력하세요" required style="max-height: 100px;"></textarea>
//                                 </div>
//                                 <button type="submit">댓글 등록</button>
//                             </form>
//                         </div>
//                     `;

//                     $('#comment_list').append(commentItem);
//                     $('#commentForm')[0].reset();
//                 } else {
//                     alert('댓글 등록 실패: ' + data.message);
//                 }
//             },
//             error: function(xhr, status, error) {
//                 console.error('AJAX 오류:', error);
//                 alert('서버 오류 발생');
//             }
//         });
//     });

    $(function () {
        const boardIdx = <?= json_encode($board_info->idx) ?>;

        // 최상위 댓글 불러오기 함수 (페이지네이션 용 더보기)
        function loadTopComments(page = 1) {
            $.ajax({
                url: '<?= site_url("CommentController/load_top_comments") ?>',
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
                url: '<?= site_url("CommentController/create") ?>',
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
                url: '<?= site_url("CommentController/create") ?>',
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
                url: '<?= site_url("CommentController/delete") ?>',
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
                url: '<?= site_url("CommentController/load_replies") ?>',
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

/* 댓글 스타일 (추후 사용) */
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
</style>