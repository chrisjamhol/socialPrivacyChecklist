Cubelook = function(){
    var current = $('.checklistPointPreview:eq(0)');
    var duration = 500;
    
    $('head').append('<link href="tools/cubeLook/cubelook.css" rel="stylesheet" type="text/css" />');
    //$('.checklistPointPreview:eq(0)').removeClass('top');
    
    
    
    $('.heading').click(function(){
        console.log($(this));
        $(current).parent().find('.checklistPointPreview').removeClass('front').addClass('top').height(0);
        $(this).parent().find('.checklistPointPreview').removeClass('top').addClass('front').height(289);
        current = $(this);
    });
}

/*
 
 $('.big').click(function(){
        $(current).removeClass('front').addClass('top');
        $(this).parent().find('.checklistPointPreview').removeClass('top').addClass('front');
        $(this).removeClass('big').addClass('small');
        //$(this).removeClass('checkButton').addClass('uncheckButton');
        /*var cur = current
        setTimeout(function(){
            console.log(cur);
            $('#checkedPoints').prepend('<div class="checkedPoint"><hr /><p>'+$('.checklistPoint:eq('+(cur+1)+')').find('.heading').text()+'</p></div>');
        },duration);
        current++;
    }); 

    $('.small').click(function(){
        $(current).removeClass('front').addClass('top');
        $(this).parent().find('.checklistPointPreview').removeClass('top').addClass('front');
        //$(this).removeClass('uncheckButton').addClass('checkButton');
        $(this).removeClass('small').addClass('big');
        current--;
    });
 
 **/