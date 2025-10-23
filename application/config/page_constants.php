<?php
defined('BASEPATH') OR exit('No direct script access allowed');


// NOTE: 페이지 매핑 설정
$config['view_names'] = [
    'BOARD_LIST' => 'board/index',
    'BOARD_REGISTER' => 'board/register',
    'BOARD_EDIT'=> 'board/edit',
    'BOARD_DETAIL' => 'board/detail',
    'AUTH_LOGIN' => 'auth/login',
    'AUTH_REGISTER' => 'auth/register', 
    

];

$config['page_titles'] = [
    'BOARD_LIST' => '게시판 목록',
    'BOARD_REGISTER' => '게시글 등록',
    'BOARD_EDIT' => '게시글 수정',
    'BOARD_DETAIL' => '게시글 상세페이지',
    'AUTH_LOGIN' => '로그인',
    'AUTH_REGISTER' => '회원가입'

];