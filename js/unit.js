//unit funcitons and attributes
function Unit(unitType,hex,playerId,typeCount){
	var id;
	var unitType;
	var num;   //number to reference the unit array
	var hexagon;
	var selected;
	var isMoving;
	var speed;
	var design;
	var playerId;
	var typeCount;
	var fullHp;   //max hit points
	var currentHp;//current hit points
	var hpTextEle;//HP text html ele associated with the unit 
	var unitEle; //htmlDOm associated with this unit
	var attack;
	var isAlive; //is unit is alive
	var turnEnded;

	var self=this;

	this.unitType=unitType;
	this.isAlive=true;
	this.typeCount=typeCount;
	hexArray[hex.num].isOccupied=true; //set true as occuipied by  unit
	this.hexagon=hex;
	this.playerId=playerId;
	this.id=this.unitType+'|'+this.typeCount+'|'+this.playerId;
	this.hp=100;
	this.currentHp=this.hp;
	this.attack=25;
	this.speed=800; //speed is in pixels the unit can move
	
	this.isMoving=false;
	this.selected=false;
	this.turnEnded=false;

	this.unselectAll=unselectAll;



	//hp==0 mean dead decided therer is no need to out of memory

	this.destroyUnit=function destroyUnit(){
		//remove from screen
		var parent = document.getElementById("svgId");
		parent.removeChild(this.unitEle); //remove element
		parent.removeChild(this.hpTextEle);//text hp text
		//remove from memory
		console.log('taking out unit at position ',this.num);
		//unitArray.splice(this.num,1);
		//delete unitArray[this.num];
		this.isAlive=false;
		this.hexagon.isOccupied=false;//make hexgon unoccupied after unit is dead
	}
	
	
	//change icon to sword if mouse hover
	this.unitHovered=function unitHovered(ele){
		//something is selected
		if(typeof hexMeshObj.selectedUnit.playerId!=='undefined'){
			var unitObj=this;
			var attackFromHexEle;//hexEle the attack will take place from

			var idArr=ele.id.split('|');  //knight|1|0


			var hoveredUnitPlayerId=idArr[2];
				if(hoveredUnitPlayerId!=PlayerId){//sword cursor only on enemy units 
					console.log(hexMeshObj.selectedUnit.playerId+'!='+this.playerId);
					ele.style.cursor='url(cursors/use75.cur),pointer';

					var mX= unitObj.hexagon.cx;
					var mY=unitObj.hexagon.cy+110;//hard coding offset ,might need to change this as HTML DOM chahnges
					var hexId= hexArray[this.hexagon.num].id;
					var hexRowCol=hexId.toString().split(",");
					var hexRow=Number(hexRowCol[0]);
					var hexCol=Number(hexRowCol[1]);
					//these values depend on the current column
					var hexIdRUp=null;
					var hexIdRDown=null;
					var hexIdLUp=null;
					var hexIdLDown=null;
					var hexEleRUp=null;
					var  hexEleRDown=null;
				    var  hexEleLUp=null;
				    var  hexEleLDown=null;
					//this values are same regarless of the column
					var hexIdUp=(hexRow-1)+','+hexCol;
					var hexEleUp= document.getElementById(hexIdUp);
					var hexIdDown=(hexRow+1)+','+hexCol;
					var hexEleDown= document.getElementById(hexIdDown);

					var attackFromHex;//the hex the attack would take place from

					//to account for irregular alignation of hex columns
					if(hexCol%2!==0){ //columsn 1,3,5,...
						hexIdRUp=(hexRow-1)+','+(hexCol+1);
						hexEleRUp= document.getElementById(hexIdRUp);

						hexIdRDown=hexRow+','+(hexCol+1);
						hexEleRDown= document.getElementById(hexIdRDown);

						hexIdLUp=(hexRow-1)+','+(hexCol-1);
						hexEleLUp= document.getElementById(hexIdLUp);

						hexIdLDown=hexRow+','+(hexCol-1);
						hexEleLDown= document.getElementById(hexIdLDown);
					}
					else{//columns 2,4,6 ...
						hexIdRUp=hexRow+','+(hexCol+1);
						hexEleRUp= document.getElementById(hexIdRUp);

						hexIdRDown=(hexRow+1)+','+(hexCol+1);
						hexEleRDown= document.getElementById(hexIdRDown);

						hexIdLUp=hexRow+','+(hexCol-1);
						hexEleLUp= document.getElementById(hexIdLUp);

						hexIdLDown=(hexRow+1)+','+(hexCol-1);
						hexEleLDown= document.getElementById(hexIdLDown);
					}

					//console.warn('up',hexEleUp,'down',hexEleDown,'UR',hexEleRUp,'DR',hexEleRDown,'UL',hexEleLUp,'DL',hexEleLDown);
					window.moveUnitHandler=function moveUnitHandler(){
						unitObj.moveToAttackUnit(hexMeshObj.selectedUnit,attackFromHex);
					};

                    window.moveAttackFromHex=function(e){
						console.log('mX:'+unitObj.hexagon.cx+' mY:'+(unitObj.hexagon.cy)+' pageX:'+ e.pageX+' pageY:'+ (e.pageY-110));
						console.log('Math.abs(e.pageX-mX)',Math.abs(e.pageX-mX))




						if (e.pageY < mY&&Math.abs(e.pageX-mX)<6&&hexRow!=1) {//up
							if(hexEleUp) {
								hexEleUp.style.fill="#ff0000";
							}
							//reset other highlighted hexs
							if(hexEleDown){
								hexEleDown.style.fill=null;
							}
							if(hexEleRDown){
								hexEleRDown.style.fill=null;
							}
							if(hexEleLUp){
								hexEleLUp.style.fill=null;
							}
							if(hexEleLDown){
								hexEleLDown.style.fill=null;
							}
							if(hexEleRUp){
								hexEleRUp.style.fill=null;
							}
							//assgin hex
							if(hexEleUp){
								attackFromHexEle=hexEleUp;
							}

						} else if(e.pageY > mY&&Math.abs(e.pageX-mX)<6) {//down

							if(hexEleDown) {
								hexEleDown.style.fill="#ff0000";
							}
							//reset other highlighted hexs
							if(hexEleUp){
								hexEleUp.style.fill=null;
							}
							if(hexEleRDown){
								hexEleRDown.style.fill=null;
							}
							if(hexEleLUp){
								hexEleLUp.style.fill=null;
							}
							if(hexEleLDown){
								hexEleLDown.style.fill=null;
							}
							if(hexEleRUp){
								hexEleRUp.style.fill=null;
							}
							//assgin hex
							if(hexEleDown){
								attackFromHexEle=hexEleDown;
							}
						}
						else if(e.pageX > mX&& e.pageY < mY) {//right up

							if(hexEleRUp) {
								hexEleRUp.style.fill="#ff0000";
							}
							//reset other highlighted hexs
							if(hexEleUp){
								hexEleUp.style.fill=null;
							}
							if(hexEleRDown){
								hexEleRDown.style.fill=null;
							}
							if(hexEleLUp){
								hexEleLUp.style.fill=null;
							}
							if(hexEleLDown){
								hexEleLDown.style.fill=null;
							}
							if(hexEleDown){
								hexEleDown.style.fill=null;
							}
							//assgin hex
							if(hexEleRUp){
								attackFromHexEle=hexEleRUp;
							}
						}
						else if(e.pageX > mX&& e.pageY > mY) {//rightdown

							if(hexEleRDown) {
								hexEleRDown.style.fill="#ff0000";
							}
							//reset other highlighted hexs
							if(hexEleUp){
								hexEleUp.style.fill=null;
							}
							if(hexEleRUp){
								hexEleRUp.style.fill=null;
							}
							if(hexEleLUp){
								hexEleLUp.style.fill=null;
							}
							if(hexEleLDown){
								hexEleLDown.style.fill=null;
							}
							if(hexEleDown){
								hexEleDown.style.fill=null;
							}
							//assgin hex
							if(hexEleRDown){
								attackFromHexEle=hexEleRDown;
							}
						}
						else if(e.pageX < mX && e.pageY<mY) {//left up

							if(hexEleLUp) {
								hexEleLUp.style.fill="#ff0000";
							}
							//reset other highlighted hexs
							if(hexEleUp){
								hexEleUp.style.fill=null;
							}
							if(hexEleRUp){
								hexEleRUp.style.fill=null;
							}
							if(hexEleRDown){
								hexEleRDown.style.fill=null;
							}
							if(hexEleLDown){
								hexEleLDown.style.fill=null;
							}
							if(hexEleDown){
								hexEleDown.style.fill=null;
							}
							//assgin hex
							if(hexEleLUp){
								attackFromHexEle=hexEleLUp;
							}
						}
						else  { //left down //if(e.pageX < mX && e.pageY>mY)
							if(hexEleLDown) {
								hexEleLDown.style.fill = "#ff0000";
							}
								//reset other highlighted hexs
							if(hexEleUp){
								hexEleUp.style.fill=null;
							}
							if(hexEleRUp){
								hexEleRUp.style.fill=null;
							}
							if(hexEleRDown){
								hexEleRDown.style.fill=null;
							}
							if(hexEleLUp){
								hexEleLUp.style.fill=null;
							}
							if(hexEleDown){
								hexEleDown.style.fill=null;
							}
							//assgin hex
							if(hexEleLDown){
								attackFromHexEle=hexEleLDown;
							}

						}

						//console.warn('attackFRomEnd',attackFromHexEle);
						if(attackFromHexEle){
							attackFromHex=attackFromHexEle.getAttribute('data-hexNum');
						}




						if(attackFromHex){//to avoid catching over board hexs'
							//removeListenerIfPresent(ele,'click',window.moveUnitHandler);     //remove mouseclick

							$(ele).unbind( "click" );
							$(ele).bind( "click",window.moveUnitHandler);

						}




					}

					//addListenerIfNone(ele,'mousemove',moveAttackFromHex);
					$(ele).bind('mousemove',moveAttackFromHex);



					addListenerIfNone(ele,'mouseout', function(){
						console.log('mouse out');
						ele.style.cursor='pointer';
						//reset all hexagons around to deafult
						if(hexEleDown){
							hexEleDown.style.fill=null;
						}
						if(hexEleUp){
							hexEleUp.style.fill=null;
						}
						if(hexEleRUp){
							hexEleRUp.style.fill=null;
						}
						if(hexEleRDown){
							hexEleRDown.style.fill=null;
						}
						if(hexEleLUp){
							hexEleLUp.style.fill=null;
						}
						if(hexEleLDown){
							hexEleLDown.style.fill=null;
						}


						//removeListenerIfPresent(this.unitEle,'mousemove',moveAttackFromHex);



					});

					
				}	
			}
	}
	
	//Move the a neighbour cell before attack,can only attack from top cell
	this.moveToAttackUnit=function moveToAttackUnit(unit,attackFromHex){
		var amStandingNextToVictim;
		//removeListenerIfPresent(this.unitEle,'mousemove',moveAttackFromHex);

		$(this.unitEle).unbind('mousemove',moveAttackFromHex);
		$(this.unitEle).unbind( "click" );
		if(turn===PlayerId){	
			// attackedUnit is  this,attacking unit is 'unit';


			//var neighbour=hexArray[neighNum];
			console.log('moveToAttack');
			console.warn('hexNum',attackFromHex,' hex ',hexArray[attackFromHex]);
			if(hexArray[attackFromHex])
			{
				amStandingNextToVictim=(hexArray[attackFromHex].num==unit.hexagon.num);
				if(!hexArray[attackFromHex].isOccupied||amStandingNextToVictim){//no one is blocking the enemmy and I the blocker is not myself
					//moveSelected(moveTo,mover,fromUser,isAttaking,attackedUnit)
					console.log('moving');
					if(hexMeshObj.moveSelected(hexArray[attackFromHex],unit,true,true,this)){//if movement succesful  than attack
						this.attackUnit(unit);
					}

				}
				else{
					gameObj.changeHelpInfo('unit blocked from that direction');
				}
			}
		}
		else {
				gameObj.changeHelpInfo('Not your turn');
		}
	}
     //attack funciton ,
	this.attackUnit=function attackUnit(unit){
		// attackedUnit is  this,attacking unit is 'unit';
     	var aliveUnits=0;

     	this.currentHp=this.currentHp-unit.attack;
     	this.hpTextEle.innerHTML=this.currentHp;
		if(this.currentHp<=0){
			this.destroyUnit();
		}
     	console.warn('unit under attack!!!',this);
		//checking how many units are alive
		for(var i= 0,l=unitArray.length;i<l;i++){
		console.log('unit of player',unitArray[i].playerId,'exist');
			if(playerId===unitArray[i].playerId&&unitArray[i].isAlive){  //if my unit are alive in unit array
				console.log('your alive unit  count ++ ');
				aliveUnits++;
			}
		}
		console.log('alive count',aliveUnits);
        if(aliveUnits==0){
			console.log('you have lost');
			ajaxWinGame(turn=Math.abs(this.playerId-1)); //as winner would be the other player
		}
		console.log('unitArray',unitArray);
	}
	
	this.makeUnit=function makeUnit(board){
	    var unitObj=this;
		console.log('make unit',this);
       

		var unit=document.createElementNS(svgns,'circle');
		this.unitEle=unit;
		this.unitEle.setAttributeNS(null,'cx',this.hexagon.originX); //everything is placec at origin then translated
		this.unitEle.setAttributeNS(null,'cy',this.hexagon.originY);
        var transformAttr = ' translate(' +this.hexagon.cx + ',' + this.hexagon.cy+ ')';
		this.unitEle.setAttributeNS(null,'transform',transformAttr);
		this.unitEle.setAttributeNS(null,'r',this.hexagon.side*0.6);
		//console.log('Player id ',unit.playerId);
		if(this.playerId===0){
			this.unitEle.setAttributeNS(null,'fill','green');
		}
		if(this.playerId===1){
		    this.unitEle.setAttributeNS(null,'fill','blue');
		}
		this.unitEle.setAttributeNS(null,'id',this.unitType+'|'+this.typeCount+'|'+this.playerId);
		
		//Enemy units not selectable

		this.unitEle.addEventListener('click',function(){unitObj.selectUnit()})
		//this.unitEle.onclick=function(){unitObj.selectUnit()};
			
		
			
		
		//make sure something is selected

		this.unitEle.addEventListener('mouseover', function(){console.log('mouse over');
										   unitObj.unitHovered(unit);	
								  });
			

		//var svg=document.getElementsByTagName('svg')[0];
		this.num=unitArray.length;
		//add to memory
		unitArray.push(this); //[ush into global unit array
		//add to screen
		board.appendChild(unit);

		//adding hp text
        var txt= document.createElementNS(svgns ,"text");
        txt.setAttribute('fill', 'red');
		txt.setAttribute('id','hpOf'+this.id);
		var data = document.createTextNode(this.currentHp);
		txt.appendChild(data);
		board.appendChild(txt);	
        
        this.hpTextEle=txt;
        this.moveHpText();
	}
	
    //add Hp text
    this.moveHpText=function moveHpText(){
    	//appending hp  text
		
		this.hpTextEle.setAttribute('x', this.hexagon.cx-this.hexagon.side/2);
		this.hpTextEle.setAttribute('y', this.hexagon.cy+this.hexagon.side*1.732*.5);

    }

	
	//user selects this unit
	this.selectUnit=function selectUnit(){
		//console.log(turn,PlayerId);	
		if(this.playerId==PlayerId){
			if(turn===PlayerId)
			{	
				if(hexMeshObj.selectedUnit.isMoving===false){
				    //console.log('selected Unit',this);
					hexMeshObj.selectedUnit=this;
					
					hexMeshObj.say();
					var unitEle=document.getElementById(this.id);
					//unselected others
					unselectAll();
					this.selected=true;
				     //select this one
					hexMeshObj.selectedEle=unitEle;
							if(unitEle){
								unitEle.setAttribute('class','selected'); //change css
							}
							else{
								console.error('tying to selected element that dont exist in the dom');
							}
				}
				else{
					console.warn('Cannot select while a piece is moving!');
				}
			}
			else{
				console.warn('Not your turn yet.')
				gameObj.changeHelpInfo('Not your turn yet.')
			}	
		}	
		else{
			console.warn('Not your unit');
			gameObj.changeHelpInfo('Not your unit')
		}
	}
	
	function unselectAll(){
	//console.log('unitArray',unitArray);
	
	for(var i=0,l=unitArray.length;i<l;i++){
			//console.log('unit ',unitArray[i]);
			//if unit is selected
			if(unitArray[i].selected){
				unitArray[i].selected=false;
				var unitEle=document.getElementById(unitArray[i].id); //change css
				if(unitEle){
					unitEle.setAttribute('class','');
				}
				else{
					console.error('tying to unselected element that dont exist in the dom');
				}
			}
		}
	}
	function move(hexagon1,hexagon2){
	
	}
	
	function attack(){
	};
}