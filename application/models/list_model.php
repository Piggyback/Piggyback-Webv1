<?php

/**
 *  @mike gao
 *   */

class List_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_my_lists($currentUID)
    {
        $query = $this->db->get_where('UserLists', array('uid' => $currentUID));

        return $query->result();
    }


    public function get_list_content()
    {
        $lid = $this->input->post('lid');
        $query = $this->db->query("SELECT *, Lists.date AS listsDate FROM Lists INNER JOIN Vendors ON Lists.vid = Vendors.id WHERE Lists.lid = " . $lid . ";");

        echo json_encode($query->result());
    }

}
