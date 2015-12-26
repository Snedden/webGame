<?php 

//users tables interactions
function insertUser($newUserData) {


	global $logger, $mysqli;
	$logger->debug("Inside insertUser() function.");
	 
    
   
	if ($mysqli == null) {
		$logger->error("Database is not setup property");
	} else {
		$logger->debug("The database is not null - OK");
	}

	//Check if email already exists
    $sql="select * from users where email=?";
    try{
    	/* Prepared statement, stage 1: prepare */
		if (!($stmt = $mysqli->prepare($sql))) {
    		 $logger->error("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
		}

		/* Prepared statement, stage 2: bind and execute */
		
		if (!$stmt->bind_param('s', $newUserData['email'])) {
		    $logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
		}

		if (!$stmt->execute()) {
		    $logger->error( "Execute failed: (" . $stmt->errno . ") " . $stmt->error);
		}
		

		$logger->info( "result user : ". $stmt->num_rows);


		if($stmt->fetch()){
			$logger->info(__FILE__.": User is not null.");
			//$logger->debug(__FILE__.": ".$user);
			$errorMsg = array(
	            'error' =>'Email Id already exist,forgot password?.'
	        );
	        $logger->info("user exist error as".$errorMsg);
	        return json_encode($errorMsg);
		} 

    }
    catch(Exception $ex){
     	$logger->error('failed to read user from db'.$mysqli->error);
     	return false;
    }
   


	//register user
	$sql = "INSERT INTO users
               (first_name,
                last_name,
                email,
                password,
                status,
                registration_date)
            VALUES
                (?,
                 ?,
                 ?,
                 ?,
                 ?,
                 ?)";

	try {
		$stmt = $mysqli->prepare($sql);
		$logger->info("inside try of insert user");
        
        $stmt->bind_param('ssssis', $newUserData['firstName'], $newUserData['lastName'], $newUserData['email'], $newUserData['password'],$newUserData['status'],$newUserData['registration_date']);
        $logger->info("v1".$newUserData['firstName']."v2".$newUserData['lastName']."v3".$newUserData['email']."v4".$newUserData['password']."v5".$newUserData['statusId']."v6".$newUserData['registration_date']);
		//$logger->info('stmt:'.$sql.'result:'.$stmt->execute().'error:'.$mysqli->error);
		if ($stmt->execute()) {
			$newUserId = $mysqli->insert_id;
			$logger->debug("A user with id " . $newUserId . " was created.");
			$stmt->close();
			$mysqli->close();
			$logger->info("user inserted");
			return true;
		} else {
			$logger->error("Could not insert a record into db.");
			$stmt->close();
			$mysqli->close();
			return false;
		}
	} catch (Exception $ex) {
		$logger->error("An error occurred when trying to run a query.");
		$logger->error($ex->getMessage());
		$stmt->close();
		$mysqli->close();
		return false;
	}

	
}

function logOutDB($userId){
	global $logger,$mysqli;
	//$logger->info("Insidd logOutDB with idUser:".$userId);
	global $mysqli;
	$sql="update  users set status=0 WHERE idUser=?";
	try{
		if($stmt=$mysqli->prepare($sql)){

			if(!$stmt->bind_param("s",$userId)){
				$logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
				//$logger->error( "Execute failed logoutDB: (" . $stmt->errno . ") " . $stmt->error);
			}

			$mysqli->close();
			return true;
		}else{
			//$logger->error("An error occured in prepare statement logoutDB".$mysqli->error);
			throw new Exception("An error occurred in getUser");
			return false;
		}
	}catch (Exception $e) {
		//$logger->error("An error occured in logoutDB".$mysqli->error);
		return false;
	}
}

function makeOnlineDB($userId){
	global $logger,$mysqli;
	//$logger->info("Insidd makeOnline with idUser:".$userId);
	global $mysqli;
	$sql="update  users set status=1 WHERE idUser=?";
	try{
		if($stmt=$mysqli->prepare($sql)){

			if(!$stmt->bind_param("s",$userId)){
				//$logger->error( "Binding parameters failed makeONlineDb: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
				//$logger->error( "Execute failed makeonlineDB: (" . $stmt->errno . ") " . $stmt->error);
			}

			$mysqli->close();
			return true;
		}else{
			//$logger->error("An error occured in prepare statement makeOnline".$mysqli->error);
			throw new Exception("An error occurred in getUser");
			return false;
		}
	}catch (Exception $e) {
		//$logger->error("An error occured in makeonlineDB".$mysqli->error);
		return false;
	}
}


function getUserByIdDB($userId){
	global $logger,$mysqli;
	//$logger->info("Insidd getUserByIdDB with idUser:".$userId);
	global $mysqli;
	$sql="SELECT first_name,last_name FROM users WHERE idUser=?";
	try{
		if($stmt=$mysqli->prepare($sql)){

			$stmt->bind_param("s",$userId);

			$data=bindSql($stmt);

			$mysqli->close();
			return $data;
		}else{
			$logger->error("An error occured in prepare statement getUser".$mysqli->error);
			throw new Exception("An error occurred in getUser");
		}
	}catch (Exception $e) {
		//$logger->error("An error occured in getUser".$mysqli->error);
		return false;
	}
}










?>