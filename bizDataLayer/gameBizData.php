<?php
//include exceptions
require_once('./bizDataLayer/exception.php');

//if we have gotten here - we know:
//-they have permissions to be here
//-we are ready to do something with the database
//-method calling these are in the svcLayer
//-method calling specific method has same name droping 'Data' at end checkTurnData() here is called by checkTurn() in svcLayer

//remember that dbInfoPS.inc looks like:
/*
$mysqli=new mysqli("localhost","yourUsername","yourPass",'yourUsername');             
if(mysqli_connect_errno()){
	printf("connection failed: ",mysqli_connect_errno());
	exit();
}
*/


/*************************
	startData
	
*/
function startData($gameId){
	//$logger->info('startData in gameBizData.php called with parameter'.$gameId);
	global $mysqli;
	//return $gameId.'sdf';
	//simple test for THIS 'game' - resets the last move and such to empty
	$sql = "UPDATE heroes_games SET player0_pieceID=null, player0_boardI=null, player0_boardJ=null,player0_attacking=0, player1_pieceID=null, player1_boardI=null, player1_boardJ=null,player1_attacking=0 WHERE game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			//bind parameters for the markers (s - string, i - int, d - double, b - blob)
			$stmt->bind_param("i",$gameId);
			$stmt->execute();
			$stmt->close();
		}else{
        	throw new Exception("An error occurred while setting up data");
        }
	}catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
	//get the init of the game
	$sql = "SELECT * FROM heroes_games WHERE game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			//bind parameters for the markers (s - string, i - int, d - double, b - blob)
			$stmt->bind_param("i",$gameId);
			$data=returnJson($stmt);
			$mysqli->close();
			return $data;
		}else{
            throw new Exception("An error occurred while fetching record data");
        }
	}catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
}
/*************************
	checkTurnData
*/
function checkTurnData($gameId){
	global $mysqli;
	$sql="SELECT whoseTurn FROM heroes_games WHERE game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("i",$gameId);
			$data=returnJson($stmt);
			$mysqli->close();
			return $data;
		}else{
        	throw new Exception("An error occurred while checking turn");
        }
    }catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
}
/*************************
	changeTurnData
*/
function changeTurnData($gameId){
	global $mysqli;
	//ugly, but toggle the turn (if the turn was 0, then make it 1, else make it 0)
	try{
		if($stmt=$mysqli->prepare("UPDATE heroes_games SET whoseTurn='2' WHERE game_id=? AND whoseTurn='0'")){
			$stmt->bind_param("i",$gameId);
			$stmt->execute();
			$stmt->close();
		}else{
        	throw new Exception("An error occurred while changing turn, step 1");
        }
		if($stmt=$mysqli->prepare("UPDATE heroes_games SET whoseTurn='0' WHERE game_id=? AND whoseTurn='1'")){
			$stmt->bind_param("i",$gameId);
			$stmt->execute();
			$stmt->close();
		}else{
        	throw new Exception("An error occurred while changing turn, step 2");
        }
		if($stmt=$mysqli->prepare("UPDATE heroes_games SET whoseTurn='1' WHERE game_id=? AND whoseTurn='2'")){
			$stmt->bind_param("i",$gameId);
			$stmt->execute();
			$stmt->close();
		}else{
        	throw new Exception("An error occurred while changing turn, step 3");
        }
	}catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
	$mysqli->close();
}
/*************************
	changeBoardData 38~dragon|1|0~32~32~0
*/
function changeBoardData($gameId,$pieceId,$boardI,$boardJ,$playerId,$isAttack){

	//update the board
	global $mysqli;
	global $logger;

	$logger->info('inside game SVC changeBoardData');
	$sql="UPDATE heroes_games SET player".$playerId."_pieceId=?, player".$playerId."_boardI=?, player".$playerId."_attacking=".$isAttack.", player".$playerId."_boardJ=? WHERE game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("siii",$pieceId,$boardI,$boardJ,$gameId);
			$stmt->execute();
			$stmt->close();
		}else{
			$logger->info('error while inserting change board data');
        	throw new Exception("An error occurred while changeBoard");
        }
	}catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
	$mysqli->close();
}
/*************************
	getMoveData
*/
function getMoveData($gameId){
	global $mysqli;
	$sql="SELECT * FROM heroes_games WHERE game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("i",$gameId);
			$data=returnJson($stmt);
			$mysqli->close();
			return $data;
		}else{
			throw new Exception("An error occurred while getMoveData");
		}
	}catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
}
/*********************************Utilities*********************************/
/*************************
	returnJson
	takes: prepared statement
		-parameters already bound
	returns: json encoded multi-dimensional associative array
*/
function returnJson ($stmt){
	$stmt->execute();
	$stmt->store_result();
 	$meta = $stmt->result_metadata();
    $bindVarsArray = array();
	//using the stmt, get it's metadata (so we can get the name of the name=val pair for the associate array)!
	while ($column = $meta->fetch_field()) {
    	$bindVarsArray[] = &$results[$column->name];
    }
	//bind it!
	call_user_func_array(array($stmt, 'bind_result'), $bindVarsArray);
	//now, go through each row returned,
	while($stmt->fetch()) {
    	$clone = array();
        foreach ($results as $k => $v) {
        	$clone[$k] = $v;
        }
        $data[] = $clone;
    }
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	//MUST change the content-type
	header("Content-Type:text/plain");
	// This will become the response value for the XMLHttpRequest object
    return json_encode($data);
}


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
    $sql="select iduser from users where email=?";
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
		
		
		$logger->info( "result user : ". $stmt->fetch());


		if($stmt->fetch()){
			$logger->info(__FILE__.": User is not null.");
			$logger->debug(__FILE__.": ".$user);
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
	$newUserId;
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

function getUser($emailId){
	global $logger,$mysqli;
	$logger->info("Insidd getUser with emailID:".$emailId);
	global $mysqli;
	$sql="SELECT * FROM users WHERE email=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			
			$stmt->bind_param("s",$emailId);
			
			$data=returnJson($stmt);
			
			//$mysqli->close();
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