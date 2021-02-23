<?php
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	require_once('get_profile_permission.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
//QUERY TO CHECK THE USERS MAINTENANCE (MAIN MENU, MAINTENANCE, USERS BUTTON)-->	
	
	//Akkor fut, amikor a users gombra kattint a felhasznalo a maintenance formon (ugyanazok mehetnek bele a Users maintenanceba, akik az approve-ba)
	//A Query megnezi, hogy van-e barmelyik tipushoz approve jogosultsaga
	$query=	"SELECT SUM(UPLOAD_P) AS UPL, SUM(APPROVE_P) AS APPR, SUM(APPROVE_GA_P) AS APPR_GA " .
			"FROM " .
			"tUsrPermissionsDO " .
			"WHERE USR_ID='" . $_SESSION['dms_username'] . "'";
			
	$result=mysqli_query($conn, $query);
	
	while($row=mysqli_fetch_array($result))
	{
		$upload_p=$row['UPL'];
		$approve_p=$row['APPR'];
		$approve_ga_p=$row['APPR_GA'];
	}
	
	$permission=GetProfilePermission('AS0005',$_SESSION['dms_username']);
		
//NO PERMISSION - MSG -->

	//Ha nincs semelyik tiupshoz approve jogosultsaga, akkor lefuttat egy Javascriptet, ami hibauzenetet ad a felhasznalonak es frissiti az oldalt
	if($upload_p==0 && $approve_p==0 && $approve_ga_p==0 && $permission==0)
	{
		$users_form='<script>alert("You don\'t have permission to this operation!");document.location.href="main.php?showMainMenu=True";</script>';
	}
	//Ha van, akkor osszerakja az oldalt
	else
	{	
		$msg='';
		
		if(isset($_SESSION['fn_error']) && $_SESSION['fn_error']!='')
		{
			$msg=$_SESSION['fn_error'];
		}		
		if(isset($_SESSION['fn_success']) && $_SESSION['fn_success']!='')
		{
			$msg=$_SESSION['fn_success'];
		}
		
		//Osszerakja a jovahagyo form kinezetet
		$users_form='<div id="mainContainerDiv" class="main_mainContainerDiv">' . "\n" .
					'<div class="main_containerDiv" id="main_containerDiv">' . "\n" .
					'<div class="main_titleDiv">' . "\n" .
					'<span class="main_title">Users<br>Maintenance Menu</span>' . "\n" .
					'<span class="main_titleDiv_sp"><img src="img/users.png" alt="Users"/></span>' . "\n" .
					'<span class="main_subtitle">You can find the list of the active users within the DMS application.<br>' ."\n" .
					'Please, click on the \'Edit\' link change the users\' data!</span>' . "\n" .
					'<hr>' . "\n" .
					'</div>' . "\n" . 
					'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
					$msg . "\n" .		
/*					'<button type="button" id="btnPTypes" name="btnPTypes" onclick="NewPTypeVisible();">Add new type</button>' . "\n" .
					'<form action="main.php?showPTypes=True#" method="POST" id="form_ptypes">' . "\n" .
							'<div class="main_dataDiv" id="main_dataDiv_a">' . "\n" .
								'<div class="upload_fline">' . "\n" .
									'<label for="sel_doc_types" id="lbltp" class="upload_lbl">Select document type:</label>' . "\n" .
									$sel_doc_type . "\n" .
								'</div>' . "\n" .	
								'<div class="upload_nline">' . "\n" .
									'<label for="descr" class="upload_lbl">Description:</label>' . "\n" .
									'<input id="descr" name="descr" type="text" required="required" style="width:50%;"/>' . "\n" .
								'</div>' . "\n" .
								'<div class="upload_nline">' . "\n" .
									'<label for="print" class="upload_lbl">Printable?</label>' . "\n" .
									'<input id="print" name="print" type="checkbox" />' . "\n" .
								'</div>' . "\n" .
								'<hr>' . "\n" .
								'<input type="submit" id="ptypes_submit" name="submit" value="Save">' . "\n" .	
								'<button type="button" id="btnPTypesCancel" name="btnPTypesCancel" onclick="NewPTypeInVisible()";>Cancel</button>' . "\n" .
							'</div>' . "\n" .
					'</form>' . "\n" .
*/					'<span class="main_subtitle"><i><b>Users:</b></i></span>' . "\n" .
					'<div id="jqxgridUsrs">' . "\n" .
					'</div>' . "\n" .
					'</div>' . "\n" .
					'</div>' . "\n" .
					'</div>';
	}
?>
