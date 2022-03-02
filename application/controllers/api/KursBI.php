<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class KursBI extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->model('ItemCategory_model');
    }
    public function index_get()
    {
         $tgl = $this->get('tgl');

          $url='https://www.bi.go.id/biwebservice/wskursbi.asmx/getSubKursLokal2?tgl=' . date("Y-m-d", strtotime($tgl)) ;

          $response = file_get_contents($url);
 

            
            $sxe = new SimpleXMLElement($response);
            $sxe->registerXPathNamespace('d', 'urn:schemas-microsoft-com:xml-diffgram-v1');
            $result = $sxe->xpath("//NewDataSet/Table");
            //echo "<pre>";

            // print_r ($result);
            
            $ToJson=json_encode($result);

            $Tojsondecode = json_decode($ToJson);


            $arr1 = array();
            $arr2 = array();



            foreach ($Tojsondecode as $list) {
                //echo 'Currency : ' . $list->mts_subkurslokal."<br>";

                array_push($arr1, array(
                                            'CurrencyID' =>  $list->mts_subkurslokal , 
                                            'TransDate' => date("d/m/Y", strtotime($list->tgl_subkurslokal)),
                                            'Jual' => $list->jual_subkurslokal,
                                            'Beli' => $list->beli_subkurslokal
                                        )
                            );

 

            }


        if ($arr1) {
            $this->response([
                'status' => true,
                'data' => $arr1
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'data' => 'Kurs Not Found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
