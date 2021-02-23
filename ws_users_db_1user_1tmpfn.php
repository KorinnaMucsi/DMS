<?php
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	$query=	"SELECT U.USR_ID, FULL_NAME, Q1.HR_FUNC_ID AS FUNC_ID_PERMANENT, HR_FUNCTION_PERMANENT, Q2.HR_FUNC_ID AS FUNC_ID_TEMPORARY, HR_FUNCTION_TEMPORARY, " .
			"IFNULL(Q2.FROM_HR,1) AS FROM_HR, " .
			"IFNULL(Q2.HR_FUNC_ID,Q1.HR_FUNC_ID) AS HR_FUNC_ID, " .
			"IFNULL(Q2.ROLE_ID,Q1.ROLE_ID) AS ROLE_ID " .
			"FROM " .
			"( " .
			"SELECT USR_ID, CONCAT(L_NAME,' ',F_NAME) AS FULL_NAME " .
			"FROM " .
			"tUsrs " .
			"WHERE ACTIVE=1 " .
			") U " .
			"LEFT JOIN " .
			"( " .
			"SELECT R.ID AS ROLE_ID, R.HR_FUNC_ID, R.USR_ID, F.HR_FUNCTION AS HR_FUNCTION_PERMANENT, FROM_HR " .
			"FROM tUsrRoles R " .
			"JOIN tHRFunctions F ON R.HR_FUNC_ID=F.ID " .
			"AND FROM_HR=1 " .
			") Q1 ON U.USR_ID=Q1.USR_ID " .
			"LEFT JOIN " .
			"( " .
			"SELECT R.ID AS ROLE_ID, R.HR_FUNC_ID, R.USR_ID, F.HR_FUNCTION AS HR_FUNCTION_TEMPORARY, FROM_HR " .
			"FROM tUsrRoles R " .
			"JOIN tHRFunctions F ON R.HR_FUNC_ID=F.ID " .
			"AND FROM_HR=0 " .
			") Q2 ON U.USR_ID=Q2.USR_ID " .
			"WHERE 1=1 " .
			"AND Q1.HR_FUNC_ID IS NOT NULL ";

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
		$query = $query . " ORDER BY FULL_NAME ";	
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
			$e_lnk='<span id="p_invisible">.</span>';
			$d_lnk='<span id="p_invisible">.</span>';
		}
		else
		{
			if($permission==0)
			{
				$d_lnk='<a id="d' . $row['ROLE_ID'] . '" href="#" onclick="Javascript:alert(\'You don\'t have permission to remove the temporary job title!\');">Delete temp.</a>';
				$e_lnk='<a id="e' . $row['ROLE_ID'] . '" href="#" onclick="Javascript:alert(\'You don\'t have permission to edit the temporary job title!\');">Edit temp.</a>';
			}
			else
			{
				$d_lnk='<a id="d' . $row['ROLE_ID'] . '" href="#" onclick="DelTempJobTitle(' . $row['ROLE_ID']. ');">Delete temp.</a>';
				$e_lnk='<a id="e' . $row['ROLE_ID'] . '" href="#" onclick="EditTempJobTitle(' . $row['HR_FUNC_ID']. ',' . '\'' . $row['USR_ID']. '\');">Edit temp.</a>';
	   		}
	   	}
		
		//Betoltjuk egy array-be a query-bol kapott adatokat es tovabbitjuk a jquery-nek
		$ws_usrs[] =	array
					(
						'FULL_NAME' => $row['FULL_NAME'],
						'ROLE_ID' => $row['ROLE_ID'],
						'FUNC_ID_PERMANENT' => $row['FUNC_ID_PERMANENT'],
						'FUNC_ID_TEMPORARY' => $row['FUNC_ID_TEMPORARY'],
						'HR_FUNCTION_PERMANENT' => $row['HR_FUNCTION_PERMANENT'],
						'HR_FUNCTION_TEMPORARY' => $row['HR_FUNCTION_TEMPORARY'],
						'E_LNK' => $e_lnk,			
						'D_LNK' => $d_lnk
					);
	}	
	echo json_encode($ws_usrs);
?>
