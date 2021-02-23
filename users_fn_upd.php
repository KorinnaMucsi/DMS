<?php
	
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
	if(isset($_POST['USR_ID']) && isset($_POST['USR_FN']))
	{
		$hr_fn=$_POST['USR_FN'];
		$usr_id=$_POST['USR_ID'];
		
		
		$query_fn_sel="SELECT COUNT(ID) AS NUM_R FROM tUsrRoles WHERE HR_FUNC_ID=" . $hr_fn . " AND USR_ID='" . $usr_id . "' AND FROM_HR=0";
		$result_fn_sel=mysqli_query($conn, $query_fn_sel);
	
		while($row_fn_sel=mysqli_fetch_array($result_fn_sel))
		{
			$num_r=$row_fn_sel['NUM_R'];
		}
		
		if($num_r>0)
		{		
			echo '0';
		}
		
		if($num_r==0)
		{
			$query_fn_upd="UPDATE tUsrs SET HR_FUNC_ID='" . $hr_fn . "' WHERE USR_ID='" . $usr_id . "'";
			$cnt=mysqli_query($conn,$query_fn_upd);
			
			if($cnt==1)
			{
				$_SESSION['fn_success']=SUCCESS_UPL_MSG_BEF . 'Successfully changed!' . SUCCESS_UPL_MSG_AFT;
			}		
			else
			{
				$_SESSION['fn_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again!' . ERROR_UPL_MSG_AFT;
			}
			echo '1';			
		}
	}
	else
	{
		$_SESSION['fn_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again!' . ERROR_UPL_MSG_AFT;
	}
?>
