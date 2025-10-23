<?php
class Board_model extends MY_Model{
   


    //생성자
    public function __construct()
    {
        parent::__construct();
    }


    protected function baseBuilder() {
        $this->db->reset_query();
        $this->db->from('board AS b');
        $this->db->join('user AS u', 'b.user_idx = u.idx', 'left');
        $this->db->join('categorys AS c', 'b.category_idx = c.idx', 'left');
        $this->db->where('c.is_used', true);
        return $this->db;
    }



    //게시글 리스트 전체 검색 매서드
    public function get_all($search = null, $limit = 10, $pages= 0,$category_idx){

        //리스트 출력
        //NOTE: 기능정의서 목록 부분
        //  - 작성시간 내림차순
        //  - 페이지당 표시될 갯수 지정
        // $select_query= "
        //     SELECT
        //         b.*,
        //         user.name as author,
        //         c.title AS category_title
        //     FROM
        //         board AS b
        //         left join user
        //         ON b.user_idx = user.idx
        //         LEFT JOIN categorys c
        //         ON b.category_idx = c.idx
        //     WHERE 
        //         b.title LIKE '%$search%'
        //         AND
        //         c.is_used = true
        //     ";
            
        //     //카테고리별 조건 추가
        //     if($category_idx != 0){
        //         $select_query.= "
        //         AND
        //         b.category_idx = '$category_idx'
        //         ";
        //     }

        //     $select_query.= "
        //         ORDER BY
        //             b.created_at DESC
        //         LIMIT ?
        //         OFFSET ?
        //     ";
        // $query= $this->db->query($select_query,array($limit,($pages*$limit)));

    

        $builder = $this->baseBuilder();
        
        $builder->select("
                b.*,
                u.name as author,
                c.title AS category_title
                ");
        $builder->order_by("b.created_at","DESC");
        $builder->limit($limit);
        $builder->offset(($pages*$limit));

        //특정 카테고리 검색시
        if($category_idx != 0){
            $builder->where('b.category_idx', $category_idx);
        }

        //검색어 추가시
        if(!is_null($search)){
            $builder->like('b.title', $search);
        }
        $query = $builder->get();
        
        return $query->result();

    }


    //게시글 전채 갯수 검색 매서드
    public function get_all_count($search=null, $category_idx){

        //V1
        // $select_query= "
        //     SELECT
        //         COUNT(b.idx) AS total
        //     FROM 
        //         board AS b
        //         LEFT JOIN categorys c
        //         ON b.category_idx = c.idx
        //     WHERE
        //         b.title LIKE '%$search%'
        //         AND
        //         c.is_used = true
        //     ";

        //     //카테고리별 조건 추가
        //     if($category_idx != 0){
        //         $select_query.= "
        //         AND
        //         b.category_idx = '$category_idx'
        //         ";
        //     }
        // $query= $this->db->query($select_query);
        

        $builder = $this->baseBuilder();
        $builder->select('COUNT(b.idx) AS total');
        
        $builder->where('c.is_used', true);

        //특정 카테고리 검색시
        if($category_idx != 0){
            $builder->where('b.category_idx', $category_idx);
        }

        //검색어 추가시
        if(!is_null($search)){
            $builder->like('b.title', $search);
        }

        $query = $builder->get();
        return $query->row();
    }

    //게시글 등록 매서드
    public function create($title,$content,$user_idx,$category_idx){

        //  $insert_board = "
        //     INSERT INTO
        //         board(title,content,user_idx)
        //     VALUES(?,?,?)
        // ";

       $query = $this->getInsertQuery("board",(object)[
        "title"=> $title,
        "content"=> $content,
        "user_idx"=> $user_idx,
        'category_idx'=> $category_idx
        ]);
        $this->db->query($query,array($title,$content,$user_idx));

    }


    //게시글 단일 검색
    public function get_one($idx){
        //V1
        // $query_text = "
        //     SELECT
        //         b.*,
        //         u.name AS author,
        //         c.title AS category_title
        //     FROM
        //         board AS b
        //         LEFT JOIN user AS u
        //         ON b.user_idx = u.idx
        //         LEFT JOIN categorys c
        //         ON b.category_idx = c.idx
        //     WHERE
        //         b.idx = ?
        //         AND
        //         c.is_used = true
        // ";
        //  $query = $this->db->query($query_text,array($idx));

        $builder = $this->baseBuilder();
        $builder->select('b.*, u.name AS author, c.title AS category_title');
        $builder->where('b.idx', $idx);
        $builder->where('c.is_used', true);
        $builder->limit(1);

        $query = $builder->get();

        return $query->row();
    }

    //게시글 수정
    public function update($title,$content,$idx, $category_idx){
        $update_board = "
            UPDATE
                board
            SET
                title = ?,
                content = ?,
                category_idx = ?
            WHERE
                idx = ?
        ";
        $this->db->query($update_board,array($title,$content, $category_idx,$idx));

    }

    
    //게시글 삭제
    public function delete($idx){
        $delete_board = "
            DELETE FROM board
            WHERE idx = ?
        ";
        $this->db->query($delete_board,array($idx));
    }

    

    
}

?>
