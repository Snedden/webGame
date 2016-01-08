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

var inGameObj;

var gameObj; //gameInit object

var addedListeners = {


};

function addListenerIfNone(addTo,eventType, func) {//needs  id as a attribute

    //console.warn("addTo:",addTo.id);
    console.warn("type:",eventType);
    //console.warn("func:",func);
    if(addTo&&addTo.id){
        if (addedListeners[addTo.id+eventType]===eventType)
        {
            console.warn('event not added');
            return;

        }//event is alreaday present
        else{
            addedListeners[addTo.id+eventType]= eventType;

            addTo.addEventListener(eventType, func,true);
            console.warn('event added');
            //console.warn('addedListerners',addedListeners);
        }

        // console.warn('event ',eventType,' added on ', addTo);
    }
    else{
        console.warn("Elements needs to have id attribute or not null");
        return false;
    }
}

function removeListenerIfPresent(addTo,eventType, func) {//needs  id as a attribute
    //console.warn("addTo:",addTo.id);
    //console.warn("type:",eventType);
    //console.warn("func:",func);
    if(addTo&&addTo.id){
        if (addedListeners[addTo.id+eventType]){ //event present
            addedListeners[addTo.id+eventType]= null;

            addTo.removeEventListener(eventType, func,true);
            console.warn("event  removed!");
            //console.warn('addedListerners',addedListeners);
        }
        else{
            //console.warn("event not removed!");
            return false;
        }

        // console.warn('event ',eventType,' added on ', addTo);
    }
    else{
        console.warn("Elements needs to have id attribute or not null");
        return false;
    }
}

console.log('global');

