<?php
/* 
    cls_depts 
*/

class cls_depts extends cls_models{
    //variable members

    //function members
    //建構式
    function __construct(){
        $this->self_table = "depts";
        $this->SQLFrom .= " $this->self_table ";
        parent::__construct();
    }

    //解構式
    function __destruct(){
        //code...
    }

    /*
    取得下拉清單
    $arrData: 傳回機構檔的下拉清單陣列
    */
    function getList(){
        //變數初始化
        $this->SQLSelect = "SELECT formcode, cmpapl ";
        $this->SQLOrderBy .= " cmpcode ";
        $this->SQL = $this->SQLSelect.$this->SQLFrom.$this->SQLOrderBy;

        //Begin
        $results = $this->PDO->query($this->SQL);
        while ($row = $results->fetch()) {
            $arrData[] = $row;
        }

        return $arrData;
        //End
    }

    




}

?>