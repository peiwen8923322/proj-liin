<?php
use Illuminate\Auth\Events\Login;
use PhpParser\Node\Stmt\Foreach_;

/*
cls_clockin 刷卡檔
*/

class cls_clockin extends cls_models
{
    //variable members

    //function members
    //建構式
    function __construct()
    {
        //Begin
        $this->self_table = "clockin";
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
    // $arrData: 資料來源(二維關聯陣列)
    // $arrTbl: 參考其他檔(二維關聯陣列)
    // return: 傳回查詢結果HTML Tag
    function viewQry($arrData, $arrTbl){
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
                            <td class="">$field[cmpapl]</td>
                            <td class="text-center">$field[empapl]</td>
                            <td class="text-center">$field[year]</td>
                            <td class="">$field[clkintime]</td>
                            <td class="text-center">$field[clkinsttapl]</td>
                            <td class="">$field[extodnymemo]</td>
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
            INSERT INTO $this->self_table (
                formcode
                , creator
                , modifier
                , deptspk
                , cmpcode
                , cmpapl
                , empformcode
                , empapl
                , empcode
                , year
                , clkintime
                , isnormality
                , clkinsttpk
                , clkinsttcode
                , clkinsttapl
                , extodnymemo
            ) VALUES (
                '$arrFormVal[formcode]'
                , '$arrFormVal[creator]'
                , '$arrFormVal[modifier]'
                , '{$arrTbl['emp']['deptspk']}'
                , '{$arrTbl['emp']['cmpcode']}'
                , '{$arrTbl['emp']['cmpapl']}'
                , '{$arrTbl['emp']['formcode']}'
                , '{$arrTbl['emp']['empapl']}'
                , '{$arrTbl['emp']['empcode']}'
                , $arrFormVal[year]
                , '$arrFormVal[clkintime]'
                , '$arrFormVal[isnormality]'
                , '{$arrTbl['clkinstt']['formcode']}'
                , '{$arrTbl['clkinstt']['listcode']}'
                , '{$arrTbl['clkinstt']['listapl']}'
                , '$arrFormVal[extodnymemo]'
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
            UPDATE materials
            SET
                modifier = '$arrFormVal[modifier]'
                , modifydate = current_timestamp()
                , splrcode = '{$arrTbl['suppliers']['splrcode']}'
                , splrapl = '{$arrTbl['suppliers']['splrapl']}'
                , mtrlcode = '$arrFormVal[mtrlcode]'
                , mtrlapl = '$arrFormVal[mtrlapl]'
                , safeamt = '$arrFormVal[safeamt]'
            WHERE formcode = '$arrFormVal[formcode]'
_sql;

        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        //End
    }

    // 註銷記錄
    // $selFormCode: 傳入目前記錄的FormCode值
    function discard($selFormCode){
        //Begin
        $this->SQL = "UPDATE $this->self_table SET formstate = 14 WHERE formcode='$selFormCode'";
        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        //End
    }










    //------------------------------------------------------------------------------------------------------

    // 取得刷卡統計並重組查詢結果
    function getNewData($SQL) : array {
        $arrStt = array(); // 重組後的查詢結果
        $arrData = array(); // 資料來源
        $tmp = array('year'=>'', 'cmpcode'=>'', 'empformcode'=>''); // 暫存記錄

        //Begin
        $arrData = $this->rtnQryResults($SQL);
        if (count($arrData) > 0) {
            foreach ($arrData as $value) {
                if ($value['year'] == $tmp['year'] && $value['cmpcode'] == $tmp['cmpcode'] && $value['empformcode'] == $tmp['empformcode']) { // 加入同一位員工的其他刷卡統計
                    if ($value['isnormality'] == '正常') { // 正常
                        $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['normal_rcds']++; // 刷卡正常
                        $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttl_rcds']++; // 刷卡總筆數
                    }
                    if ($value['isnormality'] == '異常') { // 異常
                        $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['nonnormal_rcds']++; // 刷卡異常
                        $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttl_rcds']++; // 刷卡總筆數
                    }
                } else { // 加入新記錄(新員工) + 暫存目前記錄
                    $arrStt["$value[year]$value[deptspk]$value[empformcode]"] = array(
                        'year'=>$value['year'], 'deptspk'=>$value['deptspk'], 'cmpcode'=>$value['cmpcode'], 'cmpapl'=>$value['cmpapl']
                        , 'empformcode'=>$value['empformcode'], 'empcode'=>$value['empcode'], 'empapl'=>$value['empapl']
                        , 'ttl_rcds'=> 0, 'normal_rcds'=> 0, 'nonnormal_rcds'=> 0
                    );

                    $tmp['year'] = $value['year']; // 年度
                    $tmp['cmpcode'] = $value['cmpcode']; // 單位唯一識別碼
                    $tmp['empformcode'] = $value['empformcode']; // 員工唯一識別碼

                    switch ($value['isnormality']) { // 刷卡是否正常
                        case '正常': // 正常
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['normal_rcds']++; // 刷卡正常
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttl_rcds']++; // 刷卡總筆數
                            break;
                        case '異常': // 異常
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['nonnormal_rcds']++; // 刷卡異常
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttl_rcds']++; // 刷卡總筆數
                            break;
                        default:
                            # code...
                            break;
                    }
                }

            }
        }

        return $arrStt;
        //End
    }

    // 員工刷卡年度統計查詢 View
    // $arrData: Array物件
    // $arrTbl: 其他參考檔(二維關聯陣列)
    // return: 傳回查詢結果HTML Tag
    function viewSttQry($arrData, $arrTbl) : string{
        //變數初始化
        $count = 0; //該頁筆數
        
        //Begin
        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";
            foreach ($arrData as $field) {
                $count++;
                
                $tbody .= <<<_TBODY
                    <tr>
                        <td class="">$field[cmpapl]</td>
                        <td class="text-center">$field[empapl]</td>
                        <td class="text-center">$field[empcode]</td>
                        <td class="text-center">$field[year]</td>
                        <td class="text-center">$field[ttl_rcds]</td>
                    </tr>
_TBODY;
            }
            $tbody .= "</tbody>";
        } else {
            $tbody = <<<_TBODY
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
_TBODY;
        }

        $this->calTotalPages($count); // 計算總頁數 + 目前頁數
        return $tbody;
        //End
    }














}

?>