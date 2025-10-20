<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment_model extends CI_Model
{
    protected $table = 'comments';


    //댓글 정보 가져오기
    public function get_by_id($idx){
        $query = $this->db->query(
            "
                SELECT
                    c.*,
                    u.name AS author                    
                FROM
                        comments AS c
                    LEFT JOIN
                        user AS u
                    ON
                        c.user_idx = u.idx
                WHERE
                    c.idx = ?
            ",
        array($idx));

        return $query->row();
    }

    // 최상위 댓글 가져오기 (depth = 0)
    public function get_top_level_comments($board_idx, $limit, $offset)
    {

        $query = $this->db->query(
            "
                SELECT
                    c.*,
                    u.name AS author
                FROM
                    comments c
                LEFT JOIN
                    user u ON c.user_idx = u.idx
                WHERE
                    c.board_idx = ?
                    AND
                    parent_idx IS NULL
                ORDER BY
                    c.created_at ASC
                LIMIT ? OFFSET ?
            ",
            array($board_idx, $limit, $offset)
        );

        $comments = $query->result_array();
        // 각 댓글에 대해 자식 댓글 초기 세팅
        foreach ($comments as &$comment) {
            $children = $this->getRepliesTree($comment['idx'], $limit, 0);
            $child_count = count($children);
            
            $comment['has_more_children'] = $child_count > 0 ? true : false;
        }
        return $comments;
    }

    // 최상위 댓글 총 개수
    public function count_top_level($board_idx)
    {
        $query = $this->db->query(
            "
                SELECT COUNT(idx) as count
                FROM comments
                WHERE board_idx = ? AND parent_idx IS NULL
            ",
            array($board_idx)
        );

        $result = $query->row();
        return $result->count;
    }

    // 특정 댓글의 대댓글 가져오기
    public function getReplies($parent_idx, $limit, $offset)
    {
        $this->db->select('c.*, u.name AS author');
        $this->db->from('comments c');
        $this->db->join('user u', 'c.user_idx = u.idx', 'left');
        $this->db->where('c.parent_idx', $parent_idx);
        $this->db->order_by('c.created_at', 'ASC');
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        return $query->result_array();
    }

    // 대댓글 개수
    public function countReplies($parent_idx)
    {
        $this->db->where('parent_idx', $parent_idx);
        return $this->db->count_all_results($this->table);
    }

    // 대댓글 목록 + 하위 댓글 존재 여부 체크
    public function getRepliesTree($parent_idx, $limit, $offset)
    {
        $replies = $this->getReplies($parent_idx, $limit, $offset);

        foreach ($replies as &$reply) {
            $reply['children'] = [];  // 일단 비워두기
            $reply['has_more_children'] = $this->hasMoreReplies($reply['idx']);
        }

        return $replies;
    }

    // 하위 댓글이 더 있는지 여부
    public function hasMoreReplies($parent_idx, $limit = null)
    {
        $this->db->where('parent_idx', $parent_idx);
        if ($limit) $this->db->limit(1, $limit);  // limit 이후에 남은 게 있는지 체크
        else $this->db->limit(1);

        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    // 댓글 삽입
    public function insert($data)
    {
        //NOTE: 안정성을 위해 트랙잭션 생성 후 처리
        $this->db->trans_start();
        
        // INSERT
        $this->db->insert('comments', $data);
        $insert_id = $this->db->insert_id();
        
        // SELECT
        $comment = null;
        if ($insert_id) {
            $comment = $this->get_by_id($insert_id);
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $comment;
    }

    //댓글 삭제
    public function delete($idx){
        $this->db->where('idx', $idx);
        $this->db->delete($this->table);
    }
}