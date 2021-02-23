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



	if(isset($_POST['SEL_DPT']))
	{
		$sel_dpt=$_POST['SEL_DPT'];

					
		if($_POST['SEL_DPT']=="ALL")
		{
			$opt_ws='<select name="sel_ws" class="sel" required>' . "\n".
					'<option value="">--- Please, select ---</option>' . "\n" .
					'<option value="ALL" selected="selected">ALL</option>' . "\n" .
					'</select>';
		}		
		else
		{
			$query_ws=	"SELECT WS_ID, DESCR AS WORKSTATION " .
						"FROM ". 
						"tWrkStations WS " .
						"WHERE WS_ID<>'ws_00' " .
						"AND DPT_ID= " . $sel_dpt . " " . 
						"ORDER BY WORKSTATION ";
						
			$result_ws=mysqli_query($conn,$query_ws);	
			$cnt_ws=mysqli_num_rows($result_ws);
			
			$opt_ws=	'<select name="sel_ws" class="sel" required>' . "\n";
			
			if($cnt_ws==0)
			{
				$opt_ws.=	'<option value="">--- Please, select ---</option>' . "\n" .
							'<option value="ALL" selected="selected">ALL</option>' . "\n";
			}
			else
			{
				$opt_ws.=	'<option value="" selected="selected">--- Please, select ---</option>' . "\n" .
							'<option value="ALL">ALL</option>' . "\n";
			}
			
			$cnt_ws=mysqli_num_rows($result_ws);
			while($row_ws=mysqli_fetch_array($result_ws))
			{		
				$opt_ws.='<option value="' . $row_ws['WS_ID'] . '">' . $row_ws['WORKSTATION'] . '</option>' . "\n";	
			}
			
			$opt_ws.='</select>';	
			
			$opt_ws.='<span class="chk"><input name="chklist" type="checkbox">Show extended list - with all users having access to the workstations</span>';
		}
		echo $opt_ws;
	}
?>
