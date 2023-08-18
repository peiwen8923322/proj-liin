<?php
use Illuminate\Support\Arr;
    session_start();
    unset($_SESSION['login_time']); // 清除目前的登入日期時間
    unset($_SESSION['login_emp']); // 清除儲存登入者資訊
    date_default_timezone_set('Asia/Taipei'); //設定時區
    define("INDEXPAGE", "../Apps/index/index.php");

    //Require_once
    require_once "../models/cls_form.php";
    require_once "../models/cls_models.php";
    require_once "../models/cls_employees.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_emp = new cls_employees;
    $arrNewFormVal = array('vcode'=>''); //儲存淨化後的建立內容

    //Begin
    if (isset($_POST['submit']) && $_POST['submit'] == '登入') {
        $arrNewFormVal = $obj_form->inputChk($_POST); //淨化查詢條件

        if (!($_SESSION['check_word'] == $arrNewFormVal['vcode'])) {
            $obj_form->js_alert("圖形驗證碼錯誤, 請重新輸入");
        } else {
            if ($obj_emp->isExistEmp($arrNewFormVal)) { // 檢查登入者的帳號/密碼是否正確
                $_SESSION['login_time'] = date("Y-m-d H:i:s", time()); // 目前的登入日期時間
                $_SESSION['login_emp'] = $obj_emp->getEmp($arrNewFormVal['account']); // 儲存登入者資訊

                header("location: ".INDEXPAGE);
                exit;
            }else {
                $obj_form->js_alert("帳號或密碼錯誤, 請重新輸入");
            }
        }
        
    }
    //End
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>立穎健康照護管理系統</title>
    <!-- meta使用viewport以确保页面可自由缩放 -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- 引入 jQuery Mobile 样式 -->
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
    
    <!-- 引入 jQuery 库 -->
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>

    <!-- 引入 jQuery Mobile 库 -->
    <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

    <script>
        var msg = ""; //訊息欄
        var btn = ""; //使用者按下按鈕

        $(document).ready(function(){
            //設定表單 Submit事件
            $("#form1").submit(function(){
                //alert(msg);
            });

            //設定form表單的submit type欄位 Click事件
            $(":submit").click(function(){
                /*
                if($(this).val() == "確定"){
                    msg = "資料已建立成功";
                    btn = "確定";
                }
                 */
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

        function refresh_code(){ // 更新驗證碼圖片
            document.getElementById("imgcode").src="imgcode.php"; 
        }

    </script>
</head>
<body>

    <div data-role="page" id="pageone">
        <div data-role="header">
            <h4 class="fw-bold text-center">立穎健康照護管理系統</h4>
        </div>
        
        <div data-role="content">
            
        <h4 class="fw-bold text-center">立穎健康照護管理系統</h4>
                <form method="post" action="" id="form1" name="form1">
                    <div class="container-fluid">
                        <div class="row justify-content-center my-3">
                            <div class="col-3">
                                <label for="account" class="form-label"><h5><b>帳號：</b></h5></label>
                                <input type="text" class="form-control" id="account" name="account" placeholder="請輸入帳號" title="請輸入帳號" autofocus required>
                            </div>
                        </div>
                        <div class="row justify-content-center my-3">
                            <div class="col-3">
                                <label for="passwd" class="form-label"><h5><b>密碼：</b></h5></label>
                                <input type="password" class="form-control" id="passwd" name="passwd" placeholder="請輸入密碼" title="請輸入密碼" required>
                            </div>
                        </div>
                        <div class="row justify-content-center my-3">
                            <div class="col-3">
                                <div><h5><b>圖形驗證碼：<img id="imgcode" src="imgcode.php" onclick="refresh_code()" /></b></h5>(按下圖片可以更新驗證碼)</div>
                                <div><input type="text" class="form-control form-control-user reg_vcode" id="vcode" name="vcode" value="" placeholder="輸入上方圖形驗證碼" required></div>
                            </div>
                        </div>
                        
                        <div class="row justify-content-center my-3">
                            <input type="submit" class="col-1 btn btn-primary me-3" id="submit" name="submit" value="登入">
                            <input type="reset" class="col-1 btn btn-outline-primary" id="reset" value="清除">
                        </div>
                    </div>
                </form>
                
            <!-- main區塊 -->
            <main>
                
            </main>


        </div>
        
        <div data-role="footer">
            <h1>頁面底部內容</h1>
        </div>
    </div>

</body>
</html>