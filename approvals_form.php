<?php
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
//QUERY TO CHECK THE APPROVE PERMISSIONS (MAIN MENU APPROVE BUTTON)-->	
	
	//Akkor fut, amikor az APPROVE gombra kattint a felhasznalo a fo menu formon
	//A Query megnezi, hogy van-e barmelyik tipushoz approve jogosultsaga
	$query=	"SELECT SUM(UPLOAD_P) AS UPL, SUM(APPROVE_P) AS APPR, SUM(APPROVE_GA_P) AS APPR_GA " .
			"FROM " .
			"tUsrPermissionsDO UP " .
			"JOIN tUsrs U " .
			"ON UP.USR_ID=U.USR_ID " .
			"WHERE UP.USR_ID='" . $_SESSION['dms_username'] . "'";
			
	$result=mysqli_query($conn, $query);
	
	while($row=mysqli_fetch_array($result))
	{
		$upload_p=$row['UPL'];
		$approve_p=$row['APPR'];
		$approve_ga_p=$row['APPR_GA'];
	}

//NO PERMISSION - MSG -->

	//Ha nincs semelyik tiupshoz approve jogosultsaga, akkor lefuttat egy Javascriptet, ami hibauzenetet ad a felhasznalonak es frissiti az oldalt
	if($upload_p==0 && $approve_p==0 && $approve_ga_p==0)
	{
		$approvals='<script>alert("You don\'t have permission to this operation!");document.location.href="main.php?showMainMenu=True";</script>';
	}
	//Ha van, akkor osszerakja az oldalt
	else
	{	
	
		$msg='';
		
		//A frissitesnel ki kell irni, ha sikeres volt a jovahagyas, ezert meg kell nezni, hogy a jovahagyast vegzo PHP fajlok beallitottak-e ezeket a szessziokat az eredmeny alapjan
		if(isset($_SESSION['approve_success']) && $_SESSION['approve_success']!='')
		{
			$msg=$_SESSION['approve_success'];
		}
		
		if(isset($_SESSION['approve_error']) && $_SESSION['approve_error']!='')
		{
			$msg=$_SESSION['approve_error'];
		}

//IF USER HAS PERSMISSION TO THE APPROVALS MENU, THE PROGRAM BUILDS UP THE FORM -->

		//Osszerakja a jovahagyo form kinezetet
		$approvals= '<div id="mainContainerDiv" class="main_mainContainerDiv">' . "\n" .
					'<div class="main_containerDiv" id="main_containerDiv">' . "\n" .
					'<div class="main_titleDiv">' . "\n" .
					'<span class="main_title">Approval of the<br>uploaded documents</span>' . "\n" .
					'<span class="main_titleDiv_sp"><img src="img/approve.png" alt="Approve"/></span>' . "\n" .
					'<span class="main_subtitle">In the first table you can find documents waiting for approval. The second table contains the documents, that are already approved.<br>' . "\n" .
					'Click on the Approve link within the first table to approve the selected document.</span>' . "\n" .
					'<hr>' . "\n" .
					'</div>' . "\n" . 
					'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
					$msg . "\n" .
					'<span class="main_subtitle"><i><b>Uploaded documents waiting for approval:</b></i></span>' . "\n" .
					'<div id="jqxgrid">' . "\n" .
					'</div>' . "\n" .
					'</div>' . "\n" .
					'<div class="main_dataDiv" id="main_dataDiv_a">' . "\n" .
					'<span class="main_subtitle"><i><b>Approved documents:</b></i></span>' . "\n" .
					'<div id="jqxgridApp">' . "\n" .
					'</div>' . "\n" .
					'</div>' . "\n" .
					'</div>' . "\n" .
					'</div>';
	}
?>
