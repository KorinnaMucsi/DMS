<?php
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	$query=	"SELECT * FROM (" .
			"SELECT TYPE_ID, DESCR, DD.DOC_DESCR, " .
			"CASE DT.ACTIVE WHEN 1 THEN 'Yes' ELSE 'No' END AS ACTIVE " .
			"FROM tDocTypes DT " .
			"LEFT JOIN tDocDescr DD ON DT.DOC_DESCR=DD.DESCR_ID " .
			") Q WHERE 1=1 ";

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
		$query = $query . " ORDER BY TYPE_ID ";	
	}
	
	$result=mysqli_query($conn,$query);
	
	//Leellenorizzuk, hogy a felhasznalonak van-e  jogosultsaga az Edit gombra kattintani. tProfilePermissions tabla, AS0005 jogosultsag
	//Megnezni mindenkinek van joga, aki belemehet az APPROVE menupontba, viszont editalni csak az AS0005 jogosultsaggal rendelkezoknek lehet
	
				
	$types=array();
	
	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
	{
		$e_lnk='<a id="e' . $row['TYPE_ID'] . '" href="#" onclick="EditType(\'' . $row['TYPE_ID']. '\');">Edit</a>';
		$d_lnk='<a id="d' . $row['TYPE_ID'] . '" href="#" onclick="EditDistribLst(\'' . $row['TYPE_ID']. '\');">Change</a>';

		//Betoltjuk egy array-be a query-bol kapott adatokat es tovabbitjuk a jquery-nek
		$types[] =	array
					(
						'TYPE_ID' => $row['TYPE_ID'],
						'DESCR' => $row['DESCR'],
						'DOC_DESCR' => $row['DOC_DESCR'],
						'ACTIVE' => $row['ACTIVE'],
						'E_LNK' => $e_lnk,
						'D_LNK' => $d_lnk
					);
	}	
	echo json_encode($types);
?>
