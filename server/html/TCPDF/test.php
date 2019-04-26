<?php
//require_once('config/tcpdf_config.php');
require_once('tcpdf.php');
//require_once('examples/tcpdf_include.php');
$d=date("d-m-Y");
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
sendMessage( $chatID, "Creating PDF...", $TOKEN,$keyboard);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('David Tougaw');
$pdf->SetTitle($d.' - Status');
$pdf->SetSubject('MyHome.IoT');
$pdf->SetKeywords('MyHome, PDF, MyHome.IoT, Telegram, Arduino');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// create some HTML content

$ipArduino = "192.168.1.14";
$cam2 = "192.168.1.55";
$pingresult = exec("ping -c 1 $ipArduino", $outcome, $status);
if(0 == $status){
	$arduinoStatus = "ONLINE";
	$led =file_get_contents("http://$ipArduino/arduino/digital/6/");
        $led2 =file_get_contents("http://$ipArduino/arduino/digital/5/");
	$tableArdu = '
	<table border="1" style="text-align:center">
	<tr><td style="word-wrap:break-word;"><b>Bedroom Light (White LED)</b></td><td>'.$led.'</td></tr>
	<tr><td style="word-wrap:break-word;"><b>Bathroom Light (Yellow LED)</b></td><td>'.$led2.'</td></tr></table>';
//        <tr><td style="word-wrap:break-word;"><b>Bedroom 2</b></td><td>ON</td></tr>
//        <tr><td style="word-wrap:break-word;"><b>Bathroom 2 Light</b></td><td>'.$led.'</td></tr>
//	</table>
//	';
       // getPic('http://'.$cam1.':8080/photo.jpg');

        $res= mysqli_query($conn,"SELECT * FROM cam1 order by date desc LIMIT 1");
        //$path .= "/var/www/davidtougaw.no-ip.org/ssl_pages/$TOKEN/cam1/img.jpeg";
        $row=mysqli_fetch_assoc($res);
        $pic1=$row['path'];
        $res= mysqli_query($conn,"SELECT * FROM cam2 order by date desc LIMIT 1");
        //$path .= "/var/www/davidtougaw.no-ip.org/ssl_pages/$TOKEN/cam1/img.jpeg";
        $row=mysqli_fetch_assoc($res);
        $pic2=$row['path'];
//        $res= mysqli_query($conn,"SELECT * FROM cam2 order by date desc LIMIT 1");
//        //$path .= "/var/www/davidtougaw.no-ip.org/ssl_pages/$TOKEN/cam1/img.jpeg";
//        $row2=mysqli_fetch_assoc($res);
        $val = file_get_contents("http://$ipArduino/arduino/analog/temp");
        $tableImg = '
        <div style="text-align: center"><br/>
        <table>
            <tr><th> CAM1 </th><th> CAM2 </th></tr>
            <tr>
                <td style="padding: 10" ><img src="'.$pic1.'" alt="test alt attribute" width="200" border="0" /></td>
                <td style="padding: 10"><img src="'.$pic2.'" alt="test alt attribute" width="200" border="0" /></td>
            </tr>
            
        </table>
        <h1>'.$val.'° C</h1>
	</div>
        
	';
        /*
         * <tr><th> CAM3 </th><th> CAM4 </th></tr>
            <tr>
                <td style="padding: 10" ><img src="'.$row['path'].'" alt="test alt attribute" width="200" border="0" /></td>
                <td style="padding: 10"><img src="'.$row['path'].'" alt="test alt attribute" width="200" border="0" /></td>
            </tr>
         */
}
else {
	$arduinoStatus = "OFFLINE";
	$tableArdu = "";
}

/*$pingresult = exec("ping -c 1 $cam1", $outcome, $status);
if(0 == $status){
	$tableCam='<table border=1 style="text-align:center">
	<tr><th>ARDUINO</th></tr>
	<tr><td>LED</td><td>'.$led.'</td></tr>
	<tr><td>LED 2</td><td>'.$led.'</td></tr>
	</table>';

}*/

$html = '
<div style="text-align:center">
	<h1>STATUS</h1> <br/>
	<h4>'.$d.'</h4>
</div>
<h3>Arduino: '.$arduinoStatus.'</h3>
'.$tableArdu.$tableImg;
$pdf->writeHTML($html, true, false, true, false,'');
$d=date("dmYHis");
$p=$_SERVER['DOCUMENT_ROOT'].'132507853AAGwDn4TFifj5t7GE_34sGDjtgAI5GTy8HA/docs/status/Status_'.$d.'.pdf';
$pdf->Output($p, 'F');
sendDocument($chatID, $p, $TOKEN);
?>
