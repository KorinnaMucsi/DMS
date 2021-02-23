<?php

/*
Created by: Mucsi Korinna
Date of creation: 07.05.2015.
Description: The following file is used to build up the main menu's buttons
*/
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	//A mail kuldeshez a funkciokat tartalmazo fajl
	require_once('send_mail.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
	$query=	"SELECT SUM(UPLOAD_P) AS UPL " .
			"FROM " .
			"tUsrPermissionsDO UP " .
			"WHERE USR_ID='" . $_SESSION['dms_username'] . "'";
			
	$result=mysqli_query($conn, $query);
	
	while($row=mysqli_fetch_array($result))
	{
		$upload_p=$row['UPL'];
	}
	
	if($upload_p==0)
	{
		$new_upload='<script>alert("You don\'t have permission to this operation!");document.location.href="main.php?showMainMenu=True";</script>';
	}
	else
	{

//SUBMIT-->
		if(isset($_POST['submit']))
		{
			//Ha van kivalasztott tipus, akkor betoltjuk a $document_typ valtozoba, ami kesobb a PATH mezo osszerakasahoz fog kelleni a bazisba
			//A $_SESSION['tp'] az upload_check_type_perm fajlban allitodik be a tipus jogozultsag ellenorzeskor
			if(isset($_SESSION['tp']))
			{
				$document_type=$_SESSION['tp'];
			}
			else
			{
				if(isset($_POST['doc_types']))
				{
					$document_type=$_POST['doc_types'];
				}
			}
				
			//Ha semmilyen hibaba nem utkozott a feltolteskor, akkor a program beallitja a parametereket, feltolti a bazisba a rekordot es ha ez sikeres, akkor a fajlt is feltolti fizikailag a 
			//fajlrendszerbe(NAS)
			
//FTP PARAMETERS SETUP-->
				
			$ftp_server = DB_HOST;
			$ftp_username   = "dms_uploader";
			$ftp_password   =  "eh5KgZ";
						
			$conn_id = ftp_connect($ftp_server) or die("could not connect to $ftp_server");
			@ftp_login($conn_id, $ftp_username, $ftp_password);
						
			$check_dir='../Share/' . UPLOAD_FOLDER . '/' . $document_type;
			$upload_dir='Web/Share/' . UPLOAD_FOLDER . '/' . $document_type;
			$file = $_FILES["upload_file"]["name"];
			$file_type = pathinfo($file,PATHINFO_EXTENSION);
				
//SETTING VALUES FOR THE INSERT QUERY -->
				
			$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
			$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
			mysqli_set_charset($conn,"utf8");

					
			$query_no="SELECT MAX(NO) AS MaxNo FROM tUploadedDocs WHERE YR=YEAR(CURDATE())";
			$result_no=mysqli_query($conn,$query_no);
					
			//Megnoveli 1-el a even beluli sorszamot
			if(mysqli_num_rows($result_no)==0)
			{
				$calc_no=1;
			}
			else
			{
				while($row_no=mysqli_fetch_array($result_no))
				{
					$calc_no=$row_no['MaxNo'] + 1;
				}
			}
			
			//Beallitja az egyedi dokumentum azonositot - dokumentum tipus + 1-el noveli a szamot a pont utan. Azt, hogy hanyadik helyen helyezkedik el a pont a string-ben, a
			//LOCATE('.',DOC_ID)+1 parancs segitsegevel tudjuk
			$query_doc=	"SELECT CAST(IFNULL(MAX(DocIDNo),0)+1 AS CHAR) AS MaxDocID " .
						"FROM " .
						"( " .
						"SELECT CAST(SUBSTRING(DOC_ID FROM LOCATE('.',DOC_ID)+1) AS UNSIGNED) AS DocIDNo " .
						"FROM tUploadedDocs " .
						"WHERE TYPE_ID='" . $document_type . "' " .
						")Q";

			$result_doc=mysqli_query($conn,$query_doc);
					
			if(mysqli_num_rows($result_doc)!=0)
			{
				while($row_doc=mysqli_fetch_array($result_doc))
				{
					$calc_doc_id=$document_type . '.' . $row_doc['MaxDocID'];
				}
			}
					
			//Osszerakja a feltoltendo fajlt - az uj nev ($calc_doc_id) + extenzio alapjan
			$remote_file_path = $upload_dir . "/" . $calc_doc_id . '.' . $file_type;
	
			//Minden tipuson belul csak egy aktiv lehet, ezert a tipus osszes dokumentumat passzivra allitja, majd az uj dokumentum default aktivkent kerul be a bazisba.
/*			$query_update="UPDATE tUploadedDocs SET ACTIVE=0 WHERE TYPE_ID='" . $document_type . "'";
			mysqli_query($conn, $query_update);
			//2016.10.12 Ezutan csak akkor kell deaktivalni, ha az ujat a GA jovahagyta, hogy addig is legyen aktiv dokumentum, de a prilogra tovabbra is ervenyes, mivel azt nem
			//hagyja jova a GA!
*/				
//INSERT QUERY - UPLOADED DOCUMENT -->

			//Kiszedi az adott tipusra a leirast (Kell a mailhez es a tUploadedDocs tabla toltesehez 
			//(Egyelore ez meg csak a prilogokra ervenyes, de tervben van a bevezetese a dokumetnumokra is, ahogy az uj dokumentum tipusokat is a felhasznalok fogjak 
			//megnyitni a prilgokhoz hasonloan.))
			
			$query_tp="SELECT DESCR FROM tPDocTypes WHERE TYPE_ID='" . $document_type . "'";
			$result_tp=mysqli_query($conn,$query_tp);
					
			while($row_tp=mysqli_fetch_array($result_tp))
			{
				$doc_descr=$row_tp['DESCR'];
			}
			
			//A dokumentum tipusra is rateszi 
			$query_dtp="SELECT DESCR FROM tDocTypes WHERE TYPE_ID='" . $document_type . "'";
			$result_dtp=mysqli_query($conn,$query_dtp);
					
			while($row_dtp=mysqli_fetch_array($result_dtp))
			{
				$doc_descr_dtp=$row_dtp['DESCR'];
			}		
				

			//Ket kulon query fut, attol fuggoen, hogy dokumentumot, vagy prilogot akar feltolteni a felhasznalo
			//Dokumentum
			if(isset($_SESSION['doc_kind']) && $_SESSION['doc_kind']=='D')
			{
				$query_insert=	"INSERT INTO tUploadedDocs(YR, NO, TYPE_ID, DOC_ID, DOC_DESCR, PATH, CHS_CREATOR, CHS_APPR, VRS_NO, DT_LAST_UPD, U_USR, U_DT, ACTIVE) " .
								"SELECT YEAR(CURDATE()) AS YR, " . $calc_no . ", '" . $document_type . "', '" . $calc_doc_id . "', '" . $doc_descr_dtp . "', '" . $remote_file_path . "', '" . $_POST['sel_dc'] . 
								"', '"  . $_POST['sel_da'] . "', '" . $_POST['vrs_no'] . "', '" . date('y.m.d', strtotime($_POST['lst_upd'])). "', '" . $_SESSION['dms_username'] . "', " .
								"NOW(), 1";
			}
			//Prilog (appendix)
			if(isset($_SESSION['doc_kind']) && $_SESSION['doc_kind']=='P')
			{				
				//Az osszes elozo appendixet az adott tipuson belul aktivalni kell (Ez itt rogton a feltoltesnel lefut, a dokumentumoknal csak a GA jovahagyas passzivizalhat!)
				$query_update="UPDATE tUploadedDocs SET ACTIVE=0 WHERE TYPE_ID='" . $document_type . "'";
				mysqli_query($conn, $query_update);
				
				//Resetelni kell a kezi passzivizalasra vonatkozo mezoket is. Ha peldaul passzivizaltak egy aktiv appendixet (AQP1.5), rakerul, hogy kezi modositas.
				//Ha feltoltenek egy uj appendixet (AQP1.6), az elozo passzivizalodik (AQP1.5), viszont ha nem nullazuk le a mezot mellette, hogy kezi modositas, 
				//akkor a program az alap logika szerint megengedne ujra aktivalni, ami utan ket aktiv appendix lenne a rendszerben az adott tipuson belul
				
				$query_update_map="UPDATE tUploadedDocs SET MANUAL_AP=0 WHERE TYPE_ID='" . $document_type . "'";
				mysqli_query($conn, $query_update_map);
				
				$query_insert=	"INSERT INTO tUploadedDocs(YR, NO, TYPE_ID, DOC_ID, DOC_DESCR, P_D, PATH, U_USR, U_DT, GA_APPROVED, GA_DT, GA_DUE_DT, ACTIVE) " .
								"SELECT YEAR(CURDATE()) AS YR, " . $calc_no . ", '" . $document_type . "', '" . $calc_doc_id . "', '" . $doc_descr . "', 'P', '" . $remote_file_path . "', '" . 
								$_SESSION['dms_username'] . "', NOW(), 1, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 1";
			}

			$rec_cnt=mysqli_query($conn, $query_insert);
					
			if($rec_cnt!=0)
			{
				//Ha lefutott a query, akkor megnezi, hogy az adott tipusra van-e mar mappa a Web/Share/DMS_UPLOADS mappaban - ha nincs, akkor letrehoz egy mappat a tipus nevevel
				if (!file_exists($check_dir)) 
				{
					mkdir($check_dir,0777);
				}
						
				//Feltolti a fajlt fizikailag a fajlrendszerbe(NAS)
				ftp_put($conn_id, $remote_file_path, $_FILES["upload_file"]["tmp_name"],FTP_ASCII);
				ftp_close($conn_id);

//*******************************************************************************************************************************************************************************************
//																			DOCUMENT_TYPE=DOCUMENT
//*******************************************************************************************************************************************************************************************

//MODIFY UPLOADED DOCUMENT - INSERT TABLE ON THE FIRST PAGE -->
				
				//Csak akkor kell a tablazat a .pdf alljara, ha az nem Prilog es csak akkor kell rola mailt kuldeni a DA-nak.
				if(isset($_SESSION['doc_kind']) && $_SESSION['doc_kind']=='D')
				{
					//Modositjuk a feltoltott fajlt, ugy, hogy az UPLOAD-kor kivalasztott Document Creator adatait beletesszuk az elso oldal alljan egy tablazatba
					require_once('pdf.php');
					$file_path='../Share/' . UPLOAD_FOLDER . '/' . $document_type . '/' . $calc_doc_id . '.' . $file_type;
					
					echo ModifyPDF($file_path,'phase_up', $file_path, $_POST['sel_dc'], date('d.m.Y', strtotime($_POST['lst_upd'])), '');
						
//COLLECT USERS TO SEND THEM MAIL ABOUT THE NEW UPLOAD -->						
						
					$to_list_users=array(); //Ezt fogja feltolteni a query es kuldeni azoknak, akiknek approve joga van a feltoltott dokumentumra
							
					$query_usrs="SELECT USR_ID " .
								"FROM tUsrPermissionsDO " .	
								"WHERE 1=1 " .
								"AND USR_ID NOT IN ('xy','xy') " .
								"AND APPROVE_P=1 " .
								"AND TYPE_ID='" . $document_type . "' " .
								"AND USR_ID='" . $_POST['sel_da'] . "'";
	
					$result_usrs=mysqli_query($conn, $query_usrs);
							
					while($row_usrs=mysqli_fetch_array($result_usrs))
					{
						array_push($to_list_users, $row_usrs['USR_ID']);
					}
							
					$cc='';
					//Osszeallitja a subject-ot
					$subject='Document uploaded: ' . $calc_doc_id;
					//Osszeallitja a body-t
					$body='Please, click on the \'APPROVE\' button (Quality main menu-->Shared Folders-->DMS) on the main page<br>and then click on the Approve link within the \'DA\' column to approve the document.';
					$body.='<br><br><br><br><br>This is an automatic e-mail message generated by the Quality system.<br>Please DO NOT RESPOND to this e-mail because the mail box is unattended.';
												
				}//if(isset($_SESSION['doc_kind']) && $_SESSION['doc_kind']=='D') vege
//*******************************************************************************************************************************************************************************************
//																			DOCUMENT_TYPE=DOCUMENT VEGE
//*******************************************************************************************************************************************************************************************
				
//*******************************************************************************************************************************************************************************************
//																DOCUMENT_TYPE=PRILOG (approve_document_ga.php alapjan)
//*******************************************************************************************************************************************************************************************
				//A prilogot nyotatas vedette vagy nyomtathatova tesszuk, majd azonnal megy az ertesito mail, es feltoltodik a history
				if(isset($_SESSION['doc_kind']) && $_SESSION['doc_kind']=='P')
				{
					
					//Modositjuk a feltoltott fajlt, ugy, hogy nyomtathato legyen, ha a Prilog tipusa nyomtathato illetve ne lehessen nyomtatni, ha a tipus nem nyomtathato
					//(tPDocTypes.PRINTABLE)
					
					//Ki kell szedni a prilog tipusrol, hogy nyomtathato-e
					$query_print="SELECT PRINTABLE FROM tPDocTypes WHERE TYPE_ID='" . $document_type . "'";

//PDF MODOSITAS -->
					$result_print=mysqli_query($conn,$query_print);
					while($row_print=mysqli_fetch_array($result_print))
					{
						$printable=$row_print['PRINTABLE'];
					}
					
					$file_path='../Share/' . UPLOAD_FOLDER . '/' . $document_type . '/' . $calc_doc_id . '.' . $file_type;
					
					require_once('FPDF/fpdf.php');
					require_once('FPDI/fpdi.php');
					require_once('FPDI/FPDI_Protection.php');
					
					$pdf = new FPDI_Protection();
					
					$pageCount = $pdf->setSourceFile($file_path);
					for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) 
					{
					    $tplIdx = $pdf->importPage($pageNo);
					
					    // add a page
					    $pdf->AddPage();
					    $pdf->useTemplate($tplIdx, null, null, 0, 0, true);
						
						$pdf->AddFont('DejaVuSans','','DejaVuSans.php');
						$pdf->SetFont('DejaVuSans','',8);

					
						//Ha nem nyomtathato, akkor le kell vedeni mindent a dokumentumon - nyomtathatosag es modositasi lehetoseg
						if($printable==0)
						{
							$pdf->SetProtection(array());
						}
						//Ha nyomtathato, akkor le kell vedeni a modositasi lehetoseget, viszont fent kell hagyni, hogy nyomtathato legyen
						if($printable==1)
						{
							$pdf->SetProtection(array('print'));
						}
					}
					
					$pdf->Output($file_path, 'F');
//<-- PDF MODOSITAS

					//Ki kell szedni a feltoltott dokumentum ID-jat
					$query_id="SELECT ID FROM tUploadedDocs WHERE TYPE_ID='" . $document_type . "' AND DOC_ID='" . $calc_doc_id . "'";

					$result_id=mysqli_query($conn,$query_id);
					while($row_id=mysqli_fetch_array($result_id))
					{
						$ud_id=$row_id['ID'];
					}
					
					//A role tablabol szedi ki, hogy kinek melyik dokumentumra van jogosultsaga es nincs tobbet GRP_LST update - ki tudjuk szedni a munkahelybol, hogy hova tartozik
					//a munkas
					$query_insert_hst=	"INSERT INTO tDocDistribHst(U_DOC_ID, USR_ID, VIEW_DUE_DT) " .
										"SELECT " . $ud_id . " AS U_DOC_ID, Q1.USR_ID, DATE_ADD(NOW(), INTERVAL 7 DAY) AS VIEW_DUE_DT "  .
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
										"AND Q1.TYPE_ID='" . $document_type . "' " .
										"AND Q1.VIEW_P=1 " .
										"AND U.ACTIVE=1 " .
										"GROUP BY U_DOC_ID, USR_ID, VIEW_DUE_DT ";
										
					$result_hst=mysqli_query($conn, $query_insert_hst);
					
					//Megnezzuk, hogy ki Uploader, DA es GA es azoknal a history rekordra 0-t teszunk a PLACE_ON_HST mezobe				
					$query_plc_hst=	"SELECT ID " .
									"FROM tDocDistribHst " .
									"WHERE 1=1 " .
									"AND U_DOC_ID=" . $ud_id . " " .
									"AND (USR_ID IN " .
									"( " .
									"SELECT U_USR " .
									"FROM tUploadedDocs " .
									"WHERE ID=" . $ud_id . " " .
									") " .
									"OR USR_ID IN " .
									"( " .
									"SELECT UDD.A_USR " .
									"FROM tUploadedDocs UD " .
									"JOIN tPDocTypes PT ON UD.TYPE_ID=PT.TYPE_ID " .
									"JOIN tUploadedDocs UDD ON PT.DOC_TYPE_ID=UDD.TYPE_ID " .
									"WHERE UD.ID=" . $ud_id . " " .
									"AND UDD.ACTIVE=1 " .
									"AND UDD.GA_APPROVED=1 " .
									") " .
									"OR USR_ID IN " .
									"( " .
									"SELECT UDD.GA_USR " .
									"FROM tUploadedDocs UD " .
									"JOIN tPDocTypes PT ON UD.TYPE_ID=PT.TYPE_ID " .
									"JOIN tUploadedDocs UDD ON PT.DOC_TYPE_ID=UDD.TYPE_ID " .
									"WHERE UD.ID=" . $ud_id . " " .
									"AND UDD.ACTIVE=1 " .
									"AND UDD.GA_APPROVED=1 " .
									")) ";
									
					$result_plc_hst=mysqli_query($conn,$query_plc_hst);		
					
					while($row_plc_hst=mysqli_fetch_array($result_plc_hst))
					{
						$query_update_plc_hst=	"UPDATE tDocDistribHst SET VIEW_DT=NOW(), EVENT_APPEARED_DT=NOW(), EVENT_ACKN=1, EVENT_ACKN_DT=NOW(), " .
												"EVENT_DESCR='Notification acknowledged', VIEW_COUNTER=-7 WHERE ID=" . $row_plc_hst['ID'];
						mysqli_query($conn,$query_update_plc_hst);
					}
					
					$to_list_users=array(); //Ezt fogja feltolteni a query es kuldeni azoknak, akiknek nezesi joga van a feltoltott dokumentumra (Prilog tipusnal nincs approve)
								
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
								"AND TYPE_ID='" .  $document_type . "' " .
								"GROUP BY USR_ID ";						

					$result_usrs=mysqli_query($conn, $query_usrs);
								
					while($row_usrs=mysqli_fetch_array($result_usrs))
					{
						array_push($to_list_users, $row_usrs['USR_ID']);
					}							
								
					//Osszeallitja a subject-ot
					$subject='Document approved: ' .  $calc_doc_id;
					//Osszeallitja a body-t
					$body='Please, click on the \'DOCUMENTS\' button (Quality main menu-->Shared Folders-->DMS) on the main page to see the document.';
	
				}//if(isset($_SESSION['doc_kind']) && $_SESSION['doc_kind']=='P') vege
				
//*******************************************************************************************************************************************************************************************
//																				DOCUMENT_TYPE=PRILOG VEGE
//*******************************************************************************************************************************************************************************************
				
//SEND MAIL BASED ON THE CONFIGURED PARAMETERS. MAIL GOES TO 'DA' IF DOC_KIND='D', MAIL GOES TO USERS IF DOC_KIND='P'-->
	
					echo sendMail($to_list_users, $subject, $body, $cc);
		
//SUCCESSFUL UPLOAD -->
							
				$_SESSION['upload_success']= SUCCESS_UPL_MSG_BEF . 'Successfully uploaded!' . SUCCESS_UPL_MSG_AFT;
			}
				//Ha nem futott le az INSERT query, akkor nem kerul be a fajlrendszerbe a fajl es hibat jelez a felhasznalonak
			else
			{
				$_SESSION['upload_error']= ERROR_UPL_MSG_BEF . 'An error occured, please try again!' . ERROR_UPL_MSG_AFT;
			}
			
			//Ez azert kell, hogy ne legyen ujboli POST-olas, ha frissiteni akarjak a feltoltes utan a formot a felhasznalok
			die(header("Location:main.php?showNewUpload=True"));
			$_SESSION['tp']='';
		}//SUBMIT ellenorzes vege
	
	
//DISPLAY FORM ELEMENTS AND DATA-->

		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
		$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
		mysqli_set_charset($conn,"utf8");		
		
//*******************************************************************************************************************************************************************************************
//																				TYPE COMBO KIRAJZOLASA
//*******************************************************************************************************************************************************************************************
		$dtype='';
		$query_dtypes=	"SELECT * FROM tDocDescr WHERE ACTIVE=1";
		
		$result_dtypes=mysqli_query($conn, $query_dtypes);
		
		while($row_dtypes=mysqli_fetch_array($result_dtypes))
		{
			if(isset($_SESSION['dtp']) && $_SESSION['dtp']==$row_dtypes['DESCR_ID'])
			{
				$dtp_selected=' selected="selected" ';
			}
			else
			{
				$dtp_selected='';
			}

			$dtype.='<option value="' . $row_dtypes['DESCR_ID'] . '"' . $dtp_selected . '>'. $row_dtypes['DOC_DESCR'] . '</option>';
		}

//*******************************************************************************************************************************************************************************************
//																			TYPE COMBO KIRAJZOLASA - VEGE
//*******************************************************************************************************************************************************************************************
		
		if(isset($_SESSION['dtp']) && $_SESSION['dtp']!=0)
		{
			$where_dt="WHERE DT.DOC_DESCR=" . $_SESSION['dtp'] . " ";
		}
		else
		{
			$where_dt="WHERE 1=1 ";
		}
		
		$query_types=	"SELECT PDT.TYPE_ID, PDT.DESCR, PDT.ACTIVE,'P' AS DOC_KIND, DT.DOC_DESCR " .
						"FROM tPDocTypes PDT " .
						"JOIN tDocTypes DT ON DT.TYPE_ID=PDT.DOC_TYPE_ID " .
						$where_dt . 
						"AND PDT.ACTIVE=1 " .
						"UNION ALL " .
						"SELECT TYPE_ID, DESCR, ACTIVE, 'D' AS DOC_KIND, DOC_DESCR " .
						"FROM tDocTypes DT " .
						$where_dt .
						"AND ACTIVE=1 " .
						"ORDER BY TYPE_ID";

		$result_types=mysqli_query($conn, $query_types);
		
		$msg='';
		
		//Kiirja a szessziobol az uzenetet
		if(isset($_SESSION['upload_error']) && $_SESSION['upload_error']!='')
		{
			$msg=$_SESSION['upload_error'];
		}
		
		if(isset($_SESSION['upload_success']) && $_SESSION['upload_success']!='')
		{
			$msg=$_SESSION['upload_success'];
		}
			
		$new_upload=	'<div id="mainContainerDiv" class="main_mainContainerDiv">' . "\n" .
						'<div class="main_containerDiv">' . "\n" .
						'<div class="main_titleDiv">' . "\n" .
						'<span class="main_title">Please, select a file to upload!</span>' . "\n" .
						'<span class="main_titleDiv_sp"><img src="img/upload_ico.png" alt="Upload"/></span>' . "\n" .
						'<span class="main_subtitle">Upload the file after specifying the type of the file<br> and clicking on the Browse and then on the Upload button!</span>' . "\n" .
						'<hr>' . "\n" .
						'</div>' . "\n" . 
						'<div class="upload_dataDiv">' . "\n" .
						$msg . "\n" .		
						'<form enctype="multipart/form-data" action="main.php?showNewUpload=True" method="POST">' . "\n" .
						'<div class="upload_fline">' . "\n" .
						'<label for="types" class="upload_lbl">Type:</label>' . "\n" .
						'<select id="types" name="types" onchange="DTypeUpdate();" required="required">' . "\n" .
						'<option value="" disabled>---- Please, select a type ----</option>' . "\n" . 
						'<option value="0" selected="selected">ALL</option>' . "\n" .
						$dtype . "\n" .
						'</select>' . "\n" .
						'</div>' . "\n" .
						'<div class="upload_nline">' . "\n" .
						'<label for="doc_types" class="upload_lbl">Document Type:</label>' . "\n" .
						'<select id="doc_types" name="doc_types" onchange="checkTypePerm();" required="required">' . "\n" .
						'<option value="" disabled selected="selected">---- Please, select a type ----</option>' . "\n";
	
		//SELECT-OPTION elem feltoltese es kirajzolasa	
		while($row_types=mysqli_fetch_array($result_types))
		{
			if(isset($_SESSION['tp']) && $_SESSION['tp']==$row_types['TYPE_ID'])
			{
				$selected=' selected="selected" ';
			}
			else
			{
				$selected='';
			}
				
			$new_upload.='<option value="' . $row_types['TYPE_ID'] . '"' . $selected . '>' . $row_types['TYPE_ID'] . ': ' .$row_types['DESCR'] . '</option>' . "\n";
		
		}
			
		$new_upload.=	'</select>' . "\n";
						//'<button type="button" name="btnNew" onclick="Javascript:document.location.href=\'main.php?showPTypes=True\'";><img src="img/new.png" alt="New">New type</button>';
		
		//Ha van kivalasztva tipus, es van ra a felhasznalonak feltoltesi jogosultsaga, akkor ki lehet rajzolni a file upload es a tobbi mezot es az upload gombot is		
		if(isset($_SESSION['tp']) && $_SESSION['tp']!='')
		{
		
		//Osszeszedi a kreatorokat a tUsrs tablabol, akiknek a neve mellett a SIGNATURES oszlopban 1-es van es azok kozul kell valasztani
//*******************************************************************************************************************************************************************************************
//																				DOCUMENT CREATORS COMBOBOX
//*******************************************************************************************************************************************************************************************
			//Megnezzuk, hogy dokumentumot, vagy prilogot valasztott ki, mint feltoltendo tipust. (D-Dokumentum, P-Prilog)
			if(isset($_SESSION['doc_kind']) && $_SESSION['doc_kind']=='D')
			{
				$dc= 	'<div class="upload_nline">' . "\n" .
						'<label for="sel_dc" class="upload_lbl">Document Creator:</label>' . "\n" .
						'<select id="sel_dc" name="sel_dc" required>' . "\n" .
						'<option value="" disabled selected="selected">---- Please, select a creator ----</option>';

					
				$query_dc=	"SELECT USR_ID AS USR_GRP, CONCAT(L_NAME,' ', F_NAME) AS DESCR " .
							"FROM tUsrs " .
							"WHERE 1=1 " .
							"AND SIGNATURE=1 " .
							"AND ACTIVE=1 " . 
							"ORDER BY DESCR";
							
				$result_dc=mysqli_query($conn,$query_dc);
							
				while($row_dc=mysqli_fetch_array($result_dc))
				{
					
					if ($handle = opendir('signatures')) 
					{
	    				while (false !== ($entry = readdir($handle))) 
	    				{
	        				if ($entry != "." && $entry != "..") 
	        				{
	            				//levagjuk a .png kiterjesztest es azt hasonlitjuk ossze a bazisbol kivett nevekkel. Ehhez az kell, hogy a .png file neve ugyanaz legyen, 
	            				//mint a felhasznalo neve a tUsrs tablaban es a mellette levo SIGNATURES oszlopban 1-es legyen
	            				
	            				$entry_without_ext=substr($entry,0,strlen($entry)-4);
	            				if($entry_without_ext==$row_dc['USR_GRP'])
	            				{
	            					$dc.='<option value="' . $row_dc['USR_GRP'] . '" >' . $row_dc['DESCR'] . '</option>' . "\n";
	            				}
	            			}
	   					}
	    				closedir($handle);
	    			}				
				}
				
				$dc.=	'</select>' .
						'</div>' . "\n";
//*******************************************************************************************************************************************************************************************
//																				DOCUMENT CREATORS COMBOBOX
//*******************************************************************************************************************************************************************************************


				//Megnezi, hogy az adott dokumentum tipusra mely felhasznalok vannak megadva DA-kent es oket lehet kivalasztani a kombobol, mint DA			
			
//*******************************************************************************************************************************************************************************************
//																				DOCUMENT APPROVALS COMBOBOX
//*******************************************************************************************************************************************************************************************
				$da= 	'<div class="upload_nline">' . "\n" .
						'<label for="sel_da" class="upload_lbl">Document Approver:</label>' . "\n" .
						'<select id="sel_da" name="sel_da" required>';
					
				$query_da=	"SELECT Q1.USR_ID, CONCAT(L_NAME, ' ' , F_NAME) AS FULL_NAME " . 
							"FROM " .
							"( " .
							"SELECT USR_ID, TYPE_ID, APPROVE_P " .
							"FROM tUsrPermissionsDO UP " .
							"UNION " .
							"SELECT USR_ID, DT.TYPE_ID, APPROVE_P " .
							"FROM tUsrPermissionsDO UP " .
							"JOIN tPDocTypes DT ON UP.TYPE_ID=DT.DOC_TYPE_ID " .
							")Q1 " .
							"JOIN tUsrs U ON U.USR_ID=Q1.USR_ID " .
							"WHERE 1=1 " .
							"AND APPROVE_P=1 " .
							"AND TYPE_ID='" . $_SESSION['tp'] . "' " .
							"ORDER BY FULL_NAME ";
							
				$result_da=mysqli_query($conn,$query_da);
							
				while($row_da=mysqli_fetch_array($result_da))
				{
					$da.='<option value="' . $row_da['USR_ID'] . '" >' . $row_da['FULL_NAME'] . '</option>' . "\n";
				}
				
				$da.=	'</select>' .
						'</div>' . "\n";
//*********************************************************************************************************************************************************************************************
//																				DOCUMENT APPROVALS COMBOBOX
//*********************************************************************************************************************************************************************************************
			
//*******************************************************************************************************************************************************************************************
//																					VERSION, LAST UPDATE
//*******************************************************************************************************************************************************************************************
				$vrs_lu='<div class="upload_nline">' . "\n" .
						'<label for="vrs_no" class="upload_lbl">Version number:</label>' . "\n" .
						'<input id="vrs_no" name="vrs_no" type="text" required="required"/>' . "\n" .
						'</div>' . "\n" .
						'<div class="upload_nline">' . "\n" .
						'<label for="lst_upd" class="upload_lbl">Date of last update:</label>' . "\n" .
						'<input id="lst_upd" name="lst_upd" type="text" autocomplete="off" readonly="readonly" value="' . date('d.m.Y') . '"/>' . "\n" .
						'</div>' . "\n";
//*******************************************************************************************************************************************************************************************
//																					VERSION, LAST UPDATE
//*******************************************************************************************************************************************************************************************
			}//if(isset($_SESSION['doc_kind']) && $_SESSION['doc_kind']=='D') vege
			
			//Megnezzuk, hogy dokumentumot, vagy prilogot valasztott ki, mint feltoltendo tipust. (D-Dokumentum, P-Prilog)
			//Ha prilogot, akkor nem kell se creator, se approver se verzio szam se pedig last version mezo.
			if(isset($_SESSION['doc_kind']) && $_SESSION['doc_kind']=='P')
			{
				$dc='';
				$da='';
				$vrs_lu='';
			}
			
			$new_upload.=	'</div>' . "\n" .
							'<div class="upload_nline">' . "\n" .
							'<label for="upload_file" class="upload_lbl">Choose a file to upload:</label>' . "\n" .
							'<input id="upload_file" name="upload_file" accept="application/pdf" type="file" required="required"/>' . "\n" .
							$dc . "\n" .
							$da . "\n" .
							$vrs_lu . "\n" .
							'<hr>' . "\n" .
							'<input type="submit" id="upload_submit" name="submit" value="Upload File">' . "\n" .
							'</div>' . "\n" .
							'</form>' . "\n" .
							'</div>' . "\n";
		}
		else
		{
			$new_upload.=	'<hr>' . "\n" .
							'</div>' . "\n";
		}
		
		$new_upload.=	'</div>' . "\n" .
						'</div>' . "\n";
	}
?>
