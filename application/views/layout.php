<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : '게시판'; ?></title>

    <!-- 전역 CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
    <!-- 헤더 -->
    <header>
        <div class="header-container">
            <a href="<?php echo site_url("board")?>" style="text-decoration: none;"><h1 class="header-title">게시글</h1></a>
            <a href="<?php echo site_url('auth/login'); ?>"><button class="login-btn">로그인</button></a>
        </div>
    </header>

    <!-- 메인 - 각 페이지의 내용이 여기에 들어옴 -->
    <main>
        <?php $this->load->view($content); ?>
    </main>
</body>
</html>