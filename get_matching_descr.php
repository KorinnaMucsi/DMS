<?php	
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
	if(isset($_POST['TYPE_ID']) && isset($_POST['DESCR']))
	{
		$descr=$_POST['DESCR'];
		$type_id=$_POST['TYPE_ID'];
		
		$existing_type_id='';
		$query_descr="SELECT IFNULL(TYPE_ID,'') AS TYPE_ID FROM tDocTypes WHERE DESCR='" . $descr . "' AND TYPE_ID<>'" . $type_id . "' ";
		$result_descr=mysqli_query($conn, $query_descr);
		
		while($row_descr=mysqli_fetch_array($result_descr))
		{
			$existing_type_id=$row_descr['TYPE_ID'];
		}
		
		$query_curr_descr="SELECT DESCR FROM tDocTypes WHERE TYPE_ID='" . $type_id . "' "; 
		$result_curr_descr=mysqli_query($conn, $query_curr_descr);
		
		while($row_curr_descr=mysqli_fetch_array($result_curr_descr))
		{
			$curr_descr=$row_curr_descr['DESCR'];
		}
		
		echo json_encode(array("existing_type_id" => $existing_type_id, "curr_descr" => $curr_descr));
	}
?>