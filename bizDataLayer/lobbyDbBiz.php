<?php



 global $logger;
 $logger->info('inside lobbyDbBiz'.__FILE__);
 function enterChatDb($d){
 	global $logger,$mysqli;

 	$logger->info('inside enterChatDb data'. implode("",$d));

 	if ($mysqli == null) {
		$logger->error("Database is not setup property");
	} else {
		$logger->debug("The database is not null - OK");
	}

	//enter chatMessage
	//register user
	$sql = "INSERT INTO chatMessages
               (iduser,
                text
                )
            VALUES
                (?,
                 ?)";
	$newUserId;
	try {
		
		$logger->info("inside try of chatEnter");
        
		if (!($stmt = $mysqli->prepare($sql))) {
    		 $logger->error("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
		}

		if (!$stmt->bind_param('is',  $d['userName'], $d['chatMsg'])) {
		    $logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
		}

       
         $logger->info("v1".$d['userName']."v2".$d['chatMsg']);
		//$logger->info('stmt:'.$sql.'result:'.$stmt->execute().'error:'.$mysqli->error);
		if ($stmt->execute()) {
			
			$logger->info("chat inserted");
			return true;

		} 
		else {
			$logger->error("Could not insert a chat into db.".$mysqli->error);
			return false;
		}
	} catch (Exception $ex) {
		$logger->error("An error occurred when trying to run a query.");
		return false;
	}
	finally{
		$stmt->close();
		$mysqli->close();
	}



 }

 function readChatsDb($d){
	global $logger,$mysqli;
	$logger->info('inside readChatsDb()');
	
	$sql="select text,iduser,TIMESTAMP from chatMessages where TIMESTAMP >?";

	try{
		if($stmt=$mysqli->prepare($sql)){
			
			$logger->info("prepared statement is good in read chat");

			if (!$stmt->bind_param( $d['lastTimeStamp']) {
				$logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			$data=bindSql($stmt);
			$logger->info("result for bindSql" . implode(" ",$data[0]));
			
			
			return json_encode($data);
		}else{
			$logger->error("An error occured in prepare statement readChatsDb".$mysqli->error);
			throw new Exception("An error occurred in getUser");
		}
	}
	catch (Exception $e) {
        $logger->error("An error occured in getUser".$mysqli->error);
		return false;
    }

    finally{

    }
}

