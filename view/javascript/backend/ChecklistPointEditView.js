ChecklistPointsEditView = function(){
    var checklistId;
    var checklistName;
    var screenshots = [];        //screenshots from new Checklistpoint (temp)
    this.init = function(html,params){
        checklistId = params.id;
        checklistName = params.name;
        $('#col3').load(html,function(){
                //add ChecklistPoint button event
            $('#addChecklistPointButton').unbind("click").click(function(){
                $('#addChecklistPointOptionsDialog').dialog('open');                          
            });
            
                    $('#addChecklistPointOptionsDialog,#addChecklistPointOptionAddDialog').dialog({
                        autoOpen: false
                    });

                    $('#addChecklistPointOptionNew').unbind("click").click(function(){
                        var pos = $('.checklistPointEdit').length-1;
                        if(pos < 0){
                            pos = 0;
                        }
                        $.post("../index.php",{"from": "back", "op": "addNewChecklistPoint", "values":{"checklistId":checklistId,"pos": pos}},function(newId){
                            $('#addChecklistPointNewIdHolder p').text(newId);
                            $('#addChecklistPointNew').show();
                            $.get("../index.php",{"from":"back", "op":"getCountChecklistPoints", "values":{"checklistId":checklistId}},function(count){
                                $('#addChecklistPointMaxCount').text(" (in Liste: "+count+")");
                                
                                $('#addChecklistPointDialog').dialog({
                                    width: 900, 
                                    height: 620, 
                                    position: "center",
                                    autoOpen: true,
                                    close: function(){
                                        var checklistPointId = $('#addChecklistPointNewIdHolder p').text();
                                        $.get("../index.php",{"from": "back", "op": "checkForChecklistPointCreate", "checklistPointId": checklistPointId, "checklistId": checklistId},function(){                                
                                            getChecklistPoints();
                                        });
                                        screenshots = []; //clear screenshotlist for next use
                                        $('#addChecklistPointAvalibleScreenshots').html("");
                                        $('.addChecklistPointScreenshotsDropAfter').html("").removeClass("addChecklistPointScreenshotsDropAfter").addClass("addChecklistPointScreenshotsDropBevore");
                                        //$('#addChecklistPointInputText').destroyEditor();
                                        $('#addChecklistPointInputText').val("");
                                        $('#addChecklistPointInputHeading').val("");
                                        $('#addChecklistPointInputPos').val("");
                                        $('#addChecklistPointNewIdHolder p').text("");
                                        $(this).dialog('destroy');
                                    }
                                });
                                initEditor("#addChecklistPointInputText");
                                getScreenshots();
                                });
                        });
                    });

                    $('#addChecklistPointOptionAdd').unbind("click").click(function(){
                        $('#addChecklistPointOptionsDialog').dialog("close");
                        $('#addChecklistPointOptionAddDialog').dialog({height: 600, width: 600}).dialog("open");
                        
                        $.get("../index.php",{"from": "back", "op": "getExistingChecklistPoints"},function(existing){
                            $('#existingChecklistPoints').html(existing);
                                //remove used points
                            $.map($('.checklistPointEdit'),function(value,key){
                                var id = $(value).attr("id").replace("checklistPoint","");
                                $('input[name=selectedChecklistPoints][value='+id+']').parent().parent().remove();
                            });
                        });              
                        
                    });
                    
                    $('#addChecklistPointsImportSubmit').unbind("click").click(function(){
                        var chosenPoints = [];
                        $.map($('#existingChecklistPoints input:checked'),function(value,key){
                            chosenPoints.push($(value).attr("value"));                            
                        }); 
                        if(chosenPoints.length > 0){
                            $.get("../index.php",{"from": "back","op": "importChecklistPoints","ids":chosenPoints,"checklistId": checklistId},function(){                                
                                $('#addChecklistPointOptionAddDialog').dialog("close");
                                getChecklistPoints();
                            });
                        }
                    });
                    
                    $('#addChecklistPointsCopySubmit').unbind("click").click(function(){
                        var chosenPoints = [];
                        $.map($('#existingChecklistPoints input:checked'),function(value,key){
                            chosenPoints.push($(value).attr("value"));                            
                        }); 
                        $.get("../index.php",{"from": "back","op": "copyChecklistPoints","ids":chosenPoints,"checklistId": checklistId},function(response){                                
                            $('#addChecklistPointOptionAddDialog').dialog("close");
                            getChecklistPoints();
                        });
                    });
            
            $('#editChecklistButton').unbind("click").click(function(){
                $('#editChecklistDialog').dialog({
                    buttons: {
                        "Speichern": function(){
                            $.get("../index.php",{"from": "back","op":"editChecklist","values": {"name": $('#editChecklistDialogName').val(), "id": checklistId}});
                            params.getChecklists();
                            $(this).dialog("close");
                        },
                        "Abbrechen": function(){
                            $(this).dialog("close");
                        }
                    }
                });
                $('#editChecklistDialogName').val(checklistName);            
                
            });
            
            $('#deleteChecklistButton').unbind("click").click(function(){
                var confirmStatus =  confirm("Checklist löschen");
                if(confirmStatus){
                    $.get("../index.php",{"from": "back","op":"deleteChecklist","checklistId":checklistId},function(){
                        params.getChecklists();
                    });
                }else{}
            });
            
            
                //dialog Button Events
                    //addChecklistPointSubmitButton
            $('#addChecklistPointInputSubmit').unbind("click").click(function(){
                var text = $('#addChecklistPointInputText').getCode();
                var heading = $('#addChecklistPointInputHeading').val();
                var checklistPointId = $('#addChecklistPointNewIdHolder p').text();
                var pos = $('#addChecklistPointInputPos').val();
                if(text != "" && heading != ""){
                    $.post("../index.php",{"from": "back", "op": "addNewChecklistPoint","operator": "newPoint", 
                                                                                        "values": {
                                                                                            "checklistId": checklistId,
                                                                                            "checklistPointId": checklistPointId,
                                                                                            "text": text, 
                                                                                            "heading": heading,
                                                                                            "pos": pos                                                                                
                                                                                         }
                    },function(){
                        $('#addChecklistPointDialog').dialog("close");
                    });                     
                }else{
                    alert("Bitte eine Überschrift und Text eingeben");
                }                
            });            
                //editChecklistPointSubmitButton
            $('#editChecklistPointInputSubmit').unbind("click").click(function(){
                var text = $('#editChecklistPointInputText').getCode();
                var heading = $('#editChecklistPointInputHeading').val();  
                var pos = $('#editChecklistPointInputPos').val();              
                if(text != "" && heading != ""){
                    $.post("../index.php",{"from": "back", "op": "addNewChecklistPoint", 
                                                                            "values": {
                                                                                "checklistId": checklistId,
                                                                                "checklistPointId": $('#editChecklistPointDialogIdHolder').html(),
                                                                                "text": text, 
                                                                                "heading": heading,
                                                                                "pos": pos
                                                                             }
                        },function(error){
                            getChecklistPoints();
                            $('#editChecklistPointDialog').dialog("close");
                        });                        
                        $('#editChecklistPointInputText').destroyEditor();
                }else{
                    alert("Bitte eine Überschrift und Text eingeben");
                }
            });   
            
                    //add ChecklistScreenshotButton
            $('#addChecklistPointScreenshotButton,#editChecklistPointScreenshotButton').unbind("click").click(function(){               
                $('#addChecklistPointScreenshotDialog').dialog({width: 500});                
            });
            
                //submit screenshotChanges
            $('#addChecklistPointScreenshotSubmit').unbind("click").click(function(){
                $('#addChecklistPointScreenshotDialog').dialog("close");                               
            });
            
           $('#addChecklistPointScreenshotClear').unbind("click").click(function(){
               screenshots = []; //clear screenshotlist for next use
               $('.addChecklistPointScreenshotsDropAfter').html("").removeClass("addChecklistPointScreenshotsDropAfter").addClass("addChecklistPointScreenshotsDropBevore");
            }) ;
            
                            //new screenshotupload
                    $('#addChecklistPointNewScreenshotButton').unbind("click").click(function(){
                        var uploader = new qq.FileUploader({
                            element: document.getElementById('newScreenshotUploadButton'),
                            action: '../tools/uploader/server/Uploader.php',
                            params: {"directory": "../../../images/screenshots/","description": $('#newScreenshotDescription').val()},
                            debug: false,
                            uploadButtonText: "Datei auswählen und uploaden",
                            onSubmit: function(){
                                if($('#newScreenshotDescription').val() == ""){
                                   alert("Bitte zuerst eine Beschreibung des Bildes eingeben");
                                   return false; 
                                }
                            },
                            onComplete: function(id,fileName,response){
                                $('.qq-upload-file').html(response.fileName);
                                getScreenshots();
                                $('#newScreenshotUploadDialog').dialog("close");
                            }
                        });
                        $('#newScreenshotUploadDialog').dialog();
                    });
                    
                    $('#addChecklistPointNewScreenshotSubmit').unbind("click").click(function(){
                        if($('.qq-upload-file').html() != "" && $('#newScreenshotDescription').val() != ""){
                            $('#newScreenshotUploadDialog').dialog("close");
                            $.get("../index.php",{"from": "back",
                                            "op": "saveScreenshot",
                                            "filename": $('.qq-upload-file').html(),
                                            "directory": "../../../images/screenshots/",
                                            "description": $('#newScreenshotDescription').val()},
                                function(){
                                    getScreenshots(); 
                            }); 
                        }else{
                            alert("Bitte Beschreibung angeben und Bild hochladen");
                        }
                    });         
            
            $('.getScreenshotPathButton').unbind("click").click(function(){
                $.getJSON("../index.php",{"from":"back","op":"getScreenshotsPath"},function(paths){
                    var html = "";
                    $.map(paths,function(data,screenshotId){
                        html += '<tr>'+
                                    '<td><img src="../images/screenshots/thumbnails/'+paths[screenshotId]['name']+'"/></td>'+
                                    '<td><p>'+paths[screenshotId]['desc']+'</td>'+
                                '</tr>';
                    });
                    $('#screenshotPathsTable').html(html);
                    
                    $.map($('#screenshotPathsTable tr'),function(value,key){
                        $(value).unbind('click').click(function(){
                            $('#path p').text($(this).find('img').attr("src").replace("thumbnails/","").replace("../",""));
                            $('#screenshotPathsDialog').dialog("close");
                            $('#path').dialog();
                        });
                    });
                    
                    $('#screenshotPathsDialog').dialog("open");
                });
            });
                
            $('#addNewChecklistPointDialogChoiceExisting').unbind("click").click(function(){
                $(this).hide();
                $('#addNewChecklistPointDialogChoiceNew').hide();
                $('#addChecklistPointExisting').dialog();
            });

            $('#editChecklistPointInputDelete').unbind("click").click(function(){
                deleteChecklistPoint();
            });
            $('#screenshotPathsDialog').dialog({autoOpen:false,width: 400,height: 500});
        });
        
        getChecklistPoints();
    }
    
    this.getChecklistPoints = function(){
        $.get("../index.php",{"from": "back", "op": "getChecklistPoints", "values": {"checklistId": checklistId}},function(checklistPoints){
            $('#checklistPointEditContainer').html(checklistPoints);
                //checklistPoint clickevents 
            $('.checklistPointEdit').unbind("click").click(function(){
                
                    //opening dialog
                $('#editChecklistPointDialog').dialog({
                    width: 900, 
                    height: 620, 
                    position: "center",
                    close: function(){                       
                       screenshots = []; //clear screenshotlist for next use
                       $('#addChecklistPointAvalibleScreenshots').html("");
                       $('.addChecklistPointScreenshotsDropAfter').html("").removeClass("addChecklistPointScreenshotsDropAfter").addClass("addChecklistPointScreenshotsDropBevore");
                    }
                });
                    //getting and filling in text and heading and preparing the screenshots
                    $.getJSON("../index.php",{"from": "back", "op": "getChecklistPointEdit","values": {"checklistPointId": ($(this).attr("id").replace("checklistPoint",""))}},function(checklistPoint){
                        $.get("../index.php",{"from": "back", "op": "getCountChecklistPoints", "values": {"checklistId": checklistId}},function(count){
                            $('#editChecklistPointMaxCount').text(" (in Liste: "+(parseInt(count.replace(/[^0-9.]/g, ""))+1)+")");
                        });
                        $('#editChecklistPointInputHeading').val(checklistPoint.heading);
                        initEditor("#editChecklistPointInputText");
                        $('#editChecklistPointInputText').setCode(checklistPoint.text);
                        $('#editChecklistPointDialogIdHolder').html(checklistPoint.checklistPointId);
                                
                                //getting screenshots
                        getScreenshotsCurrent(checklistPoint.checklistPointId);
                });              
            });
            
            $('.checklistPointOrderButtonUp').click(function(){
                if($(this).parent().parent().index() > 0){
                    var checklistPointId = $(this).parent().parent().find('.checklistPointEdit').attr("id").replace("checklistPoint","");
                    $.get("../index.php",{"from":"back","op":"changeChecklistPointOrder","mode":"up","checklistPointId":checklistPointId, "checklistId": checklistId},function(){getChecklistPoints();});
                }                
            });
            
            $('.checklistPointOrderButtonDown').click(function(){
                if($(this).parent().parent().index() <= $(this).parent().parent().parent().children("div").length){
                    var checklistPointId = $(this).parent().parent().find('.checklistPointEdit').attr("id").replace("checklistPoint","");
                    $.get("../index.php",{"from":"back","op":"changeChecklistPointOrder","mode":"down","checklistPointId":checklistPointId, "checklistId": checklistId},function(){getChecklistPoints();});
                }               
            });
        });        
    }
    
    this.deleteChecklistPoint = function(){
        var checkpointId = $('#editChecklistPointDialogIdHolder').html();
        var confirm = '<div class="deleteChecklistpointConfirmDialog" style="display:none;font-size:15px;" title="Checkpunkt löschen?">Checkpunkt wirklich aus dieser Checkliste löschen?</div>';
        $('#editChecklistPointDialog').append(confirm);
        $('.deleteChecklistpointConfirmDialog').dialog({
			resizable: false,
			height:200,
			modal: true,
			buttons: {
				"Löschen": function() {
                                        $.get("../index.php",{"from":"back","op":"deleteChecklistPointFromList","checklistId":checklistId,"checkpointId":checkpointId},function(){getChecklistPoints();});
					$( this ).dialog( "close" );
                                        $('#editChecklistPointDialog').dialog("close");
                                        $('.deleteChecklistpointConfirmDialog').remove();                                 
				},
				Cancel: function() {
					$( this ).dialog( "close" );
                                        $('.deleteChecklistpointConfirmDialog').remove();   
				}
			}
		});
    }
    
    var getChecklistPoints = this.getChecklistPoints;
    var deleteChecklistPoint = this.deleteChecklistPoint;
    
    function initEditor(targetId){
        if($(targetId).parent().hasClass('redactor_box')){
            $(targetId).destroyEditor();
        }
        $(targetId).val("");
        $(targetId).redactor({
            buttons: ['html', '|', 'formatting', '|', 
                        'bold', 'italic', '|',
                        'alignleft', 'aligncenter', 'alignright', 'justify', '|',
                        'image','video', 'table', 'link', '|',
                        'fontcolor', 'backcolor', '|', 
                        'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
                        'fullscreen'],
            lang: 'de',
            focus: true,
            autoresize: false
        });
    }
    
    function getScreenshots(){
        //screenshots
        $.getJSON("../index.php",{"from": "back", "op": "getScreenshotsChecklistPointAll", "values": {"all": true}},function(response){
            $('#addChecklistPointScreenshotLoding').hide();
            if(response != ""){
                $('#addChecklistPointAvalibleScreenshots').html(response.all);
            }else{

            }                   
            $.map($('#addChecklistPointAvalibleScreenshots img'),function(value,key){                       
                $(value).unbind("click").click(function(){
                    var checklistPointId = "";
                        if($('#editChecklistPointDialog').dialog('isOpen') === true){
                            checklistPointId = $('#editChecklistPointDialogIdHolder').html();
                        }else if($('#addChecklistPointDialog').dialog('isOpen') === true){
                            checklistPointId = $('#addChecklistPointNewIdHolder').html();
                        }
                    var targetId = $(value).attr("id").replace(/screenshot/,"");
                    var doubleFlag = true;  //true -> no doubles || false -> double
                    $.map(screenshots,function(v,k){if(v == targetId){doubleFlag = false;}});   //check if target is already chosen
                    if(doubleFlag == true && screenshots.length <= 3){
                        $.get("../index.php",{"from": "back", "op": "screenshotToChecklist", "screenshotId": targetId, "checklistPointId": checklistPointId, "position": (screenshots.length+1)});
                        screenshots.push($(value).attr("id").replace(/screenshot/,"")); 
                        $(value).clone().removeAttr("id").appendTo($('#screenshotDummy'+screenshots.length)); 
                        $('#screenshotDummy'+screenshots.length).removeClass("addChecklistPointScreenshotsDropBevore").addClass("addChecklistPointScreenshotsDropAfter");                        
                    }                            
                });                        
            });
        });
    }
    
    function getScreenshotsCurrent(checklistPointId){
        $.getJSON("../index.php",{"from": "back", "op": "getScreenshotsChecklistPointAll", "values": {"all": true,"current": true, "checklistPointId": checklistPointId}},function(response){
            $('#addChecklistPointAvalibleScreenshots').html(response.all);           
        });
    }
}

function getView(){
    return ChecklistPointsEditView;
}

