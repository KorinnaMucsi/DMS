function EditUsr(usr_id, usr_name, curr_fn)
{
	$.ajax(
	{
		type: "POST",
		url: "hr_functions_to_cmb.php",
		data:
		{
			CURR_FN:curr_fn
		},
		success:function(response)
		{
			content=	'<div style="width:100%;text-align:left;margin-bottom:15%;">' +
						'<b>' + usr_name + '</b><br><br>' +
						'<select name="sel_hr_fn" id="sel_hr_fn">' + 
						'<option value="" selected="selected" disabled="disabled">----- Please, select a function -----</option>' +
						response +
						'</select><br><br>' +
						'</div>';
			ShowDialogBoxUSR('Function', content, 'Save', 'Preview', 'Cancel','',null, usr_id, usr_name);
		}
	});		
}

function ShowDialogBoxUSR(title, content, btn1text, btn2text, btn3text, functionText, parameterList, usr_id, usr_name) 
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
	if (btn3text == '') 
	{
		btn3css = "hidecss";
	} 
	else 
	{
		btn3css = "showcss";
	}
	
	$("#lblMessageUSRS").html(content);
	
	$("#dialogUSRS").dialog
	({
		resizable: false,
		title: title,
		modal: true,
		width: 'auto',
		hide: { effect: 'scale', duration: 400 },
		buttons: 
		[
			{
				text: btn1text,
				"class": btn1css,
				click: function() 
				{	
					var hr_fn = document.getElementById("sel_hr_fn");
					var fn = hr_fn.options[hr_fn.selectedIndex].value;
					SaveUsrFn(usr_id,fn);
				}
			},
			{
				text: btn2text,
				"class": btn2css,
				click: function () 
				{
					var hr_fn = document.getElementById("sel_hr_fn");
					var fn = hr_fn.options[hr_fn.selectedIndex].value;
					PreviewPDF('edit',usr_id,usr_name, fn);
				}
			},
			{
				text: btn3text,
				"class": btn3css,
				click: function() 
				{					
					$("#dialogUSRS").dialog('close');
				}
			}
		]
	});
}
function PreviewPDF(lnk,usr_id,usr_name,usr_fn)
{
	if(lnk=='edit' && usr_fn=='')
	{
		alert('Please, enter a function for the user!');
	}
	else
	{
		$.ajax(
		{
			type: "POST",
			url: "get_hr_functions.php",
			data: 
			{
				USR_FN_ID:usr_fn,
			},
			async: false,
			success:function(response)
			{
				signature='signatures/' + usr_id + '.png';
		
				url="pdf_preview.php?unm=" + usr_name + "&ufn=" + response + "&sign=" + signature;
				window.location = url;
			}
		});	

	}
		

}
function SaveUsrFn(usr_id,usr_fn)
{
	if(usr_fn=='')
	{
		alert('Please, enter a function for the user!');
	}
	else
	{
		$.ajax(
		{
			type: "POST",
			url: "users_fn_upd.php",
			data: 
			{
				USR_ID:usr_id,
				USR_FN:usr_fn
			},
			async: false,
			success:function(response)
			{
				if(response==0)
				{
					alert('This job title is already set as a temporary job title to the user. Please, contact the user\'s manager!');
				}
				else
				{
					$("#dialogUSRS").dialog('close');
					location.reload();	
				}
			}
		});	
	}
}
function ViewUsr(usr_fn)
{
	PreviewPDF('view','','', usr_fn);
}

function UnblockWSUsr(usr_id)
{
	if (confirm('Are you sure you want to unblock the selected user?')) 
	{
		$.ajax(
		{
			type: "POST",
			url: "ws_users_bs_upd.php",
			data: 
			{
				USR_ID:usr_id
			},
			async: false,
			success:function()
			{
				location.reload();	
			}
		});	
	} 
}
function ViewUsrSignature(usr_id, usr_name, usr_fn)
{
	PreviewPDF('edit',usr_id,usr_name, usr_fn);
}
