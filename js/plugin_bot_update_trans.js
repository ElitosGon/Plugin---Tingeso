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
            
            var div = document.getElementsByClassName("but")[0];
            window.setInterval(function() {
                location.reload();
                var url = document.location.href;
                var base = url.split('/chat/')[0];
                var thread_id = url.split('/chat/')[1].split('/')[0];
                $.get(base + "/bot/plugin/update/"+thread_id, function(data, status){
                });               
            }, 1000 * 20);
            
        
    });

    

    

})(Mibew, jQuery);

