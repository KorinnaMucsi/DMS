<?php
	//Az approve_ga.js fajl hivja meg, hogy leellenorizze, hogy a DA jovahagyta-e mar a dokumentumot, mielott a GA jovahagyhatna (parameterkent az ID-t kuldi)

	session_start();
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");


//DA APPROVAL CONTROL QUERY -->
	
	$query=	"SELECT DOC_APPROVED AS APPR " .
			"FROM " .
			"tUploadedDocs " .
			"WHERE ID=" . $_POST['ID'];
			

	$result=mysqli_query($conn,$query);
	
//APPROVAL CONTROL -->
	
	//Leellenorizzuk, hogy az adott dokumentumot jovahagyta-e mar a DA
	while($row=mysqli_fetch_array($result))
	{
		$approve_da=$row['APPR'];
	}
	
	//Amennyiben nem hagyta jova a DA, akkor 0 az eredmeny, amit a query visszaad es hibauzenetet kap a felhasznalo (response_da az approve_ga.js fajlban)
	if($approve_da==0)
	{
		echo '0';
	}
	//Amennyiben jovahagyta a DA, jovahagyhatja a dokumentumot a GA is
	else
	{
		echo '1';
	}

?>
