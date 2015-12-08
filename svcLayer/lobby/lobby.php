<?php
global $logger;
//$logger->info("inside lobby.php");
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
	//$logger->info('in readChats, data '. readChatsDb());
	return readChatsDb($d);
}

function getOnlineUsers($d){
	global $logger;
	$logger->info('in getOnlineUsers');
	return getOnlineUsersDb();
}

function enterChallenge($d) {
	global $logger;
	$logger->info("inside enterChallenge toEmail ". ($d['toEmail']));
	$challengedUser=getUserByEmailDB($d['toEmail']);
	$challengeTo=$challengedUser[0]['iduser'];
	$logger->info("inside enterChallenge challengeUser". $challengeTo);
	$challengeFrom=$d['from'];
	return enterChallengeDB($challengeFrom,$challengeTo);
}

function getChallengeStatus($d){
	global $logger;
	$logger->info("inside getChallengeStatus() id:". $d['id']);
	return getChallengeStatusDB($d);
}

function getOpenChallenges($user){
	global $logger;
	$logger->info("inside getOpenChallenge() id:$user ");
	return getOpenChallengesDB($user);
}

