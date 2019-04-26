<?php


require_once("config.php");
$arrContextOptions=array(
        "ssl"=>array(
            "cafile" => "/etc/apache2/ssl/apache.crt",
            "verify_peer"=> true,
            "verify_peer_name"=> true)
);

//JSON string received
$update = file_get_contents("php://input", false, stream_context_create($arrContextOptions));

// SAVE TO FILE

// DEBUG FILE WHEN NEEDED
$file = 'update.txt';
// Open the file to get existing content
$current = file_get_contents($file);

$updateArray = json_decode ($update,TRUE);
// Append a new person to the file
$current .= "\n-----------\n". print_r($updateArray,true)."\n";
// Write the contents back to the file
file_put_contents($file, $current);
//---------------------------------------------------------
//$updateArray = json_decode ($update,TRUE);
$text = $updateArray["message"]["text"];
//$photo = $updateArray["message"]["photo"][0];

$chatID = $updateArray["message"]["chat"]["id"];

$first_name = $updateArray["message"]["from"]["first_name"];


require_once('config.php');
// include functions
require("func.php");

// commands
// ONLY ALLOW OWNER/S
if( $chatID == $idDavid ) {
    $msg="";
    // creating Reply KEYBOARDS
    $line1= array("Cameras $e_camera","Temperature$e_therm");
//                $line2= array("Light $e_light_bulb","Lucy $e_mouth");
    $line2= array("Light $e_light_bulb","Light $e_light_bulb$e_toilet");
    $line3= array("Pictures $e_picture","Music $e_headphone");
    $line4= array("Status $e_paperClip");
    $main_keyboard =  json_encode(array('keyboard'=> array($line1,$line2,$line3,$line4),
                    'one_time_keyboard'=>true,'resize_keyboard' =>true));
    $keyboard = null;
        // if(COMMAND)
    if(strpos($text,"/") === 0){
        switch(strtolower($text))
        {
            case "/start" : { 
                $msg.= " Hi I'm MyHome.IoT, How can I help you? " ;
                $keyboard=$main_keyboard;

            }; break;

            case "/doc1" : { 
                $dir = "docs";
                sendDocument($chatID, $dir."/doc1.pdf", $TOKEN);
            }; break;  
        }
        if($msg != "")
            sendMessage( $chatID, $msg, $TOKEN,$keyboard);

        switch(substr($text,0,4)){
            case "/pi_" : {
                $id = substr($text, 4);
                $sql = "SELECT path FROM pictures WHERE id=".$id;
                $res =  mysqli_query($conn,$sql);
                while($row=  mysqli_fetch_assoc($res)){
                    sendPhoto($chatID, $row[path], "", $TOKEN);
                }
            };break;
            
            case "/mu_" : {
                $id = substr($text, 4);
                $sql = "SELECT path,title,author FROM music WHERE id=".$id;
                $res =  mysqli_query($conn,$sql);
                while($row=  mysqli_fetch_assoc($res)){
                    sendAudio($chatID, $row['path'],$row['author'], $row['title'], $TOKEN);
                }
            };break;
            
            case "/sql": {/*CREATE TABLE IF NOT EXISTS pictures ( 
                id int(6) AUTO_INCREMENT PRIMARY KEY,
                name varchar(25) not null,
                path varchar (260) not null,
                index(id))*/
                if($chatID == $idDavid){
                    $sql = substr($text, 4);
                    if($conn->query($sql))
                        sendMessage($chatID,"Query OK",$TOKEN,null);
                }
                else
                    sendMessage($chatID,"You are not authorized to send queries",$TOKEN,null);
        }; break;
        }
    }
    else{
        //NOT COMMAND
        switch($text)
        {
            case "$e_back" :{
                $msg.= " How can I help you? " ;
                $keyboard=$main_keyboard;
            };break;
            case "/help" : { $msg.= " Help ?, here I am! " ;}; break;

            case "Cameras $e_camera":{
                $msg.= " Please select camera.. " ;
                $line1= array("Cam1","Cam2");
                //$line2= array("Cam3","Cam4");
                $line3= array($e_back);
                $keyboard = json_encode(array("keyboard" => array($line1,$line3),
                    "one_time_keyboard"=>true,"resize_keyboard" =>true),true); 
            }; break;

            case "Temperature$e_therm" : {
                //--------CONNECTING TO ARDUINO YUN -------------
                    //$ip="192.168.0.16";
                    //$ip="arduino.local";
                    $pingresult = exec("ping -c 1 $ipArduino", $outcome, $status);
                    if (0 == $status) {
                        $val = file_get_contents("http://$ipArduino/arduino/analog/temp");
                        $date=date("YmdHis");

                        $t = substr($val,0,5);
                        $sql="INSERT INTO temp(date,val) values('".$date."',$t)";
                        mysqli_query($conn,$sql) or
                                sendMessage( $chatID, "Errore inserimento temp DB $sql", $TOKEN,null);
                        $myDateTime = DateTime::createFromFormat('YmdHis', $date);
                        $mDateString = $myDateTime->format('d-m-Y H:i:s');
                        $msg.= "_Date_: *".$mDateString."*";
                    }
                    else{  
                        $res = mysqli_query($conn,"SELECT * FROM temp ORDER BY date DESC LIMIT 1");
                            $row=mysqli_fetch_assoc($res);
                            $date=$row['date'];
                            $val =$row['val'];
                            $msg.= "Last Temperature DB\nDate: ".$date;
                    }
                    $msg.= "\n_Temp_: *".$val." C° *"; 
                    sendMessage_PM( $chatID, $msg, $TOKEN,"Markdown",$keyboard);
                    $msg = "";
            };break;
            case "Light $e_light_bulb" : {
                //--------CONNECTING TO ARDUINO YUN -------------

                //$ip="arduino.local";
                $pingresult = exec("ping -c 1 $ipArduino", $outcome, $status);
                if (0 == $status) {
                    $val = file_get_contents("http://$ipArduino/arduino/digital/6/");

                    ($val == "ON" )? $val="OFF" : $val="ON" ;
                    $msg.= " Please select action.. " ;
                    $line1= array($val.$e_light_bulb);
                    $line2= array($e_back);
                    $keyboard = json_encode(array("keyboard" => array($line1,$line2),
                        "one_time_keyboard"=>true,"resize_keyboard" =>true),true);

                }else
                    $msg.= " IoT device not available at this time, try again later." ;
            };break;
    //LED COMMANDS                        
            case "ON$e_light_bulb" : {
                //--------CONNECTING TO ARDUINO YUN -------------
                    $val = file_get_contents("http://$ipArduino/arduino/digital/6/1");
                    $msg.= " Please select action.. " ;
                    $line1= array("OFF".$e_light_bulb);
                    $line2= array($e_back);
                    $keyboard = json_encode(array("keyboard" => array($line1,$line2),
                        "one_time_keyboard"=>true,"resize_keyboard" =>true),true);
    //                                //sends user PICTURE of camera
    //                                    $res = getPic('http://192.168.0.19:8080/photo.jpg');
    //
    //                                    //sendMessage( $chatID,"Last pic from DB...", $TOKEN, "");
    //                                    $res= mysqli_query($conn,"SELECT * FROM cam1 order by date desc LIMIT 1");
    //                                    //$path .= "/var/www/davidtougaw.no-ip.org/ssl_pages/$TOKEN/cam1/img.jpeg";
    //                                    $row=mysqli_fetch_assoc($res);
    //                                    $caption="Cam1: ".$row['date'];
    //                                    $path =$row['path'];
            };break;

            case "OFF$e_light_bulb" : {
                //--------CONNECTING TO ARDUINO YUN -------------
                    $val = file_get_contents("http://$ipArduino/arduino/digital/6/0");
                    $msg.= " Please select action.. " ;
                    $line1= array("ON".$e_light_bulb);
                    $line2= array($e_back);
                    $keyboard = json_encode(array("keyboard" => array($line1,$line2),
                        "one_time_keyboard"=>true,"resize_keyboard" =>true),true);
    //                                //sends user PICTURE of camera
    //                                    $res = getPic('http://192.168.0.19:8080/photo.jpg');
    //
    //                                    //sendMessage( $chatID,"Last pic from DB...", $TOKEN, "");
    //                                    $res= mysqli_query($conn,"SELECT * FROM cam1 order by date desc LIMIT 1");
    //                                    //$path .= "/var/www/davidtougaw.no-ip.org/ssl_pages/$TOKEN/cam1/img.jpeg";
    //                                    $row=mysqli_fetch_assoc($res);
    //                                    $caption="Cam1: ".$row['date'];
    //                                    $path =$row['path'];
            };break;




            case "Light ".$e_light_bulb.$e_toilet : {
                //--------CONNECTING TO ARDUINO YUN -------------

                //$ip="arduino.local";
                $pingresult = exec("ping -c 1 $ipArduino", $outcome, $status);
                if (0 == $status) {
                    $val = file_get_contents("http://$ipArduino/arduino/digital/5");

                    ($val == "ON" )? $val="OFF" : $val="ON" ;
                    $msg.= " Please select action.. " ;
                    $line1= array($val.$e_light_bulb.$e_toilet);
                    $line2= array($e_back);
                    $keyboard = json_encode(array("keyboard" => array($line1,$line2),
                        "one_time_keyboard"=>true,"resize_keyboard" =>true),true);

                }else
                    $msg.= " IoT device not available at this time, try again later." ;
            };break;
    //LED COMMANDS                        
            case "ON$e_light_bulb$e_toilet" : {
                //--------CONNECTING TO ARDUINO YUN -------------
                    $val = file_get_contents("http://$ipArduino/arduino/digital/5/1");
                    $msg.= " Please select action.. " ;
                    $line1= array("OFF".$e_light_bulb);
                    $line2= array($e_back);
                    $keyboard = json_encode(array("keyboard" => array($line1,$line2),
                        "one_time_keyboard"=>true,"resize_keyboard" =>true),true);
    //                                //sends user PICTURE of camera
    //                                    $res = getPic('http://192.168.0.19:8080/photo.jpg');
    //
    //                                    //sendMessage( $chatID,"Last pic from DB...", $TOKEN, "");
    //                                    $res= mysqli_query($conn,"SELECT * FROM cam1 order by date desc LIMIT 1");
    //                                    //$path .= "/var/www/davidtougaw.no-ip.org/ssl_pages/$TOKEN/cam1/img.jpeg";
    //                                    $row=mysqli_fetch_assoc($res);
    //                                    $caption="Cam1: ".$row['date'];
    //                                    $path =$row['path'];
            };break;

            case "OFF$e_light_bulb$e_toilet" : {
                //--------CONNECTING TO ARDUINO YUN -------------
                    $val = file_get_contents("http://$ipArduino/arduino/digital/5/0");
                    $msg.= " Please select action.. " ;
                    $line1= array("ON".$e_light_bulb);
                    $line2= array($e_back);
                    $keyboard = json_encode(array("keyboard" => array($line1,$line2),
                        "one_time_keyboard"=>true,"resize_keyboard" =>true),true);
    //                                //sends user PICTURE of camera
    //                                    $res = getPic('http://192.168.0.19:8080/photo.jpg');
    //
    //                                    //sendMessage( $chatID,"Last pic from DB...", $TOKEN, "");
    //                                    $res= mysqli_query($conn,"SELECT * FROM cam1 order by date desc LIMIT 1");
    //                                    //$path .= "/var/www/davidtougaw.no-ip.org/ssl_pages/$TOKEN/cam1/img.jpeg";
    //                                    $row=mysqli_fetch_assoc($res);
    //                                    $caption="Cam1: ".$row['date'];
    //                                    $path =$row['path'];
            };break;

            case "Status $e_paperClip" : {
                /*$ch = curl_init('http://'.$cam1.':8080/photo.jpg');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                if( (!empty($res= curl_exec($ch)))){
                    $table="cam1";
                    include("saveimage.php");
                    curl_close($ch);
                }
                $ch = curl_init('http://'.$cam2.':8080/photo.jpg');
                if( (!empty($res= curl_exec($ch)))){
                    $table="cam2";
                    include("saveimage.php");
                    curl_close($ch);}*/
                    //--------CONNECTING TO ARDUINO YUN -------------
                include("TCPDF/test.php");
            };break;
           // case "/shutdown" : { shell_exec('echo \'psw\' | sudo -S command shutdown');}; break;
    }

    if($msg != "")
            sendMessage( $chatID, $msg, $TOKEN,$keyboard);


    //PICS - FILES - DOCS
    $path = "";
    switch(strtolower($text)) {

        case "pictures $e_picture": {
    //                $files = glob("pics". '/*.*');
    //                $file = array_rand($files);
    //                sendPhoto($chatID, $files[$file],"", $TOKEN);
            $sql = "SELECT id,name,description FROM pictures";
            $res =  mysqli_query($conn,$sql);
            $msg=$e_pic_camera."<b>  PICTURES</b>".PHP_EOL.PHP_EOL;
            while($row = mysqli_fetch_assoc($res)){
                $msg .= $e_white_small_square.'<b>'.$row['name'].'</b>'.PHP_EOL.$row['description'].PHP_EOL.'<i>Download:</i> /pi_'.$row['id'].PHP_EOL.PHP_EOL;
            }
            sendMessage_PM( $chatID, $msg, $TOKEN,"HTML", null);
        }; break;

        
        /*case "photo $e_picture": {

               'CREATE TABLE pictures ( 
                id int(6) AUTO_INCREMENT PRIMARY KEY,
                name varchar(25) not null,
                path varchar (260) not null,
                index(id))
                '
        // parsemode text -> _Download:_[ /ph_334d44]
        };break;*/

        case "music $e_headphone": {
            $msg.= " Please select action.. " ;
            $line1= array("Rock $e_guitar","Pop $e_microphone");
            $line2= array("Classical $e_violin","Country $e_biceps");
            $line3= array($e_back);
            $keyboard = json_encode(array("keyboard" => array($line1,$line2,$line3),
                "one_time_keyboard"=>true,"resize_keyboard" =>true),true);
            sendMessage($chatID,"What Music Genre are you?",$TOKEN,$keyboard);
        }; break;
    ///MUSIC            
        case "pop $e_microphone": {
            sendMusic($conn,$chatID,"pop",$TOKEN);
            }; break;
        case "rock $e_guitar": {
            sendMusic($conn,$chatID,"rock",$TOKEN);
        }; break;
        case "classical $e_violin": {
            sendMusic($conn,$chatID,"classical",$TOKEN);
            }; break;

        case "country $e_biceps": {
            sendMusic($conn,$chatID,"country",$TOKEN);
        }; break;

        case "cam1": {

    //                        $ch = curl_init('http://192.168.0.19:8080/photo.jpg');
    //                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //                        //if (file_get_contents('http://192.168.0.19:8080/photo.jpg')) {
    //                        if( (!empty($res= curl_exec($ch)))){
    //                            //sendMessage( $chatID, 'include("saveimage.php");', $TOKEN, false);
    //                            include("saveimage.php");
    //                        }
    //                        else{
    ////                            $keyboard = json_encode(array("keyboard" => array(["Yes","No"]),
    ////                                "one_time_keyboard"=>true,"resize_keyboard" =>true),true); 
    //                            sendMessage( $chatID, "Error: ".curl_error($ch), $TOKEN, null); 
    //                        }
    //                        curl_close($ch);
            $ch = curl_init('http://'.$cam1.':8080/photo.jpg');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if( (!empty($res= curl_exec($ch)))){
                $table="cam1";
                include("saveimage.php");
                curl_close($ch);
            //sendMessage( $chatID,"Last pic from DB...", $TOKEN, "");
                $res= mysqli_query($conn,"SELECT * FROM cam1 order by date desc LIMIT 1");
                //$path .= "/var/www/davidtougaw.no-ip.org/ssl_pages/$TOKEN/cam1/img.jpeg";
                $row=mysqli_fetch_assoc($res);
                $caption="Cam1: [".date('d-m-Y H:i:s',strtotime($row['date'])).']';
                $path =$row['path'];

                //sendMessage($chatID,"Query:\n".$caption."\n".$path, $TOKEN, false);                     
                sendPhoto($chatID, $path, $caption, $TOKEN);
            }
            else
                sendMessage( $chatID, "Error: ".  print_r($res).curl_error($ch), $TOKEN , null); 

        }; break;
        
        
        case "cam2": {

            $ch = curl_init('http://'.$cam2.':8080/photo.jpg');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if( (!empty($res= curl_exec($ch)))){
                $table="cam2";
                include("saveimage.php");
                curl_close($ch);
            //sendMessage( $chatID,"Last pic from DB...", $TOKEN, "");
                $res= mysqli_query($conn,"SELECT * FROM cam2 order by date desc LIMIT 1");
                //$path .= "/var/www/davidtougaw.no-ip.org/ssl_pages/$TOKEN/cam1/img.jpeg";
                $row=mysqli_fetch_assoc($res);
                $caption="Cam2: [".date('d-m-Y H:i:s',strtotime($row['date'])).']';
                $path =$row['path'];

                //sendMessage($chatID,"Query:\n".$caption."\n".$path, $TOKEN, false);                     
                sendPhoto($chatID, $path, $caption, $TOKEN);
            }
            else
                sendMessage( $chatID, "Error: ".  print_r($res).curl_error($ch), $TOKEN , null); 

        }; break;
        
        case "doc": {
                //$path .= "docs/doc1.pdf";
                $files = scandir("docs", 1);
                $msg="";
                foreach ($files as $f) {
                    $msg.="/$f \n";
                }
                sendMessage_PM( $chatID, $msg, $TOKEN,"Markdown", null);}; break;
                //sendDocument($chatID, $path, $TOKEN); break;
    //                case "del image": {
    //                    $res= mysqli_query($conn,"SELECT * FROM cam1 order by date desc LIMIT 1");
    //                    $row=mysqli_fetch_assoc($res);
    //                    $path =$row['path'];
    //                    //delete last img
    //                    unlink($path);
    //                    sendMessage( $chatID, "Image $path deleted", $TOKEN,"");
    //                    mysqli_query($conn,"DELETE FROM cam1 WHERE path!=null order by date desc LIMIT 1");
    //                    sendMessage( $chatID, "Record deleted", $TOKEN,"");
    //                }

        case "ip": {
                $ip = gethostbyname('davidtougaw.no-ip.org');
                sendMessage($chatID,"Public IP Address:".$ip."\n",$TOKEN,"");};break;

    }
    mysqli_close($conn);
    }
}
else
	sendMessage( $chatID, "USER NOT AUTHORIZED!!!", $TOKEN, false);
?>
