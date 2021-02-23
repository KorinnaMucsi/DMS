<?php
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	require_once('get_profile_permission.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
	$permission=GetProfilePermission('AS0008',$_SESSION['dms_username']);
	
//NO PERMISSION - MSG -->

	//Ha nincs semelyik tiupshoz approve jogosultsaga, akkor lefuttat egy Javascriptet, ami hibauzenetet ad a felhasznalonak es frissiti az oldalt
	if($permission==0)
	{
		$ws_fns_form='<script>alert("You don\'t have permission to this operation!");document.location.href="main.php?showMainMenu=True";</script>';
	}
	else
	{
		$msg='';

//******************************************************************************************************
//										SUBMIT THE FORM
//******************************************************************************************************
	
		//Ha az elejere tesszuk a $msg beallitasat, akkor megmarad az uzenet a form submit-ja utan, 
		//viszont az elso frissites utan eltunik, ahogy kell is, hogy mukodjon a program
		
		if(isset($_SESSION['wsfn_error']) && $_SESSION['wsfn_error']!='')
		{
			$msg=$_SESSION['wsfn_error'];
			unset($_SESSION['wsfn_error']);
		}		
		if(isset($_SESSION['wsfn_success']) && $_SESSION['wsfn_success']!='')
		{
			$msg=$_SESSION['wsfn_success'];
			unset($_SESSION['wsfn_success']);
		}
		
		if(isset($_POST['submit']))
		{
			if(isset($_POST['selWs']) && isset($_POST['selHrFn']))
			{
				$ws_id=$_POST['selWs'];
				$fn_id=$_POST['selHrFn'];
				
				$query_ctrl="SELECT WSFN.ID AS ID, FN.HR_FUNCTION, WS.DESCR AS WS_DESCR " .
							"FROM tWrkStationsFunctions WSFN " .
							"JOIN tWrkStations WS ON WSFN.WS_ID=WS.WS_ID " .
							"JOIN tHRFunctions FN ON WSFN.HR_FUNC_ID=FN.ID " .
							"WHERE 1=1 " .
							"AND WSFN.WS_ID='" . $ws_id . "' " .
							"AND WSFN.HR_FUNC_ID=" . $fn_id;
	
				$result_ctrl=mysqli_query($conn,$query_ctrl);	
				$cnt_ctrl=mysqli_num_rows($result_ctrl);
				
				//Ha az ellenorzo query talal ajka
				if($cnt_ctrl!=0)
				{
					while($row_ctrl=mysqli_fetch_array($result_ctrl))
					{
						$_SESSION['wsfn_error']=ERROR_UPL_MSG_BEF . 'This job title (' .  $row_ctrl['HR_FUNCTION'] . ') is already assigned to the selected workstation (' . "\n" .
						$row_ctrl['WS_DESCR'] . ')!' . ERROR_UPL_MSG_AFT;
					}
				}
				else
				{
					$query_insert="INSERT INTO tWrkStationsFunctions (WS_ID, HR_FUNC_ID) SELECT '" . $ws_id . "', '" . $fn_id . "'";
					$cnt=mysqli_query($conn,$query_insert);
					
					if($cnt==1)
					{
						$_SESSION['wsfn_success']=SUCCESS_UPL_MSG_BEF . 'Job title successfully assigned to the workstation!' . SUCCESS_UPL_MSG_AFT;
					}
					else
					{
						$_SESSION['wsfn_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
					}
				}//Sikeres query futtatas ellenorzesenek vege
			}//A posztolt komboknak van-e tartalma ellenorzes vege		
			
			die(header("Location:main.php?showWSFns=True"));
		
		}//Submit ellenorzes vege
		
			
		
	//******************************************************************************************************
	//										SUBMIT THE FORM END
	//******************************************************************************************************
	
		//WORKSTATION COMBO A KEZDETI FORM KIRAJZOLASASHOZ
		
		$sel_ws=	'<select name="selWs" id="selWs" required="required">' . "\n" .
					'<option value="">----- Please, select -----</option>' . "\n";
	
		$query_ws="SELECT WS_ID, DESCR FROM tWrkStations WHERE ACTIVE=1 ORDER BY DESCR ASC ";
		$result_ws=mysqli_query($conn,$query_ws);
		
		while($row_ws=mysqli_fetch_array($result_ws))
		{
			$sel_ws.='<option value="' . $row_ws['WS_ID'] . '">' . $row_ws['DESCR'] . '</option>';
		}
		
		$sel_ws.='</select>';
		
		//JOB TITLE COMBO A KEZDETI FROM KIRAJZOLASAHOZ
		$sel_hrfn=	'<select name="selHrFn" id="selHrFn" required="required">' . "\n" .
					'<option value="">----- Please, select -----</option>' . "\n";
	
		$query_hrfn="SELECT ID, HR_FUNCTION FROM tHRFunctions WHERE ACTIVE=1 ORDER BY HR_FUNCTION ASC ";
		$result_hrfn=mysqli_query($conn,$query_hrfn);
		
		while($row_hrfn=mysqli_fetch_array($result_hrfn))
		{
			$sel_hrfn.='<option value="' . $row_hrfn['ID'] . '">' . $row_hrfn['HR_FUNCTION'] . '</option>';
		}
		
		$sel_hrfn.='</select>';
		
		//Osszerakja a jovahagyo form kinezetet
		$ws_fns_form='<div id="mainContainerDiv" class="main_mainContainerDiv">' . "\n" .
						'<div class="main_containerDiv" id="main_containerDiv">' . "\n" .
						'<div class="main_titleDiv">' . "\n" .
						'<span class="main_title">Workstations and job titles<br>Maintenance Menu</span>' . "\n" .
						'<span class="main_titleDiv_sp"><img src="img/users.png" alt="WSFunctions"/></span>' . "\n" .
						'<span class="main_subtitle">You can find the list of the Workstations with the job titles. ' . "\n" .
						'Only users with job titles listed here beside a workstation can access the particular workstation<br>' ."\n" .
						'Use the \'Add\' button to add a new job title to a workstation or use the \'Delete\' button to remove a job title and workstation combination!</span>' . "\n" .
						'<hr>' . "\n" .
						'</div>' . "\n" . 
						'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
						$msg . "\n" .	
						'<button type="button" id="btnWsFns" name="btnWsFns" onclick="NewWSFnsVisible();">Add new job title to workstation</button>' . "\n" .	
						'<form action="main.php?showWSFns=True" method="POST" id="form_wsfns">' . "\n" .
								'<div class="main_dataDiv" id="main_dataDiv_a">' . "\n" .
									'<div class="upload_fline">' . "\n" .
										'<label for="selWS" class="upload_lbl">Workstation:</label>' . "\n" .
										$sel_ws . "\n" .
									'</div>' . "\n" .	
									'<div class="upload_nline">' . "\n" .
										'<label for="selHrFn" class="upload_lbl">Job title:</label>' . "\n" .
										$sel_hrfn . "\n" .
									'</div>' . "\n" .
									'<hr>' . "\n" .
									'<input type="submit" id="wsfns_submit" name="submit" value="Save">' . "\n" .	
									'<button type="button" id="btnWSFnsCancel" name="btnWSFnsCancel" onclick="NewWSFnsInVisible()";>Cancel</button>' . "\n" .
								'</div>' . "\n" .
						'</form>' . "\n" .
						'<span class="main_subtitle"><i><b>Workstations with belonging job titles:</b></i></span>' . "\n" .
						'<div id="jqxgridWSFns">' . "\n" .
						'</div>' . "\n" .
						'</div>' . "\n" .
						'</div>' . "\n" .
						'</div>';
	}
?>
