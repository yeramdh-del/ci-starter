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
        //모델 불러오기
        $this->load->model("Board_model");

        //게시글 리스트 반환 쿼리
        $result = $this->Board_model->get_all();


        //게시글 전채 갯수 반환 (페이지네이션용)
        $count = $this->Board_model->get_all_count();
    
        
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

        //모델 불러오기
        $this->load->model("Board_model");

        //게시글 리스트 생성 쿼리
        $this->Board_model->create($title, $content, $user->idx);
        
       
        redirect("board");
    }

    //NOTE: 게시판 - 수정 페이지
    public function edit($idx){

        //모델 불러오기
        $this->load->model("Board_model");

        //게시글 반환 쿼리
        $result = $this->Board_model->get_one($idx);
        

        $data["page_title"]='게시판 수정페이지';
        $data['content']= "board/edit";
        $data["board_info"] = $result;
        $this->load->view("layout", $data);

    }

    //NOTE: 게시판 - 수정 매서드
    public function update($idx){
        //수정될 게시판 정보
        $title = $this->input->post("title");
        $content = $this->input->post("content");

        //권한 확인
        $user = $this->session->userdata("user");


        //모델 불러오기
        $this->load->model("Board_model");

        //게시글 반환 쿼리
        $board_one = $this->Board_model->get_one($idx);




        echo json_encode($board_one);
        // //존재하지 않는 게시글일때 알람 출력
        if(!$board_one){
           echo "
                <script>
                    alert('존재하지 않는 게시글입니다.');
                    location.href = '" . base_url('board') . "';
                </script>";
           
        }elseif($board_one && $board_one->user_idx != $user->idx){
               echo "
                <script>
                    alert('권한이 없는 사용자입니다.');
                    location.href = '" . base_url('board') . "';
                </script>";
        }else{

            //게시글 수정 쿼리
            $this->Board_model->update($title, $content, $idx);
            redirect("board");
        }

    }
    
    //NOTE: 게시판 - 상세 페이지
    public function detail($idx){


        //모델 불러오기
        $this->load->model("Board_model");

        //게시글 반환 쿼리
        $result = $this->Board_model->get_one($idx);


        $data["page_title"]='게시판 상세페이지';
        $data['content']= "board/detail";
        $data["board_info"] = $result;
        $this->load->view("layout", $data);
    }

    //NOTE: 삭제 매서드
        public function delete($idx){
        
            //권한 확인
            $user = $this->session->userdata("user");
            
            //모델 불러오기
            $this->load->model("Board_model");

            //게시글 반환 쿼리
            $board_one = $this->Board_model->get_one($idx);

            //존재하지 않는 게시글일때 알람 출력
            if(!$board_one){
            echo "
                    <script>
                        alert('존재하지 않는 게시글입니다.');
                        location.href = '" . base_url('board') . "';
                    </script>";
            //권한 확인
            }elseif($board_one && $board_one->user_idx != $user->idx){
                echo "
                    <script>
                        alert('권한이 없는 사용자입니다.');
                        location.href = '" . base_url('board') . "';
                    </script>";
            }else{
                
                $this->Board_model->delete($idx);
                redirect("board");
            }

        }
}