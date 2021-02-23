<?php

	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
	$query_vals=array();
	
	if(isset($_POST['ID']))
	{
		$_SESSION['f_id']=$_POST['ID'];
		
		$query_fn="SELECT HR_FUNCTION, HR_FUNC_SHORT, ACTIVE, DPT_ID, WS_ID FROM tHRFunctions WHERE ID=" . $_POST['ID'];
		$result_fn=mysqli_query($conn, $query_fn);
		
		while($row_fn=mysqli_fetch_array($result_fn))
		{
			$fn=$row_fn['HR_FUNCTION'];
			$fn_short=$row_fn['HR_FUNC_SHORT'];
			$active=$row_fn['ACTIVE'];
			$dpt_id=$row_fn['DPT_ID'];
			$ws_id=$row_fn['WS_ID'];
			
			$query_vals=array("fn" => $fn, "fn_short" => $fn_short, "active" => $active, "dpt_id" => $dpt_id, "ws_id" => $ws_id);
		}
	}
	echo json_encode($query_vals);

?>