<?php

/**
 * 모델 확장합니다.
 * 모든 모델은 이 클래스를 상속 받아야 합니다.
 *
 */
class MY_Model extends CI_Model
{
    protected $ci = null;

    private $db_conn = null;


    public function __construct()
    {
        $this->ci =& get_instance();

        parent::__construct();
    }

    public function excute($sSql = '', $sType = 'rows', $sPrefix = null)
    {
        //호출한 클래스와, 함수의 이름을 출력
        $call_class = get_class($this);
        $call_function = debug_backtrace()[1]['function'];
        $sSql = $sSql . "\n\r/* CLASS : {$call_class} / FUNCTION : {$call_function} */";

        $aDsn = $this->getDsn($sSql, $sPrefix);

        if (!empty($aDsn)) {
            $this->db_conn = $this->getDbInstance($aDsn);

        } else {
            throw new Exception('Select Query : aDsn can not be empty !');
        }

        return $this->_excute($sSql, $sType, $sPrefix);
    }


    private function _excute($sSql = '', $sType = 'rows', $sPrefix = null)
    {
        if (empty($sSql)) return false;

//      if ($this->isSelect($sSql) === true) {

        if ($sType == 'rows') {
            return $this->db_conn->query($sSql)->result_array();
        } else if ($sType == 'row') {
            return $this->db_conn->query($sSql)->row_array();
        } else if ($sType == 'exec') {
            return $this->db_conn->query($sSql);
        } else if ($sType == 'rtn') {
            $result = $this->db_conn->query($sSql);
            if ($result) {
                return $this->db_conn->insert_id();
            } else {
                return false;
            }
        } elseif ($sType == 'proc') {
            return $this->db_conn->query($sSql);
        } else {
            throw new Exception('Select Query : sType can not be exec or empty !');
        }

//      } else {


//      }
    }

    private function isSelect($sSql = '')
    {
        $bSelect = true;
        if (substr(trim(strtolower($sSql)), 0, 6) != "select") {
            $bSelect = false;
        }

        return $bSelect;
    }

    private function isMainDB($sPrefix = '')
    {//mainDB 인지판별 함수
        $bMain = true;
        if (substr(trim(strtolower($sPrefix)), -4, 4) != "main") {
            $bMain = false;
        }

        return $bMain;
    }


    private function getDsn($sSql = '', $sPrefix = null, $sDatabaseName = null)
    {
        if (!$sPrefix) {
            $sPrefix = "default";
        }
        // Is the config file in the environment folder?
        if (!defined('ENVIRONMENT') or !file_exists($file_path = APPPATH . 'config/' . ENVIRONMENT . '/database.php')) {
            if (!file_exists($file_path = APPPATH . 'config/database.php')) {
                show_error('The configuration file database.php does not exist.');
            }
        }

        include $file_path;

        if (!isset($db) or count($db) == 0) {
            show_error('No database connection settings were found in the database config file.');
        }

        return array('sPrefix' => $sPrefix, 'aDsn' => $db[$sPrefix]);
    }


    private function getDbInstance($aDsn = array())
    {

        $prefix = $aDsn['sPrefix'];

        foreach (get_object_vars($this->ci) as $CI_object_name => $CI_object) {

            if (is_object($CI_object) && is_subclass_of(get_class($CI_object), 'CI_DB') && $CI_object_name == $prefix) {
                return $CI_object;
            }
        }


        @$this->ci->{$prefix} = $this->ci->load->database($aDsn['aDsn'], TRUE);


        return $this->ci->{$prefix};
    }

    /**
     * Insert Query문을 만들어줍니다.
     *
     */
    public static function getInsertQuery($table, $aData)
    {
        if (empty($aData)) return false;

        $sFields = "";
        $sValues = "";

        foreach ($aData as $field => $value) {
            $sFields .= "`" . $field . "`,";
            $sValues .= self::checkValue($value) . ",";
        }
        return "INSERT INTO " . $table . " (" . substr($sFields, 0, -1) . ") VALUES (" . substr($sValues, 0, -1) . ")";
    }

    /**
     * Update Query문을 만들어줍니다.
     *
     */
    public static function getUpdateQuery($table, $aData = array(), $sWhere = "")
    {
        $str = "";
        if (empty($aData)) return;

        foreach ($aData as $field => $value) {
            $str .= $field . " = " . self::checkValue($value) . ",";
        }

        return "UPDATE " . $table . " SET " . substr($str, 0, -1) . " " . ($sWhere ? " WHERE " . $sWhere : "");
    }

    /**
     * Update Query문을 만들어줍니다.
     *
     */
    public static function getUpdateQueryArray($table, $aData = array(), $aWhere = array())
    {
        $str = "";
        $where = " WHERE ";
        if (empty($aData)) return;
        if (empty($aWhere)) return;

        foreach ($aData as $field => $value) {
            $str .= $field . " = " . self::checkValue($value) . ",";
        }

        foreach ($aWhere as $field => $value) {
            $where .= $field . " = " . self::checkValue($value) . " AND ";
        }
        $where = rtrim($where, " AND");

        return "UPDATE " . $table . " SET " . substr($str, 0, -1) . " " . $where;
    }

    /**
     * Delete Query문을 만들어줍니다.
     *
     */
    public static function getDeleteQuery($table, $aWhere = array())
    {
        $where = " WHERE ";
        if (empty($aWhere)) return;

        foreach ($aWhere as $field => $value) {
            $where .= $field . " = " . self::checkValue($value) . " AND ";
        }
        $where = rtrim($where, " AND ");

        return "DELETE FROM " . $table . $where;
    }


    private static function checkValue($value)
    {

        //예외처리
        if (gettype($value) == "string") {
            if ($value == "null" || strtolower($value) == "now()") {
                return $value;
            }
        }

        switch (strtolower(gettype($value))) {
            case 'string':
                settype($value, 'string');
//                $value = "'".mysql_real_escape_string($value)."'";
                $value = "'" . addslashes($value) . "'";
                break;
            case 'integer':
                settype($value, 'integer');
                break;
            case 'double' || 'float':
                settype($value, 'float');
                break;
            case 'boolean':
                settype($value, 'boolean');
                break;
            case 'array':
//                $value = "'".mysql_real_escape_string($value)."'";
                $value = "'" . addslashes($value) . "'";
                break;
        }

        return $value;
    }
}