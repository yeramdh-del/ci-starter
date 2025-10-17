
<?php
    $posts = $board_list;

    $curr_page = $board_info->pages; //현재 페이지
    $limit = $board_info->limit; //출력 개수
    $total = $board_info->total; //총 게시글 갯수
    $search = $board_info->search; //검색어
    $total_page = ceil($total / $limit); //총 페이지 수
    $category_idx //선택된 카테고리 selectbox


//     //FIXME: 임시 테스트
//     $posts = array([
//         "id" => "1",
//         "title"=> "test",
//         "content"=> "test content",
//         "author" => "작성자",
//         "created_date"=> "2024-06-01",
//     ],[
//         "id" => "2",
//         "title"=> "test2",
//         "content"=> "test2 content",
//         "author" => "작성자1",
//         "created_date"=> "2024-06-02",
//     ],
//     [
//         "id" => "3",
//         "title"=> "test3",
//         "content"=> "test2 content",
//         "author" => "작성자1",
//         "created_date"=> "2024-06-02",
//     ],
// [
//         "id" => "3",
//         "title"=> "test3",
//         "content"=> "test2 content",
//         "author" => "작성자1",
//         "created_date"=> "2024-06-02",
//     ]);

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
                    <tr>
                        <td class="col-idx"><?php echo $post->idx; ?></td>
                        <td class="col-title">
                            <a href="<?php echo site_url('board/detail/' . $post->idx); ?>">
                                <?php echo $post->title; ?>
                            </a>
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


<!-- 
    BUG: 페이지네이션 갯수 제안후 이전,다음 버튼 추가하기
 -->
<div class="pagination-container">
    <nav>
        <ul>
            <?php for($i=0; $i<$total_page; $i++): ?>
                <li class="<?= $i == $curr_page ? 'active' : '' ?>">
                    <a href="<?= base_url('board?pages='.$i.'&limit='.$limit.'&search='.urlencode($search)).'&category_idx='.$category_idx ?>">
                        <?= $i+1 ?>
                    </a>
                </li>
            <?php endfor; ?>
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


<style>
    .pagination-container{
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    li{
        display: inline-block;
    }
</style>
