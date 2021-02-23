<?php
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	//Athozzuk a formon beallitott parametereket a szessziok segitsegevel
	if(isset($_SESSION['where_dpt']))
	{
		$where_dpt=$_SESSION['where_dpt'];
	}	
	else
	{
		$where_dpt='';
	}
	
	if(isset($_SESSION['where_ws']))
	{
		$where_ws=$_SESSION['where_ws'];
	}	
	else
	{
		$where_ws='';
	}
	
	if(isset($_SESSION['chklist']))
	{
		$chklist=$_SESSION['chklist'];
	}	
	else
	{
		$chklist='';
	}
	
	if($chklist==1)
	{
		$query="SELECT * " .
				"FROM " .
				"( " .
				"SELECT * " .
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
				"WS.WS_ID, WS.DESCR AS WORKSTATION " .
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
				")Q1 " .
				"WHERE 1=1 " .
				$where_dpt . " " .
				$where_ws . " " .
				")Q " .
				"WHERE 1=1 ";
	}
	else
	{				
		$query=	"SELECT * " .
				"FROM " .
				"( " .
				"SELECT * " .
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
				")Q1 " .
				"WHERE 1=1 " .
				$where_dpt . " " .
				$where_ws . " " .
				")Q " .
				"WHERE 1=1 ";
	}

//FILTER THE DATA -->

	if (isset($_GET['filterscount']))
	{
		$filterscount = $_GET['filterscount'];
		if ($filterscount > 0)
		{
			$where = " AND (";
			$tmpdatafield = "";
			$tmpfilteroperator = "";

			for ($i=0; $i < $filterscount; $i++)
		    {
				// get the filter's value.
				$filtervalue = $_GET["filtervalue" . $i];
				// get the filter's condition.
				$filtercondition = $_GET["filtercondition" . $i];
				// get the filter's column.
				$filterdatafield = $_GET["filterdatafield" . $i];
				// get the filter's operator.
				$filteroperator = $_GET["filteroperator" . $i];

				if ($tmpdatafield == "")
				{
					$tmpdatafield = $filterdatafield;			
				}
				else if ($tmpdatafield <> $filterdatafield)
				{
					$where .= ")AND(";
				}
				else if ($tmpdatafield == $filterdatafield)
				{
					if ($tmpfilteroperator == 0)
					{
						$where .= " AND ";
					}
					else $where .= " OR ";	
				}

				// build the "WHERE" clause depending on the filter's condition, value and datafield.
	        	switch($filtercondition)
				{
					case "NOT_EMPTY":
					case "NOT_NULL":
						$where .= " " . $filterdatafield . " NOT LIKE '" . "" ."'";
						break;
					case "EMPTY":
					case "NULL":
						$where .= " " . $filterdatafield . " LIKE '" . "" ."'";
						break;
					case "CONTAINS_CASE_SENSITIVE":
						$where .= " BINARY  " . $filterdatafield . " LIKE '%" . $filtervalue ."%'";
						break;
					case "CONTAINS":
						$where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."%'";
						break;
					case "DOES_NOT_CONTAIN_CASE_SENSITIVE":
						$where .= " BINARY " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";
						break;
					case "DOES_NOT_CONTAIN":
						$where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";
						break;
					case "EQUAL_CASE_SENSITIVE":
						$where .= " BINARY " . $filterdatafield . " = '" . $filtervalue ."'";
						break;
					case "EQUAL":
						$where .= " " . $filterdatafield . " = '" . $filtervalue ."'";
						break;
					case "NOT_EQUAL_CASE_SENSITIVE":
						$where .= " BINARY " . $filterdatafield . " <> '" . $filtervalue ."'";
						break;
					case "NOT_EQUAL":
						$where .= " " . $filterdatafield . " <> '" . $filtervalue ."'";
						break;
					case "GREATER_THAN":
						$where .= " " . $filterdatafield . " > '" . $filtervalue ."'";
						break;
					case "LESS_THAN":
						$where .= " " . $filterdatafield . " < '" . $filtervalue ."'";
						break;
					case "GREATER_THAN_OR_EQUAL":
						$where .= " " . $filterdatafield . " >= '" . $filtervalue ."'";
						break;
					case "LESS_THAN_OR_EQUAL":
						$where .= " " . $filterdatafield . " <= '" . $filtervalue ."'";
						break;
					case "STARTS_WITH_CASE_SENSITIVE":
						$where .= " BINARY " . $filterdatafield . " LIKE '" . $filtervalue ."%'";
						break;
					case "STARTS_WITH":
						$where .= " " . $filterdatafield . " LIKE '" . $filtervalue ."%'";
						break;
					case "ENDS_WITH_CASE_SENSITIVE":
						$where .= " BINARY " . $filterdatafield . " LIKE '%" . $filtervalue ."'";
						break;
					case "ENDS_WITH":
						$where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."'";
						break;
				}
				if ($i == $filterscount - 1)
				{
					$where .= ")";
				}

				$tmpfilteroperator = $filteroperator;
				$tmpdatafield = $filterdatafield;			
			}

			// build the query.
			$query = $query . $where;
		}
	}

//SORT THE DATA -->
	
	if (isset($_GET['sortdatafield']))	
	{	
		$sortfield = $_GET['sortdatafield'];	
		$sortorder = $_GET['sortorder'];			
	
		if ($sortfield != NULL)	
		{	
			//Abban az esetben, ha lekerjuk a teljes listat, amely tartalmaz mindenkit, akinek hozzaferese van a munkaallomasokhoz, akkor a szortolas nem a FN_TYPE, 
			//hanem a FN_TYPE_ID szerint kell, hogy menjen
			if($chklist==1)
			{
				if($sortfield=='FN_TYPE')
				{
					$sortfield='FN_TYPE_ID';
				}
			}
			if ($sortorder == "desc")	
			{	
				$query = $query . " ORDER BY " . $sortfield . " DESC";	
			}	
			else if ($sortorder == "asc")	
			{	
				$query = $query . " ORDER BY " . $sortfield . " ASC";	
			}				
		}
	}
	else //Ez a default szortolas, ha nincs a griden semmilyen szortolas kivalasztva
	{
		if($chklist==1)
		{
			$query = $query . " ORDER BY DEPARTMENT, WORKSTATION, FN_TYPE_ID, HR_FUNCTION, FULL_NAME ";	
		}
		else
		{
			$query = $query . " ORDER BY DEPARTMENT, WORKSTATION, FN_TYPE, HR_FUNCTION, FULL_NAME ";
		}
	}
	
	$result=mysqli_query($conn,$query);
	
	$rep_documents=array();
	
	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
	{
	
		//Betoltjuk egy array-be a query-bol kapott adatokat es tovabbitjuk a jquery-nek
		$rep_documents[] = 	array
							(
								'DEPARTMENT' => $row['DEPARTMENT'],
								'WORKSTATION' => $row['WORKSTATION'],
								'FULL_NAME' => $row['FULL_NAME'],
								'HR_FUNCTION' => $row['HR_FUNCTION'],
								'FN_TYPE' => $row['FN_TYPE']
							);
	}	
	echo json_encode($rep_documents);
?>
