<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class V2_comments_model extends CI_Model
{
    protected $v2_comments_table = 'v2_comments';
    protected $v2_comment_tree_table = 'v2_comment_tree';



    protected function baseBuilder() {
        $this->db->reset_query();
        $this->db->from("$this->v2_comments_table AS c");
        $this->db->join("$this->v2_comment_tree_table AS c_t", 'c.idx = c_t.comment_idx', 'left');
        return $this->db;
    }

    //댓글 정보 가져오기
    public function get_by_id($idx){

        //V1
        // $query = $this->db->query(
        //     "
        //         SELECT
        //             c.*,
        //             u.name AS author,
        //             c.parent_idx,
        //             c_t.depth,
        //             c_t.path,
        //             c_t.root_idx

        //         FROM
        //                 $this->v2_comments_table AS c
        //             LEFT JOIN
        //                 user AS u
        //             ON
        //                 c.user_idx = u.idx
        //             LEFT JOIN
        //                 $this->v2_comment_tree_table AS c_t
        //             ON c.idx = c_t.comment_idx
        //         WHERE
        //             c.idx = ?
        //     ",
        // array($idx));


        $builder = $this->baseBuilder();
        $builder->join("user AS u","c.user_idx = u.idx","left");

        $builder->select("
                c.*,
                u.name AS author,
                c.parent_idx,
                c_t.depth,
                c_t.path,
                c_t.root_idx
                ");
        $builder->where("c.idx", $idx);

        $query = $builder->get();
        return $query->row();
    }

    private function root_comment_idx_list($board_idx, $limit, $offset){

        //최상위 댓글 페이지네이션
        // $root_result = $this->db->query(
        //     "
        //         SELECT c.idx
        //         FROM
        //                 v2_comments AS c
        //             LEFT JOIN
        //                 v2_comment_tree AS c_t
        //             ON  c.idx = c_t.comment_idx
        //         WHERE
        //             c.board_idx = ?
        //             AND
        //             c_t.depth = 0
        //         ORDER BY c.created_at
        //         LIMIT ?
        //         OFFSET ?
                
        //     ",
        //     array($board_idx, $limit, $offset))->result_array();
        $root_builder = $this->baseBuilder();
        $root_builder->select("c.idx");
        $root_builder->where('c_t.depth',0);
        $root_builder->where('c.board_idx', $board_idx);
        $root_builder->order_by('c.created_at','ASC');
        $root_builder->limit($limit);
        $root_builder->offset($offset);
        $root_query = $root_builder->get();
        
        $root_result = $root_query->result_array();

            
        if (empty($root_result)) {
            return array();
        }


         return array_column($root_result, 'idx');
    }

    //댓글 리스트 가져오기
    public function get_all($board_idx, $limit, $offset){

        $root_ids = $this->root_comment_idx_list($board_idx, $limit, $offset);
        
        //해당 최상위 댓글 + 모든 자식 댓글 조회
        // $placeholders = implode(',', array_fill(0, count($root_ids), '?'));
        // $comments_query = "
        //     SELECT
        //         c.idx,
        //         c.content,
        //         c.created_at,
        //         u.name AS author,
        //         c.parent_idx,
        //         c_t.depth,
        //         c_t.path
        //     FROM
        //         {$this->v2_comments_table} AS c
        //     LEFT JOIN
        //         user AS u ON c.user_idx = u.idx
        //     LEFT JOIN
        //         {$this->v2_comment_tree_table} AS c_t ON c.idx = c_t.comment_idx
        //     WHERE
        //         c_t.root_idx IN({$placeholders})
        //     ORDER BY c_t.path
        // ";
        // return $this->db->query($comments_query, array_merge($root_ids))->result_array();

        $comments_builder = $this->baseBuilder();
        $comments_builder->join("user AS u","c.user_idx = u.idx","left");
        $comments_builder->select("
            c.idx,
            c.content,
            c.created_at,
            u.name AS author,
            c.parent_idx,
            c_t.depth,
            c_t.path
        ");
        $comments_builder->where_in("c_t.root_idx",$root_ids);
        $comments_builder->order_by("c_t.path","ASC");
        $comments_query = $comments_builder->get();

        return $comments_query->result_array();
                
    }

    public function get_all_count($board_idx){

        //v1
        // $query = $this->db->query(
        //     "
        //         SELECT 
        //             COUNT(DISTINCT c_t.comment_idx) as total
        //         FROM
        //             {$this->v2_comments_table} AS c
        //         LEFT JOIN 
        //                 {$this->v2_comment_tree_table} AS c_t 
        //             ON 
        //                 c.idx = c_t.comment_idx
        //         WHERE 
        //             c.board_idx = ?
        //         AND 
        //             c_t.depth = 0

        //     ",
        // array($board_idx));

        $builder = $this->baseBuilder();
        $builder->select("COUNT(DISTINCT c_t.comment_idx) as total");
        $builder->where("c.board_idx", $board_idx);
        $builder->where("c_t.depth",0);
        $query = $builder->get();

        return $query->row();

    }


    //커멘트 기본 레코드값 셋팅
    private function init_comment_tee_recode($idx){
        return [
            'comment_idx' => $idx,
            'root_idx' => $idx,
            'path' => sprintf('%010d/',$idx)
,            'depth' => 0
        ];
    }

    // 댓글 - 추가 모달 
    public function insert($comment_data)
    {
        //NOTE: 안정성을 위해 트랙잭션 생성 후 처리
        $this->db->trans_start();
        
        // INSERT
        $this->db->insert($this->v2_comments_table, $comment_data);
        $insert_id = $this->db->insert_id();
        
        //Created comment SELECT
        $comment = null;
        if ($insert_id) {
            $comment = $this->get_by_id($insert_id);

            //부모idx 유무에 따른 tree 데이터 설계
            $comment_tree_data = $this->init_comment_tee_recode($comment->idx);
            
            if ($comment_data['parent_idx']) {
                $parent_comment = $this->get_by_id($comment_data['parent_idx']);
                $comment_tree_data['comment_idx'] = $comment->idx;
                $comment_tree_data['root_idx'] = $parent_comment->root_idx;
                $comment_tree_data['depth'] = $parent_comment->depth + 1;
                $comment_tree_data['path'] = $parent_comment->path.sprintf('%010d/', $comment->idx);
            }

            $this->db->insert($this->v2_comment_tree_table,$comment_tree_data);
            
            
        }

    
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $this->get_by_id($insert_id);
    }

    //댓글 - 삭제 모달
    public function delete($idx) {
        
        $this->db->where('idx', $idx);
        $this->db->delete($this->v2_comments_table);
    }
        
    


}