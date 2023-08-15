<?php

use GuzzleHttp\Promise\Create;

    //Require_once
    require_once "../../models//common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_employees.php";
    
    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '員工資料管理', '查詢')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有員工資料的查詢權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(INDEXPAGE); //返回首頁
        exit();
    }
    $obj_employees = new cls_employees;
    
    $SQL = "";
    $htmlQryResult = ""; //顯示查詢結果HTML Tag
    $htmlPaging = ""; //顯示查詢分頁HTML Tag
    $arrQryFld = ['empapl'=>"", 'empcode'=>"", 'mobilephone'=>""]; //儲存淨化查詢條件
    $strStsMsg = ""; //儲存狀態欄訊息
    
    //Begin
    if(isset($_POST['query'])){ //按下"查詢"按鈕的處理動作
        $arrQryFld = $obj_form->inputChk($_POST); //淨化查詢條件
        
        $obj_employees->SQLSelect .= ", year(CURRENT_DATE()) - year(takeofcdate) AS 'seniority' ";
        $obj_employees->SQLWhere .= " AND formstate = 15 ";
        $obj_employees->SQLWhere .= isset($arrQryFld['empapl']) && mb_strlen($arrQryFld['empapl']) > 0 ? " AND empapl LIKE '%{$arrQryFld['empapl']}%' " : ""; //員工姓名欄
        $obj_employees->SQLWhere .= isset($arrQryFld['empcode']) && mb_strlen($arrQryFld['empcode']) > 0 ? " AND empcode LIKE '%{$arrQryFld['empcode']}%' " : ""; //員工編號
        $obj_employees->SQLWhere .= isset($arrQryFld['atwrksta']) && mb_strlen($arrQryFld['atwrksta']) > 0 ? " AND atwrksta = $arrQryFld[atwrksta] " : ""; //在職狀況
        $obj_employees->SQLOrderBy .= " empcode DESC ";
        $htmlTags['html_atwrksta'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'atwrksta', 'attrName'=>'atwrksta', 'attrTitle'=>'請選擇在職狀況', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue', 'default'=>'srtValue'), array(array('srtTitle'=>'在職', 'srtValue'=>'15'), array('srtTitle'=>'離職', 'srtValue'=>'14'), array('srtTitle'=>'停職', 'srtValue'=>'13')), $arrQryFld['atwrksta']); //在職狀況
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數

        //統計分頁訊息
        $obj_employees->int_records_per_page = $arrQryFld['recdsperpage']; //設定每頁筆數
        $obj_employees->calTotalPages($obj_employees->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_employees->SQLFrom.$obj_employees->SQLWhere));
        $htmlPaging = $obj_form->viewPaging($obj_employees->int_total_records, $obj_employees->int_total_pages, $obj_employees->int_current_page); //顯示查詢分頁HTML Tag

        //傳回查詢結果
        $obj_employees->SQLlimit = " LIMIT $obj_employees->intStartPos,  $obj_employees->int_records_per_page";
        $obj_employees->SQL = $obj_employees->SQLSelect.$obj_employees->SQLFrom.$obj_employees->SQLWhere.$obj_employees->SQLOrderBy.$obj_employees->SQLlimit;
        $htmlQryResult = $obj_employees->viewQry($obj_employees->rtnQryResults($obj_employees->SQL));

        //Save Session
        $_SESSION['arrQryFld'] = $arrQryFld;
        $_SESSION['SQL']['Select'] = $obj_employees->SQLSelect;
        $_SESSION['SQL']['From'] = $obj_employees->SQLFrom;
        $_SESSION['SQL']['Where'] = $obj_employees->SQLWhere;
        $_SESSION['SQL']['OrderBy'] = $obj_employees->SQLOrderBy;
        $_SESSION['SQL']['CurPage'] = $obj_employees->int_current_page; //儲存目前頁數
    }elseif (isset($_POST['edit'])) { //按下"編輯"按鈕的處理動作
        $_SESSION['selFormCode'] = $_POST['selFormCode']; //儲存目前編輯記錄
        header("location: empEdit.php");
        exit();
    }elseif (isset($_POST['paging']) || isset($_POST['discard']) || (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel"))) { //執行"分頁/編輯完成/取消編輯"功能後的處理動作
        if (isset($_POST['discard'])) { //按下"註銷"按鈕的處理動作
            if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '員工資料管理', '註銷')) { //檢查使用者是否有使用權限
                $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有員工資料的註銷權限，如需該功能的使用權限，請與管理者聯絡");
                $obj_form->js_goURL(INDEXPAGE); //返回首頁
                exit();
            }
            
            $obj_pms->discardEmpPms($_POST['selFormCode']); //刪除該員工的權限記錄
            $obj_employees->discard($_POST['selFormCode']); //註銷記錄
        }
        
        //取得分頁條件
        $arrQryFld = $_SESSION['arrQryFld'];
        $obj_employees->SQLSelect = $_SESSION['SQL']['Select'];
        $obj_employees->SQLFrom = $_SESSION['SQL']['From'];
        $obj_employees->SQLWhere = $_SESSION['SQL']['Where'];
        $obj_employees->SQLOrderBy = $_SESSION['SQL']['OrderBy'];
        $htmlTags['html_atwrksta'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'atwrksta', 'attrName'=>'atwrksta', 'attrTitle'=>'請選擇在職狀況', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue', 'default'=>'srtValue'), array(array('srtTitle'=>'在職', 'srtValue'=>'15'), array('srtTitle'=>'離職', 'srtValue'=>'14'), array('srtTitle'=>'停職', 'srtValue'=>'13')), $arrQryFld['atwrksta']); //在職狀況
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數

        //統計分頁訊息
        $obj_employees->int_records_per_page = $arrQryFld['recdsperpage']; //設定每頁筆數
        $obj_employees->calTotalPages($obj_employees->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_employees->SQLFrom.$obj_employees->SQLWhere));
        
        //計算目前頁數
        if (isset($_POST['paging'])) { //分頁
            $obj_employees->int_current_page = $_POST['CurPage'];
            switch ($_POST['paging']) {
                case '|< 第一頁':
                    $obj_employees->int_current_page = 1;
                    break;
                case '<< 上一頁':
                    $obj_employees->int_current_page--;
                    break;
                case '下一頁 >>':
                    $obj_employees->int_current_page++;
                    break;
                case '最後頁 >|':
                    $obj_employees->int_current_page = $obj_employees->int_total_pages;
                    break;
                default:
                    # code...
                    break;
            }
            $_SESSION['SQL']['CurPage'] = $obj_employees->int_current_page; //儲存目前頁數
        } elseif (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel")) { //編輯 or 取消編輯
            $obj_employees->int_current_page = $_SESSION['SQL']['CurPage']; //取得目前頁數
        }
        
        $htmlPaging = $obj_form->viewPaging($obj_employees->int_total_records, $obj_employees->int_total_pages, $obj_employees->int_current_page); //顯示查詢分頁HTML Tag

        //傳回查詢結果
        $obj_employees->intStartPos = ($obj_employees->int_current_page - 1) < 0 ? 0 : ($obj_employees->int_current_page - 1) * $obj_employees->int_records_per_page ; //資料註銷後, 檢查啟始記錄是否小於0
        $obj_employees->SQLlimit = " LIMIT $obj_employees->intStartPos,  $obj_employees->int_records_per_page";
        $obj_employees->SQL = $obj_employees->SQLSelect.$obj_employees->SQLFrom.$obj_employees->SQLWhere.$obj_employees->SQLOrderBy.$obj_employees->SQLlimit;
        $htmlQryResult = $obj_employees->viewQry($obj_employees->rtnQryResults($obj_employees->SQL));
    } else { //第一次執行時的處理動作
        $htmlTags['html_atwrksta'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'atwrksta', 'attrName'=>'atwrksta', 'attrTitle'=>'請選擇在職狀況', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'在職', 'srtValue'=>'15'), array('srtTitle'=>'離職', 'srtValue'=>'14'), array('srtTitle'=>'停職', 'srtValue'=>'13')), '在職'); //在職狀況
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, 100); //每頁顯示筆數
    };

    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    } elseif (isset($_POST['query']) || isset($_POST['paging']) || isset($_POST['discard'])) { //顯示完成訊息
        $strStsMsg = "資料查詢完成, 總筆數：$obj_employees->int_total_records";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_employees = null;
    $obj_pms = null;
    $obj_form = null;
    //End  

echo <<<_HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查詢員工資料</title>
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
                    return confirm("你是否確定註銷該筆記錄，及刪除該員工的所有權限？"); //確認使用者是否註銷該筆記錄？
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
            <h6 class="col-auto text-end">使用者：{$_SESSION['login_emp']['empapl']} / 帳號：{$_SESSION['login_emp']['empcode']} / 登入日期：$_SESSION[login_time] &nbsp;&nbsp;<input type="button" class="btn btn-outline-light" name="logout" value="登出"></h6>
        </div>        
    </header>
_HTML;

    include_once "../../Require/navigation.php"; //Nav區塊 下拉選單(路徑大小寫有區分)

    include_once "empNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_HTML
    <!-- main區塊 -->
    <main>
        <h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5>
        <div class="container-fluid">
            <h4 class="text-secondary fw-bold my-3">查詢員工資料</h4>
            <div class="row justify-content-center mt-3">
                <input type="hidden" id="selFormCode" name="selFormCode" value="">
                <div class="row">
                    <div class="col-3">
                        <label for="empapl" class="form-label">員工姓名：</label>
                        <input type="text" class="form-control" id="empapl" name="empapl" value="{$arrQryFld['empapl']}" placeholder="請輸入員工姓名" title="請輸入員工姓名" autofocus>
                    </div>
                    <div class="col-3">
                        <label for="empidno" class="form-label">員工編號：</label>
                        <input type="text" class="form-control" id="empcode" name="empcode" value="{$arrQryFld['empcode']}" placeholder="請輸入員工編號" title="請輸入員工編號">
                    </div>
                    <div class="col-3">
                        <label for="mobilephone" class="form-label">在職狀況：</label>
                        $htmlTags[html_atwrksta]
                    </div>
                    <div class="col-3">
                        <label for="supplier_telephone" class="form-label">每頁顯示筆數：</label>
                        $htmlTags[html_recdsperpage]
                    </div>
                </div>
                <div class="row justify-content-center mt-2">
                    <input type="submit" class="col-1 btn btn-primary" id="query" name="query" value="查詢">&nbsp;&nbsp;<input type="reset" value="清除" class="col-1 btn btn-outline-primary">
                </div>
    
                <table class="table caption-top table-striped table-hover my-5">
                    <caption><h4 class="text-secondary fw-bold mx-3">員工資料清單</h4></caption>
                    <thead class="">
                        <tr>
                            <th class="col-1 text-center">功能</th><th class="col-1">機構</th><th class="col-1 text-center">員工姓名<br/>員工編號</th><th class="col-1 text-center">身份證字號<br/>生日</th><th class="col-1 text-center">到職日<br/>年資</th><th class="col-1 text-center">員工角色<br/>單位主管角色</th><th class="col-1 text-center">代理人<br/>單位主管</th><th class="col-1 text-center">人事主管<br/>會計主管</th><th class="col-1 text-center">主任</th>
                        </tr>
                    </thead>

                    $htmlQryResult
                </table>
  
                $htmlPaging
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
_HTML;
?>

