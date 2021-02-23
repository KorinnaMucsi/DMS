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
			"( " .
			"SELECT UD.ID, UD.YR, UD.NO, Q0.TYPE_ID, UD.DOC_ID, DT.PRINT, DD.DOC_DESCR, " .
			"CASE WHEN UD.DOC_DESCR='' THEN DT.DESCR ELSE UD.DOC_DESCR END AS DESCR, " . 
			"concat(U.L_NAME, ' ' , U.F_NAME, ' at ', DATE_FORMAT(U_DT,'%d.%m.%y %H:%i')) AS U_FLNAME_DT, " . 
			"concat(GA.L_NAME, ' ', GA.F_NAME, ' at ', DATE_FORMAT(GA_DT,'%d.%m.%y %H:%i')) AS GA_FLNAME_DT, " .
			"CASE WHEN Q3.VIEW_COUNTER IS NULL THEN '' ELSE 'Viewed' END AS V, " .
			"CASE WHEN P_NO IS NULL THEN '' ELSE 'Yes' END AS P, " .
			"CASE WHEN UD.ACTIVE=1 THEN 'Yes' ELSE '' END AS ACT, Q3.VIEW_COUNTER, IFNULL(P_CNT,0) AS P_CNT, LEFT(DT.TYPE_ID,2) AS SORT, IFNULL(TYPE_NO,0) AS TYPE_NO " .
			"FROM " .
			"( " .
			"SELECT * " .
			"FROM tUsrPermissionsFN " .
			"WHERE HR_FUNC_ID=" . $_SESSION['job_title']  . " " .
			"AND VIEW_P=1 " .
			")Q0 " .
			"JOIN tUploadedDocs UD ON Q0.TYPE_ID=UD.TYPE_ID " .
			"JOIN tDocTypes DT ON UD.TYPE_ID=DT.TYPE_ID " .
			"LEFT JOIN tUsrs U ON UD.U_USR=U.USR_ID " .
			"LEFT JOIN tUsrs GA ON UD.GA_USR=GA.USR_ID " .
			"LEFT JOIN tDocDescr DD ON DT.DOC_DESCR=DD.DESCR_ID " .
			"LEFT JOIN " .
			"( " .
			"SELECT DOC_TYPE_ID, PDT.TYPE_ID, COUNT(PDT.ID) AS P_NO " .
			"FROM tPDocTypes PDT " .
			"JOIN tUploadedDocs UD ON PDT.TYPE_ID=UD.TYPE_ID " .
			"WHERE UD.ACTIVE=1 " .
			"GROUP BY DOC_TYPE_ID, PDT.TYPE_ID " .
			") Q1 ON UD.TYPE_ID=Q1.DOC_TYPE_ID " .
			"LEFT JOIN " .
			"( " .
			"SELECT IFNULL(PDT.DOC_TYPE_ID,'-') AS DOC_TYPE_ID, COUNT(HST.ID) AS P_CNT " .
			"FROM " .
			"( " .
			"SELECT PT.TYPE_ID " .
			"FROM tUsrPermissionsFN UF " .
			"JOIN tPDocTypes PT ON UF.TYPE_ID=PT.DOC_TYPE_ID " .
			"WHERE HR_FUNC_ID=" . $_SESSION['job_title']  . " " .
			")Q1 " .
			"JOIN tUploadedDocs UD ON UD.TYPE_ID=Q1.TYPE_ID " .
			"JOIN tPDocTypes PDT ON PDT.TYPE_ID=UD.TYPE_ID " .
			"JOIN tDocDistribHst HST ON UD.ID=HST.U_DOC_ID " .
			"WHERE 1=1 " .
			"AND UD.ACTIVE=1 " . 
			"AND UD.GA_APPROVED=1 " . 
			"AND HST.USR_ID='" . $_SESSION['dms_username'] . "' " .
			"AND HST.VIEW_COUNTER IS NULL " .
			"GROUP BY PDT.DOC_TYPE_ID " .
			")Q2 ON UD.TYPE_ID=Q2.DOC_TYPE_ID " .
			"LEFT JOIN " .
			"( " .
			"SELECT * " .
			"FROM tDocDistribHst " .
			"WHERE USR_ID='" . $_SESSION['dms_username'] . "' " .
			")Q3 ON UD.ID=Q3.U_DOC_ID " .
			"LEFT JOIN tPDocTypes PDT ON PDT.TYPE_ID=UD.TYPE_ID " .
			"WHERE GA_APPROVED=1 " .
			"AND UD.ACTIVE=1 " .
			"AND UD.ID>143 " .
			"GROUP BY ID, YR, NO, TYPE_ID, DOC_ID, PRINT, DESCR, DOC_DESCR, U_FLNAME_DT, GA_FLNAME_DT, V, P, ACT, VIEW_COUNTER, P_CNT, SORT, TYPE_NO " .
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
	$documents=array();
	
	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
	{
	
		//Ezzel a modszerrel nem kell a bazisban tarolni a linket, mivel a query nem tudja visszaadni - mert rogton linket csinal belole es a mysqli_query funkcio nem tudja ertelmezni
		$v_lnk='<a id="v' . $row['ID'] . '" href="#" onclick="DownloadFile(' . $row['ID'] . ', \'log\')">View</a>';
		$vp_lnk='<a id="v' . $row['ID'] . '" href="#" onclick="DownloadFile(' . $row['ID'] . ', \'print\')">View printable v.</a>';
		$hst_lnk='<a id="h' . $row['ID'] . '" href="#" onclick="ShowDocHistory(' . $row['ID'] . ',\'' . $row['DOC_ID'] . '\'' . ', \'MY\'' . ')">History</a>';
		
		if($row['P']=='')
		{
			$p_num='<span id="p_invisible">.</span>';
		}
		if($row['P']=='Yes')
		{
			$p_num='<a id="p' . $row['ID'] . '" href="#" onclick="Javascript:ShowPrilog(\'' . $row['TYPE_ID'] . '\');">' . $row['P'] . '</a>';
		}
		
		//Piros felkijaltojel:
		//Ha VIEW_COUNTER NULL, akkor nincs megnezve a dokumentum es piros felkijaltojelet kell tenni melle
		if(is_null($row['VIEW_COUNTER']))
		{
			//Ha a P_CNT (Az adott dokumentum alatt levo megnezetlen prilogok szama) 0, akkor nem kerul ikon a piros felkijalto jel melle
			if($row['P_CNT']==0)
			{
				$descr='<img src="img/new_doc.png" alt="New" height="15em" title="This document is not viewed yet"/>' . ' ' . $row['DESCR'];
			}
			
			//Ha a P_CNT (Az adott dokumentum alatt levo megnezetlen prilogok szama) 0, akkor sarga felkijalto jel kerul a piros melle
			if($row['P_CNT']!=0)
			{
				$descr=	'<img src="img/new_doc.png" alt="New" height="15em" title="This document is not viewed yet"/>' . 
						'<img src="img/new_pdoc.png" alt="New" height="15em" title="This document has not viewed appendixes on it"/>' .
						' ' . $row['DESCR'];
			}
		}
		//Ha VIEW_COUNTER nem NULL, akkor megnezte a felhasznalo a dokumentumot nem kell piros felkijalto jel melle
		if(!is_null($row['VIEW_COUNTER']))
		{
			//Ha a P_CNT (Az adott dokumentum alatt levo megnezetlen prilogok szama) kulonbozik nullatol, sarga ikon kerul a dokumentum neve melle
			if($row['P_CNT']!=0)
			{
				$descr='<img src="img/new_pdoc.png" alt="New" height="15em" title="This document has not viewed appendixes on it"/>' . ' ' . $row['DESCR'];
			}
			//Ha a P_CNT (Az adott dokumentum alatt levo megnezetlen prilogok szama) 0, akkor nem kerul sarga ikon a dokumentum neve melle
			if($row['P_CNT']==0)
			{
				$descr=$row['DESCR'];
			}
		}
		//Betoltjuk egy array-be a query-bol kapott adatokat es tovabbitjuk a jquery-nek
		$documents[] = 		array
							(
								'YR' => $row['YR'],
								'NO' => $row['NO'],		
								'TYPE_ID' => $row['TYPE_ID'],						
								'DOC_ID' => $row['DOC_ID'],
								'DOC_DESCR' => $row['DOC_DESCR'],
								'DESCR' => $descr,
								'U_FLNAME_DT' => $row['U_FLNAME_DT'],
								'A_FLNAME_DT' => $row['GA_FLNAME_DT'],
								'U_LNK_V' => $v_lnk,
								'U_LNK_VP' => $vp_lnk,
								'P_NUM' => $p_num,
								'HST' => $hst_lnk,
								'ACT' => $row['ACT']
							);
	}
	
	echo json_encode($documents);
			
?>
