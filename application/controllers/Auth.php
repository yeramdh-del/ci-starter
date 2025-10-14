<?php
    defined("BASEPATH") OR exit("No direct script access allowed");


    class Auth extends MY_Controller{

        public function __construct(){  
            parent::__construct();
            
        }


        public function index(){
            echo "hello";
        }


        //로그인 페이지
        public function login(){ 

            $data["page_title"]="로그인";
            $data["content"]= "auth/login";
            $this->load->view("layout", $data);
        }

        public function login_check(){
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $this->db->where('email', $email);
            $this->db->where('password', $password);
            $user = $this->db->get('user')->row_array();

            if($user){
                $this->session->set_userdata('user', $user);
                redirect('board');
            }else{
                echo "<script>alert('로그인에 실패했습니다.'); history.back();</script>";
            }
        }

        //회원가입
        public function register(){
            $data["page_title"]='회원가입';
            $data['content']= 'auth/register';
            $this->load->view('layout', $data);

        }

         // 회원가입 데이터 처리
    public function register_check()
    {
        // POST 데이터 받기
        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $password_confirm = $this->input->post('password_confirm');

        // 비밀번호 중복 체크
        if($password !== $password_confirm) {
            echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
            return;
        }

        //닉네임 중복확인

        //이메일 중복확인

        //db정보 저장
        $data = array(
            'name' => $name,
            'email' => $email,
            'password' => $password
        );
        
        $this->db->insert('user', $data);


        // 비밀번호 일치 시 로그인 페이지로 이동
        echo "<script>alert('회원가입이 완료되었습니다.'); location.href='" . site_url('auth/login') . "';</script>";
    }
  


}