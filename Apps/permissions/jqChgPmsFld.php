<?php
    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_prgs.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_prgs = new cls_prgs; //程式檔

    //$arrData = array();
    //$htmlTags = ""; //Render HTML

    //Begin
    echo $obj_form->viewHTMLCheckBoxTag(array('attrId'=>'prgact', 'attrName'=>'prgact', 'Label'=>'prgact', 'attrValue'=>'prgact'), $obj_prgs->getRcds(" AND formstate = 15 AND prgcls = '$_POST[prgcls]' "), array("查詢")); //程式執行權限
    //End
?>