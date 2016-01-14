/**
 * Created by Snedden27 on 1/6/2016.
 */

function InGameFunctions(){
    var self=this;
    this.lastTimeStamp='1899-11-30 00:00:00';

    (function init(){
        readInGameChatsAjax();
        addChatListeners(); //chat intervaity
    })();



    function addChatListeners(){
        (document.getElementsByTagName('body')[0]).addEventListener('keydown',function(e){ //bind when user starts typing
            $('#chatText').focus();                //bring input box in focus
            if(e.keyCode == 13){   //enter clicked

                $('#chatTextbtn').click();
            }


        });
    }
   this.enterInGameChat=function enterInGameChat(userId){
        console.log('userId ',userId);
        var chatData={};
        chatData['chatMsg']=$("#chatText").val();
        chatData['userId']=userId;
        chatData['gameId']=gameId;
        console.log('chat inserted is ',$("#chatText"));

        ajaxCall('POST',{method:'enterInGameChat',a:'game',data:chatData},callBackEnterInGameChat);
    }

    function callBackEnterInGameChat(jsonObj){
        console.warn('chat entered in DB');
        $('#chatText').val('')
    }


    function readInGameChatsAjax(){
        var chatData={};

        chatData['lastTimeStamp']=self.lastTimeStamp;
        chatData['gameId']=gameId;
        console.log('calling inGameReadChats')
        ajaxCall("GET",{method:'readInGameChats',a:"game",data:chatData},callbackReadInGameChat);

        setTimeout(readInGameChatsAjax,1000);
    }



    function callbackReadInGameChat(jsonObj){
        console.log(jsonObj);
        console.log( typeof jsonObj);
        var months=['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];

        if( jsonObj!=null){
            self.lastTimeStamp=jsonObj[0].latestChatTime;
            console.log( 'is object' );
            // $('#chatMessages').text(''); //clear previous chat messages
            if(jsonObj!=null){
                for (var i=0,l=jsonObj.length;i<l;i++){

                    // Split timestamp into [ Y, M, D, h, m, s ]
                    var t = jsonObj[i].timestamp.split(/[- :]/);

                    // Apply each element to the Date function
                    var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);

                    // console.log('Appended ',jsonObj[i].text);
                    var chatElement=$('<li class="media"> ' +
                        '<div class="media-body">'+
                        '<div  class="media"> ' +
                        '<a class="pull-left" href="#">' +
                        '<span class="glyphicon glyphicon-user"></span> ' +
                        '</a> ' +
                        '<div  class="media-body" >' +jsonObj[i].text+
                        '<br />'+
                        '<small class="text-muted">'+jsonObj[i].first_name+' '+jsonObj[i].last_name+' | '+d.getDate()+' '+months[d.getMonth()]+' at '+d.getHours()+':'+d.getMinutes()+'</small>' +
                        '<hr />'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</li>');

                    $('#chatMessages').append(chatElement);

                    //return false;

                }
                $('#chatMessages').animate({ scrollTop: $('#chatMessages')[0].scrollHeight }, "slow");
            }

        }
       // checkSessionTimeOut();//check for session TImeout

    }

}

