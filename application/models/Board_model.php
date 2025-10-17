<?php
class Board_model extends MY_Model{
   


    //생성자
    public function __construct()
    {
        parent::__construct();
    }



    //게시글 리스트 전체 검색 매서드
    public function get_all($search = "", $limit = 10, $pages= 0,$category_idx){

        //리스트 출력
        //NOTE: 기능정의서 목록 부분
        //  - 작성시간 내림차순
        //  - 페이지당 표시될 갯수 지정
        $select_query= "
            SELECT
                board.*,
                user.name as author
            FROM
                board
                    left join user
                    ON board.user_idx = user.idx
            WHERE 
                title LIKE '%$search%'
            ";
            
            //카테고리별 조건 추가
            if($category_idx != 0){
                $select_query.= "
                AND
                category_idx = '$category_idx'
                ";
            }

            $select_query.= "
                ORDER BY
                    created_at DESC
                LIMIT ?
                OFFSET ?
            ";


        $query= $this->db->query($select_query,array($limit,($pages*$limit)));
        return $query->result();

    }


    //게시글 전채 갯수 검색 매서드
    public function get_all_count($search=null, $category_idx){
        $select_query= "
            SELECT
                COUNT(idx) AS total
            FROM 
                board
            WHERE title LIKE '%$search%'
            ";

            //카테고리별 조건 추가
            if($category_idx != 0){
                $select_query.= "
                AND
                category_idx = '$category_idx'
                ";
            }



        $query= $this->db->query($select_query);
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
        $query_text = "
            SELECT
                b.*,
                u.name AS author,
                c.title AS category_title
            FROM
                board AS b
                LEFT JOIN user AS u
                ON b.user_idx = u.idx
                LEFT JOIN categorys c
                ON b.category_idx = c.idx
            WHERE
                b.idx = ?
        ";
         $query = $this->db->query($query_text,array($idx));
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

        //추후 적용
        // $update_board.= $this->getUpdateQuery('board',array(
        //     'title'=> $title,
        //     'content'=> $content
        // ),(object)[
        //     'idx' => $idzx
        // ]);
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
