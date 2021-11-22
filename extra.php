<?php
$green = "\e[92m";
$white = "\e[97m";
$red = "\e[95m";
$yellow = "\e[93m";
$blue = "\e[34m";

function clearStdin()
{
    for ($i = 0; $i < 50; $i++) { echo "\r\n"; }
}

$message = $red."-----------------------------".$yellow."
Instagram Liker v1
Developed by Underscores
Syntax: [TAG] [DELAY PER LIKE]
".$red."-----------------------------".$yellow."
Tag Entered: $tag
Delay (Seconds): $user_delay1
".$red."-----------------------------\n\n".$white;

function fix_headers() {
	//Fix Headers
	$rows = file("headers.txt");    
	$blacklist = "gzip|content-length";
	foreach($rows as $key => $row) {
		if(preg_match("/($blacklist)/", $row)) {
			unset($rows[$key]);
		}
	}

	file_put_contents("headers.txt", implode("\n", $rows));
	$file = file_get_contents('headers.txt');
	$file = str_replace("\n", "", $file);
	file_put_contents('headers.txt', $file);
}
fix_headers();

function grab_feed($tag) {
	
	$headers1 = array("");
	$headers = file_get_contents('headers.txt');
	$headers1 = preg_split("/\r\n|\n|\r/", $headers);
	//var_dump($headers1);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://www.instagram.com/explore/tags/'.$tag.'/?__a=1');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$rt = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close ($ch);
	return $rt;
}

function grab_new_ids($tag, $max_id, $page) {
	
	$headers1 = array("");
	$headers = file_get_contents('headers.txt');
	$headers1 = preg_split("/\r\n|\n|\r/", $headers);
	//var_dump($headers1);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://i.instagram.com/api/v1/tags/'.$tag.'/sections/');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'include_persistent=0&max_id='.$max_id.'&page='.$page.'&surface=grid&tab=recent');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$rt = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close ($ch);
	return $rt;
}

function like($id) {
	
	$headers1 = array("");
	$headers = file_get_contents('headers.txt');
	$headers1 = preg_split("/\r\n|\n|\r/", $headers);
	//var_dump($headers1);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://www.instagram.com/web/likes/'.$id.'/like/');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, '');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$rt = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close ($ch);
	return $rt;
}
?>
