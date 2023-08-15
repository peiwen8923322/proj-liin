<?php

    //Require_once
    require_once "../../models//common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_stocks.php";
    
    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '盤點管理', '查詢')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有盤點資料的查詢權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(INDEXPAGE); //返回首頁
        exit();
    }
    $obj_stocks = new cls_stocks; //庫存檔
    
    $htmlTags = array(); //Render HTML
    $SQL = "";
    $htmlQryResult = ""; //顯示查詢結果HTML Tag
    $htmlPaging = ""; //顯示查詢分頁HTML Tag
    $arrQryFld = ['jobort'=>"", 'mtrlapl'=>"", 'sort'=>"", 'recdsperpage'=>""]; //儲存淨化查詢條件
    $strStsMsg = ""; //儲存狀態欄訊息
    
    //Begin
    if(isset($_POST['query']) || isset($_POST['export'])){ //按下"查詢 / 匯出"按鈕的處理動作
        $arrQryFld = $obj_form->inputChk($_POST); //淨化查詢條件
        
        $obj_stocks->SQLSelect = " SELECT s.*, m.safeamt ";
        $obj_stocks->SQLFrom = " from stocks s LEFT OUTER JOIN materials m ON(s.splrcode=m.splrcode AND s.mtrlcode=m.mtrlcode) ";
        $obj_stocks->SQLWhere .= " AND m.formstate = 15 AND s.formstate = 15 AND s.jobmth = '盤盈虧調整' ";
        $obj_stocks->SQLWhere .= isset($arrQryFld['jobort']) && mb_strlen($arrQryFld['jobort']) > 0 ? " AND s.jobort LIKE '%{$arrQryFld['jobort']}%' " : ""; //盤點人
        $obj_stocks->SQLWhere .= isset($arrQryFld['mtrlapl']) && mb_strlen($arrQryFld['mtrlapl']) > 0 ? " AND s.mtrlapl LIKE '%{$arrQryFld['mtrlapl']}%' " : ""; //品項名稱欄
        //$obj_stocks->SQLWhere .= isset($arrQryFld['mobilephone']) && mb_strlen($arrQryFld['mobilephone']) > 0 ? " AND mobilephone LIKE '%{$arrQryFld['mobilephone']}%' " : ""; //手機號碼欄
        $obj_stocks->SQLOrderBy .= isset($arrQryFld['sort']) ? " $arrQryFld[sort] " : '' ; //設定排序方式
        $htmlTags['html_sort'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'sort', 'attrName'=>'sort', 'attrTitle'=>'請輸入排序方式', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue', 'default'=>'srtValue'), array(array('srtTitle'=>'品項代碼欄位-由小到大排序', 'srtValue'=>'s.mtrlcode ASC'), array('srtTitle'=>'品項代碼欄位-由大到小排序', 'srtValue'=>'s.mtrlcode DESC')), $arrQryFld['sort']); //排序方式
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數
        //$htmlTags['html_recdsperpage'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'50', 'srtValue'=>'50'), array('srtTitle'=>'100', 'srtValue'=>'100'), array('srtTitle'=>'250', 'srtValue'=>'250'), array('srtTitle'=>'500', 'srtValue'=>'500')), $arrQryFld['recdsperpage']); //每頁顯示筆數

        //統計分頁訊息
        $obj_stocks->int_records_per_page = $arrQryFld['recdsperpage']; //設定每頁筆數
        $obj_stocks->calTotalPages($obj_stocks->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_stocks->SQLFrom.$obj_stocks->SQLWhere));
        $htmlPaging = $obj_form->viewPaging($obj_stocks->int_total_records, $obj_stocks->int_total_pages, $obj_stocks->int_current_page); //顯示查詢分頁HTML Tag

        //傳回查詢結果
        $obj_stocks->SQLlimit = " LIMIT $obj_stocks->intStartPos, $obj_stocks->int_records_per_page";
        $obj_stocks->SQL = $obj_stocks->SQLSelect.$obj_stocks->SQLFrom.$obj_stocks->SQLWhere.$obj_stocks->SQLOrderBy.$obj_stocks->SQLlimit;
        $htmlQryResult = $obj_stocks->viewPchOdr($obj_stocks->rtnQryResults($obj_stocks->SQL));

        //Save Session
        $_SESSION['arrQryFld'] = $arrQryFld; //儲存查詢條件
        $_SESSION['SQL']['Select'] = $obj_stocks->SQLSelect;
        $_SESSION['SQL']['From'] = $obj_stocks->SQLFrom;
        $_SESSION['SQL']['Where'] = $obj_stocks->SQLWhere;
        $_SESSION['SQL']['OrderBy'] = $obj_stocks->SQLOrderBy;
        $_SESSION['SQL']['CurPage'] = $obj_stocks->int_current_page; //儲存目前頁數

        if (isset($_POST['export'])) { //按下"匯出"按鈕的處理動作
            $obj_form->js_openWindow('checkStocksExport.php');
            // header('Content-Type: text/csv; charset=utf-8');
            // header('Content-Disposition: attachment; filename="download.txt"');
            // echo "匯出";
        }
    }elseif (isset($_POST['edit'])) { //按下"編輯"按鈕的處理動作
        $_SESSION['selFormCode'] = $_POST['selFormCode']; //儲存目前編輯記錄
        header("location: checkStocksEdit.php");
        exit();
    }elseif (isset($_POST['paging']) || isset($_POST['discard']) || (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel"))) { //執行"分頁/編輯完成/取消編輯"功能後的處理動作
        if (isset($_POST['discard'])) { //按下"註銷"按鈕的處理動作
            if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '盤點管理', '註銷')) { //檢查使用者是否有使用權限
                $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有進貨資料的註銷權限，如需該功能的使用權限，請與管理者聯絡");
                $obj_form->js_goURL(INDEXPAGE); //返回首頁
                exit();
            }
            $obj_stocks->discard($_POST['selFormCode']); //註銷記錄
        }
        
        //取得分頁條件
        $arrQryFld = $_SESSION['arrQryFld']; //取得查詢條件
        $obj_stocks->SQLSelect = $_SESSION['SQL']['Select'];
        $obj_stocks->SQLFrom = $_SESSION['SQL']['From'];
        $obj_stocks->SQLWhere = $_SESSION['SQL']['Where'];
        $obj_stocks->SQLOrderBy = $_SESSION['SQL']['OrderBy'];
        $htmlTags['html_sort'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'sort', 'attrName'=>'sort', 'attrTitle'=>'請輸入排序方式', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue', 'default'=>'srtValue'), array(array('srtTitle'=>'品項代碼欄位-由小到大排序', 'srtValue'=>'mtrlcode ASC'), array('srtTitle'=>'品項代碼欄位-由大到小排序', 'srtValue'=>'mtrlcode DESC')), $arrQryFld['sort']); //排序方式
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數
        //$htmlTags['html_recdsperpage'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'50', 'srtValue'=>'50'), array('srtTitle'=>'100', 'srtValue'=>'100'), array('srtTitle'=>'250', 'srtValue'=>'250'), array('srtTitle'=>'500', 'srtValue'=>'500')), $arrQryFld['recdsperpage']); //每頁顯示筆數

        //統計分頁訊息
        $obj_stocks->int_records_per_page = $arrQryFld['recdsperpage']; //設定每頁筆數
        $obj_stocks->calTotalPages($obj_stocks->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_stocks->SQLFrom.$obj_stocks->SQLWhere));
        
        //計算目前頁數
        if (isset($_POST['paging'])) { //分頁
            $obj_stocks->int_current_page = $_POST['CurPage'];
            switch ($_POST['paging']) {
                case '|< 第一頁':
                    $obj_stocks->int_current_page = 1;
                    break;
                case '<< 上一頁':
                    $obj_stocks->int_current_page--;
                    break;
                case '下一頁 >>':
                    $obj_stocks->int_current_page++;
                    break;
                case '最後頁 >|':
                    $obj_stocks->int_current_page = $obj_stocks->int_total_pages;
                    break;
                default:
                    # code...
                    break;
            }
            $_SESSION['SQL']['CurPage'] = $obj_stocks->int_current_page; //儲存目前頁數
        } elseif (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel")) { //編輯 or 取消編輯
            $obj_stocks->int_current_page = $_SESSION['SQL']['CurPage']; //取得目前頁數
        }
        $htmlPaging = $obj_form->viewPaging($obj_stocks->int_total_records, $obj_stocks->int_total_pages, $obj_stocks->int_current_page); //顯示查詢分頁HTML Tag

        //傳回查詢結果
        $obj_stocks->intStartPos = ($obj_stocks->int_current_page - 1) < 0 ? 0 : ($obj_stocks->int_current_page - 1) * $obj_stocks->int_records_per_page ; //資料註銷後, 檢查啟始記錄是否小於0
        $obj_stocks->SQLlimit = " LIMIT $obj_stocks->intStartPos,  $obj_stocks->int_records_per_page";
        $obj_stocks->SQL = $obj_stocks->SQLSelect.$obj_stocks->SQLFrom.$obj_stocks->SQLWhere.$obj_stocks->SQLOrderBy.$obj_stocks->SQLlimit;
        $htmlQryResult = $obj_stocks->viewPchOdr($obj_stocks->rtnQryResults($obj_stocks->SQL));
    } else { //第一次執行時的處理動作
        $htmlTags['html_sort'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'sort', 'attrName'=>'sort', 'attrTitle'=>'請輸入排序方式', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'品項代碼欄位-由小到大排序', 'srtValue'=>'s.mtrlcode ASC'), array('srtTitle'=>'品項代碼欄位-由大到小排序', 'srtValue'=>'s.mtrlcode DESC')), "品項代碼欄位-由小到大排序"); //排序方式
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, 100); //每頁顯示筆數
        //$htmlTags['html_recdsperpage'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'50', 'srtValue'=>'50'), array('srtTitle'=>'100', 'srtValue'=>'100'), array('srtTitle'=>'250', 'srtValue'=>'250'), array('srtTitle'=>'500', 'srtValue'=>'500')), 500); //每頁顯示筆數
    };

    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    } elseif (isset($_POST['query']) || isset($_POST['paging']) || isset($_POST['discard']) || isset($_POST['export'])) { //顯示完成訊息
        $strStsMsg = "資料查詢完成, 總筆數：$obj_stocks->int_total_records";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_stocks = null;
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
    <title>盤點管理</title>
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
                    //$("#form1").attr("action", "checkStocksEdit.php"); //設定form表單的action屬性
                    msg = "開始編輯";
                    btn = "編輯";
                    $('#selFormCode').val($(this).attr('attrformcode')); //設定被選取記錄的formcode欄位
                }else if($(this).val() == "註銷"){ //按下"註銷"按鈕
                    msg = "該筆資料己經註銷";
                    btn = "註銷";
                    $('#selFormCode').val($(this).attr('attrformcode')); //設定被選取記錄的formcode欄位
                    return confirm("你是否確定註銷該筆記錄？"); //確認使用者是否註銷該筆記錄?
                }else if($(this).val() == "查詢"){
                    //msg = "查詢成功";
                    //btn = "查詢";
                }
            });

            //設定form表單的button type欄位 Click事件
            $(":button").click(function() {
                if ($(this).val() == "登出") { //按下"登出"按鈕
                    msg = "你已經登出系統";
                    btn = "登出";
                    location.assign("../../Public/login.php");
                    alert(msg);
                }
            });

        });
    </script>
</head>
<body>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>

    <form action="" method="post" id="form1" name="form1">
    <!--  header區塊  -->
    <header>
        <div class="d-flex flex-row text-white" style="background-color: #3E7050;">
            <h1 class="col-4 me-auto"><img src="../../Images/Banners/logo.png" width="100" height="120" alt="立穎健康照護" style="vertical-align: middle;">立穎健康照護</h1>
            <h6 class="col-auto text-end">使用者：{$_SESSION['login_emp']['empapl']} / 帳號：{$_SESSION['login_emp']['empcode']} / 登入日期：$_SESSION[login_time] &nbsp;&nbsp;<input type="button" class="btn btn-outline-light" value="登出"></h6>
        </div>        
    </header>
_html;

    include_once "../../Require/navigation.php"; //Nav區塊 下拉選單(路徑大小寫有區分)

    include_once "checkStocksNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
    <!-- main區塊 -->
    <main>
        <h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5>
        <div class="container-fluid">
            <h4 class="text-secondary text-decoration-underline my-3"><b>查詢盤點資料</b></h4>
            <div class="row justify-content-center mt-3">
                <input type="hidden" id="selFormCode" name="selFormCode" value="">
                <div class="row">
                    <div class="col-3">
                        <label for="jobort" class="form-label">盤點人：</label>
                        <input type="text" class="form-control" id="jobort" name="jobort" value="{$arrQryFld['jobort']}" title="請輸入盤點人" placeholder="請輸入盤點人">
                    </div>
                    <div class="col-3">
                        <label for="mtrlapl" class="form-label">品項名稱：</label>
                        <input type="text" class="form-control" id="mtrlapl" name="mtrlapl" value="{$arrQryFld['mtrlapl']}" title="請輸入品項名稱" placeholder="請輸入品項名稱" autofocus>
                    </div>
                    <div class="col-3">
                        <label for="applicant" class="form-label">排序方式：</label>
                        $htmlTags[html_sort]
                    </div>
                    <div class="col-3">
                        <label for="supplier_telephone" class="form-label">每頁顯示筆數：</label>
                        $htmlTags[html_recdsperpage]
                    </div>
                </div>
                <div class="row justify-content-center mt-2">
                    <input type="submit" class="col-1 btn btn-primary" id="query" name="query" value="查詢">
                    &nbsp;&nbsp;<input type="reset" value="清除" class="col-1 btn btn-outline-primary">
                    
                    <!--
                    &nbsp;&nbsp;<input type="submit" class="col-1 btn btn-outline-primary" id="export" name="export" value="匯出">

                    <div class="col-1 btn-group" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">匯出</button>
                        <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><input type="submit" class="btn" id="exp01" name="exp01" value="匯出盤點資料"></li>
                            <li><a class="dropdown-item" href="#">匯出盤點資料</a></li>
                            <li><a class="dropdown-item" href="#">Dropdown link</a></li>
                        </ul>
                    </div>
                    -->
                </div>
                
                <table class="table caption-top table-striped table-hover my-5">
                    <caption><h4><b>盤點清單</b></h4></caption>
                    <thead class="">
                        <tr>
                            <th class="col-1 text-center">功能</th><th class="col-1">作業日期</th><th class="col-1">作業異動者</th><th class="col-1">供應商代碼</th><th class="col-1">供應商名稱</th><th class="col-1">品項代碼</th><th class="col-1">品項名稱</th><th class="col-1 text-center">品項數量</th><th class="col-1 text-center">品項單價</th><th class="col-1 text-center">品項總金額</th><th class="col-1 text-center">保存期限</th><th class="col-1 text-center">安全存量</th>
                        </tr>
                    </thead>

                    {$htmlQryResult}
                </table>
                
                {$htmlPaging}
                <div class="g-5"></div>
            </div>
            
        </div>
    </main>

    <!-- footer區塊 -->
    <!--
    <footer>
        <div class="container-fluid">
            <div class="row justify-content-center my-3">
                <div class="col-1">
                    <a href="#" title="註冊">註冊</a>
                </div>
                <div class="col-1">
                    <a href="#" title="變更密碼">變更密碼</a>
                </div> 
            </div>
        </div>
    </footer>
    -->
    </form>
</body>
</html>
_html;

?>