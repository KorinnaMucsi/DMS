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
		
	if(isset($_POST['ID']) && isset($_POST['TYPE']) && isset($_POST['DOC']) && isset($_POST['DOC_APPROVED']) && isset($_POST['A_CMMT']))
	{
			
//APPROVAL INFORMATION UPDATE ON THE SELECTED DOCUMENT -->

		//A jovahagyott dokumentumra rateszi, hogy jova van hagyva es a jovahagyora es az idore vonatkozo informaciokat			
		$query_update="UPDATE tUploadedDocs SET GA_APPROVED=" . $_POST['DOC_APPROVED'] . ", GA_USR='" . $_SESSION['dms_username'] . "', GA_DT=NOW(), GA_DUE_DT=DATE_ADD(NOW(), INTERVAL 7 DAY), GA_CMMT='" . $_POST['A_CMMT'] . "' WHERE ID=" . $_POST['ID'];
			
		$result=mysqli_query($conn, $query_update);
			
//IF APPROVAL SUCCEESS, MAIL AND MESSAGE -->
			
		if($result==1)
		{
			
//COLLECT USERS TO SEND THEM MAIL ABOUT THE NEW UPLOAD -->
			$cc='';	
			//Kiszedi az adott tipusra a leirast
			$query_tp="SELECT DESCR FROM tDocTypes WHERE TYPE_ID='" . $_POST['TYPE'] . "'";
			$result_tp=mysqli_query($conn,$query_tp);
					
			while($row_tp=mysqli_fetch_array($result_tp))
			{
				$doc_descr=iconv('UTF-8','windows-1250',$row_tp['DESCR']);
			}				
					
//APPROVED BY GA						
			//Ha a zold pipat valasztotta ki a GA a jovahagyaskor, akkor disztribualodik a dokumentum es minden felhasznalora bekerul egy rekord a tDocDistribHst tablaba, akinek van jogosultsaga
			if($_POST['DOC_APPROVED']==1)
			{
				//Miutan a GA jovahagyta, deaktivizaljuk az osszes addigi azonos tipusu dokumentumot
				$query_update="UPDATE tUploadedDocs SET ACTIVE=0 WHERE TYPE_ID='" . $_POST['TYPE'] . "'";
				mysqli_query($conn, $query_update);
				
				//Resetelni kell a kezi passzivizalasra vonatkozo mezoket is. Ha peldaul passzivizaltak egy aktiv dokumentumot (AQ.5), rakerul, hogy kezi modositas.
				//Ha feltoltenek egy uj dokumentumot (AQ.6), az elozo passzivizalodik (AQ.5), viszont ha nem nullazuk le a mezot mellette, hogy kezi modositas, 
				//akkor a program az alap logika szerint megengedne ujra aktivalni, ami utan ket aktiv dokumentum lenne a rendszerben az adott tipuson belul
				
				$query_update_map="UPDATE tUploadedDocs SET MANUAL_AP=0 WHERE TYPE_ID='" . $_POST['TYPE'] . "'";
				mysqli_query($conn, $query_update_map);
				
				//Majd aktivra allitjuk azt az egy feltoltott dokumentumot, amelyet eppen jovahagyott
				$query_update="UPDATE tUploadedDocs SET ACTIVE=1 WHERE ID=" . $_POST['ID'];
				mysqli_query($conn, $query_update);
				
			//Ez a query kiszedi az egyenkent definialt felhasznalokat es a csoportok alatt definialt felhasznalokat (itt minden felhasznalot egyenkent a csoport nevek alapjan),
			//majd belerakja a Hs tablaba, ami tartalmazza a listat, akiknek meg kell kapniuk az ertesitest az adott tipusu feltoltott dokumentumra

//QUERY TO FILL THE HST TABLE FOR MSG BOXES -->
				
				$query_insert_hst=	"INSERT INTO tDocDistribHst(U_DOC_ID, USR_ID, VIEW_DUE_DT) " .
									"SELECT " . $_POST['ID'] . " AS U_DOC_ID, Q1.USR_ID, DATE_ADD(NOW(), INTERVAL 7 DAY) AS VIEW_DUE_DT "  .
									"FROM " .
									"( " .
									"SELECT USR_ID, TYPE_ID, VIEW_P, UP.HR_FUNC_ID " .
									"FROM tUsrPermissionsFN UP JOIN tUsrRoles UR ON UP.HR_FUNC_ID=UR.HR_FUNC_ID " .
									"UNION " .
									"SELECT USR_ID, DT.TYPE_ID, VIEW_P, UP.HR_FUNC_ID " .
									"FROM tUsrPermissionsFN UP " .
									"JOIN tPDocTypes DT ON UP.TYPE_ID=DT.DOC_TYPE_ID " .
									"JOIN tUsrRoles UR ON UP.HR_FUNC_ID=UR.HR_FUNC_ID " .	
									")Q1 " .
									"JOIN tUsrs U ON Q1.USR_ID=U.USR_ID " .
									"WHERE 1=1 " .
									"AND Q1.TYPE_ID='" . $_POST['TYPE'] . "' " .
									"AND Q1.VIEW_P=1 " .
									"AND U.ACTIVE=1 " .
									"GROUP BY U_DOC_ID, USR_ID, VIEW_DUE_DT";	
									
				$result_hst=mysqli_query($conn, $query_insert_hst);
					
				
				//Megnezzuk, hogy ki Uploader, DA es GA es azoknal a history rekordra 0-t teszunk a PLACE_ON_HST mezobe	(ez mar nem aktualis - 2017.06.01)	
				//Megnezzuk, hogy ki az Uploader, DA es GA es ha nekik ra kell kerulniuk a disztribucios listara, akkor rakerulnek, de mar eleve megnezett lesz a dokumentumuk a jovahagyas 
				//datumaval		
				$query_plc_hst=	"SELECT ID " . 
								"FROM tDocDistribHst " .
								"WHERE 1=1 " .
								"AND U_DOC_ID= " . $_POST['ID'] . " " .
								"AND (USR_ID IN " .
								"( " .
								"SELECT U_USR " .
								"FROM tUploadedDocs " .
								"WHERE ID= " . $_POST['ID'] . " " .  
								") " .
								"OR USR_ID IN " .
								"( " .
								"SELECT A_USR " .
								"FROM tUploadedDocs " .
								"WHERE ID= " . $_POST['ID'] . " " .  
								") " .
								"OR USR_ID IN " .
								"( " .
								"SELECT GA_USR " .
								"FROM tUploadedDocs " .
								"WHERE ID= " . $_POST['ID'] . " " .  
								")) ";

				$result_plc_hst=mysqli_query($conn,$query_plc_hst);		
				
				while($row_plc_hst=mysqli_fetch_array($result_plc_hst))
				{
					$query_update_plc_hst=	"UPDATE tDocDistribHst SET VIEW_DT=NOW(), EVENT_APPEARED_DT=NOW(), EVENT_ACKN=1, EVENT_ACKN_DT=NOW(), " .
											"EVENT_DESCR='Notification acknowledged', VIEW_COUNTER=-7 WHERE ID=" . $row_plc_hst['ID'];
					mysqli_query($conn,$query_update_plc_hst);
				}
				
				$to_list_users=array(); //Ezt fogja feltolteni a query es kuldeni azoknak, akiknek view joga van a feltoltott dokumentumra
				
				$query_usrs="SELECT UR.USR_ID " .
							"FROM  " .
							"( " .
							"SELECT HR_FUNC_ID, UP.TYPE_ID, VIEW_P, MAIL_P " .
							"FROM " .
							"tUsrPermissionsFN UP " .
							"UNION " .
							"SELECT HR_FUNC_ID, DT.TYPE_ID, VIEW_P, MAIL_P " .
							"FROM tUsrPermissionsFN UP JOIN tPDocTypes DT ON UP.TYPE_ID=DT.DOC_TYPE_ID " .
							")Q1 " .
							"JOIN tUsrRoles UR ON UR.HR_FUNC_ID=Q1.HR_FUNC_ID " .
							"JOIN tUsrs U ON UR.USR_ID=U.USR_ID " .
							"WHERE 1=1 " .
							"AND VIEW_P=1 " .
							"AND MAIL_P=1 " .
							"AND U.ACTIVE=1 " .
							"AND TYPE_ID='" .  $_POST['TYPE'] . "' " .
							"GROUP BY USR_ID ";		
											
				$result_usrs=mysqli_query($conn, $query_usrs);
							
				while($row_usrs=mysqli_fetch_array($result_usrs))
				{
					array_push($to_list_users, $row_usrs['USR_ID']);
				}							
							
				//Osszeallitja a subject-ot
				$subject='Document approved: ' . $_POST['DOC'];
				//Osszeallitja a body-t
				$body='Please, click on the \'DOCUMENTS\' button (Quality main menu-->Shared Folders-->DMS) on the main page to see the document.';
				
				//Modositja a dokumentumot a pdf.php fajl meghivasaval - reateszi a vegso oszlopot a GA jovahagyasaval es levedi a dokumentumot a tovabbi modositastol es nyomtatastol
				$query_path="SELECT GA_DT, PATH, CONCAT(SUBSTR(PATH,1,LENGTH(PATH)-4),'_printable.pdf') AS PATH_PRINTABLE FROM tUploadedDocs WHERE ID=" . $_POST['ID'];
				$result_path=mysqli_query($conn,$query_path);
				if(mysqli_num_rows($result_path)==1)
				{
					while($row_path=mysqli_fetch_array($result_path))
					{
						$file_path='../' . substr($row_path['PATH'],4,strlen($row_path['PATH']));
						$file_path_printable='../' . substr($row_path['PATH_PRINTABLE'],4,strlen($row_path['PATH_PRINTABLE']));
						$lst_upd=$row_path['GA_DT'];
						$_SESSION['fp']=$file_path . '<br>' .$file_path_printable;
					}
				}
				require_once('pdf.php');
				//Elkesziti a csak az Olganak elerheto dokumentumot, amit ki tud nyomtatni, de minden masra ez is le van vedve.
				//Mas neven kerul lementesre + _printable (A sorrendben eloszor a printable-t kell megcsinalni, mivel ez mas neven menti le es az eredetit meg lehet ujbol modositani,
				//mig ha ez a masodik, mar a levedettbol nem lehet ujbol nyomtathatot csinalni)
				echo ModifyPDF($file_path,'phase_ga', $file_path_printable, $_SESSION['dms_username'], date('d.m.Y', strtotime($lst_upd)), 'AllowPrintOnly');
				//Elkesziti a mindenkinek elerheto alairt es levedett dokumentumot
				echo ModifyPDF($file_path,'phase_ga', $file_path, $_SESSION['dms_username'], date('d.m.Y', strtotime($lst_upd)), 'All');
			}
//NOT APPROVED BY GA
			//Ha a piros X-et valasztotta ki a GA jovahagyaskor, akkor a DA-knak megy az info, hogy nem fogadtak el az o jovahagyott dokumentumat
			if($_POST['DOC_APPROVED']==2)
			{
				$to_list_users=array(); //Ezt fogja feltolteni a query es kuldeni azoknak, akiknek DA joga van a feltoltott dokumentumra
				
				$query_usrs="SELECT USR_ID " .
							"FROM tUsrPermissionsDO " .
							"WHERE 1=1 " .
							"AND USR_ID NOT IN ('xy','xy') " .
							"AND APPROVE_P=1 " .
							"AND TYPE_ID='" . $_POST['TYPE'] . "' ";
										
				$result_usrs=mysqli_query($conn, $query_usrs);
							
				while($row_usrs=mysqli_fetch_array($result_usrs))
				{
					array_push($to_list_users, $row_usrs['USR_GRP']);
				}							
							
				//Osszeallitja a subject-ot
				$subject='Document not approved by GA: ' . $_POST['DOC'];
				//Osszeallitja a body-t
				$body='Dear DA!';
				$body.='<br><br>The document ' . $doc_descr . ' was not approved by the GA.';
				$body.='<br><br>Comment: ' . $_POST['A_CMMT'];
			}
						
			$body.='<br><br><br><br><br><hr>This is an automatic e-mail message generated by the Quality system.<br>Please DO NOT RESPOND to this e-mail because the mail box is unattended.';
							
//SEND MAIL BASED ON THE CONFIGURED PARAMETERS -->

			echo sendMail($to_list_users, $subject, $body, $cc);
	
			$_SESSION['approve_success']= SUCCESS_UPL_MSG_BEF . 'Successfully changed!' . SUCCESS_UPL_MSG_AFT;
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
		
?>
