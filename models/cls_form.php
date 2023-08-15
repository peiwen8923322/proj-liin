<?php
    /*
    表格類別：cls_form
    */

use Dotenv\Parser\Value;
use PhpParser\Node\Expr\AssignOp\Div;
use Symfony\Component\Console\Input\Input;

use function PHPUnit\Framework\at;
use function Symfony\Component\String\b;

    class cls_form{
        //variable members

        //function members
        //建構式
        function __construct(){
            
        }
        
        //解構式
        function __destruct(){
            
        }
        
        // 表單驗證
        // $arrData: $_POST[]
        // return: 淨化後陣列
        function inputChk($arrData){
            $arrNewData = null;
            //Begin
            foreach ($arrData as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $key2 => $value2) {
                        $arrNewData[$key][$key2] = trim(strip_tags($value2));
                    }
                } else {
                    $arrNewData[$key] = trim(stripslashes(strip_tags($value)));
                }
            }
            return $arrNewData;
            //End
        }

        // 傳回HTML <SELECT> Tag
        // $arrFieldsName: 傳入欄位名稱陣列, keys: 'attrId', 'attrName', 'attrTitle', 'optionTitle', 'optionValue', 'default'
        //     attrId：設定 <SELECT> Tag的 id屬性
        //     attrName：設定 <SELECT> Tag的 name屬性
        //     attrTitle：設定 <SELECT> Tag的title屬性
        //     optionTitle：設定 <OPTION> Tag顯示內容要讀取資料來源的哪一個欄位
        //     optionValue：設定 <OPTION> Tag的value屬性要讀取資料來源的哪一個欄位
        //     default：設定 <OPTION> Tag的預設值要讀取資料來源的哪一個欄位
        // $arrData: 傳入資料來源(二維關聯陣列)
        // $Selection: 傳入預設值(null值) / 單一值 / 一維陣列
        // $fillvacancy: 是否插入空白列，如果資料來源為空陣列時，則一定要插入空白列(true/false)
        // $isMutilple: 是否轉成清單(true/false)
        // $size: 顯示清單筆數
        // $strHTMLTag: 傳回HTML表單欄位
        function viewHTMLSelectTag($arrFieldsName, $arrData, $Selection=null, $fillvacancy=false, $isMutilple=false, $size=10){
            //Begin
            $strHTMLTag = $isMutilple ? "<select id='$arrFieldsName[attrId]' name='$arrFieldsName[attrName][]' class='form-select' multiple size='$size' aria-label='multiple $arrFieldsName[attrTitle]' title='$arrFieldsName[attrTitle]'>" : "<select id='$arrFieldsName[attrId]' name='$arrFieldsName[attrName]' class='form-select' aria-label='$arrFieldsName[attrTitle]' style='height: 1.6cm;' title='$arrFieldsName[attrTitle]'>" ;
            // $strHTMLTag = "<select id='$arrFieldsName[attrId]' name='$arrFieldsName[attrName]' class='form-select' aria-label='$arrFieldsName[attrTitle]' style='height: 1.6cm;' title='$arrFieldsName[attrTitle]'>";
            if ($fillvacancy) {
                $strHTMLTag .= "<option value=''></option>";
            }
            foreach ($arrData as $value) {
                if (isset($Selection) && is_array($Selection)) { // $Selection(一維陣列)
                    if (isset($arrFieldsName['default'])) {
                        $strHTMLTag .= in_array($value[$arrFieldsName['default']], $Selection) ? "<option value='".$value[$arrFieldsName['optionValue']]."' selected>".$value[$arrFieldsName['optionTitle']]."</option>": "<option value='".$value[$arrFieldsName['optionValue']]."'>".$value[$arrFieldsName['optionTitle']]."</option>";
                    } else {
                        $strHTMLTag .= in_array($value[$arrFieldsName['optionTitle']], $Selection) ? "<option value='".$value[$arrFieldsName['optionValue']]."' selected>".$value[$arrFieldsName['optionTitle']]."</option>": "<option value='".$value[$arrFieldsName['optionValue']]."'>".$value[$arrFieldsName['optionTitle']]."</option>";
                    }
                } elseif (isset($Selection) && strlen($Selection) > 0) { // $Selection(單一值)
                    if (isset($arrFieldsName['default'])) {
                        $strHTMLTag .= $Selection == $value[$arrFieldsName['default']] ? "<option value='".$value[$arrFieldsName['optionValue']]."' selected>".$value[$arrFieldsName['optionTitle']]."</option>": "<option value='".$value[$arrFieldsName['optionValue']]."'>".$value[$arrFieldsName['optionTitle']]."</option>";
                    } else {
                        $strHTMLTag .= $Selection == $value[$arrFieldsName['optionTitle']] ? "<option value='".$value[$arrFieldsName['optionValue']]."' selected>".$value[$arrFieldsName['optionTitle']]."</option>": "<option value='".$value[$arrFieldsName['optionValue']]."'>".$value[$arrFieldsName['optionTitle']]."</option>";
                    }
                } else { // $Selection(null值)
                    $strHTMLTag .= "<option value='".$value[$arrFieldsName['optionValue']]."'>".$value[$arrFieldsName['optionTitle']]."</option>";
                }
            }
            $strHTMLTag .= "</select>";

            return $strHTMLTag;
            //End
        }

        // 傳回HTML <SELECT> Tag
        // $arrFieldsName: 傳入欄位名稱陣列, keys: 'attrId', 'attrName', 'attrTitle'
        //     attrId：設定 <SELECT> Tag的 id屬性
        //     attrName：設定 <SELECT> Tag的 name屬性
        //     attrTitle：設定 <SELECT> Tag的title屬性
        // $arrData: 傳入資料來源(一維一般陣列)
        // $Selection: 傳入預設值
        // $fillvacancy: 是否插入空白列，如果資料來源為空陣列時，則一定要插入空白列
        // $strHTMLTag: 傳回HTML表單欄位
        function viewHTMLSTSglVal($arrFieldsName, $arrData, $Selection=null, $fillvacancy=false){
            //Begin
            $strHTMLTag = "<select id='$arrFieldsName[attrId]' name='$arrFieldsName[attrName]' class='form-select' aria-label='$arrFieldsName[attrTitle]' style='height: 1.6cm;' title='$arrFieldsName[attrTitle]'>";
            if ($fillvacancy) {
                $strHTMLTag .= "<option value=''></option>";
            }
            foreach ($arrData as $value) {
                $strHTMLTag .= isset($Selection) && $Selection == $value ? "<option value='".$value."' selected>".$value."</option>": "<option value='".$value."'>".$value."</option>";
            }
            $strHTMLTag .= "</select>";

            return $strHTMLTag;
            //End
        }

        // 傳回 HTML <input type="checkbox"> Tag
        // $arrFieldsName: 傳入欄位名稱陣列, keys: 'attrId', 'attrName', 'Label', 'attrValue'
        //     attrId：設定 <INPUT type = 'radio'> Tag的 id屬性
        //     attrName：設定 <INPUT type = 'radio'> Tag的 name屬性
        //     Label：設定 <INPUT type = 'radio'> Tag顯示內容要讀取資料來源的哪一個欄位
        //     attrValue：設定 <INPUT type = 'radio'> Tag的 value屬性要讀取資料來源的哪一個欄位
        // $arrData: 傳入資料來源陣列(二維關聯陣列)
        // $arrSelection: 傳入預設值(一維陣列)
        // $strHTMLTag: 傳回HTML表單欄位
        function viewHTMLCheckBoxTag($arrFieldsName, $arrData, $arrSelection=array()){
            //變數初始化
            $strHTMLTag = "";
            $count = 0;

            //Begin
            foreach ($arrData as $value) {
                $count++;
                $strHTMLTag .= "<div class='form-check form-check-inline'>";
                if (in_array($value[$arrFieldsName['Label']], $arrSelection)) {
                    $strHTMLTag .= "<input id='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-input' type='checkbox' name='".$arrFieldsName['attrName']."[]' value='".$value[$arrFieldsName['attrValue']]."' checked><label for='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-label'>".$value[$arrFieldsName['Label']]."</label>";
                }else {
                    $strHTMLTag .= "<input id='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-input' type='checkbox' name='".$arrFieldsName['attrName']."[]' value='".$value[$arrFieldsName['attrValue']]."'><label for='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-label'>".$value[$arrFieldsName['Label']]."</label>";
                }
                $strHTMLTag .= "</div>";
            }

            return $strHTMLTag;
            //End
        }
        
        // 傳回 HTML <input type="radio"> Tag
        // $arrFieldsName: 傳入欄位名稱陣列, keys: 'attrId', 'attrName', 'Label', 'attrValue', 'default'
        //     attrId：設定 <INPUT type = 'radio'> Tag的 id屬性
        //     attrName：設定 <INPUT type = 'radio'> Tag的 name屬性
        //     Label：設定 <INPUT type = 'radio'> Tag顯示內容要讀取資料來源的哪一個欄位
        //     attrValue：設定 <INPUT type = 'radio'> Tag的 value屬性要讀取資料來源的哪一個欄位
        //     default：設定 <INPUT type = 'radio'> Tag的預設值要讀取資料來源的哪一個欄位
        // $arrData: 傳入資料來源陣列(二維關聯陣列)
        // $Selection: 傳入預設值
        // $strHTMLTag: 傳回HTML表單欄位
        function viewHTMLRadioTag($arrFieldsName, $arrData, $Selection=null){
            //變數初始化
            $strHTMLTag = "";
            $count = 0;

            //Begin
            foreach ($arrData as $value) {
                $count++;
                $strHTMLTag .= "<div class='form-check form-check-inline'>";
                if (isset($arrFieldsName['default'])) {
                    $strHTMLTag .= $Selection == $value[$arrFieldsName['default']] ? "<input id='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-input' type='radio' name='".$arrFieldsName['attrName']."' value='".$value[$arrFieldsName['attrValue']]."' checked><label for='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-label'>".$value[$arrFieldsName['Label']]."</label>" : "<input id='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-input' type='radio' name='".$arrFieldsName['attrName']."' value='".$value[$arrFieldsName['attrValue']]."'><label for='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-label'>".$value[$arrFieldsName['Label']]."</label>";
                } else {
                    $strHTMLTag .= $Selection == $value[$arrFieldsName['Label']] ? "<input id='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-input' type='radio' name='".$arrFieldsName['attrName']."' value='".$value[$arrFieldsName['attrValue']]."' checked><label for='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-label'>".$value[$arrFieldsName['Label']]."</label>" : "<input id='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-input' type='radio' name='".$arrFieldsName['attrName']."' value='".$value[$arrFieldsName['attrValue']]."'><label for='".sprintf("%s%04d", $arrFieldsName['attrId'], $count)."' class='form-check-label'>".$value[$arrFieldsName['Label']]."</label>";
                }
                
                
                $strHTMLTag .= "</div>";
            }

            return $strHTMLTag;
            //End
        }

        // 建立分頁列
        // $int_total_records: 總筆數
        // $int_total_pages: 總頁數
        // $int_current_page: 目前頁數
        // return: 分頁HTML tag
        function viewPaging($int_total_records, $int_total_pages, $int_current_page){
            //變數初始化
            $viewPaging = ""; //分頁HTML tag
            
            //Begin
            $viewPaging .=<<<_html
                    <nav>
                        <div class="d-flex justify-content-center">
                            <div class="btn bg-success text-white px-4 me-auto">總筆數：$int_total_records / 總頁數：$int_total_pages / 目前頁數：$int_current_page</div>
_html;

            if ($int_total_pages >= $int_current_page && $int_current_page > 1) { //非第一筆記錄
                $viewPaging .=<<<_html
                            <input type="submit" class="btn btn-outline-success px-4" name="paging" value="|< 第一頁">
                            <input type="submit" class="btn btn-outline-success px-4" name="paging" value="<< 上一頁">
_html;
            }            
            if ($int_total_pages >= $int_current_page && $int_current_page != $int_total_pages) { //非最後一筆記錄
                $viewPaging .=<<<_html
                            <input type="submit" class="btn btn-outline-success px-4" name="paging" value="下一頁 >>">
                            <input type="submit" class="btn btn-outline-success px-4" name="paging" value="最後頁 >|">
_html;
            }
            
            $viewPaging .=<<<_html
                            <input type="hidden" name="CurPage" Value="$int_current_page">
                        </div>
                    </nav>
_html;

            return $viewPaging;
            //End
        }

        // 傳回查詢條件的分頁欄位
        // $arrFieldsName: 傳入欄位名稱陣列, keys: 'attrId', 'attrName', 'attrTitle', 'optionTitle', 'optionValue', 'default'
        // $arrData: 傳入二維關聯陣列
        // $Selection: 傳入預設值
        // $strHTMLTag: 傳回HTML表單欄位
        function viewHTMLPagingTag($arrFieldsName, $arrData=null, $Selection=null, $fillvacancy=false){
            //變數初始化
            $arrSrcPaging = array(array('srtTitle'=>'50', 'srtValue'=>'50'), array('srtTitle'=>'100', 'srtValue'=>'100'), array('srtTitle'=>'250', 'srtValue'=>'250'), array('srtTitle'=>'500', 'srtValue'=>'500'));

            //Begin
            $strHTMLTag = "<select id='$arrFieldsName[attrId]' name='$arrFieldsName[attrName]' class='form-select' aria-label='$arrFieldsName[attrTitle]' style='height: 1.6cm;' title='$arrFieldsName[attrTitle]'>";
            if ($fillvacancy) {
                $strHTMLTag .= "<option value=''></option>";
            }

            if (isset($arrData)) {
                foreach ($arrData as $value) {
                    if (isset($arrFieldsName['default'])) {
                        $strHTMLTag .= $Selection == $value[$arrFieldsName['default']] ? "<option value='".$value[$arrFieldsName['optionValue']]."' selected>".$value[$arrFieldsName['optionTitle']]."</option>": "<option value='".$value[$arrFieldsName['optionValue']]."'>".$value[$arrFieldsName['optionTitle']]."</option>";
                    } else {
                        $strHTMLTag .= $Selection == $value[$arrFieldsName['optionTitle']] ? "<option value='".$value[$arrFieldsName['optionValue']]."' selected>".$value[$arrFieldsName['optionTitle']]."</option>": "<option value='".$value[$arrFieldsName['optionValue']]."'>".$value[$arrFieldsName['optionTitle']]."</option>";
                    }
                }
            } else {
                foreach ($arrSrcPaging as $value) {
                    if (isset($arrFieldsName['default'])) {
                        $strHTMLTag .= $Selection == $value[$arrFieldsName['default']] ? "<option value='".$value[$arrFieldsName['optionValue']]."' selected>".$value[$arrFieldsName['optionTitle']]."</option>": "<option value='".$value[$arrFieldsName['optionValue']]."'>".$value[$arrFieldsName['optionTitle']]."</option>";
                    } else {
                        $strHTMLTag .= $Selection == $value[$arrFieldsName['optionTitle']] ? "<option value='".$value[$arrFieldsName['optionValue']]."' selected>".$value[$arrFieldsName['optionTitle']]."</option>": "<option value='".$value[$arrFieldsName['optionValue']]."'>".$value[$arrFieldsName['optionTitle']]."</option>";
                    }
                }
            }
            
            
            $strHTMLTag .= "</select>";

            return $strHTMLTag;
            //End
        }

        // 匯出CSV格式
        // $arrData: 資料來源(二維關聯陣列)
        // $title: 標題列
        // $arrFields: 要匯出的資料欄位(一維陣列)
        // function ExportCSV($arrData, $title, $arrFields) : string {
        //     // Begin
        //     $lines = $title;
        //     foreach ($arrData as $rows) {
        //         foreach ($rows as $key => $value) {
        //             if (in_array($key, $arrFields)) {
        //                 # code...
        //             }
        //         }
        //         // $lines .= "";
        //     }
        //     return $lines;
        //     // End
        // }

        // -------------------------------------------------------------------------------------------------------------------------------------------------------------------

        // 以JavaScript顯示狀態訊息
        // $msg: 傳入狀態訊息
        function js_alert($msg){
            //Begin
            echo "<script>alert('$msg');</script>";
            //End
        }

        // 以JavaScript跳至其他網頁
        // $msg: 傳入狀態訊息
        function js_goURL($URL){
            //Begin
            echo "<script>location.assign('$URL');</script>";
            //End
        }

        // 以JavaScript開啟新視窗
        // $msg: 傳入狀態訊息
        function js_openWindow($URL, $name='_blank', $attr='height=400, width==600, top=200, left=500, menubar=no, scrollbars=no, status=no, titlebar=no, toolbar=no'){
            //Begin
            echo "<script>window.open('$URL', '$name', '$attr');</script>";
            //End
        }
        
        //  登出 
        function logout(){
            //Begin
            session_destroy();
            header("location: ".WWWROOT);
            exit;
            //End
        }
    }
?>