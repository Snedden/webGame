<?php
	//ugly little page to start it off, set user to Fred or Dan
	//(you will replace this with the session variable you set when they logged in)
	//I'm using this to skip the entire login and chat/challenge room
	echo '<a href="game.php?player=Dan&gameId=38">Dan</a><br/>';
    echo '<a href="game.php?player=Snedden&gameId=38">Snedden</a>';
?>


<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Heroes Chronicles</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
        <style>
        </style>
        <link rel="stylesheet" href="css/bootstrap/bootstrap-theme.min.css">
        <link rel="stylesheet" type="text/css" href="css/styles.css">
         <!--console ouit php-->


        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
        <!--<script src="js/main.js"></script>-->
        <script type="text/javascript">
        //ajax call 
        function ajaxCall(getPost,d,callback){
        console.log(getPost,d,callback);  
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
         //REegister new user
        function registerNewAjax(formData) {
                
                // Print to console JSON object
                console.log(formData);
                ajaxCall("POST",{method:'registerUser',a:'user',data: formData},callBackReg);
               // ajaxCall("POST",{method:whatMethod,a:"game",data:val},callbackInit);
               

        };

        function callBackReg(jsonObj){
             console.log('call abck recieve', jsonObj);
              if((typeof jsonObj)==='object'){

                $("#onSubmitResponseMsg").text(jsonObj.error);
            }
            else if(jsonObj===1){//returened true
                $("#onSubmitResponseMsg").text('User added');
            }
            else{
                $("#onSubmitResponseMsg").text('User not added');
            }

        } ;

        function signIn() {
            console.log("Called signIn function");
            var loginData = {};
            $('#loginForm').find('input[name]').each(function (index, node) {
                loginData[node.name] = node.value;
            });
            console.log('data',loginData);
            ajaxCall("POST",{method: 'signIn', a: 'user', data: loginData},signInCallBack);
            
            event.preventDefault();


        };

        function signInCallBack(data) {
            console.log('sign in call back',data,data.Message ,typeof data.Message, data.Message==='Logged-in');
            if(data.Message==='Logged-in'){
                console.log('procees to lobby..');
                window.location='lobby.php';
            }
           

        }

         

        </script>
    </head>
    <body >
    	

		


        <div class="container">
            <nav class="navbar navbar-default" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <a class="navbar-brand" href="#">Heroes Chronicles</a>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li id="authUser" class="navbar-text"></li>
                        <li id="logOutLink" class="hide"><a href="#about">Sign Out</a></li>
                    </ul>
                </div>
            </nav>
            <div id="welcomeMessage" class="hidden">
                <a href="lobby.html">Proceed to the Lobby</a>
            </div>
            <div id="regSignTabs">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#logIn" aria-controls="home" role="tab" data-toggle="tab">Sign In</a></li>
                    <li role="presentation"><a href="#register" aria-controls="profile" role="tab" data-toggle="tab">Register</a></li>
                </ul>
                <div  class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="logIn">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Welcome back! Please, sign in </div>
                            <div class="panel-body">
                                <form id="loginForm" class="form-sign" role="form">
                                    <div class="form-group">
                                        <input name="userEmail" type="email" placeholder="EmailID" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input name="password" type="password" placeholder="Password" class="form-control">
                                    </div>
                                    <input type="button" class="btn btn-success" onclick="return signIn();" value="Sign In" />
                                </form>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="register">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Not a member yet? Please register below</div>
                            <div class="panel-body">
                                <form id="regForm" role="form">
                                    <div class="form-group">
                                        <label for="firstName">First Name</label>
                                        <input name="firstName" type="text" class="form-control" id="firstName" placeholder="First Name">
                                    </div>
                                    <div class="form-group">
                                        <label for="lastName">Last Name</label>
                                        <input name="lastName" type="text" class="form-control" id="lastName" placeholder="Last Name">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input name="email" type="email" class="form-control" id="email" placeholder="Email">
                                        
                                    </div>
                                    <div class="form-group">
                                        <label for="userName">Username</label>
                                        <input name="userName" type="text" class="form-control" id="userName" placeholder="Username">
                                    </div>
                                    <div class="form-group">
                                        <label for="password1">Password</label>
                                        <input name="password1" type="password" class="form-control" id="password1" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <label for="password2">Re-enter Password</label>
                                        <input name="password2" type="password" class="form-control" id="password2" placeholder="Re-enter Password">
                                    </div>
                                    <button class="btn btn-warning" onclick="registerNew();">Register</button>
                                    <label id='onSubmitResponseMsg'>Response message would go here!</label>
                                </form></div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <footer>
                <p>&copy; Snedden Gonsalves 2015</p>
            </footer>
        </div> <!-- /container -->        

      
        
      
        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="js/userFunctions.js"></script>
       
		
         
           

  

    
    

    </body>
</html>

