
<?php
    $posts = $board_list;

    $curr_page = $board_info->pages; //현재 페이지
    $limit = $board_info->limit; //출력 개수
    $total = $board_info->total; //총 게시글 갯수
    $search = $board_info->search; //검색어
    $total_page = ceil($total / $limit); //총 페이지 수
    


    $page_block_size = 3; // 페이지 번호는 3개씩만 보이게
    $start_page = max(0, $curr_page - floor($page_block_size / 2));
    $end_page = $start_page + $page_block_size;

    if ($end_page > $total_page) {
        $end_page = $total_page;
        $start_page = max(0, $end_page - $page_block_size);
    }

?>



    <div style="display:flex; justify-content: space-between; margin-bottom: 10px;">

        <div style="display:flex; gap:5px">
            
            
            <form method="get" action="<?= base_url('board') ?>" style="display:flex; gap:5px">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="검색어 입력">
                <select id="category_idx" name="category_idx">
                    <option value="0" <?= ($category_idx == 0) ? 'selected' : '' ?>>전체</option>

                    <?php foreach ($categorys as $category): ?>
                        <option value="<?= $category->idx ?>" <?= ($category_idx == $category->idx) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category->title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>   
                <button type="submit">검색</button>
            </form>

        </div>
        <div>
            <a href= "/board/register">
                <button class="login-btn">등록</button>
            </a>
            
        </div>
    </div>


    <div>
        <?php $board_list?>
    </div>


<div class="table-container">
    <table>
        <thead>
            <tr>
                <th class="col-idx">번호</th>
                <th class="col-title">제목</th>
                <th class="col-author">작성자</th>
                <th class="col-date">작성일</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($posts)): ?>
                <?php foreach($posts as $post): ?>
                    <tr class="clickable-row" data-href="<?php echo site_url('board/detail/' . $post->idx); ?>">
                        <td class="col-idx"><?php echo $post->idx; ?></td>
                        <td class="col-title">
                            <strong style="font-size: 13px; color: #007bff;">[<?= $post->category_title?>]</strong>
                            <?= nl2br(htmlspecialchars($post->title))?>
                           
                        </td>
                        <td class="col-author"><?php echo $post->author; ?></td>
                        <td class="col-date"><?php echo $post->created_at; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #999;">게시글이 없습니다.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<!-- NOTE: 페이지네이션 -->
<div class="pagination-container">
    
    <nav>
        <ul class="pagination">

            <!-- 이전 블록 -->
            <?php if ($start_page > 0): ?>
                <li class="prev">
                    <a href="<?= base_url('board?pages=' . ($start_page - 1) . '&limit=' . $limit . '&search=' . urlencode($search) . '&category_idx=' . $category_idx) ?>">
                        &lt; 이전
                    </a>
                </li>
            <?php endif; ?>

            <!-- 페이지 번호 목록 -->
            <?php for ($i = $start_page; $i < $end_page; $i++): ?>
                <li class="<?= $i == $curr_page ? 'active' : '' ?>">
                    <a href="<?= base_url('board?pages=' . $i . '&limit=' . $limit . '&search=' . urlencode($search) . '&category_idx=' . $category_idx) ?>">
                        <?= $i + 1 ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- 다음 블록 -->
            <?php if ($end_page < $total_page): ?>
                <li class="next">
                    <a href="<?= base_url('board?pages=' . $end_page . '&limit=' . $limit . '&search=' . urlencode($search) . '&category_idx=' . $category_idx) ?>">
                        다음 &gt;
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </nav>

    <form id="limitForm" method="get" action="<?= base_url('board') ?>">
        <!-- 유지할 검색어 파라미터 -->
        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
        <input type="hidden" name="category_idx" value="<?= $category_idx ?>">
        <label for="limit">출력 개수:</label>
        <select name="limit" id="limit"  onchange="document.getElementById('limitForm').submit()">
            <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5개</option>
            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10개</option>
            <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20개</option>
            <option value="30" <?= $limit == 30 ? 'selected' : '' ?>>30개</option>
        </select>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('.clickable-row');
        rows.forEach(row => {
            row.addEventListener('click', function () {
                const href = this.dataset.href;
                if (href) {
                    window.location.href = href;
                }
            });
        });
    });
</script>

<style>
    .pagination-container{
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    li{
        display: inline-block;
    }
    .pagination {
        display: flex;
        gap: 4px;
        padding: 0;
        list-style: none;
    }

    .pagination li a {
        padding: 4px 8px;
        text-decoration: none;
        border: 1px solid #ccc;
        border-radius: 3px;
        color: #333;
    }

    .pagination li.active a {
        background-color: #007bff;
        color: #fff;
    }

    .pagination li a:hover {
        background-color: #eaeaea;
    }
</style>

