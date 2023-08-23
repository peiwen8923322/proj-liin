<?php
use Illuminate\Support\Arr;
use TheSeer\Tokenizer\Exception;
    
    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_employees.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_emp = new cls_employees;
    
    $htmlTags = array(); //Render HTML
    $tbl = array(); //儲存不同的參考檔
    $arrNewFormVal = ""; //儲存淨化後的建立內容
    $qryCondition = ""; //參考其他Table的SQL WHERE條件
    $strNewSeq = null; //記錄建立成功後, 儲存新記錄序號
    $strStsMsg = ""; //儲存狀態欄訊息
    
    //Begin
    switch (isset($_POST['submit'])) {
        case '確定':
            $arrNewFormVal = $obj_form->inputChk($_POST); //淨化查詢條件
            $arrNewFormVal['account'] = $_SESSION['login_emp']['empcode']; //加入帳號資訊
            //var_dump($arrNewFormVal);

            if (!$obj_emp->isExistEmp($arrNewFormVal)) { //檢查登入者的帳號/密碼是否正確
                $strStsMsg = "舊密碼錯誤, 請重新輸入";
            }elseif (strlen($arrNewFormVal['new_passwd']) < 6) { //檢查新密碼長度 > 6位數
                $strStsMsg = "新密碼太短, 新密碼長度要至少6位數";
            }elseif ($arrNewFormVal['new_passwd'] != $arrNewFormVal['new_passwd2']) { //檢查新密碼是否一致
                $strStsMsg = "新密碼不一致, 請重新輸入";
            }else {
                $obj_emp->chgPassword($arrNewFormVal['new_passwd'], $_SESSION['login_emp']['formcode']); //變更密碼
                $obj_form->js_alert("密碼變更成功，請重新登入");
                $obj_form->js_goURL(WWWROOT);
                exit();
            }
            
            break;
        default:
            //Code ...
            break;
    }

    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        $obj_form->js_alert($strStsMsg);
        unset($_SESSION['error']);
    }elseif (strlen($strStsMsg) > 0) {
        $obj_form->js_alert($strStsMsg);
    }
    //End
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>變更密碼</title>
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
                    /*
                    msg = "密碼變更成功";
                    btn = "確定";
                    alert("密碼變更成功");
                        */
                }
            });

            //設定form表單的button type欄位 Click事件
            $(":button").click(function() {
                if ($(this).val() == "登出") {
                    msg = "你已經登出系統";
                    btn = "登出";
                    location.assign("../../Public/mlogin.php");
                    alert("你已經登出系統");
                }
            });
        });
    </script>
</head>
<body>
    <div class="container-fluid">

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>

    <!--  header區塊  -->
    <header>
        <div class="row text-white" style="background-color: #3E7050;">
            <h1 class="col-sm-4"><img src="../../Images/Banners/logo.png" width="100" height="120" alt="立穎健康照護" style="vertical-align: middle;">立穎健康照護</h1>
        </div>
        <div class="row justify-content-end text-white" style="background-color: #3E7050;">
            <div class="col-sm-auto"><input type="button" class="btn btn-outline-light" value="登出"></div>
            <h6 class="col-sm-auto">使用者：<?php echo $_SESSION['login_emp']['empapl'] ?></h6>
            <h6 class="col-sm-auto">帳號：<?php echo $_SESSION['login_emp']['empcode'] ?></h6>
            <h6 class="col-sm-auto">登入日期：<?php echo $_SESSION['login_time'] ?></h6>
        </div>        
    </header>
    
    <?php include_once "../../Require/mnavigation.php"; //Nav區塊 下拉選單(路徑大小寫有區分) ?>

    <!-- main區塊 -->
    <main>
        <div class="row"><h5 class="alert alert-success text-primary fw-bold">狀態列：<?php echo $strStsMsg ?></h5></div>
        <h4 class="fw-bold text-center">變更密碼</h4>
        <form method="post" action="" id="form1" name="form1">
                <div class="row justify-content-center my-3">
                    <div class="col-sm-3">
                        <label for="old_paword" class="form-label"><h5 class=" fw-bold">舊密碼：</h5></label>
                        <input type="password" class="form-control" id="old_paword" name="passwd" placeholder="請輸入舊密碼" title="請輸入舊密碼" autofocus required>
                    </div>
                </div>
                <div class="row justify-content-center my-3">
                    <div class="col-sm-3">
                        <label for="new_passwd" class="form-label"><h5 class=" fw-bold">新密碼(密碼長度至少6位數以上)：</h5></label>
                        <input type="password" class="form-control" id="new_passwd" name="new_passwd" placeholder="請輸入新密碼(密碼長度至少6位數以上)" title="請輸入新密碼(密碼長度至少6位數以上)" required>
                    </div>
                </div>
                <div class="row justify-content-center my-3">
                    <div class="col-sm-3">
                        <label for="new_passwd2" class="form-label"><h5 class=" fw-bold">再輸入一次新密碼：</h5></label>
                        <input type="password" class="form-control" id="new_passwd2" name="new_passwd2" placeholder="再輸入一次新密碼" title="再輸入一次新密碼" required>
                    </div>
                </div>
                <div class="row justify-content-center my-3">
                    <input type="submit" class="col-sm-1 btn btn-primary me-3" id="login" name="submit" value="確定">
                    <input type="reset" class="col-sm-1 btn btn-outline-primary" id="reset" value="清除">
                </div>
        </form>
    </main>

    </div>
</body>
</html>


