<?php
	
	session_start();
	
	//Visszaadja a HR Preview-hoz a roviditett nevet a HR funkcionak a felhasznalo melle rendelt ID alapjan a tHRFunctions tablabol (usrs_maint.js szkript PreviewPDF funkcio)
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	require_once('history.php');
	//A mail kuldeshez a funkciokat tartalmazo fajl
	require_once('send_mail.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
	//A type.js SaveListToDB funkcio altal tovabbitott munkahelyek bekerulnek a baziba illetve kitoroldnek a bazisbol a job_list array alapjan
	if(isset($_POST['QUERY']) && isset($_POST['TYPE_ID']))
	{
		$query=mysqli_real_escape_string($conn,trim($_POST['QUERY']));
		$type_id=$_POST['TYPE_ID'];
		
//HISTORY AZ UJ MUNKAHELYEKROL (Muszaly mielott megcsinalja az insert-et, mivel azutan nem talalja meg a query oket)**************************************************************
		$query_hst=	"SELECT HR_FUNC_ID, '" . $type_id . "' " .
					"FROM " .
					"( " .
					$query . " " .
					")Q " .
					"WHERE HR_FUNC_ID NOT IN (SELECT HR_FUNC_ID FROM tUsrPermissionsFN WHERE TYPE_ID='" . $type_id . "')";
		$result_hst=mysqli_query($conn, $query_hst);
		
		while($row_hst=mysqli_fetch_array($result_hst))
		{
			History('tUsrPermissionsFN', '', '', 'HR_FUNC_ID: ' . $row_hst['HR_FUNC_ID'] . ', TYPE_ID: ' . $type_id, 'N', 'New job title added to the distribution list', 
					'DMS', $_SESSION['dms_username']);
		}
//HISTORY AZ UJ MUNKAHELYEKROL VEGE**********************************************************************************************************************************************
			
		//Lementi a bazisba azokat a munkahelyeket, amelyek meg nem voltak a bazisban megnyitva az adott tipusra
		$query_insert=	"INSERT INTO tUsrPermissionsFN(HR_FUNC_ID, TYPE_ID, VIEW_P, MAIL_P, POPUP_P, TRX_USR_ID) " .
						"SELECT HR_FUNC_ID, '" . $type_id . "', 1, 1, 0, '" . $_SESSION['dms_username'] . "' " .
						"FROM " .
						"( " .
						$query . " " .
						")Q " .
						"WHERE HR_FUNC_ID NOT IN (SELECT HR_FUNC_ID FROM tUsrPermissionsFN WHERE TYPE_ID='" . $type_id . "')";
							
		mysqli_query($conn, $query_insert);

		$cnt_ins=mysqli_affected_rows($conn);

//HISTORY A TOROLT/TORLENDO MUNKAHELYEKROL****************************************************************************************************************************************
		$query_hst=	"SELECT HR_FUNC_ID, TYPE_ID FROM tUsrPermissionsFN WHERE TYPE_ID='" . $type_id . "' AND HR_FUNC_ID NOT IN (SELECT HR_FUNC_ID FROM (" . $query . ")Q )";
		
		$result_hst=mysqli_query($conn, $query_hst);
		
		while($row_hst=mysqli_fetch_array($result_hst))
		{
			History('tUsrPermissionsFN', '', '', 'HR_FUNC_ID: ' . $row_hst['HR_FUNC_ID'] . ', TYPE_ID: ' . $type_id, 'D', 'Job title deleted from the distribution list', 
					'DMS', $_SESSION['dms_username']);
		}
//HISTORY A TOROLT/TORLENDO MUNKAHELYEKROL VEGE***********************************************************************************************************************************
		
		//Kitorli azokat a munkahelyeket, amelyek eltuntek az eredeti listarol, hianyoznak a kapott array-bol, viszont a bazisban benne vannak
		$query_delete="DELETE FROM tUsrPermissionsFN WHERE TYPE_ID='" . $type_id . "' AND HR_FUNC_ID NOT IN (SELECT HR_FUNC_ID FROM (" . $query . ")Q )";
		
		$result_hr=mysqli_query($conn, $query_delete);
		
		$cnt_del=mysqli_affected_rows($conn);
		
		$query_act="SELECT ACTIVE FROM tDocTypes WHERE TYPE_ID='" . $type_id . "' ";
		$result_act=mysqli_query($conn, $query_act);
			
		while($row_act=mysqli_fetch_array($result_act))
		{
			$act=$row_act['ACTIVE'];
		}
			
		if($cnt_ins!=0 || $cnt_del!=0)
		{
			
			if($act==1)
			{
				$_SESSION['t_success']=SUCCESS_UPL_MSG_BEF . 'Distribution list successfully changed' . SUCCESS_UPL_MSG_AFT;
			}
			if($act==0)
			{
				$_SESSION['t_success']=	SUCCESS_UPL_MSG_BEF . 
										'Distribution list successfully changed.<br>' .
										'<b>Please, edit the document type and put a checkmark to the \'Active\' checkbox to complete it!</b>' . 
										SUCCESS_UPL_MSG_AFT;
			}

//MAIL KULDESE A DISZTRIBUCIOS LISTAHOZ UJONNAN HOZZAADOTT MUNKAHELYEKEN LEVO FELHASZNALOKNAK*************************************************************************************		
		
		//Osszeallitjuk azoknak a felhasznaloknak a listajat egy array-ba, akiknek mailt kell majd kapniuk arrol, hogy a munkahelyuket hozzaadtak egy dokumentum
		//tipus disztribucios listajahoz.
		
			$to_list_users=array();
			
			$query_mail_usrs=	"SELECT DDH.USR_ID " .
								"FROM tDocDistribHst DDH " .
								"JOIN tUsrs U ON DDH.USR_ID=U.USR_ID " .
								"WHERE DDH.LAST_ADDED_JT=1 " .
								"AND U.ACTIVE=1 " .
								"GROUP BY DDH.USR_ID ";
					
			$result_mail_usrs=mysqli_query($conn, $query_mail_usrs);
								
			while($row_mail_usrs=mysqli_fetch_array($result_mail_usrs))
			{
				array_push($to_list_users, $row_mail_usrs['USR_ID']);
			}	
			
			//Elkuldi a mailt, ha van legalabb egy felhasznalo, aki hozza lett adva a listahoz
			if(count($to_list_users)!=0)	
			{					
				$cc=CC;					
				//Osszeallitja a subject-ot
				$subject='DMS: Your job title was added to the distribution list of the document type: ' . $_POST['TYPE_ID'];
				//Osszeallitja a body-t
				$body='Please, click on the \'DOCUMENTS\' button (Quality main menu-->Shared Folders-->DMS) on the main page to see the document.';
				$body.='<br><br><br><br><br>This is an automatic e-mail message generated by the Quality system.<br>Please DO NOT RESPOND to this e-mail because the mail box is unattended.';
				
				//Elkuldi a mailt az elozo query-bol kiszedett felhasznaloknak
				echo sendMail($to_list_users, $subject, $body, $cc);
			}
			
			$query_last_added_upd="UPDATE tDocDistribHst SET LAST_ADDED_JT=0 WHERE LAST_ADDED_JT=1";
			mysqli_query($conn,$query_last_added_upd);
			
//MAIL KULDES VEGE****************************************************************************************************************************************************************		
		}
		if($cnt_ins==0 && $cnt_del==0)
		{
			if($act==1)
			{
				$_SESSION['t_success']=SUCCESS_UPL_MSG_BEF . 'Nothing was changed' . SUCCESS_UPL_MSG_AFT;
			}
			if($act==0)
			{
				$_SESSION['t_success']=	SUCCESS_UPL_MSG_BEF . 
										'Nothing was changed.<br>' .
										'<b>Please, edit the document type and put a checkmark to the \'Active\' checkbox to complete it!</b>' . 
										SUCCESS_UPL_MSG_AFT;
			}
			
		}
		
	}

?>
