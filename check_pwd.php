<?php
	session_start();
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
			
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
	$selected = mysqli_select_db($conn, DB_NAME); 
	mysqli_set_charset($conn,"utf8");


	//Kiszedi a mezokbol a felhasznalonevet es jelszot a kesobbi ellenorzesekhez
	$username=$_SESSION['dms_username'];
	$password=mysqli_real_escape_string($conn,trim($_POST['PWD']));
			
	//Ha nem uresek a mezok, akkor lehet osszehasonlitani az betoltott adatokat a bazis adataival
	if (!empty($username) && !empty($password))
	{
		$query="SELECT * FROM tUsrs WHERE USR_ID='" . $username . "' AND SHA(PWD)=SHA('" . $password . "')";
		$result=mysqli_query($conn, $query);
				
		if (mysqli_num_rows($result)==1)
		{	
			echo '1';
		}
		else
		{
			echo '0';
		}
	}
	else
	{
		echo '2';
	}

?>
