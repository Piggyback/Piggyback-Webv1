<?php
class Login_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
    }
    
    function add_new_user()
    {
        $this->load->database();
        
        $this->FBID = $this->input->post('FBID');
        $this->email = $this->input->post('email');
        $this->firstName = $this->input->post('firstName');
        $this->lastName = $this->input->post('lastName');
        
//        $this->db->insert('Users', $this);
        // IMPORTANT: Add escaped quotation marks for string fields.
        // Also, decided to overwrite the default 'insert' query to do potential duplicate addition attempts (wanted to pass them off as warnings instead of errors)
        $this->db->query('INSERT IGNORE INTO Users (FBID, email, firstName, lastName) VALUES (' . $this->FBID . ', \'' . $this->email. '\', \'' . $this->firstName . '\', \'' . $this->lastName . '\')');
    }
    
    function search_for_friends()
    {
        $this->load->database();
        
        $this->data = $this->input->post('data');
        $this->my_id = $this->input->post('my_id');
        
//        $this->db->select('FBID');
//        $allUsers = $this->db->get('Users');
//        // check if friend id exists in Users database
//        foreach ($this->data as $value) {
//            // if true, add friend pair to 'Friends' table
//            if (in_array($value['id'], $allUsers->result()) == TRUE) {
//                print("GOT IT! ");
//            }
//            
//        }
        foreach ($this->data as $value) {
            $query = $this->db->get_where('Users', array('FBID' => $value['id']));
            if (count($query->result()) > 0) {
                // friend exists in 'Users' table. Add pair to 'Friends' table!
//                print("GOT IT! ");
//                $friendship = array (
//                    'UID1' => $this->my_id,
//                    'UID2' => $value['id']
//                );
//                $this->db->insert('Friends', $friendship);
                $this->db->query('INSERT IGNORE INTO Friends (FBID1, FBID2) VALUES ( ' . $this->my_id. ', ' . $value['id'] . ')');
            }
        }

    }
}
?>
