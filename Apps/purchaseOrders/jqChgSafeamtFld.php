<?php
    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_materials.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_materials = new cls_materials; //耗材檔

    $arrData = array();
    //$htmlTags = ""; //Render HTML

    //Begin
    //echo "splrcode: $_POST[splrcode]";
    $arrData = $obj_materials->getRecdBySplrcodeMtrlcode($_POST['splrcode'], $_POST['mtrlcode']);
    echo isset($arrData['safeamt']) ? $arrData['safeamt'] : '';
    //End
?>