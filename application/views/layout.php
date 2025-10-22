<!-- NOTE: 로그인,접근 권한등 오류 메세지 출력 -->
<?php if ($this->session->flashdata('alert_message')): ?>
    <script>
        alert("<?= $this->session->flashdata('alert_message') ?>");
    </script>
<?php endif; ?>


<?php
    $user = $this->session->userdata('user');
    
 ?>

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
            <a href="/board" style="text-decoration: none;"><h1 class="header-title">게시글</h1></a>
            
           <?php if ($user): ?>
                <!-- FIXME: 로그인 유무 확인용 -->
                <div style="display: flex; gap: 2px; align-items: center;">
                    <span><?php echo $user->name ?>님 환영합니다.</span>
                    <a href="/auth/logout"><button class="btn-primary">로그아웃</button></a>
                </div>
                <?php else: ?>
            <a href="/auth/login"><button class="btn-primary">로그인</button></a>

            <?php endif; ?>
            
        </div>
    </header>

    <!-- 메인 - 각 페이지의 내용이 여기에 들어옴 -->
    <main>
        <?php $this->load->view($view_name); ?>
    </main>
</body>
</html>