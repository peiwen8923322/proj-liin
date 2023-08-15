<?php

    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_employees.php";
    require_once "../../models/cls_depts.php";
    require_once "../../models/cls_field_lists.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '加班管理', '編輯')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有加班資料的編輯權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(INDEXPAGE); //返回首頁
        exit();
    }
    $obj_employees = new cls_employees; //員工檔
    $obj_depts = new cls_depts; //機構檔
    $obj_fl = new cls_field_lists; //欄位清單檔

    $arrCurRecord = array(); //儲存編輯記錄
    $htmlTags = array(); //Render HTML
    $tbl = array(); //儲存不同的參考檔
    $arrNewFormVal = ""; //儲存淨化後的建立內容
    $qryCondition = ""; //參考其他Table的SQL WHERE條件
    $strStsMsg = ""; //儲存狀態欄訊息


    //Begin
    if (isset($_POST['submit']) && $_POST['submit'] == "確定") {
        /*
        $arrNewFormVal = $obj_form->inputChk($_POST); //淨化查詢條件
        $arrNewFormVal['formcode'] = $_SESSION['selFormCode']; //該筆記錄的表單編號
        $arrNewFormVal['modifier'] = $_SESSION['login_emp']['empapl'];//修改者

        //參考其他Table
        $tbl['depts'] = $obj_depts->rtnQryRecord("SELECT * FROM depts WHERE 1 AND formcode = '$arrNewFormVal[deptspk]'"); //機構
        $tbl['position'] = $obj_fl->rtnQryRecord("SELECT * FROM field_lists WHERE 1 AND formcode = '$arrNewFormVal[pospk]'"); //職稱
        $tbl['sex'] = $obj_fl->rtnQryRecord("SELECT * FROM field_lists WHERE 1 AND formcode = '$arrNewFormVal[sexpk]'");//性別
        $tbl['edu'] = $obj_fl->rtnQryRecord("SELECT * FROM field_lists WHERE 1 AND formcode = '$arrNewFormVal[edupk]'"); //教育程度
        $tbl['ntn'] = $obj_fl->rtnQryRecord("SELECT * FROM field_lists WHERE 1 AND formcode = '$arrNewFormVal[ntnpk]'"); //本國藉
        $tbl['country'] = $obj_fl->rtnQryRecord("SELECT * FROM field_lists WHERE 1 AND formcode = '$arrNewFormVal[ctypk]'"); //國家
        $tbl['lisrel'] = $obj_fl->rtnQryRecord("SELECT * FROM field_lists WHERE 1 AND formcode = '$arrNewFormVal[lisrelpk]'"); //聯絡人關係
        $tbl['marrige'] = $obj_fl->rtnQryRecord("SELECT * FROM field_lists WHERE 1 AND formcode = '$arrNewFormVal[mrgpk]'"); //婚姻狀況
        $tbl['wrktim'] = $obj_fl->rtnQryRecord("SELECT * FROM field_lists WHERE 1 AND formcode = '$arrNewFormVal[wrktimpk]'"); //上班班別
        //相關工作
        $qryCondition = implode(",", $obj_fl->addQuote($arrNewFormVal['othwrk']));
        $arrNewFormVal['Newothwrk'] = implode(",", $arrNewFormVal['othwrk']);
        $tbl['othwrk'] = $obj_fl->rtnQryResults("SELECT * FROM field_lists WHERE 1 AND listapl IN ($qryCondition)");
        //常用語言
        $qryCondition = implode(",", $obj_fl->addQuote($arrNewFormVal['lang']));
        $arrNewFormVal['Newlang'] = implode(",", $arrNewFormVal['lang']);
        $tbl['language'] = $obj_fl->rtnQryResults("SELECT * FROM field_lists WHERE 1 AND listapl IN ($qryCondition)");

        //執行SQL
        $obj_employees->Update($arrNewFormVal, $tbl);
        
        //Render HTML
        $htmlTags['html_deptspk'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'depts', 'attrName'=>'deptspk', 'attrTitle'=>'請選擇機構', 'optionTitle'=>'cmpapl', 'optionValue'=>'formcode'), $obj_depts->getList(), $tbl['depts']['cmpapl']); //機構
        $htmlTags['html_position'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'posistion', 'attrName'=>'pospk', 'attrTitle'=>'請選擇職稱', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_fl->getList("職稱"), $tbl['position']['listapl']); //職稱
        $htmlTags['html_posmemo'] = $arrNewFormVal['posmemo']; //職稱說明
        $htmlTags['html_empapl'] = $arrNewFormVal['empapl']; //員工姓名
        $htmlTags['html_empcode'] = $arrNewFormVal['empcode']; //員工編號
        $htmlTags['html_empidno'] = $arrNewFormVal['empidno']; //身分證字號/居留證
        $htmlTags['html_sex'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'sex', 'attrName'=>'sexpk', 'Label'=>'listapl', 'attrValue'=>'formcode'), $obj_fl->getList("性別"), $tbl['sex']['listapl']); //性別
        $htmlTags['html_edu'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'edu', 'attrName'=>'edupk', 'attrTitle'=>'請選擇教育程度', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_fl->getList("教育程度"), $tbl['edu']['listapl']); //教育程度
        $htmlTags['html_blood'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'blood', 'attrName'=>'blood', 'attrTitle'=>'請選擇血型', 'optionTitle'=>'listapl', 'optionValue'=>'listapl'), $obj_fl->getList("血型"), $arrNewFormVal['blood']); //血型
        $htmlTags['html_telephone'] = $arrNewFormVal['telephone']; //電話號碼
        $htmlTags['html_mobilephone'] = $arrNewFormVal['mobilephone']; //手機號碼
        $htmlTags['html_birthday'] = $arrNewFormVal['birthday']; //生日
        $htmlTags['html_email'] = $arrNewFormVal['email']; //電子郵件
        $htmlTags['html_ntn'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'ntn', 'attrName'=>'ntnpk', 'Label'=>'listapl', 'attrValue'=>'formcode'), $obj_fl->getList("本國籍"), $tbl['ntn']['listapl']);//本國籍
        $htmlTags['html_country'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'cty', 'attrName'=>'ctypk', 'Label'=>'listapl', 'attrValue'=>'formcode'), $obj_fl->getList("國家"), $tbl['country']['listapl']); //國家
        $htmlTags['html_addresses'] = $arrNewFormVal['addresses']; //地址
        $htmlTags['html_liaison'] = $arrNewFormVal['liaison']; //緊急聯絡人
        $htmlTags['html_lisrel'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'lisrel', 'attrName'=>'lisrelpk', 'attrTitle'=>'請選擇聯絡人關係', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_fl->getList("聯絡人"), $tbl['lisrel']['listapl']); //聯絡人關係
        $htmlTags['html_marrige'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'mrg', 'attrName'=>'mrgpk', 'attrTitle'=>'請選擇婚姻狀況', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_fl->getList("婚姻狀況"), $tbl['marrige']['listapl']); //婚姻狀況
        $htmlTags['html_listel'] = $arrNewFormVal['listel']; //聯絡人電話號碼
        $htmlTags['html_lismob'] = $arrNewFormVal['lismob']; //聯絡人手機號碼
        $htmlTags['html_wrktim'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'wrktim', 'attrName'=>'wrktimpk', 'attrTitle'=>'請選擇上班班別', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_fl->getList("上班班別"), $tbl['wrktim']['listapl']); //上班班別
        $htmlTags['html_takofcdate'] = $arrNewFormVal['takeofcdate']; //到職日
        $htmlTags['html_levofcdate'] = $arrNewFormVal['levofcdate']; //離職日
        $htmlTags['html_othwrk'] = $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'othwrk', 'attrName'=>'othwrk', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("相關工作"), array_column($tbl['othwrk'], 'listapl')); //相關工作
        $htmlTags['html_language'] = $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'lang', 'attrName'=>'lang', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("常用語言"), array_column($tbl['language'], 'listapl')); //常用語言
         */
    } elseif (isset($_POST['logout'])) { //登出
        //$obj_form->logout();
    } else { //第一次執行表單的處理動作
        /*
        $arrQryFld = $_SESSION['arrQryFld'];
        $obj_employees->SQLSelect = "SELECT * ";
        $obj_employees->SQLFrom = $_SESSION['SQL']['From'];
        $obj_employees->SQLWhere = " WHERE 1 AND formcode = '$_SESSION[selFormCode]'";
        $obj_employees->SQL = $obj_employees->SQLSelect.$obj_employees->SQLFrom.$obj_employees->SQLWhere;
        $arrCurRecord = $obj_employees->rtnQryRecord($obj_employees->SQL); //取得目前的編輯記錄

        //Render HTML
        $htmlTags['html_deptspk'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'depts', 'attrName'=>'deptspk', 'attrTitle'=>'請選擇機構', 'optionTitle'=>'cmpapl', 'optionValue'=>'formcode'), $obj_depts->getList(), $arrCurRecord['cmpapl']); //機構
        $htmlTags['html_position'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'posistion', 'attrName'=>'pospk', 'attrTitle'=>'請選擇職稱', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_fl->getList("職稱"), $arrCurRecord['posapl']); //職稱
        $htmlTags['html_posmemo'] = $arrCurRecord['posmemo']; //職稱說明
        $htmlTags['html_empapl'] = $arrCurRecord['empapl']; //員工姓名
        $htmlTags['html_empcode'] = $arrCurRecord['empcode']; //員工編號
        $htmlTags['html_empidno'] = $arrCurRecord['empidno']; //身分證字號/居留證
        $htmlTags['html_sex'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'sex', 'attrName'=>'sexpk', 'Label'=>'listapl', 'attrValue'=>'formcode'), $obj_fl->getList("性別"), $arrCurRecord['sexapl']); //性別
        $htmlTags['html_edu'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'edu', 'attrName'=>'edupk', 'attrTitle'=>'請選擇教育程度', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_fl->getList("教育程度"), $arrCurRecord['eduapl']); //教育程度
        $htmlTags['html_blood'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'blood', 'attrName'=>'blood', 'attrTitle'=>'請選擇血型', 'optionTitle'=>'listapl', 'optionValue'=>'listapl'), $obj_fl->getList("血型"), $arrCurRecord['blood']); //血型
        $htmlTags['html_telephone'] = $arrCurRecord['telephone']; //電話號碼
        $htmlTags['html_mobilephone'] = $arrCurRecord['mobilephone']; //手機號碼
        $htmlTags['html_birthday'] = $arrCurRecord['birthday']; //生日
        $htmlTags['html_email'] = $arrCurRecord['email']; //電子郵件
        $htmlTags['html_ntn'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'ntn', 'attrName'=>'ntnpk', 'Label'=>'listapl', 'attrValue'=>'formcode'), $obj_fl->getList("本國籍"), $arrCurRecord['ntnapl']);//本國籍
        $htmlTags['html_country'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'cty', 'attrName'=>'ctypk', 'Label'=>'listapl', 'attrValue'=>'formcode'), $obj_fl->getList("國家"), $arrCurRecord['ctyapl']); //國家
        $htmlTags['html_addresses'] = $arrCurRecord['addresses']; //地址
        $htmlTags['html_liaison'] = $arrCurRecord['liaison']; //緊急聯絡人
        $htmlTags['html_lisrel'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'lisrel', 'attrName'=>'lisrelpk', 'attrTitle'=>'請選擇聯絡人關係', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_fl->getList("聯絡人"), $arrCurRecord['lisrelapl']); //聯絡人關係
        $htmlTags['html_marrige'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'mrg', 'attrName'=>'mrgpk', 'attrTitle'=>'請選擇婚姻狀況', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_fl->getList("婚姻狀況"), $arrCurRecord['mrgapl']); //婚姻狀況
        $htmlTags['html_listel'] = $arrCurRecord['listel']; //聯絡人電話號碼
        $htmlTags['html_lismob'] = $arrCurRecord['lismob']; //聯絡人手機號碼
        $htmlTags['html_wrktim'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'wrktim', 'attrName'=>'wrktimpk', 'attrTitle'=>'請選擇上班班別', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_fl->getList("上班班別"), $arrCurRecord['wrktimapl']); //上班班別
        $htmlTags['html_takofcdate'] = $arrCurRecord['takeofcdate']; //到職日
        $htmlTags['html_levofcdate'] = $arrCurRecord['levofcdate']; //離職日
        $htmlTags['html_othwrk'] = $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'othwrk', 'attrName'=>'othwrk', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("相關工作"), explode(",", $arrCurRecord['othwrk'])); //相關工作
        $htmlTags['html_language'] = $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'lang', 'attrName'=>'lang', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("常用語言"), explode(",", $arrCurRecord['lang'])); //常用語言

        $strStsMsg = "資料編輯中"; //顯示訊息
         */
    }
    
    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    }elseif (isset($_POST['submit']) && $_POST['submit'] == "確定") { //顯示完成訊息
        $obj_form->js_alert("資料已編輯成功");
    }

    //Close Connection
    $obj_form = null;
    $obj_employees = null;
    $obj_depts = null;
    $obj_fl = null;
    //End


echo <<<_html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯員工加班資料</title>
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
                    msg = "資料已編輯成功";
                    btn = "確定";
                }else if($(this).val() == "取消"){
                    msg = "取消編輯資料";
                    btn = "取消";
                }
            })
        });
    </script>
</head>
<body>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>

    <form action="workOverTimeQuery.php" method="post" id="form1" name="form1">
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
            <h4 class="text-secondary text-decoration-underline my-3"><b>編輯員工加班資料</b></h4>
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
                <input type="submit" value="確定" class="col-1 btn btn-primary">&nbsp;&nbsp;<input type="submit" value="取消" class="col-1 btn btn-outline-primary">
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