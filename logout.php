<?php

/*Created by: Mucsi Korinna
Date of creation: 07.05.2015.
Description: The following file is used to manage the user logons and logoffs
*/
	
	session_start();
	
	require_once('connectvars/connectvars.php');

//UPDATE THE tUsrLogs TABLE WITH TEH LOGOUT INFORMATION -->

	if (isset($_SESSION['dms_login_id']))
	{
		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
		$selected = mysqli_select_db($conn, DB_NAME) or die("Couldn't open database"); 
		mysqli_set_charset($conn,"utf8");

		
		$query_logout="UPDATE tUsrLogs SET LOGOFF_DT=now() WHERE ID=" . $_SESSION['dms_login_id'];
		mysqli_query($conn, $query_logout);

	}
	
//DESTROY ALL THE SESSIONS AND DELETE THE COOKIES AND GO TO THE LOGIN SCREEN -->
	
	$_SESSION=array();
	session_destroy();
	setcookie('dms_username','',time()-3600);
	setcookie('dms_login_id','',time()-3600);
	setcookie('dms_max_logon','',time()-3600);
	setcookie('job_title','',time()-3600);
	header('Location:login.php');

?>
