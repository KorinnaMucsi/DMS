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

	
	if(isset($_POST['USR_ID']))
	{
		$usr_id=$_POST['USR_ID'];
		
		$query_bs_upd="UPDATE tUsrs SET BLOCK_ID='C' WHERE USR_ID='" . $usr_id . "'";
		$cnt=mysqli_query($conn,$query_bs_upd);
		
		if($cnt==1)
		{
			History('tUsrs',$usr_id,'B','C','M','UNBLOCK','DMS',$_SESSION['dms_username']);
			$_SESSION['fn_success']=SUCCESS_UPL_MSG_BEF . 'Successfully changed!' . SUCCESS_UPL_MSG_AFT;
		}		
		else
		{
			$_SESSION['fn_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again!' . ERROR_UPL_MSG_AFT;
		}
	}
	else
	{
		$_SESSION['fn_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again!' . ERROR_UPL_MSG_AFT;
	}
?>
