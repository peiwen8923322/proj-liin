<?php
use Illuminate\Auth\Events\Login;
use PhpParser\Node\Stmt\Foreach_;

/*
cls_egress 外出檔
*/

class cls_egress extends cls_models
{
    //variable members

    //function members
    //建構式
    function __construct()
    {
        //Begin
        $this->self_table = "egress";
        $this->SQLFrom .= " $this->self_table ";
        parent::__construct(); //執行父類別的初始函數
        //End
    }

    //解構式
    function __destruct()
    {
        //code...
    }

    // 輸出HTML Tags --------------------------------------------------------------------------------------------
    
    // 外出查詢結果 View
    // $arrData: Array物件
    // $arrTbl: 其他參考檔(二維關聯陣列)
    // return: 查詢結果HTML Tag
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
                $html_buttons = ""; // 顯示功能按鈕
                
                $btnEditId = sprintf("edit%06d", $count);
                $btnDiscardId = sprintf("discard%06d", $count);
                if ($field['frmformcode'] == '2023010003' || $field['frmformcode'] == '2023010002' || $field['frmformcode'] == '2023010023') { // 暫存 + 單位主管已退回 + 主任退回
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnEditId' name='edit' value='編輯' attrformcode='$field[formcode]'>";
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnDiscardId' name='discard' value='註銷' attrformcode='$field[formcode]'>";
                }
                
                $tbody .= <<<_TBODY
                        <tr>
                            <td class="">
                                $html_buttons
                            </td>
                            <td class="text-center">$field[frmlistapl]</td>
                            <td class="">$field[cmpapl]</td>
                            <td class="text-center">$field[empapl]</td>
                            <td class="text-center">$field[year]</td>
                            <td class="">$field[egrersn]</td>
                            <td class="text-center">$field[begindate]</td>
                            <td class="text-center">$field[enddate]</td>
                            <td class="text-center">$field[ext_hours]</td>
                            <td class="text-center">$field[applydate]</td>
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
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
_TBODY;
        }

        return $tbody;
        //End
    }

    // 外出者新增記錄
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
                , cls
                , begindate
                , enddate
                , ext_hours
                , egrersn
                , applydate
                , frmformcode
                , frmlistapl
                , mngrformcode
                , mngrapl
                , mngrcode
                , cifformcode
                , cifapl
                , cifcode
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
                , '$arrFormVal[year]'
                , '$arrFormVal[cls]'
                , '$arrFormVal[begindate]'
                , '$arrFormVal[enddate]'
                , $arrFormVal[ext_hours]
                , '$arrFormVal[egrersn]'
                , '$arrFormVal[applydate]'
                , '{$arrTbl['frmvry']['formcode']}'
                , '{$arrTbl['frmvry']['listapl']}'
                , '{$arrTbl['emp']['mngrformcode']}'
                , '{$arrTbl['emp']['mngrapl']}'
                , '{$arrTbl['emp']['mngrcode']}'
                , '{$arrTbl['emp']['cifformcode']}'
                , '{$arrTbl['emp']['cifapl']}'
                , '{$arrTbl['emp']['cifcode']}'
            )
_sql;

        // echo $this->SQL;
        $this->PDO->exec($this->SQL);
        return $this->PDO->lastInsertId();
        //End
    }

    
    // 外出者更新記錄
    // $arrFormVal: 傳入新表單欄位值陣列
    // $arrTbl: 傳入參考其他表格的欄位陣列
    function Update($arrFormVal, $arrTbl){
        //Begin        
        $this->SQL = <<<_sql
            UPDATE $this->self_table
            SET
                modifier = '$arrFormVal[modifier]'
                , modifydate = current_timestamp()
                , deptspk = '{$arrTbl['emp']['deptspk']}'
                , cmpcode = '{$arrTbl['emp']['cmpcode']}'
                , cmpapl = '{$arrTbl['emp']['cmpapl']}'
                , empformcode = '{$arrTbl['emp']['formcode']}'
                , empapl = '{$arrTbl['emp']['empapl']}'
                , empcode = '{$arrTbl['emp']['empcode']}'
                , year = $arrFormVal[year]
                , cls = '$arrFormVal[cls]'
                , begindate = '$arrFormVal[begindate]'
                , enddate = '$arrFormVal[enddate]'
                , ext_hours = $arrFormVal[ext_hours]
                , egrersn = '$arrFormVal[egrersn]'
                , applydate = current_timestamp()
                , frmformcode = '{$arrTbl['frmvry']['formcode']}'
                , frmlistapl = '{$arrTbl['frmvry']['listapl']}'
                , mngrformcode = '{$arrTbl['emp']['mngrformcode']}'
                , mngrapl = '{$arrTbl['emp']['mngrapl']}'
                , mngrcode = '{$arrTbl['emp']['mngrcode']}'
                , cifformcode = '{$arrTbl['emp']['cifformcode']}'
                , cifapl = '{$arrTbl['emp']['cifapl']}'
                , cifcode = '{$arrTbl['emp']['cifcode']}'
            WHERE formcode = '$arrFormVal[formcode]'
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

    // 外出審核--------------------------------------------------------------------------------------------------------------

    // 過濾外出審核查詢
    // $arrData: 資料來源(二維關聯陣列)
    // $arrTbl: 其他參考檔(二維關聯陣列)
    // 傳回 新資料陣列(二維關聯陣列)
    function fltVrfQry($arrData, $arrTbl){
        //變數初始化
        $count = 0; //統計筆數
        $arrNewData = array();

        //Begin
        if (isset($arrData) && count($arrData) > 0) {
            foreach ($arrData as $field) {
                if ($arrTbl['emp']['formcode'] == $field['mngrformcode'] && $field['frmformcode'] == '2023010004') { // 單位主管 + 申請人送出
                    $count++;
                    $arrNewData[$count] = $field;
                } elseif ($arrTbl['emp']['formcode'] == $field['cifformcode'] && $field['frmformcode'] == '2023010009') { // 主任 + 單位主管已簽核
                    $count++;
                    $arrNewData[$count] = $field;
                }
                
            }
        }

        // $this->calTotalPages($count); // 計算總頁數 + 目前頁數
        return $arrNewData;
        //End
    }

    // 外出/加班審核查詢結果 View + 分頁
    // $arrData: 已過濾後的資料來源(二維關聯陣列)
    // $arrEmp: 其他參考檔(二維關聯陣列)
    // 傳回 HTML Tag
    function viewVrfQry($arrData, $arrTbl){
        //變數初始化
        $btnEditId = ""; //設定[編輯]按鈕Id屬性
        $btnDiscardId = ""; //設定[註銷]按鈕Id屬性
        $count = 0; //統計筆數

        //Begin
        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";

            for ($i = $this->intStartPos; $i <= $this->intEndPos; $i++) {
                $html_buttons = ""; // 顯示功能按鈕
                
                if ($arrTbl['emp']['formcode'] == $arrData[$i]['mngrformcode'] && $arrData[$i]['frmformcode'] == '2023010004') { // 單位主管 + 申請人送出
                    $count++;
                    $btnEditId = sprintf("edit%06d", $count);
                    $btnDiscardId = sprintf("discard%06d", $count);

                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnEditId' name='pass' value='核准' attrformcode='{$arrData[$i]['formcode']}'>";
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnDiscardId' name='reject' value='退回' attrformcode='{$arrData[$i]['formcode']}'>";
                    $tbody .= <<<_TBODY
                        <tr>
                            <td class="text-center">
                                $html_buttons
                            </td>
                            <td class="text-center">{$arrData[$i]['frmlistapl']}</td>
                            <td class="">{$arrData[$i]['cmpapl']}</td>
                            <td class="text-center">{$arrData[$i]['empapl']}</td>
                            <td class="text-center">{$arrData[$i]['year']}</td>
                            <td class="">{$arrData[$i]['egrersn']}</td>
                            <td class="text-center">{$arrData[$i]['begindate']}</td>
                            <td class="text-center">{$arrData[$i]['enddate']}</td>
                            <td class="text-center">{$arrData[$i]['ext_hours']}</td>
                            <td class="text-center">{$arrData[$i]['applydate']}</td>
                        </tr>
_TBODY;
                } elseif ($arrTbl['emp']['formcode'] == $arrData[$i]['cifformcode'] && $arrData[$i]['frmformcode'] == '2023010009') { // 主任 + 單位主管已簽核
                    $count++;
                    $btnEditId = sprintf("edit%06d", $count);
                    $btnDiscardId = sprintf("discard%06d", $count);

                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnEditId' name='pass' value='核准' attrformcode='{$arrData[$i]['formcode']}'>";
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnDiscardId' name='reject' value='退回' attrformcode='{$arrData[$i]['formcode']}'>";
                    $tbody .= <<<_TBODY
                        <tr>
                        <td class="text-center">
                                $html_buttons
                            </td>
                            <td class="text-center">{$arrData[$i]['frmlistapl']}</td>
                            <td class="">{$arrData[$i]['cmpapl']}</td>
                            <td class="text-center">{$arrData[$i]['empapl']}</td>
                            <td class="text-center">{$arrData[$i]['year']}</td>
                            <td class="">{$arrData[$i]['egrersn']}</td>
                            <td class="text-center">{$arrData[$i]['begindate']}</td>
                            <td class="text-center">{$arrData[$i]['enddate']}</td>
                            <td class="text-center">{$arrData[$i]['ext_hours']}</td>
                            <td class="text-center">{$arrData[$i]['applydate']}</td>
                        </tr>
_TBODY;
                }
                
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
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
_TBODY;
        }

        return $tbody;
        //End
    }

    // 審核者核准 / 退回
    // $arrFormVal: 傳入新表單欄位值陣列
    // $arrTbl: 傳入其他參考表格(二維關聯陣列)
    function Verify($arrFormVal, $arrTbl){
        //Begin
        if ($arrTbl['emp']['formcode'] == $arrTbl['cur_recd']['mngrformcode'] && $arrTbl['cur_recd']['frmformcode'] == '2023010004') { //單位主管 + 申請者送出
            $this->SQL = <<<_sql
                UPDATE $this->self_table
                SET
                    modifier = '$arrFormVal[modifier]'
                    , modifydate = current_timestamp()
                    , frmformcode = '{$arrTbl['frmvrf']['formcode']}'
                    , frmlistapl = '{$arrTbl['frmvrf']['listapl']}'
                    , mngrvrfdate = current_timestamp()
                WHERE formcode = '$arrFormVal[formcode]'
_sql;
        } elseif ($arrTbl['emp']['formcode'] == $arrTbl['cur_recd']['cifformcode'] && $arrTbl['cur_recd']['frmformcode'] == '2023010009') { //主任 + 單位主管已簽核
            $this->SQL = <<<_sql
                UPDATE $this->self_table
                SET
                    modifier = '$arrFormVal[modifier]'
                    , modifydate = current_timestamp()
                    , frmformcode = '{$arrTbl['frmvrf']['formcode']}'
                    , frmlistapl = '{$arrTbl['frmvrf']['listapl']}'
                    , cifvrfdate = current_timestamp()
                WHERE formcode = '$arrFormVal[formcode]'
_sql;
        }

        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        //End
    }

    // 外出統計--------------------------------------------------------------------------------------------------------------

    // 外出年度統計查詢 View
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
                        <td class="text-center">$field[year]</td>
                        <td class="text-center">$field[cnt_recds]</td>
                        <td class="text-center">$field[sum_ext_hrs]</td>
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


    // 列印年度統計報表(PDF)
    // $arrData: Array物件
    // $arrTbl: 其他參考檔(二維關聯陣列)
    // return: 傳回查詢結果HTML Tag
    function PrtPDFSttQry($arrData, $arrTbl) : string{
        //變數初始化
        $count = 0; //該頁筆數
        $thead = '';
        $tbody = '';
        $table = '';
        
        //Begin
        if ($arrTbl['cls'] == '外出') {
            $thead =<<<_THEAD
                <thead>
                    <tr>
                        <th style="width:25%;">機構</th><th style="width:25%;text-align:center;">員工</th><th style="width:25%;text-align:center;">年度</th><th style="width:25%;text-align:center;">外出次數</th>
                    </tr>
                </thead>
_THEAD;
        } elseif ($arrTbl['cls'] == '加班') {
            $thead =<<<_THEAD
                <thead>
                    <tr>
                        <th style="width:25%;">機構</th><th style="width:25%;text-align:center;">員工</th><th style="width:25%;text-align:center;">年度</th><th style="width:25%;text-align:center;">加班次數</th>
                    </tr>
                </thead>
_THEAD;
        } else {
            # code...
        }
        

        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";
            foreach ($arrData as $field) {
                $count++;
                
                $tbody .= <<<_TBODY
                    <tr>
                        <td style="width:25%;">$field[cmpapl]</td>
                        <td style="width:25%;text-align:center;">$field[empapl]</td>
                        <td style="width:25%;text-align:center;">$field[year]</td>
                        <td style="width:25%;text-align:center;">$field[cnt_recds]</td>
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
                    </tr>
                </tbody>
_TBODY;
        }

        $table =<<<_TABLE
            <table class="table table-light">
                $thead
                $tbody
            </table>
_TABLE;

        return $table;
        //End
    }

    

    // 外出歷史查詢--------------------------------------------------------------------------------------------------------------

    // 員工外出歷史查詢結果 View
    // $arrData: Array物件
    // $arrTbl: 其他參考檔(二維關聯陣列)
    // return: 回傳查詢結果HTML Tag
    function viewHstyQry($arrData, $arrTbl){
        //變數初始化
        $count = 0; //該頁筆數
        
        //Begin
        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";
            foreach ($arrData as $field) {
                $count++;
                
                $tbody .= <<<_TBODY
                        <tr>
                            <td class="text-center">$field[frmlistapl]</td>
                            <td class="">$field[cmpapl]</td>
                            <td class="text-center">$field[empapl]</td>
                            <td class="text-center">$field[year]</td>
                            <td class="">$field[egrersn]</td>
                            <td class="text-center">$field[begindate]</td>
                            <td class="text-center">$field[enddate]</td>
                            <td class="text-center">$field[ext_hours]</td>
                            <td class="text-center">$field[applydate]</td>
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

    // 列印查詢報表(PDF)
    // $arrData: Array物件
    // $arrTbl: 其他參考檔(二維關聯陣列)
    // return: 回傳查詢結果HTML Tag
    function PrtPDFByHstyQry($arrData, $arrTbl){
        //變數初始化
        $count = 0; //該頁筆數
        $thead = '';
        $tbody = '';
        $table = '';
        
        //Begin
        if ($arrTbl['cls'] == '外出') {
            $thead =<<<_THEAD
                <thead>
                    <tr>
                        <th style="width:10%;text-align:center;">審核狀態</th><th style="width:12%;">機構</th><th style="width:6%;text-align:center;">員工</th><th style="width:6%;text-align:center;">年度</th><th style="width:15%;">外出事由</th><th style="width:15%;text-align:center;">外出起始日</th><th style="width:15%;text-align:center;">外出截止日</th><th style="width:6%;text-align:center;">外出時數</th><th style="width:15%;text-align:center;">外出者簽核時間</th>
                    </tr>
                </thead>
_THEAD;
        } elseif ($arrTbl['cls'] == '加班') {
            $thead =<<<_THEAD
                <thead>
                    <tr>
                        <th style="width:10%;text-align:center;">審核狀態</th><th style="width:12%;">機構</th><th style="width:6%;text-align:center;">員工</th><th style="width:6%;text-align:center;">年度</th><th style="width:15%;">加班事由</th><th style="width:15%;text-align:center;">加班起始日</th><th style="width:15%;text-align:center;">加班截止日</th><th style="width:6%;text-align:center;">加班時數</th><th style="width:15%;text-align:center;">加班者簽核時間</th>
                    </tr>
                </thead>
_THEAD;
        } else {
            # code...
        }
        
        

        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";
            foreach ($arrData as $field) {
                $count++;
                
                $tbody .= <<<_TBODY
                        <tr>
                            <td style="width:10%;text-align:center;">$field[frmlistapl]</td>
                            <td style="width:12%;">$field[cmpapl]</td>
                            <td style="width:6%;text-align:center;">$field[empapl]</td>
                            <td style="width:6%;text-align:center;">$field[year]</td>
                            <td style="width:15%;">$field[egrersn]</td>
                            <td style="width:15%;text-align:center;">$field[begindate]</td>
                            <td style="width:15%;text-align:center;">$field[enddate]</td>
                            <td style="width:6%;text-align:center;">$field[ext_hours]</td>
                            <td style="width:15%;text-align:center;">$field[applydate]</td>
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
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
_TBODY;
        }

        $table =<<<_TABLE
            <table class="table table-light">
                $thead
                $tbody
            </table>
_TABLE;

        return $table;
        //End
    }
    









}

?>