<?php
	
	session_start();
	require_once('connectvars/connectvars.php');
	require_once('params/params.php');
	
	//A type.js $("#active").click(function()) funkciotol kapjuk azt a tipust, amelyiket passzivizalni/aktivizalni szeretnenk - $_POST['TYPE_ID']
	if(isset($_POST['TYPE_ID']) && $_POST['TYPE_ID']!='' && isset($_POST['FUTURE_STATUS']) && ($_POST['FUTURE_STATUS']==0 || $_POST['FUTURE_STATUS']==1))
	{
		
		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
		$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
		mysqli_set_charset($conn,"utf8");
	
		$type_id=$_POST['TYPE_ID'];
		//Aktivalni vagy passzivalni szeretnenk a dokumentum tipust (0 - ha passzivalni, 1 - ha aktivalni)
		$doc_future_status=$_POST['FUTURE_STATUS'];
		$actdoc_err_no=0;
		$actdoc_err_descr='';
		$act_docid='';	

		//Megnezzuk passzivalasrol van-e szo
		if($doc_future_status==0)
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

			//Ha a $cnt_actdoc==1, akkor az azt jelenti, hogy van aktiv dokumentum az adott tipus alatt
			if($cnt_actdoc==1)
			{				
				while($row_actdoc=mysqli_fetch_array($result_actdoc))
				{
					$act_docid=$row_actdoc['DOC_ID'];
				}							
				
				//Hibauzenetet kap a js oldalon, mivel passzivalni akart egy olyan dokumentum tipus, ami alatt van aktiv dokumentum
				$actdoc_err_no=1;
				$actdoc_err_descr=	'There is an active document in the DMS (\'' . $act_docid . '\') for this document type!' . "\r\n" .
									'You can\'t passivate the selected document type if there is an active document in the system for it!'. "\r\n" .
	  								'Please passivate the document first!';
			}			
			//Ha nincs alatta aktiv dokumentum, akkor meg kell, hogy nezzuk, hogy aktiv prilog tipus van-e alatta
			else
			{
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
					$actdoc_err_no=1;
					$actdoc_err_descr=	'There is at least one active appendix type in the DMS for this document type!' . "\r\n" .
										'You can\'t passivate the selected document type until there is an active appendix type in the system for it!' . "\r\n" .
										'Please passivate the appendix type first!';
				}					
			} //if($cnt_actdoc==1) vege		
		}//if($doc_future_status==0) vege

		echo json_encode(array('ACTDOC_ERR_NO' => $actdoc_err_no, 'ACTDOC_ERR_NO_DESCR' => $actdoc_err_descr, 'ACT_DOCID' => $act_docid));
		  
	}//if(isset($_POST['TYPE_ID']) && $_POST['TYPE_ID']!='' && isset($_POST['FUTURE_STATUS']) && ($_POST['FUTURE_STATUS']==0 || $_POST['FUTURE_STATUS']==1))								
?>