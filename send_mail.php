<?php
	function sendMail($to_list_users, $subject, $body, $cc)
	{
	
		//A parametereket kulon mappaban taroljuk
		require_once('params/params.php');
		//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
		require_once('connectvars/connectvars.php');
	
		require_once('mail/PHPMailerAutoload.php');

		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
		$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
		mysqli_set_charset($conn,"utf8");

		$to_list=array(); //Array a felhasznalok neveivel
		$to_mail_list=array(); //Array a felhasznalok mail cimeivel
		
		foreach ($to_list_users as $users)
		{
				array_push($to_list, "'" . $users . "'");
				//Ez kiszedi a neveket a tEMail tablabol az array minden felhasznalojara
		}
		
		$query_clause=implode(",", $to_list);
		$query_m='SELECT MAIL_ADDR AS mailaddr FROM tMails WHERE USR_ID IN (' . $query_clause . ')';
				
		$result_m=mysqli_query($conn, $query_m);
		
			
		while ($row_m=mysqli_fetch_array($result_m))
		{	
			array_push($to_mail_list,$row_m['mailaddr']);
		}
			
		$to_m=implode(",", $to_mail_list); //A mailek listaja vesszovel elvalasztva
								
		$mail = new PHPMailer;
			 
		$mail->isSMTP();                                      	// Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';                       	// Specify main and backup server
		$mail->SMTPAuth = true;                               	// Enable SMTP authentication
		$mail->Username = '';   	// SMTP username
		$mail->Password = '';               			// SMTP password
		$mail->SMTPSecure = 'tls';                            	// Enable encryption, 'ssl' also accepted
		$mail->Port = 587;                                    	// Set the SMTP port number - 587 for authenticated TLS
		$mail->setFrom('', 'DMS');     		// Set who the message is to be sent from
			
		foreach ($to_mail_list as $to_address)
		{
			$mail->addAddress($to_address);               		// Name is optional
		}
		
		if($cc!='')
		{
			$mail->addCC($cc);
		}
		$mail->addBCC('');
		$mail->WordWrap = 50;                                 	// Set word wrap to 50 characters
		$mail->isHTML(true);                                  	// Set email format to HTML
			 
		$mail->Subject = $subject;
		$mail->Body    = $body;
			
		$mail->send();
	}

?>
