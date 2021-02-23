<?php

//**********************************************************************FIRST TABLE WITH THE DOCUMENTS WAITING FOR THE APPROVAL**********************************************************************
	session_start();
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


//QUERY FOR THE APPROVE MAIN FORM -->
	
	$query=	"SELECT UD.ID, UD.TYPE_ID, YR, NO, DOC_ID, PRINT, DD.DOC_DESCR, " .
			"CASE WHEN UD.DOC_DESCR='' THEN DESCR ELSE UD.DOC_DESCR END AS DESCR, " . 
			"concat(U.L_NAME, ' ' , U.F_NAME, ' at ', DATE_FORMAT(U_DT,'%d.%m.%y %H:%i')) AS U_FLNAME_DT, " . 
			"DOC_APPROVED, A_USR, A_DT " .
			"FROM tUploadedDocs UD " .
			"JOIN tDocTypes DT ON UD.TYPE_ID=DT.TYPE_ID " .
			"JOIN tUsrs U ON UD.U_USR=U.USR_ID " .
			"LEFT JOIN tDocDescr DD ON DT.DOC_DESCR=DD.DESCR_ID " .
			"LEFT JOIN tUsrPermissionsDO DO ON UD.TYPE_ID=DO.TYPE_ID " .
			"WHERE GA_APPROVED=0 " .
			"AND UD.ID>143 " .
			"AND DOC_APPROVED IN (0,1) " .
			"AND UD.ACTIVE=1 " .
			"AND DO.USR_ID='" . $_SESSION['dms_username'] . "' " .
			"GROUP BY ID, TYPE_ID, YR, NO, DOC_ID, PRINT, DOC_DESCR, DESCR, U_FLNAME_DT, DOC_APPROVED, A_USR, A_DT ";

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
				$query = $query . " ORDER BY" . " " . $sortfield . " DESC";	
			}	
			else if ($sortorder == "asc")	
			{	
				$query = $query . " ORDER BY" . " " . $sortfield . " ASC";	
			}				
		}	
	}
	else
	{
		$query = $query . " ORDER BY YR DESC, NO DESC "; //Ez a default szortolas, ha nincs a griden semmilyen szortolas kivalasztva
	}
	
	$result=mysqli_query($conn,$query);
	
	$approvals=array();
	
	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
	{
	
		//Ezzel a modszerrel nem kell a bazisban tarolni a linket, mivel a query nem tudja visszaadni - mert rogton linket csinal belole es a mysqli_query funkcio nem tudja ertelmezni
		$v_lnk='<a id="v' . $row['ID'] . '" href="#" onclick="DownloadFile(' . $row['ID'] . ', \'nolog\')">View</a>';
		
		//Ha a DA jovahagyta, de rossz jegyet kapott
		if($row['DOC_APPROVED']==2)
		{
			$da_lnk='<img src="img/badResult.ico" alt="Bad result" width="16px">';
		}
		//Ha a DA jovahagyta es jo jegyet kapott
		if($row['DOC_APPROVED']==1)
		{
			$da_lnk='<img src="img/goodResult.ico" alt="Good result" width="16px">';
		}
		//Ha meg nincs jovahagyva
		if($row['DOC_APPROVED']==0)
		{
			$da_lnk='<a id="a' . $row['ID'] . '" href="#" onclick="DApproveDocument(' . $row['ID'] . ', \'' . $row['TYPE_ID'] . '\', \'' . $row['DOC_ID'] . '\')">Approve</a>';
		}
		
		$ga_lnk='<a id="a' . $row['ID'] . '" href="#" onclick="GApproveDocument(' . $row['ID'] . ', \'' . $row['TYPE_ID'] . '\', \'' . $row['DOC_ID'] . '\')">Approve</a>';
		
		//Betoltjuk egy array-be a query-bol kapott adatokat es tovabbitjuk a jquery-nek
		$approvals[] = 		array
							(
								'YR' => $row['YR'],
								'NO' => $row['NO'],
								'TYPE_ID' => $row['TYPE_ID'],
								'DOC_ID' => $row['DOC_ID'],
								'DOC_DESCR' => $row['DOC_DESCR'],
								'DESCR' => $row['DESCR'],
								'U_FLNAME_DT' => $row['U_FLNAME_DT'],
								'U_LNK_V' => $v_lnk,
								'U_LNK_DA' => $da_lnk,
								'U_LNK_GA' => $ga_lnk
							);
	}
	
	echo json_encode($approvals);

			
?>
