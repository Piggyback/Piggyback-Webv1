<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class RecommendList_Model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();
    }
    
    public function getFriends() {
        $uid = 2409887757;
        $retrieveFriendList = "SELECT uid2 FROM Friends WHERE uid1 = $uid";
        $friendNames = $this->db->query($retrieveFriendList)->result();
        
        return $friendNames;
    }

    
    public function getListInfo() {
        $uid = 2409887757;
        //$uid = $fieldData['uid'];          
        $retrieveListInfo = "SELECT * FROM UserLists WHERE uid = $uid";
        $listInfo = $this->db->query($retrieveListInfo)->result();
        
        return $listInfo;
    }
    
    
    public function addRecommendLists($fieldData) {        
        // Create a referralList row into the referralList table only once!
        // 
        // note: check edge scenarios depending on check box or radio button for selecting lists and friends.
        //
        // referral list:
        //  uid1    given
        //  uid2    given
        //  lid     given
        //  rid     generated
        //  date    generated
        //  status  ??
        //
         
        $uid1 = 2409887757;
            //$uid1 = $fieldData['uid1'];
        $uid2 = $fieldData['uid2'];
        
        
        foreach($fieldData['uid2'] as $uid)
        {
            foreach ($fieldData['box'] as $list) {
                $date = time();
                // create User List table
                $status = "OK";
                $rid = 1234;
                $addUserListQuery = "INSERT INTO ReferralLists(uid1, uid2, lid, date, status) VALUES ($uid1, $uid, $list, $date, \"$status\")";
                mysql_query($addUserListQuery) or die(mysql_error());
            }
        }
        
        echo "add referral list success";
    }
    
    
    public function getRecommendLists()
    {
        // use JOIN to retrieve table that includes the name of the referrer and the name of the list
        //
        $uid1 = 3012081112;
        
        $this->db->select('*');
        $this->db->from('ReferralLists');
        $this->db->join('Users', 'Users.uid = ReferralLists.uid1', 'left');
        $this->db->join('UserLists', 'UserLists.lid = ReferralLists.lid', 'left');
        $this->db->where('uid2', $uid1);
        $result = $this->db->get()->result();

        return $result;

//        foreach($result as $row) {
//            
//            echo $row->rid . "<br>";
//        }
//        
        /*
        foreach($dataBox['box'] as $checkedItem)
        {
            // get all vid from lid
            $this->db->select('vid');
            $this->db->from('List');
            $this->db->where('lid', intval($checkedItem));
            $query = $this->db->get();
            
            // print the list name between lists right here
            
            foreach ($query->result() as $row)
            {
                $vidItem = intval($row->vid);
                $this->db->select('name');
                $this->db->from('Vendor');
                $this->db->where('vid', $vidItem);
                $queryObj = $this->db->get();
  
                foreach($queryObj->result() as $outputVNames)
                {   
                    echo $outputVNames->name;
                    echo "</br>";
                }
            }
        }*/
    }
}

?>