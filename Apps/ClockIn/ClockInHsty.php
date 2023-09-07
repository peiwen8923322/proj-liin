<?php

    //Require_once
    require_once "../../models//common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_clockin.php";
    require_once "../../models/cls_employees.php";
    require_once "../../models/cls_depts.php";
    
    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPmsByEmpformcode($_SESSION['login_emp']['formcode'], '刷卡管理', '歷史查詢')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有刷卡管理的歷史查詢權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(MOBILEINDEXPAGE); //返回首頁
        exit();
    }

    $obj_clockin = new cls_clockin; //刷卡檔
    $obj_emp = new cls_employees; //員工檔
    $obj_depts = new cls_depts; //機構檔
    
    $SQL = "";
    $htmlTags = array();
    $htmlQryResult = ""; //顯示查詢結果HTML Tag
    $htmlPaging = ""; //顯示查詢分頁HTML Tag
    $tbl = array(); //儲存不同的參考檔
    $arrQryFld = ['year'=>"", 'empapl'=>'', 'begindate'=>"", 'enddate'=>"", 'recdsperpage'=>'']; //儲存淨化查詢條件
    $strStsMsg = ""; //儲存狀態欄訊息
    
    //Begin
    $tbl['emp'] = $obj_emp->getRecdByFormcode($_SESSION['login_emp']['formcode']); //登入者的員工記錄
    
    if(isset($_POST['query']) || isset($_POST['prt'])){ //按下"查詢"按鈕的處理動作 OR 按下"列印"按鈕的處理動作
        $arrQryFld = $obj_form->inputChk($_POST); //淨化查詢條件
        
        // $obj_clockin->SQLSelect = "SELECT h.*, e.formcode AS e_formcode, e.cmpapl "; // 加上"e.formcode AS e_formcode, e.cmpapl"
        $obj_clockin->SQLWhere .= " AND formstate = 15 ";
        
        $obj_clockin->SQLWhere .= isset($arrQryFld['deptspk']) && mb_strlen($arrQryFld['deptspk']) > 0 ? " AND deptspk = '$arrQryFld[deptspk]' " : ""; // 機構
        $htmlTags['deptspk'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'deptspk', 'attrName'=>'deptspk', 'attrTitle'=>'請選擇機構', 'optionTitle'=>'cmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_depts->getList(), $arrQryFld['deptspk'], true); //機構
        $obj_clockin->SQLWhere .= isset($arrQryFld['year']) && mb_strlen($arrQryFld['year']) > 0 ? " AND year = $arrQryFld[year] " : ""; // 年度(西元年)
        $htmlTags['year'] = $obj_form->viewHTMLSTSglVal(array('attrId'=>'year', 'attrName'=>'year', 'attrTitle'=>'請選擇年度'), array(date("Y", time())-4, date("Y", time())-3, date("Y", time())-2, date("Y", time())-1, date("Y", time())), $arrQryFld['year'], null); // 年度(西元年)
        $obj_clockin->SQLWhere .= isset($arrQryFld['empapl']) && mb_strlen($arrQryFld['empapl']) > 0 ? " AND empapl LIKE '%$arrQryFld[empapl]%' " : ""; // 員工名稱
        if ((isset($arrQryFld['begindate']) && mb_strlen($arrQryFld['begindate']) > 0) && (isset($arrQryFld['enddate']) && mb_strlen($arrQryFld['enddate']) > 0)) { // 刷卡時間啟始日 + 刷卡時間截止日
            $obj_clockin->SQLWhere .= " AND clkintime >= '$arrQryFld[begindate] 00:00:00' AND clkintime <= '$arrQryFld[enddate] 23:59:59' ";
        } elseif (isset($arrQryFld['begindate']) && mb_strlen($arrQryFld['begindate']) > 0) { // 刷卡時間啟始日
            $obj_clockin->SQLWhere .= " AND clkintime >= '$arrQryFld[begindate] 00:00:00' ";
        } elseif (isset($arrQryFld['enddate']) && mb_strlen($arrQryFld['enddate']) > 0) { // 刷卡時間截止日
            $obj_clockin->SQLWhere .= " AND clkintime <= '$arrQryFld[enddate] 23:59:59' ";
        }
        $obj_clockin->SQLOrderBy .= " seq DESC ";
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數

        // 統計分頁訊息
        $obj_clockin->int_records_per_page = $arrQryFld['recdsperpage']; // 設定每頁筆數
        $obj_clockin->calTotalPages($obj_clockin->rtnQryField("SELECT COUNT(*) AS cnt_rcds ".$obj_clockin->SQLFrom.$obj_clockin->SQLWhere)); // 計算總頁數
        $htmlPaging = $obj_form->viewPaging($obj_clockin->int_total_records, $obj_clockin->int_total_pages, $obj_clockin->int_current_page); // 顯示查詢分頁HTML Tag

        // 傳回查詢結果
        $obj_clockin->SQLlimit = " LIMIT $obj_clockin->intStartPos,  $obj_clockin->int_records_per_page";
        $obj_clockin->SQL = $obj_clockin->SQLSelect.$obj_clockin->SQLFrom.$obj_clockin->SQLWhere.$obj_clockin->SQLOrderBy.$obj_clockin->SQLlimit;
        $htmlQryResult = $obj_clockin->viewQry($obj_clockin->rtnQryResults($obj_clockin->SQL), $tbl);

        //Save Session
        $_SESSION['arrQryFld'] = $arrQryFld;
        $_SESSION['SQL']['Select'] = $obj_clockin->SQLSelect;
        $_SESSION['SQL']['From'] = $obj_clockin->SQLFrom;
        $_SESSION['SQL']['Where'] = $obj_clockin->SQLWhere;
        $_SESSION['SQL']['OrderBy'] = $obj_clockin->SQLOrderBy;
        $_SESSION['SQL']['CurPage'] = $obj_clockin->int_current_page; //儲存目前頁數

        if (isset($_POST['prt'])) { //按下"列印"按鈕的處理動作
            $obj_form->js_openWindow('ClockInHstyRpt.php');
        }

    }elseif (isset($_POST['edit'])) { //按下"編輯"按鈕的處理動作
        $_SESSION['selFormCode'] = $_POST['selFormCode']; //儲存目前編輯記錄
        header("location: holidaysEdit.php");
        exit();
    }elseif (isset($_POST['paging']) || isset($_POST['discard']) || (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel"))) { //執行"分頁/編輯完成/取消編輯"功能後的處理動作
        // if (isset($_POST['discard'])) { //按下"註銷"按鈕的處理動作
        //     if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '請假管理', '註銷')) { //檢查使用者是否有使用權限
        //         $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有請假資料的註銷權限，如需該功能的使用權限，請與管理者聯絡");
        //         $obj_form->js_goURL(INDEXPAGE); //返回首頁
        //         exit();
        //     }
        //     $obj_clockin->discard($_POST['selFormCode']); //註銷記錄
        // }
        
        //取得分頁條件
        $arrQryFld = $_SESSION['arrQryFld'];
        $obj_clockin->SQLSelect = $_SESSION['SQL']['Select'];
        $obj_clockin->SQLFrom = $_SESSION['SQL']['From'];
        $obj_clockin->SQLWhere = $_SESSION['SQL']['Where'];
        $obj_clockin->SQLOrderBy = $_SESSION['SQL']['OrderBy'];
        $htmlTags['year'] = $obj_form->viewHTMLSTSglVal(array('attrId'=>'year', 'attrName'=>'year', 'attrTitle'=>'請選擇年度'), array(date("Y", time())-4, date("Y", time())-3, date("Y", time())-2, date("Y", time())-1, date("Y", time())), $arrQryFld['year'], true); // 年度(西元年)
        $htmlTags['deptspk'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'deptspk', 'attrName'=>'deptspk', 'attrTitle'=>'請選擇機構', 'optionTitle'=>'cmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_depts->getList(), $arrQryFld['deptspk'], true); //機構
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數

        // 統計分頁訊息
        $obj_clockin->int_records_per_page = $arrQryFld['recdsperpage']; // 設定每頁筆數
        $obj_clockin->calTotalPages($obj_clockin->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_clockin->SQLFrom.$obj_clockin->SQLWhere)); // 計算總頁數

        // 計算目前頁數
        if (isset($_POST['paging'])) { //分頁
            $obj_clockin->int_current_page = $_POST['CurPage'];
            switch ($_POST['paging']) {
                case '|< 第一頁':
                    $obj_clockin->int_current_page = 1;
                    break;
                case '<< 上一頁':
                    $obj_clockin->int_current_page--;
                    break;
                case '下一頁 >>':
                    $obj_clockin->int_current_page++;
                    break;
                case '最後頁 >|':
                    $obj_clockin->int_current_page = $obj_clockin->int_total_pages;
                    break;
                default:
                    # code...
                    break;
            }
            $_SESSION['SQL']['CurPage'] = $obj_clockin->int_current_page; // 儲存目前頁數
        } elseif (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel")) { // 編輯 or 取消編輯
            $obj_clockin->int_current_page = $_SESSION['SQL']['CurPage']; // 取得目前頁數
        }

        $htmlPaging = $obj_form->viewPaging($obj_clockin->int_total_records, $obj_clockin->int_total_pages, $obj_clockin->int_current_page); // 顯示查詢分頁HTML Tag

        // 傳回查詢結果
        $obj_clockin->intStartPos = ($obj_clockin->int_current_page - 1) < 0 ? 0 : ($obj_clockin->int_current_page - 1) * $obj_clockin->int_records_per_page ; // 資料註銷後, 檢查啟始記錄是否小於0
        $obj_clockin->SQLlimit = " LIMIT $obj_clockin->intStartPos,  $obj_clockin->int_records_per_page";
        $obj_clockin->SQL = $obj_clockin->SQLSelect.$obj_clockin->SQLFrom.$obj_clockin->SQLWhere.$obj_clockin->SQLOrderBy.$obj_clockin->SQLlimit;
        $htmlQryResult = $obj_clockin->viewQry($obj_clockin->rtnQryResults($obj_clockin->SQL), $tbl);
    } else { //第一次執行時的處理動作
        $htmlTags['year'] = $obj_form->viewHTMLSTSglVal(array('attrId'=>'year', 'attrName'=>'year', 'attrTitle'=>'請選擇年度'), array(date("Y", time())-4, date("Y", time())-3, date("Y", time())-2, date("Y", time())-1, date("Y", time())), date("Y", time()), null); // 年度(西元年)
        $htmlTags['deptspk'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'deptspk', 'attrName'=>'deptspk', 'attrTitle'=>'請選擇機構', 'optionTitle'=>'cmpapl', 'optionValue'=>'formcode'), $obj_depts->getList(), null, true); //機構
        //$htmlTags['html_sort'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'sort', 'attrName'=>'sort', 'attrTitle'=>'請輸入排序方式', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'品項代碼欄位-由小到大排序', 'srtValue'=>'mtrlcode ASC'), array('srtTitle'=>'品項代碼欄位-由大到小排序', 'srtValue'=>'mtrlcode DESC')), "品項代碼欄位-由小到大排序"); //排序方式
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, 100); //每頁顯示筆數
        //$htmlTags['html_recdsperpage'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'50', 'srtValue'=>'50'), array('srtTitle'=>'100', 'srtValue'=>'100'), array('srtTitle'=>'250', 'srtValue'=>'250'), array('srtTitle'=>'500', 'srtValue'=>'500')), 500); //每頁顯示筆數
    };

    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    } elseif (isset($_POST['query']) || isset($_POST['paging']) || isset($_POST['discard'])) { //顯示完成訊息
        $strStsMsg = "資料查詢完成, 總筆數：$obj_clockin->int_total_records";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_depts = null;
    $obj_emp = null;
    $obj_clockin = null;
    $obj_pms = null;
    $obj_form = null;
    //End

echo <<<_html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查詢刷卡歷史資料</title>
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
                    // msg = "開始編輯";
                    // btn = "編輯";
                    // $('#selFormCode').val($(this).attr('attrformcode')); //設定被選取記錄的formcode欄位
                }else if($(this).val() == "註銷"){ //按下"註銷"按鈕
                    // msg = "該筆資料己經註銷";
                    // btn = "註銷";
                    // $('#selFormCode').val($(this).attr('attrformcode')); //設定被選取記錄的formcode欄位
                    // return confirm("你是否確定註銷該筆記錄？"); //確認使用者是否註銷該筆記錄？
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

echo <<<_html
    <!-- main區塊 -->
    <main>
        <div class="row"><h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5></div>
        
        <h4 class="text-secondary text-decoration-underline my-3"><b>查詢刷卡歷史資料</b></h4>
        <div class="row justify-content-center mt-3">
            <input type="hidden" id="selFormCode" name="selFormCode" value="">
            <div class="row">
                <div class="col-sm-2">
                    <label for="year" class="form-label">年度(西元年)：</label>$htmlTags[year]
                </div>
                <div class="col-sm-2">
                    <label for="depts" class="form-label">機構：</label>$htmlTags[deptspk]
                </div>
                <div class="col-sm-2">
                    <label for="empapl" class="form-label">員工名稱：</label>
                    <input type="text" class="form-control" id="empapl" name="empapl" value="{$arrQryFld['empapl']}" placeholder="請輸入員工名稱" title="請輸入員工名稱">
                </div>
                <div class="col-sm-2">
                    <label for="begindate" class="form-label">刷卡時間啟始日：</label>
                    <input type="date" class="form-control" id="begindate" name="begindate" value="{$arrQryFld['begindate']}" placeholder="請輸入刷卡時間啟始日" title="請輸入刷卡時間啟始日">
                </div>
                <div class="col-sm-2">
                    <label for="enddate" class="form-label">刷卡時間截止日：</label>
                    <input type="date" class="form-control" id="enddate" name="enddate" value="{$arrQryFld['enddate']}" placeholder="請輸入刷卡時間截止日" title="請輸入刷卡時間截止日">
                </div>
                <div class="col-sm-2">
                    <label for="supplier_telephone" class="form-label">每頁顯示筆數：</label>$htmlTags[html_recdsperpage]
                </div>
            </div>
            
            <div class="row  justify-content-center mt-2">
                <input type="submit" class="col-sm-1 btn btn-primary" id="query" name="query" value="查詢">&nbsp;&nbsp;<input type="reset" value="清除" class="col-sm-1 btn btn-outline-primary">&nbsp;&nbsp;<input type="submit" class="col-sm-1 btn btn-outline-primary" id="query" name="prt" value="列印">
            </div>
            
            <table class="table caption-top table-striped table-hover my-5">
                <caption><h4><b>刷卡歷史資料清單</b></h4></caption>
                <thead class="">
                    <tr>
                        <th class="col-sm-2">機構</th><th class="col-sm-1 text-center">員工</th><th class="col-sm-1 text-center">年度</th><th class="col-sm-1 text-center">刷卡時間</th><th class="col-sm-1 text-center">刷卡狀態</th><th class="col-sm-2">刷卡說明</th>
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