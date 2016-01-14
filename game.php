<!doctype html

>
<html>
	<head>
		<!--checking if user is authenticated-->
		<?php
		session_start();
		if (!isset($_SESSION["user_id"]))
		{
			header("location: index.php");
		}
		?>
    <title>
	 Heroes
	</title>

	<link rel="stylesheet" type="text/css" href="css/styles.css"/>
	<link href="css/bootstrap/bootstrap.css" rel="stylesheet" />

	</head>
	<body >
	
		<!--<p>A few examples of hexagons with inline SVG (via HTML5). Also shows some CSS transitions of SVG attributes.</p>

		<h1>Color Fill</h1>-->

		<div class='heading' style="width:300px;margin:0 auto">
          	<h1 id="playerNameHeading"><?php echo $_GET['player']?></h1>
          	<p id="infoPara"></p>
		</div>
		<svg style="border-style:solid;border-bottom-width: 2px"id="svgId" xmlns="http://www.w3.org/2000/svg" version="1.1" width="1000" height="450" xmlns:xlink="http://www.w3.org/1999/xlink">
	  
			
		</svg>
		
		
		<!--	
		<h1>Color Fill with anchor</h1>

		<svg id="color-fill-anchor" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="300" xmlns:xlink="http://www.w3.org/1999/xlink">
	  
			<a xlink:href="http://viget.com">
				<polygon class="hex" points="300,150 225,280 75,280 0,150 75,20 225,20" fill="#fa5"></polygon>
			</a>
	  
		</svg>

		<h1>Image Fill</h1>

		<svg id="image-fill" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="300" xmlns:xlink="http://www.w3.org/1999/xlink">

		<defs>
			<pattern id="image-bg" x="0" y="0" height="300" width="300" patternUnits="userSpaceOnUse">
				<image width="300" height="300" xlink:href="http://placekitten.com/306/306"></image>
			</pattern>
		</defs>
	  
		<polygon class="hex" points="300,150 225,280 75,280 0,150 75,20 225,20" fill="url('#image-bg')"></polygon>
	  
		</svg>
	  -->

		<div class="panel panel-info" style="width: 320px;float:right;margin-right:20px;">
			<div class="panel-heading">
				RECENT CHAT HISTORY
			</div>
			<div class="panel-body">
				<ul class="media-list" id="chatMessages" style="max-height: 300px;height:200px;overflow:scroll">

				</ul>
			</div>
			<div class="panel-footer">
				<div class="input-group">
					<input type="text" id='chatText' class="form-control" placeholder="Enter Message" />
                                    <span class="input-group-btn">
                                        <button id="chatTextbtn" class="btn btn-info" onclick='inGameObj.enterInGameChat("<?php echo $_SESSION["user_id"]?>")' type="button">SEND</button>
                                    </span>
				</div>
			</div>
		</div>
	
	
	</body>



    	<script src="js/vendor/jquery-1.11.2.min.js"></script>
        <script src='js/globals.js'></script>
        <script src="js/ajaxFunctions.js" type="text/javascript"></script>

        <script src='js/main.js'></script>
        <script src='js/hexMesh.js'></script>   
        <script src='js/hexagon.js'></script>
        <script src='js/unit.js'></script>


	<script type="text/javascript">
			var gameId=<?php echo $_GET['gameId'] ?>;
			var player="<?php echo $_GET['player']?>";
			//alert(playerId);
			initGameAjax('start', gameId);
	</script>
	<script src='js/inGameFunctions.js'></script>


	 
	
</html>