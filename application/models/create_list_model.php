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
    
    public function getAllVendors() {
        $retrieveVendorList = "SELECT name, vid FROM Vendor";
        $vendorNames = $this->db->query($retrieveVendorList)->result();
        return $vendorNames;
    }
    
    public function getListInfo($fieldData) {
        $uid = $fieldData['uid'];          // user list
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
        
        
        // create User List table
        $addUserListQuery = "INSERT INTO UserLists(uid,name,date) VALUES ($uid,\"$listName\",$date)";
        
        mysql_query($addUserListQuery) or die(mysql_error());
     
        
        // get the uniquely generated LID from UserLists
        $lid = mysql_insert_id();
        
        // create individual list that contains vendor information and comments
        foreach($fieldData['box'] as $checkedItem)
        {
            $vid = intval($checkedItem);    // vid is now an int
            
            $comment = "test comment here";
            $addListQuery = "INSERT INTO List(lid, vid, date, comment)
                             VALUES ($lid, $vid, $date, \"$comment\")";
            mysql_query($addListQuery) or die("My sql error: " . mysql_error());
        }
    }
    
    public function getVendorList($listId)
    {
        // given list ID, return vendor names
        
        $this->db->select('*');
        $this->db->from('Vendor');
        $this->db->join('List', 'List.vid = Vendor.vid', 'left');
        $this->db->where('lid', intval($listId));
       
        $vendorNameList = $this->db->get()->result();
        
        return $vendorNameList;
    }
    
    
//    public function getList($dataBox)
//    {
//        foreach($dataBox['box'] as $checkedItem)
//        {
//            // use join to return big table with vendor names accessible, given LID
//            $this->db->select('vid');
//            $this->db->from('List');
//            $this->db->where('lid', intval($checkedItem));
//            $this->db->join('UserLists', 'UserLists.vid = List.vid');
//            
//            
//            $vidResult = $this->db->get()->result();
//            
//            
//            $this->db->select('name');
//            $this->db->from('Vendor');
//            $this->db->join('List', 'List.vid = Vendor.vid');
//            
//            
//            
//            // get all vid from lid (checkedItem)
//            $this->db->select('vid');
//            $this->db->from('List');
//            $this->db->where('lid', intval($checkedItem));
//            $query = $this->db->get();
//            
//            // print the list name between lists right here
//            
//            foreach ($query->result() as $row)
//            {
//                $vidItem = intval($row->vid);
//                $this->db->select('name');
//                $this->db->from('Vendor');
//                $this->db->where('vid', $vidItem);
//                $queryObj = $this->db->get();
//  
//                foreach($queryObj->result() as $outputVNames)
//                {   
//                    echo $outputVNames->name;
//                    echo "</br>";
//                }
//            }
//        }
//    }
}

?>