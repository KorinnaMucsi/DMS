<?php	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	
	//Az adott profilra visszaadja, hogy az adott felhasznalonak ($user_id) van-e jogosultsaga a menuponthoz ($perm_id), ha nincs ledefinialva a profilja(0 profil) 
	//a tPermissionProfiles tablaban, akkor 0-t ad vissza es alapbol nincs hozzaferese, hibauzenetet kap.
	function GetProfilePermission($perm_id, $user_id)
	{
		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
		$selected = mysqli_select_db($conn, DB_NAME); 
		mysqli_set_charset($conn,"utf8");
		$profile='P0';
	
		$query="SELECT PROFILE FROM tUsrs WHERE USR_ID='" . $user_id . "'";
		$result=mysqli_query($conn, $query);
		
		while($row=mysqli_fetch_array($result))
		{
			$profile='P' . $row['PROFILE'];
		}
		
		if($profile!='P0')
		{
			$query_p="SELECT " . $profile . " AS p FROM tProfilePermissions WHERE PERM_ID='" . $perm_id . "'";
			$result_p=mysqli_query($conn,$query_p);
			
			while($row_p=mysqli_fetch_array($result_p))
			{
				$permission=$row_p['p'];
			}
		}
		else
		{
			$permission=0;
		}
		
		return $permission;
	}
	
?>