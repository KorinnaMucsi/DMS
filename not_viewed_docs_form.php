<?php
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

		
	$job_title=$_SESSION['job_title'];
			
	$query="SELECT HR_FUNCTION FROM tHRFunctions WHERE ID=" . $job_title;
	$result=mysqli_query($conn,$query);
					
	if(mysqli_num_rows($result)==1)
	{	
		$row=mysqli_fetch_array($result);
		$_SESSION['job_title_descr']=$row['HR_FUNCTION'];
	}
	
	$btn=	'<div class="bck_btn">' . "\n" .
			'<input type="button" id="bck_btn" name="back" value="Back to the not viewed documents" onclick="Javascript:window.location=\'main.php?showNVDocuments=True\';">' . "\n" .
			'</div>' . "\n";
			
	$data=	'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
			'<div class="bck_btn">' . "\n" .
			'<input type="button" id="bck_btn" name="back" value="Show all documents for \'' . $_SESSION['job_title_descr'] . 
			'\'" onclick="Javascript:window.location=\'main.php?showMyDocuments=True\';">' . "\n" .
			'</div>' . "\n" .
			'<span class="main_subtitle"><i><b>Unread documents:</b></i></span>' . "\n" .
			'<div id="jqxgridNVDocs">' . "\n" .
			'</div>' . "\n" .
			'</div>' . "\n";
			
	require_once('documents_hst_form.php');
			
	//Osszerakja a jovahagyo form kinezetet
	$nvdocs='<div id="mainContainerDiv" class="main_mainContainerDiv">' . "\n" .
			'<div class="main_containerDiv" id="main_containerDiv">' . "\n" .
			'<div class="main_titleDiv">' . "\n" .
			'<span class="main_title">Unread Documents<br>Menu</span>' . "\n" .
			'<span class="main_titleDiv_sp"><img src="img/documents.png" alt="Unread documents"/></span>' . "\n" .
			'<span class="main_subtitle">You can find the list of your unread active documents within the DMS application.<br>' ."\n" .
			'Please, click on the \'View\' link to read the document!<br>Press \'F5\' button to refresh the list after you read the document.</span>' . "\n" .
			'<hr>' . "\n" .
			'</div>' . "\n" . 
			$data . "\n" .
			'</div>' . "\n" .
			'</div>';
?>
