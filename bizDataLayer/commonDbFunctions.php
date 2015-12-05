<?php

function bindSql ($stmt){
	global $logger;
	$logger->info("Inside bind sql");

	$stmt->execute();
	$stmt->store_result();
 	$meta = $stmt->result_metadata();
    $bindVarsArray = array();
	//using the stmt, get it's metadata (so we can get the name of the name=val pair for the associate array)!
	while ($column = $meta->fetch_field()) {
    	$bindVarsArray[] = &$results[$column->name];
    }
    $logger->info('bind columns'. implode(" ",array_keys($bindVarsArray)));
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