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
	
	if(isset($_POST['TYPE_ID']))
	{
		$_SESSION['type_id']=$_POST['TYPE_ID'];
		
		$query_tp=	"SELECT TYPE_ID, DESCR, DD.DOC_DESCR, DESCR_ID, " .
					"CASE DT.ACTIVE WHEN 1 THEN 'Yes' ELSE 'No' END AS ACTIVE " .
					"FROM tDocTypes DT " .
					"LEFT JOIN tDocDescr DD ON DT.DOC_DESCR=DD.DESCR_ID " .
					"WHERE 1=1 " .
					"AND TYPE_ID='" . $_POST['TYPE_ID'] . "' ";
					
		$result_tp=mysqli_query($conn, $query_tp);
		
		while($row_tp=mysqli_fetch_array($result_tp))
		{
			$type_tp=$row_tp['TYPE_ID'];
			$descr_tp=$row_tp['DESCR'];
			$doc_descr_tp=$row_tp['DESCR_ID'];
			$active_tp=$row_tp['ACTIVE'];
			
		}
		
//UPLOADER	
		$usr_upload='';	
		$query_upload="SELECT USR_ID FROM tUsrPermissionsDO WHERE TYPE_ID='" . $_POST['TYPE_ID'] . "' AND UPLOAD_P=1 AND USR_ID NOT IN ('administrator','xy','xy') ";
		
		$result_upload=mysqli_query($conn, $query_upload);
		
		while($row_upload=mysqli_fetch_array($result_upload))
		{
			$usr_upload=$row_upload['USR_ID'];
		}
//DOCUMENT APPROVER	
		$usr_da='';	
		$query_da="SELECT USR_ID FROM tUsrPermissionsDO WHERE TYPE_ID='" . $_POST['TYPE_ID'] . "' AND APPROVE_P=1 AND USR_ID NOT IN ('administrator','xy','xy') ";
		
		$result_da=mysqli_query($conn, $query_da);
		
		while($row_da=mysqli_fetch_array($result_da))
		{
			$usr_da=$row_da['USR_ID'];
		}
//GENERAL APPROVER	
		$usr_ga='';
		$query_ga="SELECT USR_ID FROM tUsrPermissionsDO WHERE TYPE_ID='" . $_POST['TYPE_ID'] . "' AND   APPROVE_GA_P=1 AND USR_ID NOT IN ('administrator','xy','xy') ";
		
		$result_ga=mysqli_query($conn, $query_ga);
		
		while($row_ga=mysqli_fetch_array($result_ga))
		{
			$usr_ga=$row_ga['USR_ID'];
		}
		
		$query_vals=array("type_tp" => $type_tp, "descr_tp" => $descr_tp, "doc_descr_tp" => $doc_descr_tp, "active_tp" => $active_tp, "doc_uploader" => $usr_upload, 
					"doc_da" => $usr_da, "doc_ga" => $usr_ga);

	}
	echo json_encode($query_vals);

?>