<?php

    //Require_once
    require_once "../../models//common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_employees.php";
    
    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '加班管理', '查詢')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有加班資料的查詢權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(INDEXPAGE); //返回首頁
        exit();
    }
    $obj_employees = new cls_employees;
    
    $SQL = "";
    $htmlQryResult = ""; //顯示查詢結果HTML Tag
    $htmlPaging = ""; //顯示查詢分頁HTML Tag
    $arrQryFld = ['empapl'=>"", 'empidno'=>"", 'mobilephone'=>""]; //儲存淨化查詢條件
    $strStsMsg = ""; //儲存狀態欄訊息
    
    //Begin
    if(isset($_POST['query'])){ //按下"查詢"按鈕的處理動作
        $arrQryFld = $obj_form->inputChk($_POST); //淨化查詢條件
        
        $obj_employees->SQLSelect .= ", year(CURRENT_DATE()) - year(takeofcdate) AS 'seniority' ";
        $obj_employees->SQLWhere .= " AND formstate = 15 ";
        $obj_employees->SQLWhere .= isset($arrQryFld['empapl']) && mb_strlen($arrQryFld['empapl']) > 0 ? " AND empapl LIKE '%{$arrQryFld['empapl']}%' " : ""; //員工姓名欄
        $obj_employees->SQLWhere .= isset($arrQryFld['empidno']) && mb_strlen($arrQryFld['empidno']) > 0 ? " AND empidno LIKE '%{$arrQryFld['empidno']}%' " : ""; //身份證字號欄
        $obj_employees->SQLWhere .= isset($arrQryFld['mobilephone']) && mb_strlen($arrQryFld['mobilephone']) > 0 ? " AND mobilephone LIKE '%{$arrQryFld['mobilephone']}%' " : ""; //手機號碼欄
        $obj_employees->SQLOrderBy .= " formcode DESC ";

        //統計分頁訊息
        $obj_employees->calTotalPages($obj_employees->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_employees->SQLFrom.$obj_employees->SQLWhere));
        //$htmlPaging = $obj_form->viewPaging($obj_employees->int_total_records, $obj_employees->int_total_pages, $obj_employees->int_current_page); //顯示查詢分頁HTML Tag

        //傳回查詢結果
        $obj_employees->SQLlimit = " LIMIT $obj_employees->intStartPos,  $obj_employees->int_records_per_page";
        $obj_employees->SQL = $obj_employees->SQLSelect.$obj_employees->SQLFrom.$obj_employees->SQLWhere.$obj_employees->SQLOrderBy.$obj_employees->SQLlimit;
        //$htmlQryResult = $obj_employees->viewQry($obj_employees->rtnQryResults($obj_employees->SQL));

        //Save Session
        $_SESSION['arrQryFld'] = $arrQryFld;
        $_SESSION['SQL']['Select'] = $obj_employees->SQLSelect;
        $_SESSION['SQL']['From'] = $obj_employees->SQLFrom;
        $_SESSION['SQL']['Where'] = $obj_employees->SQLWhere;
        $_SESSION['SQL']['OrderBy'] = $obj_employees->SQLOrderBy;
        $_SESSION['SQL']['CurPage'] = $obj_employees->int_current_page; //儲存目前頁數
    }elseif (isset($_POST['edit'])) { //按下"編輯"按鈕的處理動作
        $_SESSION['selFormCode'] = $_POST['selFormCode']; //儲存目前編輯記錄
        header("location: empEdit.php");
        exit();
    }elseif (isset($_POST['paging']) || isset($_POST['discard']) || (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel"))) { //執行"分頁/編輯完成/取消編輯"功能後的處理動作
        if (isset($_POST['discard'])) { //按下"註銷"按鈕的處理動作
            if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '請假管理', '註銷')) { //檢查使用者是否有使用權限
                $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有請假資料的註銷權限，如需該功能的使用權限，請與管理者聯絡");
                $obj_form->js_goURL(INDEXPAGE); //返回首頁
                exit();
            }
            $obj_employees->discard($_POST['selFormCode']); //註銷記錄
        }
        
        //取得分頁條件
        $arrQryFld = $_SESSION['arrQryFld'];
        $obj_employees->SQLSelect = $_SESSION['SQL']['Select'];
        $obj_employees->SQLFrom = $_SESSION['SQL']['From'];
        $obj_employees->SQLWhere = $_SESSION['SQL']['Where'];
        $obj_employees->SQLOrderBy = $_SESSION['SQL']['OrderBy'];

        //統計分頁訊息
        $obj_employees->calTotalPages($obj_employees->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_employees->SQLFrom.$obj_employees->SQLWhere));
        
        //計算目前頁數
        if (isset($_POST['paging'])) { //分頁
            $obj_employees->int_current_page = $_POST['CurPage'];
            switch ($_POST['paging']) {
                case '|< 第一頁':
                    $obj_employees->int_current_page = 1;
                    break;
                case '<< 上一頁':
                    $obj_employees->int_current_page--;
                    break;
                case '下一頁 >>':
                    $obj_employees->int_current_page++;
                    break;
                case '最後頁 >|':
                    $obj_employees->int_current_page = $obj_employees->int_total_pages;
                    break;
                default:
                    # code...
                    break;
            }
            $_SESSION['SQL']['CurPage'] = $obj_employees->int_current_page; //儲存目前頁數
        } elseif (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel")) { //編輯 or 取消編輯
            $obj_employees->int_current_page = $_SESSION['SQL']['CurPage']; //取得目前頁數
        }
        
        $htmlPaging = $obj_form->viewPaging($obj_employees->int_total_records, $obj_employees->int_total_pages, $obj_employees->int_current_page); //顯示查詢分頁HTML Tag

        //傳回查詢結果
        $obj_employees->intStartPos = ($obj_employees->int_current_page - 1) * $obj_employees->int_records_per_page;
        $obj_employees->SQLlimit = " LIMIT $obj_employees->intStartPos,  $obj_employees->int_records_per_page";
        $obj_employees->SQL = $obj_employees->SQLSelect.$obj_employees->SQLFrom.$obj_employees->SQLWhere.$obj_employees->SQLOrderBy.$obj_employees->SQLlimit;
        $htmlQryResult = $obj_employees->viewQry($obj_employees->rtnQryResults($obj_employees->SQL));
    };

    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    } elseif (isset($_POST['query']) || isset($_POST['paging']) || isset($_POST['discard'])) { //顯示完成訊息
        $strStsMsg = "資料查詢完成, 總筆數：$obj_employees->int_total_records";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_employees = null;
    $obj_form = null;
    //End
    

echo <<<_html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查詢員工加班資料</title>
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
                if($(this).val() == "編輯"){
                    $("#form1").attr("action", "workOverTimeEdit.php"); //設定form表單的action屬性
                    msg = "開始編輯";
                    btn = "編輯";
                }else if($(this).val() == "註銷"){
                    msg = "該筆資料己經註銷";
                    btn = "註銷";
                }else if($(this).val() == "查詢"){
                    msg = "查詢成功";
                    btn = "查詢";
                }else if($(this).val() == "登出"){
                    $("#form1").attr("action", "../../Public/login.php"); //設定form表單的action屬性
                    msg = "你已經登出系統";
                    btn = "登出";
                }

                //var action = $("#edit01").attr("id"); //設定id=edit01的 id屬性
                /*
                $("#action").attr("value", $(this).attr("id")); //設定action欄位
                $("#form1").attr("action", "empEdit.php"); //設定form表單的action屬性
                var action = $("#action").attr("value"); //設定action欄位
                var form_action = $("#form1").attr("action"); //form表單的action屬性
                alert("執行動作(action欄位)：" + action + " / form表單的action屬性：" + form_action);
                */
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

    include_once "workOverTimeNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
    <!-- main區塊 -->
    <main>
        <div class="container-fluid">
            <h4 class="text-secondary text-decoration-underline my-3"><b>查詢員工加班資料</b></h4>
            <div class="row justify-content-center mt-3">
                <input id="action" type="hidden" name="acttion" value="">
                <input type="hidden" name="編輯筆數" value="01">
                <div class="row">
                    <div class="col">
                        <label for="dept_name" class="form-label">部門：</label>
                        <select id="dept_name" name="dept_name" class="form-select" aria-label="請輸入部門" title="請輸入部門">
                            <option value=""></option>
                            <option value="管理部">管理部</option>
                            <option value="行政部">行政部</option>
                            <option value="居護組">居護組</option>
                            <option value="個管組">個管組</option>
                            <option value="督導組">督導組</option>
                            <option value="照服組">照服組</option>
                            <option value="立穎長照-管理部">立穎長照-管理部</option>
                            <option value="立穎居護-管理部">立穎居護-管理部</option>
                            <option value="壹山日照-管理部">壹山日照-管理部</option>
                            <option value="壹山日照-照服組">壹山日照-照服組</option>
                            <option value="八八長照-管理部">壹山日照-管理部</option>
                            <option value="八八長照-照服組">壹山日照-照服組</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="emp_name" class="form-label">員工姓名：</label>
                        <input id="emp_name" class="form-control" type="text" name="emp_name" placeholder="請輸入員工姓名" title="請輸入員工姓名" autofocus>
                    </div>
                    <div class="col">
                        <label for="idno" class="form-label">身份證字號：</label>
                        <input id="idno" class="form-control" type="text" name="idno" placeholder="請輸入身份證字號" title="請輸入身份證字號">
                    </div>
                    <div class="col">
                        <label for="mobile_phone" class="form-label">審核狀態：</label>
                        <select id="agent" name="agent" class="form-select" aria-label="請輸入審核狀態" title="請輸入審核狀態">
                            <option value="" selected></option>
                            <option value="15">員工-申請中</option>
                            <option value="16">員工-已呈核</option>
                            <option value="14">審核者-已退回</option>
                        </select>
                    </div>
                </div>
                <div class="row  justify-content-center mt-2">
                    <input type="submit" value="查詢" class="col-1 btn btn-primary" id="query" name="query">&nbsp;&nbsp;<input type="reset" value="清除" class="col-1 btn btn-outline-primary">
                </div>
                
                <table class="table caption-top table-striped table-hover my-5">
                    <caption><h4><b>員工加班清單</b></h4></caption>
                    <thead class="">
                        <tr>
                            <th>功能</th><th>審核狀態</th><th>部門</th><th class="text-center">加班員工</th><th class="text-center">加班類別</th><th class="text-center">加班啟始日</th><th class="text-center">加班截止日</th><th class="text-center">加班時數</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" value="編輯" id="edit01" name="edit">
                                <input type="submit" class="btn btn-outline-primary" value="註銷" id="discard01" name="discard">
                            </td>
                            <td class="col-1">員工-申請中</td>
                            <td class="col-2">壹山日照-管理部</td>
                            <td class="col-1 text-center">宋雨彤</td>
                            <td class="col-1 text-center">補休</td>
                            <td class="col-1 text-center">2022/09/12 09:00</td>
                            <td class="col-1 text-center">2022/09/12 17:00</td>
                            <td class="col-1 text-center">8</td>
                        </tr>
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" value="編輯" id="edit02" name="edit">
                                <input type="submit" class="btn btn-outline-primary" value="註銷" id="discard02" name="discard">
                            </td>
                            <td class="col-1">員工-已呈核</td>
                            <td class="col-2">壹山日照-管理部</td>
                            <td class="col-1 text-center">莊芳薇</td>
                            <td class="col-1 text-center">加班費</td>
                            <td class="col-1 text-center">2022/09/13 10:30</td>
                            <td class="col-1 text-center">2022/09/13 16:00</td>
                            <td class="col-1 text-center">4.5</td>
                        </tr>
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" value="編輯" id="edit01" name="edit">
                                <input type="submit" class="btn btn-outline-primary" value="註銷" id="discard01" name="discard">
                            </td>
                            <td class="col-1">員工-申請中</td>
                            <td class="col-2">壹山日照-管理部</td>
                            <td class="col-1 text-center">宋雨彤</td>
                            <td class="col-1 text-center">補休</td>
                            <td class="col-1 text-center">2022/09/12 09:00</td>
                            <td class="col-1 text-center">2022/09/12 17:00</td>
                            <td class="col-1 text-center">4</td>
                        </tr>
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" value="編輯" id="edit02" name="edit">
                                <input type="submit" class="btn btn-outline-primary" value="註銷" id="discard02" name="discard">
                            </td>
                            <td class="col-1">審核者-已核准</td>
                            <td class="col-2">壹山日照-管理部</td>
                            <td class="col-1 text-center">莊芳薇</td>
                            <td class="col-1 text-center">加班費</td>
                            <td class="col-1 text-center">2022/09/13 10:30</td>
                            <td class="col-1 text-center">2022/09/13 16:00</td>
                            <td class="col-1 text-center">6.5</td>
                        </tr>
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" value="編輯" id="edit01" name="edit">
                                <input type="submit" class="btn btn-outline-primary" value="註銷" id="discard01" name="discard">
                            </td>
                            <td class="col-1">審核者-已核准</td>
                            <td class="col-2">壹山日照-管理部</td>
                            <td class="col-1 text-center">宋雨彤</td>
                            <td class="col-1 text-center">補休</td>
                            <td class="col-1 text-center">2022/09/12 09:00</td>
                            <td class="col-1 text-center">2022/09/12 17:00</td>
                            <td class="col-1 text-center">3</td>
                        </tr>
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" value="編輯" id="edit02" name="edit">
                                <input type="submit" class="btn btn-outline-primary" value="註銷" id="discard02" name="discard">
                            </td>
                            <td class="col-1">審核者-已退回</td>
                            <td class="col-2">壹山日照-管理部</td>
                            <td class="col-1 text-center">莊芳薇</td>
                            <td class="col-1 text-center">補休</td>
                            <td class="col-1 text-center">2022/09/13 10:30</td>
                            <td class="col-1 text-center">2022/09/13 16:00</td>
                            <td class="col-1 text-center">2</td>
                        </tr>
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" value="編輯" id="edit01" name="edit">
                                <input type="submit" class="btn btn-outline-primary" value="註銷" id="discard01" name="discard">
                            </td>
                            <td class="col-1">審核者-已核准</td>
                            <td class="col-2">壹山日照-管理部</td>
                            <td class="col-1 text-center">宋雨彤</td>
                            <td class="col-1 text-center">補休</td>
                            <td class="col-1 text-center">2022/09/12 09:00</td>
                            <td class="col-1 text-center">2022/09/12 17:00</td>
                            <td class="col-1 text-center">1</td>
                        </tr>
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" value="編輯" id="edit02" name="edit">
                                <input type="submit" class="btn btn-outline-primary" value="註銷" id="discard02" name="discard">
                            </td>
                            <td class="col-1">審核者-已核准</td>
                            <td class="col-2">壹山日照-管理部</td>
                            <td class="col-1 text-center">莊芳薇</td>
                            <td class="col-1 text-center">補休</td>
                            <td class="col-1 text-center">2022/09/13 10:30</td>
                            <td class="col-1 text-center">2022/09/13 16:00</td>
                            <td class="col-1 text-center">2</td>
                        </tr>
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" value="編輯" id="edit01" name="edit">
                                <input type="submit" class="btn btn-outline-primary" value="註銷" id="discard01" name="discard">
                            </td>
                            <td class="col-1">審核者-已核准</td>
                            <td class="col-2">壹山日照-管理部</td>
                            <td class="col-1 text-center">宋雨彤</td>
                            <td class="col-1 text-center">加班費</td>
                            <td class="col-1 text-center">2022/09/12 09:00</td>
                            <td class="col-1 text-center">2022/09/12 17:00</td>
                            <td class="col-1 text-center">1</td>
                        </tr>
                        <tr>
                            <td class="col-1">
                                <input type="submit" class="btn btn-outline-primary" value="編輯" id="edit02" name="edit">
                                <input type="submit" class="btn btn-outline-primary" value="註銷" id="discard02" name="discard">
                            </td>
                            <td class="col-1">審核者-已退回</td>
                            <td class="col-2">壹山日照-管理部</td>
                            <td class="col-1 text-center">莊芳薇</td>
                            <td class="col-1 text-center">加班費</td>
                            <td class="col-1 text-center">2022/09/13 10:30</td>
                            <td class="col-1 text-center">2022/09/13 16:00</td>
                            <td class="col-1 text-center">3</td>
                        </tr>
                    </tbody>
                </table>
                
                <nav aria-label="Page navigation">
                    <ul class="pagination d-flex flex-row justify-content-center text-center">
                        <li class="page-item col-3" aria-current="page">
                            <div class="page-link sr-only">總頁數：20 / 目前頁數：3 / 總筆數：20</div>
                        </li>
                        <li class="page-item col-1">
                            <a class="page-link" href="#">第一頁</a>
                        </li>
                        <li class="page-item col-1">
                            <a class="page-link" href="#">下一頁</a>
                        </li>
                        <li class="page-item col-1">
                            <a class="page-link" href="#">上一頁</a>
                        </li>
                        <li class="page-item col-1">
                            <a class="page-link" href="#">最後頁</a>
                        </li>
                    </ul>
                </nav>
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