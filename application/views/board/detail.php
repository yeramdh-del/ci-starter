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
                <strong style="font-size: 13px; color: #007bff;">[<?= $board_info->category_title?>]</strong>
                <?= nl2br(htmlspecialchars($board_info->title)) ?>
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
                <?= nl2br(htmlspecialchars($board_info->content)) ?>
                
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

        <hr style="margin: 10px 0px 0px 0px;">

        <div id="comment_list"><!-- 댓글 리스트가 여기에 로드됩니다 --></div>
        <button id="load_more_top_comments" data-page="1" style="display:none;">더보기</button>
    </div>
    
   
</div>


<script>
    $(function () {
        const boardIdx = <?= json_encode($board_info->idx) ?>;

        //NOTE: 방어코드
        // XSS 방지 함수
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        // 줄바꿈 처리
        function nl2br(text) {
            if (!text) return '';
            return escapeHtml(text).replace(/\n/g, '<br>');
        }

        // 댓글 HTML 생성 함수
        function createCommentHTML(comment) {
            const childrenHTML = comment.children && comment.children.length > 0 ? 
                comment.children.map(child => createCommentHTML(child)).join('') : '';
            
        const loadMoreBtn = `
            <button class="load-more-replies" 
                    data-parent-idx="${comment.idx}" 
                    data-depth="${comment.depth + 1}" 
                    data-page="1"
                    ${comment.has_more_children ? '' : 'disabled'}>
                더보기..
            </button>`;

            return `
                <div class="comment-item" 
                    data-user_idx="${comment.user_idx}" 
                    data-idx="${comment.idx}" 
                    data-depth="${comment.depth}">
                    <div class="comment-header">
                        <strong>${escapeHtml(comment.author)}</strong>
                        <div class="meta">
                            <span>${escapeHtml(comment.created_at)}</span>
                            <div class="delete-btn">X</div>
                        </div>
                    </div>
                    <div class="comment-content">
                        ${nl2br(comment.content)}
                    </div>

                    <div style="display:flex;">
                        <button class="reply-btn" type="button">답글 달기</button>
                        <button class="load-more-replies" 
                            data-parent-idx="${comment.idx}" 
                            data-depth="${comment.depth + 1}" 
                            data-page="1"
                            ${comment.has_more_children ? '' : 'disabled'}>
                            더보기..
                        </button>
                    </div>


                    <form class="subCommentForm" method="post" hidden>
                        <input type="hidden" name="board_idx" value="${comment.board_idx}">
                        <input type="hidden" name="parent_idx" value="${comment.idx}">
                        <textarea name="comment" placeholder="답글을 입력하세요" required></textarea>
                        <button type="submit">등록</button>
                    </form>
                    
                    <div class="children-comments">
                        ${childrenHTML}
                    </div>
                </div>
            `;
        }

        // 최상위 댓글 불러오기 함수
        function loadTopComments(page = 1) {
            $.ajax({
                url: '<?= site_url("Comment/load_top_comments") ?>',
                method: 'GET',
                data: { 
                    board_idx: boardIdx, 
                    page: page 
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#load_more_top_comments').prop('disabled', true).text('로딩중...');
                },
                success: function(res) {
                    if (res.success) {
                        let html = '';
                        res.top_comments.forEach(comment => {
                            html += createCommentHTML(comment);
                        });
                        
                        $('#comment_list').append(html);
                        
                        if (res.has_more) {
                            $('#load_more_top_comments')
                                .data('page', page + 1)
                                .prop('disabled', false)
                                .text('더보기')
                                .show();
                        } else {
                            $('#load_more_top_comments').hide();
                        }
                    } else {
                        alert(res.message || '댓글 불러오기 실패');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('서버 오류 발생');
                    $('#load_more_top_comments').prop('disabled', false).text('더보기');
                }
            });
        }

        // 대댓글 더보기 함수
        function loadMoreReplies(parentIdx, depth, page, $button) {
            $.ajax({
                url: '<?= site_url("Comment/load_replies") ?>',
                method: 'GET',
                data: {
                    parent_idx: parentIdx,
                    depth: depth,
                    page: page
                },
                dataType: 'json',
                beforeSend: function() {
                    $button.prop('disabled', true).text('로딩중...');
                },
                success: function(res) {
                    if (res.success) {
                        let html = '';
                        res.children.forEach(child => {
                            html += createCommentHTML(child);
                        });
                        
                        // 대댓글을 부모 댓글의 children-comments 영역에 추가
                        $button.closest('.comment-item')
                            .find('> .children-comments')
                            .append(html);
                        
                        if (res.has_more) {
                            // 다음 페이지로 업데이트
                            $button
                                .data('page', page + 1)
                                .prop('disabled', false)
                                .text('더보기..');
                        } else {
                            // 더 이상 댓글이 없으면 버튼 제거
                            $button.remove();
                        }
                    } else {
                        alert(res.message || '답글 불러오기 실패');
                        $button.prop('disabled', false).text('더보기..');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('서버 오류 발생');
                    $button.prop('disabled', false).text('더보기..');
                }
            });
        }

        // 초기 최상위 댓글 로드
        loadTopComments(1);

        // 최상위 댓글 등록
        $('#commentForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajax({
                url: '<?= site_url("Comment/add") ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
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



        // 최상위 댓글 더보기 버튼 클릭
        $('#load_more_top_comments').on('click', function() {
            const page = $(this).data('page') || 1;
            loadTopComments(page);
        });

        // 대댓글 더보기 버튼 클릭 (동적 요소용 이벤트 위임)
        $(document).on('click', '.load-more-replies', function() {
            const $button = $(this);
            const parentIdx = $button.data('parent-idx');
            const depth = $button.data('depth');
            const page = $button.data('page') || 1;
            
            loadMoreReplies(parentIdx, depth, page, $button);
        });

        // 답글 달기 버튼 클릭
        $(document).on('click', '.reply-btn', function() {
            const $form =  $(this).closest('.comment-item').find('> .subCommentForm');
            $form.toggle();
            
            if ($form.is(':visible')) {
                $form.find('textarea').focus();
            }
        });

          // 답글 등록 폼 제출
        $(document).on('submit', '.subCommentForm', function(e) {
            e.preventDefault();
            const $form = $(this);
            const formData = $form.serialize();
            
            $.ajax({
                url: '<?= site_url("Comment/add") ?>',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {

                        // const newCommentHTML = createCommentHTML(res.comment);
                        // $childrenContainer.append(newCommentHTML);

                        // 전체 댓글 리로드
                        $('#comment_list').empty();
                        $('#load_more_top_comments').hide();
                        loadTopComments(1);
        
                        
                        // 폼 초기화 및 숨김
                        $form[0].reset();
                        $form.hide();
                    } else {
                        alert(res.message || '답글 등록 실패');
                    }
                },
                error: function() {
                    alert('서버 오류 발생');
                }
            });
        });


        // 댓글 삭제
        $(document).on('click', '.delete-btn', function() {
            if (!confirm('댓글을 삭제하시겠습니까?')) return;
            
            const $commentItem = $(this).closest('.comment-item');
            const commentIdx = $commentItem.data('idx');

            $.ajax({
                url: '<?= site_url("Comment/delete") ?>',
                method: 'POST',
                data: { idx: commentIdx , page:$(this).data('page')},
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $commentItem.fadeOut(300, function() {
                            $(this).remove();
                        });
                        // alert('댓글이 삭제되었습니다.');
                    } else {
                        alert(res.message || '댓글 삭제 실패');
                    }
                },
                error: function() {
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
    border: 1px solid #ccc;
    border-radius: 3px;
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