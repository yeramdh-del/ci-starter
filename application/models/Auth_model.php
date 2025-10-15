<?php
class Auth_model extends MY_Model{
   
    //생성자
    public function __construct()
    {
        parent::__construct();
    }

    
    //유저 정보 검색
    public function get_one_by_email($email){
        $query_text = "
           SELECT
                idx,
                name,
                email,
                password
            FROM
                user
            WHERE
                email= ?
            ";
            
        $query = $this->db->query($query_text, array($email));
        return $query->row();
    }
    public function get_one_by_name($name){
        $query_text = "
           SELECT
                idx,
                name,
                email,
                password
            FROM
                user
            WHERE
                name= ?
            ";
            
        $query = $this->db->query($query_text, array($name));
        return $query->row();
    }

    //회원가입 등록 매서드
    public function create($name,$email,$password){

       $query = $this->getInsertQuery("user",(object)[
        "name"=> $name,
        "email"=> $email,
        "password"=> $password,
        ]);
        $this->db->query($query);

    }
    
}

?>
