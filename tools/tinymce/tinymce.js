tinymceLoad = function() {
    $('textarea.tinymce').tinymce({
            // Location of TinyMCE script
            script_url : 'tools/tinymce/jscripts/tiny_mce/tiny_mce.js',

            // General options
            theme : "advanced",
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

            // Theme options
            theme_advanced_buttons1 : "save,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
            theme_advanced_buttons2 : "cut,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,|,insertdate,inserttime,preview",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,|,print,|,ltr,rtl",
            theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,

            // Example content CSS (should be your site CSS)
            content_css : "css/content.css",

            // Drop lists for link/image/media/template dialogs
            template_external_list_url : "lists/template_list.js",
            external_link_list_url : "lists/link_list.js",
            external_image_list_url : "lists/image_list.js",
            media_external_list_url : "lists/media_list.js",

            // Replace values for the template plugin
            template_replace_values : {
                    username : "Some User",
                    staffid : "991234"
            }
    });
}
