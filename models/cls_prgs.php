<?php
use Illuminate\Database\Eloquent\Model;
/*
 class prgs 程式檔
 */
class cls_prgs extends cls_models{
    //variable members

    //function members
    /*
    建構式
     */
    public function __construct() {
        //Begin
        $this->self_table = "prgs";
        $this->SQLFrom .= " $this->self_table ";
        parent::__construct(); //執行父類別的初始函數
        //End
    }

    //解構式
    function __destruct()
    {
        //code...
    }

    /*
     取得程式分類清單
     傳回二維陣列
     */
    function getClsList(){
        //Begin
        $this->init($this->self_table);
        $this->SQLWhere .= " AND formstate = 15 ";
        $this->SQLGroupBy .= " prgcls ";
        $this->SQLOrderBy .= " formcode ";
        $this->SQL = $this->SQLSelect.$this->SQLFrom.$this->SQLWhere.$this->SQLGroupBy.$this->SQLOrderBy;

        return $this->rtnQryResults($this->SQL);
        //End
    }

    /*
    取得程式檔記錄(二維關聯陣列)
    $SQLWhere: 傳入查詢條件
    $arrData: 傳回二維關聯陣列
    */
    function getRcds($SQLWhere){
        //變數初始化
        $this->init($this->self_table);
        $this->SQLWhere .= " $SQLWhere ";
        $this->SQLOrderBy .= " formcode ";
        $this->SQL = $this->SQLSelect.$this->SQLFrom.$this->SQLWhere.$this->SQLOrderBy;
        $arrData = array();
        
        //Begin
        $results = is_object($this->PDO)? $this->PDO->query($this->SQL): null;
        while ($row = $results->fetch()) {
            $arrData[] = $row;
        }
        
        return $arrData;
        //End
    }
    
    
    
}
?>