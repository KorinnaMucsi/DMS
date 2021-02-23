<?php	
	session_start();
	
	//Visszaadja a HR Preview-hoz a roviditett nevet a HR funkcionak a felhasznalo melle rendelt ID alapjan a tHRFunctions tablabol (usrs_maint.js szkript PreviewPDF funkcio)
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");



	if(isset($_POST['TYPE_ID']))
	{
		$return_to_form='';
		$return_to_array='';
		$return_array=array();
		$type_id=$_POST['TYPE_ID'];
				
		$query_dl=	"SELECT P.HR_FUNC_ID, HR.HR_FUNCTION " .
					"FROM tUsrPermissionsFN P " .
					"JOIN tHRFunctions HR ON P.HR_FUNC_ID=HR.ID " .
					"WHERE TYPE_ID='" . $type_id . "' " .
					"ORDER BY HR.HR_FUNCTION ";

		$result_dl=mysqli_query($conn, $query_dl);
		
		if(mysqli_num_rows($result_dl)==0)
		{
			$return_to_form='<li>No job titles added yet</li>';
		}
		while($row_dl=mysqli_fetch_array($result_dl))
		{
			$return_to_form.=	'<div style="display:inline-block;clear:left;float:left;width:70%;text-align:left;">' .
								'<li>' . 
								$row_dl['HR_FUNCTION'] . 
								'</li>' .
								'</div>' .
								'<div style="display:inline-block;clear:right;float:left;width:29%;text-align:right;margin-right:1%;"> ' .
								'<a id="' . $row_dl['HR_FUNC_ID'] . '"href="#" onclick="Javascript:RemoveJobFromList(' . $row_dl['HR_FUNC_ID'] . ');">Remove</a></div>';
			
			array_push($return_array, $row_dl['HR_FUNC_ID']);
		}
		
		$return_to_array=json_encode($return_array);
		//$return_to_array=implode(",", $return_array);
		
		echo json_encode(array("return_to_form" => $return_to_form, "return_to_array" => $return_to_array));
	}
?>
