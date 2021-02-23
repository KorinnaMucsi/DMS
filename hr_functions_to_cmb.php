<?php

	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	$query=	"SELECT * " .
			"FROM " .
			"tHRFunctions " .
			"WHERE ACTIVE=1 " .
			"ORDER BY HR_FUNCTION";
			
	$result=mysqli_query($conn, $query);
	
	$sel_hr_fn='';

	$curr_fn=$_POST['CURR_FN'];
	
	while($row=mysqli_fetch_array($result))
	{
		if($row['ID']==$curr_fn)
		{
			$selected=' selected="selected" ';
		}
		else
		{
			$selected='';
		}
		
		$sel_hr_fn.='<option value="' . $row['ID'] . '"' . $selected . '>' . $row['HR_FUNCTION'] . '</option>' . "\n";
	}
	
	echo $sel_hr_fn;	

?>
