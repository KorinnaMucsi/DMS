<?php

	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');

	$upload='-';
	$dapproval='-';
	$gapproval='-';
	
	$data=	'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
			'<span class="main_subtitle"><i><b>Uploaded, approved documents:</b></i></span>' . "\n" .
			'<div class="bck_btn">' . "\n" .
			'<input type="button" id="bck_btn" name="all_docs" value="Show all unread documents/appendixes" onclick="Javascript:window.location=\'main.php?showNVDocuments=True\';">' . "\n" .
			'</div>' . "\n" .
			'<div id="jqxgridDocs">' . "\n" .
			'</div>' . "\n" .
			'</div>' . "\n" .
			'<div class="main_dataDiv" id="main_dataDiv_p">' . "\n" .
			'<span class="main_subtitle"><i><b>Appendix(es) for the selected document:</b></i></span>' . "\n" .
			'<div id="jqxgridPrilog">' . "\n" .
			'</div>' . "\n" .
			'</div>';

	if(isset($_GET['showHst']) && $_GET['showHst']=='True')
	{
		if(isset($_GET['doc_id']))
		{
			
			$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
			$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
			mysqli_set_charset($conn,"utf8");

			
//DOCUMENT DESCRIPTION INFO -->

			$query_doc_descr=	"SELECT CASE WHEN UD.DOC_DESCR='' " .
								"THEN DT.DESCR " .
								"ELSE UD.DOC_DESCR " .
								"END AS DOC_DESCR " .
								"FROM tUploadedDocs UD " .
								"JOIN " .
								"( " .
								"SELECT TYPE_ID, DESCR " .
								"FROM " .
								"tDocTypes " . 
								"UNION " .
								"SELECT TYPE_ID, DESCR " .
								"FROM tPDocTypes " .
								") DT ON UD.TYPE_ID=DT.TYPE_ID " .
								"WHERE 1=1 " .
								"AND DOC_ID='" . $_GET['doc_id'] . "'";
								
			$result_doc_descr=mysqli_query($conn, $query_doc_descr);
			
			while($row_doc_descr=mysqli_fetch_array($result_doc_descr))
			{
				$doc_descr_info=$row_doc_descr['DOC_DESCR'];
			}

//UPLOAD INFO -->			
			$query_upload=	"SELECT DOC_ID, U_USR, concat(U.L_NAME, ' ', U.F_NAME) AS USR_DTL, DATE_FORMAT(U_DT,'%d.%m.%y %H:%i') AS DT, 'New document uploaded' AS DESCR " .
							"FROM tUploadedDocs UD " .
							"JOIN tUsrs U ON UD.U_USR=U.USR_ID " .
							"WHERE DOC_ID='" . $_GET['doc_id'] . "'";
							
			$result_upload=mysqli_query($conn, $query_upload);
			
			while($row_upload=mysqli_fetch_array($result_upload))
			{
				$upload=$row_upload['USR_DTL'] . ' at: ' . $row_upload['DT'];
			}
			
//DOCUMENT APPROVAL INFO -->
			$query_dapproval="SELECT DOC_ID, A_USR, concat(U.L_NAME, ' ', U.F_NAME) AS USR_DTL, DATE_FORMAT(A_DT,'%d.%m.%y %H:%i') AS DT, 'Document approved (DA)' AS DESCR " .
							"FROM tUploadedDocs UD " .
							"JOIN tUsrs U ON UD.A_USR=U.USR_ID " .
							"WHERE DOC_ID='" . $_GET['doc_id'] . "'";
							
			$result_dapproval=mysqli_query($conn, $query_dapproval);
			
			while($row_dapproval=mysqli_fetch_array($result_dapproval))
			{
				$dapproval=$row_dapproval['USR_DTL'] . ' at: ' . $row_dapproval['DT'];
			}

//GENERAL APPROVAL INFO -->
			$query_gapproval="SELECT DOC_ID, GA_USR, concat(U.L_NAME, ' ', U.F_NAME) AS USR_DTL, DATE_FORMAT(GA_DT,'%d.%m.%y %H:%i') AS DT, 'Document approved (GA)' AS DESCR " .
							"FROM tUploadedDocs UD " .
							"JOIN tUsrs U ON UD.GA_USR=U.USR_ID " .
							"WHERE DOC_ID='" . $_GET['doc_id'] . "'";
							
			$result_gapproval=mysqli_query($conn, $query_gapproval);
			
			while($row_gapproval=mysqli_fetch_array($result_gapproval))
			{
				$gapproval=$row_gapproval['USR_DTL'] . ' at: ' . $row_gapproval['DT'];
			}
			
			$query_rep_data="SELECT concat(U.L_NAME, ' ' , U.F_NAME) AS USR_INFO, GRP_LST, " .
							"CASE WHEN DATE_FORMAT(EVENT_ACKN_DT,'%d.%m.%y %H:%i')=0 " .
							"THEN '' " .
							"ELSE DATE_FORMAT(EVENT_ACKN_DT,'%d.%m.%y %H:%i') " .
							"END AS DT, " .
							"CASE WHEN DATE_FORMAT(VIEW_DT,'%d.%m.%y %H:%i')=0 " .
							"THEN '' " .
							"ELSE DATE_FORMAT(VIEW_DT,'%d.%m.%y %H:%i') " .
							"END AS VIEW_DT, " .
							"CASE WHEN GRP_LST='-' " .
							"THEN 'z' " .
							"ELSE GRP_LST " .
							"END AS GRP_LST_SORT, "	.
							"EVENT_DESCR AS DESCR " .
							"FROM tDocDistribHst DH " .
							"JOIN tUploadedDocs UD ON DH.U_DOC_ID=UD.ID " .
							"JOIN tUsrs U ON DH.USR_ID=U.USR_ID " .
							"WHERE 1=1 " .
							"AND PLACE_ON_HST=1 " .
							"GROUP BY DOC_ID, USR_INFO, EVENT_ACKN_DT " .
							"HAVING DOC_ID='" . $_GET['doc_id'] . "' " .
							"ORDER BY GRP_LST_SORT, USR_INFO";

			$result_rep_data=mysqli_query($conn,$query_rep_data);
			
			$rep_tbl=	'<table class="tblRep">' . "\n" .
						'<thead>' . "\n" .
						'<th>User</th><th>Document<br>viewed at</th><th>Group</th>'. "\n" .
						'</thead>'.	"\n" . 
						'<tbody>' . "\n"; 
							
			
			while($row_rep_data=mysqli_fetch_array($result_rep_data))
			{
				$rep_tbl.= 	'<tr>' . "\n" .
							'<td>' . $row_rep_data['USR_INFO'] . '</td>' . "\n" .
							'<td>' . $row_rep_data['VIEW_DT'] . '</td>' . "\n" .
							'<td>' . $row_rep_data['GRP_LST'] . '</td>' . "\n" .
							'</tr>';
			}
			
			$rep_tbl.=	'</tbody>' . "\n" .
						'</table>';

							
			$data=	'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
					'<div class="doc_hst_visible_on_form">' . "\n" .
					'<span class="main_subtitle"><i><b>History of the document: ' . $_GET['doc_id'] . ' - ' . $doc_descr_info . '</b></i></span>' . "\n" .
					'<ul>' . "\n" .
					'<li><span class="main_subtitle"><i><b>Upload: </b></i>' . $upload . '</span></li>' . "\n" .
					'<li><span class="main_subtitle"><i><b>Document Approval: </b></i>' . $dapproval . '</span></li>' . "\n" .
					'<li><span class="main_subtitle"><i><b>General Approval: </b></i>' . $gapproval . '</span></li>' . "\n" .
					'<li><span class="main_subtitle"><i><b>Distribution list (See below):</b></i></span></li>' . "\n" .
					'<div class="bck_btn">' . "\n" .
					'<input type="button" id="bck_btn" name="back" value="Back to the documents" onclick="Javascript:window.location=\'main.php?showDocuments=True\';">' . "\n" .
					'</div>' . "\n" .
					'<div id="jqxgridHst">' . "\n" .
					'</div>' . "\n" .
					'</ul>' . "\n" .
					'</div>' . "\n" .
					'</div>';
					
			$data.=	'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
					'<div class="doc_hst_visible_on_rep">' . "\n" .
					'<div class="logoPrint">' . "\n" .
					'<img id="img_logo" alt="logo" src="img/alltechRep.jpg"><br>' . "\n" .
					'</div>' . "\n" .
					'<hr>' . "\n" .
					'<br>' . "\n" .
					'<span class="hdrRep">History of the document: ' . $_GET['doc_id'] . '</span><br><span class="params"><b>Type:</b> ' . $doc_descr_info . '</span>' . "\n" .
					'<br><br>' . "\n" .
					'<hr>' . "\n" .
					'<span class="main_subtitle"><i><b>Upload: </b></i>' . $upload . '</span>' . "\n" .
					'<span class="main_subtitle"><i><b>Document Approval: </b></i>' . $dapproval . '</span>' . "\n" .
					'<span class="main_subtitle"><i><b>General Approval: </b></i>' . $gapproval . '</span>' . "\n" .
					'<span class="main_subtitle"><i><b>Distribution list (See below):</b></i></span>' . "\n" .
					$rep_tbl . "\n" .
					'</div>' . "\n" .
					'</div>';
		}
	}

	$documents='<div class="main_mainContainerDiv">' . "\n" .
				'<div class="main_containerDiv">' . "\n" .
				'<div class="main_titleDiv">' . "\n" .
				'<span class="main_title">List of the<br>uploaded, approved documents</span>' . "\n" .
				'<span class="main_titleDiv_sp"><img src="img/documents.png" alt="Documents"></span>' . "\n" .
				'<span class="main_subtitle">You can find the list of the uploaded, approved and active documents in the table below.<br>' . "\n" .
				'Please, click on the \'View\' link beside the document\'s name to open the document!</span>' . "\n" .
				'<hr>' . "\n" .
				'</div>' . "\n" . 
				$data . "\n" .
				'</div>' . "\n" .
				'</div>';
?>