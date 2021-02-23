<?php

	//Az approve_ga.js fajl hivja meg, hogy leellenorizze, hogy van-e az adott felhasznalonak DA jovahagyo jogosultsaga az adott tipusu dokumentumhoz (a TYPE_ID valtozot kuldi parameterkent)

	session_start();
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


//APPROVAL PERMISSION CONTROL QUERY -->
	
	$query=	"SELECT SUM(APPROVE_P) AS APPR " .
			"FROM " .
			"tUsrPermissionsDO " .
			"WHERE USR_ID='" . $_SESSION['dms_username'] . "' " . 
			"AND TYPE_ID='" . $_POST['TYPE'] . "'";
			

	$result=mysqli_query($conn,$query);
	
//APPROVAL PERMISSION CONTROL -->
	
	//Leellenorizzuk, hogy van-e az adott felhasznalonak jogosultsaga jovahagyni a kivalasztott tipusu dokumentumot
	while($row=mysqli_fetch_array($result))
	{
		$approve_p=$row['APPR'];
	}
	
	//Amennyiben nincs jogosultsaga, akkor 0 az eredmeny, amit a query visszaad es hibauzenetet kap a felhasznalo (response az approve.js fajlban)
	if($approve_p==0)
	{
		echo '0';
	}
	//Amennyiben van jogosultsaga, jovahagyhatja a dokumentumot
	else
	{
		echo '1';
	}

?>
