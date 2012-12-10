var mandatories=["optin_field_12"];
var emailfields=["optin_field_12"];
var subscriberlists=[];
var optinsetups=[];
function checkOptinForm()
{
    var msg = {
        "error_mandatory":"Füllen Sie alle mit einem * markierten Felder aus!",
        "error_email":"Bitte überprüfen Sie Ihre E-Mail-Adresse!",
        "error_list":"Bitte wählen Sie mindestens eine Liste!"
    };

    try
    {
            for (var i=0; i<mandatories.length; i++)
            {
        var cfObj=document.getElementById(mandatories[i]);
        if (cfObj!=null)
        {
            if (cfObj.type.toLowerCase()=="text")
            {
            if (cfObj.value.match(/^\s*$/)) throw msg.error_mandatory;
            }
            else if (cfObj.type.toLowerCase()=="radio" || cfObj.type.toLowerCase()=="checkbox")
            {
            var tmpObj=document.getElementsByName(cfObj.name);
            var tmpCheck=false;
            for (var j=0; j<tmpObj.length; j++)
            {
                if (tmpObj[j].checked==true)
                {
                tmpCheck=true;
                break;
                }
            }
            if(!tmpCheck) throw msg.error_mandatory;
            }
            else if (cfObj.type.toLowerCase().indexOf("select")>=0)
            {
            if (cfObj.selectedIndex<=0) throw msg.error_mandatory;
            }
        }
        }

        var regex = new RegExp(
        "^"+
        "[a-zA-Z0-9_\.\-]+"+
        "@"+
        "[a-zA-Z0-9_\.\-]+"+
        "\\."+
        "[a-zA-Z0-9]{2,4}"+
        "$"
        );
        for (var i=0; i<emailfields.length; i++){
        if(!regex.test(document.getElementById(emailfields[i]).value)) throw msg.error_email;
        }

        document.getElementById("subscribe").submit();
    }
    catch(e)
    {
        window.alert(e);
        return false;
    }
}
function ic()
{
    if (document.getElementById("ic"))
    {
        c = document.getElementById("ic");
        if (isNaN(c.value) == true)
        c.value = 0;
        else
        c.value = parseInt(c.value) + 17;
    }
    setTimeout("ic()", 1000);
}
ic();