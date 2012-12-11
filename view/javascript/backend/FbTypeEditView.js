FbTypeEditView = function(){
    
    this.init = function(html){
         $('#col3').html("");
         $('#col2').load(html,function(){
//-----------------------new Entry Button
             $('#addFbTypeButton').unbind('click').click(function(){
                 
                 var uploader = new qq.FileUploader({
                    element: document.getElementById('iconUpload'),
                    action: '../tools/uploader/server/Uploader.php',
                    debug: false,
                    params:{"directory": "../../../images/typeIcons/"},
                    onComplete: function(id,fileName,response){
                        $('.qq-upload-button').hide();
                        $('.qq-upload-file').html(response.filename);
                    }
                 }); 
                
                $('#newFbTypeDialog').dialog({width: 480});
                
                var countTypes = function(){
                    var count = 0;$.map($('#fbTypesContainerEdit button'),function(key, value){count++;});return count+1;
                }
                $('#newFbTypeInputPos').val(countTypes());
                
 //---------------------submit new entry
                $('#submitNewType').unbind('click').click(function(){
                    if(validateNewTypeDialog() === true){
                        if($('#newFbTypeInputPos').val() <= (countTypes()-1)){
                            var newOrderFlag = true;
                        }else{
                            var newOrderFlag = false;                            
                        }
                        $.get("../index.php",{"from": "back", "op": "addFbType", "newOrder": newOrderFlag, 
                            "name": $('#newFbTypeInputName').val(), 
                            "pos": $('#newFbTypeInputPos').val(),
                            "icon": $('.qq-upload-file').html(),
                            "desc": $('#newFbTypeInputDesc').val()
                        },function(){
                            getTypes();
                            $('#newFbTypeInputName').val("");
                            $('#newFbTypeInputPos').val("");
                            $('#newFbTypeInputDesc').val("");
                            $('#newFbTypeDialog').dialog("close");
                        });
                    }
                });               
             });             
         });        
         
         this.getTypes();        
         
        
         var validateNewTypeDialog = function(){
             if($('#newFbTypeInputName').val() != ""){
                         $('#newFbTypeDialog .requiredField').hide();
                        if($('#newFbTypeInputPos').val() != ""){
                             $('#newFbTypeDialog .requiredField').hide();
                            if(typeof($('.qq-upload-file').val()) != "undefined"){
                                 $('#newFbTypeDialog .requiredField').hide();
                                if($('#newFbTypeInputDesc').val() != ""){                            
                                   return true;                             
                                }else{$('#newFbTypeDialog .requiredField').hide();$('#requiredFieldDescNewFb').show();}
                            }else{$('#newFbTypeDialog .requiredField').hide();$('#requiredFieldIconNewFb').show();}                            
                        }else{$('#newFbTypeDialog .requiredField').hide();$('#requiredFieldPosNewFb').show();}
                    }else{$('#newFbTypeDialog .requiredField').hide();$('#requiredFieldNameNewFb').show();}
         }
    }
    
    this.getTypes = function(){                   
                    
        
        $.getJSON("../index.php",{"from": "back"," op": "getFbTypes"},function(types){
            $('#fbTypesContainerEdit').html(types.typesHtml);
            $('#fbTypesOrder').html(types.controls);
            
            $.map($('#fbTypesContainerEdit button'), function(value, key){
                var pos = key;
        //clickevents TypeButtons
                (function(){                    
                     $('#fbTypesContainerEdit button:eq('+pos+')').click(function(){                        
                     //get Values from fbType and fill in the form   
                        $.getJSON("../index.php",{"from": "back", "op": "getTypeInfo", "pos": pos+1},function(type){
                            $('#fbTypeEditTypeId').html(type.infos.id);
                            $('#fbTypeEditInputName').val(type.infos.name);
                            $('#fbTypeEditDesc').val(type.infos.description);
                            $('#fbTypeEditInputPos').val(type.infos.pos);
                            $('#fbTypeEditSelectChecklist').html(type.checklists.options);
                            
                            $('#editFbTypeDialog').dialog({width: 380});
                            
                            // icon change    
                                $('#changeIconButton').unbind('click').click(function(){
                                    var currentImage = "../images/typeIcons/icon"+(pos+1)+".gif";
                                    $('#currentIcon').html('<img  src="'+currentImage+'" height="100"/>');
                                    $('#changeIconDialog').dialog();
                                    var uploader = new qq.FileUploader({
                                        element: document.getElementById('changeIconUpload'),
                                        action: '../tools/uploader/server/Uploader.php',
                                        debug: false,
                                        params:{"directory": "../../../images/typeIcons/", "icon":"icon"+(pos+1)},
                                        onComplete: function(id,fileName,response){
                                            $('.qq-upload-button').hide();  
                                            d = new Date();
                                            $('#currentIcon').html('<p>aktualisiert</p><img  src="'+currentImage+"?"+d.getTime()+'" height="100"/>');
                                            $('.qq-upload-file').html(response.filename);
                                            $.get("../index.php",{"from": "back", "op": "changeTypeIcon", "id": $('#fbTypeEditTypeId').html(), "filename": response.filename});
                                            window.setTimeout(function(){$('#changeIconDialog').dialog("close")}, 1500);
                                        }
                                    }); 
                                });
                                
                        //submit all change 
                            $('#submitFbTypeEdit').unbind('click').click(function(){
                                console.log("click");
                                var values = {
                                    "fbTypeId": type.infos.id,
                                    "name": $('#fbTypeEditInputName').val(),
                                    "desc": $('#fbTypeEditDesc').val(),
                                    "checklist": $('#fbTypeEditSelectChecklist').val()
                                };
                                
                                $.get("../index.php",{"from":"back", "op":"saveFbTypeChange", "values": values},function(){
                                    getTypes();
                                    
                                    $('#editFbTypeDialog').dialog("close");
                                }); 
                            });
                        });               
                    });
                })();
             
                
        //clickevents Controlbuttons
                (function(){
                        //change pos -1
                    $('.fbTypesOrderButtons:eq('+key+')').find('.fbTypePosLow').click(function(){
                        if((pos+1) > 1){
                            var values = {"operation": "lower", "currentPos": pos+1};
                            $.get("../index.php",{"from": "back", "op": "changeFbTypePosition", "values": values},function(){
                                getTypes();
                            });
                        }                        
                    });
                        //change pos +1
                    $('.fbTypesOrderButtons:eq('+key+')').find('.fbTypePosHigh').click(function(){
                        if((pos+1) < $('#fbTypesContainerEdit button').length){
                            var values = {"operation": "higher", "currentPos": pos+1};
                            $.get("../index.php",{"from": "back", "op": "changeFbTypePosition", "values": values},function(){
                                getTypes();
                            });
                        }                       
                    });
                    
                    $('.fbTypesOrderButtons:eq('+key+')').find('.fbTypeDelete').click(function(){
                        alert("really delete "+(pos+1)+"??");
                        $.get("../index.php",{"from": "back", "op": "delFbType", "pos": pos+1},function(){
                            getTypes();
                        });
                    });
                    
                })();                
            });           
        });
    }
    
    var getTypes = this.getTypes;
}

function getView(){
    return FbTypeEditView;
};