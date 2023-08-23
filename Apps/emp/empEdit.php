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
    if (!$obj_pms->isOwnPms($_SESSION['login_emp']['empapl'], '員工資料管理', '編輯')) { //檢查使用者是否有使用權限
        $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有員工資料的編輯權限，如需該功能的使用權限，請與管理者聯絡");
        $obj_form->js_goURL(MOBILEINDEXPAGE); //返回首頁
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
        $arrNewFormVal = $obj_form->inputChk($_POST); //淨化查詢條件
        $arrNewFormVal['formcode'] = $_SESSION['selFormCode']; //該筆記錄的表單編號
        $arrNewFormVal['modifier'] = $_SESSION['login_emp']['empapl'];//修改者

        //參考其他Table
        $tbl['depts'] = $obj_depts->rtnQryRecord("SELECT * FROM depts WHERE 1 AND formcode = '$arrNewFormVal[deptspk]'"); //機構
        $tbl['position'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['pospk']); //職稱
        $tbl['sex'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['sexpk']);//性別
        $tbl['edu'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['edupk']); //教育程度
        $tbl['ntn'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['ntnpk']); //本國藉
        $tbl['country'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['ctypk']); //國家
        $tbl['lisrel'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['lisrelpk']); //聯絡人關係
        $tbl['marrige'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['mrgpk']); //婚姻狀況
        $tbl['wrktim'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['wrktimpk']); //上班班別
        $tbl['emprole'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['emprolepk']); //員工角色
        $tbl['mngrole'] = $obj_fl->getRcrdByFormcode($arrNewFormVal['mngrolepk']); //單位主管角色
        
        $tbl['proxy'] = $tbl['mngr'] = $tbl['hr'] = $tbl['finance'] = $tbl['chief'] = array('formcode'=>'', 'empapl'=>'', 'empcode'=>''); //設定代理人 / 單位主管 / 人事主管 / 會計主管 / 主任 選單的初始值
        $tbl['proxy'] = $obj_employees->getRecdByFormcode($arrNewFormVal['pryformcode']) ? $obj_employees->getRecdByFormcode($arrNewFormVal['pryformcode']) : $tbl['proxy']; //代理人
        $tbl['mngr'] = $obj_employees->getRecdByFormcode($arrNewFormVal['mngrformcode']) ? $obj_employees->getRecdByFormcode($arrNewFormVal['mngrformcode']) : $tbl['mngr']; //單位主管
        $tbl['hr'] = $obj_employees->getRecdByFormcode($arrNewFormVal['hrformcode']) ? $obj_employees->getRecdByFormcode($arrNewFormVal['hrformcode']) : $tbl['hr']; //人事主管
        $tbl['finance'] = $obj_employees->getRecdByFormcode($arrNewFormVal['fncformcode']) ? $obj_employees->getRecdByFormcode($arrNewFormVal['fncformcode']) : $tbl['finance']; //會計主管
        $tbl['chief'] = $obj_employees->getRecdByFormcode($arrNewFormVal['cifformcode']) ? $obj_employees->getRecdByFormcode($arrNewFormVal['cifformcode']) : $tbl['chief']; //主任
        $arrNewFormVal['chkrcnt'] = (mb_strlen($tbl['proxy']['formcode']) > 0 && mb_strlen($tbl['mngr']['formcode']) > 0) ? 4 : 2 ; //審核者人數

        //相關工作
        if (isset($arrNewFormVal['othwrk'])) {
            $qryCondition = implode(",", $obj_fl->addQuote($arrNewFormVal['othwrk']));
            $arrNewFormVal['Newothwrk'] = implode(",", $arrNewFormVal['othwrk']);
            $tbl['othwrk'] = $obj_fl->rtnQryResults("SELECT * FROM field_lists WHERE 1 AND listapl IN ($qryCondition)");
        } else {
            $arrNewFormVal['Newothwrk'] = "";
        }
        
        //常用語言
        if (isset($arrNewFormVal['lang'])) {
            $qryCondition = implode(",", $obj_fl->addQuote($arrNewFormVal['lang']));
            $arrNewFormVal['Newlang'] = implode(",", $arrNewFormVal['lang']);
            $tbl['language'] = $obj_fl->rtnQryResults("SELECT * FROM field_lists WHERE 1 AND listapl IN ($qryCondition)");
        } else {
            $arrNewFormVal['Newlang'] = "";
        }        

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
        $htmlTags['html_atwrksta'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'atwrksta', 'attrName'=>'atwrksta', 'attrTitle'=>'請選擇在職狀況', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue', 'default'=>'srtValue'), array(array('srtTitle'=>'在職', 'srtValue'=>'15'), array('srtTitle'=>'離職', 'srtValue'=>'14'), array('srtTitle'=>'停職', 'srtValue'=>'13')), $arrNewFormVal['atwrksta']); //在職狀況
        $htmlTags['html_amlhrs'] = $arrNewFormVal['amlhrs']; //過去未休假的累積時數
        $htmlTags['html_curhrs'] = $arrNewFormVal['curhrs']; //目前可特休時數
        $htmlTags['html_othwrk'] = isset($tbl['othwrk']) ? $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'othwrk', 'attrName'=>'othwrk', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("相關工作"), array_column($tbl['othwrk'], 'listapl')) : $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'othwrk', 'attrName'=>'othwrk', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("相關工作")) ; //相關工作
        $htmlTags['html_language'] = isset($tbl['language']) ? $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'lang', 'attrName'=>'lang', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("常用語言"), array_column($tbl['language'], 'listapl')) : $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'lang', 'attrName'=>'lang', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("常用語言")) ; //常用語言
        $htmlTags['html_proxy'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'pryformcode', 'attrName'=>'pryformcode', 'attrTitle'=>'代理人', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_employees->getListAtWork(), $tbl['proxy']['formcode'], true); //代理人('optionTitle'=>'NewEmpapl')
        $htmlTags['html_mngr'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'mngrformcode', 'attrName'=>'mngrformcode', 'attrTitle'=>'單位主管', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_employees->getListAtWork(), $tbl['mngr']['formcode'], true); //單位主管('optionTitle'=>'NewEmpapl')
        $htmlTags['html_hr'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'hrformcode', 'attrName'=>'hrformcode', 'attrTitle'=>'人事主管', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_employees->getListAtWork(), $arrNewFormVal['hrformcode']); //人事主管('optionTitle'=>'NewEmpapl')
        $htmlTags['html_finance'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'fncformcode', 'attrName'=>'fncformcode', 'attrTitle'=>'會計主管', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_employees->getListAtWork(), $arrNewFormVal['fncformcode']); //會計主管('optionTitle'=>'NewEmpapl')
        $htmlTags['html_chief'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'cifformcode', 'attrName'=>'cifformcode', 'attrTitle'=>'主任', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_employees->getListAtWork(), $arrNewFormVal['cifformcode']); //主任('optionTitle'=>'NewEmpapl')
        $htmlTags['html_emprolepk'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'emprolepk', 'attrName'=>'emprolepk', 'Label'=>'listapl', 'attrValue'=>'formcode', 'default'=>'formcode'), $obj_fl->getList("員工角色"), $arrNewFormVal['emprolepk']); //員工角色
        $htmlTags['html_mngrolepk'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'mngrolepk', 'attrName'=>'mngrolepk', 'attrTitle'=>'單位主管角色', 'optionTitle'=>'listapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_fl->getList("單位主管角色"), $arrNewFormVal['mngrolepk']); //單位主管角色
        $htmlTags['html_ctf'] = $arrNewFormVal['certificate']; // 證照
    } elseif (isset($_POST['logout'])) { //登出
        //$obj_form->logout();
    } else { //第一次執行表單的處理動作
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
        $htmlTags['html_atwrksta'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'atwrksta', 'attrName'=>'atwrksta', 'attrTitle'=>'請選擇在職狀況', 'optionTitle'=>'srtTitle', 'optionValue'=>'srtValue', 'default'=>'srtValue'), array(array('srtTitle'=>'在職', 'srtValue'=>'15'), array('srtTitle'=>'離職', 'srtValue'=>'14'), array('srtTitle'=>'停職', 'srtValue'=>'13')), $arrCurRecord['atwrksta']); //在職狀況
        $htmlTags['html_amlhrs'] = $arrCurRecord['amlhrs']; //過去未休假的累積時數
        $htmlTags['html_curhrs'] = $arrCurRecord['curhrs']; //目前可特休時數
        $htmlTags['html_othwrk'] = isset($arrCurRecord['othwrk']) ? $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'othwrk', 'attrName'=>'othwrk', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("相關工作"), explode(",", $arrCurRecord['othwrk'])) : $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'othwrk', 'attrName'=>'othwrk', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("相關工作")) ; //相關工作
        $htmlTags['html_language'] = isset($arrCurRecord['lang']) ? $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'lang', 'attrName'=>'lang', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("常用語言"), explode(",", $arrCurRecord['lang'])) : $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'lang', 'attrName'=>'lang', 'Label'=>'listapl', 'attrValue'=>'listapl'), $obj_fl->getList("常用語言")) ; //常用語言
        $htmlTags['html_proxy'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'pryformcode', 'attrName'=>'pryformcode', 'attrTitle'=>'代理人', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_employees->getListAtWork(), $arrCurRecord['pryformcode'], true); //代理人('optionTitle'=>'NewEmpapl')
        $htmlTags['html_mngr'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'mngrformcode', 'attrName'=>'mngrformcode', 'attrTitle'=>'單位主管', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_employees->getListAtWork(), $arrCurRecord['mngrformcode'], true); //單位主管('optionTitle'=>'NewEmpapl')
        $htmlTags['html_hr'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'hrformcode', 'attrName'=>'hrformcode', 'attrTitle'=>'人事主管', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_employees->getListAtWork(), $arrCurRecord['hrformcode']); //人事主管('optionTitle'=>'NewEmpapl')
        $htmlTags['html_finance'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'fncformcode', 'attrName'=>'fncformcode', 'attrTitle'=>'會計主管', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_employees->getListAtWork(), $arrCurRecord['fncformcode']); //會計主管('optionTitle'=>'NewEmpapl')
        $htmlTags['html_chief'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'cifformcode', 'attrName'=>'cifformcode', 'attrTitle'=>'主任', 'optionTitle'=>'NewEmpapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_employees->getListAtWork(), $arrCurRecord['cifformcode'], true); //主任('optionTitle'=>'NewEmpapl')
        $htmlTags['html_emprolepk'] = $obj_form->viewHTMLRadioTag(array('attrId'=>'emprolepk', 'attrName'=>'emprolepk', 'Label'=>'listapl', 'attrValue'=>'formcode', 'default'=>'formcode'), $obj_fl->getList("員工角色"), $arrCurRecord['emprolepk']); //員工角色
        $htmlTags['html_mngrolepk'] = $obj_form->viewHTMLSelectTag(array('attrId'=>'mngrolepk', 'attrName'=>'mngrolepk', 'attrTitle'=>'單位主管角色(員工角色選擇「單位主管」時, 請選擇此欄位)', 'optionTitle'=>'listapl', 'optionValue'=>'formcode', 'default'=>'formcode'), $obj_fl->getList("單位主管角色"), $arrCurRecord['mngrolepk']); //單位主管角色
        $htmlTags['html_ctf'] = $arrCurRecord['certificate']; // 證照

        $strStsMsg = "資料編輯中"; //顯示訊息
    }
    
    if (isset($_SESSION['error'])) { //檢查是否有錯誤訊息
        $strStsMsg = $_SESSION['error']['errMsg'];
        unset($_SESSION['error']);
    }elseif (isset($_POST['submit']) && $_POST['submit'] == "確定") { //顯示完成訊息
        $strStsMsg = "資料已編輯成功"; //顯示訊息
        $obj_form->js_alert($strStsMsg);
    }

    //Close Connection
    $obj_employees = null;
    $obj_depts = null;
    $obj_fl = null;
    $obj_pms = null;
    $obj_form = null;
    //End

echo <<<_HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯員工資料</title>
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
                //alert(msg);
            });

            //設定form表單的submit type欄位 Click事件
            $(":submit").click(function(){
                if($(this).val() == "確定"){
                    //msg = "資料已編輯成功";
                    //btn = "確定";
                }
            })

            //設定form表單的button type欄位 Click事件
            $(":button").click(function() {
                if ($(this).val() == "關閉") {
                    location.assign("./empQuery.php?action=cancel");
                }else if ($(this).val() == "登出") {
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
_HTML;

    include_once "../../Require/mnavigation.php"; //Nav區塊 下拉選單(路徑大小寫有區分)

    include_once "empNav.php"; //nav區塊 操作選單(路徑大小寫有區分)

echo <<<_HTML
    <!-- main區塊 -->
    <main>
        <div class="row"><h5 class="alert alert-success text-primary fw-bold">狀態列：$strStsMsg</h5></div>
        <h4 class="text-secondary fw-bold my-3">編輯員工資料</h4>
        <div class="row">
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="depts" class="form-label">機構：</label></div>
                    <div class="col-sm">$htmlTags[html_deptspk]</div>
                    <div class="col-sm-2 fw-bolder"><label for="posistion" class="form-label">職稱：</label></div>
                    <div class="col-sm">$htmlTags[html_position]</div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="posmemo" class="form-label">職稱說明：</label></div>
                    <div class="col-sm"><input class="form-control" style="height: 1.6cm;" type="text" id="posmemo" name="posmemo" value="$htmlTags[html_posmemo]" placeholder="請輸入職稱說明" title="請輸入職稱說明"></div>
                    <div class="col-sm-2 fw-bolder"><label for="empapl" class="form-label">員工姓名(必填)：</label></div>
                    <div class="col-sm"><input class="form-control" style="height: 1.6cm;" type="text" id="emp_name" name="empapl" value="$htmlTags[html_empapl]" placeholder="請輸入員工姓名" title="請輸入員工姓名" required></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="empcode" class="form-label">員工編號：<br/>(必填)</label></div>
                    <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="empcode" name="empcode" value="$htmlTags[html_empcode]" required></div>
                    <div class="col-sm-2 fw-bolder"><label for="empidno" class="form-label">身分證字號/居留證：<br/>(必填)</label></div>
                    <div class="col-sm"><input class="form-control" style="height: 1.6cm;" type="text" id="empidno" name="empidno" value="$htmlTags[html_empidno]" placeholder="請輸入身分證字號/居留證" title="請輸入身分證字號/居留證" required></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="sex0001" class="form-label">性別：</label></div>
                    <div class="col-sm">$htmlTags[html_sex]</div>
                    <div class="col-sm-2 fw-bolder"><label for="edu" class="form-label">教育程度：</label></div>
                    <div class="col-sm">$htmlTags[html_edu]</div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="blood" class="form-label">血型：</label></div>
                    <div class="col-sm">$htmlTags[html_blood]</div>
                    <div class="col-sm-2 fw-bolder"><label for="telephone" class="form-label">電話號碼：</label></div>
                    <div class="col-sm"><input type="tel" class="form-control" style="height: 1.6cm;" id="telephone" name="telephone" value="$htmlTags[html_telephone]" placeholder="請輸入電話號碼" title="請輸入電話號碼"></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="mobilephone" class="form-label">手機號碼(必填)：</label></div>
                    <div class="col-sm"><input class="form-control" style="height: 1.6cm;" type="tel" id="mobilephone" name="mobilephone" value="$htmlTags[html_mobilephone]" placeholder="請輸入手機號碼" title="請輸入手機號碼" required></div>
                    <div class="col-sm-2 fw-bolder"><label for="birthday" class="form-label">生日：</label></div>
                    <div class="col-sm"><input type="date" class="form-control" style="height: 1.6cm;" name="birthday" id="birthday" title="請輸入生日" value="$htmlTags[html_birthday]" required></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="email" class="form-label">電子郵件：</label></div>
                    <div class="col-sm"><input class="form-control" style="height: 1.6cm;" type="email" id="email" name="email" value="$htmlTags[html_email]" placeholder="請輸入電子郵件" title="請輸入電子郵件"></div>
                </div>
                <div class="row my-3">
                    <div class="col-sm-2 fw-bolder"><label for="ntn0001" class="form-label">本國籍：</label></div>
                    <div class="col-sm gy-2">$htmlTags[html_ntn]</div>
                    <div class="col-sm-2 fw-bolder"><label for="country" class="form-label">國家：</label></div>
                    <div class="col-sm gy-2">$htmlTags[html_country]</div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="addresses" class="form-label">地址：</label></div>
                    <div class="col-sm"><input class="form-control" style="height: 1.6cm;" type="text" id="addresses" name="addresses" value="$htmlTags[html_addresses]" placeholder="請輸入地址" title="請輸入地址"></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="liaison" class="form-label">緊急聯絡人：</label></div>
                    <div class="col-sm"><input class="form-control" style="height: 1.6cm;" type="text" id="liaison" name="liaison" value="$htmlTags[html_liaison]" placeholder="請輸入緊急聯絡人" title="請輸入緊急聯絡人"></div>
                    <div class="col-sm-2 fw-bolder"><label for="liaison_relationship" class="form-label">聯絡人關係：</label></div>
                    <div class="col-sm">$htmlTags[html_lisrel]</div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="marry" class="form-label">婚姻狀態：</label></div>
                    <div class="col-sm-4">$htmlTags[html_marrige]</div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="listel" class="form-label">聯絡人電話號碼：</label></div>
                    <div class="col-sm"><input id="listel" class="form-control" style="height: 1.6cm;" type="tel" id="listel" name="listel" value="$htmlTags[html_listel]" placeholder="請輸入聯絡人電話號碼" title="請輸入聯絡人電話號碼"></div>
                    <div class="col-sm-2 fw-bolder"><label for="lismob" class="form-label">聯絡人手機號碼：</label></div>
                    <div class="col-sm"><input class="form-control" style="height: 1.6cm;" type="tel" id="lismob" name="lismob" value="$htmlTags[html_lismob]" placeholder="請輸入聯絡人手機號碼" title="請輸入聯絡人手機號碼"></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="work_time" class="form-label">上班班別：</label></div>
                    <div class="col-sm">$htmlTags[html_wrktim]</div>
                    <div class="col-sm-2 fw-bolder"><label for="takeofcdate" class="form-label">到職日：</label></div>
                    <div class="col-sm"><input class="form-control" style="height: 1.6cm;" type="date" id="takeofcdate" name="takeofcdate" title="請輸入到職日" value="$htmlTags[html_takofcdate]" required></div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="levofcdate" class="form-label">離職日：</label></div>
                    <div class="col-sm"><input class="form-control" style="height: 1.6cm;" type="date" id="levofcdate" name="levofcdate" title="請輸入離職日" value="$htmlTags[html_levofcdate]"></div>
                    <div class="col-sm-2 fw-bolder"><label for="atwrksta" class="form-label">在職狀況：</label></div>
                    <div class="col-sm">$htmlTags[html_atwrksta]</div>
                </div>
                <div class="row">
                    <div class="col-sm-2 fw-bolder"><label for="amlhrs" class="form-label">過去未休假的累積時數：</label></div>
                    <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="amlhrs" name="amlhrs" title="請輸入過去未休假的累積時數" value="$htmlTags[html_amlhrs]" required></div>
                    <div class="col-sm-2 fw-bolder"><label for="curhrs" class="form-label">目前可特休時數：</label></div>
                    <div class="col-sm"><input type="text" class="form-control" style="height: 1.6cm;" id="curhrs" name="curhrs" title="請輸入目前可特休時數" value="$htmlTags[html_curhrs]" required></div>
                </div>
                <div class="row my-3">
                    <div class="col-sm-2 fw-bolder"><label for="othwrk_01" class="form-label">相關工作：</label></div>
                    <div class="col-sm">$htmlTags[html_othwrk]</div>
                </div>
                <div class="row my-3">
                    <div class="col-sm-2 fw-bolder"><label for="lang_01" class="form-label">常用語言：</label></div>
                    <div class="col-sm">$htmlTags[html_language]</div>
                </div>
                <div class="row my-3">
                    <div class="col-sm-2 fw-bolder"><label for="certificate" class="form-label">證照(請用分號區隔<br/>每行證照)：</label></div>
                    <div class="col-sm"><textarea class="form-control" id="certificate" name="certificate" rows="6" title="請輸入證照">$htmlTags[html_ctf]</textarea></div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-4 fw-bolder"><label for="emprolepk0001" class="form-label">員工角色：</label></div>
                    <div class="col-sm">$htmlTags[html_emprolepk](員工角色選擇「單位主管」時, 只填寫代理人欄位)</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 fw-bolder"><label for="emprolepk0001" class="form-label">單位主管角色：</label></div>
                    <div class="col-sm">$htmlTags[html_mngrolepk]</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 fw-bolder"><label for="pryformcode" class="form-label">代理人：</label></div>
                    <div class="col-sm">$htmlTags[html_proxy]</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 fw-bolder"><label for="mngrformcode" class="form-label">單位主管：</label></div>
                    <div class="col-sm">$htmlTags[html_mngr]</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 fw-bolder"><label for="hrformcode" class="form-label">人事主管：</label></div>
                    <div class="col-sm">$htmlTags[html_hr]</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 text-end fw-bolder"><label for="fncformcode" class="form-label">會計主管：</label></div>
                    <div class="col-sm">$htmlTags[html_finance]</div>
                </div>
                <div class="row">
                    <div class="col-sm-4 fw-bolder"><label for="work_time" class="form-label">主任：</label></div>
                    <div class="col-sm">$htmlTags[html_chief]</div>
                </div>
            </div>

            <div class="row justify-content-center my-3">
                <input type="submit" class="col-sm-1 btn btn-primary" id="submit" name="submit" value="確定">&nbsp;&nbsp;<input type="button" id="cancel" name="cancel" value="關閉" class="col-sm-1 btn btn-outline-primary">
            </div>
        </div>
        
    </main>    
    
    </form>
    </div>
</body>
</html>

_HTML;

?>