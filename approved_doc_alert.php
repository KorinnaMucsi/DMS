<?php
	
	session_start();
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


//SUBMIT -->

//UPDATE (HST) -->
	
	//UPDATE QUERY updateli a history tablat, hogy a felhasznalo jovahagyta arra az ID-re, amit az approved_doc_alert_job.php beallitott a $_SESSION['hst_id'] valtozoba
	$query_upd=	"UPDATE tDocDistribHst SET EVENT_ACKN=1, EVENT_APPEARED_DT='" . $_SESSION['appeared'] . "', EVENT_ACKN_DT='" . date("Y-m-d H:i:s") . "',  " . 
				"EVENT_DESCR='Notification acknowledged' WHERE ID='" . $_SESSION['hst_id'] . "' AND USR_ID='" . $_SESSION['dms_username'] . "'";
		

	mysqli_query($conn, $query_upd);
		
		
?>
