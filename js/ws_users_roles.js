/*function UsrRoleVisible()
{
	document.getElementById('btnUsrRoles').style.display = 'none';
	document.getElementById('form_wsu').style.display = 'inline-block';
	$("#selUsr").val("");
	$("#selHrFn").val("");
}
function UsrRoleInVisible()
{
	document.getElementById('btnUsrRoles').style.display = 'inline-block';
	document.getElementById('btnUsrRoles').style.margin = '1% 0 1% 0';
	document.getElementById('form_wsu').style.display = 'none';
}
function DelTempJobTitle(role_id)
{
	if (confirm('Are you sure you want to delete the selected temporary job from the user?')) 
	{
		$.ajax(
		{
			type: "POST",
			url: "delete_usr_role.php",
			data: 
			{
				ROLE_ID:role_id
			},
			async: false,
			success:function()
			{
				location.reload();
			}
		});	
	}
}
function EditTempJobTitle(role_id, usr_id)
{
	UsrRoleVisible();
	$("#selUsr").val(usr_id);
	$("#selHrFn").val(role_id);
}*/
function NewUsrRoleVisible()
{
	document.getElementById('btnUsrRoles').style.display = 'none';
	document.getElementById('form_wsu').style.display = 'inline-block';
}
function NewUsrRoleInVisible()
{
	document.getElementById('btnUsrRoles').style.display = 'inline-block';
	document.getElementById('btnUsrRoles').style.margin = '1% 0 1% 0';
	document.getElementById('form_wsu').style.display = 'none';
}
function DelTempJobTitle(role_id)
{
	if (confirm('Are you sure you want to delete the selected temporary job from the user?')) 
	{
		$.ajax(
		{
			type: "POST",
			url: "delete_usr_role.php",
			data: 
			{
				ROLE_ID:role_id
			},
			async: false,
			success:function()
			{
				location.reload();
				//alert(response);
			}
		});	
	}
}