class MYPDF extends TCPDF {

}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('SM Desarrollo');
$pdf->SetTitle('CV de '.$nombres.' '.$apellidos);

//quitar linea
$pdf->setPrintHeader(false); $pdf->setPrintFooter(false);
// set margins
$pdf->SetMargins(3, 3, 3);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 3);
// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
require_once(dirname(__FILE__).'/lang/spa.php');
$pdf->setLanguageArray($l);
}
// set font
$pdf->SetFont('helvetica', '', 11);
// add a page
$pdf->AddPage('', 'A4');


// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// CUSTOM PADDING

// set color for background
$pdf->SetFillColor(255, 255, 215);
// set cell padding
$pdf->setCellPaddings(2, 2, 2, 2);
//Close and output PDF document
$pdf->Output('CV de '.$nombres.' '.$apellidos, 'I');

//============================================================+
// END OF FILE
//============================================================+
