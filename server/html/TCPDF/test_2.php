
<?php
$ip = "192.168.0.21";
$d=date("d-m-Y");
$pingresult = exec("ping -c 1 $ip", $outcome, $status);
if(0 == $status){
	$arduinoStatus = "ONLINE";
	$led =file_get_contents("http://$ip/arduino/digital/6/");
	$tableArdu = '
	<table style="text-align:center">
	<tr><th>ARDUINO</th></tr>
	<tr><td>LED</td><td>'.$led.'</td></tr>
	<tr><td>LED 2</td><td>'.$led.'</td></tr>
	</table>
	';
}
else {
	$arduinoStatus = "OFFLINE";
	$tableArdu = "";
}


$html = '
<div style="text-align:center">
	<h1>STATUS</h1><br>
	<h2>'.$d.'</h2>
</div>
<h3>Arduino: '.$arduinoStatus.'</h3>
'.$tableArdu;
echo $html;
?>
