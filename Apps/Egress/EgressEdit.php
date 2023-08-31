<?php

    /* 
        作者：徐培文
        編輯外出資料
        修改日期：2023/08/25
    */
    
    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_egress.php";
    require_once "../../models/cls_field_lists.php";
    require_once "../../models/cls_employees.php";

    //變數初始化
    $obj_form = new cls_form;
    // $obj_pms = new cls_pms; //權限檔
    // if (!$obj_pms->isOwnPmsByEmpformcode($_SESSION['login_emp']['formcode'], '請假管理', '編輯')) { //檢查使用者是否有使用權限
    //     $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有請假管理的編輯權限，如需該功能的使用權限，請與管理者聯絡");
    //     $obj_form->js_goURL(MOBILEINDEXPAGE); //返回首頁
    //     exit();
    // }
    
    $obj_egress = new cls_egress; //外出檔
    $obj_field_lists = new cls_field_lists; //欄位清單檔
    $obj_emp = new cls_employees; //員工檔

    $arrCurRecord = array(); //儲存編輯記錄
    $htmlTags = array(); //Render HTML(二維關聯陣列)
    $tbl = array(); //儲存不同的參考檔(二維關聯陣列)
    $arrHldsStt = array(); // 假別統計(二維關聯陣列)
    $arrNewFormVal = ""; //儲存淨化後的建立內容
    $qryCondition = ""; //參考其他Table的SQL WHERE條件
    $strStsMsg = ""; //儲存狀態欄訊息

    //Begin
    $tbl['emp'] = $obj_emp->getRecdByFormcode($_SESSION['login_emp']['formcode']); //登入者
    $nowYear = date("Y", time()); // 目前西元年

    if (isset($_POST['submit'])) { //按下"送出 / 暫存"按鈕的處理動作
        $arrNewFormVal = $obj_form->inputChk($_POST); //淨化查詢條件
        $arrNewFormVal['formcode'] = $_SESSION['selFormCode']; //該筆記錄的表單編號
        $arrNewFormVal['modifier'] = $_SESSION['login_emp']['empapl'];//修改者
        $arrNewFormVal['cls'] = '外出'; // 類別
        $arrNewFormVal['applydate'] = date("Y-m-d H:i:s", time()); // 外出者簽核時間

        //參考其他Table
        $tbl['frmvry'] = ($_POST['submit'] == '送出') ? $obj_field_lists->getRcrdByFormcode('2023010004') : $obj_field_lists->getRcrdByFormcode('2023010003') ; //審核狀態 ("送出 / 暫存")
        
        //執行SQL
        $obj_egress->Update($arrNewFormVal, $tbl);
        
        //Render HTML
        $htmlTags['cmpapl'] = $arrNewFormVal['deptspk']; // 機構
        $htmlTags['empapl'] = $arrNewFormVal['empapl']; // 外出員工
        $htmlTags['empcode'] = $arrNewFormVal['empcode']; // 員工編號
        $htmlTags['year'] = $arrNewFormVal['year']; // 年度
        $htmlTags['egrersn'] = $arrNewFormVal['egrersn']; // 外出事由
        $htmlTags['begindate'] = $arrNewFormVal['begindate']; // 外出啟始日
        $htmlTags['enddate'] = $arrNewFormVal['enddate']; // 外出截止日
    } elseif (isset($_POST['logout'])) { //登出
        //$obj_form->logout();
    } else { //第一次執行表單的處理動作
        $arrQryFld = $_SESSION['arrQryFld'];
        $obj_egress->SQLSelect = "SELECT * ";
        $obj_egress->SQLFrom = $_SESSION['SQL']['From'];
        $obj_egress->SQLWhere = " WHERE 1 AND formcode = '$_SESSION[selFormCode]'";
        $obj_egress->SQL = $obj_egress->SQLSelect.$obj_egress->SQLFrom.$obj_egress->SQLWhere;
        $arrCurRecord = $obj_egress->rtnQryRecord($obj_egress->SQL); //取得目前的編輯記錄

        //Render HTML
        $htmlTags['cmpapl'] = $arrCurRecord['cmpapl']; // 機構
        $htmlTags['empapl'] = $arrCurRecord['empapl']; // 外出員工
        $htmlTags['empcode'] = $arrCurRecord['empcode']; // 員工編號
        $htmlTags['year'] = $arrCurRecord['year']; // 年度
        $htmlTags['egrersn'] = $arrCurRecord['egrersn']; // 外出事由
        $htmlTags['begindate'] = $arrCurRecord['begindate']; // 外出啟始日
        $htmlTags['enddate'] = $arrCurRecord['enddate']; // 外出截止日

        $strStsMsg = "資料編輯中"; //顯示訊息
    }
    
    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    }elseif (isset($_POST['submit'])) { //顯示完成訊息
        $strStsMsg = "資料已編輯成功";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_emp = null;
    $obj_field_lists = null;
    $obj_egress = null;
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
    <title>編輯員工外出資料</title>
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
                // 取消呈送
                if ($("#hldformcode").val() == '2022100101' && $("#hldshrs").val() != Math.floor($("#hldshrs").val())) { // 換休 + 請假時數含小數點
                    alert("請假時數說明：換休時數(單位：小時) / 生理假時數(單位：天) / 其他假別(單位：4小時)，請重新填寫請假啟始日和請假截止日");
                    return false;
                } 
                if ($("#hldformcode").val() == '2022100096' && $("#hldshrs").val() > 0) { // 生理假 + 含請假時數
                    alert("請假時數說明：換休時數(單位：小時) / 生理假時數(單位：天) / 其他假別(單位：4小時)，請重新填寫請假啟始日和請假截止日");
                    return false;
                }
                if (($("#hldformcode").val() == '2022100089' || $("#hldformcode").val() == '2022100090' || $("#hldformcode").val() == '2022100091' || $("#hldformcode").val() == '2022100092' || $("#hldformcode").val() == '2022100093' || $("#hldformcode").val() == '2022100094' || $("#hldformcode").val() == '2022100095' || $("#hldformcode").val() == '2022100097' || $("#hldformcode").val() == '2022100098' || $("#hldformcode").val() == '2022100099' || $("#hldformcode").val() == '2023010024' || $("#hldformcode").val() == '2022100100') && $("#hldshrs").val() % 4 != 0) { // 病假+事假+公假+特休假+婚假+喪假+家庭照顧假+陪產假+產檢假+產假+公傷病假+其他
                    alert("請假時數說明：換休時數(單位：小時) / 生理假時數(單位：天) / 其他假別(單位：4小時)，請重新填寫請假啟始日和請假截止日");
                    return false;
                }
            });

            //設定form表單的submit type欄位 Click事件
            $(":submit").click(function(){
                if($(this).val() == "送出" || $(this).val() == "暫存"){ // 按下"送出" / "暫存"按鈕
                    var interval = data; //請假小時
                    if(interval == 8) { //請假8小時
                        $("#hldsdays").val(1); //請假天數
                        $("#hldshrs").val(0); //請假小時 
                    }else if(interval < 8) { //請假 < 8小時
                        $("#hldsdays").val(0); //請假天數
                        $("#hldshrs").val(interval); //請假小時
                    }else if(interval > 8) { //請假 > 8小時
                        $("#hldsdays").val(Math.floor(interval/8)); //請假天數
                        $("#hldshrs").val(interval - Math.floor(interval/8)*8); //請假小時
                    }
                }
            });

            //設定form表單的button type欄位 Click事件
            $(":button").click(function() {
                if ($(this).val() == "關閉") {
                    location.assign("./EgressQuery.php?action=cancel");
                }else if ($(this).val() == "登出") {
                    msg = "你已經登出系統";
                    btn = "登出";
                    location.assign("../../Public/mlogin.php");
                    alert("你已經登出系統");
                }
            });

            //用jQuery AJAX計算請假天數及請假時數
            $("#begindate").blur(function(){ // 請假啟始日欄位
                $.post("./jqChgHldsFld.php", {begindate: $("#begindate").val(), enddate: $("#enddate").val()}, function(data,status){
                    // $("#hlds").val(data);

                    var interval = data; //請假小時
                    if(interval == 8) { //請假8小時
                        $("#hldsdays").val(1); //請假天數
                        $("#hldshrs").val(0); //請假小時 
                    }else if(interval < 8) { //請假 < 8小時
                        $("#hldsdays").val(0); //請假天數
                        $("#hldshrs").val(interval); //請假小時
                    }else if(interval > 8) { //請假 > 8小時
                        $("#hldsdays").val(Math.floor(interval/8)); //請假天數
                        $("#hldshrs").val(interval - Math.floor(interval/8)*8); //請假小時
                    }
                    // alert(interval);
                });
            });

            //用jQuery AJAX計算請假天數及請假時數
            $("#enddate").blur(function(){ // 請假截止日欄位
                $.post("./jqChgHldsFld.php", {begindate: $("#begindate").val(), enddate: $("#enddate").val()}, function(data,status){
                    // $("#hlds").val(data);

                    var interval = data; //請假小時
                    if(interval == 8) { //請假8小時
                        $("#hldsdays").val(1); //請假天數
                        $("#hldshrs").val(0); //請假小時 
                    }else if(interval < 8) { //請假 < 8小時
                        $("#hldsdays").val(0); //請假天數
                        $("#hldshrs").val(interval); //請假小時
                    }else if(interval > 8) { //請假 > 8小時
                        $("#hldsdays").val(Math.floor(interval/8)); //請假天數
                        $("#hldshrs").val(interval - Math.floor(interval/8)*8); //請假小時
                    }
                    // alert(interval);
                });
            });

            // 檢查假別時數是否正確
            $(":radio").click(function(){
                switch($(this).val()){
                    case '2022100101': // 換休
                        if($("#hldshrs").val() != Math.floor($("#hldshrs").val())){
                            alert("請假時數說明：換休時數(單位：小時) / 生理假時數(單位：天) / 其他假別(單位：4小時)，請重新填寫請假啟始日和請假截止日");
                        }
                        break;
                    case '2022100089': // 病假
                    case '2022100090': // 事假
                    case '2022100091': // 公假
                    case '2022100092': // 特休假
                    case '2022100093': // 婚假
                    case '2022100094': // 喪假
                    case '2022100095': // 家庭照顧假
                    case '2022100097': // 陪產假
                    case '2022100098': // 產檢假
                    case '2022100099': // 產假
                    case '2023010024': // 公傷病假
                    case '2022100100': // 其他
                        if($("#hldshrs").val() % 4 != 0){
                            alert("請假時數說明：換休時數(單位：小時) / 生理假時數(單位：天) / 其他假別(單位：4小時)，請重新填寫請假啟始日和請假截止日");
                        }
                        break;
                    case '2022100096': // 生理假
                        if($("#hldshrs").val() > 0){
                            alert("請假時數說明：換休時數(單位：小時) / 生理假時數(單位：天) / 其他假別(單位：4小時)，請重新填寫請假啟始日和請假截止日");
                        }
                        break;
                    default:
                }

                $("#hldformcode").val($(this).val());
            })



        });
    </script>
</head>
<body>
    <div class="container-fluid">

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>

    <form action="" method="post" id="form1" name="form1">
    <!--  header區塊  -->
    <div class="row justify-content-end text-white" style="background-color: #3E7050;">
        <div class="col-sm-auto"><input type="button" class="btn btn-outline-light" value="登出"></div>
        <h6 class="col-sm-auto">使用者：{$_SESSION['login_emp']['empapl']}</h6>
        <h6 class="col-sm-auto">帳號：{$_SESSION['login_emp']['empcode']}</h6>
        <h6 class="col-sm-auto">登入日期：$_SESSION[login_time]</h6>
    </div>
_html;

    include_once "../../Require/mnavigation.php"; //Nav區塊 下拉選單(路徑大小寫有區分)

    include_once "EgressNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
    <!-- main區塊 -->
    <main>
        <div class="row"><h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5></div>
            <h4 class="text-secondary text-decoration-underline my-3"><b>編輯員工外出資料</b></h4>
            <div class="row">
                <div class="col-10">
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="deptspk" class="form-label">機構：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="deptspk" name="deptspk" value="$htmlTags[cmpapl]" title="機構" readonly></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="empapl" class="form-label">外出員工：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="empapl" name="empapl" value="$htmlTags[empapl]" title="外出員工" readonly></div>
                        <div class="col-sm-2 fw-bolder"><label for="empcode" class="form-label">員工編號：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="empcode" name="empcode" value="$htmlTags[empcode]" title="員工編號" readonly></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="year" class="form-label">年度(西元年)：</label></div>
                        <div class="col-sm-4"><input type="text" class="form-control" style="height: 1.6cm;" id="year" name="year" value="$htmlTags[year]" title="請輸入年度" required></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="begindate" class="form-label">外出啟始日(必填)：</label></div>
                        <div class="col-sm"><input type="datetime-local" class="form-control" style="height: 1.6cm;" id="begindate" name="begindate" value="$htmlTags[begindate]" title="請輸入外出啟始日" required></div>
                        <div class="col-sm-2 fw-bolder"><label for="enddate" class="form-label">外出截止日(必填)：</label></div>
                        <div class="col-sm"><input type="datetime-local" class="form-control" style="height: 1.6cm;" id="enddate" name="enddate" value="$htmlTags[enddate]" title="請輸入外出截止日" required></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="egrersn" class="form-label">外出事由(必填)：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="egrersn" name="egrersn" value="$htmlTags[egrersn]" title="請輸入外出事由" placeholder="請輸入外出事由" required></div>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="row">
                        <div class="col-sm text-start fw-bolder">
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center my-3">
                <input type="submit" class="col-sm-1 btn btn-primary" name="submit" value="送出">&nbsp;&nbsp;<input type="submit" class="col-sm-1 btn btn-outline-primary" name="submit" value="暫存">&nbsp;&nbsp;<input type="button" class="col-sm-1 btn btn-outline-primary" id="cancel" name="cancel" value="關閉">
            </div>
            <div class="gy-5">&nbsp;</div>

    </main>    
    
    </form>
    </div>
</body>
</html>
_html;

?>