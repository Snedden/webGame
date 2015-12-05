<!DOCTYPE html>
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
    <title>BOOTSTRAP CHAT EXAMPLE</title>
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
        var lastTimeStamp='1899-11-30 00:00:00';
        function enterChat(chatMsg){
            var chatData={};
            chatData['chatMsg']=$("#chatText").val();
            chatData['userName']=1;

            console.log('chat inserted is ',$("#chatText"));
            ajaxCall('POST',{method:'enterChat',a:'lobby',data:chatData},callBackChat);
           
        }

        function readChatsAjax(){
            var chatData={};
            chatData['lastTimeStamp']=lastTimeStamp;
            ajaxCall("GET",{method:'readChats',a:"lobby",data:chatData},callbackReadChat);
         
            setTimeout(readChatsAjax,1000);
        }

        function callbackReadChat(jsonObj){
            console.log(jsonObj);
            console.log( typeof jsonObj);
            if((typeof jsonObj)==='object'){
                console.log( 'is object' );
                $('#chatMessages').text(''); //clear previous chat messages
                for (var i=0,l=jsonObj.length;i<l;i++){
                        
                    console.log('Appended ',jsonObj[i].text);
                    var chatElement=$('<li class="media"> ' +
                        '<div class="media-body">'+
                            '<div  class="media"> ' +
                                '<a class="pull-left" href="#">' +
                                    '<img class="media-object img-circle " src="assets/img/user.gif" /> ' +
                                '</a> ' +
                                '<div  class="media-body" >' +jsonObj[i].text+
                                    '<br />'+
                                    '<small class="text-muted">Jhon Rexa | 23rd June at 5:00pm</small>' +
                                '<hr />'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</li>');

                    $('#chatMessages').append(chatElement);


                }
            }
        }


        function callBackChat(jsonObj){
            console.log(jsonObj);
        }
    </script> 

</head>
<body onload="readChatsAjax()" style="font-family:Verdana">
  <div class="container">
<div class="row " style="padding-top:40px;">
    <h3 class="text-center" >BOOTSTRAP CHAT EXAMPLE </h3>
    <a href="logout.php" style="float:right">logout</a>
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
                                        <button class="btn btn-info" onclick="enterChat()" type="button">SEND</button>
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
                <ul class="media-list">

                                    <li class="media">

                                        <div class="media-body">

                                            <div class="media">
                                                <a class="pull-left" href="#">
                                                    <img class="media-object img-circle" style="max-height:40px;" src="assets/img/user.png" />
                                                </a>
                                                <div class="media-body" >
                                                    <h5>Alex Deo | User </h5>
                                                    
                                                   <small class="text-muted">Active From 3 hours</small>
                                                </div>
                                            </div>

                                        </div>
                                    </li>
     <li class="media">

                                        <div class="media-body">

                                            <div class="media">
                                                <a class="pull-left" href="#">
                                                    <img class="media-object img-circle" style="max-height:40px;" src="assets/img/user.gif" />
                                                </a>
                                                <div class="media-body" >
                                                    <h5>Jhon Rexa | User </h5>
                                                    
                                                   <small class="text-muted">Active From 3 hours</small>
                                                </div>
                                            </div>

                                        </div>
                                    </li>
                                </ul>
                </div>
            </div>
        
    </div>
</div>
  </div>
</body>
</html>
