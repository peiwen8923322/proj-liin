<?php
use Illuminate\Auth\Events\Login;
use PhpParser\Node\Stmt\Foreach_;

/*
cls_suppliers 供應商檔
*/

class cls_suppliers extends cls_models
{
    //variable members

    //function members
    //建構式
    function __construct()
    {
        //Begin
        $this->self_table = "suppliers";
        $this->SQLFrom .= " $this->self_table ";
        parent::__construct(); //執行父類別的初始函數
        //End
    }

    //解構式
    function __destruct()
    {
        //code...
    }

    //輸出HTML Tags
    /* 
    輸出查詢結果 View
    $arrData: Array物件
    return: 查詢結果HTML Tag
    */
    function viewQry($arrData){
        //變數初始化
        $btnEditId = ""; //設定[編輯]按鈕Id屬性
        $btnDiscardId = ""; //設定[註銷]按鈕Id屬性
        $count = 0; //該頁筆數

        //Begin
        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";
            foreach ($arrData as $field) {
                $count++;
                $btnEditId = sprintf("edit%06d", $count);
                $btnDiscardId = sprintf("discard%06d", $count);
                $tbody .= <<<_TBODY
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" id="$btnEditId" name="edit" value="編輯" attrformcode="$field[formcode]">
                                <input type="submit" class="btn btn-outline-primary" id="$btnDiscardId" name="discard" value="註銷" attrformcode="$field[formcode]">
                            </td>
                            <td class="col-1 text-center">$field[splrcode]</td>
                            <td class="col-2">$field[splrapl]</td>
                            <td class="col-1 text-center">$field[splrunicode]</td>
                            <td class="col-1 text-center">$field[splrtel]</td>
                            <td class="col-1 text-center">$field[splrfax]</td>
                            <td class="col-1 text-center">$field[splrlia]</td>
                            <td class="col-1 text-center">$field[splrmbl]</td>
                        </tr>
_TBODY;
            }
            $tbody .= "</tbody>";
        } else {
            $tbody = <<<_TBODY
                    <tbody>
                    <tr>
                        <td class="col-1"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-2"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                    </tr>
                    </tbody>
_TBODY;
        }

        return $tbody;
        //End
    }

    /*
    新增記錄
    $arrFormVal: 傳入新表單欄位值陣列
    $arrTbl: 傳入其他表格的參考陣列
    */
    function Insert($arrFormVal, $arrTbl=array()){
        //Begin
        $this->SQL = <<<_sql
            INSERT INTO suppliers (
                formcode
                , creator
                , modifier
                , splrcode
                , splrapl
                , splrunicode
                , splrtel
                , splrfax
                , splrlia
                , splrmbl
                , splraddr
                , splremail
                , memo
            ) VALUES (
                '$arrFormVal[formcode]'
                , '$arrFormVal[creator]'
                , '$arrFormVal[modifier]'
                , '$arrFormVal[splrcode]'
                , '$arrFormVal[splrapl]'
                , '$arrFormVal[splrunicode]'
                , '$arrFormVal[splrtel]'
                , '$arrFormVal[splrfax]'
                , '$arrFormVal[splrlia]'
                , '$arrFormVal[splrmbl]'
                , '$arrFormVal[splraddr]'
                , '$arrFormVal[splremail]'
                , '$arrFormVal[memo]'
            )
_sql;

        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        return $this->PDO->lastInsertId();
        //End
    }

    /*
    更新記錄
    $arrFormVal: 傳入新表單欄位值陣列
    $arrTbl: 傳入參考其他表格的欄位陣列
    */
    function Update($arrFormVal, $arrTbl=array()){
        //Begin
        $this->SQL = <<<_sql
            UPDATE suppliers
            SET
                modifier = '$arrFormVal[modifier]'
                , modifydate = current_timestamp()
                , splrcode = '$arrFormVal[splrcode]'
                , splrapl = '$arrFormVal[splrapl]'
                , splrunicode = '$arrFormVal[splrunicode]'
                , splrtel = '$arrFormVal[splrtel]'
                , splrfax = '$arrFormVal[splrfax]'
                , splrlia = '$arrFormVal[splrlia]'
                , splrmbl = '$arrFormVal[splrmbl]'
                , splraddr = '$arrFormVal[splraddr]'
                , splremail = '$arrFormVal[splremail]'
                , memo = '$arrFormVal[memo]'
            WHERE formcode = '$arrFormVal[formcode]';
_sql;

        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        //End
    }

    /*
    註銷記錄
    $selFormCode: 傳入目前記錄的FormCode值
    */
    function discard($selFormCode){
        //Begin
        $this->SQL = "UPDATE $this->self_table SET formstate = 14 WHERE formcode='$selFormCode'";
        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        //End
    }










    //------------------------------------------------------------------------------------------------------
    /*
     取得供應商清單
     */
    function getList(){
        //Begin
        $this->SQL = "SELECT * FROM $this->self_table WHERE 1 AND formstate = 15 ORDER BY splrcode";
        return $this->rtnQryResults($this->SQL);
        //End
    }

    /*
     取得單一供應商記錄
     $splrcode: 傳入供應商代碼
     return Array: 傳回單一供應商記錄
     */
    function getRcrdBySupplier($splrcode){
        //Begin
        $this->SQL = "SELECT * FROM $this->self_table WHERE 1 AND formstate = 15 AND splrcode = $splrcode ORDER BY splrcode";
        return $this->rtnQryRecord($this->SQL);
        //End
    }




}

?>