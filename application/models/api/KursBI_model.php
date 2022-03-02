<?php
class KursBI_model extends CI_Model
{
    public function getKurs($id = null)
    {
        if ($id === null) {
            return $this->db->get('tblMstItemCategory')->result_array();
        } else {
            return $this->db->get_where('tblMstItemCategory', ['CategoryID' => $id])->result_array();
        }
    }
}
