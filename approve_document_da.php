<?php
	//Ez a fajl akkor fut, amikor az approve linkre kattint a felhasznalo az Approve formon az elso tablazaton belul.
	session_start();
	
	//A parametereket kulon mappaban taroljuk
	require_once('params/params.php');
	//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
	require_once('connectvars/connectvars.php');
	//A mail kuldeshez a funkciokat tartalmazo fajl
	require_once('send_mail.php');
	
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
	$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
	mysqli_set_charset($conn,"utf8");

		
	if(isset($_POST['ID']) && isset($_POST['TYPE']) && isset($_POST['DOC']) && isset($_POST['DOC_APPROVED']) && isset($_POST['A_CMMT']))
	{
			
//APPROVAL INFORMATION UPDATE ON THE SELECTED DOCUMENT -->

		//A jovahagyott dokumentumra rateszi, hogy jova van hagyva es a jovahagyora es az idore vonatkozo informaciokat			
		$query_update="UPDATE tUploadedDocs SET DOC_APPROVED=" . $_POST['DOC_APPROVED'] . ", A_USR='" . $_SESSION['dms_username'] . "', A_DT=Now(), A_CMMT='" . $_POST['A_CMMT'] . "' WHERE ID=" . $_POST['ID'];
			
		$result=mysqli_query($conn, $query_update);
			
//IF APPROVAL SUCCEESS, MAIL AND MESSAGE -->
			
		if($result==1)
		{
			
//COLLECT USERS TO SEND THEM MAIL ABOUT THE NEW UPLOAD -->						
			$cc='';
			$to_list_users=array(); //Ezt fogja feltolteni a query es kuldeni azoknak, akiknek ga_approve joga van a feltoltott dokumentumra !!!GA!!! 2016.04.06
						
						
			//Kiszedi az adott tipusra a leirast
			$query_tp="SELECT DESCR FROM tDocTypes WHERE TYPE_ID='" . $_POST['TYPE'] . "'";
			$result_tp=mysqli_query($conn,$query_tp);
				
			while($row_tp=mysqli_fetch_array($result_tp))
			{
				$doc_descr=iconv('UTF-8','windows-1250',$row_tp['DESCR']);
			}				
						
//APPROVED BY DA						
			//Ha a zold pipat valasztotta ki a DA a jovahagyaskor, akkor a GA-nak megy az info, hogy hagyja jova az elkeszitett dokumentumot
			if($_POST['DOC_APPROVED']==1)
			{
							
				$query_usrs="SELECT UPDO.USR_ID " .
							"FROM tUsrPermissionsDO UPDO " .
							"JOIN tUsrs U ON UPDO.USR_ID=U.USR_ID " .
							"WHERE 1=1 " .
							"AND UPDO.USR_ID NOT IN ('xy','xy') " .
							"AND UPDO.APPROVE_GA_P=1 " .
							"AND U.ACTIVE=1 " .
							"AND UPDO.TYPE_ID='" . $_POST['TYPE'] . "' ";
										
				$result_usrs=mysqli_query($conn, $query_usrs);
							
				while($row_usrs=mysqli_fetch_array($result_usrs))
				{
					array_push($to_list_users, $row_usrs['USR_ID']);
				}
				//SET THE MAIL SUBJECT
				$subject='Document prepared: ' . $_POST['DOC'];
							
				//SET THE MAIL BODY
				$body='Dear GA!';
				$body.='<br><br>The new ' . $doc_descr . ' is prepared.';
				$body.='<br><br>Comment: ' . $_POST['A_CMMT'];
				$body.='<br><br><br>Please, click on the \'APPROVE\' button (Quality main menu-->Shared Folders-->DMS) on the main page<br>';
				$body.='and then click on the Approve link within the \'GA\' column to approve the document.';
							
				$cc=CC;
				
				$query_path="SELECT A_DT, PATH FROM tUploadedDocs WHERE ID=" . $_POST['ID'];
				$result_path=mysqli_query($conn,$query_path);
				if(mysqli_num_rows($result_path)==1)
				{
					while($row_path=mysqli_fetch_array($result_path))
					{
						$file_path='../' . substr($row_path['PATH'],4,strlen($row_path['PATH']));
						$lst_upd=$row_path['A_DT'];
					}
				}
							
				require_once('pdf.php');
				echo ModifyPDF($file_path,'phase_da', $file_path, $_SESSION['dms_username'], date('d.m.Y', strtotime($lst_upd)), '');
		
			}//if($_POST['DOC_APPROVED']==1) vege
						
//NOT APPROVED BY DA
			//Ha a piros X-et valasztotta ki a DA jovahagyaskor, akkor a feltoltonek megy az info, hogy nem fogadtak el a feltoltott dokumentumot
			if($_POST['DOC_APPROVED']==2)
			{
				//GET UPLOADER MAIL ADDRESS
				$query_u="SELECT U_USR FROM tUploadedDocs WHERE tUploadedDocs.ID=" . $_POST['ID'];
				$result_u=mysqli_query($conn,$query_u);
							
				while($row_u=mysqli_fetch_array($result_u))
				{
					array_push($to_list_users, $row_u['U_USR']);
				}
							
				//SET THE MAIL SUBJECT
				$subject='Document not approved by DA: ' . $_POST['DOC'];
				//SET THE MAIL BODY
				$body='Dear Uploader';		
				$body.='<br><br>The document ' . $doc_descr . ' was not approved by the DA.';
				$body.='<br><br>Comment: ' . $_POST['A_CMMT'];
			}
						
			$body.='<br><br><br><br><br><hr>This is an automatic e-mail message generated by the Quality system.<br>Please DO NOT RESPOND to this e-mail because the mail box is unattended.';

//SEND MAIL BASED ON THE CONFIGURED PARAMETERS -->

			echo sendMail($to_list_users, $subject, $body, $cc);

			$_SESSION['approve_success']= SUCCESS_UPL_MSG_BEF . 'Successfully changed!' . SUCCESS_UPL_MSG_AFT;
		}//if($result==1) vege 
			
//ELSE, ROLLBACK APPROVAL -->

		else
		{
			//Ha nincs egy rekord sem, amit az update query visszaadott volna, akkor az azt jelenti, hogy a dokumentum melletti approve mezok nem lettek modositva, 
			//hibauzenetet adni a felhasznalonak	
							
			$_SESSION['approve_error']= ERROR_UPL_MSG_BEF . 'An error occured, please try again!' . ERROR_UPL_MSG_AFT;
		}
				
	}//if(isset($_POST['ID']) && isset($_POST['TYPE']) && isseT($_POST['DOC'])) vege
?>
