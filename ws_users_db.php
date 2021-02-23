<?php
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	$query=	"SELECT * FROM " .
			"( " .
			"SELECT R.ID AS ROLE_ID, CONCAT(U.L_NAME,' ',U.F_NAME) AS FULL_NAME, F.HR_FUNCTION, FROM_HR, " .
			"CASE FROM_HR WHEN 1 THEN 'Permanent' ELSE 'Temporary' END AS FN_TYPE " .
			"FROM tUsrRoles R " .
			"JOIN tUsrs U ON R.USR_ID=U.USR_ID " .
			"JOIN tHRFunctions F ON R.HR_FUNC_ID=F.ID " .
			"WHERE 1=1 " .
			"AND U.ACTIVE=1 " .
			") Q1 " .
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
			
			if ($filteroperator == 0)
			{
				$fo = "AND ";
			}
			else
			{
				$fo = "OR ";	
			}
			// build the query.
			$query = $query . $where;
			$_SESSION['where']=	'Filter value: ' . $filtervalue . '<br>Filter condition: ' . $filtercondition . '<br>Filter data field: ' . $filterdatafield . '<br>' .
								'Filter operator:' . $fo;
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
		$query = $query . " ORDER BY FULL_NAME, FN_TYPE ";	
	}
	
	$result=mysqli_query($conn,$query);
	
	//Leellenorizzuk, hogy a felhasznalonak van-e  jogosultsaga az Edit gombra kattintani. tProfilePermissions tabla, AS0005 jogosultsag
	//Megnezni mindenkinek van joga, aki belemehet az APPROVE menupontba, viszont editalni csak az AS0005 jogosultsaggal rendelkezoknek lehet
	
	require_once('get_profile_permission.php');
	
	$permission=GetProfilePermission('AS0006',$_SESSION['dms_username']);
				
	$ws_usrs=array();
	
	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
	{
	
		//DELETE LINK OSSZERAKAS (Ha a FROM_HR=1, akkor azt a Dora allitotta be, mint allando munkahely, azt nem banthatjak a valtasvezetok vagy WS felelosok)
		if($row['FROM_HR']==1)
		{
			$d_lnk='<span id="p_invisible">.</span>';
		}
		else
		{
			if($permission==0)
			{
				$d_lnk='<a id="d' . $row['ROLE_ID'] . '" href="#" onclick="Javascript:alert(\'You don\\\'t have permission to remove the temporary job title!\');">Delete</a>';
			}
			else
			{
				$d_lnk='<a id="d' . $row['ROLE_ID'] . '" href="#" onclick="DelTempJobTitle(\'' . $row['ROLE_ID']. '\');">Delete</a>';
	   		}
	   	}
		
		//Betoltjuk egy array-be a query-bol kapott adatokat es tovabbitjuk a jquery-nek
		$ws_usrs[] =	array
					(
						'FULL_NAME' => $row['FULL_NAME'],
						'HR_FUNCTION' => $row['HR_FUNCTION'],
						'FN_TYPE' => $row['FN_TYPE'],
						'D_LNK' => $d_lnk
					);
	}	
	echo json_encode($ws_usrs);
?>
