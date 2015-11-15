////ajax util/////
//d is data sent, looks like {name:value,name2:val2}
////////////////
function ajaxCall(GetPost,d,callback){
//console.log('ajax call');	
	$.ajax({
 		type: GetPost,
 		async: true,
  		cache:false,
  		url: "mid.php",
  		data: d,  
  		dataType: "json",
  		success: callback,
  		error: function (xhr, ajaxOptions, thrownError) {
        	//console.log(xhr.status);
        	//
        	//console.error('ajax call error',d,thrownError);
        	//console.log( xhr.responseText);
      }
	});
}
////initGameAjax/////
//d is data sent, looks like {name:value,name2:val2}
//this is my starter call
//goes out and gets all pertinant information about the game (FOR ME)
//callback is callbackInit()
////////////////
function initGameAjax(whatMethod,val){
	//data is gameId
	//console.log('initAjax');
	ajaxCall("POST",{method:whatMethod,a:"game",data:val},callbackInit);
}
////callbackInit/////
//callback for initGameAjax
////////////////
function callbackInit(jsonObj){
	//compare the session name to the player name to find out my playerId;
	turn = jsonObj[0].whoseTurn;
	if(player == jsonObj[0].player1_name){
		player2 = jsonObj[0].player0_name;
		PlayerId = 1;
	}else{
		player2 = jsonObj[0].player1_name;
		PlayerId = 0;
	}
	gameObj=new GameInit();
	if(PlayerId===jsonObj[0].whoseTurn){
		gameObj.changeHelpInfo('Your turn');
	}
	else{
		gameObj.changeHelpInfo("Waiting for Oppenent's turn");
	}	
	
	//var infoText=document.createTextNode('playerId '+PlayerId+ ' turn '+turn);
	//var infoPara=document.getElementById('infoPara');
	//infoPara.appendChild(infoText);
	//start building the game (board and piece)
    
}
////changeServerTurnAjax/////
//change the turn on the server
//no callback
////////////////
function changeServerTurnAjax(whatMethod,val){
	ajaxCall("POST",{method:whatMethod,a:"game",data:val},null);
	//change the color of the names to be the other guys turn
	//document.getElementById('youPlayer').setAttributeNS(null,'fill',"black");
	//document.getElementById('opponentPlayer').setAttributeNS(null,'fill',"orange");
}
////changeBoardAjax/////
//change the board on the server
//no callback
////////////////
function changeBoardAjax(pieceId,boardI,boardJ,isAttacking,whatMethod,val){
	//data: gameId~pieceId~boardI~boardJ~playerId
	console.log('changeBoardAjax',isAttacking);
	ajaxCall("POST",{method:whatMethod,a:"game",data:val+"~"+pieceId+"~"+boardI+"~"+boardJ+"~"+PlayerId+"~"+isAttacking},null);
}
////checkTurnAjax/////
//check to see whose turn it is
//callback is callbackcheckTurn
////////////////
function checkTurnAjax(whatMethod,val){
	console.log('turn ',turn)
	if(turn!=PlayerId){
		ajaxCall("GET",{method:whatMethod,a:"game",data:val},callbackcheckTurn);
	}
	setTimeout(function(){checkTurnAjax('checkTurn',gameId)},3000);
}
////callbackcheckTurn/////
//callback for checkTurnAjax
////////////////
function callbackcheckTurn(jsonObj){
console.log('DBTurn:'+jsonObj[0].whoseTurn,'PlayerId:'+PlayerId); 	
	if(jsonObj[0].whoseTurn == PlayerId){
		
		//switch turns
		//Fturn=jsonObj[0].whoseTurn;
		//get the data from the last guys move
		getMoveAjax('getMove',gameId);
		gameObj.changeHelpInfo("Your turn");
	}

	///change heading
		

}
////checkTurnAjax/////
//get the last move
//-called after I find out it is my turn
//callback is callbackGetMove
////////////////
function getMoveAjax(whatMethod,val){
	ajaxCall("GET",{method:whatMethod,a:"game",data:val},callbackGetMove);
}
////callbackGetMove/////
//callback for getMoveAjax
////////////////
function callbackGetMove(jsonObj){
	//console.log('getMove callback');
	//tests to see what I'm getting back!
	//console.log(jsonObj[0]['player'+Math.abs(PlayerId-1)+'_pieceID']);
    //alert(jsonObj[0]['player'+Math.abs(playerId-1)+'_boardI']);
    //console.log(jsonObj[0]['player'+Math.abs(PlayerId-1)+'_boardJ']);
    var lastPieceMovedTo=hexArray[jsonObj[0]['player'+Math.abs(PlayerId-1)+'_boardI']];
    var lastMovedPiece=unitArray[jsonObj[0]['player'+Math.abs(PlayerId-1)+'_pieceID']];
    var attacking=jsonObj[0]['player'+Math.abs(PlayerId-1)+'_attacking']===1?true:false; //if isAttacking is 1 in db then attacking is true
   console.log('attacking:',attacking);
    hexMeshObj.moveSelected(lastPieceMovedTo,lastMovedPiece,false,attacking);
    //change the text output on the side for whose turn it is
	//var hold='playerId '+playerId+ ' turn '+turn;
	//document.getElementById('output2').firstChild.data=hold;
	
	//change the color of the names for whose turn it is:
	//....document.getElementById('youPlayer').setAttributeNS(null,'fill',"orange");
	//...document.getElementById('opponentPlayer').setAttributeNS(null,'fill',"black");
	
	
}











