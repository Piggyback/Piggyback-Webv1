<?php
/* 
    Document   : home_model.php
    Created on : Nov 30, 2011, 6:49:47 AM
    Author     : gaobi
    Description:
        Home model.
*/

/* 
   TO-DOs:
*/
class Home_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
    }
    
    function load_my_lists($currentUID)
    {
        $this->load->database();
        $query = $this->db->get_where('UserLists', array('uid' => $currentUID));
        
        return $query->result();
    }
}
?>
