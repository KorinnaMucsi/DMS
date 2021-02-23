<?php
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	$query=	"SELECT * " .
			"FROM " .
			"( " .	
			"SELECT UD.ID, YR, NO, DT.TYPE_ID, DOC_ID, DESCR, IFNULL(DD.DOC_DESCR,'-') AS DOC_DESCR, " .
			"concat(U.L_NAME, ' ' , U.F_NAME, ' at ', DATE_FORMAT(U_DT,'%d.%m.%y %H:%i')) AS U_FLNAME_DT, " . 
			"IFNULL(concat(GA.L_NAME, ' ', GA.F_NAME, ' at ', DATE_FORMAT(GA_DT,'%d.%m.%y %H:%i')),'-') AS GA_FLNAME_DT, " .
			"CASE WHEN H.VIEW_COUNTER IS NULL THEN '' ELSE 'Viewed' END AS V, " .
			"CASE WHEN P_NO IS NULL THEN '' ELSE 'Yes' END AS P, " . 
			"CASE WHEN UD.ACTIVE=1 THEN 'Yes' ELSE '' END AS ACT, DTD, VIEW_COUNTER, 0 AS P_CNT, LEFT(DT.TYPE_ID,2) AS SORT, TYPE_NO " .
			"FROM tUploadedDocs UD " .
			"LEFT JOIN " . 
			"( " .
			"SELECT TYPE_ID, DESCR, DOC_DESCR, 'Document' AS DTD, 0 AS TYPE_NO " .
			"FROM " .
			"tDocTypes " .
			"UNION " .
			"SELECT TYPE_ID, DESCR, 0 AS DOC_DESCR, 'Appendix' AS DTD, TYPE_NO " .
			"FROM tPDocTypes " .
			")DT ON UD.TYPE_ID=DT.TYPE_ID " .
			"LEFT JOIN tUsrs U ON UD.U_USR=U.USR_ID " .
			"LEFT JOIN tUsrs GA ON UD.GA_USR=GA.USR_ID " .
			"LEFT JOIN tDocDescr DD ON DT.DOC_DESCR=DD.DESCR_ID " .
			"LEFT JOIN tDocDistribHst H ON UD.ID=H.U_DOC_ID " .
			"LEFT JOIN " .
			"( " .
			"SELECT DOC_TYPE_ID, PDT.TYPE_ID, COUNT(PDT.ID) AS P_NO " .
			"FROM tPDocTypes PDT " .
			"JOIN tUploadedDocs UD ON PDT.TYPE_ID=UD.TYPE_ID " .
			"WHERE UD.ACTIVE=1 " .
			"GROUP BY DOC_TYPE_ID, PDT.TYPE_ID " .
			") Q1 ON UD.TYPE_ID=Q1.DOC_TYPE_ID " .
			"WHERE GA_APPROVED=1 " .
			"AND UD.ACTIVE=1 " . 
			"AND H.USR_ID='" . $_SESSION['dms_username'] . "' " .
			"AND H.VIEW_COUNTER IS NULL " .
			"GROUP BY ID, YR, NO, TYPE_ID, DOC_ID, DESCR, DOC_DESCR, U_FLNAME_DT, GA_FLNAME_DT, V, P, ACT, DTD, VIEW_COUNTER, P_CNT, SORT, TYPE_NO " .
			") QUERY " .
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
					$sortfield= " SORT DESC, TYPE_NO ";
				}
				$query = $query . " ORDER BY " . " " . $sortfield . " DESC";	
			}	
			else if ($sortorder == "asc")	
			{	
				if($sortfield == "DOC_ID")
				{
					$sortfield= " SORT ASC, TYPE_NO ";
				}
				$query = $query . " ORDER BY " . " " . $sortfield . " ASC";	
			}				
		}
	}
	else //Ez a default szortolas, ha nincs a griden semmilyen szortolas kivalasztva
	{
		$query = $query . " ORDER BY SORT, TYPE_NO ";	
	}
	
	$result=mysqli_query($conn,$query);
	
	$nvdocs=array();
	
	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
	{
	
		//Ezzel a modszerrel nem kell a bazisban tarolni a linket, mivel a query nem tudja visszaadni - mert rogton linket csinal belole es a mysqli_query funkcio nem tudja ertelmezni
		$v_lnk='<a id="v' . $row['ID'] . '" href="#" onclick="DownloadFile(' . $row['ID'] . ', \'log\')">View</a>';
		$vp_lnk='<a id="v' . $row['ID'] . '" href="#" onclick="DownloadFile(' . $row['ID'] . ', \'print\')">View printable v.</a>';
		$hst_lnk='<a id="h' . $row['ID'] . '" href="#" onclick="ShowDocHistory(' . $row['ID'] . ',\'' . $row['DOC_ID'] . '\'' . ', \'NV\'' . ')">History</a>';
		
		if($row['P']=='')
		{
			$p_no='<span id="p_invisible">.</span>';
		}
		else
		{
			$p_no='<a id="p' . $row['ID'] . '" href="#" onclick="Javascript:ShowPrilog(\'' . $row['TYPE_ID'] . '\');">' . $row['P'] . '</a>';
		}
		
		if(empty($row['VIEW_COUNTER']))
		{
			$descr='<img src="img/new_doc.png" alt="New" height="15em" title="This document is not viewed yet"/>' . ' ' . $row['DESCR'];
		}
		else
		{
			$descr=$row['DESCR'];
		}
		//Betoltjuk egy array-be a query-bol kapott adatokat es tovabbitjuk a jquery-nek
		$nvdocs[] = 		array
							(
								'YR' => $row['YR'],
								'NO' => $row['NO'],		
								'TYPE_ID' => $row['TYPE_ID'],						
								'DOC_ID' => $row['DOC_ID'],
								'DOC_DESCR' => $row['DOC_DESCR'],
								'DESCR' => $descr,
								'DTD' => $row['DTD'],
								'U_FLNAME_DT' => $row['U_FLNAME_DT'],
								'A_FLNAME_DT' => $row['GA_FLNAME_DT'],
								'U_LNK_V' => $v_lnk,
								'U_LNK_VP' => $vp_lnk,
								'P_NO' => $p_no,
								'HST' => $hst_lnk,
								'ACT' => $row['ACT']
							);
	}
	
	echo json_encode($nvdocs);
?>
