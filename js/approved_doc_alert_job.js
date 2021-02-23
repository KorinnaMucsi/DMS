$(document).ready(function () 
{
	//Lefut a funkcio az oldal beolvasasakor
	//ApprDocAlertJob();
		
	
	$('#dialog').on('dialogclose', function(event) 
	{
		ApprDocAlertJob();
	});
	
	//Lefut 5 percenkent
	setInterval(ApprDocAlertJob, 60000); //percenkent ellenorzi a program az uj dokumentumot (1 perc=60000 milisecond)
});

function ApprDocAlertJob()
{
	$.ajax(
	{
    	type: "POST",
    	//A funkcio lefuttatja a PHP fajlt, ami a bazis adatok alapjan osszeallitja annak az ablaknak a parametereit, ami ki kell, hogy nyiljon, ha a felhasznalo meg
    	//nem hagyta jova a neki szant uzenetet
        url: "approved_doc_alert_job.php",
        success : function(popup_msg) 
        {
        	//A popup_url valtozot rakta ossze es kuldte vissza a PHP fajl
        	if(popup_msg && popup_msg!='')
        	{
        		ShowCustomDialog(popup_msg);
			}
			else
			{
				$("#dialog").dialog('close');
			}
        }
	});
}


function ShowCustomDialog(msg)
{
                
	ShowDialogBox('New approved document',msg,'OK','', '',null);

}

function ShowDialogBox(title, content, btn1text, btn2text, functionText, parameterList) 
{
	var btn1css;
	var btn2css;

	if (btn1text == '') 
	{
		btn1css = "hidecss";
	} 
	else 
	{
		btn1css = "showcss";
	}

	if (btn2text == '') 
	{
		btn2css = "hidecss";
	} 
	else 
	{
		btn2css = "showcss";
	}
	
	$("#lblMessage").html(content);
	
	$("#dialog").dialog
	({
		resizable: false,
		title: title,
		modal: true,
		width: 'auto',
		height: 'auto',
		bgiframe: false,
		hide: { effect: 'scale', duration: 400 },
		buttons: 
		[
			{
				text: btn1text,
				"class": btn1css,
				click: function () 
				{
					AcknowledgeDoc();
				}
			},
			{
				text: btn2text,
				"class": btn2css,
				click: function () 
				{
					$("#dialog").dialog('close');
				}
			}
		]
	});
}

function AcknowledgeDoc()
{
	//Az a funkcio, ami lefut, amikor az OK-ra kattint a felhasznalo
	$.ajax(
	{
    	type: "POST",
    	//A funkcio lefuttatja a PHP fajlt, ami a bazis adatok alapjan osszeallitja annak az ablaknak a parametereit, ami ki kell, hogy nyiljon, ha a felhasznalo meg
    	//nem hagyta jova a neki szant uzenetet
        url: "approved_doc_alert.php",
        success : function() 
        {
        	ApprDocAlertJob();
        }
	});

	
}