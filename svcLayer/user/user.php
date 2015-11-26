<?php
ini_set('display_errors', 1);
 global $logger;
 $logger->info("inside user.php");
require_once("./bizDataLayer/userDbBiz.php");
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
    $data = $d;
    $logger->debug("Received the following registration information: ");
    foreach ($data as $key => $value) {
        $logger->debug("\t" . $key . " = " . $value);
    }
    
   // $validated_data = getValidatedRegData($data);
    $validated_data = $data;  //need to do validation here

    $logger->debug("The data has been validated -- about to insert into DB");
    
    // Prepared data for the database use
    // Hash password
    $password_hash = password_hash($validated_data['password1'], PASSWORD_BCRYPT);
    $logger->debug("Hashed password: '".$password_hash."'");
    
    
    $userData = array(
        'firstName' => $validated_data['firstName'],
        'lastName' => $validated_data['lastName'],
        'email' => $validated_data['email'],
        
        'password' => $password_hash,
        'status' => 1,
        'registration_date' => date('Y-m-d G:i:s')
    );
    
    // Let's print a value 

    $logger->debug($userData['firstName']." passed to be inserted");
   // console_log($userData);
    
    
    // Create a new user in a database
    insertUser($userData);
    $logger->debug(__FILE__.": Already called insertUser() function.");
    
}
