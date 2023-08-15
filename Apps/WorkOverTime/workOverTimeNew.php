<?php

    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_pms.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '加班管理', '建立')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有加班資料的建立權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(INDEXPAGE); //返回首頁
        exit();
    }

    //Begin
    //End

echo <<<_html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>建立員工加班資料</title>
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
                alert(msg);
            });

            //設定form表單的submit type欄位 Click事件
            $(":submit").click(function(){
                if($(this).val() == "登出"){
                    $("#form1").attr("action", "../../Public/login.php"); //設定form表單的action屬性
                    msg = "你已經登出系統";
                    btn = "登出";
                }else if($(this).val() == "確定"){
                    msg = "資料已建立成功";
                    btn = "確定";
                }
            });
        });
    </script>
</head>
<body>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>

    <form action="workOverTimeNew.php" method="post" id="form1" name="form1">
    <!--  header區塊  -->
     <header>
        <div class="d-flex flex-row text-white" style="background-color: #3E7050;">
            <h1 class="col-4 me-auto"><img src="../../Images/Banners/logo.png" width="100" height="120" alt="立穎健康照護" style="vertical-align: middle;">立穎健康照護</h1>
            <h6 class="col-auto text-end">使用者：{$_SESSION['login_emp']['empapl']} / 帳號：{$_SESSION['login_emp']['empcode']} / 登入日期：$_SESSION[login_time] &nbsp;&nbsp;<input type="button" class="btn btn-outline-light" value="登出"></h6>
        </div>        
    </header>
_html;

    include_once "../../Require/navigation.php"; //Nav區塊 下拉選單(路徑大小寫有區分)

    include_once "workOverTimeNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
    <!-- main區塊 -->
    <main>
        <div class="container-fluid">
            <h4 class="text-secondary text-decoration-underline my-3"><b>建立員工加班資料</b></h4>
            <div class="row justify-content-center g-1" style="height: 1.6cm;">
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="dept_name" class="form-label">部門：</label></div>
                <div class="col-2">
                    <select id="dept_name" name="dept_name" class="form-select" aria-label="請輸入部門" style="height: 1.6cm;" title="請輸入部門">
                        <option value="管理部">管理部</option>
                        <option value="行政部">行政部</option>
                        <option value="居護組">居護組</option>
                        <option value="個管組">個管組</option>
                        <option value="督導組">督導組</option>
                        <option value="照服組" selected>照服組</option>
                        <option value="立穎長照-管理部">立穎長照-管理部</option>
                        <option value="立穎居護-管理部">立穎居護-管理部</option>
                        <option value="壹山日照-管理部">壹山日照-管理部</option>
                        <option value="壹山日照-照服組" selected>壹山日照-照服組</option>
                        <option value="八八長照-管理部">壹山日照-管理部</option>
                        <option value="八八長照-照服組">壹山日照-照服組</option>
                    </select>
                </div>
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="emp" class="form-label">加班員工：</label></div>
                <div class="col-2">
                    <select id="emp" name="emp" class="form-select" aria-label="請輸入員工" style="height: 1.6cm;" title="請輸入員工">
                        <option value="A001">周惠貞</option>
                        <option value="A002">魏玉琴</option>
                        <option value="A003">李昱慧</option>
                        <option value="A006">劉燕樺</option>
                        <option value="A008">戴文芬</option>
                        <option value="A009">劉燕琦</option>
                        <option value="A012">鍾玉華</option>
                        <option value="A018">馬雲莉</option>
                        <option value="A020">徐培文</option>
                        <option value="A021">廖夏君</option>
                        <option value="A022">黃興文</option>
                        <option value="D001">劉思妤</option>
                        <option value="D002">陳金月</option>
                        <option value="D011">莊芳薇</option>
                        <option value="D012">楊家碩</option>
                        <option value="D013">錢經明</option>
                        <option value="D014">宋雨彤</option>
                        <option value="E001">羅昱甯</option>
                        <option value="E005">劉思妤</option>
                        <option value="E006">林玉惠</option>
                        <option value="E008">陳庭萱</option>
                        <option value="E009" selected>林雅婷</option>
                    </select>
                </div>
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="work_class" class="form-label">加班類別：</label></div>
                <div class="col-2">
                    <div class="form-check form-check-inline">
                        <input id="work_class_01" class="form-check-input" type="radio" name="education" value="補休">
                        <label for="work_class_01" class="form-check-label">補休</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input id="work_class_02" class="form-check-input" type="radio" name="education" value="加班費" checked>
                        <label for="work_class_02" class="form-check-label">加班費</label>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center g-1" style="height: 1.6cm;">
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="begin_date" class="form-label">加班啟始日：</label></div>
                <div class="col-2"><input type="datetime-local" class="form-control" style="height: 1.6cm;" name="begin_date" id="begin_date" title="請輸入加班啟始日"></div>
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="end_date" class="form-label">加班截止日：</label></div>
                <div class="col-2"><input type="datetime-local" class="form-control" style="height: 1.6cm;" name="end_date" id="end_date" title="請輸入加班截止日"></div>
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="work_hours" class="form-label">加班時數：</label></div>
                <div class="col-2"><input type="number" class="form-control" style="height: 1.6cm;" name="work_hours" id="work_hours"></div>
            </div>
            <div class="row justify-content-center g-1" style="height: 1.6cm;">
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="reason" class="form-label">加班事由：</label></div>
                <div class="col-8"><input type="text" class="form-control" style="height: 1.6cm;" name="reason" id="reason" title="請輸入加班事由" placeholder="請輸入加班事由"></div>
            </div>
            <div class="row justify-content-center g-1 mt-0" style="height: 1.6cm;">
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"><label for="examine_state" class="form-label">審核狀態：</label></div>
                <div class="col-2">
                    <select id="agent" name="agent" class="form-select" aria-label="請輸入審核狀態" style="height: 1.6cm;" title="請輸入審核狀態">
                        <option value="15" selected>員工-申請中</option>
                        <option value="16">員工-已呈核</option>
                        <option value="14">審核者-已退回</option>
                    </select>
                </div>
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"></div>
                <div class="col-2"></div>
                <div class="col-1 text-end fw-bolder" style="background-color: #ECECEC;"></div>
                <div class="col-2"></div>
            </div>
            <div class="row justify-content-center my-3">
                <input type="submit" value="確定" class="col-1 btn btn-primary">&nbsp;&nbsp;<input type="reset" value="取消" class="col-1 btn btn-outline-primary">
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