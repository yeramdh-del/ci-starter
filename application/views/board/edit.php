<div class="post_box">
    <h2>게시글 수정</h2>
    
    <form action="/board/update/<?=$board_info->idx?>" method="post">
        <div class="form-group">
            <label for="title">제목</label>
            <input type="text" id="title" name="title" required placeholder="게시글 제목을 입력하세요" value="<?=$board_info->title?>">
        </div>

        <div class="form-group">
            <label for="content">내용</label>
            <textarea id="content" name="content" required placeholder="게시글 내용을 입력하세요"><?=$board_info->content?></textarea>
        </div>

        <div style="display:flex; justify-content: center; gap:5px">
            <button type="submit" class="btn-primary">수정</button>
            <a href="/board" class="btn-cancel">취소</a>
        </div>
    </form>
</div>