<?php	
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
	if(isset($_POST['TYPE_ID']))
	{
		$type_id=$_POST['TYPE_ID'];
		$query_dl="SELECT IFNULL(COUNT(HR_FUNC_ID),0) AS FN_CNT FROM tUsrPermissionsFN WHERE TYPE_ID='" . $type_id . "' ";
		$result_dl=mysqli_query($conn, $query_dl);
		
		while($row_dl=mysqli_fetch_array($result_dl))
		{
			$dl=$row_dl['FN_CNT'];
		}
		
		echo $dl;
	}
?>