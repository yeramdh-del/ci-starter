<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Main extends MY_Controller
{
    
    //생성자
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //메인 경로
    public function index()
    {
        
        //NOTE: 페이지 및 리스트 최대 갯수 기본값 설정
        $params = [
            'limit' => 10,
            'pages' => 1,   
        ];

        //로그인시
        redirect("board?". http_build_query($params));
    }
    
}