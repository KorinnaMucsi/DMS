<?php

/*
Created by: Mucsi Korinna
Date of creation: 07.05.2015.
Description: The following file is used to manage the user logons and logoffs
*/	
	session_start();
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	$error_msg="";
	
//************************************************************************** Ha meg nincs bejelentkezese a felhasznalonak **************************************************************************	
	if (!isset($_SESSION['dms_username']) && !isset($_COOKIE['dms_username']))
	{	

//SUBMIT-->
	
		//Submit ellenorzes - mi kell, hogy tortenjen, ha a felhasznalo az OK gombra kattintott
		if (isset($_POST['submit']))
		{
			$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
			$selected = mysqli_select_db($conn, DB_NAME); 
			mysqli_set_charset($conn,"utf8");


			//Kiszedi a mezokbol a felhasznalonevet es jelszot a kesobbi ellenorzesekhez
			$username=mysqli_real_escape_string($conn,trim($_POST['username']));
			$password=mysqli_real_escape_string($conn,trim($_POST['password']));
			$job_title=mysqli_real_escape_string($conn,trim($_POST['selRole']));
			
			//Ha nem uresek a mezok, akkor lehet osszehasonlitani az betoltott adatokat a bazis adataival
			if (!empty($username) && !empty($password) && !empty($job_title))
			{
				$query="SELECT * FROM tUsrs WHERE USR_ID='" . $username . "' AND SHA(PWD)=SHA('" . $password . "')";
				$result=mysqli_query($conn, $query);
				
				if (mysqli_num_rows($result)==1)
				{	
					$row=mysqli_fetch_array($result);
					$act=$row['ACTIVE'];

//PERMISSIONS FOR ACTIVE USERS-->

					if ($act==1)
					{
//SAVE USER LOGIN TO DATABASE-->

						$_SESSION['dms_username']=$row['USR_ID'];
						setcookie('dms_username',$row['USR_ID'],time()+(30*60*60*24));
							
						$_SESSION['job_title']=$job_title;
						setcookie('job_title',$job_title,time()+(30*60*60*24));
								
						//Beirja a felhasznalomevet, bejelentkezes idejet es az IP cimet a bazisba
						$query_login="INSERT INTO tUsrLogs (USR_ID, LOGON_DT, IP_ADDR) VALUES ('" . $_SESSION['dms_username'] . "', now(), '" . $_SERVER['REMOTE_ADDR'] . "')";
						$result_login=mysqli_query($conn, $query_login);
							
						if (!isset($_SESSION['dms_login_id']) && $result_login==1)
						{
							//Meg kell keresni, hogy mi a maximum ID az adott felhasznalo neve mellett a bazisban, hogy beirodjon a szesszioba es a sutibe.
							//A kijelentkezesnel szukseg van erre az ID-ra, hogy megtalaljuk a bejeletkezes part
							$query_max_login="SELECT MAX(ID) AS MaxID, MAX(LOGON_DT) AS MaxLogon FROM tUsrLogs WHERE USR_ID='" . $_SESSION['dms_username'] . "'";
							$result_max_login=mysqli_query($conn, $query_max_login);
									
							while ($row_max_login=mysqli_fetch_array($result_max_login))
							{
//LOGIN ID (SESSION & COOKIE)-->
	
								$_SESSION['dms_login_id']=$row_max_login['MaxID'];	
								setcookie('dms_login_id', $row_max_login['MaxID'],time()+(30*60*60*24));
										
								$_SESSION['dms_max_logon']=$row_max_login['MaxLogon'];
								setcookie('dms_max_logon', $row_max_login['MaxLogon'], time()+(30*60*60*24));
							}
									
						}

//REDIRECT TO THE MAIN PAGE -->
							
						//Atiranyitas a fo oldalra, ha sikeres a bejelentkezes
						header('Location:main.php?showMainMenu=True');
					}
					else
					{
						$error_msg="You don't have permission to log in (user not active)!";
					}//if ($act==1) vege
				}
				else
				{
					$error_msg="You must enter a valid username and password.";
				}//if (mysqli_num_rows($result)==1) vege
			}
			else
			{
				$error_msg="You must enter your username and password.";
			}//if (!empty($username) && !empty($password)) vege		 			
		}		
	}
//****************************************************************************** Ha mar van bejelentkezese a felhasznalonak **************************************************************************	
	
//GET STORED DATA FROM SESSIONS AND COOKIES IF USER IS ALREADY LOGGED IN -->	
	
	else
	{
		//Betoltjuk a szukseges valtozokat a szessziokba, sutikbe mentett adatokbol
		if (isset($_COOKIE['dms_username']))
		{
			$_SESSION['dms_username']=$_COOKIE['dms_username'];
		}
		if (isset($_COOKIE['dms_login_id']))
		{
			$_SESSION['dms_login_id']=$_COOKIE['dms_login_id'];
		}
		if (isset($_COOKIE['dms_max_logon']))
		{
			$_SESSION['dms_max_logon']=$_COOKIE['dms_max_logon'];
		}
		if (isset($_COOKIE['job_title']))
		{
			$_SESSION['job_title']=$_COOKIE['job_title'];
		}
	}//if (isset($_POST['submit'])) vege

?>

<!DOCTYPE html>
<html>

<head>
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<link href="css/login.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.11.2.js"></script>
<script src="js/login.js" type="text/javascript"></script>
<title>DMS - Login</title>
</head>

<body class="login_body">
<?php

	if (empty($_SESSION['dms_username']) || !isset($_SESSION['dms_username']) || $_SESSION['dms_username']=='')
	{
?>
		<div class="login_containerDivBody">
		<h1 class="login_h1">Document Management System</h1><h1 class="login_h1">Please, Log In</h1>
		<form id="frmLogin" method="post" action="<?php $_SERVER['PHP_SELF']; ?>">
		<div class="login_centerDiv">
				<img class="login_logo" src="<?php echo LOGO; ?>" alt="logo">
				<?php echo '<p class="login_error">' . $error_msg . '</p>'; ?>
				<fieldset class="login_fieldset">
				<label class="login_label" for="username">USERNAME:</label>
				<input type="text" class="login_text" name="username" id="username" autofocus>
				<label class="login_label" for="password">PASSWORD:</label>
				<input type="password" class="login_pwd" id="password" name="password">
				<label class="login_label" for="selRole">JOB TITLE:</label>
				<div class="login_input_div" id="selRoleDiv">
				<select name="selRole" id="selRole" class="login_select">
				<option value="">----- Please, select -----</option>
				</select>
				</div>
				<input type="submit" id="login_submit" name="submit" value="Log In">
				</fieldset>
		</div>
		</form>
		</div>
<?php
	}
	else
	{
		echo '<div class="login_containerDivBody">';
		echo '<h1 class="login_h1">Document Management System</h1><h1 class="login_h1">Logon information</h1>';
		echo '<div class="login_centerDiv">';
		echo '<img class="login_logo" src="' . LOGO . '" alt="logo">';
		echo '<p id="login"><span class="login_span">You are logged in as \'' . $_SESSION['dms_username'] . '\'. </span>' .
		'<a href="main.php?showMainMenu=True">Click here</a> to navigate to the main page ' .
		'<span class="login_span">or <a href="#" onclick="UserLogout();">click here</a> to sign out if you are not \'' . $_SESSION['dms_username'] . '\'.</span></p>' ;
		echo '</div>';
		echo '</div>';
	}
?>
</body>

</html>
