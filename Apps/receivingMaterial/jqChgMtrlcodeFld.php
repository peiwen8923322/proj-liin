<?php
    //Require_once
    require_once "../../models/common.php"; //共用功能
    require_once "../../models/cls_materials.php";

    //變數初始化
    $obj_form = new cls_form;
    $obj_materials = new cls_materials; //耗材檔

    //$arrData = array();
    //$htmlTags = ""; //Render HTML

    //Begin
    //echo "splrcode: $_POST[splrcode]";
    echo $obj_form->viewHTMLSelectTag(array('attrId'=>'mtrlcode', 'attrName'=>'mtrlcode', 'attrTitle'=>'請選擇品項名稱', 'optionTitle'=>'mtrlapl', 'optionValue'=>'mtrlcode'), $obj_materials->getListBySupplier($_POST['splrcode'])); //品項名稱
    //End
?>