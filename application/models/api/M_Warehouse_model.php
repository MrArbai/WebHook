<?php
class M_Warehouse_model extends CI_Model
{
    public function getWarehouse($id = null)
    {
        if ($id === null) {
            return $this->db->get('tblMstWarehouse')->result_array();
        } else {
            return $this->db->get_where('tblMstWarehouse', ['WHSID' => $id])->result_array();
        }
    }
}
