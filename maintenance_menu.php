<?php

/*Created by: Mucsi Korinna
Date of creation: 30.05.2016.
Description: The following file is used to build up the maintenance menu's buttons
*/

//MEINTENANCE MENU BUTTONS -->
	
	$maintenance_menu=	'<div class="main_containerDivMenu">' . "\n" .
						'<div class="main_seprt_lns">' . "\n" .
							'<a href="main.php?showUsers=True"><img class="main_button" alt="Users" src="img/btns/maint_users.png"></a>' . "\n" .
							'<a href="main.php?showFns=True"><img class="main_button" alt="Functions" src="img/btns/maint_fns.png"></a>' . "\n" .
						'</div>' . "\n" .
						'<div class="main_seprt_lns">' . "\n" .
							/* Nincsenek tobbet csoportok, mivel a jogosultsag munkahely alapjan megy
							'<a href="#" onclick="Javascript:alert(\'For administrators only!\');"><img class="main_button" alt="Groups" src="img/btns/maint_groups.png"></a>' . "\n" .
							*/
							'<a href="main.php?showWSUsers=True"><img class="main_button" alt="WSUsers" src="img/btns/maint_wsusers.png"></a>' . "\n" .
							'<a href="main.php?showWSFns=True"><img class="main_button" alt="WSFns" src="img/btns/maint_wsfns.png"></a>' . "\n" .
							'<a href="main.php?showTypes=True"><img class="main_button" alt="Documents" src="img/btns/maint_documents.png"></a>' . "\n" .
							'<a href="main.php?showPTypes=True"><img class="main_button" alt="Appendix" src="img/btns/maint_appendix.png"></a>' . "\n" .
						'</div>' . "\n" .
						'</div>' . "\n";
?>
