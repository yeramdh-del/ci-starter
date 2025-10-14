
<div class="register_box">
    <h2 class="title">회원가입</h2>
    
    <form action="<?php echo site_url('auth/register_check'); ?>" method="post">
        <div class="form-group">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_confirm">비밀번호 확인</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>

        <button type="submit" class="submit">회원가입</button>
    </form>

    <div class="form-footer">
        <p>이미 계정이 있으신가요? <a href="<?php echo site_url('auth/login'); ?>">로그인</a></p>
    </div>
</div>

<style>

    .register_box{
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .title{
        text-align: center;
        margin-bottom: 30px;
    }

     .submit{
        width: 100%;
        padding: 12px; 
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
    }


</style>