<?php	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	require_once('get_profile_permission.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
	$permission=GetProfilePermission('AS0005',$_SESSION['dms_username']);
	
	if(isset($_POST['submit']))
	{
		if(isset($_POST['chkActive']))
		{
			$active=1;
		}
		else
		{
			$active=0;
		}
		
		//Ha az edit menupontbol jovunk, akkor az edit_hr_function.php fajl, amit a hr_functions.js fajl hiv meg, beallitja a szesszios valtozot.
		if(isset($_SESSION['f_id']))
		{
			$query_update_fn=	"UPDATE tHRFunctions SET HR_FUNCTION='" . $_POST['txtFn'] . "', HR_FUNC_SHORT='" . $_POST['txtSFn'] . "', DPT_ID='" . $_POST['selDpt'] . "', " .
								" ACTIVE=" . $active . " WHERE ID=" . $_SESSION['f_id'];
			
			$cnt_u=mysqli_query($conn, $query_update_fn);
			
			if($cnt_u==1)
			{
				$_SESSION['fns_success']=SUCCESS_UPL_MSG_BEF . 'Function successfully updated!' . SUCCESS_UPL_MSG_AFT;
			}
			else
			{
				$_SESSION['fns_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
			}
		}
		else
		{
			$query_insert_fn=	"INSERT INTO tHRFunctions (HR_FUNCTION, HR_FUNC_SHORT, DPT_ID, WS_ID, ACTIVE) SELECT '" . $_POST['txtFn'] . "', '" . $_POST['txtSFn'] . "', '" . 
								$_POST['selDpt'] . "', 'ws_00', " . $active;
							
			$cnt=mysqli_query($conn, $query_insert_fn);		
			
			if($cnt==1)
			{
				$_SESSION['fns_success']=SUCCESS_UPL_MSG_BEF . 'New function successfully added!' . SUCCESS_UPL_MSG_AFT;
			}
			else
			{
				$_SESSION['fns_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
			
			}
		}
		
		unset($_SESSION['f_id']);

		die(header("Location:main.php?showFns=True"));
	}
	
//NO PERMISSION - MSG -->

	//Ha nincs semelyik tiupshoz approve jogosultsaga, akkor lefuttat egy Javascriptet, ami hibauzenetet ad a felhasznalonak es frissiti az oldalt
	if($permission==0)
	{
		$fns_form='<script>alert("You don\'t have permission to this operation!");document.location.href="main.php?showMainMenu=True";</script>';
	}
	//Ha van, akkor osszerakja az oldalt
	else
	{	
		$msg='';
		
		if(isset($_SESSION['fns_error']) && $_SESSION['fns_error']!='')
		{
			$msg=$_SESSION['fns_error'];
		}		
		if(isset($_SESSION['fns_success']) && $_SESSION['fns_success']!='')
		{
			$msg=$_SESSION['fns_success'];
		}
		//DEPARTMENT COMBO  A FORMON
		$sel_dpt=	'<select name="selDpt" id="selDpt">' . "\n";
		
		$query_dpt=	"SELECT ID, DESCR " .
					"FROM tDpts " .
					"WHERE 1=1 " .
					"AND ACTIVE=1 " .
					"ORDER BY ID ";
					
		$result_dpt=mysqli_query($conn, $query_dpt);
		
		while($row_dpt=mysqli_fetch_array($result_dpt))
		{
			$sel_dpt.='<option value="' . $row_dpt['ID'] . '">' . $row_dpt['DESCR'] . '</option>';
		}
		
		$sel_dpt.='</select>';
		
		//WORKSTATION COMBO A FORMON
		$sel_ws=	'<select name="selWs" id="selWs">' . "\n";
		
		$query_ws=	"SELECT WS_ID, DESCR " .
					"FROM tWrkStations " .
					"WHERE 1=1 " .
					"AND ACTIVE=1 " .
					"ORDER BY WS_ID ";
					
		$result_ws=mysqli_query($conn, $query_ws);
		
		while($row_ws=mysqli_fetch_array($result_ws))
		{
			$sel_ws.='<option value="' . $row_ws['WS_ID'] . '">' . $row_ws['DESCR'] . '</option>';
		}
		
		$sel_ws.='</select>';
		
		//Osszerakja a jovahagyo form kinezetet
		$fns_form=	'<div id="mainContainerDiv" class="main_mainContainerDiv">' . "\n" .
					'<div class="main_containerDiv" id="main_containerDiv">' . "\n" .
					'<div class="main_titleDiv">' . "\n" .
					'<span class="main_title">Job titles (HR)<br>Maintenance Menu</span>' . "\n" .
					'<span class="main_titleDiv_sp"><img src="img/users.png" alt="Job titles"/></span>' . "\n" .
					'<span class="main_subtitle">You can find the list of the job titles that can be assigned to users within the DMS application.<br>' ."\n" .
					'Please, click on the \'Edit\' link change the functions\' data!</span>' . "\n" .
					'<hr>' . "\n" .
					'</div>' . "\n" . 
					'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
					$msg . "\n" .		
					'<button type="button" id="btnFns" name="btnFns" onclick="NewFnVisible();">Add new job title</button>' . "\n" .
					'<form action="main.php?showFns=True" method="POST" id="form_fns">' . "\n" .
							'<div class="main_dataDiv" id="main_dataDiv_a">' . "\n" .
								'<div class="upload_fline">' . "\n" .
									'<label for="txtFn" id="lblFn" class="upload_lbl">Enter the new job title name:</label>' . "\n" .
									'<textarea id="txtFn" name="txtFn" required="required" style="width:50%;"></textarea>' . "\n" .
								'</div>' . "\n" .	
								'<div class="upload_nline">' . "\n" .
									'<label for="txtSFn" class="upload_lbl">Short name of the job title:</label>' . "\n" .
									'<textarea id="txtSFn" name="txtSFn" style="width:50%;"></textarea>' . "\n" .
								'</div>' . "\n" .
								'<div class="upload_nline">' . "\n" .
									'<label for="selDpt" class="upload_lbl">Please, select which department this job title belongs to:</label>' . "\n" .
									$sel_dpt . "\n" .
								'</div>' . "\n" .
/*								'<div class="upload_nline">' . "\n" .
									'<label for="selWs" class="upload_lbl">Please, select which workstation this job title belongs to:</label>' . "\n" .
									$sel_ws . "\n" .
								'</div>' . "\n" .
*/								'<div class="upload_nline">' . "\n" .
									'<label for="chkActive" class="upload_lbl">Active?</label>' . "\n" .
									'<input id="chkActive" name="chkActive" type="checkbox" />' . "\n" .
								'</div>' . "\n" .
								'<hr>' . "\n" .
								'<input type="submit" id="fns_submit" name="submit" value="Save">' . "\n" .	
								'<button type="button" id="btnFnsCancel" name="btnFnsCancel" onclick="NewFnInVisible()";>Cancel</button>' . "\n" .
							'</div>' . "\n" .
					'</form>' . "\n" .
					'<span class="main_subtitle"><i><b>Functions:</b></i></span>' . "\n" .
					'<div id="jqxgridFns">' . "\n" .
					'</div>' . "\n" .
					'</div>' . "\n" .
					'</div>' . "\n" .
					'</div>';
	}
	
?>
