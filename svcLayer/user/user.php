<?php
ini_set('display_errors', 1);

 $validationError=false;
//require_once("./vendor/wixel/gump/gump.class.php");
require_once("./bizDataLayer/commonDbFunctions.php");
require_once("./bizDataLayer/userDbBiz.php");
require_once( "./bizDataLayer/dbInfoPS.php");//to use we need to put in: global $mysqli;
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
   
    global $validationError;
   // $logger->debug("insider registerUser() function");

    //Check if data is valid
    $data=validateData($d);  
    //$logger->debug("validate data".$data);
    // $logger->debug("validateError flag ".$validationError);
    if($validationError){ //if error
        $errorMsg = array(
                'error' => $data
            );
           // $logger->info($errorMsg);
            return json_encode($errorMsg);
    }
   
  

    //$logger->debug("The data has been validated -- about to insert into DB");
    
    // Prepared data for the database use
    // Hash password
    $password_hash = password_hash($data['password1'], PASSWORD_BCRYPT);
    //$logger->debug("Hashed password: '".$password_hash."'");
    
    
    $userData = array(
        'firstName' => $data['firstName'],
        'lastName' => $data['lastName'],
        'email' => $data['email'],
        
        'password' => $password_hash,
        'status' => 0,
        'registration_date' => date('Y-m-d G:i:s')
    );
    
    
  
    
    return insertUser($userData);
    
}
//check session
function checkSession($activity){
    //global $logger;

    //$logger->info('in check session'.$_SESSION['user_id'].' '.$activity + 10 * 60 ." time :".time());

    if ($activity + 10 * 60 < time()) { //set to 10 minutes in
        // session timed out
        return true;
    } else {
        // session ok
        return false;
    }
}



//this is the function I call
function validateData($data){
    //global $logger,$validationError;
    //$logger->debug("insider validateDatar() function");  //loggin out if the function is call ,but it is not
    
    $gump = new GUMP();
   // $logger->info("gump:".$gump);
    $data = $gump->sanitize($data); // You don't have to sanitize, but it's safest to do so.

    $gump->validation_rules(array(
        'firstName'    => 'required|alpha_numeric|max_len,100|min_len,6',
        'lastName'    => 'required|max_len,100|min_len,6',
        'email'       => 'required|valid_email',
        'password1'      => 'required|max_len,100|min_len,6'
        
    ));

    $gump->filter_rules(array(
        'firstName' => 'trim|sanitize_string',
        'lastName'=>'trim|sanitize_string',
        'password1' => 'trim',
        'email'    => 'trim|sanitize_email'
        
    ));



    $validated_data = $gump->run($data);
    //$logger->info("after validate data ".$validated_data);
    if($validated_data === false) {
       $error=$gump->get_readable_errors(true);
       // $logger->info("return false".$error);
        $validationError=true;
        return $error;
    } 
    else {
       // $logger->info("returned valid data");
        $validationError=false;
        return $validated_data;
    }
    
}


function signIn($d, $ip, $token) {
    
    
    global  $SECRET_KEY;
    
   // $logger->debug(" signIn function. data $d ip $ip token $token" );
    
    $userEmail = $d['userEmail'];      //user name is emailid
    $userPassword = $d['password'];


    $user = getUserByEmail($userEmail);
    //$logger->info('Email id from getUser '.implode(',', array_keys($user[0])));
    //$userRow=$user[0];
    //$logger->info('passworrd from form '.$userPassword);
    //$logger->info('password  from getUser '.$user[0]['password']);
    if (count($user)==0) {//no user found
       
       // $logger->info('email is not found in  db');
        $errorResponse = array(
            'errorMessage' => 'get user failed. '
            
        );
       
        echo json_encode($errorResponse);
    }
    else {
        // Check if the password matches
        $hashedPassword=$user[0]['password'];
       // $logger->debug("user password $userPassword , hashedPassword $hashedPassword". gettype($userPassword).' '.gettype($hashedPassword));

        if (password_verify($userPassword,$hashedPassword)){


            if(makeOnlineDB($user[0]['iduser'])) {
               // $logger->info(" User login succeeded for username: $user[0]['iduser'] ");
                session_start();
                $_SESSION['user_id'] = $user[0]['iduser'];
                updateUsersLastActivityDB($_SESSION['user_id']);
               // $logger->info("Session set  ". $_SESSION['user_id'] ."last_activty".$_SESSION['last_activity']);
                $Response = array(
                    'Message' => "Logged-in"
                );
                echo json_encode($Response);
            }
            else{
               // $logger->error(" database failed to update status in login ");
                $errorResponse = array(
                    'errorMessage' => "make  online failed. "
                );
            }
        } 
        else {
           // $logger->error(" User  failed to authenticate ");
             $errorResponse = array(
                'errorMessage' => "password hash failed. "
            );
            
            echo json_encode($errorResponse);
        }
        
    }
}

function makeOnline($userId){
    return makeOnlineDB($userId);
}

function getUserById($userId){
   // global $logger;
    //$logger->info("inside getUserById id:$userId");
    return json_encode(getUserByIdDB($userId));
}

function getUserByEmail($userEmail){
    return getUserByEmailDB($userEmail);
}

function logOut($userId){
   // global $logger;
    //$logger->info('in logOut with userID '.$userId);
    return logOutDB($userId);
}
