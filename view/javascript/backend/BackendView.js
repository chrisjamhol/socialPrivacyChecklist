BackendView = function(){
      
    this.init = function(){
        
            //--------------- main control -------------
        $('#OpFbType').unbind('click').click(function(){
            loadNewView("fbTypeEdit.html","cssPc/backend/fbTypeEdit.css","FbTypeEditView.js",function(){
                
            });
        });
        
        $('#OpChecklist').unbind('click').click(function(){
             loadNewView("checklistEdit.html","cssPc/backend/checklistEdit.css","ChecklistEditView.js",function(){
                
            });
        });
        
         $('#OpArticle').unbind('click').click(function(){
            loadNewView("articleEdit.html","cssPc/backend/articleEdit.css","ArticleEditView.js",function(){
                getCats();      //get categorys and fill in select tab #acticleCatDropdown
               // showArticles();
            });           
        });
        
         $('#OpStats').unbind('click').click(function(){
            loadNewView("stats.html","cssPc/backend/stats.css","StatsView.js");           
        });

        $('#OpFixer').unbind('click').click(function(){
            loadNewView("fixer.html","cssPc/backend/fixer.css","FixerView.js");
        }); 
                //-------------- articles -----------------
                
                
        $('#addCatButton').unbind('click').click(function(){
            $('#newCatDialog').dialog({height: 115});
            $('#newCatSubmitButoon').unbind('click').click(function(){
               if($('#newCatInput').val() != "" && $('#newCatInput').val() != " "){                  
                 $.get("../index.php",{"from": "back","op": "newCat", "cat": $('#newCatInput').val()},function(){$('#newCatDialog').dialog('close');}); 
               } 
            });
        });      
    }

    var getCats = function(){
        $.get("../index.php",{"from": "back","op": "getCats"},function(cats){
            $('#articleCatDropdown').html(cats); 
            $('#articleCatDropdown').change(function(){
                $.get("../index.php",{"from":"back","op":"getArticles","cat":$(this).val()},function(articles){
                    $('#articles').html(articles);
                }); 
            });
        });       
     }
}