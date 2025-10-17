<?php 

?>
<div class="post_box">

    <div>
        <h2>게시글 등록</h2>
    </div>
    <form action="/board/create" method="post">
        <div class="form-group"  style=" display:flex; justify-content: space-between; padding: 10px 0px;">
            <label for="category_idx" style="margin-bottom:0px;">카테고리</label>
            
            <select id="category_idx" name="category_idx">
                <?php foreach($categorys as $category) : ?>
                    <option value="<?= $category->idx?>"><?= $category->title?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="title">제목</label>
            <input type="text" id="title" name="title" required placeholder="게시글 제목을 입력하세요">
        </div>

        <div class="form-group">
            <label for="content">내용</label>
            <textarea id="content" name="content" required placeholder="게시글 내용을 입력하세요"></textarea>
        </div>

        <div style="display:flex; justify-content: center; gap:5px">
            <button type="submit" class="btn-primary">등록</button>
            <a href="/board" class="btn-cancel">취소</a>
        </div>
    </form>
</div>

<style>



</style>