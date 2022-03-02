<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class M_Warehouse extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/M_Warehouse_model');
    }
    public function index_get()
    {
        $id = $this->get('id');
        if ($id === null) {
            $category = $this->M_Warehouse_model->getWarehouse();
        } else {
            $category = $this->M_Warehouse_model->getWarehouse($id);
        }

        if ($category) {
            $this->response([
                'status' => true,
                'data' => $category
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'data' => NULL
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
