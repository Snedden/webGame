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
	var unitEle;
	var attack; 

	this.unitType=unitType;
	this.typeCount=typeCount;
	hexArray[hex.num].isOccupied=true; //set true as occuipied by  unit
	this.hexagon=hex;
	this.playerId=playerId;
	this.id=this.unitType+'|'+this.typeCount+'|'+this.playerId;
	this.hp=100;
	this.currentHp=this.hp;
	this.attack=50;
	
	this.isMoving=false;
	this.selected=false;

	this.unselectAll=unselectAll;
	
	
	
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
					
						ele.addEventListener('click',function(){unitObj.moveToAttackUnit(hexMeshObj.selectedUnit)});//attack unit unitObj 
					
				}	
			}
	}
	
	//Move the n
	this.moveToAttackUnit=function attackUnit(unit){
		if(turn===PlayerId){	
			// attackedUnit is  this,attacking unit is 'unit';
			var neighNum=this.hexagon.num-1;  //this.hexagon.num-1 is neighbour
			
			//var neighbour=hexArray[neighNum]; 
			console.log(unitArray[2].hexagon,unit.hexagon);
				if(hexArray[neighNum].num!=unit.hexagon.num){
					if(!hexArray[neighNum].isOccupied){
						hexMeshObj.moveSelected(hexArray[neighNum],unit,true,true);    //move to neighbour first before physically attacking	
					}
					else{
						console.warn('Cannot attack unit surrounded');
					}
					  
				}
				else{
					this.attackUnit(); //attack if standing next to the victim
				}
			}
		else{
				gameObj.changeHelpInfo('Not your turn');
			}
	}

	this.attackUnit=function attackUnit(unit){
		// attackedUnit is  this,attacking unit is 'unit';
     	console.warn('unit under attack!!!')

	}
	
	this.makeUnit=function makeUnit(board){
	    var unitObj=this;
		//console.log('make unit');
       

		var unit=document.createElementNS(svgns,'circle');
		this.unitEle=unit;
		this.unitEle.setAttributeNS(null,'cx',this.hexagon.cx);
		this.unitEle.setAttributeNS(null,'cy',this.hexagon.cy);
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
		
			
		this.unitEle.onclick=function(){unitObj.selectUnit()};
			
		
			
		
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
		unitArray.push(this);
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