ArticleEditView = function(){
    //   var x = 10;
    //    $.map($('.nav li'),function(value,key){
    //        console.log(value);
    //       $(value).animate({top:(282+x)+'px',left:(582-(x+100))+'px'},{queue:false,duration:1000,delay:x+900});
    //       x = x+10;
    //    });
    this.init = function(html){
        $('#col2').load(html);        
    }
    
     
}; 

function getView(){
    return ArticleEditView;
};