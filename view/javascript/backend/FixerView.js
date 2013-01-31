FixerView = function(){
    
    this.init = function(html){
    	$('#col2, #col3').html("");
    	$('#col2').load(html,function(){
    		$('#fixPosButton').unbind('click').click(function(){
    			$.get("../admin/Fixer.php",{"op": "fixChecklistPositions"},function(){
    				alert("repariert");
    			});
			}); 
    	});       
    }
}

function getView(){
    return FixerView;
}

