<?php
	
	session_start();
	require_once('connectvars/connectvars.php');
	require_once('params/params.php');

//UNSET THE SESSIONS - THE MESSAGES DON'T NEED TO BE VISIBLE AFTER TYPE CHANGE -->

	//Ha modositjuk a tipust, el kell, hogy tunjon az elozo uzenet, mivel egy uj feltoltest kezd a felhasznalo
	unset($_SESSION['tp']);
	
	echo '<script>document.href=document.href</script>';
	
		
	//Ha tipus modositas utan nem ures a kombo, akkor le kell ellenoriznunk, hogy a felhasznalonak van-e jogosultsaga feltolteni a kivalasztott tipusu dokumentumot
	if($_POST['tp']!='')
	{
		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
		$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
		mysqli_set_charset($conn,"utf8");

		
		//Kivesszzuk a tipus nevet, hogy a hibauzenetben megjelenithessuk
		$query_descr=	"SELECT TYPE_ID, DESCR, DOC_KIND " .
						"FROM " .
						"( " .
						"SELECT TYPE_ID, DESCR, ACTIVE,'P' AS DOC_KIND " .
						"FROM tPDocTypes " .
						"WHERE ACTIVE=1 " .
						"UNION ALL " .
						"SELECT TYPE_ID, DESCR, ACTIVE, 'D' AS DOC_KIND " .
						"FROM tDocTypes " .
						"WHERE ACTIVE=1 " .
						")Q " .
						"WHERE TYPE_ID='" . $_POST['tp'] . "'";
		
		$result_descr=mysqli_query($conn,$query_descr);
		
		while($row_descr=mysqli_fetch_array($result_descr))
		{
			$doc_kind=$row_descr['DOC_KIND'];
			$type_descr=$row_descr['DESCR'];
		}
		
//USER PERMISSION CONTROL FOR THE SELECTED TYPE -->		
		
		$query_check_types=	"SELECT IFNULL(SUM(UPLOAD_P),0) AS UPLOAD " .
							"FROM " .
							"( " .
							"SELECT UPLOAD_P, TYPE_ID, USR_ID " .
							"FROM " .
							"tUsrPermissionsDO UP " .
							"UNION " .
							"SELECT UPLOAD_P, DT.TYPE_ID, USR_ID " .
							"FROM tUsrPermissionsDO UP JOIN tPDocTypes DT ON UP.TYPE_ID=DT.DOC_TYPE_ID " .
							")Q " .
							"WHERE 1=1 " .
							"AND USR_ID='" . $_SESSION['dms_username'] . "' " .
							"AND TYPE_ID='" . $_POST['tp'] . "' " .
							"AND UPLOAD_P=1";
							
		$result_check_types=mysqli_query($conn, $query_check_types);

		while($row_check_types=mysqli_fetch_array($result_check_types))
		{
			if($row_check_types['UPLOAD']==0)
			{
				$_SESSION['upload_error']=ERROR_UPL_MSG_BEF .  'You don\'t have permission to upload the ' . $type_descr . ' document!'  . ERROR_UPL_MSG_AFT;
				$_SESSION['tp']='';	
				$_SESSION['doc_kind']='';
			}
			else
			{
				//Megnezzuk, hogy amennyiben Prilog feltolteserol van szo, akkor van-e olyan aktiv dokumentum, amihez tartozik. A Documents menupont alatt, csak az aktiv jovahagyott
				//dokumentumok vannak es a hozzajuk tartozo prilogok, igy nem toltheto fel prilog nem aktiv/jovahagyott dokumentum ala, mert akkor azt nem lehetne megnezni
				if($doc_kind=='P')
				{
					$query_act_dtp=	"SELECT PT.*, IFNULL(UD.TYPE_ID,'-') AS ACT_TYPE_ID " .
									"FROM tPDocTypes PT " .
									"LEFT JOIN " . 
									"(SELECT TYPE_ID " .
									"FROM " .
									"tUploadedDocs UD " .
									"WHERE 1=1 " .
									"AND UD.ACTIVE=1 " .
									"AND GA_APPROVED=1 " .
									") UD ON PT.DOC_TYPE_ID=UD.TYPE_ID " .
									"WHERE 1=1 " .
									"AND PT.TYPE_ID='" . $_POST['tp'] . "'";
									
					$result_act_dtp=mysqli_query($conn,$query_act_dtp);
					
					while($row_act_dtp=mysqli_fetch_array($result_act_dtp))
					{
						$act_type_id=$row_act_dtp['ACT_TYPE_ID'];
					}

					if($act_type_id=='-')
					{
						$_SESSION['upload_error']=ERROR_UPL_MSG_BEF .  'The selected appendix has no active approved parent document!'  . ERROR_UPL_MSG_AFT;
						$_SESSION['tp']='';	
						$_SESSION['doc_kind']='';
					}
					else
					{
						unset($_SESSION['upload_error']);
						$_SESSION['tp']=$_POST['tp'];
						$_SESSION['doc_kind']=$doc_kind;	
					}
				}
				else
				{
					unset($_SESSION['upload_error']);
					$_SESSION['tp']=$_POST['tp'];
					$_SESSION['doc_kind']=$doc_kind;	
				}//if($doc_kind=='P') vege			
			}//if($row_check_types['UPLOAD']==0) vege
		}//while($row_check_types=mysqli_fetch_array($result_check_types)) vege
	}//if($_POST['tp']!='') vege
?>
