<?php
/**
 * @mikegao
 * 
 * model for friends viewing page
 * 
 * created: 11/29/11
 */
class Home_model extends CI_Model {
   
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
    }
    
    function load_friends($data=array())
    {
        
        $this->load->database();
        $currentUserData = $this->session->userdata('currentUserData');
        $this->uid = $currentUserData['uid'];

        
        $friends = array();
        $query = $this->db->get_where('Friends' , array('uid1' => $this->uid));
        foreach ($query->result() as $row) {
            $query2 = $this->db->get_where('Users', array('uid' => $row->uid2));
            foreach ($query2->result() as $row) {
//                $fullName = $row->first_name . ' ' . $row->last_name;
                $user['firstName'] = $row->firstName;
                $user['lastName'] = $row->lastName;
                $user['fbid'] = $row->fbid;
                array_push($friends, $user);
            }
        }
        
        $query = $this->db->get_where('Friends', array('uid2' => $this->uid));
        foreach ($query->result() as $row) {
            $query2 = $this->db->get_where('Users', array('uid' => $row->uid1));
            foreach ($query2->result() as $row) {
                $user['firstName'] = $row->firstName;
                $user['lastName'] = $row->lastName;
                $user['fbid'] = $row->fbid;
                array_push($friends, $user);
            }
        }
        
        return $friends;
    }
}
?>
