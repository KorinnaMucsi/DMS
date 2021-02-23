function UserLogout()
{
	if (confirm('Are you sure to sign out?')==true)
	{
		window.location = "logout.php";
	}
}
$(document).ready(function()
{	
	ContHeight();
	
	GetListofRoles();
	
	$("#username").change(function() 
	{
		GetListofRoles();
	});

});
$(window).resize(function()
{	
	ContHeight();
});

function ContHeight()
{
	//Beallitja a sotetebb hatteru div meretet, hogy a documentum vegeig tartson
	//Ha csak az ablakhoz igazitanank, akkor az moge nem jutna, ami kilog az ablakbol
	//Windows + Android teszt OK
	var wH = $(document).height();
	$('.login_containerDivBody').css({minHeight: wH});

}
$( window ).on( "orientationchange", function( event ) 
{
	ContHeight();
});

function GetListofRoles()
{
	var usr_id=$("#username").val();
		    
	$.ajax
	({
		type:'POST',
		url:'get_roles.php',
		data:
		{	
			USR_ID:usr_id
		},
		success:function(data)
		{
			$("#selRoleDiv").html(data);
/*			$("#selRoleDiv").addClass("login_input_div");
			$("#selRole").addClass("login_select");			
*/		}
	});
}
