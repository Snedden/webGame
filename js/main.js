
function GameInit() {
			console.log('main');
			side=35;
			hexArray=new Array();
			//player1=new  Player('player','Snedden');
			
		    unitArray=new Array();
			hexMeshObj=new hexMesh();
			hexMeshObj.drawOver();
			hexMeshObj.drawUnits();

			//Start heartbeats
			checkTurnAjax('checkTurn',gameId);
			whoIsWining();

			//alert(playerId);
			//initGameAjax('start', gameId);
			
			this.changeHelpInfo=function changeHelpInfo(changeTo){
             	var infoText=document.createTextNode(changeTo);
				var infoPara=document.getElementById('infoPara');
                while(infoPara.firstChild){
                	infoPara.removeChild(infoPara.firstChild);
                }

				infoPara.appendChild(infoText);

			}
		}