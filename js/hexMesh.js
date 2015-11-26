
function hexMesh(){
	 var side=32;
	 //console.log('hexMesh');
	 var drawnCentroids=[];  //array of centroids of hex which are drawn on the scree
	 var drawnAroundCentroids=[];	//array of centroid of hex which have hex drawn ALL around them 
	 var ii=0;	

	var rightEdge=screen.width;
	var leftEdge=0;
	var topEdge=0;
	var botEdge=550;	
	var svg=document.getElementsByTagName('svg')[0];   //draw around svg
	var selectedUnit;         //Object in unit array that is selected //initialize in unit.js selectUnit()
	this.selectedUnit={	name:'placeholder',
						isMoving:false};    //need to initialize it to act as a property  need to ask professor why
 	var selectedEle; 	     //dom that is selected //initialize in unit.js selectUnit()
	this.selectedEle='';
	
	this.drawOver = drawOver;
	this.drawUnits = drawUnits;
	
	//draw the units on the board
	function drawUnits(){
		//console.log('drawUnits',hexArray);
		var unit1=new Unit('dragon',hexArray[203],0,1);   //(type,hexagon,playerId,typeCount)
		unit1.makeUnit(svg);
		var unit12=new Unit('dragon',hexArray[206],0,2);
		unit12.makeUnit(svg);
		
		var unit2=new Unit('knight',hexArray[23],1,1);
		unit2.makeUnit(svg);
		var unit22=new Unit('knight',hexArray[25],1,2);
		unit22.makeUnit(svg);
	}

	this.changeTurn= function changeTurn(fromUserClick){
				//locally
				console.log('Before:',turn);
				turn=Math.abs(turn-1);
				console.log('after:',turn);
				
				//how about for the server (and other player)?
				//send JSON message to server, have both clients monitor server to know who's turn it is...
				//document.getElementById('output2').firstChild.data='playerId '+playerId+ ' turn '+turn;
				if(fromUserClick){
					changeServerTurnAjax('changeTurn',gameId);
					gameObj.changeHelpInfo("Opponents turn");
				}
			}		
	

	this.moveSelected=function moveSelected(moveToHex,unit,fromUserClick,attacking){
	console.log('moveToHex:',moveToHex.num,'unit:',unit,'fromUserClick:',fromUserClick,'attacking:',attacking);	
		//if(moveToHex.isOccupied===false){
			//setting new cords on screen
			//console.log('unit1',unit);
			//unit.unselectAll();
			this.selectedEle=document.getElementById(unit.id); //get corresponding html dom unit
			//unitArray[unit.num].isMoving=true;  //set in memory unit to isMoving
			unit.isMoving=true;   //set  isMoving
			var self=this;
			//var repeater;
			//var dxy=1;
			this.selected;
            

            //trying to translate 
			var transformAttr = ' translate(' +(moveToHex.cx) + ',' + (moveToHex.cy ) + ')';
			//unit.unitEle.style.transformOrigin=hexArray[0].cx,hexArray[0].cy;
            unit.unitEle.setAttribute('transform', transformAttr);
		    
		   // unit.unitEle.setAttribute('cx',moveToHex.cx);
		    //unit.unitEle.setAttribute('cy',moveToHex.cy);	
          
			//var curX=unit.unitEle.getAttribute('cx');
			//var curY=unit.unitEle.getAttribute('cy');
			var attackingBool=0;
			//console.log('curX '+curX+'curY ',curY);
			/*if(curX<moveToHex.cx) //moveRight
			{
				unit.unitEle.setAttribute('cx',curX*1+dxy);
				//this.selectedEle.setAttribute('cx',curX*1+dxy);
			}
			if(curX>moveToHex.cx)//moveLeft
			{
				unit.unitEle.setAttribute('cx',curX*1-dxy);
				//this.selectedEle.setAttribute('cx',curX*1-dxy);
			}
			if(curY<moveToHex.cy)//movedown
			{
				unit.unitEle.setAttribute('cy',curY*1+dxy);
				//this.selectedEle.setAttribute('cy',curY*1+dxy);
			}
			if(curY>moveToHex.cy)//moveup
			{
				//console.log('move Up');
				unit.unitEle.setAttribute('cy',curY*1-dxy);
				//this.selectedEle.setAttribute('cy',curY*1-dxy);
			}*/
			//console.log('condition:'+curY+'<'+parseInt(moveToHex.cy));
			
			//destination hex
			//if((Math.abs(curY-moveToHex.cy))<1&&(Math.abs(curX-moveToHex.cx)<1)){
			//	clearTimeout(repeater);                           //stop moment 
                hexArray[unit.hexagon.num].isOccupied=false; //set isOccupied of start-hex as false
				hexArray[moveToHex.num].isOccupied=true;   //set isOccupied of the destination hex
				//unitArray[unit.num].hexagon=moveToHex;   //set final location of in memory unit

				unit.hexagon=moveToHex;                               
				//this.selectedUnit.hexagon=moveToHex;                  //placeHolder final location 
				
				unit.isMoving=false;  //set in memory unit to isMoving as false
				//this.selectedUnit.isMoving=false;   //set place holder as isMoving as false
                
                unit.moveHpText();            ///move HPtex
				//change in database
				
				
                 console.log(fromUserClick); 
				//Don't change when call back
				if(fromUserClick){
					console.log('changeBoard called');
					changeBoardAjax(unit.num,moveToHex.num,moveToHex.num,attackingBool,'changeBoard',gameId);
				}
				
				this.changeTurn(fromUserClick);

				
                console.log(unitArray);
				console.log('cleared');
				//clear selectedUnit in memnory
				//this.selectedUnit={	name:'placeholder',
				//		isMoving:false};    //need to initialize it to act as a property  need to ask professor why
 				//Clear selected dom unit
				//this.selectedEle='';
                
				return true; //piece was succesfully moved

			//}
			
			//else if(!repeater){
			//repeater=setTimeout(function(){self.moveSelected(moveToHex,unit,fromUserClick,attacking)},1); 
			//}
			
		//}	
		//else if(moveToHex.isOccupied){
		//	console.warn('place is occupied by a unit');
		//	return false; //can;t move piece
		///}
	}
	
	//get the unit from unit array
	this.getUnit=function getUnit(id){
		var l = unitArray.length;
		for(var i=0;i<l;i++) {
			if (unitArray[i]===id) {  
			//console.log('true');
				return a[i];
			}
			}
		return null;
	}
	
	//for debuggin
	this.say=function say(){
		console.log('hexMesh says',this.selectedUnit);
	}
		

   
		
		//draw hexagons over the defined edges
	function drawOver(){
		var verticalBlock=1;       			//first vertical block of hexagons
		var offSet=side*(1.73/2);  			//offset to the next block to align the hexagons
		var hexCounter=1;
		
		hexArray[0]=new Hexagon(0,0,side,hexCounter);// starting hex @param1:cx @param2:cy @param3:side @param4:id
		
		for(i=hexArray[0].cx;i<rightEdge;i=i+side*1.5){
		verticalBlock++;
			for(j=(verticalBlock%2==0)?(hexArray[0].cy):(hexArray[0].cy+offSet);j<botEdge;j=j+side*1.732){
				hexCounter++;
				hexArray[hexCounter]=new Hexagon(i,j,side,hexCounter,hexCounter);  //(cx,cy,side,id,num) id and num are same
				//console.log(i,j);
				hexArray[hexCounter].makeHex(svg);
			}
				
		}
	}	
		function drawHexAround(cent){
			var rightEdgeReached=false;
			var botEdgeReached=false;
			
				//console.log(screen.width);
				rightEdge=1300;
			
				botEdge=700;
			
			
				
			if(ii<20){
			ii++;
			
				var xRight;// y axiom from centroid of the  hexs' RIGHT of current
				var xLeft;//  y axiom from centroid of the  hexs' left of current
				var yMidTop;//  x axiom from centroid of the  hexs' sided-top of current
				var yMidBot;//  x axiom from centroid of the  hexs' sided-bottom of current
				var yTop;  // x axiom from the centroid of the top hex from current
				var yBot;  // x axiom from the centroid of the bot hex from current	
				var centroidNew;
					xRight=cent.cx+(side+side/2);
					xLeft=cent.cx-(side+side/2);
					yMidTop=(cent.cy-(1.73/2*side));   //sqrt 3 is 1.73
					yMidBot=(cent.cy+(1.73/2*side));
					yTop=(cent.cy-(1.73*side));
					yBot=(cent.cy+(1.73*side));
					
					
					
					
					 //top right
					 centroidNew={
								cx:xRight,
								cy:yMidTop
							};
					
					
					
					
					 if((!contains(drawnCentroids,centroidNew,'top right'))&&(centroidNew.cx<rightEdge&&centroidNew.cy>0)){
						makeHex(centroidNew);
						drawnCentroids.push(centroidNew);
					}	
						
					
					
					//bottom right
					centroidNew={
								cx:xRight,
								cy:yMidBot
							};
					//console.dir(drawnCentroids);
					//console.dir(centroidNew);
					//console.dir(contains(drawnCentroids,centroidNew));		
					if((!contains(drawnCentroids,centroidNew,'bottom right'))&&(centroidNew.cx<rightEdge&&centroidNew.cy<botEdge)){		
						makeHex(centroidNew);
						drawnCentroids.push(centroidNew);
					}	
					
					 //bot
					centroidNew={
								cx:cent.cx,
								cy:yBot
							};  
					if((!contains(drawnCentroids,centroidNew,'bot'))&&(centroidNew.cy<botEdge)){			
						makeHex(centroidNew);
						drawnCentroids.push(centroidNew);
					}
					
					
				   //top 
				   centroidNew={
								cx:cent.cx,
								cy:yTop
							};
					
					if((!contains(drawnCentroids,centroidNew,'top '))&&(centroidNew.cy>0)){
						//console.dir('drawnCentroids',drawnCentroids,'centroid',centroidNew,contains(drawnCentroids,centroidNew))
						makeHex(centroidNew);
						drawnCentroids.push(centroidNew);
					}	
					
					 //top left
					 centroidNew={
								cx:xLeft,
								cy:yMidTop
							};
					if((!contains(drawnCentroids,centroidNew,'top left'))&&(centroidNew.cx>0&&centroidNew.cy>0)){			
						makeHex(centroidNew);
						drawnCentroids.push(centroidNew);
					}	
					
					//bottom left
					centroidNew={
								cx:xLeft,
								cy:yMidBot
							};
					if((!contains(drawnCentroids,centroidNew,'bot left'))&&(centroidNew.cx>0&&centroidNew.cy<botEdge)){			
						makeHex(centroidNew);
						drawnCentroids.push(centroidNew);
					}
					
			drawnAroundCentroids.push(cent); //cent has all 6 sides surrounded by hexagon
			
				var l=drawnCentroids.length;
				for(var i=0;i<l;i++){
					if(!contains(drawnAroundCentroids,drawnCentroids[i])){
					     //console.log('toDraw',drawnCentroids[i],'drawn',cent);
						 //console.log(i,drawnCentroids[i]);
						 drawHexAround(drawnCentroids[i]);
					}
				}
			// console.log(drawnAroundCentroids,drawnCentroids[i]);		
			}
			else{
				return false;
			}
		}
		
	function contains(a, obj,callFrom) {
			var l = a.length;
			
			
			for(var i=0;i<l;i++) {
			//console.log(a[i],obj);
			//console.log('('+a[i].cx +','+a[i].cy+')('+obj.cx+','+obj.cy+'):'+ (a[i].cx === obj.cx && a[i].cy === obj.cy)+' '+callFrom);
			//console.log(a[i].cy ,obj.cy);
				if ((Math.abs(parseInt(a[i].cx) - parseInt(obj.cx))<1) && (Math.abs(parseInt(a[i].cy) - parseInt(obj.cy))<1)) {  //having a tolerence of one pixel
				//console.log('true');
					return true;
					
				}
			}
		return false;
	}
	
	function getHex(a,cx,cy){
		var l = a.length;
		
			
			for(var i=2;i<l;i++) {
			//console.log('a',a[i]);	
				if ((Math.abs(parseInt(a[i].cx) - parseInt(cx))<1) && (Math.abs(parseInt(a[i].cy) - parseInt(cy))<1)) {  //having a tolerence of one pixel
				//console.log('true');
					return a[i];
					
				}
			}
		return null;
	}
}		
		
		