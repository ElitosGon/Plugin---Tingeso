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
            
            var url = document.location.href;
            var base = url.split('/operator/')[0];
            var div = document.getElementById("alert");
            var div_fine = 'Sístema de bost funcionando correctamente <br clear="all">';
            var div_alert = 'Sístema de bost, problema de conexión    <br clear="all">';          

            $.get(base + "/bot/plugin/connection", function(data, status){
                    if(data == 200){
                        div.className = "fine";
                        div.style.visibility = "visible";
                        div.innerHTML = div_fine;
                    }else{
                        div.className = "alert";
                        div.style.visibility = "visible";
                        div.innerHTML = div_alert;
                    }
            }); 



            window.setInterval(function(){
                $.get(base + "/bot/plugin/connection", function(data, status){
                    if(data == 200){
                        div.className = "fine";
                        div.style.visibility = "visible";
                        div.innerHTML = div_fine;
                    }else{
                        div.className = "alert";
                        div.style.visibility = "visible";
                        div.innerHTML = div_alert;
                    }
                });               
            }, 1000 * 20);

    });

})(Mibew, jQuery);

