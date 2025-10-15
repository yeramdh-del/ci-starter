<?php
    defined("BASEPATH") OR exit("No direct script access allowed");


    class Auth extends MY_Controller{

        public function __construct(){  
            parent::__construct();
            
        }


        public function index(){
            echo "hello";
        }


        //NOTE: 로그인 - 로그인 페이지
        public function login(){ 

            $data["page_title"]="로그인";
            $data["content"]= "auth/login";
            $this->load->view("layout", $data);
        }

        //NOTE: 로그인 - 회원정보 확인 매서드
        public function login_check(){

            $email = $this->input->post("email");
            $password = $this->input->post("password");

            $select_user = "
                SELECT
                    idx,
                    name,
                    email,
                    password
                FROM
                    user
                WHERE
                    email='$email'
                    
            ";
            $select_user_query = $this->db->query($select_user);
            $user_info = $select_user_query->row();

            if(!$user_info){
                echo "<script>alert('존재하지 않는 이메일입니다.'); history.back();</script>";
            }

            if($user_info->password != $password){
                echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
            }

            //비밀번호 값제거
            unset($user_info->password);
            //회원 정보 세션 저장
            $this->session->set_userdata('user',$user_info);
            redirect('board');
        
        }


        //NOTE: 회원가입 - 페이지 이동
        public function register(){
            $data["page_title"]='회원가입';
            $data['content']= 'auth/register';
            $this->load->view('layout', $data);

        }


    //NOTE: 회원가입 - 회원가입 유저 정보 저장 매서드
    public function register_check()
    {
        // POST 데이터 받기
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $password_confirm = $this->input->post('password_confirm');

        //비밀번호 중복 체크
        if($password !== $password_confirm) {
            echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
            return;
        }

        //이메일 중복 체크
        $select_email_check = "
            SELECT
                idx
            FROM
                user
            WHERE
                email='$email'
        ";
        $select_email_query = $this->db->query($select_email_check);
        $select_email_result = $select_email_query->row();

        if($select_email_result){
            echo "<script>alert('중복된 이메일입니다. 다른 이메일을 사용하세요'); history.back();</script>";
        }
        
        //닉네임 중복 체크
        $select_name_check = "
            SELECT
                idx
            FROM
                user
            WHERE
                name='$name'
        ";
        $select_name_query = $this->db->query($select_name_check);
        $select_name_result = $select_name_query->row();

        if($select_name_result){
            echo "<script>alert('중복된 이름입니다. 다른 이름을 사용하세요'); history.back();</script>";
        }

        //DB에 회원정보 저장
        $insert_query = "
            INSERT INTO
                user(name,email,password)
            VALUES(?,?,?)
        ";

        
        $this->db->query($insert_query,array($name,$email,$password));
        

        // 비밀번호 일치 시 로그인 페이지로 이동
        echo "<script>alert('회원가입이 완료되었습니다.'); location.href='" . site_url('auth/login') . "';</script>";
    }
  

    //NOTE: 로그아웃
    public function logout(){
        $this->session->unset_userdata('user');
        redirect('board');
    }

}