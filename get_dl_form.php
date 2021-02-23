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

	
	$return_to_form='';
	$job_list=array();
	
	if(isset($_POST['ARR_JOB_LIST']))
	{
		$job_list=json_decode($_POST['ARR_JOB_LIST']);
		
		//Amikor kiurult a lista, minden tetelt leszedtunk rola, akkor azt kuldi vissza, hogy 'No job titles added yet'
		if(empty(json_decode($_POST['ARR_JOB_LIST'])))
		{
			$return_to_form='<li>No job titles added yet</li>';
		}
		
		//Vegigmegy minden egyes lista elemen, amit a types.js fajl RemoveJobFromList funkcioja kuldott neki, majd elkesziti ujbol a listat,
		//amely mar nem tartalmazza a levett elemet
		foreach($job_list as $dl_item)
		{
			$query_dl=	"SELECT ID AS HR_FUNC_ID, HR_FUNCTION " .
						"FROM tHRFunctions " .
						"WHERE ID=" . $dl_item;

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
			}
		
		}	
		//Visszakuldi a types.js fajlban talalhato RemoveJobFromList funkcionak a kirajzolt formot.	
		echo $return_to_form;
	}
?>
