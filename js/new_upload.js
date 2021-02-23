function checkTypePerm() 
{
	var tp=document.getElementById("doc_types").value;
		
	$.ajax(
	{
		type: "POST",
		url: "upload_check_type_perm.php",
		data: 
		{
			tp:tp
		},
		async: false,
		success:setTimeout(reloadPage, 1000)
	});	
	
}

function DTypeUpdate() 
{
	var dtp=document.getElementById("types").value;
	
	$.ajax(
	{
		type: "POST",
		url: "upload_set_dtsession.php",
		data: 
		{
			dtp:dtp
		},
		async: false,
		success:setTimeout(reloadPage, 100)
	});	
	
}


function ResetErrorSession() 
{
	$("body").addClass("main_wait");
		
	$.ajax(
	{
		type: "POST",
		url: "reset_error_session.php",
		data: 
		{
			st:''
		},
		success:function()
		{
			location.reload();	
		}
	});	
	
}
function reloadPage()
{
	addr=document.location.href;
	document.location.href=addr;	
}
