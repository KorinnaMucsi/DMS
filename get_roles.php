<?php
	
	session_start();
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the NAS database");
	$selected = mysqli_select_db($conn, DB_NAME) or die("Couldn't open database"); 
	mysqli_set_charset($conn,"utf8");

	
	$sel_role='<select name="selRole" id="selRole" class="login_select" required>';
	
	if(isset($_POST['USR_ID']))
	{
		$query_role=	"SELECT UR.HR_FUNC_ID AS ROLE_ID, HR_FUNCTION " .
						"FROM " .
						"tUsrRoles UR JOIN tHRFunctions HR ON UR.HR_FUNC_ID=HR.ID " .
						"WHERE 1=1 " .
						"AND UR.USR_ID='" . $_POST['USR_ID'] . "' "; 					

		$result_role=mysqli_query($conn,$query_role);
		
		if(mysqli_num_rows($result_role)>0)
		{
			while($row_role=mysqli_fetch_array($result_role))
			{
				$sel_role.='<option value="' . $row_role['ROLE_ID'] . '">' . $row_role['HR_FUNCTION'] . '</option>';
			}
		}
		else
		{
			$sel_role.='<option value="">----- Please, select -----</option>';		
		}				
	}
	else
	{
		$sel_role.='<option value="">----- Please, select -----</option>';		
	}	
	
	$sel_role.='</select>';
	
	echo $sel_role;
?>
