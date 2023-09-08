<?php

    //Require_once
    require_once "../../models//common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_holidays.php";
    require_once "../../models/cls_field_lists.php";
    require_once "../../models/cls_employees.php";
    require_once "../../models/cls_depts.php";
    
    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPmsByEmpformcode($_SESSION['login_emp']['formcode'], '請假管理', '請假統計查詢')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有請假管理的請假統計查詢權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(MOBILEINDEXPAGE); //返回首頁
        exit();
    }

    $obj_holiday = new cls_holidays; //請假檔
    $obj_field_lists = new cls_field_lists; //欄位清單檔
    $obj_emp = new cls_employees; //員工檔
    $obj_depts = new cls_depts; //機構檔
    
    $SQL = "";
    $arrData = array();
    $htmlTags = array();
    $htmlQryResult = ""; //顯示查詢結果HTML Tag
    $htmlPaging = ""; //顯示查詢分頁HTML Tag
    $tbl = array(); //儲存不同的參考檔
    $arrQryFld = ['year'=>"", 'begindate'=>'', 'enddate'=>"", 'empapl'=>"", 'empcode'=>'', 'recdsperpage'=>'']; //儲存淨化查詢條件
    $strStsMsg = ""; //儲存狀態欄訊息
    
    //Begin
    $tbl['emp'] = $obj_emp->getRecdByFormcode($_SESSION['login_emp']['formcode']); //員工檔
    $nowYear = date("Y", time()); // 目前年度

    if(isset($_POST['query']) || isset($_POST['prt'])){ //按下"查詢"按鈕的處理動作 OR 按下"列印"按鈕的處理動作
        $arrQryFld = $obj_form->inputChk($_POST); //淨化查詢條件
        
        $obj_holiday->SQLSelect = "SELECT h.year, e.deptspk, e.cmpcode, e.cmpapl, h.empformcode, h.empcode, h.empapl, h.hldformcode, h.hldclsapl, COUNT(*) AS cnt_hlds, FORMAT(SUM(h.hldsdays), 2) AS sum_hldsdays, FORMAT(SUM(h.hldshrs), 2) AS sum_hldshrs, FORMAT(SUM(h.hldsdays), 2) * 8 + FORMAT(SUM(h.hldshrs), 2) AS sum_total_hrs ";
        $obj_holiday->SQLFrom = " FROM holidays h LEFT OUTER JOIN employees e ON (h.empformcode = e.formcode) ";
        $obj_holiday->SQLWhere .= " AND h.formstate = 15  AND h.frmformcode = '2023010017' "; // 主任已簽核
        
        $obj_holiday->SQLWhere .= isset($arrQryFld['year']) && mb_strlen($arrQryFld['year']) > 0 ? " AND h.year = '{$arrQryFld['year']}' " : ""; // 年度(西元年)
        $htmlTags['html_year'] = $obj_form->viewHTMLSTSglVal(array('attrId'=>'year', 'attrName'=>'year', 'attrTitle'=>'請選擇年度'), array(date("Y", time())-4, date("Y", time())-3, date("Y", time())-2, date("Y", time())-1, date("Y", time()), date("Y", time())+1), $arrQryFld['year']); // 年度(西元年)
        $obj_holiday->SQLWhere .= isset($arrQryFld['deptspk']) && mb_strlen($arrQryFld['deptspk']) > 0 ? " AND e.deptspk LIKE '%{$arrQryFld['deptspk']}%' " : ""; // 機構
        $htmlTags['deptspk'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'deptspk', 'attrName'=>'deptspk', 'attrTitle'=>'請選擇機構', 'optionTitle'=>'cmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_depts->getList(), $arrQryFld['deptspk'], true); // 機構
        $obj_holiday->SQLWhere .= isset($arrQryFld['empapl']) && mb_strlen($arrQryFld['empapl']) > 0 ? " AND h.empapl LIKE '%{$arrQryFld['empapl']}%' " : ""; // 員工姓名
        $obj_holiday->SQLGroupBy .= " h.empformcode, h.hldformcode ";
        $obj_holiday->SQLOrderBy .= " h.year, e.cmpcode, h.formcode, h.hldformcode ";
        // $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數

        // $obj_holiday->SQLlimit = " LIMIT $obj_holiday->intStartPos,  $obj_holiday->int_records_per_page";
        $obj_holiday->SQL = $obj_holiday->SQLSelect.$obj_holiday->SQLFrom.$obj_holiday->SQLWhere.$obj_holiday->SQLGroupBy.$obj_holiday->SQLOrderBy;
        $arrData = $obj_holiday->getListByEmpformcodeAndYear($obj_holiday->SQL); // 取得請假統計並重組查詢結果


        //傳回查詢結果
        // $obj_holiday->int_records_per_page = $arrQryFld['recdsperpage']; //設定每頁筆數
        $htmlQryResult = $obj_holiday->viewSttQry($arrData, $tbl);

        
        //統計分頁訊息
        $htmlPaging = $obj_form->viewPaging($obj_holiday->int_total_records, $obj_holiday->int_total_pages, $obj_holiday->int_current_page); //顯示查詢分頁HTML Tag

        //Save Session
        $_SESSION['arrQryFld'] = $arrQryFld;
        $_SESSION['SQL']['Select'] = $obj_holiday->SQLSelect;
        $_SESSION['SQL']['From'] = $obj_holiday->SQLFrom;
        $_SESSION['SQL']['Where'] = $obj_holiday->SQLWhere;
        $_SESSION['SQL']['GroupBy'] = $obj_holiday->SQLGroupBy;
        $_SESSION['SQL']['OrderBy'] = $obj_holiday->SQLOrderBy;
        $_SESSION['SQL']['CurPage'] = $obj_holiday->int_current_page; //儲存目前頁數
        $_SESSION['SQL']['arrData'] = $arrData;

        if (isset($_POST['prt'])) { //按下"列印"按鈕的處理動作
            $obj_form->js_openWindow('hldsYearStatisticRpt.php');
        }

    }elseif (isset($_POST['edit'])) { //按下"編輯"按鈕的處理動作
        $_SESSION['selFormCode'] = $_POST['selFormCode']; //儲存目前編輯記錄
        header("location: holidaysEdit.php");
        exit();
    }elseif (isset($_POST['paging']) || isset($_POST['discard']) || (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel"))) { //執行"分頁/編輯完成/取消編輯"功能後的處理動作
        if (isset($_POST['discard'])) { //按下"註銷"按鈕的處理動作
            // if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '請假管理', '註銷')) { //檢查使用者是否有使用權限
            //     $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有請假資料的註銷權限，如需該功能的使用權限，請與管理者聯絡");
            //     $obj_form->js_goURL(INDEXPAGE); //返回首頁
            //     exit();
            // }
            $obj_holiday->discard($_POST['selFormCode']); //註銷記錄
        }
        
        //取得分頁條件
        $arrQryFld = $_SESSION['arrQryFld'];
        $obj_holiday->SQLSelect = $_SESSION['SQL']['Select'];
        $obj_holiday->SQLFrom = $_SESSION['SQL']['From'];
        $obj_holiday->SQLWhere = $_SESSION['SQL']['Where'];
        $obj_holiday->SQLGroupBy = $_SESSION['SQL']['GroupBy'];
        $obj_holiday->SQLOrderBy = $_SESSION['SQL']['OrderBy'];
        $htmlTags['html_year'] = $obj_form->viewHTMLSTSglVal(array('attrId'=>'year', 'attrName'=>'year', 'attrTitle'=>'請選擇年度'), array(date("Y", time())-4, date("Y", time())-3, date("Y", time())-2, date("Y", time())-1, date("Y", time()), date("Y", time())+1), $arrQryFld['year'], true); // 年度(西元年)
        $htmlTags['deptspk'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'deptspk', 'attrName'=>'deptspk', 'attrTitle'=>'請選擇機構', 'optionTitle'=>'cmpapl', 'optionValue'=>'formcode'), $obj_depts->getList(), $arrQryFld['deptspk'], true); // 機構
        // $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數
        $obj_holiday->SQL = $obj_holiday->SQLSelect.$obj_holiday->SQLFrom.$obj_holiday->SQLWhere.$obj_holiday->SQLGroupBy.$obj_holiday->SQLOrderBy;
        $arrData = $obj_holiday->getListByEmpformcodeAndYear($obj_holiday->SQL); // 取得請假統計並重組查詢結果

        //傳回查詢結果
        // $obj_holiday->int_records_per_page = $arrQryFld['recdsperpage']; //設定每頁筆數
        $htmlQryResult = $obj_holiday->viewSttQry($arrData, $tbl);

        //計算目前頁數
        if (isset($_POST['paging'])) { //分頁
            $obj_holiday->int_current_page = $_POST['CurPage'];
            switch ($_POST['paging']) {
                case '|< 第一頁':
                    $obj_holiday->int_current_page = 1;
                    break;
                case '<< 上一頁':
                    $obj_holiday->int_current_page--;
                    break;
                case '下一頁 >>':
                    $obj_holiday->int_current_page++;
                    break;
                case '最後頁 >|':
                    $obj_holiday->int_current_page = $obj_holiday->int_total_pages;
                    break;
                default:
                    # code...
                    break;
            }
            $_SESSION['SQL']['CurPage'] = $obj_holiday->int_current_page; //儲存目前頁數
        } elseif (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel")) { //編輯 or 取消編輯
            $obj_holiday->int_current_page = $_SESSION['SQL']['CurPage']; //取得目前頁數
        }
        
        //統計分頁訊息
        $htmlPaging = $obj_form->viewPaging($obj_holiday->int_total_records, $obj_holiday->int_total_pages, $obj_holiday->int_current_page); //顯示查詢分頁HTML Tag
    } else { //第一次執行時的處理動作
        $htmlTags['html_year'] = $obj_form->viewHTMLSTSglVal(array('attrId'=>'year', 'attrName'=>'year', 'attrTitle'=>'請選擇年度'), array(date("Y", time())-4, date("Y", time())-3, date("Y", time())-2, date("Y", time())-1, date("Y", time()), date("Y", time())+1), $nowYear); // 年度(西元年)
        $htmlTags['deptspk'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'deptspk', 'attrName'=>'deptspk', 'attrTitle'=>'請選擇機構', 'optionTitle'=>'cmpapl', 'optionValue'=>'formcode'), $obj_depts->getList(), null, true); //機構
        //$htmlTags['html_sort'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'sort', 'attrName'=>'sort', 'attrTitle'=>'請輸入排序方式', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'品項代碼欄位-由小到大排序', 'srtValue'=>'mtrlcode ASC'), array('srtTitle'=>'品項代碼欄位-由大到小排序', 'srtValue'=>'mtrlcode DESC')), "品項代碼欄位-由小到大排序"); //排序方式
        // $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, 100); //每頁顯示筆數
        //$htmlTags['html_recdsperpage'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'50', 'srtValue'=>'50'), array('srtTitle'=>'100', 'srtValue'=>'100'), array('srtTitle'=>'250', 'srtValue'=>'250'), array('srtTitle'=>'500', 'srtValue'=>'500')), 500); //每頁顯示筆數
    };

    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    } elseif (isset($_POST['query']) || isset($_POST['paging']) || isset($_POST['discard'])) { //顯示完成訊息
        $strStsMsg = "資料查詢完成, 總筆數：$obj_holiday->int_total_records";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_depts = null;
    $obj_emp = null;
    $obj_field_lists = null;
    $obj_holiday = null;
    $obj_form = null;
    //End

echo <<<_html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>統計員工請假資料</title>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">

    <!-- JQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        var msg = ""; //訊息欄
        var btn = ""; //使用者按下按鈕
        
        $(document).ready(function(){
            //設定表單 Submit事件
            $("#form1").submit(function(){
                //Code ...
            });

            //設定form表單的submit type欄位 Click事件
            $(":submit").click(function(){
                if($(this).val() == "編輯"){ //按下"編輯"按鈕
                    msg = "開始編輯";
                    btn = "編輯";
                    $('#selFormCode').val($(this).attr('attrformcode')); //設定被選取記錄的formcode欄位
                }else if($(this).val() == "註銷"){ //按下"註銷"按鈕
                    msg = "該筆資料己經註銷";
                    btn = "註銷";
                    $('#selFormCode').val($(this).attr('attrformcode')); //設定被選取記錄的formcode欄位
                    return confirm("你是否確定註銷該筆記錄？"); //確認使用者是否註銷該筆記錄？
                }else if($(this).val() == "查詢"){
                    // msg = "查詢成功";
                    // btn = "查詢";
                }
            });

            //設定form表單的button type欄位 Click事件
            $(":button").click(function() {
                if ($(this).val() == "登出") { //按下"登出"按鈕
                    msg = "你已經登出系統";
                    btn = "登出";
                    location.assign("../../Public/mlogin.php");
                    alert(msg);
                }
            });

        });
    </script>
</head>
<body>
    <div class="container-fluid">

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>

    <form action="" method="post" id="form1" name="form1">
    <!--  header區塊  -->
    <header>
        <div class="row text-white" style="background-color: #3E7050;">
            <h1 class="col-sm-4"><img src="../../Images/Banners/logo.png" width="100" height="120" alt="立穎健康照護" style="vertical-align: middle;">立穎健康照護</h1>
        </div>
        <div class="row justify-content-end text-white" style="background-color: #3E7050;">
            <div class="col-sm-auto"><input type="button" class="btn btn-outline-light" value="登出"></div>
            <h6 class="col-sm-auto">使用者：{$_SESSION['login_emp']['empapl']}</h6>
            <h6 class="col-sm-auto">帳號：{$_SESSION['login_emp']['empcode']}</h6>
            <h6 class="col-sm-auto">登入日期：$_SESSION[login_time]</h6>
        </div>
    </header>
_html;

    include_once "../../Require/mnavigation.php"; //Nav區塊 下拉選單(路徑大小寫有區分)

    // include_once "holidaysNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
    <!-- main區塊 -->
    <main>
        <div class="row"><h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5></div>
            <h4 class="text-secondary text-decoration-underline my-3"><b>統計員工請假資料</b></h4>
            <div class="row justify-content-center mt-3">
                <input type="hidden" id="selFormCode" name="selFormCode" value="">
                <div class="row">
                    <div class="col-sm-2">
                        <label for="year" class="form-label">年度(西元年)：</label>$htmlTags[html_year]
                    </div>
                    <div class="col-sm-2">
                        <label for="empcode" class="form-label">機構：</label>$htmlTags[deptspk]
                    </div>
                    <div class="col-sm-2">
                        <label for="empapl" class="form-label">員工姓名：</label>
                        <input type="text" class="form-control" id="empapl" name="empapl" value="{$arrQryFld['empapl']}" placeholder="請輸入員工姓名" title="請輸入員工姓名">
                    </div>
                </div>
                
                <div class="row justify-content-center mt-2">
                    <input type="submit" class="col-sm-1 btn btn-primary" id="query" name="query" value="查詢">&nbsp;&nbsp;<input type="reset" value="清除" class="col-sm-1 btn btn-outline-primary">&nbsp;&nbsp;<input type="submit" class="col-sm-1 btn btn-outline-primary" id="query" name="prt" value="列印">
                </div>
                
                <table class="table caption-top table-striped table-hover my-5">
                    <caption><h4><b>員工請假統計清單(主任已簽核)</b></h4></caption>
                    <thead class="">
                        <tr>
                            <th class="text-center">年度<br/>員工<br/>員工編號</th><th class="text-center">病假(天數/時數/總時數)<br/>事假(天數/時數/總時數)<br/>特休假(天數/時數/總時數)</th><th class="text-center">公假(天數/時數/總時數)<br/>婚假(天數/時數/總時數)<br/>喪假(天數/時數/總時數)</th><th class="">家庭照顧假(天數/時數/總時數)<br/>生理假(天數/時數/總時數)<br/>陪產假(天數/時數/總時數)</th><th class="">產檢假(天數/時數/總時數)<br/>產假(天數/時數/總時數)<br/>其他(天數/時數/總時數)</th><th class="">換休(天數/時數/總時數)<br/>公傷病假(天數/時數/總時數)</th>
                        </tr>
                    </thead>
                    
                    $htmlQryResult
                </table>
                
                $htmlPaging
                <div class="g-5"></div>
            </div>
            
        
    </main>

    </form>
    </div>
</body>
</html>
_html;

?>