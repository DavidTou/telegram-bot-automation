<?php

$conn;

function connect($sql) {
    $conn = mysqli_connect("localhost", "root", "rootmyass", "db");
    $res = mysqli_query($conn, $sql);
    mysqli_close($conn);
    return $res;
}

function sendMessage_PM ($chatID, $messaggio, $token, $parse_mode, $keyboard) {

      $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatID;
      if(!empty($keyboard))
        $url = $url . "&text=" . urlencode($messaggio)."&reply_markup=".urlencode($keyboard);
      else
        $url = $url . "&text=" . urlencode($messaggio);
      $url = $url . "&parse_mode=" . urlencode($parse_mode);
      $ch = curl_init();
      $optArray = array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true
      );
      curl_setopt_array($ch, $optArray);
      $result = curl_exec($ch);
      curl_close($ch);
    
}

function sendMessage($chatID, $messaggio, $token, $keyboard) {
    /* $cam1='{"text":"Cam1"}';
      $temp='{"text":"Temp"}';
      $keyboard='{"inline_keyboard":[[$cam1],[$temp]]}'; */
    /////////////////////////
//      $keyboard='{"keyboard":[["Cam1","Temp"],["Lebron","Lucia"],["Status"] ],"one_time_keyboard":true}';

      $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatID;
      if(!empty($keyboard))
        $url = $url . "&text=" . urlencode($messaggio)."&reply_markup=".urlencode($keyboard);
      else
        $url = $url . "&text=" . urlencode($messaggio);
      $ch = curl_init();
      $optArray = array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true
      );
      curl_setopt_array($ch, $optArray);
      $result = curl_exec($ch);
      curl_close($ch);

     
    // INLINE KEYBOARD MARKUP
//    $user = '{"id": $chatID,"first_name" : "David" }';
//    $callBackQuery1 = '{"id": "aa1", "from": $user }';
//
//    //$cam1= '{"text":"Cam1","callback_data": }';
//    $cam1 = '{"text":"Cam1"}';
//    $temp = '{"text":"Temp1"}';
//    //$in_keyboard='{"inline_keyboard":[["Cam1"],["Temp"]]}';
//    /*
//      $file = 'update.txt';
//      // Open the file to get existing content
//      $current = file_get_contents($file);
//     */
//

//    //https://api.telegram.org/bot132507853:AAGwDn4TFifj5t7GE_34sGDjtgAI5GTy8HA
//    ///sendMessage?chat_id=66926656&text=TEST&
//    //reply_markup={%22inline_keyboard%22:[[{%22text%22:%22Button1%22,%22url%22:%22https://www.google.com%22}],[{%22text%22:%22Button2%22,%22url%22:%22https://www.google.com%22}]]}
//    $a = array('text' => "Train 4 School", 'url' => "http://www.training4school.it");
//    $b = array('text' => "Straz", 'url' => "http://gph.is/1a2CNzE");
//    $c = array('text' => "APPLE", 'url' => "http://www.apple.com");
//    $d = array('text' => "Train 4 School", 'url' => "http://www.training4school.it");
//    $options = array([$a, $b], [$c,$d]);
//    $replyMarkup = array('inline_keyboard' => $options);
//    $encodedMarkup = json_encode($replyMarkup, true);
//    /* // print to file
//      $current.= "\n".$encodedMarkup;
//      file_put_contents($file, $current);
//     */
//
//    $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatID;
//    if ($bool)
//        $url = $url . "&text=" . urlencode($messaggio) . "&reply_markup=" . urlencode($encodedMarkup);
//    //$url = $url . "&text=" . urlencode($messaggio)."&reply_markup=".urlencode($keyboard);
//    else
//        $url = $url . "&text=" . urlencode($messaggio);
//    $ch = curl_init();
//    $optArray = array(
//        CURLOPT_URL => $url,
//        CURLOPT_RETURNTRANSFER => true
//    );
//    curl_setopt_array($ch, $optArray);
//    $result = curl_exec($ch);
//    curl_close($ch);
}

/* 	EXAMPLE
  $token = "bot<insertTokenHere>";
  $chatid = "<chatID>";
  sendMessage($chatMauro,"Hello World", $token); */

function sendPhoto($chatID, $path, $caption, $botToken) {
    $url = "https://api.telegram.org/bot" . $botToken . "/sendPhoto?";
    $post_fields = array('chat_id' => $chatID, 'photo' => new CURLFile(realpath($path)), 'caption' => $caption);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
}

function sendAudio($chatID, $path,$performer, $title, $botToken) {
    $url = "https://api.telegram.org/bot" . $botToken . "/sendAudio?";
    $post_fields = array('chat_id' => $chatID, 'audio' => new CURLFile(realpath($path)),
        'performer' => $performer, 'title' => $title);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
}

function sendDocument($chatID, $path, $botToken) {
    $url = "https://api.telegram.org/bot" . $botToken . "/sendDocument";
    $post_fields = array('chat_id' => $chatID, 'document' => new CURLFile(realpath($path)));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
}

function getPic($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //if (file_get_contents('http://192.168.0.19:8080/photo.jpg')) {
 
    curl_close($ch);
    return $res;
}

function sendMusic($conn,$chatID, $genre, $botToken){
    $sql = "SELECT id,title,author FROM music where genre='".$genre."'";
    $res =  mysqli_query($conn,$sql);
    $msg="\xF0\x9F\x94\x8A<b>  ".strtoupper($genre)." Music</b>".PHP_EOL.PHP_EOL;
    while($row = mysqli_fetch_assoc($res)){
        $msg .= "\xE2\x96\xAB".'<b>'.$row['title'].'</b>'.PHP_EOL.$row['author'].PHP_EOL.'<i>Download:</i> /mu_'.$row['id'].PHP_EOL.PHP_EOL;
    }
    sendMessage_PM( $chatID, $msg, $botToken,"HTML", null);
}

?>
