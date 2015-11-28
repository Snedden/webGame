<?php 

function insertUser($newUserData) {


	global $logger, $mysqli;
	$logger->debug("Inside insertUser() function.");
	 //Check if email already exists
    $user=getUser($newUserData['email']);
    $logger->info("got user from getUser".$user);

	
	if ($mysqli == null) {
		$logger->error("Database is not setup property");
	} else {
		$logger->debug("The database is not null - OK");
	}
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
	$newUserId;
	try {
		$stmt = $mysqli->prepare($sql);
		
        
        $stmt->bind_param('ssssis', $newUserData['firstName'], $newUserData['lastName'], $newUserData['email'], $newUserData['password'],$newUserData['status'],$newUserData['registration_date']);
        $logger->info("v1".$newUserData['firstName']."v2".$newUserData['lastName']."v3".$newUserData['email']."v4".$newUserData['password']."v5".$newUserData['statusId']."v6".$newUserData['registration_date']);
		$logger->info('stmt:'.$sql.'result:'.$stmt->execute().'error:'.$mysqli->error);
		if ($stmt->execute()) {
			$newUserId = $mysqli->insert_id;
			$logger->debug("A user with id " . $newUserId . " was created.");
		} else {
			$logger->error("Could not insert a record into db.");
		}
	} catch (Exception $ex) {
		$logger->error("An error occurred when trying to run a query.");
		$logger->error($ex->getMessage());
	}

	$stmt->close();
	$mysqli->close();
}

function getUser($emailId){
	global $logger,$mysqli;
	$logger->info("Insidd getUser with emailID:".$emailId);
	global $mysqli;
	$sql="SELECT * FROM users WHERE email=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$logger->info("Inside if email:".$emailId);
			$stmt->bind_param("s",$emailId);
			$logger->info("Inside if email:".$emailId);
			$data=returnJson($stmt);
			$logger->info("Inside if data:".$emailId);
			$mysqli->close();
			return $data;
		}else{
			$logger->error("An error occured in getUser".$mysqli->error);
			throw new Exception("An error occurred in getUser");
		}
	}catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
}
?>