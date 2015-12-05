<?php
global $logger;
$logger->info("inside user.php");
require_once("./bizDataLayer/commonDbFunctions.php");
require_once("./bizDataLayer/lobbyDbBiz.php");
require "./bizDataLayer/dbInfoPS.inc";//to use we need to put in: global $mysqli;

function enterChat($d,$ip,$token){
	global $logger;
 	$logger->info("inside enterChat  data". implode(" ", $d));
	 $gump = new GUMP();
   // $logger->info("gump:".$gump);
    $cleanChat = $gump->sanitize($d); // 

	enterChatDb($cleanChat);
}

function readChats($d,$ip,$token){
	global $logger;
	$logger->info('in readChats, data '. readChatsDb());
	return readChatsDb($d);
}

