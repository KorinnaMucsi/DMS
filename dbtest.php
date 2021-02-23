<!DOCTYPE html>
<html>

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>Untitled 1</title>
</head>

<body>

</body>

</html>

<?php	
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"UTF-8");

	$query="SELECT * FROM tDocTypes";
	$result=mysqli_query($conn, $query);
	
	while($row=mysqli_fetch_array($result))
	{
		echo '<br>' . $row['DESCR'] . '</br>';
	}
?>