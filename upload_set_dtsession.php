<?php
	
	session_start();
	
	if(isset($_POST['dtp']) && $_POST['dtp']!='')
	{
		$_SESSION['dtp']=$_POST['dtp'];
	}

?>
