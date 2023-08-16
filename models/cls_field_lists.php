<?php
/*
    cls_field_lists
*/

class cls_field_lists extends cls_models{
    //variable members

    //function members
    //建構式
    function __construct(){
        $this->self_table = "field_lists";
        $this->SQLFrom .= " $this->self_table ";
        parent::__construct();
    }

    //解構式
    function __destruct(){
        //code...
    }

    // 取得欄位清單檔(field_lists)的formcode, listapl欄位清單(以清單類別)
    // $listcls: 傳入清單類別
    // $arrData: 傳回二維關聯陣列
    function getList($listcls){
        //變數初始化
        $this->init($this->self_table);
        $this->SQLSelect = "SELECT formcode, listapl ";
        $this->SQLWhere .= " AND formstate = 15 AND listcls='$listcls' ";
        $this->SQLOrderBy .= " listcode ";
        $this->SQL = $this->SQLSelect.$this->SQLFrom.$this->SQLWhere.$this->SQLOrderBy;
        
        //Begin
        $results = is_object($this->PDO)? $this->PDO->query($this->SQL): null;
        while ($row = $results->fetch()) {
            $arrData[] = $row;
        }
        
        return $arrData;
        //End
    }

    // 取得唯一記錄(以唯一識別碼)
    // $formcode: 傳入唯一識別碼
    // 傳回一維關聯陣列
    function getRcrdByFormcode($formcode){
        //Begin
        $this->SQL = "SELECT * from $this->self_table WHERE 1 AND formcode = '$formcode'";
        return $this->rtnQryRecord($this->SQL);
        //End
    }

    // 取得欄位清單檔(field_lists)的formcode, listapl欄位清單(以清單類別)
    // $listcls: 傳入清單類別
    function getListByLikeListcls($listcls){
        //Begin
        $this->SQL = "SELECT * from $this->self_table WHERE 1 AND formstate = 15 AND listcls LIKE '%$listcls%' ORDER BY listcode";
        return $this->rtnQryResults($this->SQL);
        //End
    }











}
?>