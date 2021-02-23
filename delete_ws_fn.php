<?php

	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
	if(isset($_POST['WSFN_ID']))
	{
		
		$query_wsfn_del="DELETE FROM tWrkStationsFunctions WHERE ID=" . $_POST['WSFN_ID'];
		$cnt=mysqli_query($conn, $query_wsfn_del);
		
		if($cnt==1)
		{
			$_SESSION['wsfn_success']=SUCCESS_UPL_MSG_BEF . 'Job title - workstation pair successfully deleted!' . SUCCESS_UPL_MSG_AFT;
		}
		else
		{
			$_SESSION['wsfn_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
		}
	}
	else
	{
		$_SESSION['wsfn_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
	}
	
	echo 'OK';

?>