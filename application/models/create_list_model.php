<?php

/*
 * @andyjiang
 * 
 * model to manage all list interaction with MySQL
 * 
 */

class Create_List_Model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();
    }
    
    public function get_all_vendors() {
        $retrieveVendorList = "SELECT name, id FROM Vendors";
        $vendorNames = $this->db->query($retrieveVendorList)->result();
        return $vendorNames;
    }
    
    public function get_list_info($fieldData) {
        $uid = $fieldData['uid'];          // user list
        $retrieveListInfo = "SELECT * FROM UserLists WHERE uid = $uid";
        $listInfo = $this->db->query($retrieveListInfo)->result();
        
        return $listInfo;
    }
        
    public function add_list($fieldData) {        
        // grabs the data from create_list_view
        $uid = $fieldData['uid'];
        $listName = $fieldData['listName'];
        $date = time();
                
        // create User List table
        $addUserListQuery = "INSERT INTO UserLists(uid,name,date) VALUES ($uid,\"$listName\",$date)";
        
        mysql_query($addUserListQuery) or die(mysql_error());
     
        
        // get the uniquely generated LID from UserLists
        $lid = mysql_insert_id();
        
        // create individual list that contains vendor information and comments
        foreach($fieldData['box'] as $checkedItem)
        {
            $vid = $checkedItem;    // vid is now an string, varchar
            
            $comment = "test comment here";
            $addListQuery = "INSERT INTO Lists(lid, vid, date, comment)
                             VALUES ($lid, \"$vid\", $date, \"$comment\")";
            mysql_query($addListQuery) or die("My sql error: " . mysql_error());
        }
    }
    
    public function get_vendor_list($listId)
    {
        // given list ID, return vendor names
        
        $this->db->select('*');
        $this->db->from('Vendors');
        $this->db->join('Lists', 'Lists.vid = Vendors.id', 'left');
        $this->db->where('lid', intval($listId));
       
        $vendorNameList = $this->db->get()->result();
        
        return $vendorNameList;
    }
    
    
}

?>