<?php
class Category_model extends MY_Model{
   
    protected $table = "categorys";
    //생성자
    public function __construct()
    {
        parent::__construct();
    }

    
    //유저 정보 검색
    public function get_all(){
        $query_text = "
           SELECT
                idx,
                title
            FROM
                $this->table
            WHERE
                is_used = ?
            ";
            
        $query = $this->db->query($query_text, array(true));
        return $query->result();
    }
   
}

?>
