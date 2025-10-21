<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//NOTE: 비동기 매서드 response값 form 통일 필요
class V2_comment extends CI_Controller
{

    const MAX_LIST_NUMBER = 5; //NOTE: 최대 댓글 입력 갯수


    private function is_login(){

        $user = $this->session->userdata('user');
        if($this->session->userdata('user')){
            return true;
        }
        
        return false;
    }
    public function index($board_idx)
    {
        $data = [
            'board_info' => (object)['idx' => $board_idx],
        ];
        $this->load->view('view', $data);
    }



    //NOTE: 댓글 - 리스트 불러오기
    public function get_list(){


        //init model
        $this->load->model('V2_comments_model');
        

        //request data
        $board_idx = $this->input->get('board_idx');
        $pages = $this->input->get('pages');
        $offset = self::MAX_LIST_NUMBER * $pages;

        
        $result = $this->V2_comments_model->get_all($board_idx, self::MAX_LIST_NUMBER ,$offset);
        $count = $this->V2_comments_model->get_all_count($board_idx);
        
        echo json_encode([
            'success' => true,
            "comment_info" => (object)[
                "total" => $count->total,
                "pages" =>$pages,
                'limit' => self::MAX_LIST_NUMBER,
            ],
            "comments" => $result
        ]);

    }


    // NOTE: 댓글 - 등록
    public function add()
    {
        if(!$this->is_login()){
             echo json_encode([
            'success' => false,
            'data' => null,
            'message' => "로그인 후 사용할 수 있습니다."
        ]);
        return;
        }

        //init model
        $this->load->model('V2_comments_model');

        //request data
        $board_idx = (int) $this->input->post('board_idx');
        $parent_idx = (int) $this->input->post('parent_idx') ? : null;
        $content = $this->input->post('comment');
        
        //get session data
        $user_idx = (int)$this->session->userdata('user')->idx;


        //댓글 기본 정보 입력
        $comment_data = [
            'board_idx' => $board_idx,
            'content' => $content,
            'user_idx' => $user_idx,
            'parent_idx' => $parent_idx
        ];

        
        $created_comment = $this->V2_comments_model->insert($comment_data);

        

        echo json_encode([
            'success' => true,
            "comment" => $created_comment
        ]);
    }

    //NOTE:댓글 - 삭제
    public function delete(){

        
        //init model
        $this->load->model('V2_comments_model');

        //request data
        $idx = (int) $this->input->post('idx');

        //session
        $user = $this->session->userdata('user');
        $comment = $this->V2_comments_model->get_by_id($idx);
        
        if($user->idx != $comment->user_idx){
             echo json_encode([
            'success' => false,
            'data' => null,
            'message' => "권한이 없는 사용자입니다."
        ]);
        return;
        }


        $this->V2_comments_model->delete($idx);


        
             echo json_encode([
            'success' => true,
        ]);

    }

    

}
