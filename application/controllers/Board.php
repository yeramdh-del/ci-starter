<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Board extends MY_Controller
{
    
    //생성자
    public function __construct()
    {
        parent::__construct();
        //모델 불러오기 
        $this->load->model("Board_model");
    }

    //로그인 상태 확인
    private function is_login(){
        if($this->session->userdata('user')){
            return true;
        }
        return false;

    }

    private function get_category_list(){
         $this->load->model("Category_model");
         return $this->Category_model->get_all();
    }

    //NOTE: 게시판 - 게시판 리스트 경로
    public function index()
    {

        //리스트 최대 갯수 및 페이지 수
        $limit = (int)$this->input->get('limit') ?: 10;
        $pages = (int)$this->input->get('pages') ?: 0;
        $search = $this->input->get('search') ?:null;
        $category_idx= $this->input->get('category_idx') ?: 0;




        //게시글 리스트 반환 쿼리
        $result = $this->Board_model->get_all($search, $limit, $pages ,$category_idx);


        //게시글 전채 갯수 반환 (페이지네이션용)
        $count = $this->Board_model->get_all_count($search,$category_idx);


        //init response data
        //카테고리 리스트 송신
        $data['categorys'] = $this->get_category_list();
        $data['category_idx'] = $category_idx;
        $data['board_list'] = $result;
        //객체로 전달
        $data['board_info'] = (object)[
            'limit' => $limit,
            'pages' => $pages,
            'search' => $search, //FIXME: 임시 코드 추후 세션또는 다른방향 검토할 것
            'total' => $count->total,
        ];

        $this->render('BOARD_LIST','BOARD_LIST', $data);

    }

    //NOTE: 게시판 - 등록 페이지
    public function register(){
        
        //로그인 후 접근 가능하도록 차단
        if(!$this->is_login()){
            return $this->redirect_with_alert('로그인 후 이용해주십시오');
        }

        //init response data
        $data['categorys'] = $this->get_category_list();

        $this->render('BOARD_REGISTER','BOARD_REGISTER', $data);
    }

    //NOTE: 게시판 - 등록 메서드
    public function create(){
        //게시글 제목,내용
        $title = $this->input->post('title');
        $content = $this->input->post('content');
        $category_idx = $this->input->post('category_idx');

        //세션 고객 idx정보
        $user = $this->session->userdata('user');

        //게시글 리스트 생성 쿼리
        $this->Board_model->create($title, $content, $user->idx , $category_idx);
        
       
        redirect("board");
    }

    //NOTE: 게시판 - 수정 페이지
    public function edit($idx){

        //모델 불러오기
        $this->load->model("Board_model");

        //게시글 반환 쿼리
        $result = $this->Board_model->get_one($idx);
        //권한 확인
        $user = $this->session->userdata("user");        

        if(!$result){
            return $this->redirect_with_alert('존재하지 않는 게시글입니다.',"BOARD_LIST"); 
        }elseif($result->user_idx != $user->idx){ 
            return $this->redirect_with_alert('권한이 없는 사용자입니다.','BOARD_LIST');
        }

        $data['categorys'] = $this->get_category_list();
        $data["board_info"] = $result;

        
        $this->render("BOARD_EDIT","BOARD_EDIT", $data);

    }

    //NOTE: 게시판 - 수정 매서드
    public function update($idx){
        //수정될 게시판 정보
        $title = $this->input->post("title");
        $content = $this->input->post("content");
        $category_idx = $this->input->post('category_idx');


        //권한 확인
        $user = $this->session->userdata("user");


        //게시글 반환 쿼리
        $board_one = $this->Board_model->get_one($idx);




        // //존재하지 않는 게시글일때 알람 출력
        if(!$board_one){
            return $this->redirect_with_alert('존재하지 않는 게시글입니다.',"BOARD_LIST");    
        }elseif($board_one && $board_one->user_idx != $user->idx){
            return $this->redirect_with_alert('권한이 없는 사용자입니다.','BOARD_LIST');
        }else{
            //게시글 수정 쿼리
            $this->Board_model->update($title, $content, $idx, $category_idx);
            redirect("board");
        }

    }
    
    //NOTE: 게시판 - 상세 페이지
    public function detail($idx){

        //게시글 반환 쿼리
        $result = $this->Board_model->get_one($idx);


        if(!$result){
            return $this->redirect_with_alert("존재하지 않는 게시글입니다.", 'BOARD_LIST');
        }

        //init response data
        $data["board_info"] = $result;
        $this->render("BOARD_DETAIL","BOARD_DETAIL", $data);
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
                return $this->redirect_with_alert("존재하지 않는 게시글입니다.","BOARD_LIST");
            //권한 확인
            }elseif($board_one && $board_one->user_idx != $user->idx){
                return $this->redirect_with_alert("권한이 없는 사용자입니다.","BOARD_LIST");
            }else{
                
                $this->Board_model->delete($idx);
                redirect("board");
            }

        }
}