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

        //회원가입
        public function register(){
            $data["page_title"]='회원가입';
            $data['content']= 'auth/register';
            $this->load->view('layout', $data);

        }

        //TODO:비밀번호 중복 체크
        public function register_check(){
        
            
        }
    }