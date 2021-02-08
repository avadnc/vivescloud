            $(document).ready(function(){

                $("#imprimir").click(function(){
                    abrirWeb("/custom/vivescloud/ticket.php",{id,266});    
                });

                function abrirWeb(url,data){
                        $.post(url,data, function (data) {
                        var w = window.open("about:blank");
                        w.document.open();
                        w.document.write(data);
                        w.document.close();
                    });
                 }
            });