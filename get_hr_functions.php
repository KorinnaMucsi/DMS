<?php
	//Visszaadja a HR Preview-hoz a roviditett nevet a HR funkcionak a felhasznalo melle rendelt ID alapjan a tHRFunctions tablabol (usrs_maint.js szkript PreviewPDF funkcio)
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	$usr_fn_id=$_POST['USR_FN_ID'];
	
	$query=	"SELECT " .
			"CASE HR_FUNC_SHORT WHEN '' THEN HR_FUNCTION ELSE HR_FUNC_SHORT END AS HR_FUNCTION " .
			"FROM " .
			"tHRFunctions " .
			"WHERE 1=1 " .
			"AND ID= " . $usr_fn_id;
			
	$result=mysqli_query($conn, $query);
	

	while($row=mysqli_fetch_array($result))
	{
		$hr_fn=$row['HR_FUNCTION'];
	}
	
	echo $hr_fn;	

?>
