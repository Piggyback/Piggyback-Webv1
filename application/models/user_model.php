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
        $this->db->select('Users.uid AS userID, Users.fbid AS fbid, Users.email AS email, Users.firstName AS firstName, Users.lastName AS lastName');
        $this->db->from('Users');
        $this->db->where(array('fbid' => $fbid));

        return $this->db->get()->result();
    }
    
    function add_user($fbid, $email, $firstName, $lastName)
    {
        $limit = 1;
        $query = $this->db->get_where('Users', array('fbid' => $fbid), $limit);
//        
        if ($query->num_rows() == 0) {
            // add new user
            $newUser = array('fbid' => $fbid, 'email' => $email, 'firstName' => $firstName, 'lastName' => $lastName);
            $this->db->insert('Users', $newUser);
        
            $uid = $this->db->insert_id();
            
            return $uid;
        } else {
//             return existing user
            $user_result = $query->result();
            return $user_result[0]->uid;
        }
    }
    
    function check_if_user_exists($fbid)
    {
        $limit = 1;
        
        return $this->db->get_where('Users', array('fbid' => $fbid), $limit)->num_rows();
    }
    
    function get_all_users()
    {
        return $this->db->get('Users')->result();
    }
    
    function get_friends_for_current_user($currentUserUID)
    {
        $this->db->select('Users.uid AS uid, Users.fbid AS fbid, Users.email AS email, Users.firstName AS firstName, Users.lastName as lastName');
        $this->db->from('Users');
        $this->db->join('Friends', 'Users.uid = Friends.uid1');
        $this->db->where('Friends.uid2', $currentUserUID);
        $friendsResult1 = $this->db->get()->result();
        
        $this->db->select('Users.uid AS uid, Users.fbid AS fbid, Users.email AS email, Users.firstName AS firstName, Users.lastName as lastName');
        $this->db->from('Users');
        $this->db->join('Friends', 'Users.uid = Friends.uid2');
        $this->db->where('Friends.uid1', $currentUserUID);
        $friendsResult2 = $this->db->get()->result();
        
        return array_merge($friendsResult1, $friendsResult2);
    }
    
    function add_friends_for_current_user($currentUserUID, $friendsUID)
    {
        $insertData = array();
        
        foreach ($friendsUID as $currentFriendUID) {
            $addNewRow = array('uid1' => $currentUserUID, 'uid2' => $currentFriendUID);
            array_push($insertData, $addNewRow);
        }
        
        if (count($insertData) > 0) {
            $this->db->insert_batch('Friends', $insertData);
        }
    }
}

?>