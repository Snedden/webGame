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
	this.speed=200; //speed is in pixels the unit can move
	
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
			//console.log(PlayerId,ele.id );
			var idArr=ele.id.split('|');  //knight|1|0
			//console.log('idArr',idArr);
			var hoveredUnitPlayerId=idArr[2];
				if(hoveredUnitPlayerId!=PlayerId){//sword cursor only on enemy units 
					console.log(hexMeshObj.selectedUnit.playerId+'!='+this.playerId);
					ele.style.cursor='url(cursors/use75.cur),pointer';

					addListenerIfNone(ele,'click', function(){unitObj.moveToAttackUnit(hexMeshObj.selectedUnit)});
					//ele.addEventListener('click',function(){unitObj.moveToAttackUnit(hexMeshObj.selectedUnit)});//attack unit unitObj
					
				}	
			}
	}
	
	//Move the a neighbour cell before attack,can only attack from top cell
	this.moveToAttackUnit=function moveToAttackUnit(unit){
		if(turn===PlayerId){	
			// attackedUnit is  this,attacking unit is 'unit';
			var neighNum=this.hexagon.num-1;  //this.hexagon.num-1 is neighbour, again being lazy here as using only top cell as neighbour
			
			//var neighbour=hexArray[neighNum];
			console.log('moveToAttack');
			console.log(unitArray[2].hexagon,unit.hexagon);
				//if(hexArray[neighNum].num!=unit.hexagon.num){
			var amStandingNextToVictim=(hexArray[neighNum].num==unit.hexagon.num);
					if(!hexArray[neighNum].isOccupied||(amStandingNextToVictim)){//no one is blocking the enemmy
						//moveSelected(moveTo,mover,fromUser,isAttaking,attackedUnit)
						if(hexMeshObj.moveSelected(hexArray[neighNum],unit,true,true,this)){//if movement succesful  than attack
							this.attackUnit(unit);
						}

					}
					else{
						console.warn('Cannot attack unit surrounded');
					}
					  
				//}
				//else{
				//	console.log('unit before call',unit);
				//	this.attackUnit(unit); //attack if standing next to the victim
				//}
			}
		else{
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
			
		this.unitEle.addEventListener('mouseout', function(){console.log('mouse out');
										   unit.style.cursor='pointer';	
										   
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