<?php

//**************************************************************************TABLE WITH THE DOCUMENTS (CONCERNING USERS' RIGHTS)**********************************************************************
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


//QUERY FOR THE DOCUMENTS MAIN FORM -->

	$query=	"SELECT concat(U.L_NAME, ' ' , U.F_NAME) AS USR_INFO, GRP_LST, " .
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
			"AND U.ACTIVE=1 " .
			"AND U.USR_ID<>'administrator' " .
			//"WHERE DOC_ID='" . $_SESSION['doc_id'] . "' " .
			"GROUP BY DOC_ID, USR_INFO, EVENT_ACKN_DT " .
			"HAVING DOC_ID='" . $_SESSION['doc_id'] . "' ";
			//"ORDER BY EVENT_ACKN_DT"; Ki kell venni a szortolast, mert a where vagy having utan nem allhat semmi, akkor nem fog mukodni a grid szort...

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
		
			if ($sortfield == "GRP_LST")
			{
				$sortfield="GRP_LST_SORT";
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
		$query = $query . " ORDER BY DOC_ID ";	
	}
	
	$result=mysqli_query($conn,$query);
	
	$documents_hst=array();
	
	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
	{
	
		//Betoltjuk egy array-be a query-bol kapott adatokat es tovabbitjuk a jquery-nek
		$documents_hst[] = 	array
							(
								'USR_INFO' => $row['USR_INFO'],
								'GRP_LST' => $row['GRP_LST'],
								'VIEW_DT' => $row['VIEW_DT']
							);
	}	
	echo json_encode($documents_hst);
	//unset($_SESSION['doc_id']);
			
?>
