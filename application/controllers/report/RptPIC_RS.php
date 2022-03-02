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
class RptPIC_RS extends REST_Controller {

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


                // $sql='SELECT A.BPBID,A.TransDate,A.PABID,A.BPBRemark,A.WHSName,A.DeptName,  
                //         A.OldItemID,A.ItemName,A.ProductionDate,A.UOMName,A.LotNumber,A.Qnty,A.Remark,A.WHSID,
                //         UPPER(B.LoginName) AS PABCreatedLoginName, UPPER(B.PositionName) AS PABCreatedPositionName, A.PABCreatedDate AS PABCreatedDate,
                //         UPPER(C.LoginName) AS DeptApprovalLoginName, UPPER(C.PositionName) AS DeptApprovalPositionName, 
                //         UPPER(D.LoginName) AS BPBCreatedLoginName, UPPER(D.PositionName) AS BPBCreatedPositionName, A.CreatedDate AS BPBCreatedDate,
                //         UPPER(E.LoginName) AS WHSApprovalLoginName, UPPER(E.PositionName) AS WHSApprovalPositionName ,A.WHSApprovalDate AS WHSApprovalDate,
                //         UPPER(A.BPBReceivedBy) AS BPBReceivedByName,UPPER(A.BPBReceivedPosition) AS BPBReceivedPosition,A.BPBReceivedDate,
                //         A.DSign_TerimaOlehNama,A.DSign_TerimaOlehJabatan,A.DSign_TerimaOlehDate,A.DSign_TerimaOlehRemark
                //         FROM MyPSG..vwBPB A WITH (NoLock)  
                //         LEFT JOIN MyPSG..tblMstUser B WITH (NoLock) ON A.PABCreatedBy = B.LoginID  
                //         LEFT JOIN MyPSG..tblMstUser C WITH (NoLock) ON A.DeptApprovalBy = C.LoginID  
                //         LEFT JOIN MyPSG..tblMstUser D WITH (NoLock) ON A.CreatedBy = D.LoginID  
                //         LEFT JOIN MyPSG..tblMstUser E WITH (NoLock) ON A.WHSApprovalBy = E.LoginID  
                //         WHERE A.STATUS=2 AND A.BPBID = ' . "'" . $id . "' " .
                //         'ORDER BY A.ItemID ';



                $sql = "SELECT A.*, " .
                          " B.LoginName AS CreatedLoginName, B.PositionName AS CreatedPositionName," .
                          " C.LoginName AS SignedLoginName1, C.PositionName AS SignedPositionName1," .
                          " D.LoginName AS SignedLoginName2, D.PositionName AS SignedPositionName2," .
                          " E.LoginName AS SignedLoginName3, E.PositionName AS SignedPositionName3," .
                          " F.LoginName AS SignedLoginName4, F.PositionName AS SignedPositionName4," .
                          " G.LoginName AS SignedLoginName5, G.PositionName AS SignedPositionName5" .
                          " FROM MyPSG..vwShipmentPlanning1 A " .
                          " LEFT JOIN MyPSG..tblMstUser B ON A.CreatedBy = B.LoginID " .
                          " LEFT JOIN MyPSG..tblMstUser C ON A.SignedBy1 = C.LoginID " .
                          " LEFT JOIN MyPSG..tblMstUser D ON A.SignedBy2 = D.LoginID " .
                          " LEFT JOIN MyPSG..tblMstUser E ON A.SignedBy3 = E.LoginID " .
                          " LEFT JOIN MyPSG..tblMstUser F ON A.SignedBy4 = F.LoginID " .
                          " LEFT JOIN MyPSG..tblMstUser G ON A.SignedBy5 = G.LoginID " .
                          " WHERE A.TransID = '" . $id  . "'" .
                          " ORDER BY A.TransID, RIGHT(A.PONumber, 2), LEFT(RIGHT(A.PONumber, 5), 2), A.PONumber, A.PORef, A.ProductNo ";

                // $bpb = $this->db->get_where("vwBPB", ['BPBID' => $id])->result_array();
                //var_dump($sql);
                $doc = $this->db->query($sql,$id)->result_array();
                if (!$doc) {

                    $this->response([
                        'status' => FALSE,
                        'message' => 'NOT_FOUND!'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code   

                } else {
                    //$this->response($bpb, REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code  
                    $this->showbpb($doc);
                    //var_dump($bpb);
                }

        }


 
 

    }


        private function savebpb($doc) {

         $pdf=new exFPDF('l', 'mm', 'A4');

         $pdf->AddPage(); 
         $pdf->SetFont('Arial','',8);


         
         $tahun = date("Y", strtotime($bpb[0]['TransDate']));
         $whsid = substr('000' . $bpb[0]['WHSID'], -3);
         $bulan = substr('00' . date("m", strtotime($bpb[0]['TransDate'])), -2);

         $path='\\\192.168.3.38\mypsg\E-DOC\TRANSACTION\\' . $tahun . '\\' . $bulan . '\\' . $whsid . '\\BPB\\API\\' ;
         $filename= 'Rptbpb_v01_' . str_replace('/','-',$bpb[0]['BPBID'])  . '.pdf';

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

         $this->header($pdf,$bpb);
         $this->data($pdf,$bpb);
         $this->footer($pdf,$bpb);
 
         $pdf->Output($path . $filename,'F'); 

        }

        private function showbpb($doc) {

         $pdf=new exFPDF('l', 'mm', 'A4');

         $pdf->AddPage(); 
         $pdf->SetFont('Arial','',8);
         

         $this->header($pdf,$doc);
         $this->data($pdf,$doc);
         // $this->footer($pdf,$bpb);

         $pdf->Output(); 


        }


        private function header($pdf,$doc) {

          switch ($doc[0]['ShipmentPlanningType']) {
              case "1":
                  $judul = 'Rencana Shipment Coconut Cream';
                  break;
              case "2":
                  $judul = 'Rencana Shipment Coconut Cream';
                  break;
              default:
                  $judul = 'Rencana Shipment';
                  break;
          }

                $table1=new easyTable($pdf,'{25,25,3,230}',' border:1;border-color:#1a66ff;'); // 2 = column
                $table1->easyCell('', 'img:' . str_replace('C:','',APPPATH) . 'libraries/fpdf/Pics/logo_pulau_sambu.jpg, w25; align:L;rowspan:2;');
                $table1->easyCell("<b>PT. Pulau Sambu</b>\n<i>Sungai Guntung - Inhil - Riau</i> \n Telp: +62 - 779 - 552 888 Fax: +62 - 779 - 552 000 \nEmail : general@psg.co.id, b.hadianto@psg.co.id",'colspan:3;');
                $table1->printRow();

                // $table1->easyCell('');
                $table1->easyCell("Document" . "\nShipment Date" . "\nVessel Name" . "\nRevision");
                $table1->easyCell(":" . "\n:" . "\n:" . "\n:");
                $table1->easyCell($doc[0]['DocumentNumber'] . "\n" . date("d/m/Y", strtotime($doc[0]['DeliveryDate'])) . "\n" . $doc[0]['VesselName'] . "\n" . $doc[0]['ReportRevisionNumber'] . "\n" ,'font-style:B;');
                $table1->printRow();


                $table1->easyCell($judul,'font-size:12; font-style:B;align:C;valign:M;colspan:4;');
                $table1->printRow();

             // $table1->easyCell("<b>PT. Pulau Sambu</b>\n<i>Sungai Guntung - Inhil - Riau</i>",'colspan:2;');
             // $table1->easyCell("BPB No \nBPB Date \nWarehouse \nDoc Request \nDepartment");
             // $table1->easyCell(": \n: \n: \n: \n:");
             // $table1->easyCell($bpb[0]['BPBID'] . " \n" . 
             //                    date("d/m/Y", strtotime($bpb[0]['TransDate'])) . " \n" . 
             //                    $bpb[0]['WHSName'] . " \n" . 
             //                    ($bpb[0]['PABID']==NULL?"-" : $bpb[0]['PABID']) . " \n" . 
             //                    $bpb[0]['DeptName']);
             // $table1->printRow();


             $table1->endTable(3);


            
        }

         private function Data($pdf,$doc) {

            $table1=new easyTable($pdf,'{10,40,40,40,35,35,12,9,9,20,80}',' border:1;'); // 2 = column
            $table1->easyCell('No.','font-size:8; font-style:B;align:C;valign:M;');
            $table1->easyCell('PO Number','font-size:8; font-style:B;align:C;valign:M;');
            $table1->easyCell("Buyer",'font-size:8; font-style:B;align:C;valign:M;');
            $table1->easyCell("Product",'font-size:8; font-style:B;align:C;valign:M;');
            $table1->easyCell('Packaging','font-size:8; font-style:B;align:C;valign:M;colspan:2;');
            $table1->easyCell('Fmgs','font-size:8; font-style:B;align:C;valign:M;');
            $table1->easyCell('20 ft','font-size:8; font-style:B;align:C;valign:M;');
            $table1->easyCell('40 ft','font-size:8; font-style:B;align:C;valign:M;');
            $table1->easyCell('Qty','font-size:8; font-style:B;align:R;valign:M;');
            $table1->easyCell('Remark','font-size:8; font-style:B;align:L;valign:M;');

            $table1->printRow();

            $productDetail='';
            $Primary='';
            $Second='';

            $FCL20ft='';
            $FCL40ft='';
            $FCL20ftTotal=0;
            $FCL40ftTotal=0;

            $CurrentPO = '';
            $Nomor = 0;
            foreach ($doc as $row) {

            $productDetail = ($row['ProductID']==NULL?"" : "Product ID : " . $row['ProductID'])  . "\n" . 
                             ($row['Formula']==NULL?"" : "Formula : " . $row['Formula'])  . "\n" . 
                             ($row['FormulaDesc']==NULL?"" : "Formula Desc : " . $row['FormulaDesc'])  . "\n" . 
                             ($row['CartonBarcode']==NULL?"" : "Carton Barcode : " . $row['CartonBarcode'])  . "\n" . 
                             ($row['ProdukBarcode']==NULL?"" : "Product Barcode : " . $row['ProdukBarcode'])  . "\n" . 
                             ($row['AdditionalBarcode']==NULL?"" : "Additional Barcode : " . $row['AdditionalBarcode'])  . "\n" . 
                             ($row['BarcodeRemarks1']==NULL?"" : "*" . $row['BarcodeRemarks1'])  ;

    // Fields("ProdukDetail").Value = Fields("ProdukID").Value & _
    //                                Fields("Formula").Value & _
    //                                Fields("FormulaDesc").Value & _
    //                                Fields("CartonBarcode").Value & _
    //                                Fields("ProductBarcode").Value & _
    //                                Fields("AdditionalBarcode").Value & _
    //                                Fields("BarcodeRemarks1").Value
            $Primary = "Primary Packaging : " . "\n" . $row['Primary1'] . "\n" . $row['Primary2'] . "\n" . $row['Primary3'] . "\n" . "Secondary Packaging : " . "\n" . $row['Secondary1'] . "\n" . $row['Secondary2'] . "\n" . $row['Secondary3'] . "\n" . $row['Secondary4'] . "\n" . $row['Secondary5'] . "\n" . $row['Secondary6'] . "\n" . $row['Secondary7'] . "\n" . $row['Secondary8']  ;

            $Second = "More Packaging : " . "\n" . $row['Dunnage'] . "\n" . $row['Straw'] . "\n" . $row['Shrink'] . "\n" . $row['StickerPack'] . "\n" . $row['StickerCarton'] . "\n" . $row['StickerCarton2'] . "\n" . $row['Marking']  ;

                $Nomor = $Nomor + 1 . '.';
                $table1->rowStyle('border:{T,B,L,R};');
 
                $table1->easyCell($Nomor,'font-size:8;align:R;valign:T;');
                $table1->easyCell($row['PONumber'] ,'font-size:8; font-style:R;align:L;valign:T;paddingY:1;');
                $table1->easyCell($row['BuyerName'] ,'font-size:8; font-style:R;align:L;valign:T;paddingY:1;');
                $table1->easyCell($productDetail ,'font-size:8; font-style:R;align:L;valign:T;paddingY:1;');
                $table1->easyCell($Primary,'font-size:8; font-style:R;align:L;valign:T;paddingY:1;');
                $table1->easyCell($Second ,'font-size:8; font-style:R;align:L;valign:T;paddingY:1;');

                $table1->easyCell(($row['Fumigation']==0?"-" : "v"),'font-size:8;align:C;valign:T;');


                switch ($row['ContainerSize']) {
                    case "20":
                        $FCL20ft=number_format($row['ProductContainerLoadPercentage'],2,",",".") ;
                        $FCL40ft='';
                        $FCL20ftTotal = $FCL20ftTotal + $row['ProductContainerLoadPercentage'];
                        break;
                    case "40":
                        $FCL20ft='';
                        $FCL40ft=number_format($row['ProductContainerLoadPercentage'],2,",",".") ;
                        $FCL40ftTotal = $FCL40ftTotal + $row['ProductContainerLoadPercentage'];
                        break;
                    default:
                        $FCL20ft='';
                        $FCL40ft='';
                        break;
                }


                $table1->easyCell($FCL20ft,'font-size:8;align:R;valign:T;');
                $table1->easyCell($FCL40ft,'font-size:8;align:R;valign:T;');
                $table1->easyCell(number_format($row['Qnty'],0,",",".") ,'font-size:8;align:R;valign:T;');
                $table1->easyCell("Marking : \n" . $row['ShipmentPlanningProductRemark']);


                $table1->printRow();

              if ($row['DokumenPOName']==NULL) {
                  $table1->easyCell('','colspan:11;');
              }else {
                  // $table1->easyCell('', 'img:' . '\\\192.168.3.38\DocumentPPIC\\' . str_replace('/','-',$row['DokumenPOName'])  . ', w25; align:L;colspan:11;' );
                  // $table1->easyCell('', 'img:' . "\\192.168.3.38\DocumentPPIC\C027_03_2020_PSS_Bakhresa.jpg" . ', w25; align:L;colspan:11;' );
                  // 
                  $table1->easyCell('', 'img:' . '\\\192.168.3.38\foto\\941_0_0.png' . ', w25; align:L;colspan:11;' );
                  

              }

              $table1->printRow();
                // $table1->easyCell($Nomor,'font-size:8;align:R;valign:T;');
                // $table1->easyCell($row['OldItemID'] . "\n" . $row['ItemName'],'font-size:8; font-style:R;align:L;valign:T;paddingY:1;border:T,L,R;');
                // $table1->easyCell($row['ProductionDate'],'font-size:8;align:C;valign:T;');
                // $table1->easyCell($row['LotNumber'],'font-size:8;align:L;valign:T;');
                // $table1->easyCell($row['UOMName'],'font-size:8;align:L;valign:T;');
                // $table1->easyCell(number_format($row['Qnty'],2,",","."),'font-size:8;align:R;valign:T;'); 
                // $table1->easyCell($row['Remark'],'font-size:8;align:L;valign:T;');

                // $table1->printRow();

                // $table1->rowStyle('border:{B,L,R};');
                // $table1->easyCell('Nama : ' . $row['ItemName'] ,'font-size:8;align:L;valign:T;paddingY:0');


                // $table1->printRow();



            }


            //$table1->easyCell("BPB Remark : \n" . $doc[0]['BPBRemark'] ,'font-size:8;align:L;valign:T;colspan:7;');
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
