<?php

/*
Created by: Mucsi Korinna
Date of creation: 07.05.2015.
Description: The following file is used to show who is logged on + sign out
*/
/*	
	Ez a resz nem kell ide, mivel barki bemehet a menupontba, akinek van jogosultsaga az APPROVE menupontra is
	require_once('get_profile_permission.php');
	
	$permission=GetProfilePermission('AS0005',$_SESSION['dms_username']);
			
	if($permission==0)
	{
		DEFINE('USRS_MAINT','Javascript:alert(\'For Administrators only!\');');
	}
	else
	{
		DEFINE('USRS_MAINT','Javascript:document.location.href=\'main.php?showUsers=True\';');
	}
*/

//DOCUMENT HEADER AND SIDE MENU - FIXED -->

	$hdr=	'<div id="hdr_div" class="hdr_div">' . "\n" .
				'<div class="hdr_userinfo">' . "\n" . 
					'You are logged in as: <b>' . $_SESSION['dms_username'] . '</b><br>'. "\n" .
					'<a id="sign_out" href="#" onclick="Javascript:UserLogout();">Sign out</a>' . "\n" .
				'</div>';
	
	$hdr.=	'<div id="hdr_menu" class="hdr_menu">' . "\n" .
			'<button id="showLeft">Show/Hide Left Slide Menu</button>' . "\n" .
			'<nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="cbp-spmenu-s1">' . "\n" .
			'<h3 id="main_menu" onclick="Javascript:OpenMainMenu();">Menu</h3>' . "\n" .
			'<a href="main.php?showNewUpload=True">UPLOAD</a>' . "\n" .
			'<a href="main.php?showApprove=True">APPROVE</a>' . "\n" .
			'<button id="showDocs">DOCUMENTS<img id="docsimage" class="downmenu" src="img/down.png"/></button>' . "\n" .
			'<ul class="cbp-submenu" id="cbp-submenu-docs">' . "\n" .
			'<li id="mydocs" onclick="Javascript:document.location.href=\'main.php?showMyDocuments=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">My Documents</span></li>' . "\n" .
			'<li id="alldocs" onclick="Javascript:document.location.href=\'main.php?showAllDocuments=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">All Documents</span></li>' . "\n" .
			'</ul>' . "\n" .
			'<button id="showReps">REPORTS<img id="repsimage" class="downmenu" src="img/down.png"/></button>' . "\n" .
			'<ul class="cbp-submenu" id="cbp-submenu-reps">' . "\n" .
			'<li id="repDocs" onclick="Javascript:document.location.href=\'main.php?showRepDocs=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">Documents</span></li>' . "\n" .
			'<li id="repDTP" onclick="Javascript:document.location.href=\'main.php?showRepDTP=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">Document types</span></li>' . "\n" .
			'<li id="repUsrsJobs" onclick="Javascript:document.location.href=\'main.php?showRepUsrsJobs=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">Users - Job titles</span></li>' . "\n" .
			'</ul>' . "\n" .
			'<button id="showMaint">MAINTENANCE<img id="maintimage" class="downmenu" src="img/down.png"/></button>' . "\n" .
			'<ul class="cbp-submenu" id="cbp-submenu">' . "\n" .
			'<li id="usrs" onclick="Javascript:document.location.href=\'main.php?showUsers=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">Users</span></li>' . "\n" .
			'<li id="usrs" onclick="Javascript:document.location.href=\'main.php?showFns=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">Job Titles</span></li>' . "\n" .
			'<li id="ws_usrs" onclick="Javascript:document.location.href=\'main.php?showWSUsers=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">Assign temp. job title</span><span class="submenuicon_nextline">to user</span></li>' . "\n" .
			'<li id="ws_fns" onclick="Javascript:document.location.href=\'main.php?showWSFns=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">Assign job title</span><span class="submenuicon_nextline">to workstation</span></li>' . "\n" .
			'<li id="documents" onclick="Javascript:document.location.href=\'main.php?showTypes=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">Document types</span></li>' . "\n" .
			'<li id="appendix" onclick="Javascript:document.location.href=\'main.php?showPTypes=True\';"><img class="submenuicon" src="img/right.png" alt="right">' .
			'<span class="submenulabel">Appendix types</span></li>' . "\n" .
			'</ul>' . "\n" .
			'<a href="#" onclick="Javascript:UserLogout();">SIGN OUT</a>' . "\n" .
			'</nav>' . "\n" .
			'<script src="js/classie.js"></script>' . "\n" .
			'<script>' . "\n" .
			'var menuLeft = document.getElementById("cbp-spmenu-s1"),' . "\n" .
			'body = document.body;' . "\n" .
			'showLeft.onclick = function() {' . "\n" .
			'classie.toggle( this, "active" );' . "\n" .
			'classie.toggle( menuLeft, "cbp-spmenu-open" );' . "\n" .
			'disableOther( "showLeft" );' . "\n" .
			'};' . "\n" .
			'var menuMaint = document.getElementById("cbp-submenu"),' . "\n" .
			'body = document.body;' . "\n" .
			'var menuReps = document.getElementById("cbp-submenu-reps"),' . "\n" .
			'body = document.body;' . "\n" .
			'var menuDocs = document.getElementById("cbp-submenu-docs"),' . "\n" .
			'body = document.body;' . "\n" .
			'showMaint.onclick = function() {' . "\n" .
			'var nm=document.getElementById("maintimage").src; ' . "\n" .
			'var fileNameIndex = nm.lastIndexOf("/") + 1; ' . "\n" .
			'var filename = nm.substr(fileNameIndex); ' . "\n" .
			'if(filename==\'down.png\') ' . "\n" .
			'{' . "\n" .
			'document.getElementById("maintimage").src="img/up.png";' . "\n" .			
			'}' . "\n" .
			'else' . "\n" .
			'{' . "\n" .
			'document.getElementById("maintimage").src="img/down.png";' . "\n" . 
			'};' . "\n" .
			'classie.toggle( this, "active" );' . "\n" .
			'classie.toggle( menuMaint, "cbp-submenu-open" );' . "\n" .
			'};' . "\n" .
			'showReps.onclick = function() {' . "\n" .
			'var nm=document.getElementById("repsimage").src; ' . "\n" .
			'var fileNameIndex = nm.lastIndexOf("/") + 1; ' . "\n" .
			'var filename = nm.substr(fileNameIndex); ' . "\n" .
			'if(filename==\'down.png\') ' . "\n" .
			'{' . "\n" .
			'document.getElementById("repsimage").src="img/up.png";' . "\n" .			
			'}' . "\n" .
			'else' . "\n" .
			'{' . "\n" .
			'document.getElementById("repsimage").src="img/down.png";' . "\n" . 
			'};' . "\n" .
			'classie.toggle( this, "active" );' . "\n" .
			'classie.toggle( menuReps, "cbp-submenu-open" );' . "\n" .
			'};' . "\n" .
			'showDocs.onclick = function() {' . "\n" .
			'var nm=document.getElementById("docsimage").src; ' . "\n" .
			'var fileNameIndex = nm.lastIndexOf("/") + 1; ' . "\n" .
			'var filename = nm.substr(fileNameIndex); ' . "\n" .
			'if(filename==\'down.png\') ' . "\n" .
			'{' . "\n" .
			'document.getElementById("docsimage").src="img/up.png";' . "\n" .			
			'}' . "\n" .
			'else' . "\n" .
			'{' . "\n" .
			'document.getElementById("docsimage").src="img/down.png";' . "\n" . 
			'};' . "\n" .
			'classie.toggle( this, "active" );' . "\n" .
			'classie.toggle( menuDocs, "cbp-submenu-open" );' . "\n" .
			'};' . "\n" .
			'function disableOther( button ) {' . "\n" .
			'if( button !== "showLeft" ) {' . "\n" .
			'classie.toggle( showLeft, "disabled" );' . "\n" .
			'}' . "\n" .
			'}' . "\n" .
			'</script>' . "\n" . 
			'</div>' . "\n";
	$hdr.='</div>';

?>
