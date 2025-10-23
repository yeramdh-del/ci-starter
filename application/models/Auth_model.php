<?php
class Auth_model extends MY_Model{
   

    protected $table = "user";
    //생성자
    public function __construct()
    {
        parent::__construct();
    }

    

    //유저 정보 검색
    public function get_one_by($column, $value) {
    // 허용된 컬럼만 검색 가능하도록 지정
    $allowed_columns = ['email', 'name', 'idx'];


    if (!in_array($column, $allowed_columns)) {
        return null;
    }

    $query = $this->db
        ->select('idx, name, email, password')
        ->get_where($this->table, [$column => $value], 1);

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
