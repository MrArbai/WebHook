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
class Rptbtb_v01 extends REST_Controller {

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

                $sql='SELECT A.BTBID,A.TransDate,A.ArrivalDate,A.BCNameNo,A.NPBBNo,SupplierName,A.DeptName,A.BTBRemark,
                    A.OldItemID,A.ItemName,A.ProductionDate,A.UOMName,A.LotNumber,A.Qnty,A.Remark,
                    A.CreatedBy,B.LoginName as CreatedByName,B.PositionName as CreatedByPosition,A.CreatedDate,
                    A.WHSApprovalBy,C.LoginName as WHSApprovalByName,C.PositionName as WHSApprovalByPosition,A.WHSApprovalDate,
                    A.DeptApprovalBy,D.LoginName as DeptApprovalByName,D.PositionName as DeptApprovalByPosition,A.DeptApprovalDate
                    FROM MyPSG..vwbtb A WITH (NoLock) 
                    LEFT JOIN MyPSG..tblMstUser B WITH (NoLock) ON A.CreatedBy = B.LoginID 
                    LEFT JOIN MyPSG..tblMstUser C WITH (NoLock) ON A.WHSApprovalBy = C.LoginID  
                    LEFT JOIN MyPSG..tblMstUser D WITH (NoLock) ON A.DeptApprovalBy = D.LoginID  
                    WHERE A.STATUS=2 AND A.BTBID = ' . "'" . $id . "' " .
                    'ORDER BY A.ItemID';

                // var_dump($sql);
                $btb = $this->db->query($sql,$id)->result_array();
                if (!$btb) {

                    $this->response([
                        'status' => FALSE,
                        'message' => 'NOT_FOUND!'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code   

                } else {
            
                    $this->showbpb($btb);

                }

        }


 
 

    }

        private function showbpb($btb) {

         $pdf=new exFPDF('L','mm','A5');

         $pdf->AddPage(); 
         $pdf->SetFont('Arial','',8);
         
   

         $this->header($pdf,$btb);
         $this->data($pdf,$btb);
         $this->footer($pdf,$btb);

         $pdf->Output(); 

        }


        private function header($pdf,$btb) {

                $table1=new easyTable($pdf,'{25,20, 100, 25,5,40}',' border:0;border-color:#1a66ff;'); // 2 = column
                $table1->easyCell('', 'img:' . str_replace('C:','',APPPATH) . 'libraries/fpdf/Pics/logo_pulau_sambu.jpg, w25; align:L;rowspan:2;');
                $table1->easyCell('');
                $table1->easyCell('BUKTI TERIMA BARANG', 'font-size:12; font-style:B;align:C;colspan:3');
                $table1->easyCell('');
                $table1->printRow();



                 $table1->easyCell("<b>PT. Pulau Sambu</b>\n<i>Sungai Guntung - Inhil - Riau</i>",'colspan:2;');
                 $table1->easyCell("BC Type / No. \nBTB No \nBTB Date \nArrival Date \nNPBB No \nDepartment \nSupplier");
                 $table1->easyCell(": \n: \n: \n: \n: \n: \n:");
                 $table1->easyCell($btb[0]['BCNameNo'] . " \n" . 
                                    $btb[0]['BTBID'] . " \n" . 
                                    date("d/m/Y", strtotime($btb[0]['TransDate'])) . " \n" . 
                                    date("d/m/Y", strtotime($btb[0]['ArrivalDate'])) . " \n" . 
                                    $btb[0]['NPBBNo'] . " \n" . 
                                    $btb[0]['DeptName'] . " \n" . 
                                    $btb[0]['SupplierName']);
                 $table1->printRow();


                 $table1->endTable(10);


            
        }

         private function Data($pdf,$btb) {

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
            foreach ($btb as $row) {
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


            $table1->easyCell("BTB Remark : \n" . $btb[0]['BTBRemark'] ,'font-size:8;align:L;valign:T;colspan:7;');


            $table1->printRow();


             $table1->endTable(5);
        }


        //Footer
        private function footer($pdf,$btb) {

            $table1=new easyTable($pdf,'{75,75,75,75}','font-size:8; font-style:B;border:0;border-color:#1a66ff;'); // 2 = column
            $table1->easyCell('Dokumen ini telah disetujui secara elektronik. Tanda tangan tidak diperlukan. ', 'font-style:BIU;align:L;colspan:4;');
            $table1->printRow();
            
            $table1->easyCell('BTB By : ', 'font-style:B;align:L;');
            $table1->easyCell('BTB Approve By : ', 'font-style:B;align:L;');
            $table1->easyCell('Dept Approve By : ', 'font-style:B;align:L;');
            $table1->easyCell('');
            $table1->printRow();
  
            $table1->easyCell(($btb[0]['CreatedByName']==NULL?"-" : $btb[0]['CreatedByName']) . " \n" . 
                              ($btb[0]['CreatedByPosition']==NULL?"-" : $btb[0]['CreatedByPosition']) . " \n" . 
                              ($btb[0]['CreatedDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($btb[0]['CreatedDate']))) ,
                              'font-style:R;align:L;');
            $table1->easyCell(($btb[0]['WHSApprovalByName']==NULL?"-" : $btb[0]['WHSApprovalByName']) . " \n" . 
                              ($btb[0]['WHSApprovalByPosition']==NULL?"-" : $btb[0]['WHSApprovalByPosition']) . " \n" . 
                              ($btb[0]['WHSApprovalDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($btb[0]['WHSApprovalDate']))) ,
                              'font-style:R;align:L;');
            $table1->easyCell(($btb[0]['DeptApprovalByName']==NULL?"-" : $btb[0]['DeptApprovalByName']) . " \n" . 
                              ($btb[0]['DeptApprovalByPosition']==NULL?"-" : $btb[0]['DeptApprovalByPosition']) . " \n" . 
                              ($btb[0]['DeptApprovalDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($btb[0]['DeptApprovalDate']))) ,
                              'font-style:R;align:L;');
            $table1->easyCell('');
            $table1->printRow();



            $table1->endTable(2);


            
        }


}
