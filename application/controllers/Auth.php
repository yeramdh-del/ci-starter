<?php
    defined("BASEPATH") OR exit("No direct script access allowed");


    class Auth extends MY_Controller{

        public function __construct(){  
            parent::__construct();
            $this->load->model("Auth_model");
        }


        public function index(){
            redirect("auth/login");
        }


        //NOTE: 로그인 - 로그인 페이지
        public function login(){ 
            $this->render("AUTH_LOGIN","AUTH_LOGIN");
        }

        //NOTE: 로그인 - 회원정보 확인 매서드
        public function login_check(){

            //request data
            $email = $this->input->post("email");
            $password = $this->input->post("password");
   
            $user_info = $this->Auth_model->get_one_by('email',$email);

            if(!$user_info){

                return $this->redirect_with_alert("존재하지 않는 이메일입니다.");
            }

            if($user_info->password != $password){
                return $this->redirect_with_alert('비밀번호가 일치하지 않습니다.');
            }
            else{
                //비밀번호 값제거
                unset($user_info->password);
                //회원 정보 세션 저장
                $this->session->set_userdata('user',$user_info);
                redirect('board');
            }
            
        }


        //NOTE: 회원가입 - 페이지 이동
        public function register(){
            $this->render('AUTH_REGISTER','AUTH_REGISTER');
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
            return $this->redirect_with_alert('비밀번호가 일치하지 않습니다.');
        }
        
        $user_info = $this->Auth_model->get_one_by('email',$email);



        if($user_info){
            return $this->redirect_with_alert('중복된 이메일입니다. 다른 이메일을 사용하세요.');
        }
        
        //닉네임 중복 체크

        $select_name_result =  $this->Auth_model->get_one_by('name',$name);;

        if($select_name_result){
            return $this->redirect_with_alert('중복된 이름입니다. 다른 이름을 사용하세요.');
        }

        //DB에 회원정보 저장
        $this->Auth_model->create($name,$email,$password);
        

        // 비밀번호 일치 시 로그인 페이지로 이동
        $this->redirect_with_alert('회원가입이 완료되었습니다.', "AUTH_LOGIN");
    }
  

    //NOTE: 로그아웃
    public function logout(){
        $this->session->unset_userdata('user');
        redirect('board');
    }

}