<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//NOTE: 비동기 매서드 response값 form 통일 필요
class CommentController extends CI_Controller
{

    const MAX_LIST_NUMBER = 3; //NOTE: 최대 댓글 입력 갯수


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

    // 최상위 댓글 리스트 Ajax 호출 (페이징)
    public function load_top_comments()
    {
        $board_idx = (int) $this->input->get('board_idx');
        $page = (int) $this->input->get('page') ?: 1;
        $limit = self::MAX_LIST_NUMBER;
        $offset = ($page - 1) * $limit;

    
        $this->load->model('Comment_model');

        $top_comments = $this->Comment_model->get_top_level_comments($board_idx, $limit, $offset);

        
        $total = $this->Comment_model->count_top_level($board_idx);


        
        $has_more = ($offset + $limit) < $total;

        $html = '';
        foreach ($top_comments as $comment) {
            $html .= $this->load->view('/board/comment_item', ['comment' => $comment], true);
        }

        echo json_encode([
            'success' => true,
            'html' => $html,
            'has_more' => $has_more
        ]);
    }

    // NOTE: 댓글 - 등록
    public function create()
    {
        if(!$this->is_login()){
             echo json_encode([
            'success' => false,
            'data' => null,
            'message' => "로그인 후 사용할 수 있습니다."
        ]);
        return;
        }

        $this->load->model('Comment_model');

        $data = [
            'board_idx'  => $this->input->post('board_idx'),
            'parent_idx'  => $this->input->post('parent_idx') ?: null,
            'depth'      => (int) $this->input->post('depth') ?: 0,
            'user_idx'     => $this->session->userdata('user')->idx,
            'content'    => $this->input->post('comment'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->Comment_model->insert($data);

        echo json_encode(['success' => $result]);
    }

    // NOTE: 댓글 - 삭제
    public function delete(){
        $this->load->model('Comment_model');
        $author_idx = $this->input->post('user_idx');
        $idx = $this->input->post('idx');
        $user = $this->session->userdata('user');

        
        if($user->idx != $author_idx){
             echo json_encode([
            'success' => false,
            'data' => null,
            'message' => "권한이 없는 사용자입니다."
        ]);
        return;
        }
        else{

            $this->Comment_model->delete($idx);

            echo json_encode([
            'success' => true,
            'data' => null,
            'message' => null
        ]);
        }

    }
    // 대댓글 더보기 Ajax
    public function load_replies()
    {
        $parent_idx = (int) $this->input->get('parent_idx');
        $page      = (int) $this->input->get('page') ?: 1;
        $limit     =  self::MAX_LIST_NUMBER;;
        $offset    = ($page - 1) * $limit;

        $this->load->model('Comment_model');

        $replies = $this->Comment_model->getRepliesTree($parent_idx, $limit, $offset);
        $total   = $this->Comment_model->countReplies($parent_idx);

        $has_more = ($offset + $limit) < $total;

        $html = '';
        foreach ($replies as $reply) {
            $html .= $this->load->view('/board/comment_item', ['comment' => $reply], true);
        }

        echo json_encode([
            'success' => true,
            'html' => $html,
            'has_more' => $has_more
        ]);
    }
}
