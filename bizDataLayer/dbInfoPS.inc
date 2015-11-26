<?php
//remember that dbInfoPS.inc looks like:
$mysqli=new mysqli('localhost','root','','test');             
if(mysqli_connect_errno()){
	printf("connection failed: ",mysqli_connect_errno());
	exit();
}
else{
	//global $logger->info('connect refused');
}
?>