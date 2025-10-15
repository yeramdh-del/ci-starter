<div class="login_box">
    <h2 class="title">로그인</h2>
    
    <form action="/auth/login_check" method="post">
        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" required >
        </div>

        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" required >
        </div>

        <button type="submit" class="login" >로그인</button>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <p>계정이 없으신가요? <a href="/auth/register" style="color: #007bff; text-decoration: none;">회원가입</a></p>
    </div>
</div>

<style>

    .login_box{
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


    
    .login{
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