<?php

	function ModifyPDF($source_file, $phase_for_visibility, $destination_file, $user, $date, $security)
	{
		//A parametereket kulon mappaban taroljuk
		require_once('params/params.php');
		//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
		require_once('connectvars/connectvars.php');
		
		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
		$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
		mysqli_set_charset($conn,"utf8");

		$query_usr=	"SELECT " .
					"CASE F.HR_FUNC_SHORT WHEN '' THEN F.HR_FUNCTION ELSE F.HR_FUNC_SHORT END AS HR_FUNCTION " . 
					"FROM tUsrs U " . 
					"JOIN tHRFunctions F " . 
					"ON U.HR_FUNC_ID=F.ID " .
					"WHERE 1=1 " .
					"AND USR_ID='" . $user . "'";
	
		$result_usr=mysqli_query($conn,$query_usr);
		
		if(mysqli_num_rows($result_usr)==1)
		{
			while($row_usr=mysqli_fetch_array($result_usr))
			{
				$user_function=$row_usr['HR_FUNCTION'];
			}
		}
		
		$query_usrnm="SELECT CONCAT(L_NAME,' ', F_NAME) AS U_NAME FROM tUsrs WHERE USR_ID='" . $user . "'";
		$result_usrnm=mysqli_query($conn,$query_usrnm);
		
		if(mysqli_num_rows($result_usrnm)==1)
		{
			while($row_usrnm=mysqli_fetch_array($result_usrnm))
			{
				$user_name=$row_usrnm['U_NAME'];
			}
		}
		
		$signature_file_path="signatures/" . $user . ".png";
		
		require_once('FPDF/fpdf.php');
		require_once('FPDI/fpdi.php');
		require_once('FPDI/FPDI_Protection.php');
		
		/*
			A $phase_for_visibility alapjan donti el a program, hogy melyik oszlopot rajzolja ki, illetve melyiket adja hozza a mar meglevo tablazathoz
			--phase_up-- maga a feltoltes, ekkor az elso ket oszlop kerul ra (a sorok jelentese illetve az IZRADIO informacio)
			--phase_da-- maga a DA altali jovahagyas, ekkor a haramdik oszlop kerul ra (a SAGLASAN informacio)
			--phase_ga-- maga a GA altali jovahagyas, ekkor a negyedik oszlop kerul ra (az ODOBRIO informacio)
		*/ 
		
		$pdf = new FPDI_Protection();
		
		//Beallitja, hogy hol van az a fajl, amibol a modositottat szeretnenk csinalni
		$pageCount = $pdf->setSourceFile($source_file);
		
		//Vegigmegy minden oldalon miutan megallapitotta, hogy hany oldalas az eredeti fajl es minden oldalat atmasol az uj fajlba,
		//valamint a FPDF/fpdf.pdf fajlban talalhato Footer(funkcio rateszi a kepet oda, ahova mondjuk az oldalon belul)
		for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) 
		{
		    $tplIdx = $pdf->importPage($pageNo);
		
		    // add a page
		    $pdf->AddPage();
		    $pdf->useTemplate($tplIdx, null, null, 0, 0, true);
			
			$pdf->AddFont('DejaVuSans','','DejaVuSans.php');
			$pdf->SetFont('DejaVuSans','',8);
			
		    //A tablazatot csak az elso oldalra kell ratenni, mig a 'Odštampana kopija ovog dokumenta je NEKONTROLISANA kopija, za aktuelnu verziju proverite DSM aplikaciju' 
		    //szoveget minden oldal alljara
		    if($pageNo==1)
		    {
				// Select Arial italic 8
				//$pdf->SetFont('Arial', '', 8);		
				// Go to 6 cm from bottom
				$pdf->SetY(-70);
				
			    $user_name=iconv('UTF-8','windows-1250',$user_name);
			    $user_function=iconv('UTF-8','windows-1250',$user_function);
			    
				//Feltolteskor kirajzolt oszlopok 
				if($phase_for_visibility=='phase_up')	
				{					
					$pdf->SetX(10);
					$pdf->Cell(40,5,'',1,0,'L',false);
					$pdf->Cell(50,5,'IZRADIO',1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(10);
					$pdf->Cell(40,5,'Ime',1,0,'L',false);
					$pdf->Cell(50,5,$user_name,1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(10);
					$pdf->Cell(40,5,'Funkcija',1,0,'L',false);
					$pdf->Cell(50,5,$user_function,1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(10);
					$pdf->Cell(40,5,'Datum',1,0,'L',false);
					$pdf->Cell(50,5,$date,1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(10);
					$pdf->Cell(40,10,'Potpis',1,0,'L',false);
					$pdf->Cell(50,10,$pdf->Image($signature_file_path,$pdf->GetX(), $pdf->GetY(),50),1,0,'C',false);
				}
				
				//DA jovahagyaskor kirajzolt oszlop
				if($phase_for_visibility=='phase_da')	
				{
					$pdf->SetX(100);
					$pdf->Cell(50,5,'SAGLASAN',1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(100);
					$pdf->Cell(50,5,$user_name,1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(100);
					$pdf->Cell(50,5,$user_function,1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(100);
					$pdf->Cell(50,5,$date,1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(100);
					$pdf->Cell(50,10,$pdf->Image($signature_file_path,$pdf->GetX(), $pdf->GetY(),50),1,0,'C',false);
				}
				
				//GA jovahagyaskor kirajzolt oszlop
				if($phase_for_visibility=='phase_ga')	
				{
					$pdf->SetX(150);
					$pdf->Cell(50,5,'ODOBRIO',1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(150);
					$pdf->Cell(50,5,$user_name,1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(150);
					$pdf->Cell(50,5,$user_function,1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(150);
					$pdf->Cell(50,5,$date,1,0,'L',false);
					$pdf->Ln();
					$pdf->SetX(150);
					$pdf->Cell(50,10,$pdf->Image($signature_file_path,$pdf->GetX(), $pdf->GetY(),50),1,0,'C',false);					
				}//if GA vege
			}//if $pageNo==1 vege	
			
			//Csak akkor kerul minden oldal alljara a lenti szoveg, ha a GA jovahagyta				
			if($phase_for_visibility=='phase_ga')	
			{
				// Go to 2 cm from bottom
			    $txt=iconv('UTF-8','windows-1250','Odštampana kopija ovog dokumenta je NEKONTROLISANA kopija, za aktuelnu verziju proverite DMS aplikaciju');
				$pdf->SetX(0);
				$pdf->SetY(-30);
				$pdf->Cell(100,5,$txt,'',0,'L',false);
			}
		}//for ciklus vege
		
		if($phase_for_visibility=='phase_ga')
		{
			//Amikor a GA hagyja jova, akkor iras es nyomtatas vedett lesz a fajl es nem lehet tobbet hozzaferni					
			if($security=='All')
			{
				$pdf->SetProtection(array());
			}
			if($security=='AllowPrintOnly')
			{
				$pdf->SetProtection(array('print'));
			}		
		}		

		//Az elkeszitett fajl neve (a D parameter azt jelenti, hogy letolti, az F pedig, hogy nem csinal vele semmit)
		$pdf->Output($destination_file, 'F');
	}//funkcio vege
?>