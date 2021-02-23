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

	if(isset($_SESSION['p_error']) && $_SESSION['p_error']!='')
	{
		$msg=$_SESSION['p_error'];
		unset($_SESSION['p_error']);
	}		
	if(isset($_SESSION['p_success']) && $_SESSION['p_success']!='')
	{
		$msg=$_SESSION['p_success'];
		unset($_SESSION['p_success']);
	}
	
	if(isset($_POST['submit']))
	{
		//Ha ki van valasztva, hogy melyik dokumentum tipus ala fog tartozni az appendix tipus, akkor az uj tipus es INSERT query kell
		if(isset($_POST['sel_doc_types']) && $_POST['sel_doc_types']!='' && isset($_POST['descr']))
		{
			$doc_type_id=$_POST['sel_doc_types'];
			$descr=$_POST['descr'];
			if(isset($_POST['print']))
			{
				$print=1;
			}
			else
			{
				$print=0;
			}
			
			$query_doc="SELECT IFNULL(max(TYPE_NO),0) AS TYPE_NO FROM tPDocTypes WHERE DOC_TYPE_ID='" . $doc_type_id . "'"; 
			$result_doc=mysqli_query($conn,$query_doc);
			
			while($row_doc=mysqli_fetch_array($result_doc))
			{
				$type_no=$row_doc['TYPE_NO'] + 1;
				$type_id=$doc_type_id . 'P' . $type_no;
			}
			
			$query_insert_pdoc=	"INSERT INTO tPDocTypes(DOC_TYPE_ID, TYPE_ID, TYPE_NO, DESCR, PRINTABLE, ACTIVE) " .
								"SELECT '" . $doc_type_id . "', '" . $type_id . "', " . $type_no . ", '" . $descr . "', " . $print . ", 1";
								
			$cnt=mysqli_query($conn, $query_insert_pdoc);	
			if($cnt==1)
			{
				$_SESSION['p_success']=SUCCESS_UPL_MSG_BEF . 'New type successfully added' . SUCCESS_UPL_MSG_AFT;
			}
			else
			{
				$_SESSION['p_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
			}
		
		}
		//Ha nincs valasztva, hogy melyik dokumentum tipus ala fog tartozni az appendix tipus (mivel ez nem elerheto modositas alatt), akkor az meglevo tipus es UPDATE query kell
		else
		{
			if(isset($_POST['descr']) && $_POST['descr']!='' && isset($_POST['type_id_pdt']) && $_POST['type_id_pdt']!='')
			{
				
				//Ha ez a valtozo 1, akkor nem lehet lefuttatni az update query-t (pl. van aktiv appendix a passzivizalando appendix tipus alatt)
				$err_actdoc=0;
				//Mivel tobb okbol is adhat hibauzenetet a program, az adott helyen kell tarolni az uzenet tartalmat
				$err_actdoc_descr='';
				$type_id=$_POST['type_id_pdt'];

				$descr=$_POST['descr'];

				if(isset($_POST['print']))
				{
					$print=1;
				}
				else
				{
					$print=0;
				}
				
				//Meg kell nezni mi volt az eredeti activ statusz, hogy tudjunk rola history rekordot csinalni, ha megvaltozott
				if(isset($_POST['orig_active_pdt']) && $_POST['orig_active_pdt']=='on')
				{
					$orig_act=1;
				}
				else
				{
					$orig_act=0;
				}
				
				if(isset($_POST['active_pdt']) && $_POST['active_pdt']=='on')
				{
					$act=1;
				}
				else
				{
					$act=0;
					
					//Ha passzivalni akarunk egy appendix tipust, eloszor meg kell neznunk, hogy van-e hozzatartozo aktiv appendix. Ebben az esetben nem engedhetjuk 
					//a passzivalast. Ellenkezo esetben passzivalhato az appendix tipus
					$query_actdoc=	"SELECT ID, TYPE_ID, DOC_ID, ACTIVE " .
									"FROM tUploadedDocs " .
									"WHERE 1=1 " .
									"AND TYPE_ID='" . $type_id . "' " .
									"AND ACTIVE=1 ";

					$result_actdoc=mysqli_query($conn, $query_actdoc);
					
					$cnt_actdoc=mysqli_num_rows($result_actdoc);
		
					//Ha a $cnt_actdoc==1, akkor az azt jelenti, hogy van aktiv appendix az adott tipus alatt
					if($cnt_actdoc==1)
					{				
						while($row_actdoc=mysqli_fetch_array($result_actdoc))
						{
							$act_docid=$row_actdoc['DOC_ID'];
						}							
						
						//Hibauzenetet kap a js oldalon, mivel passzivalni akart egy olyan appendix tipus, ami alatt van aktiv appendix
						$err_actdoc=1;
						$err_actdoc_descr=	'There is an active appendix in the DMS (\'' . $act_docid . '\') for this appendix type!' . '<br>' .
											'You can\'t passivate the selected appendix type if there is an active appendix in the system for it!'. '<br>' .
			  								'Please passivate the appendix first!';
					} //if($cnt_actdoc==1) vege		

				}
				
				if($err_actdoc==0)
				{
					$query_update="UPDATE tPDocTypes SET DESCR='" . $descr . "', PRINTABLE=" . $print . ", ACTIVE=" . $act . " WHERE ID=" . $_SESSION['ptype_id'];
					$cnt=mysqli_query($conn,$query_update);
					
					if($cnt==1)
					{
						$_SESSION['p_success']=SUCCESS_UPL_MSG_BEF . 'The selected type was successfully changed' . SUCCESS_UPL_MSG_AFT;
						
						//Ha updatekor kulonbozik az eredeti SP statusz, akkor arrol history rekord kell
						if($orig_act!=$act)
						{
							History('tPDocTypes', $_SESSION['ptype_id'], $orig_act, $act, 'M', 'Change ACTIVE status from ' . $orig_act . ' from: ' . $act, 'DMS', $_SESSION['dms_username']);
						}
					}
					else
					{
						$_SESSION['p_error']=ERROR_UPL_MSG_BEF . 'An error occured, please try again or call ext. 601!' . ERROR_UPL_MSG_AFT;
					}
				}
				else
				{
					$_SESSION['p_error']=ERROR_UPL_MSG_BEF . $err_actdoc_descr . ERROR_UPL_MSG_AFT;
				}//if($err_actdoc==0) vege
				
				
			}//if(isset($_POST['descr']) && $_POST['descr']!='') vege		
		}//if(isset($_POST['sel_doc_types']) && $_POST['sel_doc_types']!='' && isset($_POST['descr'])) vege
					
		die(header("Location:main.php?showPTypes=True"));
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

	//Ha nincs semelyik tiupshoz approve jogosultsaga, akkor lefuttat egy Javascriptet, ami hibauzenetet ad a felhasznalonak es frissiti az oldalt
	if($upload_p==0)
	{
		$ptypes_form='<script>alert("You don\'t have permission to this operation!");document.location.href="main.php?showMainMenu=True";</script>';
	}
	//Ha van, akkor osszerakja az oldalt
	else
	{	
		//Osszerakja az uj prilog tipus beviteli form kinezetet
		
//*******************************************************************************************************************************************************************************************
//																					DOCUMENT TYPE COMBO
//*******************************************************************************************************************************************************************************************
			
		$query_types="SELECT * FROM tDocTypes WHERE ACTIVE=1 ORDER BY TYPE_ID ASC";
		$result_types=mysqli_query($conn, $query_types);
		
		$sel_doc_type=	'<select id="sel_doc_types" name="sel_doc_types" required="required">' . "\n" .
						'<option value=""></option>' . "\n";
		
		//SELECT-OPTION elem feltoltese es kirajzolasa	
		while($row_types=mysqli_fetch_array($result_types))
		{
			$sel_doc_type.='<optgroup label="' . $row_types['TYPE_ID'] . '"><option value="' . $row_types['TYPE_ID'] . '"' . $selected . '>' . $row_types['DESCR'] . '</option></optgroup>' . "\n";
		}
			
		$sel_doc_type.=	'</select>' . "\n";


//*******************************************************************************************************************************************************************************************
//																				DOCUMENT TYPE COMBO END
//*******************************************************************************************************************************************************************************************
		
		$ptypes_form=	'<div id="mainContainerDiv" class="main_mainContainerDiv">' . "\n" .
						'<div class="main_containerDiv" id="main_containerDiv">' . "\n" .
						'<div class="main_titleDiv">' . "\n" .
						'<span class="main_title">Document Types (Appendix)<br>Maintenance Menu</span>' . "\n" .
						'<span class="main_titleDiv_sp"><img src="img/docs.png" alt="Document types (Appendix)"/></span>' . "\n" .
						'<span class="main_subtitle">You can find the list of the active document types (appendix) in the DMS application.<br>' ."\n" .
						'Please, click on the \'New\' button to add a new document type (Appendix)!</span>' . "\n" .
						'<hr>' . "\n" .
						'</div>' . "\n" . 
						'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
						$msg . "\n" .	
						'<button type="button" id="btnPTypes" name="btnPTypes" onclick="NewPTypeVisible();">Add new type</button>' . "\n" .
						'<form action="main.php?showPTypes=True#" method="POST" id="form_ptypes">' . "\n" .
								'<div class="main_dataDiv" id="main_dataDiv_a">' . "\n" .
									'<div class="upload_fline">' . "\n" .
										'<label id="lbl_type_pdt" for="type_id_pdt" class="upload_lbl">ID:</label>' . "\n" .
										'<input id="type_id_pdt" name="type_id_pdt" type="text"/>' . "\n" .
									'</div>' . "\n" .
									'<div class="upload_nline">' . "\n" .
										'<label for="sel_doc_types" id="lbltp" class="upload_lbl">Select document type:</label>' . "\n" .
										$sel_doc_type . "\n" .
									'</div>' . "\n" .	
									'<div class="upload_nline">' . "\n" .
										'<label for="descr" class="upload_lbl">Description:</label>' . "\n" .
										'<input id="descr" name="descr" type="text" required="required" style="width:50%;"/>' . "\n" .
									'</div>' . "\n" .
									'<div class="upload_nline">' . "\n" .
										'<label for="print" class="upload_lbl">Printable?</label>' . "\n" .
										'<input id="print" name="print" type="checkbox" />' . "\n" .
									'</div>' . "\n" .
									'<div class="upload_nline">' . "\n" .
										'<label for="active_pdt" id="lbl_active_pdt" class="upload_lbl">Active:</label>' . "\n" .
										'<input id="active_pdt" name="active_pdt" type="checkbox" />' . "\n" .
										'<input id="orig_active_pdt" name="orig_active_pdt" type="checkbox"/>' . "\n" .
									'</div>' . "\n" .
									'<hr>' . "\n" .
									'<input type="submit" id="ptypes_submit" name="submit" value="Save">' . "\n" .	
									'<button type="button" id="btnPTypesCancel" name="btnPTypesCancel" onclick="NewPTypeInVisible()";>Cancel</button>' . "\n" .
								'</div>' . "\n" .
						'</form>' . "\n" .
						'<span class="main_subtitle"><i><b>Types:</b></i></span>' . "\n" .
						'<div id="jqxgridPTypes">' . "\n" .
						'</div>' . "\n" .
						'</div>' . "\n" .
						'</div>' . "\n" .
						'</div>';
	}
?>
