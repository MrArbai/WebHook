<?php
class ItemCategory_model extends CI_Model
{
    public function getCategory($id = null)
    {
        if ($id === null) {
            return $this->db->get('tblMstItemCategory')->result_array();
        } else {
            return $this->db->get_where('tblMstItemCategory', ['CategoryID' => $id])->result_array();
        }
    }
}
