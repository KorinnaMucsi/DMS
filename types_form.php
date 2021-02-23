<?php
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	require_once('history.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

	
//*******************************************************************************************************************************************************************************************
//																					SUBMIT FORM
//*******************************************************************************************************************************************************************************************
	//Ha az elejere tesszuk a $msg beallitasat, akkor megmarad az uzenet a form submit-ja utan, 
	//viszont az elso frissites utan eltunik, ahogy kell is, hogy mukodjon a program

	$msg='';

	if(isset($_SESSION['t_error']) && $_SESSION['t_error']!='')
	{
		$msg=$_SESSION['t_error'];
		unset($_SESSION['t_error']);
	}		
	if(isset($_SESSION['t_success']) && $_SESSION['t_success']!='')
	{
		$msg=$_SESSION['t_success'];
		unset($_SESSION['t_success']);
	}
	
	if(isset($_POST['submit']))
	{
		if(isset($_POST['descr']) && isset($_POST['sel_doc_descr']) && isset($_POST['sel_doc_uploader']) && isset($_POST['sel_doc_da']) && isset($_POST['sel_doc_ga']))
		{
			$descr=mysqli_real_escape_string($conn,trim($_POST['descr']));
			$doc_descr=$_POST['sel_doc_descr'];
			$uploader=$_POST['sel_doc_uploader'];
			$da=$_POST['sel_doc_da'];
			$ga=$_POST['sel_doc_ga'];
			
			//Ha posztolodott a tipus es nem ures az erteke, az azt jelenti, hogy modositasrol van szo (dupla vedelem, van egy ellenorzes az aktiv checkbox onchange eventen is)
			if(isset($_POST['type_id']) && $_POST['type_id']!='')
			{
				//Ha ez a valtozo 1, akkor nem lehet lefuttatni az update query-t (pl. van aktiv dokumentum a passzivizalando dokumentum tipus alatt)
				$err_actdoc=0;
				//Mivel tobb okbol is adhat hibauzenetet a program, az adott helyen kell tarolni az uzenet tartalmat
				$err_actdoc_descr='';
				$type_id=$_POST['type_id'];
				$doc_type_id=$type_id;
				
				//Meg kell nezni mi volt az eredeti activ statusz, hogy tudjunk rola history rekordot csinalni, ha megvaltozott
				if(isset($_POST['orig_active']) && $_POST['orig_active']=='on')
				{
					$orig_act=1;
				}
				else
				{
					$orig_act=0;
				}
				
				if(isset($_POST['active']) && $_POST['active']=='on')
				{
					$act=1;
				}
				else
				{
					//Ha passzivalni akarunk egy dokumentum tipust, eloszor meg kell neznunk, hogy van-e hozzatartozo aktiv dokumentum. Ebben az esetben nem engedhetjuk 
					//a passzivalast. Ellenkezo esetben passzivalhato a dokumentum tipus
					$query_actdoc=	"SELECT ID, TYPE_ID, DOC_ID, ACTIVE " .
									"FROM tUploadedDocs " .
									"WHERE 1=1 " .
									"AND TYPE_ID='" . $type_id . "' " .
									"AND ACTIVE=1 ";
					$result_actdoc=mysqli_query($conn, $query_actdoc);
					
					$cnt_actdoc=mysqli_num_rows($result_actdoc);
					
					//Ha a $cnt_actdoc==1, akkor az azt jelenti, hogy van aktiv dokumentum az adott tipus alatt es az hibauzenetet kell, hogy adjon
					if($cnt_actdoc==1)
					{	
						while($row_actdoc=mysqli_fetch_array($result_actdoc))
						{
							$act_docid=$row_actdoc['DOC_ID'];
							$act_docact=$row_actdoc['ACTIVE'];
						}
						
						//Ez a parameter donti el, hogy kell-e a hibauzenet
						$err_actdoc=1;
						$err_actdoc_descr=	'There is an active document in the DMS (' . $act_docid . ') for this document type!' . '<br>' .
											'You can\'t passivate the selected document type until there is an active document in the system for it!' . '<br>' .
											'Please passivate the document first!';
					}
					//Ha nincs alatta aktiv dokumentum, akkor meg kell, hogy nezzuk, hogy aktiv prilog tipus van-e alatta
					else
					{
						//Ha passzivalni akarunk egy dokumentum tipust, meg kell nezni, hogy van-e alatta levo aktiv prilog tipus, nem hagyhatunk
						//aktiv prilog tipust alatta, mivel akkor fel is lehetne ala tolteni dokumentumot, az viszont nem latszodna sehol
						//De ez az ellenorzes csak akkor erdekes, ha nincs aktiv dokumentum a dokumentum tipus alatt
						$query_actpdt=	"SELECT TYPE_ID " . 
										"FROM tPDocTypes PDT " .
										"WHERE 1=1 " .
										"AND DOC_TYPE_ID='" . $type_id . "' " .
										"AND ACTIVE=1 ";
										
						$result_actpdt=mysqli_query($conn, $query_actpdt);
						
						$cnt_actpdt=mysqli_num_rows($result_actpdt);
						
						//Ha van legalabb egy aktiv prilog tipus az adott dokumentum tipus alatt, akkor nem lehet passzivizalni
						if($cnt_actpdt>=1)
						{
							$err_actdoc=1;
							$err_actdoc_descr=	'There is at least one active appendix type in the DMS for this document type!' . '<br>' .
												'You can\'t passivate the selected document type until there is an active appendix type in the system for it!' . '<br>' .
												'Please passivate the appendix type first!';
						}					
					}
					
					$act=0;
				}	
				
				if($err_actdoc==0)
				{
					$query_update_doc="UPDATE tDocTypes SET DESCR='" . $descr . "', DOC_DESCR=" . $doc_descr . ", ACTIVE=" . $act . " WHERE TYPE_ID='" . $type_id . "' ";
					mysqli_query($conn, $query_update_doc);
					
					//Ha updatekor kulonbozik az eredeti SP statusz, akkor arrol history rekord kell
					if($orig_act!=$act)
					{
						History('tDocTypes', $type_id, $orig_act, $act, 'M', 'Change ACTIVE status from ' . $orig_act . ' from: ' . $act, 'DMS', $_SESSION['dms_username']);
					}
				}
				
			}
			//Ha posztolodott a tipus es ures az erteke, az azt jelenti, hogy uj tipusrol van szo es INSERT query kell
			else
			{
				$query_new_doct="SELECT CONCAT(A.COMBINATION_BASE, B.COMBINATION_BASE) AS TYPE_ID " .
								"FROM tTypeIDdb A JOIN tTypeIDdb B " .    
								"WHERE CONCAT(A.COMBINATION_BASE, B.COMBINATION_BASE) NOT IN " . 
								"( " .
								"SELECT TYPE_ID " .
								"FROM tDocTypes " .
								") " . 
								"ORDER BY TYPE_ID " . 
								"LIMIT 1 "; 
								
				$result_new_doct=mysqli_query($conn, $query_new_doct);
				while($row_new_doct=mysqli_fetch_array($result_new_doct))
				{
					$new_doct=$row_new_doct['TYPE_ID'];
					$doc_type_id=$new_doct;
				}	
				
				
				$query_insert_doct=	"INSERT INTO tDocTypes(TYPE_ID, DESCR, DOC_DESCR, NEW_TYPE, ACTIVE, TRX_USR_ID) " .
									"SELECT '" . $new_doct . "' , '" . $descr . "', " . $doc_descr . ", 1, 0, '" . $_SESSION['dms_username'] . "' ";
				mysqli_query($conn, $query_insert_doct);
			}
						
			//Leellenorizzuk, hogy az INSERT QUERY betoltotte-e a bazisba a formrol az uj tipust, illetve, hogy az UPDATE megtalalta-e
			$query_ctrl="SELECT IFNULL(COUNT(TYPE_ID),0) AS CNT_TP FROM tDocTypes WHERE TYPE_ID='" . $doc_type_id . "'";
			$result_ctrl=mysqli_query($conn, $query_ctrl);
			$row_ctrl=mysqli_fetch_array($result_ctrl);
			$cnt_tp=$row_ctrl['CNT_TP'];
			
			//Abban az esetben, ha a tipus sikeresen beirodott a bazisba/modosult, be lehet irni az UPLOAD, DA es GA jogosultsagokat is
			if($cnt_tp==1)
			{
				//Megszamoljuk, hogy hany rekordot kellene beirnia a bazisba, mivel ha egy felhasznalo az UPLOADER es a DA is, akkor neki csak egy rekord kell, nem pedig ketto
				$query_cnt_udaga=	"SELECT COUNT(*) AS CNT_UDAGA " .
									"FROM " .
									"( " .
									"SELECT USR, SUM(U), SUM(DA), SUM(GA) " .
									"FROM " .
									"( " .
									"SELECT '" . $uploader . "' AS USR, 1 AS U, 0 AS DA, 0 AS GA " .
									"UNION " .  
									"SELECT '" . $da . "' AS USR, 0 AS U, 1 AS DA, 0 AS GA " .
									"UNION " . 
									"SELECT '" . $ga . "' AS USR, 0 AS U, 0 AS DA, 1 AS GA " .
									")Q " .
									"GROUP BY USR " .
									")Q1 ";
									
				$result_cnt_udaga=mysqli_query($conn, $query_cnt_udaga);
				while($row_cnt_udaga=mysqli_fetch_array($result_cnt_udaga))
				{
					$cnt_udaga=$row_cnt_udaga['CNT_UDAGA'];
				}			
				
				//Mielott bevinnenk az UPLOADER, DA, GA rekordokat a bazisba, kitoroljuk oket az adott tipusra, mivel az update es az insert is ugyanugy 
				//kell, hogy mukodjon
				
				$query_del_udaga="DELETE FROM tUsrPermissionsDO WHERE TYPE_ID='" . $doc_type_id . "' ";
				mysqli_query($conn,$query_del_udaga);
				 
				$query_insert_udaga="INSERT INTO tUsrPermissionsDO(USR_ID, TYPE_ID, UPLOAD_P, APPROVE_P, APPROVE_GA_P) " .
									"SELECT USR, TYPE_ID, SUM(U), SUM(DA), SUM(GA) " .
									"FROM " .
									"( " .
									"SELECT '" . $uploader . "' AS USR, '" . $doc_type_id . "' AS TYPE_ID, 1 AS U, 0 AS DA, 0 AS GA " .
									"UNION " .  
									"SELECT '" . $da . "' AS USR, '" . $doc_type_id . "' AS TYPE_ID, 0 AS U, 1 AS DA, 0 AS GA " .
									"UNION " .  
									"SELECT '" . $ga . "' AS USR, '" . $doc_type_id . "' AS TYPE_ID, 0 AS U, 0 AS DA, 1 AS GA " .
									")Q " .
									"GROUP BY USR, TYPE_ID ";

				mysqli_query($conn, $query_insert_udaga);
				
				//Lellenorizzuk, hogy ugyanannyi rekordot irt-e be a program a bazisba, mint amennyit az elejen megszamoltunk, hogy kell neki. Ha igen, akkor uzenet, hogy minden rendben,
				//ellenkezo esetben hibauzenet
				if(mysqli_affected_rows($conn)==$cnt_udaga)
				{
					//Ez az uzenet a meglevo modositasakor, ha nincs aktiv/passziv hibauzenet
					if(isset($_POST['type_id']) && $_POST['type_id']!='' && $err_actdoc==0)
					{
						$_SESSION['t_success']=	SUCCESS_UPL_MSG_BEF .
												'Successfully changed.' .
												SUCCESS_UPL_MSG_AFT;
					}
					//Ez az uzenet a meglevo modositasakor, ha van aktiv/passziv hibauzenet
					if(isset($_POST['type_id']) && $_POST['type_id']!='' && $err_actdoc==1)
					{
						$_SESSION['t_error']=ERROR_UPL_MSG_BEF . $err_actdoc_descr . ERROR_UPL_MSG_AFT;
					}
					//Ez az uzenet az uj bevitelkor
					if(!isset($_POST['type_id']) || $_POST['type_id']=='')
					{
						$_SESSION['t_success']=	SUCCESS_UPL_MSG_BEF .
												'New type successfully added. It is not active and not enabled for upload by default.<br>'.
												'Please, add job titles to the distribution list first, save it and then check the Active checkbox on the Edit form.' .
												SUCCESS_UPL_MSG_AFT;
					}
				}
				else
				{
					$_SESSION['t_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
				}
			
			}//if(mysqli_affected_rows($conn)==1) vege
			else
			{
				$_SESSION['t_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
				//Ha nem sikerult lementeni a tipust
			}
 		}//if(isset($_POST['descr']) && isset($_POST['sel_doc_descr']) && isset($_POST['sel_doc_uploader']) && isset($_POST['sel_doc_da']) && isset($_POST['sel_doc_ga'])) vege
		
		die(header("Location:main.php?showTypes=True"));
	}

//*******************************************************************************************************************************************************************************************
//																					SUBMIT FORM END
//*******************************************************************************************************************************************************************************************
	
//QUERY TO CHECK THE USERS MAINTENANCE (MAIN MENU, MAINTENANCE, USERS BUTTON)-->	
	
	//Akkor fut, amikor a New type gombra kattint a felhasznalo az upload formon (ugyanazok mehetnek bele a Users maintenanceba, akik az approve-ba)
	//A Query megnezi, hogy van-e upload jogosultsaga a felhasznalonak, mivel csak az nyithat uj tipust, aki feltoltheti oket
	$query=	"SELECT SUM(UPLOAD_P) AS UPL " .
			"FROM " .
			"tUsrPermissionsDO " .
			"WHERE USR_ID='" . $_SESSION['dms_username'] . "'";
			
	$result=mysqli_query($conn, $query);
	
	while($row=mysqli_fetch_array($result))
	{
		$upload_p=$row['UPL'];
	}
	
//NO PERMISSION - MSG -->

	//Ha nincs semelyik tiupshoz upload jogosultsaga, akkor lefuttat egy Javascriptet, ami hibauzenetet ad a felhasznalonak es frissiti az oldalt
	if($upload_p==0)
	{
		$types_form='<script>alert("You don\'t have permission to this operation!");document.location.href="main.php?showMainMenu=True";</script>';
	}
	//Ha van, akkor osszerakja az oldalt
	else
	{	
		
//******************************************************************************************************************************************************************************************
//																		DOCUMENT DESCRIPTION
//******************************************************************************************************************************************************************************************
		$sel_doc_descr=	'<select id="sel_doc_descr" name="sel_doc_descr" required="required">' . "\n" .
						'<option value="" disabled selected="selected">---- Please, select a type ----</option>' . "\n";

		$query_doc_descr="SELECT DESCR_ID, DOC_DESCR FROM tDocDescr WHERE ACTIVE=1 ORDER BY SORT";
		$result_doc_descr=mysqli_query($conn,$query_doc_descr);
		
		while($row_doc_descr=mysqli_fetch_array($result_doc_descr))
		{
			$sel_doc_descr.='<option value="' . $row_doc_descr['DESCR_ID'] . '">' . $row_doc_descr['DOC_DESCR'] . '</option>' . "\n";
		}
			
		$sel_doc_descr.=	'</select>' . "\n";
//******************************************************************************************************************************************************************************************
//																	 DOCUMENT DESCRIPTION VEGE
//******************************************************************************************************************************************************************************************
		
//******************************************************************************************************************************************************************************************
//																			UPLOADER
//******************************************************************************************************************************************************************************************
		$sel_doc_uploader=	'<select id="sel_doc_uploader" name="sel_doc_uploader" required="required">' . "\n" .
							'<option value="" disabled selected="selected">---- Please, select an uploader ----</option>' . "\n";

		 
		$query_doc_uploader="SELECT USR_ID, CONCAT(L_NAME, ' ', F_NAME) AS FULL_NAME FROM tUsrs WHERE ACTIVE=1 AND SIGNATURE=1 ORDER BY FULL_NAME";
		
		$result_doc_uploader=mysqli_query($conn,$query_doc_uploader);
		
		while($row_doc_uploader=mysqli_fetch_array($result_doc_uploader))
		{
			$sel_doc_uploader.='<option value="' . $row_doc_uploader['USR_ID'] . '">' . $row_doc_uploader['FULL_NAME'] . '</option>' . "\n";
		}
			
		$sel_doc_uploader.=	'</select>' . "\n";
//******************************************************************************************************************************************************************************************
//		    																UPLOADER VEGE
//******************************************************************************************************************************************************************************************

//******************************************************************************************************************************************************************************************
//																			DOCUMENT APPROVER
//******************************************************************************************************************************************************************************************
		$sel_doc_da='<select id="sel_doc_da" name="sel_doc_da" required="required">' . "\n" .
					'<option value="" disabled selected="selected">---- Please, select a document approver ----</option>' . "\n";

		 
		$query_doc_da="SELECT USR_ID, CONCAT(L_NAME, ' ', F_NAME) AS FULL_NAME FROM tUsrs WHERE ACTIVE=1 AND SIGNATURE=1 ORDER BY FULL_NAME";
		
		$result_doc_da=mysqli_query($conn,$query_doc_da);
		
		while($row_doc_da=mysqli_fetch_array($result_doc_da))
		{
			$sel_doc_da.='<option value="' . $row_doc_da['USR_ID'] . '">' . $row_doc_da['FULL_NAME'] . '</option>' . "\n";
		}
			
		$sel_doc_da.=	'</select>' . "\n";
//******************************************************************************************************************************************************************************************
//																			DOCUMENT APPROVER VEGE
//******************************************************************************************************************************************************************************************

//******************************************************************************************************************************************************************************************
//																			GENERAL APPROVER
//******************************************************************************************************************************************************************************************
		$sel_doc_ga='<select id="sel_doc_ga" name="sel_doc_ga" required="required">' . "\n" .
					'<option value="" disabled selected="selected">---- Please, select a general approver ----</option>' . "\n";

		 
		$query_doc_ga="SELECT USR_ID, CONCAT(L_NAME, ' ', F_NAME) AS FULL_NAME FROM tUsrs WHERE ACTIVE=1 AND SIGNATURE=1 ORDER BY FULL_NAME";
		
		$result_doc_ga=mysqli_query($conn,$query_doc_ga);
		
		while($row_doc_ga=mysqli_fetch_array($result_doc_ga))
		{
			$sel_doc_ga.='<option value="' . $row_doc_ga['USR_ID'] . '">' . $row_doc_ga['FULL_NAME'] . '</option>' . "\n";
		}
			
		$sel_doc_ga.=	'</select>' . "\n";
//******************************************************************************************************************************************************************************************
//																			GENERAL APPROVER VEGE
//******************************************************************************************************************************************************************************************

		//Osszerakja az uj prilog tipus beviteli form kinezetet
		
		$types_form=	'<div id="mainContainerDiv" class="main_mainContainerDiv">' . "\n" .
						'<div class="main_containerDiv" id="main_containerDiv">' . "\n" .
						'<div class="main_titleDiv">' . "\n" .
						'<span class="main_title">Document Types<br>Maintenance Menu</span>' . "\n" .
						'<span class="main_titleDiv_sp"><img src="img/docs.png" alt="Document types"/></span>' . "\n" .
						'<span class="main_subtitle">You can find the list of the active document types in the DMS application.<br>' ."\n" .
						'Please, click on the \'New\' button to add a new document type!</span>' . "\n" .
						'<hr>' . "\n" .
						'</div>' . "\n" . 
						'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
						$msg . "\n" .	
						'<button type="button" id="btnTypes" name="btnTypes" onclick="NewTypeVisible();">Add new type</button>' . "\n" .
						'<form action="main.php?showTypes=True#" method="POST" id="form_types">' . "\n" .
								'<div class="main_dataDiv" id="main_dataDiv_a">' . "\n" .
									'<div class="upload_fline">' . "\n" .
										'<label id="lbl_type" for="type_id" class="upload_lbl">ID:</label>' . "\n" .
										'<input id="type_id" name="type_id" type="text"/>' . "\n" .
									'</div>' . "\n" .
									'<div class="upload_nline">' . "\n" .
										'<label for="descr" class="upload_lbl">Description:</label>' . "\n" .
										'<textarea id="descr" name="descr" cols="50" required="required"></textarea>' . "\n" .
									'</div>' . "\n" .
									'<div class="upload_nline">' . "\n" .
										'<label for="sel_doc_descr" class="upload_lbl">Type:</label>' . "\n" .
										$sel_doc_descr . "\n" .
									'</div>' . "\n" .
									'<div class="upload_nline">' . "\n" .
										'<label for="sel_doc_uploader" class="upload_lbl">Uploader:</label>' . "\n" .
										$sel_doc_uploader . "\n" .
									'</div>' . "\n" .
									'<div class="upload_nline">' . "\n" .
										'<label for="sel_doc_da" class="upload_lbl">Document Approver (DA):</label>' . "\n" .
										$sel_doc_da . "\n" .
									'</div>' . "\n" .
									'<div class="upload_nline">' . "\n" .
										'<label for="sel_doc_ga" class="upload_lbl">General Approver (GA):</label>' . "\n" .
										$sel_doc_ga . "\n" .
									'</div>' . "\n" .
									'<div class="upload_nline">' . "\n" .
										'<label for="active" id="lbl_active" class="upload_lbl">Active:</label>' . "\n" .
										'<input id="active" name="active" type="checkbox"/>' . "\n" .
										'<input id="orig_active" name="orig_active" type="checkbox"/>' . "\n" .
									'</div>' . "\n" .
									'<hr>' . "\n" .
									'<input type="submit" id="types_submit" name="submit" value="Save">' . "\n" .	
									'<button type="button" id="btnTypesCancel" name="btnTypesCancel" onclick="NewTypeInVisible()";>Cancel</button>' . "\n" .
								'</div>' . "\n" .
						'</form>' . "\n" .
						'<span class="main_subtitle"><i><b>Document types:</b></i></span>' . "\n" .
						'<div id="jqxgridTypes">' . "\n" .
						'</div>' . "\n" .
						'</div>' . "\n" .
						'</div>' . "\n" .
						'</div>';
	}
?>
