<?php



 global $logger;
 //$logger->info('inside lobbyDbBiz'.__FILE__);
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

	try {
		
		$logger->info("inside try of chatEnter");
        
		if (!($stmt = $mysqli->prepare($sql))) {
    		 $logger->error("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
		}

		if (!$stmt->bind_param('is',  $d['userId'], $d['chatMsg'])) {
		    $logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
		}

       
         $logger->info("v1".$d['userId']."v2".$d['chatMsg']);
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
	//$logger->info('inside readChatsDb()');

	 if ($mysqli == null) {
		 $logger->error("Database is not setup property");
	 }

	$sql="SELECT u.first_name,u.last_name,c.text,c.timestamp
			FROM test.chatmessages c
			LEFT OUTER JOIN test.users u
			ON u.iduser=c.iduser
			order by c.timestamp";

	try{
		if($stmt=$mysqli->prepare($sql)){
			
			//$logger->info("prepared statement is good in read chat");
			/*
			if (!$stmt->bind_param( $d['lastTimeStamp'])) {
				$logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}
			*/
			$data=bindSql($stmt);
			//$logger->info("result for bindSql" . implode(" ",$data[0]));
			
			
			return json_encode($data);
		}else{
			$logger->error("An error occured in prepare statement readChatsDb".$mysqli->error);
			throw new Exception("An error occurred in getUser");
		}
	}
	catch (Exception $e) {
        $logger->error("An error occured in readChatDB".$mysqli->error);
		return false;
    }

    finally{

    }
}

function getOnlineUsersDb(){
	global $logger,$mysqli;
	$logger->info('inside getOnlineUsersDb');

	if ($mysqli == null) {
		$logger->error("Database is not setup property");
	}

	$sql="select first_name,last_name,email from users where status=1";

	try{
		if($stmt=$mysqli->prepare($sql)){


			$data=bindSql($stmt);
			$logger->info("result for bindSql" . implode(" ",$data[0]));


			return json_encode($data);
		}else{
			$logger->error("An error occured in prepare statementget OnlineUsersDb".$mysqli->error);

		}
	}
	catch (Exception $e) {
		$logger->error("An error occured in getOnlineUsersDbr".$mysqli->error);
		return false;
	}

	finally{

	}




}

function enterChallengeDB($from,$to){
	global $logger,$mysqli;
	$logger->info('inside getOnlineUsersDb');

	if ($mysqli == null) {
		$logger->error("Database is not setup property");
	}

	//check if there are already any open challeges from this user
	$sql="select challenge_id from challenges where status='open' and from_user=? and to_user=? ";
	try{
		if($stmt=$mysqli->prepare($sql)){
			if (!$stmt->bind_param('ii',  $from, $to)) {
				$logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if (!$stmt->execute()) {
				$logger->info("could not excecute getOpen challenges check");
			}
			else {
				if($stmt->fetch()){
					$logger->info(__FILE__.": There are open challenges ");

					$errorMsg = array(
							'error' =>'You have already sent a challenge to this user.'
					);
					$logger->info("user exist error as".$errorMsg);
					return json_encode($errorMsg);
				}
			}



		}
		else{
			$logger->info("Error in prepapring statement in checking open challenges ,EnterChallengeDB()");
		}
	}
	catch (Exception $e){
		$logger->info("Something went wrong in checking open challenges");
	}

    //insert challenge in DB
	$sql="insert into challenges
		  (from_user,
		  to_user,
		  status)
		  values(
		  ?,
		  ?,
		  'open') ";

	try{
		if($stmt=$mysqli->prepare($sql)){

			if (!$stmt->bind_param('ii',  $from, $to)) {
				$logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

			if ($stmt->execute()) {
				$logger->info("challenge inserted");

				$data=[
					'chlgnid'=>$mysqli->insert_id,
					'status'=>'open'
				];
				return  json_encode($data);
			}
			else {
				$logger->error("Could not insert a challenge into db.".$mysqli->error);
				return false;
			}


			return json_encode($data);
		}else{
			$logger->error("An error occured in prepare statementget enterChallengeDB".$mysqli->error);

		}
	}
	catch (Exception $e) {
		$logger->error("An error occured in enterChallengeDB".$mysqli->error);
		return false;
	}

	finally{

	}
}


function getChallengeStatusDB($d){
	global $logger,$mysqli;
	$logger->info('inside getChallengeStatusDB');

	if ($mysqli == null) {
		$logger->error("Database is not setup property");
	}
    $chlgnId=$d['id'];
	$sql="SELECT status FROM challenges WHERE challenge_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){

			$stmt->bind_param("i",$chlgnId);

			$data=bindSql($stmt);
            $logger->info('return data from getChallengeStatusDb sql '.$data[0]['status']);
			//$mysqli->close();
			$formatData=[
					'chlgnid'=>$chlgnId,
					'status'=>$data[0]['status']
			];
			return json_encode($formatData);
		}else{
			$logger->error("An error occured in prepare statement getChallengeStatus".$mysqli->error);
			throw new Exception("An error occurred in getUser");
		}
	}catch (Exception $e) {
		$logger->error("An error occured in getChallengeStatus".$mysqli->error);
		return false;
	}



}

function getOpenChallengesDB($user){
	global $logger,$mysqli;
	$logger->info('inside getOpenChallengessDB');

	if ($mysqli == null) {
		$logger->error("Database is not setup property");
	}

	$sql="Select distinct u.email,c.challenge_id from test.challenges c join test.users u on c.from_user=u.iduser
			where c.to_user=? and c.status='open'
			Order by c.timestamp DESC";   ///get all open challegnes and order them with latest first

	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("i",$user);

			$data=bindSql($stmt);
			$logger->info('return data from getOpenChallengesDB sql '.array_keys($data[0]));

			return json_encode($data);
		}
		else{
			$logger->info('Error in preparing sql statement at getOpenChallengeDB'. $mysqli->error);
		}
	}
	catch(Exception $e){

	}
}

