<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Create_List_Model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();
    }
    
    public function getVendors() {
        $retrieveVendorList = "SELECT name, vid FROM Vendor";
        $vendorNames = $this->db->query($retrieveVendorList)->result();
        return $vendorNames;
    }
    
    public function getListInfo() {
        $uid = 2409887757;          // user list
        $retrieveListInfo = "SELECT * FROM UserLists WHERE uid = $uid";
        $listInfo = $this->db->query($retrieveListInfo)->result();
        
        return $listInfo;
    }
    
    
    public function addList($fieldData) {        
        // grabs the data from create_list_view
        
        // Create a MySQL table in the selected database

        $uid = $fieldData['uid'];
        $listName = $fieldData['listName'];
        $date = time();
        $lid = $fieldData['lid'];
        
        // lid must be unique forever
        
        // create User List table
        $addUserListQuery = "INSERT INTO UserLists(uid,lid,name,date) VALUES ($uid,$lid,\"$listName\",$date)";
        
        mysql_query($addUserListQuery) or die(mysql_error());
     
        // create individual list that contains vendor information and comments
        foreach($fieldData['box'] as $checkedItem)
        {
            $vid = intval($checkedItem);    // vid is now an int
            
            $comment = "test comment here";
            $addListQuery = "INSERT INTO List(lid, vid, date, comment)
                             VALUES ($lid, $vid, $date, \"$comment\")";
            mysql_query($addListQuery) or die("My sql error: " . mysql_error());
        }
        echo "success";
    }
    
    
    public function getList($_POST)
    {
        foreach($_POST['box'] as $checkedItem)
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
        }
    }
}

?>