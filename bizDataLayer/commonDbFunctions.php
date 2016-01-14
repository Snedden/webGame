<?php

function bindSql ($stmt){
	//global $logger;
	//$logger->info("Inside bind sql");

	$stmt->execute();
	$stmt->store_result();
 	$meta = $stmt->result_metadata();
    $bindVarsArray = array();
	//using the stmt, get it's metadata (so we can get the name of the name=val pair for the associate array)!
	while ($column = $meta->fetch_field()) {
    	$bindVarsArray[] = &$results[$column->name];
    }
    //$logger->info('bind columns'. implode(" ",array_keys($bindVarsArray)));
	//bind it!
	call_user_func_array(array($stmt, 'bind_result'), $bindVarsArray);
	//now, go through each row returned,
	while($stmt->fetch()) {
    	$clone = array();
        foreach ($results as $k => $v) {
        	$clone[$k] = $v;
        }
     //$logger->info('clone '.implode(',', array_values($clone)));   
        $data[] = $clone;
    }
  
	
    return $data;
}

function getUserByEmailDB($emailId){

	//$logger->info("Insidd getUser with emailID:".$emailId);
	global $mysqli;
	$sql="SELECT * FROM users WHERE email=?";
	try{
		if($stmt=$mysqli->prepare($sql)){

			$stmt->bind_param("s",$emailId);

			$data=bindSql($stmt);

			//$mysqli->close();
			return $data;
		}else{
			//$logger->error("An error occured in prepare statement getUser".$mysqli->error);
			throw new Exception("An error occurred in getUser");
		}
	}catch (Exception $e) {
		//$logger->error("An error occured in getUser".$mysqli->error);
		return false;
	}
}

function updateUsersLastActivityDB($userId){
	global $mysqli;

	if ($mysqli == null) {
		//$logger->error("Database is not setup property");
	} else {
		//$logger->debug("The database is not null - OK");
	}

	$sql="update users set last_activity=now() where iduser=? ";

	try{
		if($stmt=$mysqli->prepare($sql)){

			//$logger->info("prepared statement is good in updateLastLogin");

			if (!$stmt->bind_param( 'i',$userId)) {
				//$logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if ($stmt->execute()) {

				//$logger->info("last activity updated");
				return true;
			}
			else{
				//$logger->info("Execute failed in last acotivity update");
				return false;
			}
		}else{
			//$logger->error("An error occured in prepare statement readChatsDb".$mysqli->error);
			//throw new Exception("An error occurred in getUser");
			return false;
		}
	}
	catch(Expection $e){
		return false;
	}

}