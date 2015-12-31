//DEfine each hexagon cell
function Hexagon(cx,cy,side,id,num){
	//console.log('hexagon');
	this.originX=0;
	this.originY=0;//All hexagons start at zero and are than translated to cx and cy
	this.cx=cx;    //translate by cx
	this.cy=cy;	   //translate by cy
	this.side=side;
	this.id=id;
	this.isOccupied=false;
	this.num=num;  		//serial number for reference of global array
	this.elem;          //HTML DOM Element

	//console.log('centroid',cent);
	
};

Hexagon.prototype.getSelectedUnit=function(){
	for(var i= 0,l=unitArray.length;i<l;i++){  //get the selected units hex before calculating distance
		if(unitArray[i].selected){
			var selectedUnit=unitArray[i];
			break;
		}
	}
	if(selectedUnit){
		return selectedUnit;
	}
	else{

		return false;
	}
}

Hexagon.prototype.getDistanceFromSelected=function getDistanceFromSelected(){
	console.log('getDistance called');
	if(hexMeshObj.selectedUnit.hexagon) {
		console.log('something is selected');
		//formula - sqrt((x1-x2)^2+(y1-y2)^2)
		var dist = Math.sqrt(Math.pow((hexMeshObj.selectedUnit.hexagon.cx - this.cx), 2) + Math.pow((hexMeshObj.selectedUnit.hexagon.cy - this.cy), 2));
		console.log('distance:', dist);
		return dist;
	}
	else{
		console.log('no object selected');
		return false;
	}

}
//Add attributes to hex  html dom object
Hexagon.prototype.makeHex=function(ele){//ele is the parent element to draw in 
	//console.log(this);
	var hexObj=this;
	var x1,x2,x3,x4,x5,x6;
	var y1,y2,y3,y4,y5,y6;



	x1=this.originX-this.side;
	console.log(this.originX+'-'+this.side,'x1:',x1);
	y1=this.originY;
	
	x2=this.originX-this.side/2;
	y2=this.originY+parseInt(Math.sqrt(3)*this.side/2);
	
	x3=this.originX+this.side/2;
	y3=this.originY+parseInt(Math.sqrt(3)*this.side/2);
	
	x4=this.originX+this.side;
	y4=this.originY;
	
	x5=this.originX+this.side/2;
	y5=this.originY-parseInt(Math.sqrt(3)*this.side/2);
	
	x6=this.originX-this.side/2;
	y6=this.originY-parseInt(Math.sqrt(3)*this.side/2);
	
	var hex=document.createElementNS(svgns,'polygon');
	hex.setAttributeNS(null,'points',x1+','+y1+' '+x2+','+y2+' '+x3+','+y3+' '+x4+','+y4+' '+x5+','+y5+' '+x6+','+y6);//create at origin
	hex.setAttributeNS(null,'class','hex');
	hex.setAttributeNS(null,'id',this.id);
	hex.setAttributeNS(null,'data-hexNum',this.num);

	//adding mouse events to hex dom
	this.elem=hex;

	//mouse enter
	this.elem.addEventListener('mouseenter',function(){
		console.log('adding event');
		var dist=hexObj.getDistanceFromSelected();
		var selectedUnit=hexObj.getSelectedUnit();
		if(selectedUnit){
			if(selectedUnit.speed>dist){
				console.log('running horse cursor');
				hex.style.cursor='url(cursors/horse.cur),pointer';
			}
			else{
				console.log('pointer cursor');
				hex.style.cursor='pointer';
			}

		}//something is selcted
	},false);
	//mouse leave
	this.elem.addEventListener('mouseleave',function(){
		this.removeEventListener('mouseenter',function(){
			hexObj.getDistanceFromSelected();
		},false);
		console.log('event removed');
	},false);
	//mouse click
	hex.addEventListener('click',function(){
		console.log('num:',hexObj.num);
		//var dist=hexObj.getDistanceFromSelected();
		//var selectedUnit=hexObj.getSelectedUnit();
		if( turn===PlayerId){
			//if(selectedUnit.speed>dist){
			if(hexMeshObj.selectedUnit.id){//something is selected
				if(!hexObj.isOccupied){
					hexMeshObj.moveSelected(hexObj,hexMeshObj.selectedUnit,true);
				}
				else{
					console.warn('Place already occupied');
				}
			}
			else{
				console.warn('select a unit first');
			}


		}
		else{
			gameObj.changeHelpInfo('Not your turn');
		}

	});
	//var svg=document.getElementsByTagName('svg')[0];
	ele.appendChild(hex);
	var transformAttr = ' translate(' +hexObj.cx + ',' + hexObj.cy+ ')';
	hex.setAttributeNS(null,'transform',transformAttr); //translate after created
	}