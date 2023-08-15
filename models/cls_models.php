<?php
/*
類別：cls_models
*/

class cls_models{
    //variable members
    protected $host = 'localhost';
    protected $db = 'liin'; // 測試主機(http://192.168.204.28/liin-testing/Public/login.php)
    // protected $db = 'liinhome_liin'; // 正式主機(https://liin520.com/liin/Public/login.php)
    protected $user = 'liin'; // 測試主機(http://192.168.204.28/liin-testing/Public/login.php)
    // protected $user = 'liinhome_liin'; // 正式主機(https://liin520.com/liin/Public/login.php)
    protected $paword = '/eg9XQbo(K4.*25e'; // 測試主機(http://192.168.204.28/liin-testing/Public/login.php)
    // protected $paword = '/eg9XQbo(K4.*25e'; // 正式主機(https://liin520.com/liin/Public/login.php)
    protected $chrs = 'utf8mb4';
    protected $dsn;
    protected $opts = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false];
    var $PDO;
    var $SQLSelect = "SELECT * ";
    var $SQLFrom = "FROM ";
    var $self_table = "";
    var $SQLWhere = "WHERE 1 ";
    var $SQLGroupBy = "GROUP BY ";
    var $SQLOrderBy = "ORDER BY ";
    var $SQLlimit = "LIMIT ";
    var $SQL = "";
    protected $str_PK = ""; //記錄主鍵
    protected $bigint_seq = 0; //流水號
    var $int_records_per_page = 100; //每頁記錄筆數
    var $int_total_records = 0; //總筆數
    var $int_total_pages = 0; //總頁數
    var $int_current_page = 1; //目前頁數
    var $intStartPos = 0; //開始記錄位置
    var $intEndPos = 0; //結束記錄位置
    var $arrData = []; //設定查詢結果

    //function members
    //建構式
    function __construct(){
        //Begin
        //Open Connection
        try{
            $this->dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->chrs}";
            $this->PDO = new PDO($this->dsn, $this->user, $this->paword, $this->opts);
        }
        catch(PDOException $e){
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
        //End
    }

    //解構式
    function __destruct(){
        
    }

    /*
    傳回查詢結果(二維關聯陣列)
    $SQL: 傳入SQL語句
    $this->arrData: 傳回二維關聯陣列
    */
    function rtnQryResults($SQL){
        //Begin
        $result = $this->PDO->query($SQL);
        /*
        $newResult = array();
        while ($row = $result->fetch()) {
            $newResult[] = $row;
        }
        */
        return $result->fetchAll();
        //End
    }

    /* 
    取得單一記錄(一維關聯陣列)
    $SQL: 傳入SQL語句
    $arrData: 傳回一維關聯陣列
    */
    function rtnQryRecord($SQL){
        //Begin
        $result = $this->PDO->query($SQL);
        return $result->fetch();
        //End
    }

     
    // 取得單一記錄(一維關聯陣列)
    // $formcode: 傳入唯一識別碼
    function getRecdByFormcode($formcode){
        //Begin
        $this->SQL = "SELECT * FROM $this->self_table WHERE 1 AND formcode = '$formcode'";
        return $this->rtnQryRecord($this->SQL);
        //End
    }

    /*
    傳回統計結果或單一欄位值
    $SQL: 傳入SQL語句
    $fieldName: 傳入單一欄位值的欄位名稱
    $row[0]: 傳回統計結果或單一欄位值
    */
    function rtnQryField($SQL){
        //Begin
        $result = $this->PDO->query($SQL);
        $row = $result->fetch(PDO::FETCH_NUM);

        return $row[0];
        //End
    }

    /*
    計算總頁數 + 目前頁數
    $int_total_records: 傳入總筆數
    */
    function calTotalPages($int_total_records){
        //Begin
        $this->int_total_records = $int_total_records;
        $this->int_total_pages = ($this->int_total_records % $this->int_records_per_page) > 0 ? floor($this->int_total_records / $this->int_records_per_page) + 1 : floor($this->int_total_records / $this->int_records_per_page);
        $this->int_current_page = ($this->int_total_pages > 0) ? 1 : 0 ;
        //End
    }

    /*
    為一維陣列的資料值加上單引號
    $arrSrc: 傳入一維陣列
     */
    function addQuote($arrSrc){
        //變數初始化
        $arrData = array();

        //Begin
        foreach ($arrSrc as $value) {
            $arrData[] = $this->PDO->quote($value);
        }

        return $arrData;
        //End
    }

    /* 取得最新表單編號
    $cur_date: 傳入查詢表單編號的年月份
    return: 傳回最新表單編號
    */
    function getNextFormCode($cur_date){
        //Begin
        if ($this->rtnQryField("SELECT COUNT(formcode) AS cntFormCode $this->SQLFrom WHERE formcode LIKE '{$cur_date}%' ") > 0) {
            $this->SQLSelect = "SELECT MAX(formcode)+1 AS newformcode ";
            $this->SQLWhere .= " AND formcode LIKE '$cur_date%' ";
            $this->SQL = $this->SQLSelect.$this->SQLFrom.$this->SQLWhere;
            $result = $this->PDO->query($this->SQL);
            $row = $result->fetch();
            
            return $row['newformcode'];
        } else {
            return "{$cur_date}0001";
        }
        //End
    }

    //初始化成員變數
    function init($Table){
        $this->self_table = $Table;
        
        $this->SQLSelect = "SELECT * ";
        $this->SQLFrom = "FROM $this->self_table ";
        $this->SQLWhere = "WHERE 1 ";
        $this->SQLGroupBy = "GROUP BY ";
        $this->SQLOrderBy = "ORDER BY ";
        $this->SQLlimit = "LIMIT ";
        $this->SQL = "";
    }


}
?>