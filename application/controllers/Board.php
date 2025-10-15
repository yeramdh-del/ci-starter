<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Board extends MY_Controller
{
    
    //생성자
    public function __construct()
    {
        parent::__construct();
    }

    //NOTE: 게시판 - 게시판 리스트 경로
    public function index()
    {

        //리스트 최대 갯수 및 페이지 수
        $limit = $this->input->get('limit') ?: 10;
        $pages = $this->input->get('pages') ?: 1;
        $search = $this->input->get('search') ?:null;
        

        //FIXME: 게시글 등록/수정/삭제 후 SORTING 및 페이지네이션 코드 수정
        /* 
            TODO: 게시글 페이지 - 리스트 출력 쿼리문 조건
            - 정렬
                - 작성시간 내림차순 : created_at / DESC
            - 검색
                - 조건
            - 페이지 네이션
                - OFFSET : LIMIT * page + 1
                - LIMIT : LIMIT
        */

        //페이지네이션 갯수 확인용
        $select_total= "
            SELECT
                COUNT(idx) AS total
            from 
                board
            ";
        $select_total_query= $this->db->query($select_total);
        $total = $select_total_query->row();


        //리스트 출력
        $select_query= '
            SELECT
                board.*,
                user.name as author
            from 
                board
                    left join user
                    ON board.user_idx = user.idx
            ';
        $query= $this->db->query($select_query);
        $result = $query->result();

        

        
        $data["page_title"]='게시판';
        $data['content']= "board/list";
        $data['board_list'] = $result;
        //객체로 전달
        $data['board_info'] = (object)[
            'limit' => $limit,
            'pages' => $pages,
            'search' => $search,
        ];
        $this->load->view( 'layout', $data );
    }

    //NOTE: 게시판 - 등록 페이지
    public function register(){
        $data['page_title']= '게시글 등록';
        $data['content']= 'board/register';
        $this->load->view( 'layout', $data );
    }

    //NOTE: 게시판 - 등록 메서드
    public function create(){
        //게시글 제목,내용
        $title = $this->input->post('title');
        $content = $this->input->post('content');

        //세션 고객 idx정보
        $user = $this->session->userdata('user');
        
        $insert_board = "
            INSERT INTO
                board(title,content,user_idx)
            VALUES(?,?,?)
        ";
        $this->db->query($insert_board,array($title,$content,$user->idx));
        redirect("board");
    }

    //TODO: 수정 페이지
    
    //TODO: 상세 페이지
    public function detail($idx){

        $select_board_detail = "
            SELECT
                b.idx,
                b.title,
                b.content,
                u.name AS author
            FROM 
                board AS b
                    left join user AS u
                    ON b.user_idx = u.idx
            WHERE
                b.idx = ?                
        ";
        $select_board_detail_query = $this->db->query($select_board_detail, array($idx));
        $result = $select_board_detail_query->row();


        $data["page_title"]='게시판 상세페이지';
        $data['content']= "board/detail";
        $data["board_info"] = $result;
        $this->load->view("layout", $data);
    }

    //TODO: 삭제 기능
}