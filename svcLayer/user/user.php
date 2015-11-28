<?php
ini_set('display_errors', 1);
 global $logger;
 $logger->info("inside user.php");

//require_once("./vendor/wixel/gump/gump.class.php");
require_once("./bizDataLayer/gameBizData.php");
require "./bizDataLayer/dbInfoPS.inc";//to use we need to put in: global $mysqli;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 * @global type $logger
 * @param type $d API data as JSON
 * @param type $ip user IP address
 * @param type $token user token
 */
function registerUser($d, $ip, $token) {
   
    global $logger;
    $logger->debug("insider registerUser() function");
   // $data = $d;
    
   
    

    //Check if data is valid
    $data=validateData($d);
   
    

    if(!$data){
        $errorMsg = array(
                'error' =>'Data send is invalid.'
            );
            $logger->info("user exist error as".$errorMsg);
            return json_encode($errorMsg);
    }
   
  

    $logger->debug("The data has been validated -- about to insert into DB");
    
    // Prepared data for the database use
    // Hash password
    $password_hash = password_hash($validated_data['password1'], PASSWORD_BCRYPT);
    $logger->debug("Hashed password: '".$password_hash."'");
    
    
    $userData = array(
        'firstName' => $data['firstName'],
        'lastName' => $data['lastName'],
        'email' => $data['email'],
        
        'password' => $password_hash,
        'status' => 1,
        'registration_date' => date('Y-m-d G:i:s')
    );
    
    
  
    
    return insertUser($userData);
    
}

function validateData($data){
    $logger->debug("insider validateDatar() function");
    
    $gump = new GUMP();
   // $logger->info("gump:".$gump);
    $data = $gump->sanitize($data); // You don't have to sanitize, but it's safest to do so.

    $gump->validation_rules(array(
        'firstName'    => 'required|alpha_numeric|max_len,100|min_len,6',
        'lastName'    => 'required|max_len,100|min_len,6',
        'email'       => 'required|valid_email',
        'password'      => 'required|max_len,100|min_len,6',
        'status' => 'required|integer'
    ));

    $gump->filter_rules(array(
        'firstName'    => 'required|alpha_numeric|max_len,100|min_len,6',
        'lastName'    => 'required|max_len,100|min_len,6',
        'email'       => 'required|valid_email',
        'password'      => 'required|max_len,100|min_len,6',
        'status' => 'required|integer'
    ));

    $validated_data = $gump->run($data);

    if($validated_data === false) {
        return false;
    } else {
        return $validated_data;
    }
    
}


function signIn($data, $ipAddress, $token) {
    // Cleanse username and password
    // TODO
    
    global $logger, $SECRET_KEY;
    
    $logger->debug(__FILE__ . ": Inside the signIn function.");
    
    $userName = $data['userName'];      //user name is emailid
    $userPassword = $data['password'];


    $user = getUser($userName);
    if (is_null($user)) {
       
        header('response_code 401');
        $errorResponse = array(
            'errorMessage' => 'Failed to authenticate user. '
            . 'Username/password combination cannot be validated.'
        );
        setResponseHeaders();
        echo json_encode($errorResponse);
    } else {
        // Check if the password matches
        if (password_verify($userPassword, $user->passwordHash)){
            $logger->debug(__FILE__.": User login succeeded for username: ".$userName);
            // function tokenizeIdData($userId, $ipAddress, $secretKey)
            tokenizeIdData($user->userId, $ipAddress, $SECRET_KEY);
            $logger->debug(__FILE__.": Security token set.");
//            header( "Location: http://localhost/battleship/lobby.php" ); // redirect to lobby
//            $logger->debug(__FILE__.": The user has been redirected to lobby.");
            
        } else {
            $logger->warn(__FILE__.": User login failed for username: ".$userName);
            // TODO: return error message
        }
        
    }
}
