<?php
	//Ez a fajl akkor fut, amikor az approve linkre kattint a felhasznalo az Approve formon az elso tablazaton belul.
	session_start();
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	//A mail kuldeshez a funkciokat tartalmazo fajl
	require_once('send_mail.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


//APPROVAL PERMISSION CONTROL QUERY -->
	
	$query=	"SELECT SUM(APPROVE_P) AS APPR " .
			"FROM " .
			"tUsrPermissions UP " .
			"JOIN " .
			"( " .
			"SELECT USR_ID AS UWG, USR_ID FROM tUsrs " .
			"UNION " .
			"SELECT GRP_ID AS UWG, USR_ID FROM tGroupsUsrs " .
			") Q1 " .
			"ON UP.USR_GRP=Q1.UWG " .
			"WHERE USR_ID='" . $_SESSION['dms_username'] . "' " . 
			"AND TYPE_ID='" . $_POST['TYPE'] . "'";
			

	$result=mysqli_query($conn,$query);
	
//APPROVAL PERMISSION CONTROL -->
	
	//Leellenorizzuk, hogy van-e az adott felhasznalonak jogosultsaga jovahagyni a kivalasztott tipusu dokumentumot
	while($row=mysqli_fetch_array($result))
	{
		$approve_p=$row['APPR'];
	}
	
	//Amennyiben nincs jogosultsaga, akkor 0 az eredmeny, amit a query visszaad es hibauzenetet kap a felhasznalo (response az approve.js fajlban)
	if($approve_p==0)
	{
		echo '0';
	}
	//Amennyiben van jogosultsaga, jovahagyhatja a dokumentumot
	else
	{
		
		if(isset($_POST['ID']) && isset($_POST['TYPE']) && isset($_POST['DOC']))
		{
			//Ez a query kiszedi az egyenkent definialt felhasznalokat es a csoportok alatt definialt felhasznalokat (itt minden felhasznalot egyenkent a csoport nevek alapjan),
			//majd belerakja a Hs tablaba, ami tartalmazza a listat, akiknek meg kell kapniuk az ertesitest az adott tipusu feltoltott dokumentumra

//QUERY TO FILL THE HST TABLE FOR MSG BOXES -->

			$grp=array();
			
			$query_insert_hst=	"INSERT INTO tDocDistribHst(U_DOC_ID, USR_ID, GRP_LST) " .
								"SELECT " . $_POST['ID'] . " AS U_DOC_ID, USR_GRP, '' "  .
								"FROM " .
								"( " .
								"SELECT USR_GRP, TYPE_ID, POPUP_P " .
								"FROM tUsrPermissions " .
								"WHERE U_G = 'U' " .
								"UNION " .
								"SELECT USR_ID AS USR_GRP, TYPE_ID, POPUP_P " .
								"FROM tGroupsUsrs GU " .
								"JOIN tUsrPermissions UP ON GU.GRP_ID=UP.USR_GRP " .
								"WHERE UP.U_G='G' " .
								")Q1 " .
								"WHERE 1=1 " .
								"AND Q1.TYPE_ID='" . $_POST['TYPE'] . "' " .
								"AND Q1.POPUP_P=1";	
														
			$result_hst=mysqli_query($conn, $query_insert_hst);
			
			$query_hst=	"SELECT USR_ID, U_DOC_ID, ID "  .
						"FROM tDocDistribHst " . 
						"WHERE U_DOC_ID=" . $_POST['ID'];
						
			
			$result_hst=mysqli_query($conn,$query_hst);
			
			while($row_hst=mysqli_fetch_array($result_hst))
			{
				$query_grp=	"SELECT GU.USR_ID, G.GRP_ID, G.DESCR " .
							"FROM tGroupsUsrs GU JOIN tGroups G " .
							"ON GU.GRP_ID=G.GRP_ID " .
							"JOIN tUsrPermissions UP " .
							"ON G.GRP_ID=UP.USR_GRP AND UP.U_G='G' AND TYPE_ID='" . $_POST['TYPE'] . "' AND VIEW_P=1 " .
							"WHERE USR_ID='" . $row_hst['USR_ID'] . "'";
				
				$result_grp=mysqli_query($conn,$query_grp);
				
				if(mysqli_num_rows($result_grp)!=0)
				{
					while($row_grp=mysqli_fetch_array($result_grp))
					{
						array_push($grp, $row_grp['DESCR']);
					}	
					
					$grp_fld=implode(",", $grp);
				}
				else
				{
					$grp_fld='-';
				}	
				
				
				$query_update_hst_grp="UPDATE tDocDistribHst SET GRP_LST='" . $grp_fld . "' WHERE ID=" . $row_hst['ID'];
				
				$result_update_hst_grp=mysqli_query($conn, $query_update_hst_grp);
				
				$grp=array();
				
			}			
			
//APPROVAL INFORMATION UPDATE ON THE SELECTED DOCUMENT -->

			//A jovahagyott dokumentumra rateszi, hogy jova van hagyva es a jovahagyora es az idore vonatkozo informaciokat			
			$query_update="UPDATE tUploadedDocs SET DOC_APPROVED=1, A_USR='" . $_SESSION['dms_username'] . "', A_DT=Now() WHERE ID=" . $_POST['ID'];
			
			$result=mysqli_query($conn, $query_update);
			
//IF APPROVAL SUCCEESS, MAIL AND MESSAGE -->
			
			if($result==1)
			{
			
//COLLECT USERS TO SEND THEM MAIL ABOUT THE NEW UPLOAD -->						
						
						$to_list_users=array(); //Ezt fogja feltolteni a query es kuldeni azoknak, akiknek approve joga van a feltoltott dokumentumra
						
						$query_usrs="SELECT USR_GRP " .
									"FROM " .
									"( " .
									"SELECT USR_GRP, VIEW_P, MAIL_P, TYPE_ID " .
									"FROM tUsrPermissions " .
									"WHERE U_G='U' " .
									"UNION SELECT USR_ID AS USR_GRP, VIEW_P, MAIL_P, TYPE_ID " .
									"FROM tUsrPermissions UP " .
									"JOIN tGroupsUsrs GU " .
									"ON UP.USR_GRP=GU.GRP_ID " .
									"WHERE U_G='G' " .
									")Q1 " .
									"JOIN tUsrs U ON Q1.USR_GRP=U.USR_ID " .
									"WHERE 1=1 " .
									"AND VIEW_P=1 " .
									"AND MAIL_P=1 " .
									"AND ACTIVE=1 " .
									"AND TYPE_ID='" . $_POST['TYPE'] . "' ";
									
						$result_usrs=mysqli_query($conn, $query_usrs);
						
						while($row_usrs=mysqli_fetch_array($result_usrs))
						{
							array_push($to_list_users, $row_usrs['USR_GRP']);
						}
						
						//Kiszedi az adott tipusra a leirast
						$query_tp="SELECT DESCR FROM tDocTypes WHERE TYPE_ID='" . $_POST['TYPE'] . "'";
						$result_tp=mysqli_query($conn,$query_tp);
				
						while($row_tp=mysqli_fetch_array($result_tp))
						{
							$doc_descr=$row_tp['DESCR'];
						}				
						
						//Osszeallitja a subject-ot
						$subject='Document approved: ' . $_POST['DOC'];
						//Osszeallitja a body-t
						$body='Please, click on the \'DOCUMENTS\' button (Quality main menu-->Shared Folders-->DMS) on the main page to see the document.';
						$body.='<br><br><br><br><br>This is an automatic e-mail message generated by the Quality system.<br>Please DO NOT RESPOND to this e-mail because the mail box is unattended.';
						
//SEND MAIL BASED ON THE CONFIGURED PARAMETERS -->

						echo sendMail($to_list_users, $subject, $body);

				$_SESSION['approve_success']= SUCCESS_UPL_MSG_BEF . 'The document was successfully approved!' . SUCCESS_UPL_MSG_AFT;
			}
			
//ELSE, ROLLBACK APPROVAL -->

			else
			{
				//Ha nincs egy rekord sem, amit az update query visszaadott volna, akkor az azt jelenti, hogy a dokumentum melletti approve mezok nem lettek modositva, igy
				//az meg jovahagyatlan - ezert ki kell torolni a HST informaciot is a tDocDistribHst tablakbol es hibauzenetet adni a felhasznalonak	
							
				$query_del_hdr="DELETE FROM tDocDistribHst WHERE U_DOC_ID=" . $_POST['ID'];
				mysqli_query($conn, $query_del_hdr);
				
				$_SESSION['approve_error']= ERROR_UPL_MSG_BEF . 'An error occured, please try again!' . ERROR_UPL_MSG_AFT;
			}
				
		}//if(isset($_POST['ID']) && isset($_POST['TYPE']) && isseT($_POST['DOC']))
		
		echo '1'; //Ez kell az ellenorzes miatt, amennyiben nincs jogosultsaga, akkor 0 az eredmeny, amit a query visszaad es hibauzenetet kap a felhasznalo (response az approve.js fajlban)

		
	}//if($approve_p==0)


?>
