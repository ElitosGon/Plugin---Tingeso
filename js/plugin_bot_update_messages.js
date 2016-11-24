(function (Mibew, $) {
    // Initialize separated Marionette.js module for the plugin.
    var module = Mibew.Application.module(
        'TaisaPlusBotPlugin',
        {startWithParent: false}
    );

    var eventsMap = {
        'start': function() {
            module.start();
        },
        'stop': function() {
            module.stop();
        }
    }

    Mibew.Application.Chat.on(eventsMap);

    module.addInitializer(function() {
            
            
            var url = document.location.href;
            var base = url.split('/chat/')[0];
            var thread_id = url.split('/chat/')[1].split('/')[0];
          
            // This will emit an event with the value of window.someData every minute
            window.setInterval(function() {
                $.get(base + "/bot/plugin/update/"+thread_id, function(data, status){
                });               
            }, 1000 * 20);
        
        
    });

    

    

})(Mibew, jQuery);
