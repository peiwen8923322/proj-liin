<?php
    //Require_once
    require_once "../../models/common.php"; // 共用功能
    require_once "../../models/cls_holidays.php"; // 請假檔

    //變數初始化
    $obj_form = new cls_form;
    $obj_hlds = new cls_holidays;

    //$arrData = array();
    //$htmlTags = ""; //Render HTML

    //Begin
    // echo "begindate: $_POST[begindate], enddate: $_POST[enddate]";
    // $obj_FirstDayEndTime = new DateTime($_POST['begindate']);
    // echo $obj_FirstDayEndTime->format('Y-m-d 17:00:00');
    $obj_begindate = new DateTime($_POST['begindate']); //請假啟始日
    $obj_begindateTS = new DateTime($obj_begindate->format("Y-m-d 00:00:00")); //請假啟始日 00:00:00
    $obj_enddate = new DateTime($_POST['enddate']); //請假截止日
    $obj_enddateTS = new DateTime($obj_enddate->format("Y-m-d 00:00:00")); //請假截止日 00:00:00
    $obj_interval = $obj_begindate->diff($obj_enddate); // 計算請假截止日 - 請假啟始日的差異時間
    $obj_intervalTS = $obj_begindateTS->diff($obj_enddateTS); // 計算請假截止日 00:00:00 - 請假截止日 00:00:00的日期差異
    // echo "日期差異：$obj_intervalTS->days 天";

    // $obj_FirstDayEndTime = new DateTime($obj_begindate->format('Y-m-d 17:00:00')); //請假啟始日~當日下班的間隔時間
    // $obj_FirstDayItv = $obj_begindate->diff($obj_FirstDayEndTime); // 請假在二天以上時, 計算請假啟始日當天的請假時間
    // echo "請假啟始日~當日下班的間隔時間： ".(string)((float)$obj_FirstDayItv->i / 60 + (float)$obj_FirstDayItv->h);

    // $obj_EndDayBeginTime = new DateTime($obj_enddate->format('Y-m-d 09:00:00')); // 當日上班啟始時間~請假截止日的間隔時間
    // $obj_EndDayItv = $obj_enddate->diff($obj_EndDayBeginTime); // 請假在二天以上時, 計算請假啟始日當天的請假時間
    // echo "當日上班啟始時間~請假截止日的間隔時間： ".(string)((float)$obj_EndDayItv->i / 60 + (float)$obj_EndDayItv->h);

    if ($obj_intervalTS->days == 0) { // 請假在同一天(傳回請假小時)
        if ((int)$obj_interval->h == 8) { //請假 8小時
            echo $obj_interval->h;
        } else { //請假 < 8小時
            echo (float)$obj_interval->h + (float)$obj_interval->i / 60;
        }
    } elseif ($obj_intervalTS->days == 1) { // 請假在連續二天之內(傳回請假小時)
        $obj_FirstDayEndTime = new DateTime($obj_begindate->format('Y-m-d 17:00:00')); //請假啟始日~當日下班的間隔時間
        $obj_FirstDayItv = $obj_begindate->diff($obj_FirstDayEndTime); // 請假在二天以上時, 計算請假啟始日當天的請假時間
        $obj_EndDayBeginTime = new DateTime($obj_enddate->format('Y-m-d 09:00:00')); // 當日上班啟始時間~請假截止日的間隔時間
        $obj_EndDayItv = $obj_EndDayBeginTime->diff($obj_enddate); // 請假在二天以上時, 計算請假啟始日當天的請假時間
        
        echo (float)$obj_FirstDayItv->h + (float)$obj_FirstDayItv->i / 60 + (float)$obj_EndDayItv->h + (float)$obj_EndDayItv->i / 60;
    } elseif ($obj_intervalTS->days > 1) { // 請假在連續三天以上(傳回請假小時)
        $obj_FirstDayEndTime = new DateTime($obj_begindate->format('Y-m-d 17:00:00')); //請假啟始日~當日下班的間隔時間
        $obj_FirstDayItv = $obj_begindate->diff($obj_FirstDayEndTime); // 請假在二天以上時, 計算請假啟始日當天的請假時間
        $obj_EndDayBeginTime = new DateTime($obj_enddate->format('Y-m-d 09:00:00')); // 當日上班啟始時間~請假截止日的間隔時間
        $obj_EndDayItv = $obj_EndDayBeginTime->diff($obj_enddate); // 請假在二天以上時, 計算請假啟始日當天的請假時間
        
        echo (float)$obj_FirstDayItv->h + (float)$obj_FirstDayItv->i / 60 + (float)$obj_EndDayItv->h + (float)$obj_EndDayItv->i / 60 + (float)($obj_intervalTS->days - 1 - $obj_hlds->getWeekendDaysByPeriod($obj_begindateTS->format("Y-m-d 00:00:00"), $obj_enddateTS->format("Y-m-d 00:00:00")) + $obj_hlds->getMkupwrkByPeriod($obj_begindateTS->format("Y-m-d 00:00:00"), $obj_enddateTS->format("Y-m-d 00:00:00"))) * 8; // 請假啟始日 + 請假截止日 - 1 - 週未二日 + 補上班日
    }
    //End
?>