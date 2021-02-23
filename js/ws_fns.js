function NewWSFnsVisible()
{
	document.getElementById('btnWsFns').style.display = 'none';
	document.getElementById('form_wsfns').style.display = 'inline-block';
}
function NewWSFnsInVisible()
{
	document.getElementById('btnWsFns').style.display = 'inline-block';
	document.getElementById('btnWsFns').style.margin = '1% 0 1% 0';
	document.getElementById('form_wsfns').style.display = 'none';
}
function DelWSFns(wsfn_id)
{
	if (confirm('Are you sure you want to delete the selected job title - workstation pair?')) 
	{
		$.ajax(
		{
			type: "POST",
			url: "delete_ws_fn.php",
			data: 
			{
				WSFN_ID:wsfn_id
			},
			async: false,
			success:function(response)
			{
				location.reload();
			}
		});	
	}
}