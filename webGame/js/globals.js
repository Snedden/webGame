hexArray=new Array();
unitArray=new Array();
var hexMeshObj;


var gameId="<?php echo $_GET['gameId'] ?>";
var player="<?php echo $_GET['player']?>";
var PlayerId;
var svgns = "http://www.w3.org/2000/svg";
var turn;
var player1;
var player2;

var gameObj; //gameInit object

var addedListeners = {


};

function addListenerIfNone(addTo,eventType, func) {
    if ((addedListeners[addTo]===func)&&(addedListeners[eventType] ===eventType)) return;//event is alreaday present
    addedListeners[addTo] = func;//if not add
    addedListeners[eventType] =eventType;
    addTo.addEventListener(eventType, func);
    console.log('event ',eventType,' added on ', addTo);
}

console.log('global');

