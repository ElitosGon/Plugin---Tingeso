(function (Mibew, $) {
    // Initialize separated Marionette.js module for the plugin.
    var module = Mibew.Application.module(
        'TaisaPlusBotPlugin',
        {startWithParent: false}
    );

    Mibew.Application.on({
        'start': function() {
            module.start();
        },
        'stop': function() {
            module.stop();
        }
    });

    module.addInitializer(function() {
        
        
        var onButton =  '<a id ="send-message-ab-a" class="boton" href="" onClick="bot_on()" title="Activar Bot"> Activar Bot</a>';
        var offButton = '<a id ="send-message-ab-d" class="boton" href="" onClick="bot_off()" title="Desactivar Bot"> Desactivar Bot</a>';
        
        var div = document.getElementById("post-message");
        
        var url = window.location.href;
        var base = url.split('/operator/chat/')[0];
        var thread_id = url.split('/operator/chat/')[1].split('/')[0];
        var token = url.split('/operator/chat/')[1].split('/')[1];

        div.innerHTML = div.innerHTML;

        
        $.get(base + "/operator/bot/status/"+thread_id , function(data, status){
                if(data === "1" || data === "-1" || data === "-2" || data === "-3" || data === "-4"){
                    div.innerHTML = onButton + div.innerHTML;
                }else{
                    div.innerHTML = offButton + div.innerHTML;
                }
        });

        // This will emit an event with the value of window.someData every minute
        window.setInterval(function() {
          $.get(base + "/operator/bot/status/"+thread_id , function(data, status){
                if(data === "1" || data === "-1" || data === "-2" || data === "-3" || data === "-4"){
                    var bot = document.getElementsByClassName("boton")[0];
                    bot.parentNode.removeChild(bot);
                    div.innerHTML = onButton + div.innerHTML;
                }else{
                    var bot = document.getElementsByClassName("boton")[0];
                    bot.parentNode.removeChild(bot);
                    div.innerHTML = offButton + div.innerHTML;
                }
            });
        }, 1000 * 3);
       
    });

    

    

})(Mibew, jQuery);

function bot_on(){
    var url = window.location.href;
    var base = url.split('/operator/chat/')[0];
    var thread_id = url.split('/operator/chat/')[1].split('/')[0];
    var token = url.split('/operator/chat/')[1].split('/')[1];

    $.get(base + "/operator/bot/on/"+thread_id+"/"+token, function(data, status){
        if(data === "true"){
            alert("Bot desactivado exitosamente");
        }else{
            alert("No se logro desactivar bot");
        }
    });
};

function bot_off(){
    var url = window.location.href;
    var base = url.split('/operator/chat/')[0];
    var thread_id = url.split('/operator/chat/')[1].split('/')[0];
    var token = url.split('/operator/chat/')[1].split('/')[1];

    $.get(base + "/operator/bot/off/"+thread_id+"/"+token, function(data, status){
        if(data === "true"){
            alert("Bot desactivado exitosamente");
        }else{
            alert("No se logro desactivar bot");
        }
    });
}
