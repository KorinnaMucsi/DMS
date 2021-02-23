<?php
	function History($tbl_nm, $col_id, $old_v, $new_v, $type_of_chng, $info, $fe, $usr_id)
	{
		require_once('connectvars/connectvars.php');

		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
		$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
		mysqli_set_charset($conn,"utf8");

		
		$query_insert_hst=	"INSERT INTO tHistory(TBL_NM, COL_ID, OLD_V, NEW_V, TYPE_OF_CHNG, INFO, FE, USR_ID) " .
		"SELECT '" . $tbl_nm . "', '" . $col_id . "', '" . $old_v . "', '" . $new_v . "', '" . $type_of_chng . "', '" . mysqli_real_escape_string($conn,$info) . "', '" .
		$fe . "','" . $usr_id . "'";
		mysqli_query($conn,$query_insert_hst);
	}
?>