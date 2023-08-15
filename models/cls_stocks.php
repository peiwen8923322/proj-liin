<?php
use Illuminate\Auth\Events\Login;
use PhpParser\Node\Stmt\Foreach_;

/*
cls_stocks 庫存檔
*/

class cls_stocks extends cls_models
{
    //variable members

    //function members
    //建構式
    function __construct()
    {
        //Begin
        $this->self_table = "stocks";
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
                            <td class="col-1">$field[jobdate]</td>
                            <td class="col-1">$field[jobort]</td>
                            <td class="col-1">$field[splrcode]</td>
                            <td class="col-1">$field[splrapl]</td>
                            <td class="col-1">$field[mtrlcode]</td>
                            <td class="col-2">$field[mtrlapl]</td>
                            <td class="col-1 text-center">$field[mtrlamt]</td>
                            <td class="col-1 text-center">$field[mtrlprc]</td>
                            <td class="col-1 text-center">$field[mtrlagg]</td>
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
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
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
    輸出進貨的查詢結果 View
    $arrData: Array物件
    return: 查詢結果HTML Tag
    */
    function viewPchOdr($arrData){
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
                            <td class="col-1">$field[jobdate]</td>
                            <td class="col-1">$field[jobort]</td>
                            <td class="col-1">$field[splrcode]</td>
                            <td class="col-1">$field[splrapl]</td>
                            <td class="col-1">$field[mtrlcode]</td>
                            <td class="col-1">$field[mtrlapl]</td>
                            <td class="col-1 text-center">$field[mtrlamt]</td>
                            <td class="col-1 text-center">$field[mtrlprc]</td>
                            <td class="col-1 text-center">$field[mtrlagg]</td>
                            <td class="col-1 text-center">$field[expdate]</td>
                            <td class="col-1 text-center">$field[safeamt]</td>
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
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
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
    輸出領料的查詢結果 View
    $arrData: Array物件
    return: 查詢結果HTML Tag
    */
    function viewRcvMtrl($arrData){
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
                            <td class="col-1">$field[jobdate]</td>
                            <td class="col-1">$field[jobort]</td>
                            <td class="col-1">$field[splrcode]</td>
                            <td class="col-1">$field[splrapl]</td>
                            <td class="col-1">$field[mtrlcode]</td>
                            <td class="col-2">$field[mtrlapl]</td>
                            <td class="col-1 text-center">$field[mtrlamt]</td>
                            <td class="col-1 text-center">$field[mtrlprc]</td>
                            <td class="col-1 text-center">$field[mtrlagg]</td>
                            <td class="col-1 text-center">$field[safeamt]</td>
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
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-1 text-center"></td>
                        <td class="col-2 text-center"></td>
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
    function Insert($arrFormVal, $arrTbl){
        //Begin
        $this->SQL = <<<_sql
            INSERT INTO stocks (
                formcode
                , creator
                , modifier
                , jobdate
                , jobmth
                , jobort
                , splrcode
                , splrapl
                , mtrlcode
                , mtrlapl
                , mtrlamt
                , mtrlprc
                , mtrlagg
                , expdate
                , memo
            ) VALUES (
                '$arrFormVal[formcode]'
                , '$arrFormVal[creator]'
                , '$arrFormVal[modifier]'
                , '$arrFormVal[jobdate]'
                , '$arrFormVal[jobmth]'
                , '$arrFormVal[jobort]'
                , '$arrFormVal[splrcode]'
                , '{$arrTbl['suppliers']['splrapl']}'
                , '$arrFormVal[mtrlcode]'
                , '{$arrTbl['materials']['mtrlapl']}'
                , '$arrFormVal[mtrlamt]'
                , '$arrFormVal[mtrlprc]'
                , '$arrFormVal[mtrlagg]'
                , '$arrFormVal[expdate]'
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
    function Update($arrFormVal, $arrTbl){
        //Begin
        $this->SQL = <<<_sql
            UPDATE stocks
            SET
                modifier = '$arrFormVal[modifier]'
                , modifydate = current_timestamp()
                , jobdate = '$arrFormVal[jobdate]'
                , jobort = '$arrFormVal[jobort]'
                , splrcode = '$arrFormVal[splrcode]'
                , splrapl = '{$arrTbl['suppliers']['splrapl']}'
                , mtrlcode = '$arrFormVal[mtrlcode]'
                , mtrlapl = '{$arrTbl['materials']['mtrlapl']}'
                , mtrlamt = '$arrFormVal[mtrlamt]'
                , mtrlprc = '$arrFormVal[mtrlprc]'
                , mtrlagg = '$arrFormVal[mtrlagg]'
                , expdate = '$arrFormVal[expdate]'
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

    /*
    取得某項品項的最近進貨單價清單
    $splrcode: 輸入供應商代碼
    $mtrlcode: 輸入品項代碼
    */
    function getListByMtrlPrc($splrcode, $mtrlcode){
        //Begin
        $this->SQL = "SELECT COUNT(*) AS cnt from stocks WHERE 1 AND jobmth = '進貨' AND splrcode = '$splrcode' AND mtrlcode = '$mtrlcode'";
        if ($this->rtnQryField($this->SQL) > 0) {
            $this->SQL = "SELECT DISTINCT mtrlprc from stocks WHERE 1 AND jobmth = '進貨' AND splrcode = '$splrcode' AND mtrlcode = '$mtrlcode' ORDER BY seq DESC LIMIT 0, 5";
            return $this->rtnQryResults($this->SQL);
        } else {
            return array();
        }
        //echo $this->SQL;
        //End
    }


}

?>