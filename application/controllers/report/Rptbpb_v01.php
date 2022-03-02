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
class Rptbpb_v01 extends REST_Controller {

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


                $sql='SELECT A.BPBID,A.TransDate,A.PABID,A.BPBRemark,A.WHSName,A.DeptName,  
                        A.OldItemID,A.ItemName,A.ProductionDate,A.UOMName,A.LotNumber,A.Qnty,A.Remark,A.WHSID,
                        UPPER(B.LoginName) AS PABCreatedLoginName, UPPER(B.PositionName) AS PABCreatedPositionName, A.PABCreatedDate AS PABCreatedDate,
                        UPPER(C.LoginName) AS DeptApprovalLoginName, UPPER(C.PositionName) AS DeptApprovalPositionName, 
                        UPPER(D.LoginName) AS BPBCreatedLoginName, UPPER(D.PositionName) AS BPBCreatedPositionName, A.CreatedDate AS BPBCreatedDate,
                        UPPER(E.LoginName) AS WHSApprovalLoginName, UPPER(E.PositionName) AS WHSApprovalPositionName ,A.WHSApprovalDate AS WHSApprovalDate,
                        UPPER(A.BPBReceivedBy) AS BPBReceivedByName,UPPER(A.BPBReceivedPosition) AS BPBReceivedPosition,A.BPBReceivedDate,
                        A.DSign_TerimaOlehNama,A.DSign_TerimaOlehJabatan,A.DSign_TerimaOlehDate,A.DSign_TerimaOlehRemark
                        FROM MyPSG..vwBPB A WITH (NoLock)  
                        LEFT JOIN MyPSG..tblMstUser B WITH (NoLock) ON A.PABCreatedBy = B.LoginID  
                        LEFT JOIN MyPSG..tblMstUser C WITH (NoLock) ON A.DeptApprovalBy = C.LoginID  
                        LEFT JOIN MyPSG..tblMstUser D WITH (NoLock) ON A.CreatedBy = D.LoginID  
                        LEFT JOIN MyPSG..tblMstUser E WITH (NoLock) ON A.WHSApprovalBy = E.LoginID  
                        WHERE A.STATUS=2 AND A.BPBID = ' . "'" . $id . "' " .
                        'ORDER BY A.ItemID ';

                // $bpb = $this->db->get_where("vwBPB", ['BPBID' => $id])->result_array();
                // var_dump($sql);
                $bpb = $this->db->query($sql,$id)->result_array();
                if (!$bpb) {

                    $this->response([
                        'status' => FALSE,
                        'message' => 'NOT_FOUND!' 
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code   

                } else {
                    //$this->response($bpb, REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code  
                    $this->showbpb($bpb);
                    //var_dump($bpb);
                }

        }


 
 

    }


        private function savebpb($bpb) {

         $pdf=new exFPDF('l', 'mm', 'A5');

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

        private function showbpb($bpb) {

         $pdf=new exFPDF('l', 'mm', 'A5');

         $pdf->AddPage(); 
         $pdf->SetFont('Arial','',8);
         
   

         $this->header($pdf,$bpb);
         $this->data($pdf,$bpb);
         $this->footer($pdf,$bpb);

         $pdf->Output(); 


        }


        private function header($pdf,$bpb) {

                $table1=new easyTable($pdf,'{25,20, 150, 30,5,40}',' border:0;border-color:#1a66ff;'); // 2 = column
                $table1->easyCell('', 'img:' . str_replace('C:','',APPPATH) . 'libraries/fpdf/Pics/logo_pulau_sambu.jpg, w25; align:L;rowspan:2;');
                $table1->easyCell('');
                $table1->easyCell('BUKTI PENGELUARAN BARANG', 'font-size:12; font-style:B;align:C;colspan:3');
                $table1->easyCell('');
                $table1->printRow();

             $table1->easyCell("<b>PT. Pulau Sambu</b>\n<i>Sungai Guntung - Inhil - Riau</i>",'colspan:2;');
             $table1->easyCell("BPB No \nBPB Date \nWarehouse \nDoc Request \nDepartment");
             $table1->easyCell(": \n: \n: \n: \n:");
             $table1->easyCell($bpb[0]['BPBID'] . " \n" . 
                                date("d/m/Y", strtotime($bpb[0]['TransDate'])) . " \n" . 
                                $bpb[0]['WHSName'] . " \n" . 
                                ($bpb[0]['PABID']==NULL?"-" : $bpb[0]['PABID']) . " \n" . 
                                $bpb[0]['DeptName']);
             $table1->printRow();


             $table1->endTable(10);


            
        }

         private function Data($pdf,$bpb) {

            $table1=new easyTable($pdf,'{10,100,25,30,15,30,50}',' border:1;'); // 2 = column
            $table1->easyCell('No.','font-size:8; font-style:B;align:R;valign:M;');
            $table1->easyCell("Item \nDescription",'font-size:8; font-style:B;align:L;valign:M;');
            $table1->easyCell("Production \nDate",'font-size:8; font-style:B;align:C;valign:M;');
            $table1->easyCell('Lot Number','font-size:8; font-style:B;align:L;valign:M;');
            $table1->easyCell('Uom','font-size:8; font-style:B;align:L;valign:M;');
            $table1->easyCell('Qty','font-size:8; font-style:B;align:R;valign:M;');
            $table1->easyCell('Remark','font-size:8; font-style:B;align:L;valign:M;');

            $table1->printRow();

            $Nomor = 0;
            foreach ($bpb as $row) {
                $Nomor = $Nomor + 1 . '.';
                $table1->rowStyle('border:{T,B,L,R};');
                $table1->easyCell($Nomor,'font-size:8;align:R;valign:T;');
                $table1->easyCell($row['OldItemID'] . "\n" . $row['ItemName'],'font-size:8; font-style:R;align:L;valign:T;paddingY:1;border:T,L,R;');
                $table1->easyCell($row['ProductionDate'],'font-size:8;align:C;valign:T;');
                $table1->easyCell($row['LotNumber'],'font-size:8;align:L;valign:T;');
                $table1->easyCell($row['UOMName'],'font-size:8;align:L;valign:T;');
                $table1->easyCell(number_format($row['Qnty'],2,",","."),'font-size:8;align:R;valign:T;'); 
                $table1->easyCell($row['Remark'],'font-size:8;align:L;valign:T;');

                $table1->printRow();

                // $table1->rowStyle('border:{B,L,R};');
                // $table1->easyCell('Nama : ' . $row['ItemName'] ,'font-size:8;align:L;valign:T;paddingY:0');


                // $table1->printRow();



            }


            $table1->easyCell("BPB Remark : \n" . $bpb[0]['BPBRemark'] ,'font-size:8;align:L;valign:T;colspan:7;');


            $table1->printRow();


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
