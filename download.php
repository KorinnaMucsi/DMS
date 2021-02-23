<?php
	
	session_start();
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


//UPDATE HISTORY RECORD IN THE DATABASE -->	

	if(isset($_GET['ID']))
	{	
		
		if(isset($_GET['SRC']) && $_GET['SRC']=='log')
		{
			$view_dt=date("Y-m-d");
			
			$query_counter=	"SELECT DATEDIFF('" . $view_dt . "',DATE_FORMAT(VIEW_DUE_DT,'%Y-%m-%d')) AS VIEW_COUNTER FROM tDocDistribHst WHERE U_DOC_ID=" .  $_GET['ID'] . " AND USR_ID='" . $_SESSION['dms_username'] . "'";
			$result_counter=mysqli_query($conn,$query_counter);
			while($row_counter=mysqli_fetch_array($result_counter))
			{
				$view_counter=$row_counter['VIEW_COUNTER'];
			}
			
			$query_hst_upd=	"UPDATE tDocDistribHst SET VIEW_DT='" . date("Y-m-d H:i:s") . "', VIEW_COUNTER=" . $view_counter . " WHERE U_DOC_ID=" .  $_GET['ID'] . " AND USR_ID='" . $_SESSION['dms_username'] . "' " .
							"AND VIEW_COUNTER IS NULL";
			mysqli_query($conn,$query_hst_upd);
		}
		
	//FIND THE UPLODED DOC RECORD WITH THE SELECTED ID -->	
		
		//Megkeresi a program a tUploadedDocs tablaban a kivalasztott ID-ju dokumentumot, hogy megkapjon minden szukseges informaciot a letolteshez
		$query="SELECT *, CONCAT(SUBSTR(PATH,1,LENGTH(PATH)-4),'_printable.pdf') AS PATH_PRINTABLE FROM tUploadedDocs WHERE ID=" . $_GET['ID'];
		$result=mysqli_query($conn,$query);
		
		while($row=mysqli_fetch_array($result))
		{
			$file_to_open=$row['PATH'];
			//Ha az Uploader a Documents menuponton keresztul rakattint a View printable v. linkre, akkor neki a nyomtathato verzio fog kinyilni 
			if(isset($_GET['SRC']) && $_GET['SRC']=='print')
			{
				$file_to_open=$row['PATH_PRINTABLE'];
			}
		}
	
	//CONNECT TO THE FTP SERVER -->
	
		$ftp_server = '';
		$ftp_username   = "";
		$ftp_password   =  "";
	
	//GET THE FILE -->
		
		$file='ftp://' . $ftp_username . ':' . $ftp_password . '@' . DB_HOST . '/' . $file_to_open;
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		@readfile($file);
	}


?>
