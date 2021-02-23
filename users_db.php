<?php
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


	$query=	"SELECT USR_ID, concat(L_NAME, ' ', F_NAME) AS USR_NAME, F.HR_FUNCTION, HR_FUNC_ID, U.ACTIVE, BLOCK_ID " .
			"FROM tUsrs U " .
			"LEFT JOIN tHRFunctions F " .
			"ON U.HR_FUNC_ID=F.ID " .
			"WHERE 1=1 " .
			//"AND SIGNATURE=1 " .
			"AND U.ACTIVE=1 ";

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
		$query = $query . " ORDER BY concat(L_NAME, ' ', F_NAME) ";	
	}
	
	$result=mysqli_query($conn,$query);
	
	//Leellenorizzuk, hogy a felhasznalonak van-e  jogosultsaga az Edit gombra kattintani. tProfilePermissions tabla, AS0005 jogosultsag
	//Megnezni mindenkinek van joga, aki belemehet az APPROVE menupontba, viszont editalni csak az AS0005 jogosultsaggal rendelkezoknek lehet
	
	require_once('get_profile_permission.php');
	
	$permission=GetProfilePermission('AS0005',$_SESSION['dms_username']);
	$ws_permission=GetProfilePermission('AS0006',$_SESSION['dms_username']);
	
	$usrs=array();
	$ws_usrs=array();
	
	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
	{
	
		//EDIT LINK OSSZERAKAS
		if($permission==0)
		{
			$e_lnk='<a id="e' . $row['USR_ID'] . '" href="#" onclick="Javascript:alert(\'For HR staff only!\');">Edit</a>';
		}
		else
		{
			$e_lnk='<a id="e' . $row['USR_ID'] . '" href="#" onclick="EditUsr(\'' . $row['USR_ID']. '\',\'' . $row['USR_NAME'] . '\',\'' . $row['HR_FUNC_ID'] . '\');">Edit</a>';
   		}
		
		//VIEW LINK OSSZERAKAS
		if($row['HR_FUNCTION']=='')
		{
			$v_lnk='<a id="v' . $row['USR_ID'] . '" href="#" onclick="Javascript:alert(\'No function entered yet!\');">Preview</a>';
		}
		else
		{
        	$v_lnk='<a id="v' . $row['USR_ID'] . '" href="#" onclick="ViewUsrSignature(\'' . $row['USR_ID']. '\',\'' . $row['USR_NAME'] . '\',\'' . $row['HR_FUNC_ID'] . '\');">Preview</a>';
		}
		//UNBLOCK LINK OSSZERAKAS
		if($row['BLOCK_ID']=='B')
		{
			if($ws_permission==0)
			{
				$ub_lnk='<a id="e' . $row['USR_ID'] . '" href="#" onclick="Javascript:alert(\'For Supervisors staff only!\');">Unblock</a>';
			}
			else
			{
				$ub_lnk='<a id="e' . $row['USR_ID'] . '" href="#" onclick="UnblockWSUsr(\'' . $row['USR_ID']. '\');">Unblock</a>';
	   		}
	   	}
	   	else
	   	{
	   		$ub_lnk='<span id="p_invisible">.</span>';
	   	}

			
		
		//Betoltjuk egy array-be a query-bol kapott adatokat es tovabbitjuk a jquery-nek
		$usrs[] =	array
					(
						'USR_NAME' => $row['USR_NAME'],
						'HR_FUNCTION' => $row['HR_FUNCTION'],
						'E_LNK' => $e_lnk,
						'V_LNK' => $v_lnk,
						'UB_LNK' => $ub_lnk
					);
	}	
	echo json_encode($usrs);
?>
