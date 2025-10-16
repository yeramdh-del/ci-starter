<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class CommentController extends CI_Controller
{

    const MAX_LIST_NUMBER = 10; //NOTE: 최대 댓글 입력 갯수
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

    // 댓글 등록
    public function create()
    {
        $this->load->model('Comment_model');

        $data = [
            'board_idx'  => $this->input->post('board_idx'),
            'parent_idx'  => $this->input->post('parent_idx') ?: null,
            'depth'      => (int) $this->input->post('depth') ?: 0,
            'user_idx'     => $this->session->userdata('user') ? $this->session->userdata('user')->idx : '익명',
            'content'    => $this->input->post('comment'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->Comment_model->insert($data);

        echo json_encode(['success' => $result]);
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
