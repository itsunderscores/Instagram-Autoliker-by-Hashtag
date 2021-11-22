<?php

ob_start();
error_reporting(0);
set_time_limit(1200000);
ini_set('max_execution_time', 1200000);
include 'extra.php';

/*
Developed by Underscores
Discord: underscores#0001
This PHP file is mainly ran on console/terminal.
Syntax: php liker.php photography 10

Go to Instagram, sign into your account, and copy your request headers and paste them into file "headers.txt"
*/

clearStdin(); //Shitty way to clearn form on Windows lol

//User Preferences
$tag = $_GET['tag'];
if($tag == null) {
	$tag = $argv[1];
	if($tag == null) {
		echo $red."[*] Enter a tag!".$white; die();
	}
}
$user_delay = $argv[2]; //User inputs this amount if they want to delay
if($user_delay == null) { $user_delay = rand(10,18); $user_delay1 = "Randomized"; } else { $user_delay1 = $user_delay; }


echo $message;
$rt = grab_feed($tag); //Fetch Data
$data = json_decode($rt, true); //Decode JSON
$next_max = $data['data']['recent']['next_max_id']; //Next Max ID to scrape more images
$array = array(""); //Setting blank array




/* Grab Top Posts (Should be 3 columns, 3 rows) */
$_GET['top'] = "true"; $_GET['recent'] = "true";
if($_GET['top'] == "true") {
	echo $yellow."-----------------------------\n[+] Grabbing Top Posts\n-----------------------------\n".$white;
	$events = $data['data']['top']['sections'];
	foreach($events as $key=>$value)
	{	
		//Section One
		$pk_1 = $value['layout_content']['medias'][0]['media']['pk'];
		$username_1 = $value['layout_content']['medias'][0]['media']['user']['username'];
		array_push($array, $pk_1);
		
		//Section Two
		$pk_2 = $value['layout_content']['medias'][1]['media']['pk'];
		$username_2 = $value['layout_content']['medias'][1]['media']['user']['username'];
		array_push($array, $pk_2);
		
		//Section Three
		$pk_3 = $value['layout_content']['medias'][2]['media']['pk'];
		$username_3 = $value['layout_content']['medias'][2]['media']['user']['username'];
		array_push($array, $pk_3);
	}
	echo $green . "[-] Picture Queue: ". count($array) ."\n". $white;
}







/* Grab Recent Posts (Should be 10 columns, 3 rows) */
if($_GET['recent'] == "true") {
	echo $yellow . "\n-----------------------------\n[-] Grabbing Recent Posts\n-----------------------------\n".$white;
	$events = $data['data']['recent']['sections'];
	foreach($events as $key=>$value)
	{	

		//Section One
		$pk_1 = $value['layout_content']['medias'][0]['media']['pk'];
		$username_1 = $value['layout_content']['medias'][0]['media']['user']['username'];
		array_push($array, $pk_1);
		
		//Section Two
		$pk_2 = $value['layout_content']['medias'][1]['media']['pk'];
		$username_2 = $value['layout_content']['medias'][1]['media']['user']['username'];
		array_push($array, $pk_2);
		
		//Section Three
		$pk_3 = $value['layout_content']['medias'][2]['media']['pk'];
		$username_3 = $value['layout_content']['medias'][2]['media']['user']['username'];
		array_push($array, $pk_3);
	}
	echo $green . "[-] Picture Queue: ". count($array) ."\n". $white;
	ob_flush(); flush();
}








/* Grab next couple of pages as well */
echo $yellow."\n-----------------------------\n[-] Grabbing more IDs...\n-----------------------------\n".$white;
ob_flush(); flush();
$current_page = 1;
for ($x = 1; $x <= 1; $x++) {
	
	//Grab Max ID from array
	if($x > 1) {
		foreach($array2 as $item) {
			$next_max = $item;
		}
		unset($array2); //Reset
		$array2 = array("");
	} else {
		$array2 = array("");
	}
	
	$more_data = grab_new_ids($tag, $next_max, $x);
	$data = json_decode($more_data, true); //Decode JSON
	$current_page++;
	
	//Writing new Max ID to file
	$new = $data['next_max_id'];
	if($new == null) { echo "Could not grab new max id on the first step!"; exit(); die(); }
	
	array_push($array2, $new);
	
	//Grab Recent Posts (Should be 10 columns, 3 rows)
	$events = $data['sections'];
	foreach($events as $key=>$value)
	{	
		//Section One
		$pk_1 = $value['layout_content']['medias'][0]['media']['pk'];
		$username_1 = $value['layout_content']['medias'][0]['media']['user']['username'];
		array_push($array, $pk_1);
		
		//Section Two
		$pk_2 = $value['layout_content']['medias'][1]['media']['pk'];
		$username_2 = $value['layout_content']['medias'][1]['media']['user']['username'];
		array_push($array, $pk_2);
		
		//Section Three
		$pk_3 = $value['layout_content']['medias'][2]['media']['pk'];
		$username_3 = $value['layout_content']['medias'][2]['media']['user']['username'];
		array_push($array, $pk_3);
		
		//echo "$pk_1 ($username_1) - $pk_2 ($username_2) - $pk_3 ($username_3)";
		//ob_flush(); flush();
		
	}
	echo $green . "[-] Picture Queue: ". count($array) ."\n". $white;
	ob_flush(); flush();
}




/* Start Liking Images */
$total_liked = 0;
echo $red."\n-----------------------------\n[!] Starting Likes\n-----------------------------\n".$white;
$likes = 0;
foreach($array as $item) {
	if($item != null) {
		$picture_id = $item;
		$send_like = like($picture_id);
		
		//Check status of liked image
		$decode_like = json_decode($send_like, true);
		if($decode_like['status'] == "ok") {
			$total_liked++;
			echo "[+] Successfully liked picture $picture_id [". $likes + 1 ."/".count($array)."] [$total_liked] \n";
			$likes++;
		} else {
			echo "\n[!] Unexpected response from Instagram: ".$decode_like['status']." Sleeping for 5 minutes...\n";
			ob_flush(); flush();
			Sleep(300);
		}
		
		//Flush
		ob_flush(); flush();
		
		//Check how many likes have been sent so far
		if($likes % 50 == 0) {
			$delay = rand(30,60);
			echo "[*] Waiting $delay seconds before continuing...\n"; ob_flush(); flush();
			sleep($delay); //Delay
		} else {
			sleep($user_delay); //Sleep for random seconds
		}
		
		//ob_flush(); flush();
	}
}








/* Scrape More Images, run in loop from here... */
while (true) {
    
echo $yellow."\n-----------------------------\n[!] Scraping More Images ($current_page)\n-----------------------------\n".$white;
	ob_flush(); flush();
	
    //Scrape more images
    unset($array); //Reset
    $likes = 0; //Reset
    $array = array(""); //Setting blank array
    
    //Grab Max ID from array
    if ($current_page > 1) {
        foreach ($array2 as $item) {
            $next_max = $item;
        }
        //unset($array2); //Reset
        //$array2 = array("");
    }
    
    for ($x = 1; $x <= 3; $x++) {
	    $more_data = grab_new_ids($tag, $next_max, $current_page);
	    $data      = json_decode($more_data, true);
	    $current_page++;
	    
	    //Writing new Max ID to file
	    $new = $data['next_max_id'];
		if($new == null) 
		{ 
			echo "Could not grab new max id on the first step! Sleeping for 10 minutes and will retry again.\n";
			sleep(600);
		} else {
			array_push($array2, $new);
			unset($array2); //Reset
	        $array2 = array("");
		}
	}
    
    //Grab Recent Posts (Should be 10 columns, 3 rows)
    $events = $data['sections'];
    foreach ($events as $key => $value) {
        //Section One
        $pk_1       = $value['layout_content']['medias'][0]['media']['pk'];
        $username_1 = $value['layout_content']['medias'][0]['media']['user']['username'];
        array_push($array, $pk_1);
        
        //Section Two
        $pk_2       = $value['layout_content']['medias'][1]['media']['pk'];
        $username_2 = $value['layout_content']['medias'][1]['media']['user']['username'];
        array_push($array, $pk_2);
        
        //Section Three
        $pk_3       = $value['layout_content']['medias'][2]['media']['pk'];
        $username_3 = $value['layout_content']['medias'][2]['media']['user']['username'];
        array_push($array, $pk_3);
        
        //echo "$pk_1 ($username_1) - $pk_2 ($username_2) - $pk_3 ($username_3) \n";
		//ob_flush(); flush();
    }
    echo $green . "[-] Picture Queue: ". count($array) ."\n". $white;
    ob_flush(); flush();
    
    //echo $red."\n-----------------------------\n[!] Starting Likes\n-----------------------------\n".$white;

    $likes = 0;
    foreach ($array as $item) {
        if ($item != null) {
            $picture_id = $item;
            $send_like  = like($picture_id);
            
            //Check status of liked image
            $decode_like = json_decode($send_like, true);
            if ($decode_like['status'] == "ok") {
				$total_liked++;
				echo "[+] Successfully liked picture $picture_id [". $likes + 1 ."/".count($array)."] [$total_liked] \n";
                $likes++;
            } else {
				echo "\n[!] Unexpected response from Instagram: ".$decode_like['status']." Sleeping for 5 minutes...\n";
				ob_flush(); flush();
				Sleep(300);
            }
            
            //Flush
            ob_flush(); flush();
            
            //Check how many likes have been sent so far
			if($likes % 50 == 0) {
				$delay = rand(30,60);
				echo "[*] Waiting $delay seconds before continuing...\n"; ob_flush(); flush();
				ob_flush(); flush();
				sleep($delay); //Delay
			} else {
				sleep($user_delay); //Sleep for random seconds
			}
            
            //echo str_pad('', 4096) . "\n"; ob_flush(); flush();
        }
    }

    //Sleep before getting new list.
    sleep(rand(30,60));
}
?>
