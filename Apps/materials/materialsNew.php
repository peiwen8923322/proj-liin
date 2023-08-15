<?php

    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_suppliers.php";
    require_once "../../models/cls_materials.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '庫存品項管理', '建立')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有庫存品項資料的建立權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(INDEXPAGE); //返回首頁
        exit();
    }
    $obj_suppliers = new cls_suppliers; //供應商檔
    $obj_materials = new cls_materials; //耗材檔

    $htmlTags = array(); //Render HTML
    $tbl = array(); //儲存不同的參考檔
    $arrNewFormVal = ""; //儲存淨化後的表單內容
    $qryCondition = ""; //參考其他Table的SQL WHERE條件
    $strNewSeq = null; //記錄建立成功後, 儲存新記錄序號
    $strStsMsg = ""; //儲存狀態欄訊息

    //Begin
    if (isset($_POST['submit']) && $_POST['submit'] == "確定") { //按下"確定"按鈕的處理動作
        $arrNewFormVal = $obj_form->inputChk($_POST); //淨化查詢條件
        
        $arrNewFormVal['formcode'] = $obj_materials->getNextFormCode(Date("Ym", time())); //表單編號
        $arrNewFormVal['creator'] = $_SESSION['login_emp']['empapl'];//建立者
        $arrNewFormVal['modifier'] = $_SESSION['login_emp']['empapl'];//修改者
        
        //參考其他Table
        $tbl['suppliers'] = $obj_suppliers->rtnQryRecord("SELECT * FROM $obj_suppliers->self_table WHERE 1 AND formstate = 15 AND splrcode = '$arrNewFormVal[splrcode]'"); //供應商
        // $tbl['materials'] = $obj_materials->rtnQryRecord("SELECT * FROM $obj_materials->self_table WHERE 1 AND formstate = 15 AND splrcode = '$arrNewFormVal[splrcode]' AND mtrlcode = '$arrNewFormVal[mtrlcode]'"); //品項名稱
        
        //執行SQL
        $strNewSeq = $obj_materials->Insert($arrNewFormVal, $tbl);

        //Render HTML
        $htmlTags['html_splrcode'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'splrcode', 'attrName'=>'splrcode', 'attrTitle'=>'請選擇供應商名稱', 'optionTitle'=>'splrapl', 'optionValue'=>'splrcode'), $obj_suppliers->getList(), $tbl['suppliers']['splrapl']); //供應商名稱
        $htmlTags['html_mtrlcode'] = $arrNewFormVal['mtrlcode']; //品項代碼
        $htmlTags['html_mtrlapl'] = $arrNewFormVal['mtrlapl']; //品項名稱
        $htmlTags['html_safeamt'] = $arrNewFormVal['safeamt']; //安全存量
    } else { //表單第一次執行的處理動作
        //預設表單欄位
        $htmlTags['html_splrcode'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'splrcode', 'attrName'=>'splrcode', 'attrTitle'=>'請選擇供應商名稱', 'optionTitle'=>'splrapl', 'optionValue'=>'splrcode'), $obj_suppliers->getList(), "禾欣實業有限公司"); //供應商名稱
        $htmlTags['html_mtrlcode'] = ""; //品項代碼
        $htmlTags['html_mtrlapl'] = ""; //品項名稱
        $htmlTags['html_safeamt'] = "0"; //安全存量
    }

    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    }elseif (isset($strNewSeq)) { //顯示完成訊息
        $strStsMsg = "資料建立完成 [$strNewSeq]";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_suppliers = null;
    $obj_materials = null;
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
    <title>建立品項資料</title>
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
                if($(this).val() == "確定"){
                    //msg = "資料已建立成功";
                    //btn = "確定";
                }
            });

            //設定form表單的button type欄位 Click事件
            $(":button").click(function() {
                if ($(this).val() == "登出") {
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

    include_once "materialsNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
    <!-- main區塊 -->
    <main>
        <h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5>
        <div class="container-fluid">
            <h4 class="text-secondary text-decoration-underline my-3"><b>建立品項資料</b></h4>
            <div class="row justify-content-center g-1"><h5 class="col-9 text-danger fw-bold mx-3">※填寫前請查詢目前庫存品項的資料格式</h5></div>
            <div class="row justify-content-center g-1" style="height: 1.6cm;">
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="splrcode">供應商名稱：</label></div>
                <div class="col-8">
                    $htmlTags[html_splrcode]
                </div>
            </div>
            <div class="row justify-content-center g-1" style="height: 1.6cm;">
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="mtrlcode" class="form-label">品項代碼：</label></div>
                <div class="col-2"><input type="text" class="form-control" style="height: 1.6cm;" id="mtrlcode" name="mtrlcode" value="$htmlTags[html_mtrlcode]" title="請輸入品項代碼" placeholder="請輸入品項代碼"></div>
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="mtrlapl" class="form-label">品項名稱：</label></div>
                <div class="col-5"><input type="text" class="form-control" style="height: 1.6cm;" id="mtrlapl" name="mtrlapl" value="$htmlTags[html_mtrlapl]" title="請輸入品項名稱" placeholder="請輸入品項名稱"></div>
            </div>
            <div class="row justify-content-center g-1" style="height: 1.6cm;">
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="safeamt" class="form-label">安全存量：</label></div>
                <div class="col-2"><input type="number" class="form-control" style="height: 1.6cm;" id="safeamt" name="safeamt" value="$htmlTags[html_safeamt]" title="請輸入安全存量" placeholder="請輸入安全存量"></div>
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"></div>
                <div class="col-2"></div>
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"></div>
                <div class="col-2"></div>
            </div>

            <div class="row justify-content-center my-3">
                <input type="submit" class="col-1 btn btn-primary" id="submit" name="submit" value="確定">&nbsp;&nbsp;<input type="reset" class="col-1 btn btn-outline-primary" value="取消">
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