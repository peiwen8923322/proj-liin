<?php
    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_stocks.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_stocks = new cls_stocks; //耗材檔

    //$arrData = array();
    //$htmlTags = ""; //Render HTML

    //Begin
    // echo "splrcode: $_POST[splrcode], mtrlcode: $_POST[mtrlcode]";
    echo count($obj_stocks->getListByMtrlPrc($_POST["splrcode"], $_POST["mtrlcode"])) > 0 ? $obj_form->viewHTMLSelectTag(array('attrId'=>'mtrlprclist', 'attrName'=>'mtrlprclist', 'attrTitle'=>'請選擇品項單價，如果沒有合適的品項單價，請在右欄自行輸入。', 'optionTitle'=>'mtrlprc', 'optionValue'=>'mtrlprc'), $obj_stocks->getListByMtrlPrc($_POST["splrcode"], $_POST["mtrlcode"])) : $obj_form->viewHTMLSelectTag(array('attrId'=>'mtrlprclist', 'attrName'=>'mtrlprclist', 'attrTitle'=>'請選擇品項單價，如果沒有合適的品項單價，請在右欄自行輸入。', 'optionTitle'=>'mtrlprc', 'optionValue'=>'mtrlprc'), array(), null, true) ; //如果品項單價不存在, 預設值為空白選項
    //End
?>