<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/fpdf/fpdf.php';
require APPPATH . 'libraries/fpdf/exfpdf.php';
require APPPATH . 'libraries/fpdf/easyTable.php';


/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class RptPP extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->database();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        // $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        // $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        // $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function index_get()
    {
        $id = $this->get('id');

        // If the id parameter doesn't exist return all the users

        if ($id === NULL)
        {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Provide an id!'
                ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code

        } else {

                $sql = 'SELECT A.*,  
                        B.LoginName AS CreatedLoginName, B.PositionName AS CreatedPositionName, 
                        C.LoginName AS SignedLoginName1, C.PositionName AS SignedPositionName1, 
                        D.LoginName AS SignedLoginName2, D.PositionName AS SignedPositionName2, 
                        E.LoginName AS SignedLoginName3, E.PositionName AS SignedPositionName3, 
                        F.LoginName AS SignedLoginName4, F.PositionName AS SignedPositionName4, 
                        G.LoginName AS SignedLoginName5, G.PositionName AS SignedPositionName5, 
                        Z1.Stock AS StockPrimary1, Z1.UOMName AS UP1,Z2.Stock AS StockPrimary2,Z2.UOMName AS UP2, Z3.Stock AS StockPrimary3, Z3.UOMName AS UP3,  
                        X1.Stock AS StockSecondary1, X1.UOMName AS US1, X2.Stock AS StockSecondary2, X2.UOMName AS US2, X3.Stock AS StockSecondary3, X3.UOMName AS US3, 
                        X4.Stock AS StockSecondary4, X4.UOMName AS US4, X5.Stock AS StockSecondary5, X5.UOMName AS US5, X6.Stock AS StockSecondary6, X6.UOMName AS US6,  
                        X7.Stock AS StockSecondary7, X7.UOMName AS US7, X8.Stock AS StockSecondary8, X8.UOMName AS US8, Y1.Stock AS StockStickerPack, Y1.UOMName AS USP, 
                        Y2.Stock AS StockStickerOuter, Y2.UOMName AS USO, Y3.Stock As StockStickerInner, Y3.UOMName AS USI, Y4.Stock AS StockIsolasi, Y4.UOMName AS UIS, 
                        Y5.Stock AS StockStraw, Y5.UOMName AS UST, Y6.Stock As StockDunnage, Y6.UOMName AS UDN, Y7.Stock AS StockShrink , Y7.UOMName AS USH , Convert(varchar,Getdate(),103) as PrintedOn 
                        FROM MyPSG..vwTrnProductionPlanning A  
                        LEFT JOIN MyPSG..tblMstUser B ON A.CreateBy = B.LoginID  
                        LEFT JOIN MyPSG..tblMstUser C ON A.SignedBy1 = C.LoginID  
                        LEFT JOIN MyPSG..tblMstUser D ON A.SignedBy2 = D.LoginID  
                        LEFT JOIN MyPSG..tblMstUser E ON A.SignedBy3 = E.LoginID  
                        LEFT JOIN MyPSG..tblMstUser F ON A.SignedBy4 = F.LoginID  
                        LEFT JOIN MyPSG..tblMstUser G ON A.SignedBy5 = G.LoginID  
                        LEFT JOIN MyPSG..vwStockProductionPlanning Z1 ON A.Primary1 = Z1.OldItemID 
                        LEFT JOIN MyPSG..vwStockProductionPlanning Z2 ON A.Primary2 = Z2.OldItemID 
                        LEFT JOIN MyPSG..vwStockProductionPlanning Z3 ON A.Primary3 = Z3.OldItemID  
                        LEFT JOIN MyPSG..vwStockProductionPlanning X1 ON A.Secondary1 = X1.OldItemID  
                        LEFT JOIN MyPSG..vwStockProductionPlanning X2 ON A.Secondary2 = X2.OldItemID  LEFT JOIN MyPSG..vwStockProductionPlanning X3 ON A.Secondary3 = X3.OldItemID  
                        LEFT JOIN MyPSG..vwStockProductionPlanning X4 ON A.Secondary4 = X4.OldItemID 
                        LEFT JOIN MyPSG..vwStockProductionPlanning X5 ON A.Secondary5 = X5.OldItemID  
                        LEFT JOIN MyPSG..vwStockProductionPlanning X6 ON A.Secondary6 = X6.OldItemID  
                        LEFT JOIN MyPSG..vwStockProductionPlanning X7 ON A.Secondary7 = X7.OldItemID 
                        LEFT JOIN MyPSG..vwStockProductionPlanning X8 ON A.Secondary8 = X8.OldItemID  
                        LEFT JOIN MyPSG..vwStockProductionPlanning Y1 ON A.StickerProduct = Y1.OldItemID 
                        LEFT JOIN MyPSG..vwStockProductionPlanning Y2 ON A.StrickerOuter = Y2.OldItemID 
                        LEFT JOIN MyPSG..vwStockProductionPlanning Y3 ON A.StickerInner = Y3.OldItemID 
                        LEFT JOIN MyPSG..vwStockProductionPlanning Y4 ON A.Isolasi = Y4.OldItemID  
                        LEFT JOIN MyPSG..vwStockProductionPlanning Y5 ON A.Straw = Y5.OldItemID 
                        LEFT JOIN MyPSG..vwStockProductionPlanning Y6 ON A.Dunnage = Y6.OldItemID 
                        LEFT JOIN MyPSG..vwStockProductionPlanning Y7 ON A.Shrink = Y7.OldItemID  
                        WHERE A.TransID = ' . "'" . $id . "' " .
                        'order By A.Priority Desc, A.PoNumber, A.nourut' ;
                // $bpb = $this->db->get_where("vwBPB", ['BPBID' => $id])->result_array();
                // var_dump($sql);
                $PP = $this->db->query($sql,$id)->result_array();
                if (!$PP) {

                    $this->response([
                        'status' => FALSE,
                        'message' => 'NOT_FOUND!'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code   

                } else {
                    //$this->response($bpb, REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code  
                    $this->showbpb($PP);
                    //var_dump($bpb);
                }

        }


 
 

    }


        // private function savebpb($bpb) {

        //  $pdf=new exFPDF('l', 'mm', array(210,330));

        //  $pdf->AddPage(); 
        //  $pdf->SetFont('Arial','',8);


         
        //  $tahun = date("Y", strtotime($bpb[0]['TransDate']));
        //  $whsid = substr('000' . $bpb[0]['WHSID'], -3);
        //  $bulan = substr('00' . date("m", strtotime($bpb[0]['TransDate'])), -2);

        //  $path='\\\192.168.3.38\mypsg\E-DOC\TRANSACTION\\' . $tahun . '\\' . $bulan . '\\' . $whsid . '\\BPB\\API\\' ;
        //  $filename= 'Rptbpb_v01_' . str_replace('/','-',$bpb[0]['BPBID'])  . '.pdf';

        // if (!file_exists($path)) {
        //     mkdir($path, 0777, true);
        // }

        //  $this->header($pdf,$bpb);
        //  $this->data($pdf,$bpb);
        //  $this->footer($pdf,$bpb);
 
        //  $pdf->Output($path . $filename,'F'); 

        // }

        private function showbpb($PP) {

         $pdf=new exFPDF('l', 'mm', array(210,330));

         $pdf->AddPage(); 
         $pdf->SetFont('Arial','',8);
         
   

         $this->header($pdf,$PP);
         $this->data($pdf,$PP);
         // $this->footer($pdf,$PP);

         $pdf->Output(); 


        }


        private function header($pdf,$PP) {

                // $table1=new easyTable($pdf,'{25,20, 150, 30,5,40}',' border:0;border-color:#1a66ff;'); // 2 = column
             //    $table1->rowStyle('border:{T,B,L,R};');
             //    $table1->easyCell('', 'img:' . str_replace('C:','',APPPATH) . 'libraries/fpdf/Pics/logo_pulau_sambu.jpg, w25; align:L;rowspan:2;');
             //    $table1->easyCell('');
             //    $table1->easyCell('BUKTI PENGELUARAN BARANG', 'font-size:12; font-style:B;align:C;colspan:3');
             //    $table1->easyCell('');
             //    $table1->printRow();

             // $table1->easyCell("<b>PT. Pulau Sambu</b>\n<i>Sungai Guntung - Inhil - Riau</i>",'colspan:2;');
             // $table1->easyCell("BPB No \nBPB Date \nWarehouse \nDoc Request \nDepartment");
             // $table1->easyCell(": \n: \n: \n: \n:");
             // $table1->easyCell($bpb[0]['BPBID'] . " \n" . 
             //                    date("d/m/Y", strtotime($bpb[0]['TransDate'])) . " \n" . 
             //                    $bpb[0]['WHSName'] . " \n" . 
             //                    ($bpb[0]['PABID']==NULL?"-" : $bpb[0]['PABID']) . " \n" . 
             //                    $bpb[0]['DeptName']);
             // $table1->printRow();
             // $table1->endTable(10);


            $table1=new easyTable($pdf,'{47,47,47,47,47,23,24,47}',' border:0;border-color:#1a66ff;');  
            $table1->easyCell('Production Planning Coconut Cream', 'font-size:12; font-style:B;align:C;colspan:8');
            $table1->printRow();

            $table1->easyCell('PT. Pulau Sambu', 'font-size:8; font-style:B;align:L;colspan:2');
            $table1->easyCell('', 'colspan:4');
            $table1->easyCell('Document No ', 'align:L;colspan:0');
            $table1->easyCell(': ' . $PP[0]['DocNO'], 'colspan:0');
            $table1->printRow();

            $table1->easyCell('Sungai Guntung - Inhil - Riau', 'font-size:8; font-style:R;align:L;colspan:2');
            $table1->easyCell('', 'colspan:4');
            $table1->easyCell('Production  ', 'align:L;colspan:0');
            $table1->easyCell(': ' . date("d/m/Y", strtotime($PP[0]['ProductionDate'])) . ' - ' . date("d/m/Y", strtotime($PP[0]['ProductionDateEnd'])), 'colspan:0');
            $table1->printRow();

            $table1->easyCell('Telp: +62 - 779 - 552 888 Fax: +62 - 779 - 552 000', 'font-size:8; font-style:R;align:L;colspan:2');
            $table1->easyCell('', 'colspan:4');
            $table1->easyCell('Revision No ', 'align:L;colspan:0');
            $table1->easyCell( ': ' . $PP[0]['RevisionNumber'], 'colspan:0');
            $table1->printRow();

            $table1->easyCell('Email : general@psg.co.id, b.hadianto@psg.co.id', 'font-size:8; font-style:R;align:L;colspan:8');
            $table1->printRow();

            $table1->endTable(3);
            
        }

         private function Data($pdf,$PP) {

            $table1=new easyTable($pdf,'{47,47,47,47,47,47,47}',' border:1;border-color:#1a66ff;');  
            $table1->easyCell('Information Product', 'font-size:5; font-style:B;align:C;colspan:0;rowspan:2');
            $table1->easyCell('Format Date', 'font-size:5; font-style:B;align:CL;colspan:3');
            $table1->easyCell('Packaging', 'font-size:5; font-style:B;align:C;colspan:0;rowspan:2');
            $table1->easyCell('More Packaging', 'font-size:5; font-style:B;align:C;colspan:0;rowspan:2');
            $table1->easyCell('Remark', 'font-size:5; font-style:B;align:C;colspan:0;rowspan:2');
            $table1->printRow();

            $table1->easyCell('Pack/ Bag', 'font-size:5; font-style:B;align:C;colspan:0');
            $table1->easyCell('Carton Box / Drum', 'font-size:5; font-style:B;align:C;colspan:0');
            $table1->easyCell('Inner', 'font-size:5; font-style:B;align:C;colspan:0');
            $table1->printRow();

            $Nomor =0;


            $Information_Product = '';
            $Pack_Bag = '';
            $CartonBox_Drum = '';
            $Inner = '';
            $Packaging = '';
                $PrimaryPackaging = '';
                $SecondPackaging = '';
                $ThirdPackaging = '';
            $More_Packaging = '';
            $Remark = '';

            foreach ($PP as $row) {
                $Nomor = $Nomor + 1 . '.';

                $Information_Product = 'Product ID : ' . "\n" . 
                                  $row['ProductID'] . "\n" . 
                                  'Product Name : ' . "\n" . 
                                  $row['ProductName'] . "\n" . 
                                  'Buyer : ' . "\n" . 
                                  $row['BuyerName'] . "\n" .
                                  'Quantity : ' . $row['Qnty'] . ' ' . $row['UOMQntyName']  . "\n" .
                                  'Shelf Life : ' . $row['ShelfLife']  . ' Month' . "\n" .
                                  'Formula ID : ' . $row['Formula']  . "\n"  .
                                  'Formula Deskripsi : ' . $row['FormulaDesc'] ;
                $Pack_Bag =  $row['FormatPack'] ;
                $CartonBox_Drum =  $row['FormatCarton'] ;
                $Inner =  $row['FormatInner'] ;
                    $PrimaryPackaging = 'Primary Packaging : ' . "\n" .
                                        ($row['Primary1']==NULL?"" :  
                                            $row['Primary1'] . "\n" . 
                                            'Stock : ' . $row['StockPrimary1'] . ' ' . $row['UP1'] . '(' . $row['PrintedOn'] . ')' . "\n") .
                                        ($row['Primary2']==NULL?"" :
                                            $row['Primary2'] . "\n" . 
                                            'Stock : ' . $row['StockPrimary2'] . ' ' . $row['UP2'] . '(' . $row['PrintedOn'] . ')' . "\n") .
                                        ($row['Primary3']==NULL?"" :
                                            $row['Primary3'] . "\n" . 
                                            'Stock : ' . $row['StockPrimary3'] . ' ' . $row['UP3'] . '(' . $row['PrintedOn'] . ')' . "\n")  
                                        ;
                    $SecondPackaging = 'Secondary Packaging : ' . "\n" .

                                        ($row['Secondary1']==NULL?"" : 
                                            $row['Secondary1'] . "\n" . 
                                            'Stock : ' . $row['StockSecondary1'] . ' ' . $row['US1'] . '(' . $row['PrintedOn'] . ')' . "\n") .
                                        ($row['Secondary2']==NULL?"" : 
                                            $row['Secondary2'] . "\n" . 
                                            'Stock : ' . $row['StockSecondary2'] . ' ' . $row['US2'] . '(' . $row['PrintedOn'] . ')' . "\n") .
                                        ($row['Secondary3']==NULL?"" : 
                                            $row['Secondary3'] . "\n" . 
                                            'Stock : ' . $row['StockSecondary3'] . ' ' . $row['US3'] . '(' . $row['PrintedOn'] . ')' . "\n") .
                                        ($row['Secondary4']==NULL?"" : 
                                            $row['Secondary4'] . "\n" . 
                                            'Stock : ' . $row['StockSecondary4'] . ' ' . $row['US4'] . '(' . $row['PrintedOn'] . ')' . "\n")  
                                        ;
                    $ThirdPackaging = 'Third Packaging : ' . "\n" .
                                        ($row['Secondary5']==NULL?"" : 
                                            $row['Secondary5'] . "\n" . 
                                            'Stock : ' . $row['StockSecondary5'] . ' ' . $row['US5'] . '(' . $row['PrintedOn'] . ')' . "\n") .
                                        ($row['Secondary6']==NULL?"" : 
                                            $row['Secondary6'] . "\n" . 
                                            'Stock : ' . $row['StockSecondary6'] . ' ' . $row['US6'] . '(' . $row['PrintedOn'] . ')' . "\n") .
                                        ($row['Secondary7']==NULL?"" : 
                                            $row['Secondary7'] . "\n" . 
                                            'Stock : ' . $row['StockSecondary7'] . ' ' . $row['US7'] . '(' . $row['PrintedOn'] . ')' . "\n") .
                                        ($row['Secondary8']==NULL?"" : 
                                            $row['Secondary8'] . "\n" . 
                                            'Stock : ' . $row['StockSecondary8'] . ' ' . $row['US8'] . '(' . $row['PrintedOn'] . ')' . "\n")  
                                        ;

                $Packaging =  $PrimaryPackaging . "\n" .  "\n" . $SecondPackaging  .  "\n" . $ThirdPackaging;
                $More_Packaging =  $row['FormatPack'] ;
                $Remark =  $row['FormatPack'] ;


                $table1->rowStyle('border:{T,B,L,R};');
                $table1->easyCell($Information_Product,'font-size:5; font-style:R;align:L;valign:T;paddingY:1;border:T,L,R;');
                $table1->easyCell($Pack_Bag,'font-size:5; font-style:R;align:L;valign:T;paddingY:1;border:T,L,R;');
                $table1->easyCell($CartonBox_Drum,'font-size:5; font-style:R;align:L;valign:T;paddingY:1;border:T,L,R;');
                $table1->easyCell($Inner,'font-size:5; font-style:R;align:L;valign:T;paddingY:1;border:T,L,R;');
                $table1->easyCell($Packaging,'font-size:5; font-style:R;align:L;valign:T;paddingY:1;border:T,L,R;');
                $table1->easyCell($More_Packaging,'font-size:5; font-style:R;align:L;valign:T;paddingY:1;border:T,L,R;');
                $table1->easyCell($Remark,'font-size:5; font-style:R;align:L;valign:T;paddingY:1;border:T,L,R;');

                $table1->printRow();

                if ($row['FileCartonPath']!=NULL) {
                    $table1->easyCell('', 'img:' . $row['FilePackPath'] . ', w25; align:L');
                }else {
                    $table1->easyCell('');
                }

                if ($row['FileCartonPath']!=NULL) {
                    $table1->easyCell('', 'img:' . $row['FileCartonPath'] . ', w25; align:L');
                }else {
                    $table1->easyCell('');
                }

                $table1->easyCell('');
                $table1->easyCell('');
                $table1->easyCell('');
                //$table1->easyCell('', 'img:' . '\\\192.168.3.38\mypsg\DocumentPPIC\\' . $row['FilePack'] . ', w25; align:L;rowspan:2;');
                // $table1->easyCell('', 'img:' . '\\\192.168.3.38\mypsg\DocumentPPIC\01. Fok_Hing_200mL_FC24_pack01.jpg' . ', w25; align:L;rowspan:2;');
                
                
                
                $table1->printRow();
            }
            // $Nomor = 0;
            // foreach ($PP as $row) {
            //     $Nomor = $Nomor + 1 . '.';
            //     $table1->rowStyle('border:{T,B,L,R};');
            //     $table1->easyCell($Nomor,'font-size:8;align:R;valign:T;');
            //     $table1->easyCell($row['OldItemID'] . "\n" . $row['ItemName'],'font-size:8; font-style:R;align:L;valign:T;paddingY:1;border:T,L,R;');
            //     $table1->easyCell($row['ProductionDate'],'font-size:8;align:C;valign:T;');
            //     $table1->easyCell($row['LotNumber'],'font-size:8;align:L;valign:T;');
            //     $table1->easyCell($row['UOMName'],'font-size:8;align:L;valign:T;');
            //     $table1->easyCell(number_format($row['Qnty'],2,",","."),'font-size:8;align:R;valign:T;'); 
            //     $table1->easyCell($row['Remark'],'font-size:8;align:L;valign:T;');

            //     $table1->printRow();

            //     // $table1->rowStyle('border:{B,L,R};');
            //     // $table1->easyCell('Nama : ' . $row['ItemName'] ,'font-size:8;align:L;valign:T;paddingY:0');


            //     // $table1->printRow();



            // }


            // $table1->easyCell("BPB Remark : \n" . $bpb[0]['BPBRemark'] ,'font-size:8;align:L;valign:T;colspan:7;');


            // $table1->printRow();


             $table1->endTable(5);
        }


        //Footer
        private function footer($pdf,$bpb) {

            $table1=new easyTable($pdf,'{75,75,75,75}','font-size:8; font-style:B;border:0;border-color:#1a66ff;'); // 2 = column
            $table1->easyCell('Dokumen ini telah disetujui secara elektronik. Tanda tangan tidak diperlukan. ', 'font-style:BIU;align:L;colspan:4;');
            $table1->printRow();
            
            $table1->easyCell('Request By : ', 'font-style:B;align:L;');
            $table1->easyCell('BPB By : ', 'font-style:B;align:L;');
            $table1->easyCell('BPB Approve By : ', 'font-style:B;align:L;');
            $table1->easyCell('Received By : ', 'font-style:B;align:L;');
            $table1->printRow();

            $table1->easyCell(($bpb[0]['PABCreatedLoginName']==NULL?"-" : $bpb[0]['PABCreatedLoginName']) . " \n" . 
                              ($bpb[0]['PABCreatedPositionName']==NULL?"-" : $bpb[0]['PABCreatedPositionName']) . " \n" . 
                              ($bpb[0]['PABCreatedDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($bpb[0]['PABCreatedDate']))) ,
                              'font-style:R;align:L;');
            $table1->easyCell(($bpb[0]['BPBCreatedLoginName']==NULL?"-" : $bpb[0]['BPBCreatedLoginName']) . " \n" . 
                              ($bpb[0]['BPBCreatedPositionName']==NULL?"-" : $bpb[0]['BPBCreatedPositionName']) . " \n" . 
                              ($bpb[0]['BPBCreatedDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($bpb[0]['BPBCreatedDate']))) ,
                              'font-style:R;align:L;');
            $table1->easyCell(($bpb[0]['WHSApprovalLoginName']==NULL?"-" : $bpb[0]['WHSApprovalLoginName']) . " \n" . 
                              ($bpb[0]['WHSApprovalPositionName']==NULL?"-" : $bpb[0]['WHSApprovalPositionName']) . " \n" . 
                              ($bpb[0]['WHSApprovalDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($bpb[0]['WHSApprovalDate']))) ,
                              'font-style:R;align:L;');
            if ($bpb[0]['DSign_TerimaOlehNama']==NULL) {
                $table1->easyCell('');
            }else {
                $table1->easyCell('', 'img:' . '\\\192.168.3.38\mypsg\E-DOC\SerahTerima\Signature\BPB\\' . str_replace('/','-',$bpb[0]['BPBID']) . '.jpg' . ', w25; align:L;rowspan:2;' );
            }

            $table1->printRow();



 

            $table1->easyCell('');
            $table1->easyCell('');
            $table1->easyCell('');         
            $table1->printRow();

            $table1->easyCell('');
            $table1->easyCell('');
            $table1->easyCell('');
            $table1->easyCell(($bpb[0]['DSign_TerimaOlehNama']==NULL?"" : $bpb[0]['DSign_TerimaOlehNama']) . " \n" .
                               ($bpb[0]['DSign_TerimaOlehJabatan']==NULL?"" : $bpb[0]['DSign_TerimaOlehJabatan']) . " \n" .
                               ($bpb[0]['DSign_TerimaOlehDate']==NULL?"" : date("d/m/Y H:i:s", strtotime($bpb[0]['DSign_TerimaOlehDate']))),
                               'font-style:R;align:L;');
            $table1->printRow();

            $table1->endTable(2);


            
        }


}
