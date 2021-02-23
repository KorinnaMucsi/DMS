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
			$wh='WHERE 1=1';
			$tp='ALL';
		}
		else
		{
			$wh='WHERE DESCR_ID=' . $_POST['sel_type'];
			
			$query_tp="SELECT DOC_DESCR FROM tDocDescr WHERE DESCR_ID=" . $_POST['sel_type'];
			$result_tp=mysqli_query($conn,$query_tp);
			while($row_tp=mysqli_fetch_array($result_tp))
			{
				$tp=$row_tp['DOC_DESCR'];
			}
			
		}
		if($_POST['sel_act']=='ALL')
		{
			$wh.='';
			$act="ALL";
		}
		if($_POST['sel_act']=='0')
		{
			$wh.=' AND DT.ACTIVE=0 ';
			$act="Only passive doc. types";
		}
		if($_POST['sel_act']=='1')
		{
			$wh.=' AND DT.ACTIVE=1 ';
			$act="Only active doc.types";
		}
		
		$_SESSION['where']=$wh;
		
	
		$query_rep_data="SELECT DT.TYPE_ID, " . 
						"CASE WHEN DT.ACTIVE=1 THEN 'Yes' ELSE '-' END AS DT_ACT, " .
						"CASE WHEN DOC_ID IS NULL THEN '-' ELSE DOC_ID END AS DOC_ID, DESCR, " . 
						"CASE WHEN VRS_NO IS NULL THEN '-' ELSE VRS_NO END AS VRS_NO, " .
						"CASE WHEN DT_LAST_UPD IS NULL THEN '-' ELSE DT_LAST_UPD END AS DT_LAST_UPD, DD.DOC_DESCR, " . 
						"CASE WHEN concat(Q.L_NAME, ' ', Q.F_NAME) IS NULL THEN '-' ELSE concat(Q.L_NAME, ' ', Q.F_NAME) END AS APPROVER, " . 
						"CASE WHEN DATE_FORMAT(DT_LAST_UPD,'%d.%m.%y') IS NULL THEN '-' ELSE DATE_FORMAT(DT_LAST_UPD,'%d.%m.%y') END AS LAST_UPD " .
						"FROM tDocTypes DT " .
						"LEFT JOIN " .
						"( " .
						"SELECT UD.*, A.F_NAME, A.L_NAME " .
						"FROM " .
						"tUploadedDocs UD " .
						"LEFT JOIN tUsrs A ON UD.GA_USR=A.USR_ID " .
						"WHERE 1=1 " .
						"AND UD.ID>143 " .
						"AND GA_APPROVED=1 " .
						"AND UD.ACTIVE=1 " .
						")Q ON DT.TYPE_ID=Q.TYPE_ID " .
						"LEFT JOIN tDocDescr DD ON DT.DOC_DESCR=DD.DESCR_ID " .
						$wh . " " .
						"ORDER BY TYPE_ID ";

		$result_rep_data=mysqli_query($conn,$query_rep_data);		
		$cnt=mysqli_num_rows($result_rep_data);
		
		$rep_tbl=	'<table class="tblRep">' . "\n" .
					'<thead>' . "\n" .
					'<th>Doc. type</th><th>Active<br>Doc. type?</th><th>Doc. ID</th><th>Description</th><th>Type</th>'. "\n" .
					'</thead>'.	"\n" . 
					'<tbody>' . "\n"; 
								
				
		while($row_rep_data=mysqli_fetch_array($result_rep_data))
		{
			if(strlen($row_rep_data['DESCR'])>=60)
			{
				$descr=substr($row_rep_data['DESCR'],0,60) . '...';
			}
			else
			{
				$descr=$row_rep_data['DESCR'];
			}
			
			
			$rep_tbl.= 	'<tr>' . "\n" .
						'<td><div class="leftrightPad">' . $row_rep_data['TYPE_ID'] . '</div></td>' . "\n" .
						'<td><div class="leftrightPad">' . $row_rep_data['DT_ACT'] . '</div></td>' . "\n" .
						'<td><div class="leftrightPad">' . $row_rep_data['DOC_ID'] . '</div></td>' . "\n" .
						'<td><div class="leftPad">' . $descr . '</div></td>' . "\n" .
						'<td><div class="leftPad">' . $row_rep_data['DOC_DESCR']. '</div></td>' . "\n" .
						'</tr>';
		}
				
		$rep_tbl.=	'</tbody>' . "\n" .
					'</table>';
					
		$rep=	'<span class="main_subtitle"><i><b>Document types:</b></i></span>' . "\n" .
				'<div id="jqxgridRepDTP">' . "\n" .
				'</div>';				
	}
	else
	{
		$rep='';
		$cnt='';
		$rep_tbl='';
		$tp='';
		$act='';
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
	//******************************************************************************************************************************************************************************
	//																					ACT  COMBOBOX -->
	//******************************************************************************************************************************************************************************
	
	$opt_act=	'<select name="sel_act" required>' .
				'<option value="" selected="selected">--- Please, select ---</option>' .
				'<option value="ALL">ALL</option>' .
				'<option value="1">Active</option>' .
				'<option value="0">Passive</option>' .
				'</select>';
	
	
	//******************************************************************************************************************************************************************************
	//																					<-- ACT  COMBOBOX 
	//******************************************************************************************************************************************************************************
	
	$action='main.php?showRepDTP=True';
	
	$data=	'<div class="doc_hst_visible_on_form">' . "\n" .
			'<form id="frm" method="post" action="' . $action . '">' . "\n" . 
			'<div class="upload_fline">' . "\n" .
			'<label id="lbl_type" class="lbl" for="sel_type">Type:</label>' . "\n" .
			$opt_type . "\n" .
			'</div>' . "\n" .
			'<div class="upload_nline">' . "\n" .
 			'<label id="lbl_type" class="lbl" for="sel_type">Doc. type status:</label>' . "\n" .
			$opt_act . "\n" .
			'</div>' . "\n" .
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
			'<span class="hdrRep">' . ASR0003 . '<br></span>' . "\n" .
			'<span class="main_subtitle">Type: ' . $tp . ' </span>' . "\n" .
			'<span class="main_subtitle">Doc. type status: ' . $act . ' </span>' . "\n" .
			'<span class="main_subtitle">Total number: ' . $cnt . ' </span>' . "\n" .
			'<br><br>' . "\n" .
			'<hr>' . "\n" .
			$rep_tbl . "\n" .
			'</div>';

	$rep_dtp='<div class="main_mainContainerDiv">' . "\n" .
				'<div class="main_containerDiv">' . "\n" .
				'<div class="main_titleDiv">' . "\n" .
				'<span class="main_title">List of the<br>document types</span>' . "\n" .
				'<span class="main_titleDiv_sp"><img src="img/documents.png" alt="Document Types"></span>' . "\n" .
				'<span class="main_subtitle">You can find the list of all the document types in the table below.<br>' . "\n" .
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