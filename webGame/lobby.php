﻿<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


    <!--checking if user is authenticated-->
    <?php
    session_start();
    if (!isset($_SESSION["user_id"]))
    {
       header("location: index.php");
    }
    ?>


    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <![endif]-->
    <title>Heroes Lobby</title>
    <!-- BOOTSTRAP CORE STYLE CSS -->
    <link href="css/bootstrap/bootstrap.css" rel="stylesheet" />
     <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
    <script>
         function ajaxCall(getPost,d,callback){
       // console.log(getPost,d,callback);
        $.ajax({
            type: getPost,
            async: true,
            cache:false,
            url: "mid.php",
            data: d,  
            dataType: "json",
            success: callback,
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                //
                //console.error('ajax call error',d,thrownError);
                //console.log( xhr.responseText);
          }
        });
        }




         /////////////////Init
         var lastTimeStamp='1899-11-30 00:00:00';
         function init(){
             getUserAjax(); //current user
             populateOnlineUsers();  //online users list
             addChatListeners();//chat interactivelty
             readChatsAjax(); //read chat heart beat

             window.onbeforeunload = function ()
             {
                 logOutAjax();//logout if user closes window
             }


         }

         function addChatListeners(){
             (document.getElementsByTagName('body')[0]).addEventListener('keydown',function(e){ //bind when user starts typing
                 $('#chatText').focus();                //bring input box in focus
                 if(e.keyCode == 13){   //enter clicked

                     $('#chatTextbtn').click();
                 }


             });
         }

         /////////GET USER
         function getUserAjax(){


             var userId="<?php echo $_SESSION["user_id"]  ?>";
             console.log('userID ',userId);


             ajaxCall('GET',{method:'getUserById',a:'user',data:userId},callBackGetUser);
         }

         var firstName; //need to have wider scope as I use it at entering game

         function callBackGetUser(jsonObj){
             console.log(jsonObj,jsonObj[0].first_name,jsonObj[0].last_name,$('#greetingText'));
             firstName=jsonObj[0].first_name;
             var greeting="Welcome "+firstName+" "+jsonObj[0].last_name;
             $('#greetingText')[0].innerHTML=greeting;
         }



         /////////GET OPEN CHALLENGES WHERE TO_USER =me and STATUS=OPEN
         function getOpenChallenges(){
             var userId="<?php echo $_SESSION["user_id"]  ?>";
             //console.log('userID ',userId);

             ajaxCall('GET',{method:'getOpenChallenges',a:'lobby',data:userId},callBackGetOpenChallenges);
             //setTimeout(getOpenChallenges,2000);
         }

         function callBackGetOpenChallenges(jsonObj){
             console.log('Open challenges',jsonObj);
             if(jsonObj) {
                 for (var i= 0,l=jsonObj.length;i<l;i++){
                     var chlgIcon=document.getElementById(jsonObj[i].email);
                     var chlgSentDiv=document.getElementById("challengeSentBy~"+jsonObj[i].email);
                     if(chlgIcon&&chlgSentDiv) {
                         chlgIcon.style.display = "none";
                         chlgSentDiv.style.display = "block";
                         var c=chlgSentDiv.childNodes;

                         var Aid='accept~'+jsonObj[i].challenge_id;
                         c[0].setAttribute('id',Aid);
                         var Abutton=$(document.getElementById(Aid));
                         console.log('abutton ',Abutton);
                         Abutton.attr('onclick','acceptChallenge(this)');
                         /*if (-1 !== $.inArray(acceptChallenge, Abutton.data('events').click)) {
                             Abutton.bind('click',function(){
                                 acceptChallenge(this)});
                         }*/


                         var Rid='reject~'+jsonObj[i].challenge_id;
                         c[1].setAttribute('id',Rid);
                         var Rbutton= $(document.getElementById(Rid));
                         Rbutton.attr('onclick','rejectChallenge(this)');
                         /*if (-1 !== $.inArray(rejectChallenge, Rbutton.data('events').click)) {
                             Rbutton.bind('click',function(){
                                 rejectChallenge(this)});
                         }*/

                     }
                     else
                     {
                         console.error("challenge div/icon was not set before changing");
                     }

                 }
             }
         }
         /////////GET ALL CHALLENGES WHERE FROM_USER=me and timestamp=most recent
         function getSentChallenges(){
             var userId="<?php echo $_SESSION["user_id"]  ?>";
             //console.log('userID ',userId);

             ajaxCall('GET',{method:'getSentChallenges',a:'lobby',data:userId},callBackGetSentChallenges);
             setTimeout(getSentChallenges,2000);
         }

         function callBackGetSentChallenges(jsonObj){
             console.log('Sent challenges',jsonObj);
             if(jsonObj) {
                 for (var i = 0, l = jsonObj.length; i < l; i++) {
                     console.log(jsonObj[i].email);
                     var chlgIcon=document.getElementById(jsonObj[i].email);
                     if(chlgIcon) {
                         if(jsonObj[i].status==='open') {
                             console.log('waiting for acceptane from '+jsonObj[i].email);
                             chlgIcon.setAttribute('src', 'assets/icons/balls.gif'); ///make icon as waiting for opponent to accept challenge
                             chlgIcon.setAttribute('title', 'Waiting for acceptance');
                             chlgIcon.removeAttribute("onclick");
                         }
                         else if(jsonObj[i].status==='rejected'){
                             chlgIcon.setAttribute('src', 'assets/icons/history-swords-crossed.png'); ///make icon as waiting for opponent to accept challenge
                             chlgIcon.setAttribute('title', 'Challenge');
                             chlgIcon.setAttribute("onclick","challengeUser(this)");
                         }


                         else if((jsonObj[i].status==='accepted')){
                             //update  challenge table to set status as challege 'met'

                                 var challengeID=jsonObj[i].challenge_id;

                                 ajaxCall('GET', {
                                     method: 'metChallenge',
                                     a: 'lobby',
                                     data:challengeID
                                 },function(gameId){
                                     console.log('proceed to game..');
                                     window.location = 'game.php?player=' + firstName + '&gameId=' +gameId;
                                 } );

                             //callback of setChallnge MET


                         }
                         else if((jsonObj[i].status==='met')){

                                console.log('met chalemge');

                              //   window.location = 'game.php?player=' + firstName + '&gameId=' +jsonObj[i].game_id; //using only firstName to enter in game table

                         }
                         else {
                             console.warn('unexpected status returned sentChallgens');
                         }
                     }
                     else
                     {
                         console.log("challenge div/icon was not set before changing");
                     }
                 }
             }
         }
         ///////////////////// LOG OUT
         function logOutAjax(){

             var userId="<?php echo $_SESSION["user_id"]  ?>";
             console.log('userID ',userId);
             ajaxCall('GET',{method:'logOut',a:'user',data:userId},callBackLogout);
         }

         function callBackLogout(jsonObj){
             console.log('call back logout', typeof jsonObj);
             if(jsonObj===1){
                 console.log('proceed to logout.php..');
                 window.location='logout.php';

             }
         }
         //checking for inactivity
         function checkSessionTimeOut(){
             var activity="<?php echo $_SESSION["last_activity"]  ?>";

             ajaxCall('GET',{method:'checkSession',a:'user',data:activity},callBackSessionTimeOut);
             setTimeout(checkSessionTimeOut,60000);
         }

         function callBackSessionTimeOut(timedOut){
             console.log('Session',timedOut)
             if(timedOut){
                // logOutAjax();
             }

         }

        //////////////////////ENTER CHAT
        function enterChat(chatMsg){

            var chatData={};
            chatData['chatMsg']=$("#chatText").val();
            chatData['userId']="<?php echo $_SESSION["user_id"]  ?>";

            console.log('chat inserted is ',$("#chatText"));
            ajaxCall('POST',{method:'enterChat',a:'lobby',data:chatData},callBackEnterChat);


        }

         function callBackEnterChat(jsonObject){
             console.log('called back enter chhat');
             console.log( $('#chatText'));
             $("#chatText").val('');
             //keep the message scoller down always to see new message without scrolling down
             $('#chatMessages').animate({ scrollTop: $('#chatMessages')[0].scrollHeight }, "slow");
         }




        ///////CHAT HEARTBEAT
        function readChatsAjax(){
            var chatData={};
            chatData['lastTimeStamp']=lastTimeStamp;
            ajaxCall("GET",{method:'readChats',a:"lobby",data:chatData},callbackReadChat);

           setTimeout(readChatsAjax,500);
        }



         function callbackReadChat(jsonObj){
             //console.log(jsonObj);
             //console.log( typeof jsonObj);
             var months=['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];

             if((typeof jsonObj)==='object'){
                 //console.log( 'is object' );
                 $('#chatMessages').text(''); //clear previous chat messages
                 for (var i=0,l=jsonObj.length;i<l;i++){

                     // Split timestamp into [ Y, M, D, h, m, s ]
                     var t = jsonObj[i].timestamp.split(/[- :]/);

                     // Apply each element to the Date function
                     var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);

                     // console.log('Appended ',jsonObj[i].text);
                     var chatElement=$('<li class="media"> ' +
                         '<div class="media-body">'+
                         '<div  class="media"> ' +
                         '<a class="pull-left" href="#">' +
                         '<span class="glyphicon glyphicon-user"></span> ' +
                         '</a> ' +
                         '<div  class="media-body" >' +jsonObj[i].text+
                         '<br />'+
                         '<small class="text-muted">'+jsonObj[i].first_name+' '+jsonObj[i].last_name+' | '+d.getDate()+' '+months[d.getMonth()-1]+' at '+d.getHours()+':'+d.getMinutes()+'</small>' +
                         '<hr />'+
                         '</div>'+
                         '</div>'+
                         '</div>'+
                         '</li>');

                     $('#chatMessages').append(chatElement);

                     //return false;

                 }
             }
             checkSessionTimeOut();//check for session TImeout
         }
         ////ONLINE USERS HEARTBEAT
        function populateOnlineUsers(){
            var userId="<?php echo $_SESSION["user_id"]  ?>";
            ajaxCall("GET",{method:'getOnlineUsers',a:"lobby",data:userId},populateOnlineUsersCallBack);
            setTimeout(populateOnlineUsers,50000);
        }

        function populateOnlineUsersCallBack(jsonObj){
             console.log('online users ',jsonObj);
            $('#onlineUsers').text('');
            if(jsonObj) {
                for (var i = 0, l = jsonObj.length; i < l; i++) {
                    var onlineUserElement = $(' <li class="media">' +
                        '<div class="media-body">' +
                        '<div class="media">' +
                        '<div class="pull-left" >' +
                            '<img class=" btn media-object img-circle" style="max-height:40px;"   id="'+jsonObj[i].email+'" onclick="challengeUser(this)" title="Challenge" alt="Chl" src="assets/icons/history-swords-crossed.png" />' +
                            '<div id="challengeSentBy~'+jsonObj[i].email+'" style="display:none">'+
                                '<button type="button" class="btn btn-default btn-sm">'+
                                    '<span class="glyphicon glyphicon-ok"></span> '+
                                '</button>'+
                                '<button type="button" class="btn btn-default btn-sm">'+
                                    '<span class="glyphicon glyphicon-remove"></span> '+
                                '</button>'+
                            '</div>'+

                        '</div>' +
                        '<div class="media-body" >' +
                        '<h5>' + jsonObj[i].first_name + ' ' + jsonObj[i].last_name + ' | ' + jsonObj[i].email + ' </h5>' +
                        '<small class="text-muted">Active From 3 hours</small>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</li>');
                    $('#onlineUsers').append(onlineUserElement);

                }
            }
            else{
                $('#onlineUsers').append('<p>No users online</p>');
            }
            getOpenChallenges();//get open challenges
            getSentChallenges();//get all the challenges sent by this user
         }

        /////CHALLENGE METHODS
         function challengeUser(self){
            console.log(self.id);
             self.setAttribute('src','assets/icons/balls.gif');
             self.setAttribute('title','Waiting for acceptance');
             self.removeAttribute("onclick");
             var challengeData={};
            challengeData['toEmail']=self.id;  //id is the emailid
             challengeData['from']="<?php echo $_SESSION["user_id"]  ?>";
             ajaxCall("POST",{method:'enterChallenge',a:"lobby",data:challengeData},challengeUserCallBack);

         }
        /////This function is called recursively until challenge status is accepted
        function challengeUserCallBack(jsonObj){
/*
            console.log('challenge callback ',jsonObj);
            var challengeData={};
            var heartbeat;

            if(jsonObj.errorMsg){
                console.log(jsonObj.errorMsg);
            }
            else if(jsonObj){
                challengeData['id']=jsonObj.chlgnid;

                if(jsonObj.status==='open') {
                    ajaxCall("POST",{method:'getChallengeStatus',a:"lobby",data:challengeData},function(jsonObj){

                        heartbeat=setTimeout(challengeUserCallBack,1000,jsonObj); //set hearBeat and pass the JUST returned jsonObj
                    });
                    console.log('waiting for accpectance');
                }
                else if(jsonObj.status==='accepted'){

                    if(heartbeat){
                        clearTimeout(heartbeat);//clear heart beat
                    }
                    //go to the game
                    console.log('proceed to game..');
                    var gameID=jsonObj.game_id;
                    window.location='game.php?player='+firstName+'&gameId='+gameID; //using only firstName to enter in game table


                    console.log('accepted,proceed to game');
                }
                else{
                    console.log('callback did not return valid data in challengeUser()');
                }
            }
*/

        }
        function acceptChallenge(e){
             console.log('challenge accepted ',e.getAttribute("id"));
            var eleId=e.getAttribute("id");
            var idArray= eleId.split('~'); //idFormat= accept~id
            var challengeId=idArray[1]
            ajaxCall('POST',{method:'acceptChallenge',a:"lobby",data:challengeId},acceptChallengeCallBack);
         }

        function acceptChallengeCallBack(gameID){
            if(gameID){
                console.log('proceed to game..');
                window.location='game.php?player='+firstName+'&gameId='+gameID; //using only firstName to enter in game table
            }
        }


         function rejectChallenge(e){
             console.log(e.getAttribute("id"));
             var eleId=e.getAttribute("id");
             var idArray= eleId.split('~'); //idFormat= reject~id
             var challengeId=idArray[1];
             ajaxCall('POST',{method:'rejectChallenge',a:"lobby",data:challengeId},function(){
                 rejectChallengeCallBack(e.parentNode.getAttribute("id"));
             });
         }

         function rejectChallengeCallBack(parentID){
             console.log(parentID);
             var arrayId=parentID.split("~");//id format challengeSentBy~emailID
             document.getElementById(parentID).style.display='none';
             document.getElementById(arrayId[1]).style.display='block';
         }



    </script> 

</head>
<body onload="init()" style="font-family:Verdana">
  <div class="container">
<div class="row " style="padding-top:40px;">
    <h3 id="greetingText" class="text-center" >Welcome user </h3>
    <a onclick="logOutAjax()" style="float:right">logout</a>
    <br /><br />
    <div class="col-md-8">
        <div class="panel panel-info">
            <div class="panel-heading">
                RECENT CHAT HISTORY
            </div>
            <div class="panel-body">
                <ul class="media-list" id="chatMessages" style="max-height: 200px;overflow: scroll">

                </ul>
            </div>
            <div class="panel-footer">
                <div class="input-group">
                                    <input type="text" id='chatText' class="form-control" placeholder="Enter Message" />
                                    <span class="input-group-btn">
                                        <button id="chatTextbtn" class="btn btn-info" onclick="enterChat()" type="button">SEND</button>
                                    </span>
                                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
          <div class="panel panel-primary">
            <div class="panel-heading">
               ONLINE USERS
            </div>
            <div class="panel-body">
                     <ul class="media-list" id="onlineUsers">


                    </ul>
                </div>
            </div>
        
    </div>
</div>
  </div>
</body>
</html>
