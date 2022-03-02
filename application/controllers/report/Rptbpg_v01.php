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
class Rptbpg_v01 extends REST_Controller {

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


                $sql='SELECT A.TransID,A.TransDate,A.BTBID,A.BPGRequestID,A.TransferRemark,A.WHSNameFrom,A.WHSNameTo, 
                    A.OldItemID,A.ItemName,A.ProductionDate,A.UOMName,A.LotNumber,A.Qnty,A.Remark,
                    A.CreatedBy,B.LoginName as CreatedByName,B.PositionName as CreatedByPosition,A.CreatedDate,
                    A.BTBCreatedBy,D.LoginName as BTBCreatedByName,D.PositionName as BTBCreatedByPosition,A.BTBCreatedDate,
                    A.WHSApprovalBy,C.LoginName as WHSApprovalByName,C.PositionName as WHSApprovalByPosition,A.WHSApprovalDate,
                    A.DSign_TerimaOlehNama,A.DSign_TerimaOlehJabatan,A.DSign_TerimaOlehDate,A.DSign_TerimaOlehRemark
                    FROM MyPSG..vwTransferWHS A WITH (NoLock) 
                    LEFT JOIN MyPSG..tblMstUser B WITH (NoLock) ON A.CreatedBy = B.LoginID 
                    LEFT JOIN MyPSG..tblMstUser C WITH (NoLock) ON A.WHSApprovalBy = C.LoginID  
                    LEFT JOIN MyPSG..tblMstUser D WITH (NoLock) ON A.BTBCreatedBy = D.LoginID  
                    WHERE A.STATUS=2 AND A.transid = ' . "'" . $id . "' " .
                    'ORDER BY A.ItemID ';

                // var_dump($sql);
                $bpg = $this->db->query($sql,$id)->result_array();
                if (!$bpg) {

                    $this->response([
                        'status' => FALSE,
                        'message' => 'NOT_FOUND!'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code   

                } else {
            
                    $this->showbpb($bpg);

                }

        }


 
 

    }

        private function showbpb($bpg) {

         $pdf=new exFPDF('l', 'mm', 'A5');

         $pdf->AddPage(); 
         $pdf->SetFont('Arial','',8);
         
   

         $this->header($pdf,$bpg);
         $this->data($pdf,$bpg);
         $this->footer($pdf,$bpg);

         $pdf->Output(); 

        }


        private function header($pdf,$bpg) {

                $table1=new easyTable($pdf,'{25,20, 150, 30,5,40}',' border:0;border-color:#1a66ff;'); // 2 = column
                $table1->easyCell('', 'img:' . str_replace('C:','',APPPATH) . 'libraries/fpdf/Pics/logo_pulau_sambu.jpg, w25; align:L;rowspan:2;');
                $table1->easyCell('');
                $table1->easyCell('BUKTI PINDAH GUDANG', 'font-size:12; font-style:B;align:C;colspan:3');
                $table1->easyCell('');
                $table1->printRow();

                 $table1->easyCell("<b>PT. Pulau Sambu</b>\n<i>Sungai Guntung - Inhil - Riau</i>",'colspan:2;');
                 $table1->easyCell("BPG No \nBPG Date \nWarehouse \nTransfer To \n BTB No");
                 $table1->easyCell(": \n: \n: \n: \n:");
                 $table1->easyCell($bpg[0]['TransID'] . " \n" . 
                                    date("d/m/Y", strtotime($bpg[0]['TransDate'])) . " \n" . 
                                    $bpg[0]['WHSNameFrom'] . " \n" . 
                                    ($bpg[0]['WHSNameTo']==NULL?"-" : $bpg[0]['WHSNameTo']) . " \n" . 
                                    $bpg[0]['BTBID']);
                 $table1->printRow();


                 $table1->endTable(10);


            
        }

         private function Data($pdf,$bpg) {

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
            foreach ($bpg as $row) {
                $Nomor = $Nomor + 1 . '.';
                $table1->rowStyle('border:{T,B,L,R};');
                $table1->easyCell($Nomor,'font-size:8;align:R;valign:T;');
                $table1->easyCell( $row['OldItemID'] . "\n" . $row['ItemName'] ,'font-size:8; font-style:R;align:L;valign:T;paddingY:1;border:B,T,L,R;');
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


            $table1->easyCell("BPG Remark : \n" . $bpg[0]['TransferRemark'] ,'font-size:8;align:L;valign:T;colspan:7;');


            $table1->printRow();


             $table1->endTable(5);
        }


        //Footer
        private function footer($pdf,$bpg) {

            $table1=new easyTable($pdf,'{75,75,75,75}','font-size:8; font-style:B;border:0;border-color:#1a66ff;'); // 2 = column
            $table1->easyCell('Dokumen ini telah disetujui secara elektronik. Tanda tangan tidak diperlukan. ', 'font-style:BIU;align:L;colspan:4;');
            $table1->printRow();
            
            $table1->easyCell('BPG By : ', 'font-style:B;align:L;');
            $table1->easyCell('BPG Approve By : ', 'font-style:B;align:L;');
            $table1->easyCell('');
            $table1->easyCell('Received By : ');
            $table1->printRow();


            // $table1->easyCell('');
            // $table1->easyCell('');
            // $table1->easyCell('');
            // $table1->easyCell('', 'img:' . '\\\192.168.3.38\mypsg\E-DOC\SerahTerima\Signature\BPG\\' . str_replace('/','-',$bpg[0]['TransID']) . '.jpg' . ', w25; align:L;rowspan:1;' );
            // $table1->printRow();

            if ($bpg[0]['BTBCreatedBy']==NULL) { 
                $table1->easyCell(($bpg[0]['CreatedByName']==NULL?"-" : $bpg[0]['CreatedByName']) . " \n" . 
                                  ($bpg[0]['CreatedByPosition']==NULL?"-" : $bpg[0]['CreatedByPosition']) . " \n" . 
                                  ($bpg[0]['CreatedDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($bpg[0]['CreatedDate']))) ,
                                  'font-style:R;align:L;');
            }
            else {
                $table1->easyCell(($bpg[0]['BTBCreatedByName']==NULL?"-" : $bpg[0]['BTBCreatedByName']) . " \n" . 
                                  ($bpg[0]['BTBCreatedByPosition']==NULL?"-" : $bpg[0]['BTBCreatedByPosition']) . " \n" . 
                                  ($bpg[0]['BTBCreatedDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($bpg[0]['BTBCreatedDate']))) ,
                                  'font-style:R;align:L;');
            }

            $table1->easyCell(($bpg[0]['WHSApprovalByName']==NULL?"-" : $bpg[0]['WHSApprovalByName']) . " \n" . 
                              ($bpg[0]['WHSApprovalByPosition']==NULL?"-" : $bpg[0]['WHSApprovalByPosition']) . " \n" . 
                              ($bpg[0]['WHSApprovalDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($bpg[0]['WHSApprovalDate']))) ,
                              'font-style:R;align:L;');
            $table1->easyCell('');

            if ($bpg[0]['DSign_TerimaOlehNama']==NULL) {
                $table1->easyCell('');
            } else {
                $table1->easyCell('', 'img:' . '\\\192.168.3.38\mypsg\E-DOC\SerahTerima\Signature\BPG\\' . str_replace('/','-',$bpg[0]['TransID']) . '.jpg' . ', w25; align:L;rowspan:2;' );
            }
            $table1->printRow();

            $table1->easyCell('');
            $table1->easyCell('');
            $table1->easyCell('');
            
            $table1->printRow();

            $table1->easyCell('');
            $table1->easyCell('');
            $table1->easyCell('');
            $table1->easyCell(($bpg[0]['DSign_TerimaOlehNama']==NULL?"" : $bpg[0]['DSign_TerimaOlehNama']) . " \n" .
                               ($bpg[0]['DSign_TerimaOlehJabatan']==NULL?"" : $bpg[0]['DSign_TerimaOlehJabatan']) . " \n" .
                               ($bpg[0]['DSign_TerimaOlehDate']==NULL?"" : date("d/m/Y H:i:s", strtotime($bpg[0]['DSign_TerimaOlehDate']))),
                               'font-style:R;align:L;');
            $table1->printRow();



            $table1->endTable(2);


            
        }


}
