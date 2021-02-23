<?php	
	session_start();
	
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	require_once('history.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"UTF-8");
	
	if(isset($_POST['DOC_OR_APPX']) && isset($_POST['DOC_ID']) && isset($_POST['TYPE_ID']) && isset($_POST['AP']))
	{
		$doc_app=$_POST['DOC_OR_APPX'];
		$doc_id=$_POST['DOC_ID'];
		$ap=$_POST['AP'];
		$type_id=$_POST['TYPE_ID'];
		
		$query_doc=	"SELECT ID, UD.TYPE_ID, MANUAL_AP, ACTIVE_TYPE " .
					"FROM tUploadedDocs UD " .
					"JOIN " .
					"( " .
					"SELECT TYPE_ID, ACTIVE AS ACTIVE_TYPE " .
					"FROM tDocTypes  DT " .
					"UNION " .
					"SELECT TYPE_ID, ACTIVE AS ACTIVE_TYPE " .
					"FROM tPDocTypes PDT " .
					")Q ON UD.TYPE_ID=Q.TYPE_ID " .
					" WHERE DOC_ID='" . $doc_id . "'";

		$result_doc=mysqli_query($conn,$query_doc);
			
		while($row_doc=mysqli_fetch_array($result_doc))
		{
			$upl_id=$row_doc['ID'];
			$manual_ap=$row_doc['MANUAL_AP'];
			$type_id=$row_doc['TYPE_ID'];
			$active_type=['ACTIVE_TYPE'];
		}
		
		//Az Appendix-eket, csak akkor lehet aktivalni, ha aktiv az appendix tipus, ahova tartoznak es ha elotte kezzel passzivalva lettek, elozo verziokat nem lehet
		//Az Appendix-eket barmikor lehet passzivalni
		if($doc_app=='Appendix')
		{
			
			//Ez a query arra szolgal, hogy leellenorizze, hogy van-e aktiv dokumentum, ami ala kerulhet az appendix, miutan aktivaltuk
			//Ha nincs, akkor elotte dokumentumot kell aktivalni, vagy feltolteni
			$query_act_doc=	"SELECT DOC_TYPE_ID AS DTYPE_ID, CASE COUNT(*) WHEN NULL THEN 0 ELSE COUNT(*) END AS CNT_DOC " .
							"FROM tUploadedDocs UD " .
							"JOIN " .
							"( " .
							"SELECT DOC_TYPE_ID " .
							"FROM tPDocTypes " .
							"WHERE TYPE_ID='" . $type_id . "'" .
							")Q ON UD.TYPE_ID=Q.DOC_TYPE_ID " .
							"WHERE 1=1 " .
							"AND ACTIVE=1 " .
							"AND GA_APPROVED=1 ";
							
			$result_act_doc=mysqli_query($conn,$query_act_doc);
			
			while($row_act_doc=mysqli_fetch_array($result_act_doc))
			{
				$dtype_id=['DTYPE_ID'];
				$cnt_doc=$row_act_doc['CNT_DOC'];
			}
		

			//passzivalas
			if($ap==0)
			{
				$query_upd="UPDATE tUploadedDocs SET ACTIVE=" . $ap . ", MANUAL_AP=1 WHERE ID=" . $upl_id;
				$cnt=mysqli_query($conn,$query_upd);
				$result_to_return=mysqli_affected_rows($conn);
				$msg_to_return="Appendix " . $doc_id . " successfully passivated!";
			}
			
			//aktivalas, ha nem aktiv az appendix tipus
			if($ap==1 && $active_type==0)
			{
				$result_to_return=0;
				$msg_to_return="Appendix " . $doc_id . " can't be activated, because the appendix type is not active! Please, activate the appendix type first!";
			}
			
			//aktivalas, ha nem kezi passzivalas volt elotte
			if($ap==1 && $manual_ap==0)
			{
				$result_to_return=0;
				$msg_to_return="Appendix " . $doc_id . " can't be activated, because it was not passivated manually! Please, try to upload a new version of this appendix instead!";
			}
			
			//aktivalas, ha nincs aktiv dokumentum ami ala csapodhatna az appendix (Ide meg ide lehetne tenni az uzenetbe zarojelbe, hogy melyik volt a fo dokumentuma)
			if($ap==1 && $cnt_doc==0)
			{
				$result_to_return=0;
				$msg_to_return=	"Appendix " . $doc_id . " can't be activated, because there is no active document for it! " . "\n" .
								"Please, activate the right document or upload a new document first (" . $type_id . ")!";
			}			
			
			//aktivalas, ha kezi passzivalas volt elotte es van aktiv dokumentum, ami ala tartozik
			if($ap==1 && $manual_ap==1 && $cnt_doc==1)
			{
				$query_upd="UPDATE tUploadedDocs SET ACTIVE=" . $ap . ", MANUAL_AP=1 WHERE ID=" . $upl_id;
				$cnt=mysqli_query($conn,$query_upd);
				$result_to_return=mysqli_affected_rows($conn);
				$msg_to_return="Appendix " . $doc_id . " successfully activated!";
			}
		}
		
		//A Dokumentumot az appendixre vonatkozo szabalyokon felul csak akkor lehet passzivalni, ha nincs alatta aktiv appendix
		if($doc_app=='Document')
		{
		
			$query_appx_cnt="SELECT COUNT(*) AS CNT_APPX " .
							"FROM " .
							"( " .
							"SELECT UD.DOC_ID, UD.TYPE_ID, Q.DOC_ID AS APPX_ID, Q.TYPE_ID AS APPX_TYPE_ID " .
							"FROM " .
							"tUploadedDocs UD " .
							"JOIN " .
							"( " .
							"SELECT UD.ID, UD.DOC_ID, UD.TYPE_ID, DOC_TYPE_ID " .
							"FROM tUploadedDocs UD " .
							"JOIN tPDocTypes PDT ON UD.TYPE_ID=PDT.TYPE_ID " .
							"WHERE 1=1 " .
							"AND P_D='P' " .
							"AND UD.ACTIVE=1 " .
							"AND GA_APPROVED=1 " .
							")Q ON UD.TYPE_ID=Q.DOC_TYPE_ID " .
							"WHERE 1=1 " .
							"AND ACTIVE=1 " .
							"AND GA_APPROVED=1 " .
							"AND UD.DOC_ID='" . $doc_id . "' " .
							")Q1 ";
							
			$result_appx_cnt=mysqli_query($conn,$query_appx_cnt);
			
			while($row_appx_cnt=mysqli_fetch_array($result_appx_cnt))
			{
				$cnt_appx=$row_appx_cnt['CNT_APPX'];
			}
			
			//passzivalas, ha van rajta aktiv appendix
			if($ap==0 && $cnt_appx!=0)
			{
				$result_to_return=0;
				$msg_to_return="You can't passivate this document, because it has active appendixes! Please passivate the belonging appendixes first (" . $cnt_appx . ")!";
			}
			
			//passzivalas, ha nincs rajta aktiv appendix
			if($ap==0 && $cnt_appx==0)
			{
				$query_upd="UPDATE tUploadedDocs SET ACTIVE=" . $ap . ", MANUAL_AP=1 WHERE ID=" . $upl_id;
				$cnt=mysqli_query($conn,$query_upd);
				$result_to_return=mysqli_affected_rows($conn);
				$msg_to_return="Document " . $doc_id . " successfully passivated!";
			}
			
			//aktivalas, ha nem aktiv a dokumentum tipus
			if($ap==1 && $active_type==0)
			{
				$result_to_return=0;
				$msg_to_return="Document " . $doc_id . " can't be activated, because the document type is not active! Please, activate the document type first!";
			}

			//aktivalas, ha nem kezi passzivalas volt elotte
			if($ap==1 && $manual_ap==0)
			{
				$result_to_return=0;
				$msg_to_return="Document " . $doc_id . " can't be activated, because it was not passivated manually! Please, try to upload a new version of this document instead!";
			}
			
			
			//aktivalas, ha kezi passzivalas volt elotte
			if($ap==1 && $manual_ap==1)
			{
				$query_upd="UPDATE tUploadedDocs SET ACTIVE=" . $ap . ", MANUAL_AP=1 WHERE ID=" . $upl_id;
				$cnt=mysqli_query($conn,$query_upd);
				$result_to_return=mysqli_affected_rows($conn);
				$msg_to_return="Document " . $doc_id . " successfully activated!";
			}
			
		
		}
		
		History('tUploadedDocs', $upl_id, '-', $ap, 'M', 'Change ACTIVE status to ' . $ap . ': ' . $msg_to_return, 'DMS', $_SESSION['dms_username']);

		
		if(isset($result_to_return))
		{
			echo $msg_to_return;
		}
	}

?>