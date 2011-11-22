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
        
        foreach($_POST['box'] as $checkedItem)
        {
            // Get the corresponding VID from the Vendor name
            $retrieveVendorInfo = "SELECT vid
                                   FROM Vendor 
                                   WHERE name = '$checkedItem'";
            $resourceId = mysql_query($retrieveVendorInfo) or die("My sql error: " . mysql_error());
            $vid = mysql_fetch_row($resourceId);
            
            echo $checkedItem . ": " . $vid[0];
            echo "</br>";
            
            $comment = "test comment here";
            $addListQuery = "INSERT INTO List(lid,vid,date,comment) VALUES ($lid, $vid[0], $date, \"$comment\")";
            mysql_query($addListQuery) or die("My sql error: " . mysql_error());
        }
        
        
        echo "success";
    }
    
    
    public function getList($_POST)
    {
        
        
        $item = $_POST['box'];

        // get all vid from lid
        $retrieveVID = "SELECT vid
                        FROM List
                        WHERE lid = '$item[0]'";
        $resourceId = mysql_query($retrieveVID) or die("My sql error: " . mysql_error());

        while($row = mysql_fetch_array($resourceId))
        {
            $vidItem = $row['vid'];
            echo $vidItem." ";
            
            $retrieveVName = "SELECT name
                              FROM Vendor
                              WHERE vid = '$vidItem'";
            $resourceId = mysql_query($retrieveVName);
            while($outputVNames = mysql_fetch_array($resourceId))
            {
                
                echo $outputVNames['name'];
                echo "</br>";
                
            }
            
        }
        
    }

}

?>