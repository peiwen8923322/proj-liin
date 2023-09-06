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
    if (!$obj_pms->isOwnPmsByEmpformcode($_SESSION['login_emp']['formcode'], '請假管理', '審核')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有請假管理的審核權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(MOBILEINDEXPAGE); //返回首頁
        exit();
    }

    $obj_holiday = new cls_holidays; //請假檔
    $obj_field_lists = new cls_field_lists; //欄位清單檔
    $obj_emp = new cls_employees; //員工檔
    
    $SQL = "";
    // $arrData = array();
    $htmlTags = array();
    $htmlQryResult = ""; // 顯示查詢結果HTML Tag
    $htmlPaging = ""; // 顯示查詢分頁HTML Tag
    $tbl = array(); // 儲存不同的參考檔
    $arrNewFormVal = array(); // 儲存淨化後的建立內容
    $arrQryFld = ['year'=>"", 'begindate'=>"", 'enddate'=>"", 'recdsperpage'=>'']; // 儲存淨化查詢條件
    $strStsMsg = ""; // 儲存狀態欄訊息
    
    //Begin
    $tbl['emp'] = $obj_emp->getRecdByFormcode($_SESSION['login_emp']['formcode']); // 登入者的員工記錄
    // $tbl['paging']['strPage'] = $tbl['paging']['endPage'] = 0; // 啟始頁數 + 結束頁數

    if(isset($_POST['query']) || isset($_POST['prt'])){ //按下"查詢"按鈕的處理動作 OR 按下"列印"按鈕的處理動作
        $arrQryFld = $obj_form->inputChk($_POST); // 淨化查詢條件
        
        $obj_holiday->SQLSelect = "SELECT h.*, e.formcode AS e_formcode, e.cmpapl "; // 加上"e.formcode AS e_formcode, e.cmpapl"
        $obj_holiday->SQLFrom = " FROM holidays h LEFT OUTER JOIN employees e ON (h.empformcode = e.formcode) ";
        $obj_holiday->SQLWhere .= " AND h.formstate = 15 AND e.formstate = 15 AND (h.pryformcode = '{$tbl['emp']['formcode']}' OR h.mngrformcode = '{$tbl['emp']['formcode']}' OR h.cifformcode = '{$tbl['emp']['formcode']}') "; // 代理人 OR 一般單位主管 OR 主任
        $obj_holiday->SQLWhere .= isset($arrQryFld['year']) && mb_strlen($arrQryFld['year']) > 0 ? " AND h.year = '{$arrQryFld['year']}' " : ""; // 年度(西元年)
        $htmlTags['html_year'] = $obj_form->viewHTMLSTSglVal(array('attrId'=>'year', 'attrName'=>'year', 'attrTitle'=>'請選擇年度'), array(date("Y", time())-2, date("Y", time())-1, date("Y", time()), date("Y", time())+1), $arrQryFld['year'], false); // 年度(西元年)
        if ((isset($arrQryFld['begindate']) && mb_strlen($arrQryFld['begindate']) > 0) && (isset($arrQryFld['enddate']) && mb_strlen($arrQryFld['enddate']) > 0)) { // 請假啟始日 + 請假截止日
            $obj_holiday->SQLWhere .= " AND h.begindate >= '{$arrQryFld['begindate']}' AND h.enddate <= '{$arrQryFld['enddate']}' ";
        } elseif (isset($arrQryFld['begindate']) && mb_strlen($arrQryFld['begindate']) > 0) { // 請假啟始日
            $obj_holiday->SQLWhere .= " AND h.begindate >= '{$arrQryFld['begindate']}' ";
        } elseif (isset($arrQryFld['enddate']) && mb_strlen($arrQryFld['enddate']) > 0) { // 請假截止日
            $obj_holiday->SQLWhere .= " AND h.enddate <= '{$arrQryFld['enddate']}' ";
        }
        $obj_holiday->SQLOrderBy .= " h.year, h.begindate, e.cmpcode, e.empcode ";
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數

        // 過濾請假審核查詢
        $obj_holiday->SQL = $obj_holiday->SQLSelect.$obj_holiday->SQLFrom.$obj_holiday->SQLWhere.$obj_holiday->SQLOrderBy;
        $arrData = $obj_holiday->fltVrfQry($obj_holiday->rtnQryResults($obj_holiday->SQL), $tbl);

        // 分頁按鈕 + 統計頁數訊息(HTML Tag)
        $obj_holiday->int_records_per_page = $arrQryFld['recdsperpage']; // 設定每頁筆數
        $obj_holiday->calTotalPages(count($arrData)); // 計算總頁數
        $htmlPaging = $obj_form->viewPaging($obj_holiday->int_total_records, $obj_holiday->int_total_pages, $obj_holiday->int_current_page); // 顯示查詢分頁HTML Tag

        // 查詢清單(HTML Tag)
        if ($obj_holiday->int_total_records >= $obj_holiday->int_records_per_page) { // 計算第一頁的啟始筆數 + 結束筆數
            $obj_holiday->intStartPos = 1;
            $obj_holiday->intEndPos = $obj_holiday->int_records_per_page;
        } else {
            $obj_holiday->intStartPos = 1;
            $obj_holiday->intEndPos = $obj_holiday->int_total_records;
        }
        $htmlQryResult = $obj_holiday->viewVrfQry($arrData, $tbl);

        // Save Session
        $_SESSION['arrQryFld'] = $arrQryFld;
        $_SESSION['SQL']['Select'] = $obj_holiday->SQLSelect;
        $_SESSION['SQL']['From'] = $obj_holiday->SQLFrom;
        $_SESSION['SQL']['Where'] = $obj_holiday->SQLWhere;
        $_SESSION['SQL']['OrderBy'] = $obj_holiday->SQLOrderBy;
        $_SESSION['SQL']['CurPage'] = $obj_holiday->int_current_page; // 儲存目前頁數
        $_SESSION['SQL']['arrData'] = $arrData; // 過濾後的資料來源

        if (isset($_POST['prt'])) { //按下"列印"按鈕的處理動作
            $obj_form->js_openWindow('hldsExamineRpt.php');
        }

    }elseif (isset($_POST['paging']) || isset($_POST['pass']) || isset($_POST['reject'])) { //執行"分頁 / 核准 / 退回"功能後的處理動作
        $arrNewFormVal = $obj_form->inputChk($_POST); //淨化查詢條件
        $arrNewFormVal['modifier'] = $_SESSION['login_emp']['empapl'];//修改者

        if (isset($arrNewFormVal['selFormCode']) && strlen($arrNewFormVal['selFormCode']) > 0) { // 核准 / 退回某一筆記錄
            $arrNewFormVal['formcode'] = $arrNewFormVal['selFormCode']; //該筆記錄的表單編號
            $tbl['holidays'] = $obj_holiday->getRecdByFormcode($arrNewFormVal['formcode']); //取得目前記錄
            if ($tbl['emp']['formcode'] == $tbl['holidays']['pryformcode'] && $tbl['holidays']['frmformcode'] == '2023010004') { // 代理人 + 送出
                if (isset($_POST['pass'])) { //代理人 + "核准"按鈕的處理動作
                    $tbl['frmvrf'] = $obj_field_lists->getRcrdByFormcode('2023010006'); //審核狀態(代理人已簽核)
                    $obj_holiday->Verify($arrNewFormVal, $tbl); //核准
                }
                if (isset($_POST['reject'])) { //代理人 + "退回"按鈕的處理動作
                    $tbl['frmvrf'] = $obj_field_lists->getRcrdByFormcode('2023010001'); //審核狀態(代理人已退回)
                    $obj_holiday->Verify($arrNewFormVal, $tbl); //退回
                }
            } elseif ($tbl['emp']['formcode'] == $tbl['holidays']['mngrformcode'] && $tbl['holidays']['emprolepk'] == '2023010012' && $tbl['holidays']['frmformcode'] == '2023010006') { // 單位主管 + 申請者(員工身份) + 代理人已簽核
                if (isset($_POST['pass'])) { //單位主管 + "核准"按鈕的處理動作
                    $tbl['frmvrf'] = $obj_field_lists->getRcrdByFormcode('2023010009'); //審核狀態(單位主管已簽核)
                    $obj_holiday->Verify($arrNewFormVal, $tbl); //核准
                }
                if (isset($_POST['reject'])) { //單位主管 + "退回"按鈕的處理動作
                    $tbl['frmvrf'] = $obj_field_lists->getRcrdByFormcode('2023010002'); //審核狀態(單位主管已退回)
                    $obj_holiday->Verify($arrNewFormVal, $tbl); //退回
                }
            }  elseif ($tbl['emp']['formcode'] == $tbl['holidays']['cifformcode'] && (($tbl['holidays']['emprolepk'] == '2023010012' && $tbl['holidays']['frmformcode'] == '2023010009') || ($tbl['holidays']['emprolepk'] == '2023010013' && $tbl['holidays']['frmformcode'] == '2023010006'))) { // 主任 + (申請者(員工身份)+單位主管已簽核 OR 申請者(單位主管身份)+代理人已簽核)
                if (isset($_POST['pass'])) { //主任 + "核准"按鈕的處理動作
                    $tbl['frmvrf'] = $obj_field_lists->getRcrdByFormcode('2023010017'); //審核狀態(主任已簽核)
                    $obj_holiday->Verify($arrNewFormVal, $tbl); //核准
                }
                if (isset($_POST['reject'])) { //單位主管 + "退回"按鈕的處理動作
                    $tbl['frmvrf'] = $obj_field_lists->getRcrdByFormcode('2023010023'); //審核狀態(主任已退回)
                    $obj_holiday->Verify($arrNewFormVal, $tbl); //退回
                }
            }
        }
        

        //取得分頁條件
        $arrQryFld = $_SESSION['arrQryFld'];
        $obj_holiday->SQLSelect = $_SESSION['SQL']['Select'];
        $obj_holiday->SQLFrom = $_SESSION['SQL']['From'];
        $obj_holiday->SQLWhere = $_SESSION['SQL']['Where'];
        $obj_holiday->SQLOrderBy = $_SESSION['SQL']['OrderBy'];
        $htmlTags['html_year'] = $obj_form->viewHTMLSTSglVal(array('attrId'=>'year', 'attrName'=>'year', 'attrTitle'=>'請選擇年度'), array(date("Y", time())-2, date("Y", time())-1, date("Y", time()), date("Y", time())+1), $arrQryFld['year'], false); // 年度(西元年)
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數

        // 過濾請假審核查詢
        $obj_holiday->SQL = $obj_holiday->SQLSelect.$obj_holiday->SQLFrom.$obj_holiday->SQLWhere.$obj_holiday->SQLOrderBy;
        $arrData = $obj_holiday->fltVrfQry($obj_holiday->rtnQryResults($obj_holiday->SQL), $tbl);

        // 分頁按鈕 + 統計頁數訊息(HTML Tag)
        $obj_holiday->int_records_per_page = $arrQryFld['recdsperpage']; // 設定每頁筆數
        $obj_holiday->calTotalPages(count($arrData)); // 計算總頁數
        
        // 計算目前頁數
        if (isset($_POST['paging'])) { // 分頁
            $obj_holiday->int_current_page = $_POST['CurPage'];
            switch ($_POST['paging']) {
                case '|< 第一頁':
                    $obj_holiday->int_current_page = 1;
                    break;
                case '<< 上一頁':
                    $obj_holiday->int_current_page--;
                    break;
                case '下一頁 >>':
                    $obj_holiday->int_current_page++;
                    break;
                case '最後頁 >|':
                    $obj_holiday->int_current_page = $obj_holiday->int_total_pages;
                    break;
                default:
                    # code...
                    break;
            }
            $_SESSION['SQL']['CurPage'] = $obj_holiday->int_current_page; // 儲存目前頁數
        } elseif (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel")) { // 編輯 or 取消編輯
            $obj_holiday->int_current_page = $_SESSION['SQL']['CurPage']; // 取得目前頁數
        }
        
        $htmlPaging = $obj_form->viewPaging($obj_holiday->int_total_records, $obj_holiday->int_total_pages, $obj_holiday->int_current_page); // 顯示查詢分頁HTML Tag
        
        // 查詢清單(HTML Tag)
        if ($obj_holiday->int_current_page == 1 && $obj_holiday->int_total_records >= $obj_holiday->int_records_per_page) { // 計算第一頁的啟始筆數 + 結束筆數(超過一頁)
            $obj_holiday->intStartPos = 1;
            $obj_holiday->intEndPos = $obj_holiday->int_records_per_page;
        } if ($obj_egress->int_current_page == 1 && $obj_egress->int_total_records < $obj_egress->int_records_per_page) { // 計算第一頁的啟始筆數 + 結束筆數(未滿一頁)
            $obj_egress->intStartPos = 1;
            $obj_egress->intEndPos = $obj_egress->int_total_records;
        } elseif ($obj_holiday->int_current_page == $obj_holiday->int_total_pages) { // 計算最後頁的啟始筆數 + 結束筆數
            $obj_holiday->intStartPos = ($obj_holiday->int_current_page - 1) * $obj_holiday->int_records_per_page + 1;
            $obj_holiday->intEndPos = $obj_holiday->int_total_records;
        } else { // 計算中間頁的啟始筆數 + 結束筆數
            $obj_holiday->intStartPos = ($obj_holiday->int_current_page - 1) * $obj_holiday->int_records_per_page + 1;
            $obj_holiday->intEndPos = $obj_holiday->int_current_page * $obj_holiday->int_records_per_page;
        }
        $htmlQryResult = $obj_holiday->viewVrfQry($arrData, $tbl);
    } else { //第一次執行時的處理動作
        $htmlTags['html_year'] = $obj_form->viewHTMLSTSglVal(array('attrId'=>'year', 'attrName'=>'year', 'attrTitle'=>'請選擇年度'), array(date("Y", time())-2, date("Y", time())-1, date("Y", time()), date("Y", time())+1), date("Y", time()), false); // 年度(西元年)
        // $htmlTags['html_hldscls'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'hldformcode', 'attrName'=>'hldformcode', 'attrTitle'=>'請選擇假別', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_field_lists->getList('請假'), null, true); //假別
        // $htmlTags['html_frmformcode'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'frmformcode', 'attrName'=>'frmformcode', 'attrTitle'=>'請選擇審核狀態', 'optionTitle'=>'listapl', 'optionValue'=>'formcode'), $obj_field_lists->getListByLikeListcls('表單審核'), null, true); //審核狀態

        //$htmlTags['html_sort'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'sort', 'attrName'=>'sort', 'attrTitle'=>'請輸入排序方式', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'品項代碼欄位-由小到大排序', 'srtValue'=>'mtrlcode ASC'), array('srtTitle'=>'品項代碼欄位-由大到小排序', 'srtValue'=>'mtrlcode DESC')), "品項代碼欄位-由小到大排序"); //排序方式
        $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, 100); //每頁顯示筆數
        //$htmlTags['html_recdsperpage'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'50', 'srtValue'=>'50'), array('srtTitle'=>'100', 'srtValue'=>'100'), array('srtTitle'=>'250', 'srtValue'=>'250'), array('srtTitle'=>'500', 'srtValue'=>'500')), 500); //每頁顯示筆數
    };

    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    } elseif (isset($_POST['query']) || isset($_POST['paging'])) { //顯示完成訊息
        $strStsMsg = "資料查詢完成, 總筆數：$obj_holiday->int_total_records";
        $obj_form->js_alert($strStsMsg);
    } elseif (isset($_POST['pass'])) { //核准
        $strStsMsg = "該筆記錄資料已核准";
        $obj_form->js_alert($strStsMsg);
    } elseif (isset($_POST['reject'])) { //退回
        $strStsMsg = "該筆記錄資料已退回";
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_emp = null;
    $obj_field_lists = null;
    $obj_holiday = null;
    $obj_form = null;
    //End

echo <<<_html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>審核員工請假資料</title>
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
                if($(this).val() == "核准"){ //按下"核准"按鈕
                    // msg = "開始編輯";
                    // btn = "編輯";
                    $('#selFormCode').val($(this).attr('attrformcode')); //設定被選取記錄的formcode欄位
                }else if($(this).val() == "退回"){ //按下"退回"按鈕
                    // msg = "該筆資料己經註銷";
                    // btn = "註銷";
                    $('#selFormCode').val($(this).attr('attrformcode')); //設定被選取記錄的formcode欄位
                    // return confirm("你是否確定註銷該筆記錄？"); //確認使用者是否註銷該筆記錄？
                }else if($(this).val() == "查詢"){
                    // msg = "查詢成功";
                    // btn = "查詢";
                }
            });

            //設定form表單的button type欄位 Click事件
            $(":button").click(function() {
                if ($(this).val() == "登出") { //按下"登出"按鈕
                    msg = "你已經登出系統";
                    btn = "登出";
                    location.assign("../../Public/mlogin.php");
                    alert(msg);
                }
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

    //include_once "holidaysNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_html
    <!-- main區塊 -->
    <main>
        <div class="row"><h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5></div>
        <h4 class="text-secondary text-decoration-underline my-3"><b>審核員工請假資料</b></h4>
        <div class="row justify-content-center mt-3">
            <input type="hidden" id="selFormCode" name="selFormCode" value="">
            <div class="row">
                <div class="col-sm-2">
                    <label for="year" class="form-label">年度(西元年)：</label>$htmlTags[html_year]
                </div>
                <div class="col-sm-2">
                    <label for="begindate" class="form-label">請假啟始日：</label>
                    <input type="date" class="form-control" id="begindate" name="begindate" value="{$arrQryFld['begindate']}" placeholder="請輸入請假啟始日" title="請輸入請假啟始日">
                </div>
                <div class="col-sm-2">
                    <label for="enddate" class="form-label">請假截止日：</label>
                    <input type="date" class="form-control" id="enddate" name="enddate" value="{$arrQryFld['enddate']}" placeholder="請輸入請假截止日" title="請輸入請假截止日">
                </div>
                <div class="col-sm-2">
                    <label for="supplier_telephone" class="form-label">每頁顯示筆數：</label>$htmlTags[html_recdsperpage]
                </div>
            </div>
            <div class="row gy-2">
                
            </div>

            <div class="row justify-content-center mt-2">
                <input type="submit" class="col-sm-1 btn btn-primary" id="query" name="query" value="查詢">&nbsp;&nbsp;<input type="reset" value="清除" class="col-sm-1 btn btn-outline-primary">&nbsp;&nbsp;<input type="submit" class="col-sm-1 btn btn-outline-primary" id="query" name="prt" value="列印">
            </div>
            
            <table class="table caption-top table-striped table-hover my-5">
                <caption><h4><b>員工請假清單</b></h4></caption>
                <thead class="">
                    <tr>
                        <th class="col-sm-1 text-center">功能</th><th class="">審核狀態</th><th class="text-center">年度</th><th class="text-center">機構</th>><th class="text-center">員工</th><th class="text-center">代理人</th><th class="text-center">假別</th><th class="col-sm-2">請假事由</th><th class="text-center">假單送出時間</th><th class="text-center">請假起始日</th><th class="text-center">請假截止日</th><th class="text-center">請假天數<br/>請假時數</th>
                    </tr>
                </thead>

                $htmlQryResult
            </table>
            
            $htmlPaging
            <div class="g-5"></div>
        </div>
            
        
    </main>

    </form>
    </div>
</body>
</html>
_html;

?>