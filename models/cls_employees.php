<?php
use Illuminate\Auth\Events\Login;
use PhpParser\Node\Stmt\Foreach_;

/*
cls_employees 員工檔
*/

class cls_employees extends cls_models
{
    //variable members

    //function members
    //建構式
    function __construct()
    {
        //Begin
        $this->self_table = "employees";
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
    // 輸出查詢結果 View
    // $arrData: Array物件
    // return: 查詢結果HTML Tag
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
                            <td class="">
                                <input type="submit" class="btn btn-outline-primary" id="$btnEditId" name="edit" value="編輯" attrformcode="$field[formcode]">
                                <input type="submit" class="btn btn-outline-primary" id="$btnDiscardId" name="discard" value="註銷" attrformcode="$field[formcode]">
                            </td>
                            <td class="">$field[cmpapl]</td>
                            <td class="text-center">$field[empapl]<br/>$field[empcode]</td>
                            <td class="text-center">$field[empidno]<br/>$field[birthday]</td>
                            <td class="text-center">$field[takeofcdate]<br/>$field[seniority]</td>
                            <td class="text-center">$field[emproleapl]<br/>$field[mngroleapl]</td>
                            <td class="text-center">$field[pryapl]<br/>$field[mngrapl]</td>
                            <td class="text-center">$field[hrapl]<br/>$field[fncapl]</td>
                            <td class="text-center"><br/>$field[cifapl]</td>
                        </tr>
_TBODY;
            }
            $tbody .= "</tbody>";
        } else {
            $tbody = <<<_TBODY
                    <tbody>
                    <tr>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                    </tr>
                    </tbody>
_TBODY;
        }

        return $tbody;
        //End
    }

    // 新增記錄
    // $arrFormVal: 傳入新表單欄位值陣列
    // $arrTbl: 傳入其他表格的參考陣列
    function Insert($arrFormVal, $arrTbl){
        //Begin
        $this->SQL = <<<_sql
            INSERT INTO employees (
                formcode
                , creator
                , modifier
                , deptspk
                , cmpcode
                , cmpapl
                , pospk
                , poscode
                , posapl
                , posmemo
                , empapl
                , empcode
                , paword
                , empidno
                , sexpk
                , sexcode
                , sexapl
                , edupk
                , educode
                , eduapl
                , telephone
                , mobilephone
                , birthday
                , blood
                , email
                , ntnpk
                , ntncode
                , ntnapl
                , ctypk
                , ctycode
                , ctyapl
                , addresses
                , liaison
                , listel
                , lismob
                , lisrelpk
                , lisrelcode
                , lisrelapl
                , mrgpk
                , mrgcode
                , mrgapl
                , wrktimpk
                , wrktimcode
                , wrktimapl
                , takeofcdate
                , othwrk
                , lang
                , pryformcode
                , pryapl
                , prycode
                , mngrformcode
                , mngrapl
                , mngrcode
                , hrformcode
                , hrapl
                , hrcode
                , fncformcode
                , fncapl
                , fnccode
                , cifformcode
                , cifapl
                , cifcode
                , chkrcnt
                , amlhrs
                , curhrs
                , emprolepk
                , emprolecode
                , emproleapl
                , mngrolepk
                , mngrolecode
                , mngroleapl
                , certificate
            ) VALUES (
                '{$arrFormVal['formcode']}'
                , '{$arrFormVal['creator']}'
                , '{$arrFormVal['modifier']}'
                , '{$arrTbl['depts']['formcode']}'
                , '{$arrTbl['depts']['cmpcode']}'
                , '{$arrTbl['depts']['cmpapl']}'
                , '{$arrTbl['position']['formcode']}'
                , '{$arrTbl['position']['listcode']}'
                , '{$arrTbl['position']['listapl']}'
                , '$arrFormVal[posmemo]'
                , '$arrFormVal[empapl]'
                , '$arrFormVal[empcode]'
                , 'ai2sjv9x'
                , '$arrFormVal[empidno]'
                , '{$arrTbl['sex']['formcode']}'
                , '{$arrTbl['sex']['listcode']}'
                , '{$arrTbl['sex']['listapl']}'
                , '{$arrTbl['edu']['formcode']}'
                , '{$arrTbl['edu']['listcode']}'
                , '{$arrTbl['edu']['listapl']}'
                , '$arrFormVal[telephone]'
                , '$arrFormVal[mobilephone]'
                , '$arrFormVal[birthday]'
                , '$arrFormVal[blood]'
                , '$arrFormVal[email]'
                , '{$arrTbl['ntn']['formcode']}'
                , '{$arrTbl['ntn']['listcode']}'
                , '{$arrTbl['ntn']['listapl']}'
                , '{$arrTbl['country']['formcode']}'
                , '{$arrTbl['country']['listcode']}'
                , '{$arrTbl['country']['listapl']}'
                , '$arrFormVal[addresses]'
                , '$arrFormVal[liaison]'
                , '$arrFormVal[listel]'
                , '$arrFormVal[lismob]'
                , '{$arrTbl['lisrel']['formcode']}'
                , '{$arrTbl['lisrel']['listcode']}'
                , '{$arrTbl['lisrel']['listapl']}'
                , '{$arrTbl['marrige']['formcode']}'
                , '{$arrTbl['marrige']['listcode']}'
                , '{$arrTbl['marrige']['listapl']}'
                , '{$arrTbl['wrktim']['formcode']}'
                , '{$arrTbl['wrktim']['listcode']}'
                , '{$arrTbl['wrktim']['listapl']}'
                , '$arrFormVal[takeofcdate]'
                , '$arrFormVal[Newothwrk]'
                , '$arrFormVal[Newlang]'
                , '{$arrTbl['proxy']['formcode']}'
                , '{$arrTbl['proxy']['empapl']}'
                , '{$arrTbl['proxy']['empcode']}'
                , '{$arrTbl['mngr']['formcode']}'
                , '{$arrTbl['mngr']['empapl']}'
                , '{$arrTbl['mngr']['empcode']}'
                , '{$arrTbl['hr']['formcode']}'
                , '{$arrTbl['hr']['empapl']}'
                , '{$arrTbl['hr']['empcode']}'
                , '{$arrTbl['finance']['formcode']}'
                , '{$arrTbl['finance']['empapl']}'
                , '{$arrTbl['finance']['empcode']}'
                , '{$arrTbl['chief']['formcode']}'
                , '{$arrTbl['chief']['empapl']}'
                , '{$arrTbl['chief']['empcode']}'
                , '$arrFormVal[chkrcnt]'
                , $arrFormVal[amlhrs]
                , $arrFormVal[curhrs]
                , '{$arrTbl['emprole']['formcode']}'
                , '{$arrTbl['emprole']['listcode']}'
                , '{$arrTbl['emprole']['listapl']}'
                , '{$arrTbl['mngrole']['formcode']}'
                , '{$arrTbl['mngrole']['listcode']}'
                , '{$arrTbl['mngrole']['listapl']}'
                , '$arrFormVal[certificate]'
            )
_sql;

        $this->PDO->exec($this->SQL);
        // echo $this->SQL;
        return $this->PDO->lastInsertId();
        //End
    }

    // 更新記錄
    // $arrFormVal: 傳入新表單欄位值陣列
    // $arrTbl: 傳入參考其他表格的欄位陣列
    function Update($arrFormVal, $arrTbl){
        //Begin
        $this->SQL = <<<_sql
            UPDATE employees
            SET
                modifier = '{$arrFormVal['modifier']}'
                , modifydate = current_timestamp()
                , deptspk = '{$arrTbl['depts']['formcode']}'
                , cmpcode = '{$arrTbl['depts']['cmpcode']}'
                , cmpapl = '{$arrTbl['depts']['cmpapl']}'
                , pospk = '{$arrTbl['position']['formcode']}'
                , poscode = '{$arrTbl['position']['listcode']}'
                , posapl = '{$arrTbl['position']['listapl']}'
                , posmemo = '$arrFormVal[posmemo]'
                , empapl = '$arrFormVal[empapl]'
                , empcode = '$arrFormVal[empcode]'
                , empidno = '$arrFormVal[empidno]'
                , sexpk = '{$arrTbl['sex']['formcode']}'
                , sexcode = '{$arrTbl['sex']['listcode']}'
                , sexapl = '{$arrTbl['sex']['listapl']}'
                , edupk = '{$arrTbl['edu']['formcode']}'
                , educode = '{$arrTbl['edu']['listcode']}'
                , eduapl = '{$arrTbl['edu']['listapl']}'
                , telephone = '$arrFormVal[telephone]'
                , mobilephone = '$arrFormVal[mobilephone]'
                , birthday = '$arrFormVal[birthday]'
                , blood = '$arrFormVal[blood]'
                , email = '$arrFormVal[email]'
                , ntnpk = '{$arrTbl['ntn']['formcode']}'
                , ntncode = '{$arrTbl['ntn']['listcode']}'
                , ntnapl = '{$arrTbl['ntn']['listapl']}'
                , ctypk = '{$arrTbl['country']['formcode']}'
                , ctycode = '{$arrTbl['country']['listcode']}'
                , ctyapl = '{$arrTbl['country']['listapl']}'
                , addresses = '$arrFormVal[addresses]'
                , liaison = '$arrFormVal[liaison]'
                , listel = '$arrFormVal[listel]'
                , lismob = '$arrFormVal[lismob]'
                , lisrelpk = '{$arrTbl['lisrel']['formcode']}'
                , lisrelcode = '{$arrTbl['lisrel']['listcode']}'
                , lisrelapl = '{$arrTbl['lisrel']['listapl']}'
                , mrgpk = '{$arrTbl['marrige']['formcode']}'
                , mrgcode = '{$arrTbl['marrige']['listcode']}'
                , mrgapl = '{$arrTbl['marrige']['listapl']}'
                , wrktimpk = '{$arrTbl['wrktim']['formcode']}'
                , wrktimcode = '{$arrTbl['wrktim']['listcode']}'
                , wrktimapl = '{$arrTbl['wrktim']['listapl']}'
                , takeofcdate = '$arrFormVal[takeofcdate]'
                , levofcdate = '$arrFormVal[levofcdate]'
                , atwrksta = $arrFormVal[atwrksta]
                , othwrk = '$arrFormVal[Newothwrk]'
                , lang = '$arrFormVal[Newlang]'
                , pryformcode = '{$arrTbl['proxy']['formcode']}'
                , pryapl = '{$arrTbl['proxy']['empapl']}'
                , prycode = '{$arrTbl['proxy']['empcode']}'
                , mngrformcode = '{$arrTbl['mngr']['formcode']}'
                , mngrapl = '{$arrTbl['mngr']['empapl']}'
                , mngrcode = '{$arrTbl['mngr']['empcode']}'
                , hrformcode = '{$arrTbl['hr']['formcode']}'
                , hrapl = '{$arrTbl['hr']['empapl']}'
                , hrcode = '{$arrTbl['hr']['empcode']}'
                , fncformcode = '{$arrTbl['finance']['formcode']}'
                , fncapl = '{$arrTbl['finance']['empapl']}'
                , fnccode = '{$arrTbl['finance']['empcode']}'
                , cifformcode = '{$arrTbl['chief']['formcode']}'
                , cifapl = '{$arrTbl['chief']['empapl']}'
                , cifcode = '{$arrTbl['chief']['empcode']}'
                , chkrcnt = '$arrFormVal[chkrcnt]'
                , amlhrs = $arrFormVal[amlhrs]
                , curhrs = $arrFormVal[curhrs]
                , emprolepk = '{$arrTbl['emprole']['formcode']}'
                , emprolecode = '{$arrTbl['emprole']['listcode']}'
                , emproleapl = '{$arrTbl['emprole']['listapl']}'
                , mngrolepk = '{$arrTbl['mngrole']['formcode']}'
                , mngrolecode = '{$arrTbl['mngrole']['listcode']}'
                , mngroleapl = '{$arrTbl['mngrole']['listapl']}'
                , certificate = '$arrFormVal[certificate]'
            WHERE formcode = '{$arrFormVal['formcode']}';
_sql;

        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        //End
    }

    // 註銷記錄
    // $selFormCode: 傳入目前記錄的FormCode值
    function discard($selFormCode)
    {
        //Begin
        $this->SQL = "UPDATE $this->self_table SET formstate = 14 WHERE formcode='$selFormCode'";
        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        //End
    }

    // ---------------------------------------------------------------------------------------------------------------------------------------------------------

    //  檢查登入者的帳號/密碼是否正確
    //  $arrEmp: 傳入員工記錄陣列
    //  return: 傳回 true/false
    function isExistEmp($arrEmp){
        //Begin
        $this->SQL = "SELECT COUNT(*) AS cntEmp FROM $this->self_table WHERE 1 AND formstate = 15 AND empcode = '$arrEmp[account]' AND paword = '$arrEmp[passwd]'";
        $cntEmp = $this->rtnQryField($this->SQL);
        return $cntEmp == 1 ? true: false;
        //End
    }

    //  取得單筆員工記錄(以員工代碼)
    //  $empcode: 傳入員工代碼
    //  return: 傳回一維關聯陣列
    function getEmp($empcode){
        //Begin
        $this->SQL = "SELECT * FROM $this->self_table WHERE 1 AND formstate = 15 AND empcode = '$empcode'";
        return $this->rtnQryRecord($this->SQL);
        //End
    }

    // 取得員工清單(目前使用中的記錄)
    // return: 傳回二維關聯陣列
    function getEmpListByUsage(){
        //Begin
        $this->SQL = "SELECT * FROM $this->self_table WHERE formstate = 15";
        return $this->rtnQryResults($this->SQL);
        //End
    }

    //  取得在職的員工清單
    //  return: 傳回二維關聯陣列
    function getListAtWork(){
        //Begin
        $this->SQL = "SELECT *, CONCAT(empcode, '-', empapl) AS NewEmpapl FROM employees WHERE 1 AND formstate = 15 AND (levofcdate IS NULL OR levofcdate = '0000-00-00') ORDER BY empcode";
        return $this->rtnQryResults($this->SQL);
        //End
    }

    //  變更密碼
    //  $NewPassword: 傳入新密碼
    //  $formcode: 傳入表單編號
    function chgPassword($NewPassword, $formcode){
        //Begin
        $this->SQL = "UPDATE employees SET modifydate = current_timestamp(), paword = '$NewPassword' WHERE formcode = '$formcode'";
        $this->PDO->exec($this->SQL);
        //End
    }




    



}

?>