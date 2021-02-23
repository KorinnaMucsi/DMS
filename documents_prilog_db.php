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
	
	$query=	"SELECT * " .
			"FROM " .
			"(" .
			"SELECT UD.ID, YR, NO, PDT.TYPE_ID, DOC_ID, " .
			"CASE WHEN UD.DOC_DESCR='' THEN PDT.DESCR ELSE UD.DOC_DESCR END AS DESCR, " . 
			"concat(U.L_NAME, ' ' , U.F_NAME, ' at ', DATE_FORMAT(U_DT,'%d.%m.%y %H:%i')) AS U_FLNAME_DT, " .
			"CASE WHEN UD.ACTIVE=1 THEN 'Yes' ELSE '' END AS ACT, VIEW_COUNTER, TYPE_NO " .
			"FROM tUploadedDocs UD " .
			"JOIN tPDocTypes PDT ON UD.TYPE_ID=PDT.TYPE_ID " .
			"LEFT JOIN tUsrs U ON UD.U_USR=U.USR_ID " .
			"LEFT JOIN tDocDistribHst HST ON UD.ID=HST.U_DOC_ID AND HST.USR_ID='" . $_SESSION['dms_username'] . "' " .
			"WHERE GA_APPROVED=1 " .
			"AND UD.ID>143 " .
			"AND DOC_TYPE_ID='" . $_SESSION['doc_type_id'] . "' " . 
			")Q " .
			"WHERE 1=1 ";
			
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
			if ($sortorder == "desc")	
			{	
				if($sortfield == "DOC_ID")
				{
					$sortfield= " TYPE_NO ";
				}
				$query = $query . " ORDER BY " . " " . $sortfield . " DESC";	
			}	
			else if ($sortorder == "asc")	
			{	
				if($sortfield == "DOC_ID")
				{
					$sortfield= " TYPE_NO ";
				}
				$query = $query . " ORDER BY " . " " . $sortfield . " ASC";	
			}				
		}	
	}
	else //Ez a default szortolas, ha nincs a griden semmilyen szortolas kivalasztva
	{
		$query = $query . " ORDER BY TYPE_NO ";
	}
	
	$result=mysqli_query($conn,$query);
	
	$prilog=array();
	
	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
	{
	
		//Ezzel a modszerrel nem kell a bazisban tarolni a linket, mivel a query nem tudja visszaadni - mert rogton linket csinal belole es a mysqli_query funkcio nem tudja ertelmezni
		$v_lnk='<a id="v' . $row['ID'] . '" href="#" onclick="DownloadFile(' . $row['ID'] . ', \'log\')">View</a>';
		$hst_lnk='<a id="h' . $row['ID'] . '" href="#" onclick="ShowDocHistory(' . $row['ID'] . ',\'' . $row['DOC_ID'] . '\'' . ', \'PDT\'' . ')">History</a>';
		
		if(is_null($row['VIEW_COUNTER']))
		{
			$descr='<img src="img/new_doc.png" alt="New" height="15em" title="This document is not viewed yet"/>' . ' ' . $row['DESCR'];
		}
		else
		{
			$descr=$row['DESCR'];
		}
		
		//Betoltjuk egy array-be a query-bol kapott adatokat es tovabbitjuk a jquery-nek
		$prilog[] = 		array
							(
								'YR' => $row['YR'],
								'NO' => $row['NO'],		
								'DOC_ID' => $row['DOC_ID'],						
								'DESCR' => $descr,
								'U_FLNAME_DT' => $row['U_FLNAME_DT'],
								'U_LNK_V' => $v_lnk,
								'HST' => $hst_lnk,
								'ACT' => $row['ACT']
							);
	}
	
	echo json_encode($prilog);

			
?>
