<?php
    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_employees.php";
    require_once "../../models/cls_holidays.php";
    
    //變數初始化
    $obj_form = new cls_form;
    // $obj_pms = new cls_pms; //權限檔
    // if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '請假管理', '查詢')) { //檢查使用者是否有使用權限
    //     $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有請假資料的查詢權限，如需該功能的使用權限，請與管理者聯絡");
    //     $obj_form->js_goURL(INDEXPAGE); //返回首頁
    //     exit();
    // }
    $obj_emp = new cls_employees; //員工檔
    $obj_holiday = new cls_holidays; //請假檔

    $htmlTags = array(); //Render HTML
    $tbl = array(); //儲存不同的參考檔

    //Begin
    $tbl['emp'] = $obj_emp->getRecdByFormcode($_SESSION['login_emp']['formcode']); //登入者的員工記錄

    $htmlTags['year'] = date("Y", time()); // 西元年
    // 請假申請統計
    $htmlTags['HldsAplyTotal'] = $obj_holiday->sttByHldApply('合計', $tbl['emp']['formcode'], date("Y", time())); // 請假申請統計(合計)
    $htmlTags['HldsAplyUnsigned'] = $obj_holiday->sttByHldApply('簽核中', $tbl['emp']['formcode'], date("Y", time())); // 請假申請統計(簽核中)
    $htmlTags['HldsAplySigned'] = $obj_holiday->sttByHldApply('已簽核', $tbl['emp']['formcode'], date("Y", time())); // 請假申請統計(已簽核)

    // 請假審核統計
    $obj_holiday->SQLSelect = "SELECT COUNT(*) ";
    $obj_holiday->SQLWhere .= " AND formstate = 15 AND ((pryformcode = '{$tbl['emp']['formcode']}' AND frmformcode = '2023010004')  "; // 代理人 + 送出
    $obj_holiday->SQLWhere .= " OR (mngrformcode = '{$tbl['emp']['formcode']}' AND emprolepk = '2023010012' AND frmformcode = '2023010006') "; // 單位主管 + 申請者(員工身份) + 代理人已簽核
    $obj_holiday->SQLWhere .= " OR (cifformcode = '{$tbl['emp']['formcode']}' AND ((emprolepk = '2023010012' AND frmformcode = '2023010009') OR (emprolepk = '2023010013' AND frmformcode = '2023010006')))) "; // 主任 + (申請者(員工身份)+單位主管已簽核 OR 申請者(單位主管身份)+代理人已簽核)
    $obj_holiday->SQLWhere .= " AND year = '$htmlTags[year]' "; //年度
    $obj_holiday->SQL = $obj_holiday->SQLSelect.$obj_holiday->SQLFrom.$obj_holiday->SQLWhere;
    $htmlTags['HldChkUnSign'] = $obj_holiday->rtnQryField($obj_holiday->SQL);// 請假審核統計(未簽核)
    //End

echo <<<_html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>首頁</title>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">

    <!-- JQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
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
                if($(this).val() == "登出"){
                    // Code ...
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

echo <<<_html
    <!-- main區塊 -->
    <main>
        <div class="container-fluid">
            <!--
            <div class="row my-4">
                <button type="button" class="col btn btn-primary mx-1">Notifications <span class="badge bg-secondary">4</span></button>
                <button type="button" class="col btn btn-primary mx-1">Notifications <span class="badge bg-secondary">5</span></button>
                <button type="button" class="col btn btn-primary mx-1">Notifications <span class="badge bg-secondary">6</span></button>
                <button type="button" class="col btn btn-primary mx-1">Notifications <span class="badge bg-secondary">7</span></button>
            </div>
            -->
            <div class="row my-2"></div>
            <div class="row row-cols-4 row-cols-md-4 gy-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body btn-primary">
                            <h5 class="card-title fw-bold"><a class="text-white" href="../holidays/holidaysQuery.php" target="_self">請假申請統計</a>($htmlTags[year]年)</h5>
                            <div>合計：$htmlTags[HldsAplyTotal]</div>
                            <div>簽核中：$htmlTags[HldsAplyUnsigned]</div>
                            <div>已簽核：$htmlTags[HldsAplySigned]</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                    <div class="card-body btn-secondary">
                        <h5 class="card-title fw-bold"><a class="text-white" href="../holidays/holidaysExamine.php" target="_self">請假審核統計</a>($htmlTags[year]年)</h5>
                        <div>未簽核：$htmlTags[HldChkUnSign]</div>
                        <div>&nbsp;</div>
                        <div>&nbsp;</div>
                    </div>
                    </div>
                </div>
                <!--
                <div class="col">
                    <div class="card">
                    <div class="card-body btn-success">
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content.</p>
                    </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                    <div class="card-body btn-warning">
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                    </div>
                    </div>
                </div>
                -->
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