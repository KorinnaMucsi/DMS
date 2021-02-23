<?php
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	require_once('history.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
	$msg='';
	
	if(isset($_POST['submit']))
	{

		$query_sel_ctrl="SELECT COUNT(UR.USR_ID) AS CNT_USR, CONCAT(L_NAME,' ',F_NAME) AS FULL_NAME,HRF.HR_FUNCTION " .
						"FROM tUsrRoles UR " .
						"JOIN tUsrs U ON UR.USR_ID=U.USR_ID " .
						"JOIN tHRFunctions HRF ON UR.HR_FUNC_ID=HRF.ID " .
						"WHERE 1=1 " .
						"AND U.ACTIVE=1 " .
						"AND UR.USR_ID='" . $_POST['selUsr'] . "' " .
						"AND UR.HR_FUNC_ID='" . $_POST['selHrFn'] . "'";
						
		$result_sel_ctrl=mysqli_query($conn, $query_sel_ctrl);
		
		while($row_sel_ctrl=mysqli_fetch_array($result_sel_ctrl))
		{
			if($row_sel_ctrl['CNT_USR']!=0)
			{
				$_SESSION['wsu_error']=ERROR_UPL_MSG_BEF . 'Job (' . $row_sel_ctrl['HR_FUNCTION'] . ') is already assigned to the selected user (' . $row_sel_ctrl['FULL_NAME'] . ')!' .
				ERROR_UPL_MSG_AFT;
			}
			else
			{
				$query_insert="INSERT INTO tUsrRoles(USR_ID, HR_FUNC_ID) SELECT '" . $_POST['selUsr'] . "', '" . $_POST['selHrFn'] . "'";
				$cnt=mysqli_query($conn,$query_insert);
				
//HISTORY - INSERT
				//Kivesszuk az ujonnan bekerult temporary szerep ID-jat a tablabol, hogy a History tablaban tudjunk ra hivatkozni
				$query_select="SELECT ID FROM tUsrRoles WHERE USR_ID='" . $_POST['selUsr'] . "' AND HR_FUNC_ID='" . $_POST['selHrFn'] . "' AND FROM_HR=0";
				$result=mysqli_query($conn, $query_select);
				
				while($row=mysqli_fetch_array($result))
				{
					$new_id=$row['ID'];
				}
				
				History('tUsrRoles', $new_id, '', 'USR_ID: ' . $_POST['selUsr'] . ', HR_FUNC_ID: ' . $_POST['selHrFn'] , 'N', 'Temporary job title assigned to the user', 
						'DMS', $_SESSION['dms_username']);
				
//FINAL MESSAGE				
				if($cnt==1)
				{
					$_SESSION['wsu_success']=SUCCESS_UPL_MSG_BEF . 'Temporary job successfully assigned to the user !' . SUCCESS_UPL_MSG_AFT;
				}
				else
				{
					$_SESSION['wsu_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
				}
			}//if($row_sel_ctrl['CNT_USR']!=0) ellenorzes vege
		}//while vege				
		
		die(header("Location:main.php?showWSUsers=True"));

	}//submit ellenorzes vege
		
	if(isset($_SESSION['wsu_error']) && $_SESSION['wsu_error']!='')
	{
		$msg=$_SESSION['wsu_error'];
	}		
	if(isset($_SESSION['wsu_success']) && $_SESSION['wsu_success']!='')
	{
		$msg=$_SESSION['wsu_success'];
	}
	
	//USER COMBO A FORMON
	$sel_usr=	'<select name="selUsr" id="selUsr" required="required">' . "\n" .
				'<option value="">----- Please, select -----</option>' . "\n";
	
	$query_usr=	"SELECT USR_ID, CONCAT(L_NAME, ' ', F_NAME) AS FULL_NAME FROM tUsrs WHERE ACTIVE=1 ORDER BY CONCAT(L_NAME, ' ', F_NAME) ASC ";
	$result_usr=mysqli_query($conn, $query_usr);
	
	while($row_usr=mysqli_fetch_array($result_usr))
	{
		$sel_usr.='<option value="' . $row_usr['USR_ID'] . '">' . $row_usr['FULL_NAME'] . '</option>';
	}
	
	$sel_usr.=	'</select>';
	
	//HR_FUNCTION COMBO A FORMON
	$sel_hrfn=	'<select name="selHrFn" id="selHrFn" required="required">' . "\n" .
				'<option value="">----- Please, select -----</option>' . "\n";
				
	$query_hrfn="SELECT ID, HR_FUNCTION FROM tHRFunctions WHERE ACTIVE=1 ORDER BY HR_FUNCTION ASC ";
	$result_hrfn=mysqli_query($conn, $query_hrfn);
	
	while($row_hrfn=mysqli_fetch_array($result_hrfn))
	{
		$sel_hrfn.='<option value="' . $row_hrfn['ID'] . '">' . $row_hrfn['HR_FUNCTION'] . '</option>';
	}
	
	$sel_hrfn.=	'</select>';
	
	//Osszerakja a jovahagyo form kinezetet
	$ws_users_form='<div id="mainContainerDiv" class="main_mainContainerDiv">' . "\n" .
					'<div class="main_containerDiv" id="main_containerDiv">' . "\n" .
					'<div class="main_titleDiv">' . "\n" .
					'<span class="main_title">Users and temporary job titles<br>Maintenance Menu</span>' . "\n" .
					'<span class="main_titleDiv_sp"><img src="img/users.png" alt="UsersTitles"/></span>' . "\n" .
					'<span class="main_subtitle">You can find the list of the users with all their job titles below.<br>' ."\n" .
					'Use the \'Add\' button to add a new temporary job title to the user or use the \'Delete\' button to remove a user and its temporary job title!</span>' . "\n" .
					'<hr>' . "\n" .
					'</div>' . "\n" . 
					'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
					$msg . "\n" .	
					'<button type="button" id="btnUsrRoles" name="btnUsrRoles" onclick="NewUsrRoleVisible();">Add new temporary job title to user</button>' . "\n" .	
					'<form action="main.php?showWSUsers=True" method="POST" id="form_wsu">' . "\n" .
							'<div class="main_dataDiv" id="main_dataDiv_a">' . "\n" .
								'<div class="upload_fline">' . "\n" .
									'<label for="selUsr" class="upload_lbl">User:</label>' . "\n" .
									$sel_usr . "\n" .
								'</div>' . "\n" .	
								'<div class="upload_nline">' . "\n" .
									'<label for="selHrFn" class="upload_lbl">Temporary job title you want to assign this user to:</label>' . "\n" .
									$sel_hrfn . "\n" .
								'</div>' . "\n" .
								'<hr>' . "\n" .
								'<input type="submit" id="wsu_submit" name="submit" value="Save">' . "\n" .	
								'<button type="button" id="btnFnsCancel" name="btnFnsCancel" onclick="NewUsrRoleInVisible()";>Cancel</button>' . "\n" .
							'</div>' . "\n" .
					'</form>' . "\n" .
					'<span class="main_subtitle"><i><b>Users with job titles:</b></i></span>' . "\n" .
					'<div id="jqxgridWSUsrs">' . "\n" .
					'</div>' . "\n" .
					'</div>' . "\n" .
					'</div>' . "\n" .
					'</div>';
?>
