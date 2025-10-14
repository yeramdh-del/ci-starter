<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Main extends MY_Controller
{
    
    //생성자
    public function __construct()
    {
        parent::__construct();
    }

    //메인 경로
    public function index()
    {

        //로그인시
        redirect("board");
    }
    
}