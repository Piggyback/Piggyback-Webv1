<?php
/* 
    Document   : login_model.php
    Created on : Nov 30, 2011, 6:49:47 AM
    Author     : gaobi
    Description:
        Login model.
*/

/* 
   TO-DOs:
*/

class Login_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
        $this->load->database();
    }
    
    function check_if_user_exists($fbid)
    {
        $this->fbid = $fbid;
        
        // check if user exists
        $query = $this->db->get_where('Users', array('fbid' => $this->fbid));
        $result = $query->result();
        if (count($result) > 0) {
            // user exists -- store session data
            $currentUser = $result[0];
            
            $currentUserData = array(
                'uid' => $currentUser->uid,
                'fbid' => $currentUser->fbid,
                'email' => $currentUser->email,
                'firstName' => $currentUser->firstName,
                'lastName' => $currentUser->lastName
            );
            
            // store info in current session
            $this->session->set_userdata('currentUserData', $currentUserData);
        }
        // else, user does not exist; move on to add_new_user();
        return (count($result));
        
    }
    
    // add_user only called if user current user does not exist already
    function add_user($fbid, $email, $firstName, $lastName)
    {
        $this->fbid = $fbid;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        
        $this->db->insert('Users', $this);
        
        // retrieve uid and store in current session
        $query = $this->db->get_where('Users', array('fbid' => $this->fbid));
        $result = $query->result();
        $currentUser = $result[0];

        $currentUserData = array(
            'uid' => $currentUser->uid,
            'fbid' => $currentUser->fbid,
            'email' => $currentUser->email,
            'firstName' => $currentUser->firstName,
            'lastName' => $currentUser->lastName
        );

        // store info in current session
        $this->session->set_userdata('currentUserData', $currentUserData);
        
        
        // IMPORTANT: Add escaped quotation marks for string fields.
        // Also, decided to overwrite the default 'insert' query to do potential duplicate addition attempts (wanted to pass them off as warnings instead of errors)
//        $this->db->query('INSERT IGNORE INTO Users (fbid, email, firstName, lastName) VALUES (' . $this->fbid . ', \'' . $this->email. '\', \'' . $this->firstName . '\', \'' . $this->lastName . '\')');
    }
    
    function search_for_friends($data)
    {
        $this->data = $data;
        $currentUserData = $this->session->userdata('currentUserData');
        $this->my_uid = $currentUserData['uid'];

        // loop through each Facebook friend to check if they are in our Users table
        foreach ($this->data as $value) {
            $query = $this->db->get_where('Users', array('fbid' => $value['id']));
            // if facebook friend exists, add to our Friends table
            $result = $query->result();
            if (count($result) > 0) {
                $friend = $result[0];
                //TODO: MG - update from fbid to uid
                $this->db->query("INSERT IGNORE INTO Friends (uid1, uid2) VALUES (?,?)",array($this->my_uid,$friend->uid));
            }
        }

    }
}
?>
