<?php
class test_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
    }
    
    
    // cute.
    function testMethod()
    {
        $hello = "hahaha";
        return $hello;
    }
    
    public function get_likes() {
        $uid = $_POST["uid"];
        
        $q = "SELECT Referrals.rid, Likes.uid, Likes.date 
              FROM Likes 
              LEFT JOIN Referrals 
              ON Likes.rid = Referrals.rid 
              WHERE Referrals.uid1 = $uid";
        
        $result = mysql_query($q);
        $count = mysql_num_rows($result);
        echo $count;
        
    }
    
    function refer_list() {
        $this->load->database();
        $lid = $_POST["lid"];
        $uid = $_POST["uid"];
        $numFriends = $_POST["numFriends"];
        $uidFriends = json_decode($_POST["uidFriends"]);
        $date = $_POST["date"];
        $comment = $_POST["comment"];
                
        $q = "INSERT INTO Referrals VALUES ";
        for ($i = 0; $i < $numFriends; $i++) {
           $friendNum = "friend".$i;
           $uidFriend = $uidFriends->$friendNum;
           $q = "$q (NULL, $uid, $uidFriend, \"$date\", $lid, \"$comment\"),";
        }
        
        $q = substr($q,0,-1);
        echo $q;
              
        // run the query to add one entry to the Referral table for each friend referred to the list
        if($numFriends > 0) {
            mysql_query($q);
            
            // get all vendors in the referred list
            $getVendorQuery = "SELECT vid from Lists where lid = $lid";
            echo "\n\n$getVendorQuery\n\n";
            $vendorResult = mysql_query($getVendorQuery);
            
            // set up string for adding all rows - one for each vendor in the list, for each friend
            $addRefDetsQuery = "INSERT INTO ReferralDetails VALUES ";
            
            // get RID that was just inserted into Referrals table
            for ($i = 0; $i < $numFriends; $i++) {
                $friendNum = "friend".$i;
                $uidFriend = $uidFriends->$friendNum;
                $getRIDquery = "SELECT rid FROM Referrals WHERE uid1 = $uid AND uid2 = $uidFriend AND date = \"$date\" AND lid = $lid AND comment = \"$comment\"";
                echo "\n\n$getRIDquery\n\n";
                $result = mysql_query($getRIDquery);
                $resultRow = mysql_fetch_row($result);
                $rid = $resultRow[0];

                while($vid = mysql_fetch_row($vendorResult)) {
                    // add new row to ReferralDetails table using this rid and the vid
                    $addRefDetsQuery = "$addRefDetsQuery ($rid,\"$vid[0]\",0,0),";
                }
                
                // move pointer to vendor array back to the beginning so it can be traversed again for next friend
                mysql_data_seek($vendorResult,0);
            }
            
            // remove last comma and run query to add rows to ReferralDetails table
            $addRefDetsQuery = substr($addRefDetsQuery,0,-1);
            
            if(mysql_num_rows($vendorResult) > 0) {
                echo $addRefDetsQuery;
                mysql_query($addRefDetsQuery);
            }
        }
    }
}
?>
