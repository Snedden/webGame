<?php 

function insertUser($newUserData) {
	global $logger, $mysqli;
	$logger->debug("Inside insertUser() function.");
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
?>