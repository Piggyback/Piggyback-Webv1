<?php

/*
 * @andyjiang
 * 
 * model manages all referral objects and interactions with MySQL
 * 
 */

class Refer_List_Model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();
    }
    
    public function get_friends() {
        // given uid, return all of that user's friends
        //    note: not specific to refer_list_model
        $uid = 2409887757;
        $retrieveFriendList = "SELECT uid2 FROM Friends WHERE uid1 = $uid";
        $friendNames = $this->db->query($retrieveFriendList)->result();
        
        return $friendNames;
    }

    
    public function get_list_info() {
        // given uid, return all of that user's lists
        //     note: not specific to refer_list_model
        $uid = 2409887757;
        //$uid = $fieldData['uid'];          
        $retrieveListInfo = "SELECT * FROM UserLists WHERE uid = $uid";
        $listInfo = $this->db->query($retrieveListInfo)->result();
        
        return $listInfo;
    }
    
    
    public function add_referral($fieldData) {       
        // insert a new row into Referrals table with the below data
        // 
        // rid      (auto increment, unique key)
        // uid1     given (current user)
        // uid2     given (target user)
        // date     generated
        // lid      if referring single vendor, lid=0; else lid corresponds to referred list
        // 
        
        // get uid1 from session
        $uid1 = 2409887757;
        
        // get uid2 from fieldData
        $uid2 = $fieldData['uid2'];
        
        
        foreach($fieldData['uid2'] as $uid)
        {
            foreach ($fieldData['box'] as $list) {
                $date = time();
                // create User List table
                $status = "OK";
                $addUserListQuery = "INSERT INTO ReferralLists(uid1, uid2, lid, date, status) VALUES ($uid1, $uid, $list, $date, \"$status\")";
                mysql_query($addUserListQuery) or die(mysql_error());
            }
        }
        
        echo "add referral list success";
    }
    
    
    public function get_referral()
    {
        // use JOIN to retrieve table that includes the name of the referrer and the name of the list
        //
        $uid1 = 3012081112;
        
        $this->db->select('*');
        $this->db->from('Referrals');
        $this->db->join('Users', 'Users.uid = ReferralLists.uid1', 'left');
        $this->db->join('UserLists', 'UserLists.lid = ReferralLists.lid', 'left');
        $this->db->where('uid2', $uid1);
        $result = $this->db->get()->result();

        return $result;

    }
}

?>