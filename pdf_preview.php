<?php

	if(isset($_GET['unm']) && isset($_GET['ufn']) && isset($_GET['sign']))
	{
			
		require_once('FPDF/fpdf.php');
	
		$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->AddFont('DejaVuSans','','DejaVuSans.php');
		$pdf->SetFont('DejaVuSans','',8);
		
		//$pdf->SetY(-70);
	
		$user_name=iconv('UTF-8','windows-1250',$_GET['unm']);
		$user_function=iconv('UTF-8','windows-1250',$_GET['ufn']);
		$date=date('d.m.Y');
		
		$filename=$_GET['sign'];
		
		if (file_exists($filename)) 
		{
		    $signature_file_path=$_GET['sign'];
		}
		else 
		{
		    $signature_file_path='NoSignature';
		}		
		

		$pdf->SetX(10);
		$pdf->Cell(40,5,'',1,0,'L',false);
		$pdf->Cell(50,5,'IZRADIO',1,0,'L',false);
		$pdf->Cell(50,5,'SAGLASAN',1,0,'L',false);
		$pdf->Cell(50,5,'ODOBRIO',1,0,'L',false);
		$pdf->Ln();
		$pdf->SetX(10);
		$pdf->Cell(40,5,'Ime',1,0,'L',false);
		$pdf->Cell(50,5,$user_name,1,0,'L',false);
		$pdf->Cell(50,5,'',1,0,'L',false);
		$pdf->Cell(50,5,'',1,0,'L',false);
		$pdf->Ln();
		$pdf->SetX(10);
		$pdf->Cell(40,5,'Funkcija',1,0,'L',false);
		$pdf->Cell(50,5,$user_function,1,0,'L',false);
		$pdf->Cell(50,5,'',1,0,'L',false);
		$pdf->Cell(50,5,'',1,0,'L',false);
		$pdf->Ln();
		$pdf->SetX(10);
		$pdf->Cell(40,5,'Datum',1,0,'L',false);
		$pdf->Cell(50,5,$date,1,0,'L',false);
		$pdf->Cell(50,5,'',1,0,'L',false);
		$pdf->Cell(50,5,'',1,0,'L',false);
		$pdf->Ln();
		$pdf->SetX(10);
		$pdf->Cell(40,10,'Potpis',1,0,'L',false);
		if($signature_file_path!='NoSignature')
		{
			$pdf->Cell(50,10,$pdf->Image($signature_file_path,$pdf->GetX(), $pdf->GetY(),50),1,0,'C',false);
		}
		else
		{
			$pdf->Cell(50,10,'',1,0,'C',false);
		}
		$pdf->Cell(50,10,'',1,0,'C',false);
		$pdf->Cell(50,10,'',1,0,'C',false);
		$pdf->Output('D');
	}
?>