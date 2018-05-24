<?php
/*
copyright @ medantechno.com
Modified @ Farzain - zFz
2017

*/

require_once('./line_class.php');
require_once('./unirest-php-master/src/Unirest.php');

$channelAccessToken = '0wMfczYB9GTr6TZb5G5IbcwA+uGcSwTGzm+/JKaUvvesgSxi7VEt+gvFSWongAYo2VlUasN6Svr/h86Pi8+QGghRnGDUOWZgDH54x9Tm1KpYKmLqg4aMByOVeLg7cfSKrJkeCFq7UBt9pfrDIJi7yQdB04t89/1O/w1cDnyilFU='; //sesuaikan 
$channelSecret = '6c589d3320de394d76ae37b52ea1c7ba';

$client = new LINEBotTiny($channelAccessToken, $channelSecret);

$userId 	= $client->parseEvents()[0]['source']['userId'];
$groupId 	= $client->parseEvents()[0]['source']['groupId'];
$replyToken = $client->parseEvents()[0]['replyToken'];
$timestamp	= $client->parseEvents()[0]['timestamp'];
$type 		= $client->parseEvents()[0]['type'];

$message 	= $client->parseEvents()[0]['message'];
$messageid 	= $client->parseEvents()[0]['message']['id'];

$profil = $client->profil($userId);

$pesan_datang = explode(" ", $message['text']);

$command = $pesan_datang[0];
$options = $pesan_datang[1];
if (count($pesan_datang) > 2) {
    for ($i = 2; $i < count($pesan_datang); $i++) {
        $options .= '+';
        $options .= $pesan_datang[$i];
    }
}

#-------------------------[Function]-------------------------#
function piket($keyword) {
    $uri = "https://canny-composites.000webhostapp.com/" . $keyword;

    $response = Unirest\Request::get("$uri");
	$date = new DateTime();
	$tg = $date->format('d-m-Y');
    $json = json_decode($response->raw_body, true);
    $result = "Jadwal Piket Sekre HMEI";
	$result .= "\nTanggal : ";
	$result .= $tg;
	//$result .= $json['minggu1']['senin'][0];
	$result .= "\n\nDivisi :\n\t\t\t";
	$result .= $json['minggu2']['senin'][1];
	$result .= "\n\t\t\t";
	$result .= $json['minggu4']['jumat'][0];
	$result .= "\n\nPI/Kadept :\n\t\t\t\t\t";
	$result .= $json['kadept']['rabu'][0];
	$result .= "\n\t\t\t\t\t";
	$result .= $json['kadept']['kamis'][1];
	
    return $result;
}
#-------------------------[Function]-------------------------#
function senin1() {
    $uri = "https://canny-composites.000webhostapp.com/";

    $response = Unirest\Request::get("$uri");
	$date = new DateTime();
	$tg = $date->format('d-m-Y');
    $json = json_decode($response->raw_body, true);
    $result = "Jadwal Piket Sekre HMEI";
	$result .= "\nTanggal : ";
	$result .= $tg;
	$result .= "\n\nDivisi :\n\t\t\t";
	$result .= $json['minggu1']['senin'][1];
	$result .= "\n\t\t\t";
	$result .= $json['minggu1']['senin'][0];
	$result .= "\n\nPI/Kadept :\n\t\t\t\t\t";
	$result .= $json['kadept']['senin'][0];
	$result .= "\n\t\t\t\t\t";
	$result .= $json['kadept']['senin'][1];
	
    return $result;
}

# require_once('./src/function/search-1.php');
# require_once('./src/function/download.php');
# require_once('./src/function/random.php');
# require_once('./src/function/search-2.php');
# require_once('./src/function/hard.php');

//show menu, saat join dan command /menu
if ($type == 'join' || $command == '/menu') {
    $text = "Silahkan ketik \n\n/piket\nPiket\npiket \n\nnanti aku bakalan kasih tahu jadwal piket untuk hari ini ^_^";
    $balas = array(
        'replyToken' => $replyToken,
        'messages' => array(
            array(
                'type' => 'text',
                'text' => $text
            )
        )
    );
}
$date = new DateTime();
$tgl = $date->format('d-m');
$tg = $date->format('d-m-Y');
//$tgl ='15:00';
if ($tgl=='24-05' && $command=='/test'){
	$result = senin1();
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $result
                )
            )
        );
}

//pesan bergambar
if($message['type']=='text') {
	    if ($command == '/piket'
		||$command == 'piket'||$command == 'Piket') {

        $result = piket($options);
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $result
                )
            )
        );
    }
if ($command == '/date') {
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $tgl
                )
            )
        );
    }
	
}else if($message['type']=='sticker')
{	
	$balas = array(
							'replyToken' => $replyToken,														
							'messages' => array(
								array(
										'type' => 'text',									
										'text' => 'Makasih Kak Stikernya ^_^'										
									
									)
							)
						);
						
}
if (isset($balas)) {
    $result = json_encode($balas);
//$result = ob_get_clean();

    file_put_contents('./balasan.json', $result);


    $client->replyMessage($balas);
}
?>
