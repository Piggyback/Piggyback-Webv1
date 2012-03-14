<?php

/**
 *  @mike gao
 *   */

class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Functions called by home controller
     */
    function get_user($fbid)
    {
        $query = $this->db->get_where('Users', array('fbid' => $fbid));

        return $query->result();
    }
}

?>