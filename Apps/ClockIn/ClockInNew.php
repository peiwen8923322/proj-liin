<?php

    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_clockin.php";
    require_once "../../models/cls_field_lists.php";
    require_once "../../models/cls_employees.php";

    //變數初始化
    $obj_form = new cls_form;
    // $obj_pms = new cls_pms; //權限檔
    // if (!$obj_pms->isOwnPmsByEmpformcode($_SESSION['login_emp']['formcode'], '請假管理', '建立')) { //檢查使用者是否有使用權限
    //     $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有請假管理的建立權限，如需該功能的使用權限，請與管理者聯絡");
    //     $obj_form->js_goURL(INDEXPAGE); //返回首頁
    //     exit();
    // }
    
    $obj_clockin = new cls_clockin; //刷卡檔
    $obj_fl = new cls_field_lists; //欄位清單檔
    $obj_emp = new cls_employees; //員工檔

    $clkintime = "";
    $htmlTags = array(); //Render HTML(二維關聯陣列)
    $tbl = array(); //儲存不同的參考檔(二維關聯陣列)
    $arrNewFormVal = ""; //儲存淨化後的表單內容
    $strNewSeq = null; //記錄建立成功後, 儲存新記錄序號
    $strStsMsg = ""; //儲存狀態欄訊息

    //Begin
    $tbl['emp'] = $obj_emp->getRecdByFormcode($_SESSION['login_emp']['formcode']); // 登入者

    if (isset($_POST['submit']) && $_POST['submit'] == "確定送出") { //按下"確定送出"按鈕的處理動作
        $arrNewFormVal = $obj_form->inputChk($_POST); // 淨化查詢條件
        $arrNewFormVal['formcode'] = $obj_clockin->getNextFormCode(Date("Ym", time())); // 表單編號
        $arrNewFormVal['creator'] = $_SESSION['login_emp']['empapl']; // 建立者
        $arrNewFormVal['modifier'] = $_SESSION['login_emp']['empapl']; // 修改者
        $clkintime = strtotime($arrNewFormVal['clkintime']); // 時間轉換 UNIX時間
        $arrNewFormVal['isnormality'] = (date("H", $clkintime) >= 8 && date("H", $clkintime) < 9) || (date("H", $clkintime) >= 17 && date("H", $clkintime) < 18) ? "正常" : "異常"; // 刷卡是否正常
        $arrNewFormVal['year'] = date("Y", $clkintime); // 年度
        
        // 參考其他Table
        $tbl['clkinstt'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['clkinsttpk']); //刷卡狀態
        
        // 執行SQL
        $strNewSeq = $obj_clockin->Insert($arrNewFormVal, $tbl);

        // Render HTML
        $htmlTags['html_isnormality'] = $arrNewFormVal['isnormality']; // 刷卡是否正常
        $htmlTags['submit_clkintime'] = $arrNewFormVal['clkintime']; // 已送出刷卡時間
        $htmlTags['html_clkinsttpk'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'clkinsttpk', 'attrName'=>'clkinsttpk', 'Label'=>'listapl', 'attrValue'=>'formcode', 'default'=>'formcode'), $obj_fl->getList("刷卡狀態"), $arrNewFormVal['clkinsttpk']); // 刷卡狀態
        $htmlTags['extodnymemo'] = $arrNewFormVal['extodnymemo']; // 刷卡異常說明
    } else { // 表單第一次執行的處理動作
        // 預設表單欄位
        $htmlTags['html_isnormality'] = ""; // 刷卡是否正常
        $htmlTags['submit_clkintime'] = ""; // 已送出刷卡時間
        $htmlTags['html_clkinsttpk'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'clkinsttpk', 'attrName'=>'clkinsttpk', 'Label'=>'listapl', 'attrValue'=>'formcode'), $obj_fl->getList("刷卡狀態"), "外出-上班刷卡"); // 刷卡狀態
        $htmlTags['extodnymemo'] = ""; // 刷卡異常說明
    }

    if (isset($_SESSION['error'])) { // 檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    }elseif (isset($strNewSeq)) { // 顯示完成訊息
        $strStsMsg = "資料建立完成 [$strNewSeq], 已送出刷卡時間：$arrNewFormVal[clkintime]";
        $obj_form->js_alert($strStsMsg);
    }

    // Close Connection
    $obj_emp = null;
    $obj_fl = null;
    $obj_clockin = null;
    // $obj_pms = null;
    $obj_form = null;
    //End

echo <<<_html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>建立刷卡資料(外出刷卡專用)</title>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">

    <!-- JQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        var msg = ""; //訊息欄
        var btn = ""; //使用者按下按鈕

        // Begin
        $(document).ready(function(){
            // 初始化
            let objDate = new Date();
            let strNow = objDate.toLocaleDateString() + " " + objDate.getHours() + ":" + objDate.getMinutes() + ":" + objDate.getSeconds();
            $("#h1_time").text(strNow); // 時間大標題
            $("#clkintime").val(strNow); // 刷卡時間欄位

            //設定表單 Submit事件
            $("#form1").submit(function(){
                // code...
            });

            //設定form表單的submit type欄位 Click事件
            $(":submit").click(function(){
                if($(this).val() == "確定送出"){ // 按下"確定送出"按鈕
                    // alert("已送出刷卡時間： " + $("#clkintime").val());
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

        // 打卡鐘
        let timer = setInterval(function(){
            let objDate = new Date();
            let strNow = objDate.toLocaleDateString() + " " + objDate.getHours() + ":" + objDate.getMinutes() + ":" + objDate.getSeconds();
            $("#h1_time").text(strNow); // 時間大標題
            $("#clkintime").val(strNow); // 刷卡時間欄位
            
            if (objDate.getHours() >= 8 && objDate.getHours() < 9 || objDate.getHours() >= 17 && objDate.getHours() < 18) { // 刷卡是否正常欄位
                $("#isnormality").val("正常");
            } else {
                $("#isnormality").val("異常");
            }
        }
        , 50);



        // End
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

    include_once "ClockInNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
    <!-- main區塊 -->
    <main>
        <h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5>
        <div class="container-fluid">
            <h4 class="text-secondary text-decoration-underline my-3"><b>建立刷卡資料(外出刷卡專用)</b></h4>
            <div class="row">
                <div class="col-10">
                    <div class="row">
                        <div class="col text-center"><div class="btn btn-success"><h1>打卡鐘</h1></div></div>
                    </div>

                    <p><h1 class="text-center fw-bolder" id="h1_time"></h1></p>
                    <div class="row justify-content-center">
                        <div class="col-2 text-end fw-bolder"><label for="clkintime" class="form-label">刷卡時間：</label></div>
                        <div class="col-2"><input type="text" class="form-control" id="clkintime" name="clkintime" value="" title="請輸入打卡時間" placeholder="請輸入打卡時間" required readonly></div>
                        <div class="col-2 text-end fw-bolder"><label for="clkintime" class="form-label">已送出刷卡時間：</label></div>
                        <div class="col-2 text-end fw-bolder">$htmlTags[submit_clkintime]</div>
                    </div>
                    <div class="row justify-content-center my-3">
                        <div class="col-2 text-end fw-bolder"><label for="isnormality" class="form-label">刷卡是否正常：</label></div>
                        <div class="col-6"><input type="text" class="form-control" id="isnormality" name="isnormality" value="$htmlTags[html_isnormality]" title="" placeholder="" required readonly></div>
                    </div>
                    <div class="row justify-content-center my-3">
                        <div class="col-2 text-end fw-bolder">刷卡狀態：</div>
                        <div class="col-6">$htmlTags[html_clkinsttpk]</div>
                    </div>
                    <div class="row justify-content-center my-3">
                        <div class="col-2 text-end fw-bolder">刷卡異常說明：</div>
                        <div class="col-6"><input type="text" class="form-control" id="extodnymemo" name="extodnymemo" value="$htmlTags[extodnymemo]" title="請輸入刷卡異常說明：" placeholder="請輸入刷卡異常說明："></div>
                    </div>
                    
                    <div class="row justify-content-center my-3">
                        <input type="submit" class="col-1 btn btn-primary" id="submit" name="submit" value="確定送出">
                    </div>
                </div>

                <div class="col-2">
                    <div class="row">
                        <div class="col text-start fw-bolder">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="gy-5">&nbsp;</div>
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