<?php
class Home_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
    }
    
    function load_friends()
    {
        require '../assets/facebook-php-sdk/facebook.php';
        $facebook = new Facebook(array(
            'appId' => '251920381531962',
            'secret' => 'c364ef6ec8d7cb57860983ec4be053b4',
        ));
        
        //TODO: ASSUMING USER IS ALREADY LOGGED IN. WHAT IF NOT?
        $user = $facebook->getUser();
        
        $this->load->database();
        $this->FBID = $user;
        
        $friends = array();
        // TODO: MUST OPTIMIZE WITH JOINT CALLS
        $query = $this->db->get_where('Friends' , array('FBID1' => $this->FBID));
        foreach ($query->result() as $row) {
            $query2 = $this->db->get_where('Users', array('FBID' => $row->FBID2));
            foreach ($query2->result() as $row) {
                $fullName = $row->first_name . ' ' . $row->last_name;
                array_push($friends, $fullName);
            }
        }
        
        $query = $this->db->get_where('Friends', array('FBID2' => $this->FBID));
        foreach ($query->result() as $row) {
            $query2 = $this->db->get_where('Users', array('FBID' => $row->FBID1));
            foreach ($query2->result() as $row) {
                $fullName = $row->first_name . ' ' . $row->last_name;
                array_push($friends, $fullName);
            }
        }
        
        return $friends;
    }
}
?>
