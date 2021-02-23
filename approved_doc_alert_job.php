<?php
	
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$popup_msg='';
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
//CHECK HST (tDocDistribHst) FOR RECORD -->

	//Query, amely megkeresi, hogy az adott felhasznalonak a fejlec tablaban van-e rekord a popup-ra vonatkozolag. Ha talal, akkor az ID-t egy valtozoban tarolja ($hst_id)
	
	$query_doc_distrib=	"SELECT UD.DOC_ID, UD.TYPE_ID, DH.ID, DH.U_DOC_ID, DH.USR_ID " . 
						"FROM " . 
						"tUploadedDocs UD JOIN tDocDistribHst DH " . 
						"ON UD.ID=DH.U_DOC_ID " .
						"WHERE UD.ACTIVE=1 " . 
						"AND DH.USR_ID='" . $_SESSION['dms_username'] . "' " . 
						"AND EVENT_ACKN=0";
						
	$result_doc_distrib=mysqli_query($conn,$query_doc_distrib);
		
	//Ha talal rekordot, akkor az azt jelenti, hogy a felhasznalo meg nem hagyta jova a dokumentumot	
	if(mysqli_num_rows($result_doc_distrib)>0)
	{
		while ($row_doc_distrib=mysqli_fetch_array($result_doc_distrib))
		{
			//Beallitjuk az ID-t, ami melle majd updatelni kell a jovahagyas infokat
			$hst_id=$row_doc_distrib['ID'];
			
			//Ha jova kell hagyni, akkor NO a parameter erteke
			$ackn_does_exist="No";
							
			//Popup iformaciok
			$doc_id=$row_doc_distrib['DOC_ID'];		
			$doc_usr=$_SESSION['dms_username'];		
		}
	}
	else
	{
			$ackn_does_exist="Yes";
	}	
	
//BUILD AND POPUP THE FORM TO THE USER -->	
		
			//A popup ablak csak akkor jelenik meg, ha a history tablaban (tDistribDocHst) az EVENT_ACKN mezo erteke 0

			if($ackn_does_exist=="No") //Uj jovahagyott dokumentum msgBox ellenorzes es elougrasztas
			{	
				$popup_msg=	'Document approved: ' . $doc_id . '<br>' . "\n" .
							'Please click on the \'Documents\' button on the main page to see the document!';

				//Ez a ket ertek alapjan fog bekerulni a bazisba a jovahagyas. (approved_doc_alert.php)
				$_SESSION['hst_id']=$hst_id;
				$_SESSION['appeared']=date("Y-m-d H:i:s");
			}
			else
			{
				$popup_msg='';
			}

			//Ez kuldi vissza az approved_doc_alert_job.js fajlnak az ablak parametereket, majd a Javascript fajl kinyitja azt
			echo $popup_msg;	
?>
