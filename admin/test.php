<html>
    <head>
        <script>
            var uhrzeitScript;
            window.setInterval(function(){
                uhrzeitScript = "<?php echo date('h:i:s'); ?>";
                document.getElementById("Uhrzeit").innerHTML = uhrzeitScript;
                console.log(uhrzeitScript);
            },1000);
        </script>
    </head>
    <body>
    <p>
        Uhrzeit: <div id="Uhrzeit">
            
                </div> 
    </p>
    </body>
   

</html>
