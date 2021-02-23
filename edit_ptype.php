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
		$_SESSION['ptype_id']=$_POST['ID'];
		$query_tp="SELECT TYPE_ID, DESCR, PRINTABLE, ACTIVE FROM tPDocTypes WHERE ID=" . $_POST['ID'];
		$result_tp=mysqli_query($conn, $query_tp);
		
		while($row_tp=mysqli_fetch_array($result_tp))
		{
			$type_tp_pdt=$row_tp['TYPE_ID'];
			$descr_tp=$row_tp['DESCR'];
			$prnt_tp=$row_tp['PRINTABLE'];
			$active_pdt=$row_tp['ACTIVE'];
			
			$query_vals=array("type_tp_pdt" => $type_tp_pdt, "descr_tp" => $descr_tp, "prnt_tp" => $prnt_tp, "active_pdt" => $active_pdt);
		}
	}
	echo json_encode($query_vals);

?>