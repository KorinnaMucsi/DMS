<?php
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	require_once('get_profile_permission.php');
	
	$permission=GetProfilePermission('AS0007',$_SESSION['dms_username']);
	
	if($permission==0)
	{
		$documents_all='<script>alert("You don\'t have permission to this operation!");document.location.href="main.php?showDocuments=True";</script>';
	}
	else
	{
		
		$btn=	'<div class="bck_btn">' . "\n" .
				'<input type="button" id="bck_btn" name="back" value="Back to the \'All documents\'" onclick="Javascript:window.location=\'main.php?showAllDocuments=True\';">' . "\n" .
				'</div>' . "\n";
	
		$data=	'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
				'<div class="bck_btn">' . "\n" .
				'<input type="button" id="bck_btn" name="back" value="Go back to \'Documents\' menu" onclick="Javascript:window.location=\'main.php?showDocuments=True\';">' . "\n" .
				'</div>' . "\n" .
				'<span class="main_subtitle"><i><b>List of all the active approved documents:</b></i></span>' . "\n" .
				'<div id="jqxgridAllDocs">' . "\n" .
				'</div>' . "\n" .
				'</div>' . "\n";
				
		require_once('documents_hst_form.php');
		
		//Osszerakja a jovahagyo form kinezetet
		$documents_all=	'<div id="mainContainerDiv" class="main_mainContainerDiv">' . "\n" .
						'<div class="main_containerDiv" id="main_containerDiv">' . "\n" .
						'<div class="main_titleDiv">' . "\n" .
						'<span class="main_title">All Active Approved Documents<br>Menu</span>' . "\n" .
						'<span class="main_titleDiv_sp"><img src="img/documents.png" alt="All documents"/></span>' . "\n" .
						'<span class="main_subtitle">You can find the list of all active documents approved by the General Approver within the DMS application.<br>' ."\n" .
						'Please, click on the \'View\' link to read the document!</span>' . "\n" .
						'<hr>' . "\n" .
						'</div>' . "\n" . 
						$data . "\n" .
						'</div>' . "\n" .
						'</div>';
	}
?>
