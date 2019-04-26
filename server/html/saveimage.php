<?php

require("config.php");

//set random name for the image, used time() for uniqueness
$d=date("YmdHis"); // data 20160413 ora 215803
$filename =  $d. '.jpg';
$filepath = $table.'/';

//read the raw POST data and save the file with file_put_contents()

//$result = file_put_contents( $filepath.$filename, file_get_contents('http://192.168.0.19:8080/photo.jpg') );
$result = file_put_contents( $filepath.$filename, $res);
if(!mysqli_query($conn,"INSERT INTO $table (path,date) VALUES ('". $filepath.$filename."','". $d."')"))
	sendMessage( $idDavid, "Errore INSERT DB", $TOKEN,false);
if (!$result) {
	$m="ERROR: Failed to write data to $filepath$filename, check permissions or webcam connection\n";
        sendMessage( $idDavid, $m, $TOKEN,false);
	exit();
}

echo $filepath.$filename;
?>
