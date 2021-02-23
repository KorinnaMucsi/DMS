<?php
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	require_once('reports/report_names.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	unset($_SESSION['where_dpt']);
	unset($_SESSION['where_ws']);
	unset($_SESSION['chklist']);
	
	if(isset($_POST['run']))
	{
		if($_POST['sel_dpt']=='ALL')
		{
			$where_dpt='';
			$where_ws='';
			$dpt='ALL';
			$ws='ALL';
		}
		else
		{			
			$where_dpt='AND DPT_ID=' . $_POST['sel_dpt'];
			
			//Kikeressuk a department id-hoz tartozo leirast, hogy a jelentesen azt jelenitsuk meg a parameterek fejlecben
			$query_dpt="SELECT DESCR FROM tDpts WHERE ID=" . $_POST['sel_dpt'];
			$result_dpt=mysqli_query($conn,$query_dpt);
			
			while($row_dpt=mysqli_fetch_array($result_dpt))
			{
				$dpt=$row_dpt['DESCR'];
			}
			
			if($_POST['sel_ws']=='ALL')
			{
				$where_ws='';
				$ws='ALL';
			}
			else
			{
				$where_ws='AND WS_ID=\'' . $_POST['sel_ws'] . '\'';
				
				//Kikeressuk a workstation id-hoz tartozo leirast, hogy a jelentesen azt jelenitsuk meg a parameterek fejlecben
				$query_ws="SELECT DESCR FROM tWrkStations WHERE WS_ID='" . $_POST['sel_ws'] . "'";
				$result_ws=mysqli_query($conn,$query_ws);
				
				while($row_ws=mysqli_fetch_array($result_ws))
				{
					$ws=$row_ws['DESCR'];
				}				
			}					
		}
		
		$_SESSION['where_dpt']=$where_dpt;
		$_SESSION['where_ws']=$where_ws;
		
		if(isset($_POST['chklist']))
		{
			$query_rep_data="SELECT * " .
							"FROM " .
							"( " .
							"SELECT R.ID AS ROLE_ID, CONCAT(U.L_NAME,' ',U.F_NAME) AS FULL_NAME, F.HR_FUNCTION, FROM_HR, " .
							"CASE WHEN PLACE_ON_REP=0 THEN " .
							"	2 " .
							"ELSE " .
							"	CASE FROM_HR WHEN 1 THEN 0 ELSE 1 END " .
							"END AS FN_TYPE_ID, " .
							"CASE WHEN PLACE_ON_REP=0 THEN " .
							"	'Access granted to WS' " .
							"ELSE " .
							"	CASE FROM_HR WHEN 1 THEN 'Permanent' ELSE 'Temporary' END " .
							"END AS FN_TYPE, WSF.PLACE_ON_REP, " .
							"CASE WHEN WS.DPT_ID IS NULL THEN DP.ID ELSE WS.DPT_ID END AS DPT_ID, " .
							"CASE WHEN D.DESCR IS NULL THEN DP.DESCR ELSE D.DESCR END AS DEPARTMENT, " .
							"CASE WHEN WS.WS_ID IS NULL THEN 'ALL' ELSE WS.WS_ID END AS WS_ID, WS.DESCR AS WORKSTATION " .
							"FROM tUsrRoles R " .
							"JOIN tUsrs U ON R.USR_ID=U.USR_ID " .
							"JOIN tHRFunctions F ON R.HR_FUNC_ID=F.ID " .
							"LEFT JOIN tWrkStationsFunctions WSF ON F.ID=WSF.HR_FUNC_ID " .
							"LEFT JOIN tWrkStations WS ON WSF.WS_ID=WS.WS_ID " .
							"LEFT JOIN tDpts D ON WS.DPT_ID=D.ID " .
							"LEFT JOIN tDpts DP ON F.DPT_ID=DP.ID " .
							"WHERE 1=1 " .
							"AND U.ACTIVE=1 " .
							"AND U.USR_ID<>'Administrator' " .
							")Q " .
							"WHERE 1=1 " .
							$where_dpt . " " .
							$where_ws . " " .
							"ORDER BY DEPARTMENT, WORKSTATION, FN_TYPE_ID, HR_FUNCTION, FULL_NAME ";
			$_SESSION['chklist']=1;
		}
		else
		{				
			$query_rep_data="SELECT * " .
							"FROM " .
							"( " .
							"SELECT R.ID AS ROLE_ID, CONCAT(U.L_NAME,' ',U.F_NAME) AS FULL_NAME, F.HR_FUNCTION, FROM_HR, " .
							"CASE FROM_HR WHEN 1 THEN 'Permanent' ELSE 'Temporary<span style=\"font-weight:bold;\">*</span>' END AS FN_TYPE, " .
							"WS.DPT_ID AS DPT_ID, " .
							"D.DESCR AS DEPARTMENT, " .
							"WS.WS_ID, WS.DESCR AS WORKSTATION " .
							"FROM tUsrRoles R " .
							"JOIN tUsrs U ON R.USR_ID=U.USR_ID " .
							"JOIN tHRFunctions F ON R.HR_FUNC_ID=F.ID " .
							"JOIN tWrkStationsFunctions WSF ON F.ID=WSF.HR_FUNC_ID " .
							"JOIN tWrkStations WS ON WSF.WS_ID=WS.WS_ID " .
							"JOIN tDpts D ON WS.DPT_ID=D.ID " .
							"WHERE 1=1 " .
							"AND U.ACTIVE=1 " .
							"AND U.USR_ID<>'Administrator' " .
							"AND WSF.PLACE_ON_REP=1 " .
							")Q " .
							"WHERE 1=1 " .
							$where_dpt . " " .
							$where_ws . " " .
							"ORDER BY DEPARTMENT, WORKSTATION, FN_TYPE, HR_FUNCTION, FULL_NAME";
			$_SESSION['chklist']=0;
		}

		$result_rep_data=mysqli_query($conn,$query_rep_data);		
		
		$rep_tbl=	'<table class="tblRep">' . "\n" .
					'<thead>' . "\n" .
					'<th>Department</th><th>Workstation</th><th>Name</th><th>Job title</th><th>Type</th>'. "\n" .
					'</thead>'.	"\n" . 
					'<tbody>' . "\n"; 
								
		while($row_rep_data=mysqli_fetch_array($result_rep_data))
		{
			
			//Meg kell oldani, hogy ha valtozik a ws a dpt-n belul, akkor egy vonallal valasztodjon el a sor!!!			
			$rep_ws_new=$row_rep_data['WORKSTATION'];
			
			if($rep_ws_new!=$rep_ws_old)
			{
				$rep_tbl.='<tr><td class="underline" colspan="5"></td></tr>' . "\n";
			}
			
			$rep_tbl.= 	'<tr>' . "\n" .
						'<td><div class="leftPad">' . $row_rep_data['DEPARTMENT']. '</div></td>' . "\n" .
						'<td><div class="leftPad">' . $row_rep_data['WORKSTATION']. '</div></td>' . "\n" .
						'<td><div class="leftPad">' . $row_rep_data['FULL_NAME']. '</div></td>' . "\n" .
						'<td><div class="leftPad">' . $row_rep_data['HR_FUNCTION'] . '</div></td>' . "\n" .
						'<td><div class="leftPad">' . $row_rep_data['FN_TYPE'] . '</div></td>' . "\n" .
						'</tr>';
			
			
			$rep_ws_old=$row_rep_data['WORKSTATION'];		
		}
				
		$rep_tbl.=	'</tbody>' . "\n" .
					'</table>';
					
		$rep=	'<span id="job_titles" class="main_subtitle"><i><b>Users with job titles:</b></i></span>' . "\n" .
				'<div id="jqxgridRepUsrsJobs">' . "\n" .
				'</div>';				
	}
	else
	{
		$rep='';
		$rep_tbl='';
		$dpt='';
	}
						
				
	//******************************************************************************************************************************************************************************
	//																					DEPARTMENT COMBOBOX -->
	//******************************************************************************************************************************************************************************
	
	//A kivalasztott department kombotol fuggoen fog megjelenni a Workstation kombo, amit a resports.js fajl $("#sel_dpt").change(function() funkcioja fog kirajzolni
	$query_dpt=	"SELECT DPT_ID, DEPARTMENT " .
				"FROM " .
				"( " .
				"SELECT D.ID AS DPT_ID, HRFN.HR_FUNCTION AS 'JOB_TITLE', WS.DESCR AS WORKSTATION, D.DESCR AS DEPARTMENT " .
				"FROM tHRFunctions HRFN " .
				"LEFT JOIN tWrkStationsFunctions WSFN ON HRFN.ID=WSFN.HR_FUNC_ID " .
				"LEFT JOIN tWrkStations WS ON WS.WS_ID=WSFN.WS_ID " .
				"LEFT JOIN tDpts D ON HRFN.DPT_ID=D.ID " .
				")Q " .
				"GROUP BY Department " .
				"ORDER BY Department";
				
	$result_dpt=mysqli_query($conn,$query_dpt);	
	
	$opt_dpt=	'<select name="sel_dpt" id="sel_dpt" class="sel" required>' . "\n" .
				'<option value="" selected="selected">--- Please, select ---</option>' . "\n" .
				'<option value="ALL">ALL</option>';

	while($row_dpt=mysqli_fetch_array($result_dpt))
	{		
		$opt_dpt.='<option value="' . $row_dpt['DPT_ID'] . '">' . $row_dpt['DEPARTMENT'] . '</option>';	
	}
	
	$opt_dpt.='</select>';	
	
	//******************************************************************************************************************************************************************************
	//																					<-- DEPARTMENT COMBOBOX 
	//******************************************************************************************************************************************************************************
	
	

	$action='main.php?showRepUsrsJobs=True';
	
	$data=	'<div class="doc_hst_visible_on_form">' . "\n" .
			'<form id="frm" method="post" action="' . $action . '">' . "\n" . 
			'<label id="lbl_dpt" class="lbl" for="sel_dpt">Department:</label>' . "\n" .
			$opt_dpt . "\n" .
			'<span id="ws_lbl"><label id="lbl_ws" class="lbl" for="sel_ws">Workstation:</label></span>' . "\n" .
			'<span id="ws">' . "\n" .
			'</span>' . "\n" .
			'<hr class="main_hr" style="margin-top:1%;">' . "\n" .
			'<input type="submit" class="btn" value="Run" name="run"/>' . "\n" .
			'</form>' . "\n" .
			'<button class="btn" id="btnR" onclick="Javascript:document.location.href=\'main.php?showReports=True\';">Return to reports</button>' . "\n" .
			'<hr class="main_hr" style="margin-bottom:1%;">' . "\n" .
			$rep . "\n" .
			'</div>';
			
	
	$data.=	'<div class="doc_hst_visible_on_rep">' . "\n" .
			'<hr>' . "\n" .
			'<br>' . "\n" .
			'<span class="hdrRep">' . ASR0002 . '<br></span>' . "\n" .
			'<span class="main_subtitle">Department: ' . $dpt . ' </span>' . "\n" .
			'<span class="main_subtitle">Workstation: ' . $ws . ' </span>' . "\n" .
			'<br><br>' . "\n" .
			'<hr>' . "\n" .
			$rep_tbl . "\n" .
			'</div>';

	$rep_ujt=	'<div class="main_mainContainerDiv">' . "\n" .
				'<div class="main_containerDiv">' . "\n" .
				'<div class="main_titleDiv">' . "\n" .
				'<span class="main_title">List of the<br>users and their job titles</span>' . "\n" .
				'<span class="main_titleDiv_sp"><img src="img/documents.png" alt="Users-job titles"></span>' . "\n" .
				'<span class="main_subtitle">You can find the list of the active users with their currently assigned permanent and temporary jobs.<br>' . "\n" .
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