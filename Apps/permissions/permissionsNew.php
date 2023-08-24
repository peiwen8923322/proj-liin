<?php

    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_employees.php";
    require_once "../../models/cls_prgs.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '權限管理', '建立')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有權限管理的建立權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(MOBILEINDEXPAGE); //返回首頁
        exit();
    }
    $obj_emp = new cls_employees; //員工檔
    $obj_prgs = new cls_prgs; //程式檔

    $count = 0;
    $htmlTags = array(); //Render HTML
    $tbl = array(); //儲存不同的參考檔
    $arrNewFormVal = ""; //儲存淨化後的建立內容
    $qryCondition = ""; //參考其他Table的SQL WHERE條件
    $strNewSeq = null; //記錄建立成功後, 儲存新記錄序號
    $strStsMsg = ""; //儲存狀態欄訊息

    //Begin
    $tbl['emp'] = $obj_emp->getRecdByFormcode($_SESSION['login_emp']['formcode']); //員工檔

    if (isset($_POST['submit']) && $_POST['submit'] == '確定') { //按下"確定"按鈕的處理動作
        if (!isset($_POST['prgact'])) { //沒有設定程式執行權限, 則顯示錯誤訊息
            $_POST['prgact'] = array(); //設定程式執行權限為 null值
            trigger_error("請選擇程式執行權限"); //觸發錯誤訊息
        }
        $arrNewFormVal = $obj_form->inputChk($_POST); //淨化查詢條件
        $arrNewFormVal['formcode'] = $obj_pms->getNextFormCode(Date("Ym", time())); //表單編號
        $arrNewFormVal['creator'] = $_SESSION['login_emp']['empapl'];//建立者
        $arrNewFormVal['modifier'] = $_SESSION['login_emp']['empapl'];//修改者
        if ((!in_array('查詢', $arrNewFormVal['prgact'])) && (in_array('編輯', $arrNewFormVal['prgact']) || in_array('註銷', $arrNewFormVal['prgact']))) { //只設定程式的編輯或註銷權限時, 會自動加上查詢權限
            array_push($arrNewFormVal['prgact'], "查詢");
        }
        $arrNewFormVal['actions'] = join(", ", $arrNewFormVal['prgact']); //合併程式執行權限為字串

        //參考其他Table
        foreach ($arrNewFormVal['empformcode'] as $value) { // 設定權限的員工唯一識別碼加上單引號
            $arrNewEmp[] = $obj_emp->PDO->quote($value);
        }
        $strEmp = implode(", ", $arrNewEmp); // 將員工陣列組合成字串
        $tbl['pmsEmp'] = $obj_emp->rtnQryResults("SELECT * FROM employees WHERE 1 AND formcode IN ($strEmp)"); // 設定權限的員工

        //新增 or 更新記錄
        foreach ($tbl['pmsEmp'] as $value) {
            if (!$obj_pms->isExistPms($value['formcode'], $arrNewFormVal['prgcls'])) { //新增記錄
                $strNewSeq = $obj_pms->Insert($arrNewFormVal, $value);
            } else { //更新記錄
                $obj_pms->Update($arrNewFormVal, $value);
            }
            $count++;
        }

        //Render HTML
        $htmlTags['html_prgs'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'prgs', 'attrName'=>'prgcls', 'attrTitle'=>'請選擇程式', 'optionTitle'=>'prgcls', 'optionValue'=>'prgcls'), $obj_prgs->getClsList(), $arrNewFormVal['prgcls']); //程式
        $htmlTags['html_empapl'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'emp', 'attrName'=>'empformcode', 'attrTitle'=>'請選擇員工', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_emp->getListAtWork(), $arrNewFormVal['empformcode'], false, true); //員工('optionTitle'=>'NewEmpapl')
        $htmlTags['html_prgact'] = $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'prgact', 'attrName'=>'prgact', 'Label'=>'prgact', 'attrValue'=>'prgact'), $obj_prgs->getRcds(" AND formstate = 15 AND prgcls = '$_POST[prgcls]' "), $_POST['prgact']); //程式執行權限
        //$strStsMsg = "{$tbl['emp']['empapl']}權限建立完成";
    } else { //表單第一次執行的處理動作
        $htmlTags['html_prgs'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'prgs', 'attrName'=>'prgcls', 'attrTitle'=>'請選擇程式', 'optionTitle'=>'prgcls', 'optionValue'=>'prgcls'), $obj_prgs->getClsList()); //程式
        $htmlTags['html_empapl'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'emp', 'attrName'=>'empformcode', 'attrTitle'=>'請選擇員工', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_emp->getListAtWork(), $tbl['emp']['formcode'], false, true); //員工('optionTitle'=>'NewEmpapl')
        // $htmlTags['html_empapl'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'emp', 'attrName'=>'empformcode', 'attrTitle'=>'請選擇員工', 'optionTitle'=>'empapl', 'optionValue'=>'formcode'), $obj_emp->getEmpListByUsage(), ['徐培文', '廖夏君', '陳金月'], false, true); //員工
        $htmlTags['html_prgact'] = $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'prgact', 'attrName'=>'prgact', 'Label'=>'prgact', 'attrValue'=>'prgact'), $obj_prgs->getRcds(" AND formstate = 15 AND prgcls = '員工資料管理' "), array("查詢")); //程式執行權限
    }
    
    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    } elseif ($count > 0) { //顯示完成訊息
        $strStsMsg = "員工權限已建立($count 筆記錄)";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_emp = null;
    $obj_prgs = null;
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
    <title>建立員工權限</title>
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
                    // msg = "資料已建立成功";
                    // btn = "確定";
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

            //用jQuery AJAX取得程式的權限清單
            $("#prgs").click(function(){
                $.post("./jqChgPmsFld.php", {prgcls: $("#prgs").val()}, function(data,status){
                    $("#pms").html(data);

                    //alert("數據：" + data + " 狀態：" + status);
                });
            });




        });
    </script>
</head>
<body>
    <div class="container-fluid">

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>

    <form action="permissionsNew.php" method="post" id="form1" name="form1">
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

    include_once "permissionsNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
<!-- main區塊 -->
    <main>
        <div class="row"><h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5></div>
        <h4 class="text-secondary text-decoration-underline my-3"><b>建立員工權限</b></h4>
        <div class="row">
            <div class="col-sm-10">
                <div class="row my-2">
                    <div class="col-sm-2 fw-bolder"><label for="passwd" class="form-label">程式：</label></div>
                    <div class="col-sm-6">$htmlTags[html_prgs]</div>
                </div>
                <div class="row my-2">
                    <div class="col-sm-2 fw-bolder"><label for="account" class="form-label">員工：</label></div>
                    <div class="col-sm-6">$htmlTags[html_empapl]</div>
                </div>
                <div class="row my-2">
                    <div class="col-sm-2 fw-bolder"><label for="prgact_01" class="form-label">程式執行權限：</label></div>
                    <div class="col-sm" id="pms">$htmlTags[html_prgact]</div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="prgact_01" class="form-label">說明：</label></div>
                    <div class="col-sm text-danger font-bolder">
                        <h6>
                        ※ 01. 為員工設定請假權限時，請選擇建立/查詢/編輯/註銷/審核<br/>
                        ※ 02. 請選擇任何一項執行權限, 否則執行權限無法建立<br/>
                        ※ 03.程式的執行權限重覆建立時, 則覆蓋之前的權限設定<br/>
                        ※ 04. 只設定程式的編輯或註銷權限時, 會自動加上查詢權限
                        </h6>
                    </div>
                </div>
                <div class="row justify-content-center my-3">
                    <input type="submit" class="col-sm-1 btn btn-primary" id="submit" name="submit" value="確定">&nbsp;&nbsp;<input type="reset" class="col-sm-1 btn btn-outline-primary" value="取消">
                </div>
            </div>

            <div class="col-sm-2">
                <div class="row"></div>
            </div>
        </div>
        
    </main>    
    
    </form>
    </div>
</body>
</html>
_html;

?>