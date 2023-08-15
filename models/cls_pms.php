<?php
use Illuminate\Database\Eloquent\Model;
/*
 class prgs 程式檔
 */
class cls_pms extends cls_models{
    //variable members

    //function members
    /*
    建構式
     */
    public function __construct() {
        //Begin
        $this->self_table = "pms";
        $this->SQLFrom .= " $this->self_table ";
        parent::__construct(); //執行父類別的初始函數
        //End
    }

    //解構式
    function __destruct()
    {
        //code...
    }

    //  取得該員工的程式分類記錄是否存在
    //  $empformcode: 傳入員工唯一識別碼
    //  $prgcls: 傳入程式類別
    //  return: 傳回記錄是否存在(true: false)
    function isExistPms($empformcode, $prgcls){
        //Begin
        $this->SQL = "SELECT COUNT(*) AS cntPms FROM pms WHERE 1 AND formstate = 15 AND empformcode = '$empformcode' AND prgcls = '$prgcls'";
        return $this->rtnQryField($this->SQL) == 1 ? true: false;
        //End
    }

    //  更新記錄
    //  $arrFormVal: 傳入新表單欄位值陣列
    //  $arrTbl: 傳入其他表格的參考陣列
    function Update($arrFormVal, $arrTbl){
        //Begin
        $this->SQL = "UPDATE pms SET modifier = '$arrTbl[empapl]', modifydate = CURRENT_TIMESTAMP(), prgact = '$arrFormVal[actions]' WHERE 1 AND formstate = 15 AND empformcode = '$arrTbl[formcode]' AND prgcls = '$arrFormVal[prgcls]'";
        $this->PDO->exec($this->SQL);
        // echo "$this->SQL<br/>";
        //End
    }

    // 新增記錄
    // $arrFormVal: 傳入新表單欄位值陣列
    // $arrTbl: 傳入其他表格的參考陣列
    function Insert($arrFormVal, $arrTbl){
        //Begin
        $this->SQL = <<<_sql
            INSERT INTO pms (
                formcode
                , creator
                , modifier
                , empformcode
                , empapl
                , empcode
                , prgcls
                , prgact
            ) VALUES (
                '{$arrFormVal['formcode']}'
                , '{$arrFormVal['creator']}'
                , '{$arrFormVal['modifier']}'
                , '{$arrTbl['formcode']}'
                , '{$arrTbl['empapl']}'
                , '{$arrTbl['empcode']}'
                , '$arrFormVal[prgcls]'
                , '$arrFormVal[actions]'
            )
_sql;

        $this->PDO->exec($this->SQL);
        // echo "$this->SQL<br/>";
        //End
    }
    
    // 註銷記錄
    // $selFormCode: 傳入目前記錄的FormCode值
    function discard($selFormCode){
        //Begin
        $this->SQL = "DELETE FROM pms WHERE formcode = '$selFormCode'";
        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        //End
    }

    // 刪除員工的權限記錄
    // $empformcode: 傳入員工檔的唯一識別碼(empformcode)
    function discardEmpPms($empformcode){
        //Begin
        $this->SQL = "DELETE FROM pms WHERE empformcode = '$empformcode'";
        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        //End
    }
    
    // 輸出查詢結果 View
    // $arrData: Array物件
    // return: 查詢結果HTML Tag
    function viewQry($arrData){
        //變數初始化
        $btnDiscardId = ""; //設定[註銷]按鈕Id屬性
        $count = 0; //該頁筆數

        //Begin
        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";
            foreach ($arrData as $field) {
                $count++;
                $btnDiscardId = sprintf("discard%06d", $count);
                $tbody .= <<<_TBODY
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" id="$btnDiscardId" name="discard" value="註銷" attrformcode="$field[pmsformcode]">
                            </td>
                            <td class="col-2">$field[cmpapl]</td>
                            <td class="col-1 text-center">$field[empapl]</td>
                            <td class="col-1">$field[prgcls]</td>
                            <td class="col-1">$field[prgact]</td>
                        </tr>
_TBODY;
            }
            $tbody .= "</tbody>";
        } else {
            $tbody = <<<_TBODY
                    <tbody>
                    <tr>
                        <td class="col-1"></td>
                        <td class="col-2"></td>
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
    
    //  使用者是否擁有該程式的使用權限
    //  $empapl: 傳入員工姓名
    //  $prgcls: 傳入程式類別
    //  $prgact: 傳入程式執行權限
    //  return: 傳回是否有使用權限(true: false)
    function isOwnPms($empapl, $prgcls, $prgact){
        //Begin
        $this->SQL = "SELECT COUNT(*) AS cntPms FROM pms WHERE 1 AND formstate = 15 AND empapl = '$empapl' AND prgcls = '$prgcls' AND prgact LIKE '%$prgact%'";
        return $this->rtnQryField($this->SQL) == 1 ? true: false;
        //End
    }

    //  使用者是否擁有該程式的使用權限
    //  $empformcode: 傳入員工唯一識別碼
    //  $prgcls: 傳入程式類別
    //  $prgact: 傳入程式執行權限
    //  return: 傳回是否有使用權限(true: false)
    function isOwnPmsByEmpformcode($empformcode, $prgcls, $prgact){
        //Begin
        $this->SQL = "SELECT COUNT(*) AS cntPms FROM pms WHERE 1 AND formstate = 15 AND empformcode = '$empformcode' AND prgcls = '$prgcls' AND prgact LIKE '%$prgact%'";
        return $this->rtnQryField($this->SQL) == 1 ? true: false;
        //End
    }







    
}
?>