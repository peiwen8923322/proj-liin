<?php

    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_stocks.php";
    require_once "../../models/cls_suppliers.php";
    require_once "../../models/cls_materials.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '領料管理', '建立')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有領料資料的建立權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(INDEXPAGE); //返回首頁
        exit();
    }
    $obj_stocks = new cls_stocks; //庫存檔
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
        
        $arrNewFormVal['formcode'] = $obj_stocks->getNextFormCode(Date("Ym", time())); //表單編號
        $arrNewFormVal['creator'] = $_SESSION['login_emp']['empapl'];//建立者
        $arrNewFormVal['modifier'] = $_SESSION['login_emp']['empapl'];//修改者
        $arrNewFormVal['jobmth'] = "請領"; //作業異動類別
        $arrNewFormVal['expdate'] = ""; //作業異動者
        
        //參考其他Table
        $tbl['suppliers'] = $obj_suppliers->rtnQryRecord("SELECT * FROM $obj_suppliers->self_table WHERE 1 AND formstate = 15 AND splrcode = '$arrNewFormVal[splrcode]'"); //供應商
        $tbl['materials'] = $obj_materials->getRecdBySplrcodeMtrlcode($arrNewFormVal['splrcode'], $arrNewFormVal['mtrlcode']); //品項名稱
        // $tbl['materials'] = $obj_materials->rtnQryRecord("SELECT * FROM $obj_materials->self_table WHERE 1 AND formstate = 15 AND splrcode = '$arrNewFormVal[splrcode]' AND mtrlcode = '$arrNewFormVal[mtrlcode]'"); //品項名稱
        
        //執行SQL
        $strNewSeq = $obj_stocks->Insert($arrNewFormVal, $tbl);

        //Render HTML
        $htmlTags['html_jobdate'] = $arrNewFormVal['jobdate']; //作業日期
        $htmlTags['html_jobort'] = $arrNewFormVal['jobort']; //作業異動者
        $htmlTags['html_splrcode'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'splrcode', 'attrName'=>'splrcode', 'attrTitle'=>'請選擇供應商名稱', 'optionTitle'=>'splrapl', 'optionValue'=>'splrcode'), $obj_suppliers->getList(), $tbl['suppliers']['splrapl']); //供應商名稱
        $htmlTags['html_mtrlcode'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'mtrlcode', 'attrName'=>'mtrlcode', 'attrTitle'=>'請選擇品項名稱', 'optionTitle'=>'mtrlapl', 'optionValue'=>'mtrlcode'), $obj_materials->getListBySupplier($arrNewFormVal['splrcode']), $tbl['materials']['mtrlapl']); //品項名稱
        $htmlTags['html_mtrlamt'] = $arrNewFormVal['mtrlamt']; //品項數量
        $htmlTags['html_safeamt'] = $tbl['materials']['safeamt']; //安全存量
        $htmlTags['html_mtrlprc'] = $arrNewFormVal['mtrlprc']; //品項單價
        $htmlTags['html_mtrlagg'] = $arrNewFormVal['mtrlagg']; //品項總金額
        $htmlTags['html_memo'] = $arrNewFormVal['memo']; //備註
    } else { //表單第一次執行的處理動作
        //預設表單欄位
        $htmlTags['html_jobdate'] = date("Y-m-d", time()); //作業日期
        $htmlTags['html_jobort'] = ""; //作業異動者
        $htmlTags['html_splrcode'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'splrcode', 'attrName'=>'splrcode', 'attrTitle'=>'請選擇供應商名稱', 'optionTitle'=>'splrapl', 'optionValue'=>'splrcode'), $obj_suppliers->getList(), "禾欣實業有限公司"); //供應商名稱
        $htmlTags['html_mtrlcode'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'mtrlcode', 'attrName'=>'mtrlcode', 'attrTitle'=>'請選擇品項名稱', 'optionTitle'=>'mtrlapl', 'optionValue'=>'mtrlcode'), $obj_materials->getListBySupplier("K007"), "酒精棉片"); //品項名稱
        $htmlTags['html_mtrlamt'] = "1"; //品項數量
        $tbl['materials'] = $obj_materials->getRecdBySplrcodeMtrlcode('K007', 'AL003');
        $htmlTags['html_safeamt'] = $tbl['materials']['safeamt']; //安全存量
        $htmlTags['html_mtrlprc'] = "0"; //品項單價
        $htmlTags['html_mtrlagg'] = "0"; //品項總金額
        $htmlTags['html_memo'] = ""; //備註
    }

    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    }elseif (isset($strNewSeq)) { //顯示完成訊息
        $strStsMsg = "資料建立完成 [$strNewSeq]";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_stocks = null;
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
    <title>領料管理</title>
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
                    $("#mtrlagg").val($("#mtrlamt").val() * $("#mtrlprc").val()); //計算品項總金額
                    //msg = "資料已建立成功";
                    //btn = "確定";
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

            //計算品項總金額
            $("#mtrlamt").blur(function () { //品項數量
                $("#mtrlagg").val($("#mtrlamt").val() * $("#mtrlprc").val());
            });
            $("#mtrlprc").blur(function () { //品項單價
                $("#mtrlagg").val($("#mtrlamt").val() * $("#mtrlprc").val());
            });

            //用jQuery AJAX取得供應商的品項清單
            $("#splrcode").click(function(){
                $.post("./jqChgMtrlcodeFld.php", {splrcode: $("#splrcode").val()}, function(data,status){
                    $("#mtrlcode").html(data);

                    //alert("數據：" + data + " 狀態：" + status);
                });
            });

            //用jQuery AJAX取得該品項的安全存量
            $("#mtrlcode").blur(function(){
                $.post("./jqChgSafeamtFld.php", {splrcode: $("#splrcode").val(), mtrlcode: $("#mtrlcode").val()}, function(data,status){
                    $("#safeamt").val(data);

                    // alert("供應商：" + $("#splrcode").val() + "  / 品項：" + $("#mtrlcode").val());
                });
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

    include_once "receivingMaterialNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
    <!-- main區塊 -->
    <main>
        <div class="row"><h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5></div>
        <h4 class="text-secondary text-decoration-underline my-3"><b>建立領料資料</b></h4>
        <div class="row">
            <div class="col-sm-10">
                <div class="row justify-content-center">
                    <div class="col-sm-2 fw-bolder"><label for="jobdate" class="form-label">作業日期：</label></div>
                    <div class="col-sm-2"><input type="date" class="form-control" id="jobdate" name="jobdate" value="$htmlTags[html_jobdate]" title="請輸入作業日期" required></div>
                    <div class="col-sm-2 fw-bolder"><label for="jobort" class="form-label">作業異動者：(員工姓名)</label></div>
                    <div class="col-sm-2"><input type="text" class="form-control" id="jobort" name="jobort" value="$htmlTags[html_jobort]" title="請輸入作業異動者" placeholder="請輸入作業異動者" autofocus required></div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-sm-2 fw-bolder"><label for="splrcode">供應商名稱：</label></div>
                    <div class="col-sm-6">
                        $htmlTags[html_splrcode]
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-sm-2 fw-bolder"><label for="mtrlcode">品項名稱：</label></div>
                    <div class="col-sm-6">
                        $htmlTags[html_mtrlcode]
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-sm-2 fw-bolder"><label for="mtrlamt" class="form-label">品項數量：</label></div>
                    <div class="col-sm-2"><input type="text" class="form-control" id="mtrlamt" name="mtrlamt" value="$htmlTags[html_mtrlamt]" title="請輸入品項數量" placeholder="請輸入品項數量" required></div>
                    <div class="col-sm-2 fw-bolder"><label for="safeamt" class="form-label">安全存量：</label></div>
                    <div class="col-sm-2"><input type="text" class="form-control" id="safeamt" name="safeamt" value="$htmlTags[html_safeamt]" title="安全存量" placeholder="安全存量" readonly></div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-sm-2 fw-bolder"><label for="mtrlprc" class="form-label">品項單價：</label></div>
                    <div class="col-sm-2"><input type="text" class="form-control" id="mtrlprc" name="mtrlprc" value="$htmlTags[html_mtrlprc]" title="請輸入品項單價" placeholder="請輸入品項單價" required></div>
                    <div class="col-sm-2 fw-bolder"><label for="mtrlagg" class="form-label">品項總金額：</label></div>
                    <div class="col-sm-2"><input type="text" class="form-control" id="mtrlagg" name="mtrlagg" value="$htmlTags[html_mtrlagg]" title="請輸入品項總金額" placeholder="請輸入品項總金額" required></div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-sm-2 fw-bolder"><label for="memo" class="form-label">備註：</label></div>
                    <div class="col-sm-6"><input type="text" class="form-control" id="memo" name="memo" title="請輸入備註" value="$htmlTags[html_memo]" placeholder="請輸入備註"></div>
                </div>
                
            </div>

            <div class="col-2">
                <div class="row">
                    <div class="col text-start fw-bolder">
                        
                    </div>
                </div>
            </div>

            <div class="row justify-content-center my-3">
                <input type="submit" class="col-1 btn btn-primary" id="submit" name="submit" value="確定">&nbsp;&nbsp;<input type="reset" class="col-1 btn btn-outline-primary" value="取消">
            </div>
        </div>
        <div class="gy-5">&nbsp;</div>

    </main>    
    
    </form>

    </div>
</body>
</html>
_html;

?>