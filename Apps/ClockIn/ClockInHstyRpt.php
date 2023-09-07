<?php
//============================================================+
// File name   : example_002.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 002 for TCPDF class
//               Removing Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Removing Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 * @group header
 * @group footer
 * @group page
 * @group pdf
 */


require_once "../../models/common.php"; //共用功能
require_once "../../models/cls_pms.php";
require_once "../../models/cls_clockin.php";
require_once "../../models/cls_employees.php";

$obj_form = new cls_form;
$obj_pms = new cls_pms; //權限檔
// if (!$obj_pms->isOwnPmsByEmpformcode($_SESSION['login_emp']['formcode'], '請假管理', '查詢')) { //檢查使用者是否有使用權限
//     $obj_form->js_alert("使用者：[{$_SESSION['login_emp']['empapl']}]沒有請假管理的查詢權限，如需該功能的使用權限，請與管理者聯絡");
//     $obj_form->js_goURL(MOBILEINDEXPAGE); //返回首頁
//     exit();
// }
$obj_clockin = new cls_clockin; //刷卡檔
$obj_emp = new cls_employees; //員工檔
$tbl = array();
$tbl['emp'] = $obj_emp->getRecdByFormcode($_SESSION['login_emp']['formcode']); // 登入者

// Include the main TCPDF library (search for installation path).
require_once('../../models/TCPDF/examples/tcpdf_include.php');

class MYPDF extends TCPDF {
    public $arrEmp;

	//Page header
	public function Header() {
		// Logo
		// $image_file = K_PATH_IMAGES.'logo_example.jpg';
		// $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Position at 15 mm from bottom
		$this->setY(10);
		// Set font
		$this->setFont('msungstdlight', '', 20);
		// Title
		$this->Cell(0, 0, '刷卡歷史資料清單', 0, true, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln();
        $this->setFont('msungstdlight', '', 10);
        $this->Cell(0, 10, "機構：{$this->arrEmp['cmpapl']} / 員工：{$this->arrEmp['empapl']}", 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Cell(0, 10, "列印人：{$this->arrEmp['empapl']} / 列印日期：".date("Y-m-d H:i", time()), 0, true, 'R', 0, '', 0, false, 'M', 'M');
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->setY(-15);
		// Set font
		$this->setFont('msungstdlight', 'I', 12);

        $this->Cell(0, 0, '主任：                                                    會計：                                                    人事：                                                    經辦：', 'T', false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Ln();
		// Page number
		$this->Cell(0, 0, $this->getAliasNumPage().' / '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}

// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('徐培文');
$pdf->setTitle('刷卡歷史資料清單');
$pdf->setSubject('刷卡歷史資料清單');
$pdf->setKeywords('員工, PDF, 刷卡');

// remove default header/footer
$pdf->setPrintHeader(true);
$pdf->arrEmp = $tbl['emp'];
$pdf->setPrintFooter(true);

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->setFont('msungstdlight', '', 9);

// add a page
$pdf->AddPage();

$obj_clockin->SQLSelect = $_SESSION['SQL']['Select'];
$obj_clockin->SQLFrom = $_SESSION['SQL']['From'];
$obj_clockin->SQLWhere = $_SESSION['SQL']['Where'];
$obj_clockin->SQLOrderBy = $_SESSION['SQL']['OrderBy'];
$obj_clockin->SQL = $obj_clockin->SQLSelect.$obj_clockin->SQLFrom.$obj_clockin->SQLWhere.$obj_clockin->SQLOrderBy;
$pdf->writeHTML($obj_clockin->PrtPDFByHstyQry($obj_clockin->rtnQryResults($obj_clockin->SQL), $tbl), true, false, true, false, '');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('ClockInHstyRpt.pdf', 'I');

$obj_emp = null;
$obj_clockin = null;
$obj_pms = null;
$obj_form = null;
//============================================================+
// END OF FILE
//============================================================+
