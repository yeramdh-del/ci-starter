
<!-- jQuery CDN 추가 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="post_box">
    <div style="display: flex; justify-content:space-between">
        <h2>게시글 상세</h2>

        <!-- 본인 계정에서만 수정 삭제 가능하도록 설정 -->
        <?php if($this->session->userdata('user') && $this->session->userdata('user')->name == $board_info->author): ?>
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

        <!-- 최상위 댓글 작성 폼 -->
        <form id="commentForm" method="post">
            <input type="hidden" name="board_idx" value="<?= $board_info->idx ?>">
            <textarea name="comment" placeholder="댓글을 입력하세요" required></textarea>
            <button type="submit">댓글 등록</button>
        </form>

        <hr style="margin: 10px 0px 0px 0px;">

        <!-- 댓글 리스트 -->
        <div id="comment_list"></div>

        <!-- 페이지 네이션 -->
         <div id="pagination"></div>
    </div>
</div>

<script>
$(function () {
    const boardIdx = <?= json_encode($board_info->idx) ?>;
    const currentUser = <?= json_encode($this->session->userdata('user') ? $this->session->userdata('user')->name : null) ?>;
    let currentPage = 0;
    const limit = 10; // 서버에서 설정한 MAX_LIST_NUMBER와 동일해야 함

    

    let isLoading = false;

    function runAsyncTask(asyncFunc) {

        if (isLoading) return;
        isLoading = true;

        const jqXHR = asyncFunc();

        jqXHR.fail(err => {
            console.error(err);
            alert('서버 오류 발생');
        }).always(() => {
            isLoading = false;
        });
    }

    
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
        const indent = comment.depth * 15; // depth에 따라 30px씩 들여쓰기
        const isAuthor = currentUser && currentUser === comment.author;
        
        let html = `
            <div class="comment-item" data-idx="${comment.idx}" style="margin-left: ${indent}px;">
                <div class="comment-header">
                    <div>
                        <strong>${escapeHtml(comment.author)}</strong>
                        ${comment.depth > 0 ? '<span class="reply-badge">↳</span>' : ''}
                    </div>
                    <div class="meta">
                        <span>${comment.created_at}</span>
                        ${isAuthor ? `<span class="delete-btn" data-idx="${comment.idx}">삭제</span>` : ''}
                    </div>
                </div>
                <div class="comment-content">
                    <p>${nl2br(comment.content)}</p>
                </div>
                <button class="reply-btn" data-idx="${comment.idx}" data-root="${comment.root_parent_idx || comment.idx}" data-depth="${comment.depth}">
                    답글 달기
                </button>
                
                <!-- 답글 작성 폼 -->
                <form class="subCommentForm" style="display:none;">
                    <input type="hidden" name="board_idx" value="${boardIdx}">
                    <input type="hidden" name="parent_idx" value="${comment.idx}">
                    <input type="hidden" name="root_parent_idx" value="${comment.root_parent_idx || comment.idx}">
                    <textarea name="comment" placeholder="답글을 입력하세요" required></textarea>
                    <button type="submit">답글 등록</button>
                    <button type="button" class="cancel-reply-btn">취소</button>
                </form>
            </div>
        `;
        
        return html;
    }


    // 전체 댓글 불러오기
    function loadAllComments(pages = 0 ) {
        
        $.ajax({
            url: '<?= site_url("V2_comment/get_list") ?>',
            method: 'GET',
            data: { board_idx: boardIdx , pages:pages },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    let html = '';
                    const comments = res.data.list;
                    const pages = res.data.pages;
                    const limit = res.data.limit;
                    const total = res.data.total;                    
                    const totalPages = Math.ceil(total / limit);


                    comments.forEach(comment => {
                        html += createCommentHTML(comment);
                    });
                    $('#comment_list').html(html);

                    // 페이지네이션 생성
                    generatePagination(totalPages, pages);
                    currentPage = pages;
                } else {
                    $('#comment_list').html('<p style="text-align:center; color:#999;">댓글이 없습니다.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('댓글을 불러오는데 실패했습니다.');
            }
        });
    }

    // 초기 댓글 로드
    loadAllComments();



    // 페이지네이션 버튼 생성 함수
    function generatePagination(totalPages, currentPage) {
        let paginationHtml = '';

        if (totalPages <= 1) {
            $('#pagination').empty();
            return;
        }

        // 이전 버튼
        if (currentPage > 0) {
            paginationHtml += `<button class="pagination-btn" data-page="${currentPage - 1}">« 이전</button> `;
        }

        // 페이지 번호 버튼
        for (let i = 0; i < totalPages; i++) {
            if (i === currentPage) {
                paginationHtml += `<button style="font-weight:bold;" disabled>${i + 1}</button> `;
            } else {
                paginationHtml += `<button class="pagination-btn" data-page="${i}">${i + 1}</button> `;
            }
        }

        // 다음 버튼
        if (currentPage < totalPages - 1) {
            paginationHtml += `<button class="pagination-btn" data-page="${currentPage + 1}">다음 »</button>`;
        }

        $('#pagination').html(paginationHtml);
    }
    // 페이지네이션 버튼 클릭 처리
    $(document).on('click', '.pagination-btn', function() {
        const page = $(this).data('page');
        loadAllComments(page);
    });


 

    // 최상위 댓글 등록
    $('#commentForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        runAsyncTask(()=>{
            return $.ajax({
                url: '<?= site_url("V2_comment/add") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        loadAllComments();
                        $('#commentForm')[0].reset();
                    } else {
                        alert(res.message || '댓글 등록 실패');
                    }
                },
                error: function() {
                    alert('서버 오류 발생');
                }
            });
        });

    });

    // 답글 달기 버튼 클릭
    $(document).on('click', '.reply-btn', function() {
        // 다른 답글 폼 모두 숨김
        $('.subCommentForm').hide();
        
        const $form = $(this).siblings('.subCommentForm');
        $form.show();
        $form.find('textarea').focus();
    });

    // 답글 취소 버튼
    $(document).on('click', '.cancel-reply-btn', function() {
        $(this).closest('.subCommentForm').hide();
    });

    // 답글 등록
    $(document).on('submit', '.subCommentForm', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        runAsyncTask(()=>{
                return $.ajax({
                url: '<?= site_url("V2_comment/add") ?>',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        loadAllComments();
                    } else {
                        alert(res.message || '답글 등록 실패');
                    }
                },
                error: function() {
                    alert('서버 오류 발생');
                }
            });
        });   
    });

    // 댓글 삭제
    $(document).on('click', '.delete-btn', function() {
        if (!confirm('댓글을 삭제하시겠습니까?')) return;
        
        const commentIdx = $(this).data('idx');

        runAsyncTask(()=>{
            return $.ajax({
            url: '<?= site_url("V2_comment/delete") ?>',
            method: 'POST',
            data: { idx: commentIdx },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    loadAllComments();
                } else {
                    alert(res.message || '댓글 삭제 실패');
                }
            },
            error: function() {
                alert('서버 오류 발생');
            }
        });
        })
        
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

#commentForm button:hover {
    background-color: #555;
}

/* 댓글 아이템 */
.comment-item {
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
    transition: all 0.3s ease;
}

.comment-item:hover {
    background-color: #f5f5f5;
}

/* 답글 뱃지 */
.reply-badge {
    color: #007bff;
    font-size: 0.9em;
    margin-left: 5px;
}

/* 댓글 헤더 */
.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    margin-bottom: 8px;
}

.comment-header strong {
    font-weight: bold;
    color: #333;
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
    transition: color 0.2s;
}

.delete-btn:hover {
    color: #ff4444;
}

/* 댓글 내용 */
.comment-content {
    font-size: 14px;
    line-height: 1.6;
    margin: 8px 0 10px 0;
    padding: 10px;
    background-color: white;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
}

.comment-content p {
    margin: 0;
    word-wrap: break-word;
}

/* 답글 달기 버튼 */
.reply-btn {
    padding: 5px 12px;
    font-size: 13px;
    background-color: #fff;
    color: #007bff;
    border: 1px solid #007bff;
    border-radius: 3px;
    cursor: pointer;
    transition: all 0.2s;
}

.reply-btn:hover {
    background-color: #007bff;
    color: white;
}

/* 답글 작성 폼 */
.subCommentForm {
    margin-top: 10px;
    padding: 10px;
    background-color: #f0f7ff;
    border-radius: 5px;
}

.subCommentForm textarea {
    width: 100%;
    height: 60px;
    resize: none;
    padding: 8px;
    font-size: 13px;
    border-radius: 5px;
    border: 1px solid #ccc;
    margin-bottom: 8px;
}

.subCommentForm button {
    padding: 6px 12px;
    font-size: 13px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    margin-right: 5px;
}

.subCommentForm button[type="submit"] {
    background-color: #007bff;
    color: white;
}

.subCommentForm button[type="submit"]:hover {
    background-color: #0056b3;
}

.cancel-reply-btn {
    background-color: #6c757d;
    color: white;
}

.cancel-reply-btn:hover {
    background-color: #5a6268;
}

/* depth별 왼쪽 테두리 색상 */
.comment-item[style*="margin-left: 0px"] {
    border-left: 3px solid #007bff;
    padding-left: 10px;
}

.comment-item[style*="margin-left: 15px"] {
    border-left: 3px solid #28a745;
    padding-left: 10px;
}

.comment-item[style*="margin-left: 30px"] {
    border-left: 3px solid #ffc107;
    padding-left: 10px;
}

.comment-item[style*="margin-left: 45px"] {
    border-left: 3px solid #dc3545;
    padding-left: 10px;
}
</style>