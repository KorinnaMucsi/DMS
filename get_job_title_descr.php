<?php
	//Visszaadja a HR Preview-hoz a roviditett nevet a HR funkcionak a felhasznalo melle rendelt ID alapjan a tHRFunctions tablabol (usrs_maint.js szkript PreviewPDF funkcio)
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	if(isset($_POST['SEL_JOB_TITLE']))
	{
		$sel_job_title=$_POST['SEL_JOB_TITLE'];
		
		$query=	"SELECT HR_FUNCTION FROM tHRFunctions WHERE ID=" . $sel_job_title;
				
		$result=mysqli_query($conn, $query);		
	
		while($row=mysqli_fetch_array($result))
		{
			$sel_job_title_descr=$row['HR_FUNCTION'];
		}
		
		echo $sel_job_title_descr;	
	}
?>
