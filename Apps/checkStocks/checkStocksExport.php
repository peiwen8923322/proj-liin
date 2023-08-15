<?php
    
    //Require_once
    require_once "../../models//common.php"; //共用功能
    require_once "../../models/cls_pms.php";
    require_once "../../models/cls_stocks.php";
    
    //變數初始化
    $obj_form = new cls_form;
    $obj_pms = new cls_pms; //權限檔
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '盤點管理', '查詢')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有盤點資料的查詢權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(INDEXPAGE); //返回首頁
        exit();
    }
    $obj_stocks = new cls_stocks; //庫存檔
    
    $arrQry = array(); //儲存匯出資料(二維關聯陣列)
    // $htmlTags = array(); //Render HTML
    // $SQL = "";
    // $htmlQryResult = ""; //顯示查詢結果HTML Tag
    // $htmlPaging = ""; //顯示查詢分頁HTML Tag
    // $arrQryFld = ['jobort'=>"", 'mtrlapl'=>"", 'sort'=>"", 'recdsperpage'=>""]; //儲存淨化查詢條件
    // $strStsMsg = ""; //儲存狀態欄訊息
    
    //Begin
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="download.txt"');
    
    $obj_stocks->SQLSelect = $_SESSION['SQL']['Select'];
    $obj_stocks->SQLFrom = $_SESSION['SQL']['From'];
    $obj_stocks->SQLWhere = $_SESSION['SQL']['Where'];
    $obj_stocks->SQLOrderBy = $_SESSION['SQL']['OrderBy'];
    $obj_stocks->SQL = $obj_stocks->SQLSelect.$obj_stocks->SQLFrom.$obj_stocks->SQLWhere.$obj_stocks->SQLOrderBy;
    $arrQry = $obj_stocks->rtnQryResults($obj_stocks->SQL);
    var_dump($arrQry);

    // if(isset($_POST['query'])){ //按下"查詢"按鈕的處理動作
    //     $arrQryFld = $obj_form->inputChk($_POST); //淨化查詢條件
        
    //     $obj_stocks->SQLSelect = " SELECT s.*, m.safeamt ";
    //     $obj_stocks->SQLFrom = " from stocks s LEFT OUTER JOIN materials m ON(s.splrcode=m.splrcode AND s.mtrlcode=m.mtrlcode) ";
    //     $obj_stocks->SQLWhere .= " AND m.formstate = 15 AND s.formstate = 15 AND s.jobmth = '盤盈虧調整' ";
    //     $obj_stocks->SQLWhere .= isset($arrQryFld['jobort']) && mb_strlen($arrQryFld['jobort']) > 0 ? " AND s.jobort LIKE '%{$arrQryFld['jobort']}%' " : ""; //盤點人
    //     $obj_stocks->SQLWhere .= isset($arrQryFld['mtrlapl']) && mb_strlen($arrQryFld['mtrlapl']) > 0 ? " AND s.mtrlapl LIKE '%{$arrQryFld['mtrlapl']}%' " : ""; //品項名稱欄
    //     //$obj_stocks->SQLWhere .= isset($arrQryFld['mobilephone']) && mb_strlen($arrQryFld['mobilephone']) > 0 ? " AND mobilephone LIKE '%{$arrQryFld['mobilephone']}%' " : ""; //手機號碼欄
    //     $obj_stocks->SQLOrderBy .= isset($arrQryFld['sort']) ? " $arrQryFld[sort] " : '' ; //設定排序方式
    //     $htmlTags['html_sort'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'sort', 'attrName'=>'sort', 'attrTitle'=>'請輸入排序方式', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue', 'default'=>'srtValue'), array(array('srtTitle'=>'品項代碼欄位-由小到大排序', 'srtValue'=>'s.mtrlcode ASC'), array('srtTitle'=>'品項代碼欄位-由大到小排序', 'srtValue'=>'s.mtrlcode DESC')), $arrQryFld['sort']); //排序方式
    //     $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數
    //     //$htmlTags['html_recdsperpage'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'50', 'srtValue'=>'50'), array('srtTitle'=>'100', 'srtValue'=>'100'), array('srtTitle'=>'250', 'srtValue'=>'250'), array('srtTitle'=>'500', 'srtValue'=>'500')), $arrQryFld['recdsperpage']); //每頁顯示筆數

    //     //統計分頁訊息
    //     $obj_stocks->int_records_per_page = $arrQryFld['recdsperpage']; //設定每頁筆數
    //     $obj_stocks->calTotalPages($obj_stocks->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_stocks->SQLFrom.$obj_stocks->SQLWhere));
    //     $htmlPaging = $obj_form->viewPaging($obj_stocks->int_total_records, $obj_stocks->int_total_pages, $obj_stocks->int_current_page); //顯示查詢分頁HTML Tag

    //     //傳回查詢結果
    //     $obj_stocks->SQLlimit = " LIMIT $obj_stocks->intStartPos, $obj_stocks->int_records_per_page";
    //     $obj_stocks->SQL = $obj_stocks->SQLSelect.$obj_stocks->SQLFrom.$obj_stocks->SQLWhere.$obj_stocks->SQLOrderBy.$obj_stocks->SQLlimit;
    //     $htmlQryResult = $obj_stocks->viewPchOdr($obj_stocks->rtnQryResults($obj_stocks->SQL));

    //     //Save Session
    //     $_SESSION['arrQryFld'] = $arrQryFld; //儲存查詢條件
    //     $_SESSION['SQL']['Select'] = $obj_stocks->SQLSelect;
    //     $_SESSION['SQL']['From'] = $obj_stocks->SQLFrom;
    //     $_SESSION['SQL']['Where'] = $obj_stocks->SQLWhere;
    //     $_SESSION['SQL']['OrderBy'] = $obj_stocks->SQLOrderBy;
    //     $_SESSION['SQL']['CurPage'] = $obj_stocks->int_current_page; //儲存目前頁數
    // }elseif (isset($_POST['edit'])) { //按下"編輯"按鈕的處理動作
    //     $_SESSION['selFormCode'] = $_POST['selFormCode']; //儲存目前編輯記錄
    //     header("location: checkStocksEdit.php");
    //     exit();
    // }elseif (isset($_POST['paging']) || isset($_POST['discard']) || (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel"))) { //執行"分頁/編輯完成/取消編輯"功能後的處理動作
    //     if (isset($_POST['discard'])) { //按下"註銷"按鈕的處理動作
    //         if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '盤點管理', '註銷')) { //檢查使用者是否有使用權限
    //             $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有進貨資料的註銷權限，如需該功能的使用權限，請與管理者聯絡");
    //             $obj_form->js_goURL(INDEXPAGE); //返回首頁
    //             exit();
    //         }
    //         $obj_stocks->discard($_POST['selFormCode']); //註銷記錄
    //     }
        
    //     //取得分頁條件
    //     $arrQryFld = $_SESSION['arrQryFld']; //取得查詢條件
    //     $obj_stocks->SQLSelect = $_SESSION['SQL']['Select'];
    //     $obj_stocks->SQLFrom = $_SESSION['SQL']['From'];
    //     $obj_stocks->SQLWhere = $_SESSION['SQL']['Where'];
    //     $obj_stocks->SQLOrderBy = $_SESSION['SQL']['OrderBy'];
    //     $htmlTags['html_sort'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'sort', 'attrName'=>'sort', 'attrTitle'=>'請輸入排序方式', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue', 'default'=>'srtValue'), array(array('srtTitle'=>'品項代碼欄位-由小到大排序', 'srtValue'=>'mtrlcode ASC'), array('srtTitle'=>'品項代碼欄位-由大到小排序', 'srtValue'=>'mtrlcode DESC')), $arrQryFld['sort']); //排序方式
    //     $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, $arrQryFld['recdsperpage']); //每頁顯示筆數
    //     //$htmlTags['html_recdsperpage'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'50', 'srtValue'=>'50'), array('srtTitle'=>'100', 'srtValue'=>'100'), array('srtTitle'=>'250', 'srtValue'=>'250'), array('srtTitle'=>'500', 'srtValue'=>'500')), $arrQryFld['recdsperpage']); //每頁顯示筆數

    //     //統計分頁訊息
    //     $obj_stocks->int_records_per_page = $arrQryFld['recdsperpage']; //設定每頁筆數
    //     $obj_stocks->calTotalPages($obj_stocks->rtnQryField("SELECT COUNT(*) AS 'cnt_recds' ".$obj_stocks->SQLFrom.$obj_stocks->SQLWhere));
        
    //     //計算目前頁數
    //     if (isset($_POST['paging'])) { //分頁
    //         $obj_stocks->int_current_page = $_POST['CurPage'];
    //         switch ($_POST['paging']) {
    //             case '|< 第一頁':
    //                 $obj_stocks->int_current_page = 1;
    //                 break;
    //             case '<< 上一頁':
    //                 $obj_stocks->int_current_page--;
    //                 break;
    //             case '下一頁 >>':
    //                 $obj_stocks->int_current_page++;
    //                 break;
    //             case '最後頁 >|':
    //                 $obj_stocks->int_current_page = $obj_stocks->int_total_pages;
    //                 break;
    //             default:
    //                 # code...
    //                 break;
    //         }
    //         $_SESSION['SQL']['CurPage'] = $obj_stocks->int_current_page; //儲存目前頁數
    //     } elseif (isset($_GET['action']) && ($_GET['action'] == "update" || $_GET['action'] == "cancel")) { //編輯 or 取消編輯
    //         $obj_stocks->int_current_page = $_SESSION['SQL']['CurPage']; //取得目前頁數
    //     }
    //     $htmlPaging = $obj_form->viewPaging($obj_stocks->int_total_records, $obj_stocks->int_total_pages, $obj_stocks->int_current_page); //顯示查詢分頁HTML Tag

    //     //傳回查詢結果
    //     $obj_stocks->intStartPos = ($obj_stocks->int_current_page - 1) < 0 ? 0 : ($obj_stocks->int_current_page - 1) * $obj_stocks->int_records_per_page ; //資料註銷後, 檢查啟始記錄是否小於0
    //     $obj_stocks->SQLlimit = " LIMIT $obj_stocks->intStartPos,  $obj_stocks->int_records_per_page";
    //     $obj_stocks->SQL = $obj_stocks->SQLSelect.$obj_stocks->SQLFrom.$obj_stocks->SQLWhere.$obj_stocks->SQLOrderBy.$obj_stocks->SQLlimit;
    //     $htmlQryResult = $obj_stocks->viewPchOdr($obj_stocks->rtnQryResults($obj_stocks->SQL));
    // } elseif (isset($_POST['export'])) {
    //     $_SESSION['selFormCode'] = $_POST['selFormCode']; //儲存目前編輯記錄
    //     $obj_form->js_openWindow('https://tw.yahoo.com');
    //     $htmlTags['html_sort'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'sort', 'attrName'=>'sort', 'attrTitle'=>'請輸入排序方式', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'品項代碼欄位-由小到大排序', 'srtValue'=>'s.mtrlcode ASC'), array('srtTitle'=>'品項代碼欄位-由大到小排序', 'srtValue'=>'s.mtrlcode DESC')), "品項代碼欄位-由小到大排序"); //排序方式
    //     $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, 100); //每頁顯示筆數
    //     // header('Content-Type: text/csv; charset=utf-8');
    //     // header('Content-Disposition: attachment; filename="download.txt"');
    //     // echo "匯出";
    // } else { //第一次執行時的處理動作
    //     $htmlTags['html_sort'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'sort', 'attrName'=>'sort', 'attrTitle'=>'請輸入排序方式', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'品項代碼欄位-由小到大排序', 'srtValue'=>'s.mtrlcode ASC'), array('srtTitle'=>'品項代碼欄位-由大到小排序', 'srtValue'=>'s.mtrlcode DESC')), "品項代碼欄位-由小到大排序"); //排序方式
    //     $htmlTags['html_recdsperpage'] = $obj_form->viewHTMLPagingTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), null, 100); //每頁顯示筆數
    //     //$htmlTags['html_recdsperpage'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'recdsperpage', 'attrName'=>'recdsperpage', 'attrTitle'=>'請輸入每頁顯示筆數', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue'), array(array('srtTitle'=>'50', 'srtValue'=>'50'), array('srtTitle'=>'100', 'srtValue'=>'100'), array('srtTitle'=>'250', 'srtValue'=>'250'), array('srtTitle'=>'500', 'srtValue'=>'500')), 500); //每頁顯示筆數
    // };

    // if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
    //     $strStsMsg = $_SESSION['error']['errMsg'];
    //     unset($_SESSION['error']);
    // } elseif (isset($_POST['query']) || isset($_POST['paging']) || isset($_POST['discard'])) { //顯示完成訊息
    //     $strStsMsg = "資料查詢完成, 總筆數：$obj_stocks->int_total_records";
    //     $obj_form->js_alert($strStsMsg);
    // }

    //Close Connection
    $obj_stocks = null;
    $obj_pms = null;
    $obj_form = null;
    //End
?>