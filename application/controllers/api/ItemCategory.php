<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class ItemCategory extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ItemCategory_model');
    }
    public function index_get()
    {
        $id = $this->get('id');
        if ($id === null) {
            $category = $this->ItemCategory_model->getCategory();
        } else {
            $category = $this->ItemCategory_model->getCategory($id);
        }

        if ($category) {
            $this->response([
                'status' => true,
                'data' => $category
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'data' => 'Item Category Not Found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
