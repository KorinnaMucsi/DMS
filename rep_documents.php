<?php
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	require_once('reports/report_names.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	unset($_SESSION['where']);

	if(isset($_POST['run']))
	{
		if($_POST['sel_type']=='ALL')
		{
			$where='WHERE 1=1';
			$tp='ALL';
		}
		else
		{
			$where='WHERE DD.DESCR_ID=' . $_POST['sel_type'];
			
			$query_tp="SELECT DOC_DESCR FROM tDocDescr WHERE DESCR_ID=" . $_POST['sel_type'];
			$result_tp=mysqli_query($conn,$query_tp);
			while($row_tp=mysqli_fetch_array($result_tp))
			{
				$tp=$row_tp['DOC_DESCR'];
			}
			
		}
		
		$_SESSION['where']=$where;
	
		$query_rep_data="SELECT DOC_ID, DESCR, VRS_NO, DT_LAST_UPD, DD.DOC_DESCR, " . 
						"concat(A.L_NAME, ' ', A.F_NAME) AS APPROVER, DATE_FORMAT(DT_LAST_UPD,'%d.%m.%y') AS LAST_UPD " .
						"FROM tUploadedDocs UD " .
						"JOIN tDocTypes DT ON UD.TYPE_ID=DT.TYPE_ID " .
						"LEFT JOIN tUsrs A ON UD.GA_USR=A.USR_ID " .
						"LEFT JOIN tDocDescr DD ON DT.DOC_DESCR=DD.DESCR_ID " .
						$where . " " .
						"AND UD.ID>143 " .
						"AND GA_APPROVED=1 " .
						"AND UD.ACTIVE=1 " .
						"ORDER BY DOC_ID";
	
		$result_rep_data=mysqli_query($conn,$query_rep_data);		
		$cnt=mysqli_num_rows($result_rep_data);
		
		$rep_tbl=	'<table class="tblRep">' . "\n" .
					'<thead>' . "\n" .
					'<th>Doc. ID</th><th>Description</th><th>Type</th><th>Version number</th><th>Date of last<br>update</th>'. "\n" .
					'</thead>'.	"\n" . 
					'<tbody>' . "\n"; 
								
				
		while($row_rep_data=mysqli_fetch_array($result_rep_data))
		{
			if(strlen($row_rep_data['DESCR'])>=65)
			{
				$descr=substr($row_rep_data['DESCR'],0,65) . '...';
			}
			else
			{
				$descr=$row_rep_data['DESCR'];
			}
			
			
			$rep_tbl.= 	'<tr>' . "\n" .
						'<td><div class="leftrightPad">' . $row_rep_data['DOC_ID'] . '</div></td>' . "\n" .
						'<td><div class="leftPad">' . $descr . '</div></td>' . "\n" .
						'<td><div class="leftPad">' . $row_rep_data['DOC_DESCR']. '</div></td>' . "\n" .
						'<td><div class="leftPad">' . $row_rep_data['VRS_NO'] . '</div></td>' . "\n" .
						'<td><div class="leftrightPad">' . $row_rep_data['LAST_UPD'] . '</div></td>' . "\n" .
						'</tr>';
		}
				
		$rep_tbl.=	'</tbody>' . "\n" .
					'</table>';
					
		$rep=	'<span class="main_subtitle"><i><b>Approved active documents:</b></i></span>' . "\n" .
				'<div id="jqxgridRepDocs">' . "\n" .
				'</div>';				
	}
	else
	{
		$rep='';
		$cnt='';
		$rep_tbl='';
		$tp='';
	}
						
				
	//******************************************************************************************************************************************************************************
	//																					TYPE  COMBOBOX -->
	//******************************************************************************************************************************************************************************
	
	$query_type="SELECT * FROM tDocDescr WHERE ACTIVE=1 ORDER BY SORT ASC";
	$result_type=mysqli_query($conn,$query_type);	
	
	$opt_type=	'<select name="sel_type" required>' .
				'<option value="" selected="selected">--- Please, select ---</option>' .
				'<option value="ALL">ALL</option>';

	while($row_type=mysqli_fetch_array($result_type))
	{		
		$opt_type.='<option value="' . $row_type['DESCR_ID'] . '">' . $row_type['DOC_DESCR'] . '</option>';	
	}
	
	$opt_type.='</select>';	
	
	//******************************************************************************************************************************************************************************
	//																					<-- TYPE  COMBOBOX 
	//******************************************************************************************************************************************************************************
	
	$action='main.php?showRepDocs=True';
	
	$data=	'<div class="doc_hst_visible_on_form">' . "\n" .
			'<form id="frm" method="post" action="' . $action . '">' . "\n" . 
			'<label id="lbl_type" class="lbl" for="sel_type">Type:</label>' . "\n" .
			$opt_type . "\n" .
			'<hr class="main_hr" style="margin-top:1%;">' . "\n" .
			'<input type="submit" class="btn" value="Run" name="run"/>' . "\n" .
			'</form>' . "\n" .
			'<button class="btn" id="btnR" onclick="Javascript:document.location.href=\'main.php?showMainMenu=True\';">Return to main menu</button>' . "\n" .
			'<hr class="main_hr" style="margin-bottom:1%;">' . "\n" .
			$rep . "\n" .
			'</div>';
			
	
	$data.=	'<div class="doc_hst_visible_on_rep">' . "\n" .
			'<div class="logoPrint">' . "\n" .
			'<img id="img_logo" alt="logo" src="img/alltechRep.jpg"><br>' . "\n" .
			'</div>' . "\n" .
			'<hr>' . "\n" .
			'<br>' . "\n" .
			'<span class="hdrRep">' . ASR0001 . '<br></span>' . "\n" .
			'<span class="main_subtitle">Type: ' . $tp . ' </span>' . "\n" .
			'<span class="main_subtitle">Total number: ' . $cnt . ' </span>' . "\n" .
			'<br><br>' . "\n" .
			'<hr>' . "\n" .
			$rep_tbl . "\n" .
			'</div>';

	$rep_docs='<div class="main_mainContainerDiv">' . "\n" .
				'<div class="main_containerDiv">' . "\n" .
				'<div class="main_titleDiv">' . "\n" .
				'<span class="main_title">List of the<br>approved documents</span>' . "\n" .
				'<span class="main_titleDiv_sp"><img src="img/documents.png" alt="Documents"></span>' . "\n" .
				'<span class="main_subtitle">You can find the list of the approved active documents in the table below.<br>' . "\n" .
				'Please, set the parameters and click on the \'Run\' button.<br>' . "\n" .
				'You can print the report by navigating to <b>File-->Print...</b> or <b>File-->Print preview...</b> in the browser\'s Menu bar' . "\n" .
				'</span>' . "\n" .
				'<hr>' . "\n" .
				'</div>' . "\n" . 
				'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
				$data . "\n" .
				'</div>' . "\n" .
				'</div>' . "\n" .
				'</div>';
?>