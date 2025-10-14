<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Board extends MY_Controller
{
    
    //생성자
    public function __construct()
    {
        parent::__construct();
    }

    //게시판 리스트 경로
    public function index()
    {

        
        $data["page_title"]='게시판';
        $data['content']= 'board/list';
        $this->load->view( 'layout', $data );
    }

    // 등록 페이지
    public function register(){
        $data['page_title']= '게시글 등록';
        $data['content']= 'board/register';
        $this->load->view( 'layout', $data );
    }

    //TODO: 수정 페이지
    
    //TODO: 상세 페이지


    //TODO: 삭제 기능
}