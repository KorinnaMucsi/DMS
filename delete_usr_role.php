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

	
	if(isset($_POST['ROLE_ID']))
	{
		
		//Mielott kitoroljuk a temporary munkahelyet, ki kell vennunk az azonositojat, mert utana mar nem lehet, viszont szukseg van ra a history toltesehez
		$query_select="SELECT USR_ID, HR_FUNC_ID FROM tUsrRoles WHERE ID=" . $_POST['ROLE_ID'] . " AND FROM_HR=0";
		$result=mysqli_query($conn, $query_select);
				
		//Ha nem volt elozo munkahely, amit ki kellett volna torolni (uj temporary munkahely hozzaadasanal), akkor 0 lesz a szerep azonositoja, ami bekerul a 
		//history tablaba
		
		$usr_id='';		
		$hr_func_id=0;
				
		while($row=mysqli_fetch_array($result))
		{
			$usr_id=$row['USR_ID'];		
			$hr_func_id=$row['HR_FUNC_ID'];
		}
		
				
//HISTORY - DELETE
		History('tUsrRoles', '', 'USR_ID: ' . $usr_id . ' , HR_FUNC_ID: ' . $hr_func_id, '', 'D', 'Temporary job title deleted from the user', 
				'DMS', $_SESSION['dms_username']);
								
								
		$query_role_del="DELETE FROM tUsrRoles WHERE ID=" . $_POST['ROLE_ID'];
		$result_role_del=mysqli_query($conn, $query_role_del);
		$cnt_role_del=mysqli_affected_rows($conn);		
		
		if($cnt_role_del==1)
		{
			$_SESSION['wsu_success']=SUCCESS_UPL_MSG_BEF . 'Temporary job successfully deleted!' . SUCCESS_UPL_MSG_AFT;
		}
		else
		{
			$_SESSION['wsu_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
		}
	}
	else
	{
		$_SESSION['wsu_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
	}

?>