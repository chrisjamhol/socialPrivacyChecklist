StatsView = function(){
    
    this.init = function(){
        $('#col2, #col3').html("");
        $.get("stats.php",function(data){
            $('#col2').html(data);
        });
    }
}

function getView(){
    return StatsView;
}

