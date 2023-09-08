<?php
use Illuminate\Auth\Events\Login;
use PhpParser\Node\Stmt\Foreach_;

/*
cls_holidays 請假檔
*/

class cls_holidays extends cls_models
{
    //variable members

    //function members
    //建構式
    function __construct()
    {
        //Begin
        $this->self_table = "holidays";
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
    
    // 員工請假查詢結果 View
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
                if ($field['frmformcode'] == '2023010003' || $field['frmformcode'] == '2023010001' || $field['frmformcode'] == '2023010002' || $field['frmformcode'] == '2023010022' || $field['frmformcode'] == '2023010023') { //暫存 + 代理人已退回 + 單位主管已退回 + 人事主管退回 + 主任退回 
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnEditId' name='edit' value='編輯' attrformcode='$field[formcode]'>";
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnDiscardId' name='discard' value='註銷' attrformcode='$field[formcode]'>";
                }
                
                $tbody .= <<<_TBODY
                        <tr>
                            <td class="">
                                $html_buttons
                            </td>
                            <td class="">$field[frmlistapl]</td>
                            <td class="text-center">$field[year]</td>
                            <td class="text-center">$field[empapl]</td>
                            <td class="text-center">$field[pryapl]</td>
                            <td class="text-center">$field[hldclsapl]</td>
                            <td class="">$field[hldrsn]</td>
                            <td class="text-center">$field[applydate]</td>
                            <td class="text-center">$field[begindate]</td>
                            <td class="text-center">$field[enddate]</td>
                            <td class="text-center">$field[hldsdays]</td>
                            <td class="text-center">$field[hldshrs]</td>
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
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
_TBODY;
        }

        return $tbody;
        //End
    }

    // 列印員工請假資料(PDF檔)
    // $arrData: Array物件
    // $arrTbl: 其他參考檔(二維關聯陣列)
    // return: 查詢結果HTML Tag
    function PrtPDFByQry($arrData, $arrTbl){
        //變數初始化
        $count = 0; //該頁筆數
        $thead = '';
        $tbody = '';
        $table = '';
        
        //Begin
        $thead =<<<_THEAD
            <thead>
                <tr>
                    <th style="width:10%;">審核狀態</th><th style="width:6%;">年度</th><th style="width:6%;">員工</th><th style="width:6%;">代理人</th><th style="width:6%;">假別</th><th style="width:15%;">請假事由</th><th style="width:12%;">假單送出時間</th><th style="width:12%;">請假啟始日</th><th style="width:12%;">請假截止日</th><th style="width:8%;text-align:center;">請假天數</th><th style="width:8%;text-align:center;">請假時數</th>
                </tr>
            </thead>
_THEAD;

        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";
            foreach ($arrData as $field) {
                $count++;
                
                $tbody .= <<<_TBODY
                        <tr>
                            <td style="width:10%;">$field[frmlistapl]</td>
                            <td style="width:6%;">$field[year]</td>
                            <td style="width:6%;">$field[empapl]</td>
                            <td style="width:6%;">$field[pryapl]</td>
                            <td style="width:6%;">$field[hldclsapl]</td>
                            <td style="width:15%;">$field[hldrsn]</td>
                            <td style="width:12%;">$field[applydate]</td>
                            <td style="width:12%;">$field[begindate]</td>
                            <td style="width:12%;">$field[enddate]</td>
                            <td style="width:8%;text-align:center;">$field[hldsdays]</td>
                            <td style="width:8%;text-align:center;">$field[hldshrs]</td>
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

    // 請假者新增記錄
    // $arrFormVal: 傳入新表單欄位值陣列
    // $arrTbl: 傳入其他表格的參考陣列
    function Insert($arrFormVal, $arrTbl){
        //Begin
        $this->SQL = <<<_sql
            INSERT INTO holidays (
                formcode
                , creator
                , modifier
                , empformcode
                , empapl
                , empcode
                , takeofcdate
                , amlhrs
                , curhrs
                , emprolepk
                , emproleapl
                , year
                , hldformcode
                , hldclsapl
                , hldrsn
                , begindate
                , enddate
                , hldsdays
                , hldshrs
                , aftrest
                , frmformcode
                , frmlistapl
                , pryformcode
                , pryapl
                , prycode
                , mngrformcode
                , mngrapl
                , mngrcode
                , hrformcode
                , hrapl
                , hrcode
                , cifformcode
                , cifapl
                , cifcode
            ) VALUES (
                '$arrFormVal[formcode]'
                , '$arrFormVal[creator]'
                , '$arrFormVal[modifier]'
                , '{$arrTbl['emp']['formcode']}'
                , '{$arrTbl['emp']['empapl']}'
                , '{$arrTbl['emp']['empcode']}'
                , '{$arrTbl['emp']['takeofcdate']}'
                , '{$arrTbl['emp']['amlhrs']}'
                , '{$arrTbl['emp']['curhrs']}'
                , '{$arrTbl['emp']['emprolepk']}'
                , '{$arrTbl['emp']['emproleapl']}'
                , '$arrFormVal[year]'
                , '{$arrTbl['hlds']['formcode']}'
                , '{$arrTbl['hlds']['listapl']}'
                , '$arrFormVal[hldrsn]'
                , '$arrFormVal[begindate]'
                , '$arrFormVal[enddate]'
                , $arrFormVal[hldsdays]
                , $arrFormVal[hldshrs]
                , '{$arrTbl['aftrest']['listapl']}'
                , '{$arrTbl['frmvry']['formcode']}'
                , '{$arrTbl['frmvry']['listapl']}'
                , '{$arrTbl['proxy']['formcode']}'
                , '{$arrTbl['proxy']['empapl']}'
                , '{$arrTbl['proxy']['empcode']}'
                , '{$arrTbl['emp']['mngrformcode']}'
                , '{$arrTbl['emp']['mngrapl']}'
                , '{$arrTbl['emp']['mngrcode']}'
                , '{$arrTbl['emp']['hrformcode']}'
                , '{$arrTbl['emp']['hrapl']}'
                , '{$arrTbl['emp']['hrcode']}'
                , '{$arrTbl['emp']['cifformcode']}'
                , '{$arrTbl['emp']['cifapl']}'
                , '{$arrTbl['emp']['cifcode']}'
            )
_sql;

        $this->PDO->exec($this->SQL);
        //echo $this->SQL;
        return $this->PDO->lastInsertId();
        //End
    }

    
    // 請假者更新記錄
    // $arrFormVal: 傳入新表單欄位值陣列
    // $arrTbl: 傳入參考其他表格的欄位陣列
    function Update($arrFormVal, $arrTbl){
        //Begin        
        $this->SQL = <<<_sql
            UPDATE holidays
            SET
                modifier = '$arrFormVal[modifier]'
                , modifydate = current_timestamp()
                , empformcode = '{$arrTbl['emp']['formcode']}'
                , empapl = '{$arrTbl['emp']['empapl']}'
                , empcode = '{$arrTbl['emp']['empcode']}'
                , takeofcdate = '{$arrTbl['emp']['takeofcdate']}'
                , amlhrs = '{$arrTbl['emp']['amlhrs']}'
                , curhrs = '{$arrTbl['emp']['curhrs']}'
                , emprolepk = '{$arrTbl['emp']['emprolepk']}'
                , emproleapl = '{$arrTbl['emp']['emproleapl']}'
                , year = '$arrFormVal[year]'
                , hldformcode = '{$arrTbl['hlds']['formcode']}'
                , hldclsapl = '{$arrTbl['hlds']['listapl']}'
                , hldrsn = '$arrFormVal[hldrsn]'
                , begindate = '$arrFormVal[begindate]'
                , enddate = '$arrFormVal[enddate]'
                , hldsdays = $arrFormVal[hldsdays]
                , hldshrs = $arrFormVal[hldshrs]
                , aftrest = '{$arrTbl['aftrest']['listapl']}'
                , applydate = current_timestamp()
                , frmformcode = '{$arrTbl['frmvry']['formcode']}'
                , frmlistapl = '{$arrTbl['frmvry']['listapl']}'
                , pryformcode = '{$arrTbl['proxy']['formcode']}'
                , pryapl = '{$arrTbl['proxy']['empapl']}'
                , prycode = '{$arrTbl['proxy']['empcode']}'
                , mngrformcode = '{$arrTbl['emp']['mngrformcode']}'
                , mngrapl = '{$arrTbl['emp']['mngrapl']}'
                , mngrcode = '{$arrTbl['emp']['mngrcode']}'
                , hrformcode = '{$arrTbl['emp']['hrformcode']}'
                , hrapl = '{$arrTbl['emp']['hrapl']}'
                , hrcode = '{$arrTbl['emp']['hrcode']}'
                , cifformcode = '{$arrTbl['emp']['cifformcode']}'
                , cifapl = '{$arrTbl['emp']['cifapl']}'
                , cifcode = '{$arrTbl['emp']['cifcode']}'
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

    // 請假審核--------------------------------------------------------------------------------------------------------------

    // 過濾請假審核查詢
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
                if ($arrTbl['emp']['formcode'] == $field['pryformcode'] && $field['frmformcode'] == '2023010004') { // 代理人 + 送出
                    $count++;
                    $arrNewData[$count] = $field;
                } elseif ($arrTbl['emp']['formcode'] == $field['mngrformcode'] && $field['emprolepk'] == '2023010012' && $field['frmformcode'] == '2023010006') { // 單位主管 + 申請者(員工身份) + 代理人已簽核
                    $count++;
                    $arrNewData[$count] = $field;
                } elseif ($arrTbl['emp']['formcode'] == $field['cifformcode'] && (($field['emprolepk'] == '2023010012' && $field['frmformcode'] == '2023010009') || ($field['emprolepk'] == '2023010013' && $field['frmformcode'] == '2023010006'))) { // 主任 + (申請者(員工身份)+單位主管已簽核 OR 申請者(單位主管身份)+代理人已簽核)
                    $count++;
                    $arrNewData[$count] = $field;
                }
                
            }
        }

        $this->calTotalPages($count); // 計算總頁數 + 目前頁數
        return $arrNewData;
        //End
    }

    // 請假審核查詢結果 View + 分頁
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
                
                if ($arrTbl['emp']['formcode'] == $arrData[$i]['pryformcode'] && $arrData[$i]['frmformcode'] == '2023010004') { // 代理人 + 送出
                    $count++;
                    $btnEditId = sprintf("edit%06d", $count);
                    $btnDiscardId = sprintf("discard%06d", $count);

                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnEditId' name='pass' value='核准' attrformcode='{$arrData[$i]['formcode']}'>";
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnDiscardId' name='reject' value='退回' attrformcode='{$arrData[$i]['formcode']}'>";
                    $tbody .= <<<_TBODY
                        <tr>
                            <td class="">
                                $html_buttons
                            </td>
                            <td class="">{$arrData[$i]['frmlistapl']}</td>
                            <td class="text-center">{$arrData[$i]['year']}</td>
                            <td class="text-center">{$arrData[$i]['cmpapl']}</td>
                            <td class="text-center">{$arrData[$i]['empapl']}</td>
                            <td class="text-center">{$arrData[$i]['pryapl']}</td>
                            <td class="text-center">{$arrData[$i]['hldclsapl']}</td>
                            <td class="">{$arrData[$i]['hldrsn']}</td>
                            <td class="text-center">{$arrData[$i]['applydate']}</td>
                            <td class="text-center">{$arrData[$i]['begindate']}</td>
                            <td class="text-center">{$arrData[$i]['enddate']}</td>
                            <td class="text-center">{$arrData[$i]['hldsdays']}<br/>{$arrData[$i]['hldshrs']}</td>
                        </tr>
_TBODY;
                } elseif ($arrTbl['emp']['formcode'] == $arrData[$i]['mngrformcode'] && $arrData[$i]['emprolepk'] == '2023010012' && $arrData[$i]['frmformcode'] == '2023010006') { // 單位主管 + 申請者(員工身份) + 代理人已簽核
                    $count++;
                    $btnEditId = sprintf("edit%06d", $count);
                    $btnDiscardId = sprintf("discard%06d", $count);

                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnEditId' name='pass' value='核准' attrformcode='{$arrData[$i]['formcode']}'>";
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnDiscardId' name='reject' value='退回' attrformcode='{$arrData[$i]['formcode']}'>";
                    $tbody .= <<<_TBODY
                        <tr>
                            <td class="">
                                $html_buttons
                            </td>
                            <td class="">{$arrData[$i]['frmlistapl']}</td>
                            <td class="text-center">{$arrData[$i]['year']}</td>
                            <td class="text-center">{$arrData[$i]['cmpapl']}</td>
                            <td class="text-center">{$arrData[$i]['empapl']}</td>
                            <td class="text-center">{$arrData[$i]['pryapl']}</td>
                            <td class="text-center">{$arrData[$i]['hldclsapl']}</td>
                            <td class="">{$arrData[$i]['hldrsn']}</td>
                            <td class="text-center">{$arrData[$i]['applydate']}</td>
                            <td class="text-center">{$arrData[$i]['begindate']}</td>
                            <td class="text-center">{$arrData[$i]['enddate']}</td>
                            <td class="text-center">{$arrData[$i]['hldsdays']}<br/>{$arrData[$i]['hldshrs']}</td>
                        </tr>
_TBODY;
                } elseif ($arrTbl['emp']['formcode'] == $arrData[$i]['cifformcode'] && (($arrData[$i]['emprolepk'] == '2023010012' && $arrData[$i]['frmformcode'] == '2023010009') || ($arrData[$i]['emprolepk'] == '2023010013' && $arrData[$i]['frmformcode'] == '2023010006'))) { // 主任 + (申請者(員工身份)+單位主管已簽核 OR 申請者(單位主管身份)+代理人已簽核)
                    $count++;
                    $btnEditId = sprintf("edit%06d", $count);
                    $btnDiscardId = sprintf("discard%06d", $count);

                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnEditId' name='pass' value='核准' attrformcode='{$arrData[$i]['formcode']}'>";
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnDiscardId' name='reject' value='退回' attrformcode='{$arrData[$i]['formcode']}'>";
                    $tbody .= <<<_TBODY
                        <tr>
                            <td class="">
                                $html_buttons
                            </td>
                            <td class="">{$arrData[$i]['frmlistapl']}</td>
                            <td class="text-center">{$arrData[$i]['year']}</td>
                            <td class="text-center">{$arrData[$i]['cmpapl']}</td>
                            <td class="text-center">{$arrData[$i]['empapl']}</td>
                            <td class="text-center">{$arrData[$i]['pryapl']}</td>
                            <td class="text-center">{$arrData[$i]['hldclsapl']}</td>
                            <td class="">{$arrData[$i]['hldrsn']}</td>
                            <td class="text-center">{$arrData[$i]['applydate']}</td>
                            <td class="text-center">{$arrData[$i]['begindate']}</td>
                            <td class="text-center">{$arrData[$i]['enddate']}</td>
                            <td class="text-center">{$arrData[$i]['hldsdays']}<br/>{$arrData[$i]['hldshrs']}</td>
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
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
_TBODY;
        }

        return $tbody;
        //End
    }

    // 列印員工請假審核報表(PDF檔)
    // $arrData: 已過濾後的資料來源(二維關聯陣列)
    // $arrEmp: 其他參考檔(二維關聯陣列)
    // 傳回 HTML Tag
    function PrtPDFByVrfQry($arrData, $arrTbl){
        //變數初始化
        $count = 0; //統計筆數
        $thead = '';
        $tbody = '';
        $table = '';

        //Begin
        $thead =<<<_THEAD
            <thead>
                <tr>
                    <th style="width:10%;">審核狀態</th><th style="width:4%;">年度</th><th style="width:10%;">機構</th><th style="width:6%;">員工</th><th style="width:6%;">代理人</th><th style="width:6%;">假別</th><th style="width:15%;">請假事由</th><th style="width:12%;text-align:center;">假單送出時間</th><th style="width:12%;text-align:center;">請假啟始日</th><th style="width:12%;text-align:center;">請假截止日</th><th style="width:8%;text-align:center;">請假天數<br/>請假時數</th>
                </tr>
            </thead>
_THEAD;

        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";

            for ($i = $this->intStartPos; $i <= $this->intEndPos; $i++) {
                if ($arrTbl['emp']['formcode'] == $arrData[$i]['pryformcode'] && $arrData[$i]['frmformcode'] == '2023010004') { // 代理人 + 送出
                    $count++;
                    
                    $tbody .= <<<_TBODY
                        <tr>
                            <td style="width:10%;">{$arrData[$i]['frmlistapl']}</td>
                            <td style="width:4%;">{$arrData[$i]['year']}</td>
                            <td style="width:10%;">{$arrData[$i]['cmpapl']}</td>
                            <td style="width:6%;">{$arrData[$i]['empapl']}</td>
                            <td style="width:6%;">{$arrData[$i]['pryapl']}</td>
                            <td style="width:6%;">{$arrData[$i]['hldclsapl']}</td>
                            <td style="width:15%;">{$arrData[$i]['hldrsn']}</td>
                            <td style="width:12%;text-align:center;">{$arrData[$i]['applydate']}</td>
                            <td style="width:12%;text-align:center;">{$arrData[$i]['begindate']}</td>
                            <td style="width:12%;text-align:center;">{$arrData[$i]['enddate']}</td>
                            <td style="width:8%;text-align:center;">{$arrData[$i]['hldsdays']}<br/>{$arrData[$i]['hldshrs']}</td>
                        </tr>
_TBODY;
                } elseif ($arrTbl['emp']['formcode'] == $arrData[$i]['mngrformcode'] && $arrData[$i]['emprolepk'] == '2023010012' && $arrData[$i]['frmformcode'] == '2023010006') { // 單位主管 + 申請者(員工身份) + 代理人已簽核
                    $count++;
                    
                    $tbody .= <<<_TBODY
                        <tr>
                            <td style="width:10%;">{$arrData[$i]['frmlistapl']}</td>
                            <td style="width:4%;">{$arrData[$i]['year']}</td>
                            <td style="width:10%;">{$arrData[$i]['cmpapl']}</td>
                            <td style="width:6%;">{$arrData[$i]['empapl']}</td>
                            <td style="width:6%;">{$arrData[$i]['pryapl']}</td>
                            <td style="width:6%;">{$arrData[$i]['hldclsapl']}</td>
                            <td style="width:15%;">{$arrData[$i]['hldrsn']}</td>
                            <td style="width:12%;text-align:center;">{$arrData[$i]['applydate']}</td>
                            <td style="width:12%;text-align:center;">{$arrData[$i]['begindate']}</td>
                            <td style="width:12%;text-align:center;">{$arrData[$i]['enddate']}</td>
                            <td style="width:8%;text-align:center;">{$arrData[$i]['hldsdays']}<br/>{$arrData[$i]['hldshrs']}</td>
                        </tr>
_TBODY;
                } elseif ($arrTbl['emp']['formcode'] == $arrData[$i]['cifformcode'] && (($arrData[$i]['emprolepk'] == '2023010012' && $arrData[$i]['frmformcode'] == '2023010009') || ($arrData[$i]['emprolepk'] == '2023010013' && $arrData[$i]['frmformcode'] == '2023010006'))) { // 主任 + (申請者(員工身份)+單位主管已簽核 OR 申請者(單位主管身份)+代理人已簽核)
                    $count++;
                    
                    $tbody .= <<<_TBODY
                        <tr>
                            <td style="width:10%;">{$arrData[$i]['frmlistapl']}</td>
                            <td style="width:4%;">{$arrData[$i]['year']}</td>
                            <td style="width:10%;">{$arrData[$i]['cmpapl']}</td>
                            <td style="width:6%;">{$arrData[$i]['empapl']}</td>
                            <td style="width:6%;">{$arrData[$i]['pryapl']}</td>
                            <td style="width:6%;">{$arrData[$i]['hldclsapl']}</td>
                            <td style="width:15%;">{$arrData[$i]['hldrsn']}</td>
                            <td style="width:12%;text-align:center;">{$arrData[$i]['applydate']}</td>
                            <td style="width:12%;text-align:center;">{$arrData[$i]['begindate']}</td>
                            <td style="width:12%;text-align:center;">{$arrData[$i]['enddate']}</td>
                            <td style="width:8%;text-align:center;">{$arrData[$i]['hldsdays']}<br/>{$arrData[$i]['hldshrs']}</td>
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

    // 審核者核准 / 退回
    // $arrFormVal: 傳入新表單欄位值陣列
    // $arrTbl: 傳入其他參考表格(二維關聯陣列)
    function Verify($arrFormVal, $arrTbl){
        //Begin
        if ($arrTbl['emp']['formcode'] == $arrTbl['holidays']['pryformcode'] && $arrTbl['holidays']['frmformcode'] == '2023010004') { //代理人 + 送出
            $this->SQL = <<<_sql
                UPDATE holidays
                SET
                    modifier = '$arrFormVal[modifier]'
                    , modifydate = current_timestamp()
                    , frmformcode = '{$arrTbl['frmvrf']['formcode']}'
                    , frmlistapl = '{$arrTbl['frmvrf']['listapl']}'
                    , pryvrfdate = current_timestamp()
                WHERE formcode = '$arrFormVal[formcode]'
_sql;
        } elseif ($arrTbl['emp']['formcode'] == $arrTbl['holidays']['mngrformcode'] && $arrTbl['holidays']['emprolepk'] == '2023010012' && $arrTbl['holidays']['frmformcode'] == '2023010006') { //單位主管 + 申請者(員工身份) + 代理人已簽核
            $this->SQL = <<<_sql
                UPDATE holidays
                SET
                    modifier = '$arrFormVal[modifier]'
                    , modifydate = current_timestamp()
                    , frmformcode = '{$arrTbl['frmvrf']['formcode']}'
                    , frmlistapl = '{$arrTbl['frmvrf']['listapl']}'
                    , mngrvrfdate = current_timestamp()
                WHERE formcode = '$arrFormVal[formcode]'
_sql;
        }  elseif ($arrTbl['emp']['formcode'] == $arrTbl['holidays']['cifformcode'] && (($arrTbl['holidays']['emprolepk'] == '2023010012' && $arrTbl['holidays']['frmformcode'] == '2023010009') || ($arrTbl['holidays']['emprolepk'] == '2023010013' && $arrTbl['holidays']['frmformcode'] == '2023010006'))) { //主任 + (申請者(員工身份)+單位主管已簽核 OR 申請者(單位主管身份)+代理人已簽核)
            $this->SQL = <<<_sql
                UPDATE holidays
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

    // 請假統計--------------------------------------------------------------------------------------------------------------

    // 員工請假年度統計查詢 View
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
                        <td class="text-center">$field[year]<br/>$field[empapl]<br/>$field[empcode]</td>
                        <td class="text-center">$field[day2022100089]/$field[hrs2022100089]/$field[ttlhrs2022100089]<br/>$field[day2022100090]/$field[hrs2022100090]/$field[ttlhrs2022100090]<br/>$field[day2022100092]/$field[hrs2022100092]/$field[ttlhrs2022100092]</td>
                        <td class="text-center">$field[day2022100091]/$field[hrs2022100091]/$field[ttlhrs2022100091]<br/>$field[day2022100093]/$field[hrs2022100093]/$field[ttlhrs2022100093]<br/>$field[day2022100094]/$field[hrs2022100094]/$field[ttlhrs2022100094]</td>
                        <td class="text-center">$field[day2022100095]/$field[hrs2022100095]/$field[ttlhrs2022100095]<br/>$field[day2022100096]/$field[hrs2022100096]/$field[ttlhrs2022100096]<br/>$field[day2022100097]/$field[hrs2022100097]/$field[ttlhrs2022100097]</td>
                        <td class="text-center">$field[day2022100098]/$field[hrs2022100098]/$field[ttlhrs2022100098]<br/>$field[day2022100099]/$field[hrs2022100099]/$field[ttlhrs2022100099]<br/>$field[day2022100100]/$field[hrs2022100100]/$field[ttlhrs2022100100]</td>
                        <td class="text-center"><br/>$field[day2022100101]/$field[hrs2022100101]/$field[ttlhrs2022100101]<br/>$field[day2023010024]/$field[hrs2023010024]/$field[ttlhrs2023010024]</td>
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
                </tr>
                </tbody>
_TBODY;
        }

        $this->calTotalPages($count); // 計算總頁數 + 目前頁數
        return $tbody;
        //End
    }


    // 列印員工請假年度統計報表(PDF)
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
        $thead =<<<_THEAD
            <thead>
                <tr>
                    <th style="width:10%;text-align:center;">年度<br/>員工<br/>員工編號</th><th style="width:18%;text-align:center;">病假(天數/時數/總時數)<br/>事假(天數/時數/總時數)<br/>特休假(天數/時數/總時數)</th><th style="width:18%;text-align:center;">公假(天數/時數/總時數)<br/>婚假(天數/時數/總時數)<br/>喪假(天數/時數/總時數)</th><th style="width:18%;text-align:center;">家庭照顧假(天數/時數/總時數)<br/>生理假(天數/時數/總時數)<br>陪產假(天數/時數/總時數)</th><th style="width:18%;text-align:center;">產檢假(天數/時數/總時數)<br/>產假(天數/時數/總時數)<br/>其他(天數/時數/總時數)</th><th style="width:18%;text-align:center;">換休(天數/時數/總時數)<br/>公傷病假(天數/時數/總時數)</th>
                </tr>
            </thead>
_THEAD;

        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";
            foreach ($arrData as $field) {
                $count++;
                
                $tbody .= <<<_TBODY
                    <tr>
                        <td style="width:10%;text-align:center;">$field[year]<br/>$field[empapl]<br/>$field[empcode]</td>
                        <td style="width:18%;text-align:center;">$field[day2022100089]/$field[hrs2022100089]/$field[ttlhrs2022100089]<br/>$field[day2022100090]/$field[hrs2022100090]/$field[ttlhrs2022100090]<br/>$field[day2022100092]/$field[hrs2022100092]/$field[ttlhrs2022100092]</td>
                        <td style="width:18%;text-align:center;">$field[day2022100091]/$field[hrs2022100091]/$field[ttlhrs2022100091]<br/>$field[day2022100093]/$field[hrs2022100093]/$field[ttlhrs2022100093]<br/>$field[day2022100094]/$field[hrs2022100094]/$field[ttlhrs2022100094]</td>
                        <td style="width:18%;text-align:center;">$field[day2022100095]/$field[hrs2022100095]/$field[ttlhrs2022100095]<br/>$field[day2022100096]/$field[hrs2022100096]/$field[ttlhrs2022100096]<br/>$field[day2022100097]/$field[hrs2022100097]/$field[ttlhrs2022100097]</td>
                        <td style="width:18%;text-align:center;">$field[day2022100098]/$field[hrs2022100098]/$field[ttlhrs2022100098]<br/>$field[day2022100099]/$field[hrs2022100099]/$field[ttlhrs2022100099]<br/>$field[day2022100100]/$field[hrs2022100100]/$field[ttlhrs2022100100]</td>
                        <td style="width:18%;text-align:center;"><br/>$field[day2022100101]/$field[hrs2022100101]/$field[ttlhrs2022100101]<br/>$field[day2023010024]/$field[hrs2023010024]/$field[ttlhrs2023010024]</td>
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


    // 取得請假統計並重組查詢結果(二維關聯陣列)
    // $SQL: SQL字串
    function getListByEmpformcodeAndYear($SQL) : array {
        $arrStt = array('00000000000000000000000'=>array()); // 重組後的查詢結果
        $arrData = array(); // 資料來源

        //Begin
        $arrData = $this->rtnQryResults($SQL);
        if (count($arrData) > 0) {
            foreach ($arrData as $value) {
                if (array_key_exists("$value[year]$value[deptspk]$value[empformcode]", $arrStt)) { // 加入同一位員工的其他假別統計
                    switch ($value['hldformcode']) {
                        case '2022100089': // 病假(2022100089)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100089'] = $value['sum_hldsdays']; // 病假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100089'] = $value['sum_hldshrs']; // 病假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100089'] = $value['sum_total_hrs']; // 病假總時數
                            break;
                        case '2022100090': // 事假(2022100090)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100090'] = $value['sum_hldsdays']; // 事假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100090'] = $value['sum_hldshrs']; // 事假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100090'] = $value['sum_total_hrs']; // 事假總時數
                            break;
                        case '2022100091': // 公假(2022100091)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100091'] = $value['sum_hldsdays']; // 公假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100091'] = $value['sum_hldshrs']; // 公假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100091'] = $value['sum_total_hrs']; // 公假總時數
                            break;
                        case '2022100092': // 特休假(2022100092)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100092'] = $value['sum_hldsdays']; // 特休假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100092'] = $value['sum_hldshrs']; // 特休假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100092'] = $value['sum_total_hrs']; // 特休假總時數
                            break;
                        case '2022100093': // 婚假(2022100093)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100093'] = $value['sum_hldsdays']; // 婚假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100093'] = $value['sum_hldshrs']; // 婚假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100093'] = $value['sum_total_hrs']; // 婚假總時數
                            break;
                        case '2022100094': // 喪假(2022100094)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100094'] = $value['sum_hldsdays']; // 喪假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100094'] = $value['sum_hldshrs']; // 喪假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100094'] = $value['sum_total_hrs']; // 喪假總時數
                            break;
                        case '2022100095': // 家庭照顧假(2022100095)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100095'] = $value['sum_hldsdays']; // 家庭照顧假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100095'] = $value['sum_hldshrs']; // 家庭照顧假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100095'] = $value['sum_total_hrs']; // 家庭照顧假總時數
                            break;
                        case '2022100096': // 生理假(2022100096)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100096'] = $value['sum_hldsdays']; // 生理假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100096'] = $value['sum_hldshrs']; // 生理假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100096'] = $value['sum_total_hrs']; // 生理假總時數
                            break;
                        case '2022100097': // 陪產假(2022100097)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100097'] = $value['sum_hldsdays']; // 陪產假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100097'] = $value['sum_hldshrs']; // 陪產假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100097'] = $value['sum_total_hrs']; // 陪產假總時數
                            break;
                        case '2022100098': // 產檢假(2022100098)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100098'] = $value['sum_hldsdays']; // 產檢假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100098'] = $value['sum_hldshrs']; // 產檢假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100098'] = $value['sum_total_hrs']; // 產檢假總時數
                            break;
                        case '2022100099': // 產假(2022100099)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100099'] = $value['sum_hldsdays']; // 產假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100099'] = $value['sum_hldshrs']; // 產假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100099'] = $value['sum_total_hrs']; // 產假假總時數
                            break;
                        case '2022100101': // 換休(2022100101)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100101'] = $value['sum_hldsdays']; // 換休天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100101'] = $value['sum_hldshrs']; // 換休時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100101'] = $value['sum_total_hrs']; // 換休假總時數
                            break;
                        case '2023010024': // 公傷病假(2023010024)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2023010024'] = $value['sum_hldsdays']; // 公傷病假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2023010024'] = $value['sum_hldshrs']; // 公傷病假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2023010024'] = $value['sum_total_hrs']; // 公傷病假假總時數
                            break;
                        case '2022100100': // 其他(2022100100)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100100'] = $value['sum_hldsdays']; // 其他天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100100'] = $value['sum_hldshrs']; // 其他假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100100'] = $value['sum_total_hrs']; // 其他假總時數
                            break;
                        default:
                            # code...
                            break;
                    }
                } else { // 加入新記錄(新員工) + 暫存目前記錄
                    $arrStt["$value[year]$value[deptspk]$value[empformcode]"] = array(
                        'year'=>$value['year'], 'deptspk'=>$value['deptspk'], 'cmpcode'=>$value['cmpcode'], 'cmpapl'=>$value['cmpapl'], 'empformcode'=>$value['empformcode'], 'empcode'=>$value['empcode'], 'empapl'=>$value['empapl']
                        , 'code2022100089'=>'2022100089', 'apl2022100089'=>'病假', 'day2022100089'=>'', 'hrs2022100089'=>'', 'ttlhrs2022100089'=>''
                        , 'code2022100090'=>'2022100090', 'apl2022100090'=>'事假', 'day2022100090'=>'', 'hrs2022100090'=>'', 'ttlhrs2022100090'=>''
                        , 'code2022100091'=>'2022100091', 'apl2022100091'=>'公假', 'day2022100091'=>'', 'hrs2022100091'=>'', 'ttlhrs2022100091'=>''
                        , 'code2022100092'=>'2022100092', 'apl2022100092'=>'特休假', 'day2022100092'=>'', 'hrs2022100092'=>'', 'ttlhrs2022100092'=>''
                        , 'code2022100093'=>'2022100093', 'apl2022100093'=>'婚假', 'day2022100093'=>'', 'hrs2022100093'=>'', 'ttlhrs2022100093'=>''
                        , 'code2022100094'=>'2022100094', 'apl2022100094'=>'喪假', 'day2022100094'=>'', 'hrs2022100094'=>'', 'ttlhrs2022100094'=>''
                        , 'code2022100095'=>'2022100095', 'apl2022100095'=>'家庭照顧假', 'day2022100095'=>'', 'hrs2022100095'=>'', 'ttlhrs2022100095'=>''
                        , 'code2022100096'=>'2022100096', 'apl2022100096'=>'生理假', 'day2022100096'=>'', 'hrs2022100096'=>'', 'ttlhrs2022100096'=>''
                        , 'code2022100097'=>'2022100097', 'apl2022100097'=>'陪產假', 'day2022100097'=>'', 'hrs2022100097'=>'', 'ttlhrs2022100097'=>''
                        , 'code2022100098'=>'2022100098', 'apl2022100098'=>'產檢假', 'day2022100098'=>'', 'hrs2022100098'=>'', 'ttlhrs2022100098'=>''
                        , 'code2022100099'=>'2022100099', 'apl2022100099'=>'產假', 'day2022100099'=>'', 'hrs2022100099'=>'', 'ttlhrs2022100099'=>''
                        , 'code2022100101'=>'2022100101', 'apl2022100101'=>'換休', 'day2022100101'=>'', 'hrs2022100101'=>'', 'ttlhrs2022100101'=>''
                        , 'code2023010024'=>'2023010024', 'apl2023010024'=>'公傷病假', 'day2023010024'=>'', 'hrs2023010024'=>'', 'ttlhrs2023010024'=>''
                        , 'code2022100100'=>'2022100100', 'apl2022100100'=>'其他', 'day2022100100'=>'', 'hrs2022100100'=>'', 'ttlhrs2022100100'=>''
                    );

                    switch ($value['hldformcode']) {
                        case '2022100089': // 病假(2022100089)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100089'] = $value['sum_hldsdays']; // 病假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100089'] = $value['sum_hldshrs']; // 病假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100089'] = $value['sum_total_hrs']; // 病假總時數
                            break;
                        case '2022100090': // 事假(2022100090)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100090'] = $value['sum_hldsdays']; // 事假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100090'] = $value['sum_hldshrs']; // 事假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100090'] = $value['sum_total_hrs']; // 事假總時數
                            break;
                        case '2022100091': // 公假(2022100091)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100091'] = $value['sum_hldsdays']; // 公假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100091'] = $value['sum_hldshrs']; // 公假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100091'] = $value['sum_total_hrs']; // 公假總時數
                            break;
                        case '2022100092': // 特休假(2022100092)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100092'] = $value['sum_hldsdays']; // 特休假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100092'] = $value['sum_hldshrs']; // 特休假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100092'] = $value['sum_total_hrs']; // 特休假總時數
                            break;
                        case '2022100093': // 婚假(2022100093)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100093'] = $value['sum_hldsdays']; // 婚假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100093'] = $value['sum_hldshrs']; // 婚假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100093'] = $value['sum_total_hrs']; // 婚假總時數
                            break;
                        case '2022100094': // 喪假(2022100094)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100094'] = $value['sum_hldsdays']; // 喪假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100094'] = $value['sum_hldshrs']; // 喪假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100094'] = $value['sum_total_hrs']; // 喪假總時數
                            break;
                        case '2022100095': // 家庭照顧假(2022100095)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100095'] = $value['sum_hldsdays']; // 家庭照顧假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100095'] = $value['sum_hldshrs']; // 家庭照顧假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100095'] = $value['sum_total_hrs']; // 家庭照顧假總時數
                            break;
                        case '2022100096': // 生理假(2022100096)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100096'] = $value['sum_hldsdays']; // 生理假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100096'] = $value['sum_hldshrs']; // 生理假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100096'] = $value['sum_total_hrs']; // 生理假總時數
                            break;
                        case '2022100097': // 陪產假(2022100097)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100097'] = $value['sum_hldsdays']; // 陪產假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100097'] = $value['sum_hldshrs']; // 陪產假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100097'] = $value['sum_total_hrs']; // 陪產假總時數
                            break;
                        case '2022100098': // 產檢假(2022100098)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100098'] = $value['sum_hldsdays']; // 產檢假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100098'] = $value['sum_hldshrs']; // 產檢假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100098'] = $value['sum_total_hrs']; // 產檢假總時數
                            break;
                        case '2022100099': // 產假(2022100099)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100099'] = $value['sum_hldsdays']; // 產假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100099'] = $value['sum_hldshrs']; // 產假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100099'] = $value['sum_total_hrs']; // 產假假總時數
                            break;
                        case '2022100101': // 換休(2022100101)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100101'] = $value['sum_hldsdays']; // 換休天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100101'] = $value['sum_hldshrs']; // 換休時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100101'] = $value['sum_total_hrs']; // 換休假總時數
                            break;
                        case '2023010024': // 公傷病假(2023010024)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2023010024'] = $value['sum_hldsdays']; // 公傷病假天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2023010024'] = $value['sum_hldshrs']; // 公傷病假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2023010024'] = $value['sum_total_hrs']; // 公傷病假假總時數
                            break;
                        case '2022100100': // 其他(2022100100)
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['day2022100100'] = $value['sum_hldsdays']; // 其他天數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['hrs2022100100'] = $value['sum_hldshrs']; // 其他假時數
                            $arrStt["$value[year]$value[deptspk]$value[empformcode]"]['ttlhrs2022100100'] = $value['sum_total_hrs']; // 其他假總時數
                            break;
                        default:
                            # code...
                            break;
                    }
                }

            }
        }
        unset($arrStt['00000000000000000000000']);

        return $arrStt;
        //End
    }

    // 請假歷史查詢--------------------------------------------------------------------------------------------------------------

    // 員工請假歷史查詢結果 View
    // $arrData: Array物件
    // $arrTbl: 其他參考檔(二維關聯陣列)
    // return: 查詢結果HTML Tag
    function viewHstyQry($arrData, $arrTbl){
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
                if ($field['frmformcode'] == '2023010003' || $field['frmformcode'] == '2023010001' || $field['frmformcode'] == '2023010002' || $field['frmformcode'] == '2023010022' || $field['frmformcode'] == '2023010023') { //暫存 + 代理人已退回 + 單位主管已退回 + 人事主管退回 + 主任退回 
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnEditId' name='edit' value='編輯' attrformcode='$field[formcode]'>";
                    $html_buttons .= "<input type='submit' class='btn btn-outline-primary' id='$btnDiscardId' name='discard' value='註銷' attrformcode='$field[formcode]'>";
                }
                
                $tbody .= <<<_TBODY
                        <tr>
                            <td class="">$field[cmpapl]</td>
                            <td class="text-center">$field[empapl]<br/>$field[pryapl]</td>
                            <td class="text-center">$field[amlhrs]<br/>$field[curhrs]</td>
                            <td class="text-center">$field[frmlistapl]</td>
                            <td class="text-center">$field[year]<br/>$field[hldclsapl]</td>
                            <td class="">$field[hldrsn]</td>
                            <td class="text-center">$field[begindate]<br/>$field[enddate]</td>
                            <td class="text-center">$field[hldsdays]<br/>$field[hldshrs]</td>
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

    // 列印員工請假歷史清單(PDF)
    // $arrData: Array物件
    // $arrTbl: 其他參考檔(二維關聯陣列)
    // return: 查詢結果HTML Tag
    function PrtPDFByHstyQry($arrData, $arrTbl){
        //變數初始化
        $count = 0; //該頁筆數
        $thead = '';
        $tbody = '';
        $table = '';
        
        //Begin
        $thead =<<<_THEAD
            <thead>
                <tr>
                    <th style="width:10%;">機構</th><th style="width:8%;text-align:center;">員工</th><th style="width:10%;">過去未休假的累積天數<br/>目前可特休天數</th><th style="width:10%;">表單審核狀態</th><th style="width:6%;">年度</th><th style="width:10%;">假別</th><th style="width:15%;">請假事由</th><th style="width:12%;text-align:center;">請假起始日<br/>請假截止日</th><th style="width:8%;text-align:center;">請假天數<br/>請假時數</th><th style="width:12%;text-align:center;">假單送出時間</th>
                </tr>
            </thead>
_THEAD;

        if (isset($arrData) && count($arrData) > 0) {
            $tbody = "<tbody>";
            foreach ($arrData as $field) {
                $count++;
                
                $tbody .= <<<_TBODY
                        <tr>
                            <td style="width:10%;">$field[cmpapl]</td>
                            <td style="width:8%;text-align:center;">$field[empapl]</td>
                            <td style="width:10%;text-align:center;">$field[amlhrs]<br/>$field[curhrs]</td>
                            <td style="width:10%;">$field[frmlistapl]</td>
                            <td style="width:6%;">$field[year]</td>
                            <td style="width:10%;">$field[hldclsapl]</td>
                            <td style="width:15%;">$field[hldrsn]</td>
                            <td style="width:12%;text-align:center;">$field[begindate]<br/>$field[enddate]</td>
                            <td style="width:8%;text-align:center;">$field[hldsdays]<br/>$field[hldshrs]</td>
                            <td style="width:12%;text-align:center;">$field[applydate]</td>
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

        $table =<<<_TABLE
            <table class="table table-light">
                $thead
                $tbody
            </table>
_TABLE;
        // $this->calTotalPages($count); // 計算總頁數 + 目前頁數

        return $table;
        //End
    }


    //------------------------------------------------------------------------------------------------------

    //  取得週末天數
    //  $begin_date: 傳入開始日期
    //  $end_date: 傳入結束日期
    function getWeekendDaysByPeriod($begin_date, $end_date) {
        //Begin
        $this->SQL = "SELECT COUNT(*) FROM hldmkupwrk WHERE 1 AND formstate = 15 AND weekend = '是' AND hlddate BETWEEN '$begin_date' AND '$end_date';";
        return $this->rtnQryField($this->SQL);
        //End
    }

    // 取得補上班日天數
    // $splrcode: 供應商代碼
    // $mtrlcode: 品項代碼
    public function getMkupwrkByPeriod($begin_date, $end_date) {
        // Begin
        $this->SQL = "SELECT COUNT(*) FROM hldmkupwrk WHERE 1 AND formstate = 15 AND mkupwrk = '是' AND hlddate BETWEEN '$begin_date' AND '$end_date';";
        return $this->rtnQryField($this->SQL);
        // End
    }

    // 請假申請統計
    // $state: 統計狀態, 合計 / 已簽核(主任已簽核) / 簽核中(主任未簽核)
    // $empFormcode: 員工檔唯一識別碼
    // $year: 西元年
    function sttByHldApply($state, $empFormcode, $year) : int {
        //Begin
        switch ($state) {
            case '合計':
                $this->SQL = "SELECT COUNT(*) FROM holidays WHERE 1 AND formstate = 15 AND empformcode = '$empFormcode' AND year = '$year'";
                break;
            case '簽核中':
                $this->SQL = "SELECT COUNT(*) FROM holidays WHERE 1 AND formstate = 15 AND frmformcode <> '2023010017' AND empformcode = '$empFormcode' AND year = '$year'";
                break;
            case '已簽核':
                $this->SQL = "SELECT COUNT(*) FROM holidays WHERE 1 AND formstate = 15 AND frmformcode = '2023010017' AND empformcode = '$empFormcode' AND year = '$year'";
                break;
            default:
                // do nothing
                break;
        }

        return $this->rtnQryField($this->SQL);
        //End
    }

    // 請假審核統計(未簽核)
    // $year: 西元年
    // $arrTbl: 參考其他相關資料表($arrTbl['emp']: 一維關聯陣列, $arrTbl['holidays']: 二維關聯陣列)
    function sttByHldChkUnSign($year, $arrTbl) : int {
        // 變數初始化
        $int_count = 0; // 統計筆數

        //Begin
        foreach ($arrTbl['holidays'] as $holidays) {
            if ($arrTbl['emp']['formcode'] == $holidays['pryformcode'] && $holidays['frmformcode'] == '2023010004') { // 年度 + 代理人 + 請假申請者送出(申請者=員工)
                $int_count++;
            }
            if ($arrTbl['emp']['formcode'] == $holidays['mngrformcode'] && $holidays['emprolepk'] == '2023010012' && $holidays['frmformcode'] == '2023010006') { // 年度 + 單位主管 + 申請者(員工身份) + 代理人已簽核(申請者=員工)
                $int_count++;
            }
            if ($arrTbl['emp']['formcode'] == $holidays['hrformcode'] && (($holidays['emprolepk'] == '2023010012' && $holidays['frmformcode'] == '2023010009') || ($holidays['emprolepk'] == '2023010013' && $holidays['frmformcode'] == '2023010006'))) { // 年度 + 人事主管 + (申請者(員工身份)+單位主管已簽核 OR 申請者(單位主管身份)+代理人已簽核)
                $int_count++;
            }
            if ($arrTbl['emp']['formcode'] == $holidays['cifformcode'] && $holidays['frmformcode'] == '2023010011') { // 年度 + 主任 + 人事主管已簽核
                $int_count++;
            }
        }
        
        return $int_count;
        //End
    }

    // 假別統計
    // $year: 西元年
    // $arrTbl: 參考其他相關資料表($arrTbl['emp']: 一維關聯陣列)
    function sttHldsByEmpAndYear($year, $arrTbl) : array {
        $arrStatistic = array();
        
        // Begin
        $this->SQL = "SELECT hldformcode, hldclsapl, COUNT(*) as cnt_hlds, FORMAT(SUM(hldsdays), 2) as sum_hldsdays, FORMAT(SUM(hldshrs), 2) as sum_hldshrs FROM $this->self_table WHERE 1 AND formstate = 15 AND empformcode = '{$arrTbl['emp']['formcode']}' AND year = '$year' AND frmformcode = '2023010017' GROUP BY hldformcode";
        $arrStatistic = $this->rtnQryResults($this->SQL);

        if (is_array($arrStatistic) && count($arrStatistic) > 0) {
            return $arrStatistic;
        } else {
            return array();
        }        
        // End
    }









}

?>