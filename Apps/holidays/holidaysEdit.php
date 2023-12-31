<?php

    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_holidays.php";
    require_once "../../models/cls_field_lists.php";
    require_once "../../models/cls_employees.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPmsByEmpformcode($_SESSION['login_emp']['formcode'], '請假管理', '編輯')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有請假管理的編輯權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(MOBILEINDEXPAGE); //返回首頁
        exit();
    }
    
    $obj_holiday = new cls_holidays; //請假檔
    $obj_field_lists = new cls_field_lists; //欄位清單檔
    $obj_emp = new cls_employees; //員工檔

    $arrCurRecord = array(); //儲存編輯記錄
    $htmlTags = array(); //Render HTML(二維關聯陣列)
    $tbl = array(); //儲存不同的參考檔(二維關聯陣列)
    $arrHldsStt = array(); // 假別統計(二維關聯陣列)
    $arrNewFormVal = ""; //儲存淨化後的建立內容
    $qryCondition = ""; //參考其他Table的SQL WHERE條件
    $strStsMsg = ""; //儲存狀態欄訊息
    $dbl_hld2022100101_ttlhrs = 0; // 換休(補休)總時數

    //Begin
    $tbl['emp'] = $obj_emp->getRecdByFormcode($_SESSION['login_emp']['formcode']); //員工檔
    $tbl['year'] = $nowYear = date("Y", time()); // 目前西元年
    // 假別統計
    // 病假 / 事假 / 公假 / 特休假 / 婚假 / 喪假 / 家庭照顧假 / 生理假 / 陪產假 / 產檢假 / 產假 / 換休 / 公偒病假 / 其他
    $htmlTags['html_hld2022100089'] = $htmlTags['html_hld2022100090'] = $htmlTags['html_hld2022100091'] = $htmlTags['html_hld2022100092'] = $htmlTags['html_hld2022100093'] = $htmlTags['html_hld2022100094'] = $htmlTags['html_hld2022100095'] = $htmlTags['html_hld2022100096'] = $htmlTags['html_hld2022100097'] = $htmlTags['html_hld2022100098'] = $htmlTags['html_hld2022100099'] = $htmlTags['html_hld2022100101'] = $htmlTags['html_hld2023010024'] = $htmlTags['html_hld2022100100'] = '';
    $arrHldsStt = $obj_holiday->sttHldsByEmpAndYear($nowYear, $tbl);
    if (count($arrHldsStt) > 0) {
        // var_dump($arrHldsStt);
        foreach ($arrHldsStt as $value) {
            switch ($value['hldformcode']) {
                case '2022100089': // 病假
                    $htmlTags['html_hld2022100089'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100090': // 事假
                    $htmlTags['html_hld2022100090'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100091': // 公假
                    $htmlTags['html_hld2022100091'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100092': // 特休假
                    $htmlTags['html_hld2022100092'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100093': // 婚假
                    $htmlTags['html_hld2022100093'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100094': // 喪假
                    $htmlTags['html_hld2022100094'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100095': // 家庭照顧假
                    $htmlTags['html_hld2022100095'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100096': // 生理假
                    $htmlTags['html_hld2022100096'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100097': // 陪產假
                    $htmlTags['html_hld2022100097'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100098': // 產檢假
                    $htmlTags['html_hld2022100098'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100099': // 產假
                    $htmlTags['html_hld2022100099'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100101': // 換休(補休)
                    $htmlTags['html_hld2022100101'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    $dbl_hld2022100101_ttlhrs = $value['sum_ttlhrs']; // 換休(補休)總時數
                    break;
                case '2023010024': // 公偒病假
                    $htmlTags['html_hld2023010024'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                case '2022100100': // 其他
                    $htmlTags['html_hld2022100100'] = "$value[sum_hldsdays] 天 $value[sum_hldshrs] 小時";
                    break;
                default:
                    # code...
                    break;
            }
        }
    }

    // 取得目前的編輯記錄
    $arrQryFld = $_SESSION['arrQryFld'];
    $obj_holiday->SQLSelect = "SELECT * ";
    $obj_holiday->SQLFrom = $_SESSION['SQL']['From'];
    $obj_holiday->SQLWhere = " WHERE 1 AND formcode = '$_SESSION[selFormCode]'";
    $obj_holiday->SQL = $obj_holiday->SQLSelect.$obj_holiday->SQLFrom.$obj_holiday->SQLWhere;
    $arrCurRecord = $obj_holiday->rtnQryRecord($obj_holiday->SQL); // 取得目前的編輯記錄

    $htmlTags['workOverTime_hours'] = $obj_holiday->calWorkOverTimeHrs($tbl); //加班總時數
    $htmlTags['workOverTime_rsvhours'] = ((float)$htmlTags['workOverTime_hours']) - $dbl_hld2022100101_ttlhrs; // 加班剩餘時數

    if (isset($_POST['submit'])) { //按下"送出 / 暫存"按鈕的處理動作
        $arrNewFormVal = $obj_form->inputChk($_POST); //淨化查詢條件
        $arrNewFormVal['formcode'] = $_SESSION['selFormCode']; //該筆記錄的表單編號
        $arrNewFormVal['modifier'] = $_SESSION['login_emp']['empapl'];//修改者
        $arrNewFormVal['atmname'] = $arrCurRecord['atmname']; // 附件檔案名稱
        $arrNewFormVal['atmtype'] = $arrCurRecord['atmtype']; // 附件檔案型態
        $arrNewFormVal['atmsize'] = $arrCurRecord['atmsize']; // 附件檔案大小
        $arrNewFormVal['ttlhrs'] = $arrNewFormVal['hldsdays'] * 8 + $arrNewFormVal['hldshrs']; // 當天總時數

        //參考其他Table
        $tbl['aftrest'] = $obj_field_lists->getRcrdByFormcode($arrNewFormVal['aftrest']); // 中午是否休息
        $tbl['hlds'] = $obj_field_lists->getRcrdByFormcode($arrNewFormVal['hldformcode']); //假別
        $tbl['frmvry'] = ($_POST['submit'] == '送出') ? $obj_field_lists->getRcrdByFormcode('2023010004') : $obj_field_lists->getRcrdByFormcode('2023010003') ; //審核狀態 ("送出 / 暫存")
        $tbl['proxy'] = $obj_emp->getRecdByFormcode($arrNewFormVal['pryformcode']); // 代理人

        // 加班時數 < 換休(補休)，換休(補休)請假失敗
        if ($arrNewFormVal['hldformcode'] == '2022100101' && $htmlTags['workOverTime_hours'] < ($arrNewFormVal['hldsdays']*8 + $arrNewFormVal['hldshrs'])) {
            $_SESSION['error']['errMsg'] = "加班時數 < 換休(補休)時數，換休(補休)請假失敗，請選擇其他假別";  // 加班時數 < 換休(補休)，換休(補休)請假失敗
        }

        if (isset($_FILES["file"]["name"]) && strlen($_FILES["file"]["name"]) > 0) { // 上傳附件
        
            if ($_FILES["file"]["error"] > 0) { // 上傳附件發生錯誤，請聯絡系統管理者
                $_SESSION['error']['errMsg'] = "上傳附件發生錯誤，請聯絡系統管理者({$_FILES["file"]["error"]})";
                $htmlTags['atm_img'] = ""; // 附件影像
            } else if (!(($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png"))) { // 檔案格式錯誤
                $_SESSION['error']['errMsg'] = "檔案格式錯誤，請重新輸入圖檔格式(GIF/JPEG/PNG)";
                $htmlTags['atm_img'] = ""; // 附件影像
            } else if ($_FILES["file"]["size"] >= 3000000) { // 檔案太大(檔案大小限制在 3M以下)
                $_SESSION['error']['errMsg'] = "檔案太大(檔案大小限制在 3M以下)，請重新輸入附件";
                $htmlTags['atm_img'] = ""; // 附件影像
            } else { // 複製檔案
        
                // echo "Before Upload Name：" . $_FILES["file"]["name"] . "<br />";
                switch ($_FILES["file"]["type"]) {
                    case 'image/gif':
                        $_FILES["file"]["name"] = "$nowYear{$tbl['emp']['empcode']}$arrNewFormVal[formcode].gif";
                        break;
                    case 'image/jpeg':
                        $_FILES["file"]["name"] = "$nowYear{$tbl['emp']['empcode']}$arrNewFormVal[formcode].jpeg";
                        break;
                    case 'image/png':
                        $_FILES["file"]["name"] = "$nowYear{$tbl['emp']['empcode']}$arrNewFormVal[formcode].png";
                        break;
                    default:
                        # code...
                        break;
                }
                
                // echo "After Upload Name：" . $_FILES["file"]["name"] . "<br />";
                // echo "Type：" . $_FILES["file"]["type"] . "<br />";
                // echo "Size：" . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
                // echo "Temp file：" . $_FILES["file"]["tmp_name"] . "<br />";
        
                // if (file_exists("upload/" . $_FILES["file"]["name"])) {
                //     $_SESSION['error']['errMsg'] = "上傳的附件檔案已經存在，上傳的附件檔案請勿重覆(請查詢後點選'編輯'功能進行附件修改)";
                // } else {
                //     if (strlen($arrCurRecord["atmname"]) > 0) { // 刪除目前的請假表單的附件
                //         unlink("upload/$arrCurRecord[atmname]");
                //     }
                //     move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
                //     $arrNewFormVal['atmname'] = $_FILES["file"]["name"]; // 附件檔案名稱
                //     $arrNewFormVal['atmtype'] = $_FILES["file"]["type"]; // 附件檔案型態
                //     $arrNewFormVal['atmsize'] = ($_FILES["file"]["size"] / 1024); // 附件檔案大小
                // }

                if (strlen($arrCurRecord["atmname"]) > 0 && file_exists("upload/".$_FILES["file"]["name"])) { // 刪除目前的請假表單的附件
                    unlink("upload/$arrCurRecord[atmname]");
                }
                move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
                $arrNewFormVal['atmname'] = $_FILES["file"]["name"]; // 附件檔案名稱
                $arrNewFormVal['atmtype'] = $_FILES["file"]["type"]; // 附件檔案型態
                $arrNewFormVal['atmsize'] = ($_FILES["file"]["size"] / 1024); // 附件檔案大小

                $htmlTags['atm_img'] = "<img class='ratio ratio-4x3' src='upload/{$_FILES["file"]["name"]}' />"; // 附件影像
            }
        
        } else { // 無附件上傳
            if (strlen($arrCurRecord['atmname']) > 0 && file_exists("upload/" . $arrCurRecord['atmname'])) {
                $htmlTags['atm_img'] = "<img class='ratio ratio-4x3' src='upload/$arrCurRecord[atmname]' />"; // 附件影像
            } else {
                $htmlTags['atm_img'] = ""; // 附件影像
            }
        }

        //執行SQL
        if (!(isset($_SESSION['error']))) {
            $obj_holiday->Update($arrNewFormVal, $tbl);
        }
        
        
        //Render HTML
        $htmlTags['html_empcode'] = $_SESSION['login_emp']['empapl']; //請假員工
        $htmlTags['html_year'] = $arrNewFormVal['year']; //年度
        $htmlTags['html_hldformcode'] = $arrNewFormVal['hldformcode']; // 預設假別
        $htmlTags['html_hldscls'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'hldformcode', 'attrName'=>'hldformcode', 'Label'=>'listapl', 'attrValue'=>'formcode', 'default'=>'formcode'), $obj_field_lists->getList('請假'), $arrNewFormVal['hldformcode']); //假別
        $htmlTags['html_hldrsn'] = $arrNewFormVal['hldrsn']; //請假事由
        $htmlTags['html_begindate'] = $arrNewFormVal['begindate']; //請假啟始日
        $htmlTags['html_enddate'] = $arrNewFormVal['enddate']; //請假截止日
        $htmlTags['html_hldsdays'] = $arrNewFormVal['hldsdays']; //請假天數
        $htmlTags['html_hldshrs'] = $arrNewFormVal['hldshrs']; //請假時數
        $htmlTags['aftrest'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'aftrest', 'attrName'=>'aftrest', 'Label'=>'listapl', 'attrValue'=>'formcode', 'default'=>'formcode'), $obj_field_lists->getList('中午是否休息'), $arrNewFormVal['aftrest']); // 中午是否休息
        $htmlTags['html_pryformcode'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'pryformcode', 'attrName'=>'pryformcode', 'attrTitle'=>'代理人', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_emp->getListAtWork(), $arrNewFormVal['pryformcode']); // 代理人('optionTitle'=>'NewEmpapl')
        $htmlTags['html_frmlistapl'] = $_POST['submit']; //審核狀態
    } elseif (isset($_POST['logout'])) { //登出
        //$obj_form->logout();
    } else { //第一次執行表單的處理動作
        //Render HTML
        $htmlTags['html_empcode'] = $_SESSION['login_emp']['empapl']; //請假員工
        // $htmlTags['html_empcode'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'empcode', 'attrName'=>'empcode', 'attrTitle'=>'請選擇請假員工', 'optionTitle'=>'empapl', 'optionValue'=>'empcode'), $obj_emp->getListAtWork(), $_SESSION['login_emp']['empapl']); //請假員工
        $htmlTags['html_year'] = $arrCurRecord['year']; //年度
        $htmlTags['html_hldformcode'] = $arrCurRecord['hldformcode']; // 預設假別
        $htmlTags['html_hldscls'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'hldformcode', 'attrName'=>'hldformcode', 'Label'=>'listapl', 'attrValue'=>'formcode', 'default'=>'formcode'), $obj_field_lists->getList('請假'), $arrCurRecord['hldformcode']); //假別
        $htmlTags['html_hldrsn'] = $arrCurRecord['hldrsn']; //請假事由
        $htmlTags['html_begindate'] = $arrCurRecord['begindate']; //請假啟始日
        $htmlTags['html_enddate'] = $arrCurRecord['enddate']; //請假截止日
        $htmlTags['html_hldsdays'] = $arrCurRecord['hldsdays']; //請假天數
        $htmlTags['html_hldshrs'] = $arrCurRecord['hldshrs']; //請假時數
        $htmlTags['aftrest'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'aftrest', 'attrName'=>'aftrest', 'Label'=>'listapl', 'attrValue'=>'formcode'), $obj_field_lists->getList('中午是否休息'), $arrCurRecord['aftrest']); // 中午是否休息
        $htmlTags['html_pryformcode'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'pryformcode', 'attrName'=>'pryformcode', 'attrTitle'=>'代理人', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_emp->getListAtWork(), $arrCurRecord['pryformcode']); // 代理人('optionTitle'=>'NewEmpapl')
        $htmlTags['html_frmlistapl'] = $arrCurRecord['frmlistapl']; //審核狀態
        $htmlTags['atm_img'] = strlen($arrCurRecord["atmname"]) > 0 && file_exists("upload/".$arrCurRecord["atmname"]) ? "<img class='ratio ratio-4x3' src='upload/{$arrCurRecord['atmname']}' />" : "";

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
    $obj_holiday = null;
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
    <title>編輯員工請假資料</title>
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
                if($("#hldformcode").val() == "2022100101" && $("#workOverTime_rsvhours").text() < ($("#hldsdays").val()*8 + $("#hldshrs").val())) { // 加班剩餘時數 < 當天換休(補休)時數，換休(補休)請假失敗
                    alert("加班剩餘時數 < 換休(補休)時數，當天換休(補休)請假失敗，請選擇其他假別");
                    return false;
                }

                if(!confirm('是否確定送出??')) {
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
                    location.assign("./holidaysQuery.php?action=cancel");
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

                // alert("假別：" + $("#hldformcode").val());
                // alert("加班剩餘時數：" + $("#workOverTime_rsvhours").text());
                // alert("請假天數：" + $("#hldsdays").val()*8);
                // alert("請假時數：" + $("#hldshrs").val());

                //  && $("#workOverTime_rsvhours").text() < ($("#hldsdays").val()*8 + $("#hldshrs").val())
                // if ($("#hldformcode").val() == "2022100101" && ($("#workOverTime_rsvhours").text() < ($("#hldsdays").val()*8 + $("#hldshrs").val()))) {
                //     alert("加班剩餘時數 < 換休(補休)時數，換休(補休)請假失敗");
                // }

                // if ($("#hldformcode").val() == "2022100101" && $("#workOverTime_rsvhours").text() < ($("#hldsdays").val()*8 + $("#hldshrs").val())) {
                //     alert("加班剩餘時數 < 換休(補休)時數，換休(補休)請假失敗");
                // }

                
            })



        });
    </script>
</head>
<body>
    <div class="container-fluid">

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>

    <form action="" method="post" id="form1" name="form1" enctype="multipart/form-data">
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

    include_once "holidaysNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
    <!-- main區塊 -->
    <main>
        <div class="row"><h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5></div>
            <h4 class="text-secondary text-decoration-underline my-3"><b>編輯員工請假資料</b></h4>
            <input type="hidden" id="hldformcode" name="hldformcode" value="$htmlTags[html_hldformcode]">
            <div class="row">
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="empapl" class="form-label">請假員工：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="empapl" name="empapl" value="$htmlTags[html_empcode]" title="請假員工" readonly></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="takeofcdate" class="form-label">到職日：</label></div>
                        <div class="col-sm"><input type="date" class="form-control" style="height: 1.6cm;" id="takeofcdate" name="takeofcdate" value="{$tbl['emp']['takeofcdate']}" title="到職日" readonly></div>
                        <div class="col-sm-2 fw-bolder"><label for="amlhrs" class="form-label">過去未休假的累積時數：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="amlhrs" name="amlhrs" value="{$tbl['emp']['amlhrs']}" title="過去未休假的累積時數" readonly></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="year" class="form-label">年度(西元年)：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="year" name="year" value="$htmlTags[html_year]" title="請輸入年度" required></div>
                        <div class="col-sm-2 fw-bolder"><label for="curhrs" class="form-label">目前可特休天數：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="curhrs" name="curhrs" value="{$tbl['emp']['curhrs']}" title="目前可特休時數" readonly></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="begindate" class="form-label">請假起始日<br/>(必填)：</label></div>
                        <div class="col-sm"><input type="datetime-local" class="form-control" style="height: 1.6cm;" id="begindate" name="begindate" value="$htmlTags[html_begindate]" title="請輸入請假起始日" required></div>
                        <div class="col-sm-2 fw-bolder"><label for="enddate" class="form-label">請假截止日<br/>(必填)：</label></div>
                        <div class="col-sm"><input type="datetime-local" class="form-control" style="height: 1.6cm;" id="enddate" name="enddate" value="$htmlTags[html_enddate]" title="請輸入請假截止日" required></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="hldsdays" class="form-label">請假天數：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="hldsdays" name="hldsdays" value="$htmlTags[html_hldsdays]" title="請輸入請假天數" readonly></div>
                        <div class="col-sm-2 fw-bolder"><label for="hldshrs" class="form-label">請假時數：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="hldshrs" name="hldshrs" value="$htmlTags[html_hldshrs]" title="請輸入請假時數" readonly></div>
                    </div>
                    <div class="row my-3">
                        <div class="col-sm-2 fw-bolder"><label for="aftrest" class="form-label">中午是否休息(1小時)：</label></div>
                        <div class="col-sm">$htmlTags[aftrest]</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="hldformcode0002" class="form-label">假別：</label></div>
                        <div class="col-sm">$htmlTags[html_hldscls] <b>[請假時數說明：換休時數(單位：小時) / 生理假時數(單位：天) / 其他假別(單位：4小時)]</b></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="hldrsn" class="form-label">請假事由(必填)：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="hldrsn" name="hldrsn" value="$htmlTags[html_hldrsn]" title="請輸入請假事由" placeholder="請輸入請假事由" required></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="pryformcode" class="form-label">代理人：</label></div>
                        <div class="col-sm">$htmlTags[html_pryformcode]</div>
                        <div class="col-sm-2 fw-bolder"><label for="frmlistapl" class="form-label">審核狀態：</label></div>
                        <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="frmlistapl" name="frmlistapl" value="$htmlTags[html_frmlistapl]" title="審核狀態" disabled></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder text-danger"><label for="pryformcode" class="form-label">上傳附件：<br/>(上傳檔案格式：GIF/JPEG/PNG，檔案大小限制在 3MB以下)</label></div>
                        <div class="col-sm fw-bolder text-danger"><input type="file" name="file" id="file" /></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 fw-bolder"><label for="frmformcode" class="form-label">附件：</label></div>
                        <div class="col-sm">$htmlTags[atm_img]</div>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="row">
                        <div class="col-sm text-start fw-bolder">
                            假別統計 ($htmlTags[html_year]年)：
                            <div><span>病假：</span><span>$htmlTags[html_hld2022100089]</span></div>
                            <div><span>事假：</span><span>$htmlTags[html_hld2022100090]</span></div>
                            <div><span>公假：</span><span>$htmlTags[html_hld2022100091]</span></div>
                            <div><span>特休假：</span><span>$htmlTags[html_hld2022100092]</span></div>
                            <div><span>婚假：</span><span>$htmlTags[html_hld2022100093]</span></div>
                            <div><span>喪假：</span><span>$htmlTags[html_hld2022100094]</span></div>
                            <div><span>家庭照顧假：</span><span>$htmlTags[html_hld2022100095]</span></div>
                            <div><span>生理假：</span><span>$htmlTags[html_hld2022100096]</span></div>
                            <div><span>陪產假：</span><span>$htmlTags[html_hld2022100097]</span></div>
                            <div><span>產檢假：</span><span>$htmlTags[html_hld2022100098]</span></div>
                            <div><span>產假：</span><span>$htmlTags[html_hld2022100099]</span></div>
                            <div><span>加班總時數：</span><span id="workOverTime_hours">$htmlTags[workOverTime_hours]</span></div>
                            <div><span>加班剩餘時數：</span><span id="workOverTime_rsvhours">$htmlTags[workOverTime_rsvhours]</span></div>
                            <div><span>換休(補休)：</span><span>$htmlTags[html_hld2022100101]</span></div>
                            <div><span>公偒病假：</span><span>$htmlTags[html_hld2023010024]</span></div>
                            <div><span>其他：</span><span>$htmlTags[html_hld2022100100]</span></div>
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