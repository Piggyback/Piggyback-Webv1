<?php
class Home_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
    }
    
    function load_friends()
    {
        $this->load->database();
        $this->FBID = $this->input->post('FBID');
        
        $friends = array();
        // TODO: MUST OPTIMIZE WITH JOINT CALLS
        $query = $this->db->get_where('Friends' , array('FBID1' => $this->FBID));
        foreach ($query->result() as $row) {
            $query2 = $this->db->get_where('Users', array('FBID' => $row->FBID2));
            foreach ($query2->result() as $row) {
                $fullName = $row->firstName . ' ' . $row->lastName;
                array_push($friends, $fullName);
            }
        }
        
        $query = $this->db->get_where('Friends', array('FBID2' => $this->FBID));
        foreach ($query->result() as $row) {
            $query2 = $this->db->get_where('Users', array('FBID' => $row->FBID1));
            foreach ($query2->result() as $row) {
                $fullName = $row->firstName . ' ' . $row->lastName;
                array_push($friends, $fullName);
            }
        }
        
        return $friends;
    }
}
?>
