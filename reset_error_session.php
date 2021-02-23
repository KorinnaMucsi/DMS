<?php
	
	session_start();

//RESETS ALL THE SESSIONS IF THE USER GOES TO THE MAIN MENU -->
	
	if(isset($_SESSION['upload_error']))
	{
		$_SESSION['upload_error']=$_POST['st'];
		unset($_SESSION['upload_error']);
	}	
	
	if(isset($_SESSION['upload_success']))
	{
		$_SESSION['upload_success']=$_POST['st'];
		unset($_SESSION['upload_success']);
	}	
	
	if(isset($_SESSION['approve_error']))
	{
		$_SESSION['approve_error']=$_POST['st'];
		unset($_SESSION['approve_error']);
	}	
	
	if(isset($_SESSION['approve_success']))
	{
		$_SESSION['approve_success']=$_POST['st'];
		unset($_SESSION['approve_success']);
	}
	if(isset($_SESSION['fn_error']))
	{
		$_SESSION['fn_error']=$_POST['st'];
		unset($_SESSION['fn_error']);
	}	
	
	if(isset($_SESSION['fn_success']))
	{
		$_SESSION['fn_success']=$_POST['st'];
		unset($_SESSION['fn_success']);
	}
	
	if(isset($_SESSION['bs_success']))
	{
		$_SESSION['bs_success']=$_POST['st'];
		unset($_SESSION['bs_success']);
	}
	
	if(isset($_SESSION['bs_error']))
	{
		$_SESSION['bs_error']=$_POST['st'];
		unset($_SESSION['bs_error']);
	}	
	if(isset($_SESSION['fns_error']))
	{
		$_SESSION['fns_error']=$_POST['st'];
		unset($_SESSION['fns_error']);
	}	
	
	if(isset($_SESSION['fns_success']))
	{
		$_SESSION['fns_success']=$_POST['st'];
		unset($_SESSION['fns_success']);
	}
	if(isset($_SESSION['p_error']))
	{
		$_SESSION['p_error']=$_POST['st'];
		unset($_SESSION['p_error']);
	}	
	if(isset($_SESSION['p_success']))
	{
		$_SESSION['p_success']=$_POST['st'];
		unset($_SESSION['p_success']);
	}	
	if(isset($_SESSION['wsu_success']))
	{
		$_SESSION['wsu_success']=$_POST['st'];
		unset($_SESSION['wsu_success']);
	}
	if(isset($_SESSION['wsu_error']))
	{
		$_SESSION['wsu_error']=$_POST['st'];
		unset($_SESSION['wsu_error']);
	}	
	
	if(isset($_SESSION['wsfn_success']))
	{
		$_SESSION['wsfn_success']=$_POST['st'];
		unset($_SESSION['wsfn_success']);
	}
	if(isset($_SESSION['wsfn_error']))
	{
		$_SESSION['wsfn_error']=$_POST['st'];
		unset($_SESSION['wsfn_error']);
	}	
	if(isset($_SESSION['t_error']))
	{
		$_SESSION['t_error']=$_POST['st'];
		unset($_SESSION['t_error']);
	}	
	if(isset($_SESSION['t_success']))
	{
		$_SESSION['t_success']=$_POST['st'];
		unset($_SESSION['t_success']);
	}
?>
