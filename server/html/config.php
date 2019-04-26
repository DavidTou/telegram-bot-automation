<?php
	
	$TOKEN="";
	//https://api.telegram.org/bot<token>/METHOD_NAME

	$webSite="https://api.telegram.org/BOTNAME".$TOKEN;
	//echo "$webSite";

	// MY ID (OwnerID TELEGRAM)
	$idDavid=0000000;

	//EMOJI Byte Format UTF-8

	$crown = "\xF0\x9F\x91\x91";
	$rocket = "\xF0\x9F\x9A\x80";
        $e_camera="\xF0\x9F\x8E\xA5"; 
        $e_fire="\xF0\x9F\x94\xA5";
        $e_snowFlake="\xE2\x9D\x84";
        $e_basketBall="\xF0\x9F\x8F\x80";
        $e_mouth="\xF0\x9F\x91\x84";
        $e_paperClip="\xF0\x9F\x93\x8E";
        $e_back="\xF0\x9F\x94\x99";
        $e_light_bulb = "\xF0\x9F\x92\xA1";
        $e_toilet = "\xF0\x9F\x9A\xBD";
        $e_headphone= "\xF0\x9F\x8E\xA7";
        $e_picture = "\xF0\x9F\x96\xBC";
        $e_therm = "\xF0\x9F\x8C\xA1";
        $e_guitar="\xF0\x9F\x8E\xB8";
        $e_violin="\xF0\x9F\x8E\xBB";
        $e_microphone ="\xF0\x9F\x8E\xA4";
        $e_heart = "\xE2\x9D\xA4";
        // CAMS
        $cam1= "192.168.1.12";
        $cam2= "192.168.1.55";
        //ARDUINO YUN
        $ipArduino="192.168.1.14";
        
        $conn=mysqli_connect("localhost","root","password","db");
?>