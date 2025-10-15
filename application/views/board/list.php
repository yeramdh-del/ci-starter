
<?php
    $posts = $board_list;

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
        <form method="get" action="<?= base_url('board') ?>" style="display:flex; gap:5px">
            <input style="padding: 10px 20px;" type="text" name="search" placeholder="제목을 입력하세요.">
            <button type="submit">검색</button>
        </form>

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

