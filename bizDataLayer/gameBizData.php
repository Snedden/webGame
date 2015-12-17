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

	global $mysqli,$logger;
	$logger->info('startData in gameBizData.php called with parameter'.$gameId);
    //Get status of the current game
	$sql="select status from heroes_games where game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("i",$gameId);
			$data=bindSql($stmt);

			$gameStatus=$data[0]['status'];
		}
		else{
			$logger->error('Somthing went wrong in prepare statement of game in startData()'.$mysqli->error);
		}
	}catch(Excpetino $e){
		$logger->error('Something went wrong in getting status of the game  in startData');
	}


	$logger->info('Game Status:'.strcmp('inGame',$gameStatus));
	//Init game table only once ,to avoid refresh button click by the user
	if(strcmp('inGame',$gameStatus)!=0){
   		 $logger->info('inside start game update');

		//return $gameId.'sdf';
		//simple test for THIS 'game' - resets the last move and such to empty
		$sql = "UPDATE heroes_games SET player0_pieceID=null, player0_boardI=null, player0_boardJ=null,player0_attacking=0, player1_pieceID=null, player1_boardI=null, player1_boardJ=null,player1_attacking=0,status='inGame' WHERE game_id=?";
		try{
			if($stmt=$mysqli->prepare($sql)){
				//bind parameters for the markers (s - string, i - int, d - double, b - blob)
				$stmt->bind_param("i",$gameId);
				$stmt->execute();
				$stmt->close();
			}else{

				$logger->info('There was a error in prepare statement startData '.$mysqli->error);
			}
		}catch (Exception $e) {
			log_error($e, $sql, null);
			return false;
		}

	}  //end of heroes_table init

	//get the init of the game
	$sql = "SELECT * FROM heroes_games WHERE game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			//bind parameters for the markers (s - string, i - int, d - double, b - blob)
			$stmt->bind_param("i",$gameId);
			$data=bindSql($stmt);
			$data=json_encode($data);
			$mysqli->close();
			$logger->info("Return data from start $data");
			return $data;
		}else{
			$logger->info('There was a error in prepare statement startData select '.$mysqli->error);
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
			$data=bindSql($stmt);
			$data=json_encode($data);
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

		return false;
    }
	$mysqli->close();
}
/*************************
	changeBoardData 38~dragon|1|0~32~32~0
*/
//val+"~"+pieceId+"~"+boardI+"~"+PlayerId+"~"+isAttacking+"~"+attackedPiece
function changeBoardData($gameId,$pieceId,$boardI,$playerId,$isAttack,$attackedUnit){

	//update the board
	global $mysqli;
	global $logger;

	$logger->info('inside game SVC changeBoardData attackunit '.$attackedUnit);
	$sql="UPDATE heroes_games SET player".$playerId."_pieceId=?, player".$playerId."_boardI=?, player".$playerId."_attacking=?,attackedUnit=? WHERE game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("siisi",$pieceId,$boardI,$isAttack,$attackedUnit,$gameId);
			$stmt->execute();
			$stmt->close();
		}else{
			$logger->error('prepared stament failed in changeboard data'.$mysqli->error);
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
			$data=bindSql($stmt);
			$data=json_encode($data);
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

function checkWinnerDB($gameId){
	global $mysqli;

	global $mysqli;
	$sql="SELECT winner FROM heroes_games WHERE game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("i",$gameId);
			$data=bindSql($stmt);
			$data=json_encode($data);
			
			return $data;
		}else{
			throw new Exception("An error occurred while getMoveData");
		}
	}catch (Exception $e) {
		log_error($e, $sql, null);
		return false;
	}
}

function winGameDB($data){
	global $logger,$mysqli;
	$logger->info('inside winsDB');

	$logger->info("Data at winGameDB()".print_r($data,true));


	$sql="update  heroes_games set status='complete',winner=? where game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("ii",$data[playerId],$data[gameId]);
			$stmt->execute();
			$stmt->close();

		    return true;
		}
		else{
			$logger->error('Something went wrong while preparing statement inGameDb'.$mysqli->error);
			return false;
		}

	}catch(Exception $e){
		$logger->error('Something went wrong while updating status of challenges in metChallengeDB');
		return false;
	}
}
/*********************************Utilities*********************************/
/*************************
	returnJson
	takes: prepared statement
		-parameters already bound
	returns: json encoded multi-dimensional associative array

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
}*/




?>