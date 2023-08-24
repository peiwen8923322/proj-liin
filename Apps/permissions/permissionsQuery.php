<?php
    
    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_employees.php";
    require_once "../../models/cls_prgs.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '權限管理', '查詢')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有權限管理的查詢權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(MOBILEINDEXPAGE); //返回首頁
        exit();
    }
    $obj_emp = new cls_employees; //員工檔
    $obj_prgs = new cls_prgs; //程式檔

    $SQL = "";
    $htmlQryResult = ""; //顯示查詢結果HTML Tag
    $htmlPaging = ""; //顯示查詢分頁HTML Tag
    $arrQryFld = ['empapl'=>'', 'prgcls'=>'']; //儲存淨化後的查詢條件
    $strStsMsg = ""; //儲存狀態欄訊息

    //Begin
    if(isset($_POST['query'])){ //按下"查詢"按鈕的處理動作
        $arrQryFld = $obj_form->inputChk($_POST); //淨化查詢條件

        $obj_pms->SQLSelect = "SELECT p.formcode AS pmsformcode, p.empapl AS empapl, p.prgcls AS prgcls, p.prgact AS prgact, e.formcode AS empformcode, e.cmpapl AS cmpapl ";
        $obj_pms->SQLFrom = " FROM pms p INNER JOIN employees e ON (p.empformcode=e.formcode) ";
        $obj_pms->SQLWhere .= " AND p.formstate = 15 ";
        $obj_pms->SQLWhere .= isset($arrQryFld['empapl']) && mb_strlen($arrQryFld['empapl']) > 0 ? " AND p.empapl LIKE '%{$arrQryFld['empapl']}%' " : ""; //員工姓名欄
        $obj_pms->SQLWhere .= isset($arrQryFld['prgcls']) && mb_strlen($arrQryFld['prgcls']) > 0 ? " AND p.prgcls LIKE '%{$arrQryFld['prgcls']}%' " : ""; //程式分類
        //$obj_pms->SQLWhere .= isset($arrQryFld['empidno']) && mb_strlen($arrQryFld['empidno']) > 0 ? " AND empidno LIKE '%{$arrQryFld['empidno']}%' " : ""; //身份證字號欄
        //$obj_pms->SQLWhere .= isset($arrQryFld['mobilephone']) && mb_strlen($arrQryFld['mobilephone']) > 0 ? " AND mobilephone LIKE '%{$arrQryFld['mobilephone']}%' " : ""; //手機號碼欄
        $obj_pms->SQLOrderBy .= " p.formcode DESC ";
        
        //統計分頁訊息
        $obj_pms->calTotalPages($obj_pms->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_pms->SQLFrom.$obj_pms->SQLWhere));
        $htmlPaging = $obj_form->viewPaging($obj_pms->int_total_records, $obj_pms->int_total_pages, $obj_pms->int_current_page); //顯示查詢分頁HTML Tag

        //傳回查詢結果
        $obj_pms->SQLlimit = " LIMIT $obj_pms->intStartPos,  $obj_pms->int_records_per_page";
        $obj_pms->SQL = $obj_pms->SQLSelect.$obj_pms->SQLFrom.$obj_pms->SQLWhere.$obj_pms->SQLOrderBy.$obj_pms->SQLlimit;
        $htmlQryResult = $obj_pms->viewQry($obj_pms->rtnQryResults($obj_pms->SQL));

        //Save Session
        $_SESSION['arrQryFld'] = $arrQryFld;
        $_SESSION['SQL']['Select'] = $obj_pms->SQLSelect;
        $_SESSION['SQL']['From'] = $obj_pms->SQLFrom;
        $_SESSION['SQL']['Where'] = $obj_pms->SQLWhere;
        $_SESSION['SQL']['OrderBy'] = $obj_pms->SQLOrderBy;
        $_SESSION['SQL']['CurPage'] = $obj_pms->int_current_page; //儲存目前頁數
    }elseif (isset($_POST['paging']) || isset($_POST['discard']) || (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel"))) { //執行"分頁/編輯完成/取消編輯"功能後的處理動作
        if (isset($_POST['discard'])) { //按下"註銷"按鈕的處理動作
            if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '權限管理', '註銷')) { //檢查使用者是否有使用權限
                $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有權限管理的註銷權限，如需該功能的使用權限，請與管理者聯絡");
                $obj_form->js_goURL(INDEXPAGE); //返回首頁
                exit();
            }
            $obj_pms->discard($_POST['selFormCode']); //註銷記錄
        }
        
        //取得分頁條件
        $arrQryFld = $_SESSION['arrQryFld'];
        $obj_pms->SQLSelect = $_SESSION['SQL']['Select'];
        $obj_pms->SQLFrom = $_SESSION['SQL']['From'];
        $obj_pms->SQLWhere = $_SESSION['SQL']['Where'];
        $obj_pms->SQLOrderBy = $_SESSION['SQL']['OrderBy'];

        //統計分頁訊息
        $obj_pms->calTotalPages($obj_pms->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_pms->SQLFrom.$obj_pms->SQLWhere));
        
        //計算目前頁數
        if (isset($_POST['paging'])) { //分頁
            $obj_pms->int_current_page = $_POST['CurPage'];
            switch ($_POST['paging']) {
                case '|< 第一頁':
                    $obj_pms->int_current_page = 1;
                    break;
                case '<< 上一頁':
                    $obj_pms->int_current_page--;
                    break;
                case '下一頁 >>':
                    $obj_pms->int_current_page++;
                    break;
                case '最後頁 >|':
                    $obj_pms->int_current_page = $obj_pms->int_total_pages;
                    break;
                default:
                    # code...
                    break;
            }
            $_SESSION['SQL']['CurPage'] = $obj_pms->int_current_page; //儲存目前頁數
        } elseif (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel")) { //編輯 or 取消編輯
            $obj_pms->int_current_page = $_SESSION['SQL']['CurPage']; //取得目前頁數
        }
        
        $htmlPaging = $obj_form->viewPaging($obj_pms->int_total_records, $obj_pms->int_total_pages, $obj_pms->int_current_page); //顯示查詢分頁HTML Tag

        //傳回查詢結果
        $obj_pms->intStartPos = ($obj_pms->int_current_page - 1) < 0 ? 0 : ($obj_pms->int_current_page - 1) * $obj_pms->int_records_per_page ; //資料註銷後, 檢查啟始記錄是否小於0
        //$obj_pms->intStartPos = ($obj_pms->int_current_page - 1) * $obj_pms->int_records_per_page;
        $obj_pms->SQLlimit = " LIMIT $obj_pms->intStartPos,  $obj_pms->int_records_per_page";
        $obj_pms->SQL = $obj_pms->SQLSelect.$obj_pms->SQLFrom.$obj_pms->SQLWhere.$obj_pms->SQLOrderBy.$obj_pms->SQLlimit;
        $htmlQryResult = $obj_pms->viewQry($obj_pms->rtnQryResults($obj_pms->SQL));
    } else { //第一次執行時的處理動作
        $htmlTags['html_atwrksta'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'atwrksta', 'attrName'=>'atwrksta', 'attrTitle'=>'請選擇在職狀況', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'在職', 'srtValue'=>'15'), array('srtTitle'=>'離職', 'srtValue'=>'14'), array('srtTitle'=>'停職', 'srtValue'=>'13')), '在職'); //在職狀況
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, 100); //每頁顯示筆數
    };
    
    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    } elseif (isset($_POST['query']) || isset($_POST['paging']) || isset($_POST['discard'])) { //顯示查詢/分頁訊息
        $strStsMsg = "資料查詢完成, 總筆數：$obj_pms->int_total_records";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_form = null;
    $obj_emp = null;
    $obj_prgs = null;
    $obj_pms = null;
    //End

echo <<<_html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查詢員工權限</title>
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
                if($(this).val() == "編輯"){
                    //$("#form1").attr("action", "permissionsEdit.php"); //設定form表單的action屬性
                    //msg = "開始編輯";
                    //btn = "編輯";
                }else if($(this).val() == "註銷"){
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
                if ($(this).val() == "登出") {
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

    include_once "permissionsNav.php"; //nav區塊 操作選單

echo <<<_html
    <!-- main區塊 -->
    <main>
        <div class="row"><h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5></div>
        <h4 class="text-secondary text-decoration-underline my-3"><b>查詢員工權限</b></h4>
        <div class="row justify-content-center mt-3">
            <input type="hidden" id="selFormCode" name="selFormCode" value="">
            <div class="row">
                <div class="col-sm-3">
                    <label for="empapl" class="form-label">員工姓名：</label>
                    <input type="text" class="form-control" id="empapl" name="empapl" value="$arrQryFld[empapl]" placeholder="請輸入員工姓名" title="請輸入員工姓名">
                </div>
                <div class="col-sm-3">
                    <label for="prgcls" class="form-label">程式分類：</label>
                    <input type="text" class="form-control" id="prgcls" name="prgcls" value="$arrQryFld[prgcls]" placeholder="請輸入程式分類" title="請輸入程式分類">
                </div>
            </div>

            <div class="row justify-content-center mt-2">
                <input type="submit" class="col-sm-1 btn btn-primary" id="query" name="query" value="查詢">&nbsp;&nbsp;<input type="reset" value="清除" class="col-sm-1 btn btn-outline-primary">
            </div>
            
            <table class="table caption-top table-striped table-hover my-5">
                <caption><h4><b>員工權限清單</b></h4></caption>
                <thead class="">
                    <tr>
                        <th class="">功能</th><th class="">機構</th><th class="text-center">員工姓名</th><th class="">程式分類</th><th class="">程式執行權限</th>
                    </tr>
                </thead>

                {$htmlQryResult}
            </table>
            
            {$htmlPaging}
            <div class="g-5"></div>
        </div>
            
        
    </main>

    </form>
    </div>
</body>
</html>
_html;

?>