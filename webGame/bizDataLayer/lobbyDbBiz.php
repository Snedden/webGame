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

function getOnlineUsersDb($userId){
	global $logger,$mysqli;
	$logger->info('inside getOnlineUsersDb');

	if ($mysqli == null) {
		$logger->error("Database is not setup property");
	}

	$sql="select first_name,last_name,email from users where status=1 and iduser<>?";

	try{
		if($stmt=$mysqli->prepare($sql)){

			if (!$stmt->bind_param('i', $userId)) {
				$logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			}

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
	$sql="SELECT status,game_id FROM challenges WHERE challenge_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){

			$stmt->bind_param("i",$chlgnId);

			$data=bindSql($stmt);
            $logger->info('return data from getChallengeStatusDb sql '.$data[0]['status']);
			//$mysqli->close();
			$formatData=[
					'chlgnid'=>$chlgnId,
					'status'=>$data[0]['status'],
				    'game_id'=>$data[0]['game_id']
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


function getSentChallengesDB($user){
	global $logger,$mysqli;
	$logger->info('inside getOpenChallengessDB');

	if ($mysqli == null) {
		$logger->error("Database is not setup property");
	}

	$sql="
		  Select distinct u.email,c.challenge_id,c.game_id,c.status,c.timestamp
			from
				(select ch.challenge_id,ch.game_id,ch.status,ch.from_user,ch.to_user,timestamp, (select Max(timestamp) from challenges
				 where to_user=ch.to_user and from_user=?) as latest
				 from test.challenges ch  ) c
			join test.users u
			on
			 	c.to_user=u.iduser
			and
		  	c.latest=c.timestamp
		  ";   ///get most recent challenge

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
			$logger->info('something went wrog in getSentChallenge');
	}
}
//update challenge status to 'met'
function metChallengesDB($id){
	global $logger,$mysqli;
	$logger->info('inside getOpenChallengessDB');

	$sql="update  challenges set status='met' where challenge_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("i",$id);
			$stmt->execute();
			$stmt->close();

			//get gameid to return
			$sql="select game_id from challenges where challenge_id=?";
			try{
				if($stmt=$mysqli->prepare($sql)){
					$stmt->bind_param("i",$id);
					$data=bindSql($stmt);

					$game_id=$data[0]['game_id'];
					return $game_id;
				}
				else{
					$logger->error('Somthing went wrong in prepare statement of game in metchallnge'.$mysqli->error);
				}
			}catch(Excpetino $e){
				$logger->error('Something went wrong in getting status of the game  in startData');
			}
		}
		else{
			$logger->info('Something went wrong while preparing statement metChallngeDb'.$mysqli->error);
			return false;
		}

	}catch(Exception $e){
		$logger->info('Something went wrong while updating status of challenges in metChallengeDB');
		return false;
	}
}
function rejectChallengeDB($id){
	global $logger,$mysqli;
	//$logger->info('inside rejectChallengessDB');
	$sql="update challenges set status='rejected' where challenge_id=?";

	try{
		if($stmt=$mysqli->prepare($sql)){

			if (!$stmt->bind_param('i',$id)) {
				//$logger->error( "Binding parameters failed: " . $mysqli->error );
			}

			if ($stmt->execute()) {
				//$logger->info("challenge updated to rejected");
				return true;
			}
			else {
				//$logger->error("Could not update a challenge into db.".$mysqli->error);
				return false;
			}
		}
		else{
			//$logger->error("An error occured in prepare statementget rejectChallengeDB".$mysqli->error);
		}
	}
	catch (Exception $e) {
		//$logger->error("An error occured in rejectChallengeDB".$mysqli->error);
		return false;
	}
}

function acceptChallengeDB($id){
	global $logger,$mysqli;
	//$logger->info('inside acceptChallengessDB');

	if ($mysqli == null) {
		//$logger->error("Database is not setup property");
	}
	//get first_name of challenger and challengee
	$sql="SELECT first_name
			FROM test.Users
			WHERE iduser IN
				(SELECT to_user
				   FROM test.challenges
				   WHERE Challenge_Id = ?
				  UNION
				   SELECT from_user
				   FROM test.challenges
				   WHERE Challenge_Id = ?)";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("ii",$id,$id);

			$data=bindSql($stmt);
			//$logger->info('return firstnames from acceptChallange sql '.print_r($data,true).''. $data[0]['first_name'].''.$data[1]['first_name'] );
            $challenger= $data[1]['first_name'];
			$challengee=$data[0]['first_name'];

			//insert a new game in games table
			$sql="insert into heroes_games(player0_name,player1_name,status) values(?,?,'started')";

			try{
				if($stmt=$mysqli->prepare($sql)){
					if(!$stmt->bind_param("ss",$challenger,$challengee)){
						//$logger->info("something went wrong in biding params in accept challenge".$mysqli->error);
					}

					if ($stmt->execute()) {
						$newGameId = $mysqli->insert_id;
						//$logger->debug("A game with id " . $newGameId . " was created.");

						//$logger->info("game inserted");
                        //update challenge status to accepted
						$sql="update test.challenges set status='accepted',game_id=?  where challenge_id=?";
						try{
							if($stmt=$mysqli->prepare($sql)){

								if (!$stmt->bind_param('ii',$newGameId,$id)) {
									//$logger->error( "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
								}

								if ($stmt->execute()) {
									//$logger->info("challenge updated to accepted");

									return $newGameId;  //return the new game id that was created
								}
								else {
									//$logger->error("Could not update a challenge into db.".$mysqli->error);
									return false;
								}


							}else{
								//$logger->error("An error occured in prepare statementget acceptChallengeDB".$mysqli->error);

							}
						}
						catch (Exception $e) {
							//$logger->error("An error occured in acceptChallengeDB".$mysqli->error);
							return false;
						}

					} else {
						//$logger->error("Could not insert a record into game db.");
						//$stmt->close();
						//$mysqli->close();
						return false;
					}
				}
				else{
					//$logger->info("Something went wrong while preparing statment insert new game ".$mysqli->error);
					return false;
				}
			}
			catch(Exception $e){
				//$logger->info("something went wrong while inserting new game");
				return false;
			}

		}
		else{
			//$logger->info('Error in preparing sql statement at acceptChallengeDB'. $mysqli->error);
			return false;
		}
	}
	catch(Exception $e){
		//$logger->info('something went wrog in acceptChallenge');
	}
}

