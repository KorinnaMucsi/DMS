function NewFnVisible()
{
	document.getElementById('btnFns').style.display = 'none';
	document.getElementById('form_fns').style.display = 'inline-block';
}
function NewFnInVisible()
{
	document.getElementById('btnFns').style.display = 'inline-block';
	document.getElementById('btnFns').style.margin = '1% 0 1% 0';
	document.getElementById('form_fns').style.display = 'none';
}
function EditFn(ID)
{
	NewFnVisible();
	
	$.ajax(
	{
		type: "POST",
		//Ez a fajl kesziti a szesszios valtozot
		url: "edit_hr_function.php",
		data: 
		{
			ID:ID
		},
		dataType: 'json',
		async: false,
		success:function(response)
		{
			$("#txtFn").val(response["fn"]);
			$("#txtSFn").val(response["fn_short"]);
			$("#selDpt").val(response["dpt_id"]);
			$("#selWs").val(response["ws_id"]);
			
			if(response["active"]==0)
			{
				$("#chkActive").prop("checked", false);
			}
			else
			{
				$("#chkActive").prop("checked", true);
			}
		}
	});	

}