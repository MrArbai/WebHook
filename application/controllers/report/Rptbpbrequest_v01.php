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
class Rptbpbrequest_v01 extends REST_Controller {

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


                $sql='SELECT A.PABID,A.TransDate,A.PABID,A.Remark,A.WHSName,A.DeptName,  
                        A.OldItemID,A.ItemName,A.UOMName,A.Qnty,A.QntyBekas,A.Remark,A.WHSID,
                        UPPER(B.LoginName) AS CreatedLoginName, UPPER(B.PositionName) AS CreatedPositionName, A.CreatedDate,
                        UPPER(C.LoginName) AS DeptApprovalLoginName, UPPER(C.PositionName) AS DeptApprovalPositionName,A.DeptApprovalDate
                        FROM MyPSG..vwPABAll A WITH (NoLock)  
                        LEFT JOIN MyPSG..tblMstUser B WITH (NoLock) ON A.CreatedBy = B.LoginID  
                        LEFT JOIN MyPSG..tblMstUser C WITH (NoLock) ON A.DeptApprovalBy = C.LoginID   
                        WHERE A.STATUS=2  AND A.PABID = ' . "'" . $id . "' " .
                        'ORDER BY A.ItemID ';

                // $bpb = $this->db->get_where("vwBPB", ['BPBID' => $id])->result_array();
                // var_dump($sql);
                $bpbrequest = $this->db->query($sql,$id)->result_array();
                if (!$bpbrequest) {

                    $this->response([
                        'status' => FALSE,
                        'message' => 'NOT_FOUND!'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code   

                } else {
                    //$this->response($bpb, REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code  
                    $this->showbpbrequest($bpbrequest);
                    //var_dump($bpb);
                }

        }


 
 

    }


 

        private function showbpbrequest($bpbrequest) {

         $pdf=new exFPDF('l', 'mm', 'A5');

         $pdf->AddPage(); 
         $pdf->SetFont('Arial','',8);
         
   

         $this->header($pdf,$bpbrequest);
         $this->data($pdf,$bpbrequest);
         $this->footer($pdf,$bpbrequest);

         $pdf->Output(); 


        }


        private function header($pdf,$bpbrequest) {

                $table1=new easyTable($pdf,'{25,20, 150, 30,5,60}',' border:0;border-color:#1a66ff;'); // 2 = column
                $table1->easyCell('', 'img:' . str_replace('C:','',APPPATH) . 'libraries/fpdf/Pics/logo_pulau_sambu.jpg, w25; align:L;rowspan:2;');
                $table1->easyCell('');
                $table1->easyCell('BPB - Request', 'font-size:12; font-style:BU;align:C;colspan:3');
                $table1->easyCell('');
                $table1->printRow();
 

             $table1->easyCell("<b>PT. Pulau Sambu</b>/\n<i>Sungai Guntung - Inhil - Riau</i>",'colspan:2;');
             $table1->easyCell("\nRequest No\nRequest Date \nDepartment \nWarehouse");
             $table1->easyCell(": \n: \n: \n:");
             $table1->easyCell($bpbrequest[0]['PABID'] . " \n" . 
                                date("d/m/Y", strtotime($bpbrequest[0]['TransDate'])) . " \n" . 
                                $bpbrequest[0]['DeptName'] . " \n" . 
                                $bpbrequest[0]['WHSName']);             
             
             $table1->printRow();


             $table1->endTable(4);


            
        }

         private function Data($pdf,$bpbrequest) {

            $table1=new easyTable($pdf,'{10,100,15,30,50}',' border:1;'); // 2 = column
            $table1->easyCell('No.','font-size:8; font-style:B;align:R;valign:M;');
            $table1->easyCell("Item \nDescription",'font-size:8; font-style:B;align:L;valign:M;');
            $table1->easyCell('Uom','font-size:8; font-style:B;align:L;valign:M;');
            $table1->easyCell('Qty','font-size:8; font-style:B;align:R;valign:M;');
            $table1->easyCell('Remark','font-size:8; font-style:B;align:L;valign:M;');

            $table1->printRow();

            $Nomor = 0;
            foreach ($bpbrequest as $row) {
                $Nomor = $Nomor + 1 . '.';
                $table1->rowStyle('border:{T,B,L,R};');
                $table1->easyCell($Nomor,'font-size:8;align:R;valign:T;');
                $table1->easyCell($row['OldItemID'] . "\n" . $row['ItemName'],'font-size:8; font-style:R;align:L;valign:T;paddingY:1;');
                $table1->easyCell($row['UOMName'],'font-size:8;align:L;valign:T;');
                $table1->easyCell(number_format($row['Qnty'],2,",","."),'font-size:8;align:R;valign:T;'); 
                $table1->easyCell($row['Remark'],'font-size:8;align:L;valign:T;');

                $table1->printRow();

                // $table1->rowStyle('border:{B,L,R};');
                // $table1->easyCell('Nama : ' . $row['ItemName'] ,'font-size:8;align:L;valign:T;paddingY:0');


                // $table1->printRow();



            }


            // $table1->easyCell("BPB Remark : \n" . $bpbrequest[0]['BPBRemark'] ,'font-size:8;align:L;valign:T;colspan:7;');
            // $table1->printRow();


             $table1->endTable(3);
        }


        //Footer
        private function footer($pdf,$bpbrequest) {

            $table1=new easyTable($pdf,'{75,75,75,75}','font-size:8; font-style:B;border:0;border-color:#1a66ff;'); // 2 = column
            $table1->easyCell('Dokumen ini telah disetujui secara elektronik. Tanda tangan tidak diperlukan. ', 'font-style:BIU;align:L;colspan:4;');
            $table1->printRow();
            
            $table1->easyCell('Created By : ', 'font-style:B;align:L;');
            $table1->easyCell('Approved By : ', 'font-style:B;align:L;');
            $table1->easyCell('');
            $table1->easyCell('');
            $table1->printRow();

            $table1->easyCell(($bpbrequest[0]['CreatedLoginName']==NULL?"-" : $bpbrequest[0]['CreatedLoginName']) . " \n" . 
                              ($bpbrequest[0]['CreatedPositionName']==NULL?"-" : $bpbrequest[0]['CreatedPositionName']) . " \n" . 
                              ($bpbrequest[0]['CreatedDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($bpbrequest[0]['CreatedDate']))) ,
                              'font-style:R;align:L;');
            $table1->easyCell(($bpbrequest[0]['DeptApprovalLoginName']==NULL?"-" : $bpbrequest[0]['DeptApprovalLoginName']) . " \n" . 
                              ($bpbrequest[0]['DeptApprovalPositionName']==NULL?"-" : $bpbrequest[0]['DeptApprovalPositionName']) . " \n" . 
                              ($bpbrequest[0]['DeptApprovalDate']==NULL?"-" : date("d/m/Y H:i:s", strtotime($bpbrequest[0]['DeptApprovalDate']))) ,
                              'font-style:R;align:L;');
            $table1->easyCell('');
            $table1->easyCell('');

            $table1->printRow();




            $table1->endTable(3);


            
        }


}
