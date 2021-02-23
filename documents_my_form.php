<?php
	
	$btn=	'<div class="bck_btn">' . "\n" .
			'<input type="button" id="bck_btn" name="back" value="Back to the documents" onclick="Javascript:window.location=\'main.php?showMyDocuments=True\';">' . "\n" .
			'</div>' . "\n";

	
	$data=	'<div class="main_dataDiv" id="main_dataDiv">' . "\n" .
			'<div class="bck_btn">' . "\n" .
			'<input type="button" id="bck_btn" name="all_docs" value="Show all unread documents/appendixes" onclick="Javascript:window.location=\'main.php?showNVDocuments=True\';">' . "\n" .
			'</div>' . "\n" .
			'<span class="main_subtitle"><i><b>Uploaded, approved documents:</b></i></span>' . "\n" .
			'<div id="jqxgridDocs">' . "\n" .
			'</div>' . "\n" .
			'</div>' . "\n" .
			'<div class="main_dataDiv" id="main_dataDiv_p">' . "\n" .
			'<span class="main_subtitle"><i><b>Appendix(es) for the selected document:</b></i></span>' . "\n" .
			'<div id="jqxgridPrilog">' . "\n" .
			'</div>' . "\n" .
			'</div>';
			
	require_once('documents_hst_form.php');
	
	$documents_my=	'<div class="main_mainContainerDiv">' . "\n" .
					'<div class="main_containerDiv">' . "\n" .
					'<div class="main_titleDiv">' . "\n" .
					'<span class="main_title">List of the<br>uploaded, approved documents</span>' . "\n" .
					'<span class="main_titleDiv_sp"><img src="img/documents.png" alt="Documents"></span>' . "\n" .
					'<span class="main_subtitle">You can find the list of the uploaded, approved and active documents in the table below.<br>' . "\n" .
					'Please, click on the \'View\' link beside the document\'s name to open the document!</span>' . "\n" .
					'<hr>' . "\n" .
					'</div>' . "\n" . 
					$data . "\n" .
					'</div>' . "\n" .
					'</div>';
?>