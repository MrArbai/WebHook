<?php
 include 'fpdf.php';
 include 'exfpdf.php';
 include 'easyTable.php';

 $pdf=new exFPDF();
 $pdf->AddPage('L','A5'); 
 $pdf->SetFont('courier','',8);
 $pdf->SetMargins(0.5,0.5,0.5);

 $table1=new easyTable($pdf,'{15,20, 80, 22,4,40}',' border:1;border-color:#1a66ff;'); // 2 = column

 $table1->easyCell('', 'img:Pics/Logo_pulau_sambu.jpg, w20; align:L;rowspan:2;');
 $table1->easyCell('BUKTI PENGELUARAN BARANG', 'font-size:14; font-style:B;align:C;colspan:5');
 $table1->printRow();
 
//  $table1->easyCell('', 'img:Pics/Logo_pulau_sambu.jpg, w15; align:L;rowspan:2; colspan:2;');
 $table1->easyCell("<b>PT. Pulau Sambu</b>\n<i>Sungai Guntung - Inhil - Riau</i>",'colspan:2;');
 $table1->easyCell("BPB No \nBPB Date \nWarehouse \nDoc Request \nDepartment");
 $table1->easyCell(": \n: \n: \n: \n:");
 $table1->easyCell("0049/040/022018 \n01/02/2018 \nGdg WHS-PIS \n0009/CMP/022018 \nCoconut Milk Powder");
 $table1->printRow();

 $table1->endTable(6);

 // $tblDtl=new easyTable($pdf,'{80,15,30,10,20,40}',' border:0;border-color:#1a66ff;'); // 2 = column
 // $tblDtl->easyCell('');
 // $tblDtl->easyCell('');
 // $tblDtl->easyCell('');
 // $tblDtl->easyCell('BPB Date');
 // $tblDtl->easyCell(':');
 // $tblDtl->easyCell('01/02/2018');
 // $tblDtl->printRow();
 

//  $table1->rowStyle('font-size:10; font-style:B;');
//  $table1->easyCell('');
//  $table1->easyCell('');
//  $table1->easyCell('BPB', 'align:L;');
//  $table1->printRow();



 $pdf->Output(); 


 

?>