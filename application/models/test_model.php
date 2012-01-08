<?php
class test_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
    }
    
    function get_referral_items() {
        
        $this->load->database();
        
//        $where = $data['where'];
//        $onCriterion = $data['onCriterion'];
        $where = "((uid1 != 14 AND uid2 != 14) AND 
                ( uid1 IN (14,1,14,2,14,12,14,13) OR
                uid2 IN (14,1,14,2,14,12,14,13) ))";
//        $where = "uid2 != 14";
        
        $data['where'] = $where;
        
        $this->get_referral_items_test($data);
    }
    
    function get_referral_items_test($data) {
        $where = $data['where'];
        $data['itemType'] = 'friend-activity-tab';
        
        // return rid's with most recent activity (comments / likes)
        $q = "(select rid, date
              from Likes
              where rid in (select rid from Referrals where " . $where . "))
              union
              (select rid, date
              from Comments
              where rid in (select rid from Referrals where " . $where . "))
              order by date desc;";

//        echo $q;
//        $q = "SELECT rid, date FROM Likes WHERE rid IN (SELECT rid FROM Referrals WHERE uid2 = 14);";
//        $q = "SELECT rid FROM Referrals WHERE uid2 != 14";
//        $q = "SELECT rid FROM Referrals WHERE uid2 = 14";
//        $q = "SELECT rid, date FROM Likes WHERE rid IN (5, 6, 7, 8, 9, 10)";
//        $q = "SELECT rid FROM Referrals WHERE uid=14;";

        $res = mysql_query($q);
//        $res = $this->db->query($q)->result();
        //var_dump($res);

        // create an ordered list of these rid's to order the results of the details query below
        $updatedRIDs = array();

        if (mysql_num_rows($res) > 0) {
            $updatedRIDsStr = "(";
            while ($row = mysql_fetch_row($res)) {
                if (!in_array($row[0],$updatedRIDs)) {
                    array_push($updatedRIDs, $row[0]);
                    $updatedRIDsStr = $updatedRIDsStr . $row[0] . ",";
                }
            }

            $updatedRIDsStr[strlen($updatedRIDsStr)-1] = ")";
        }

        $detailsQ = "SELECT *, Referrals.comment AS ReferralsComment, Referrals.date AS refDate
                     FROM Referrals
                     LEFT JOIN Users
                     ON Users.uid = uid1";

        if (mysql_num_rows($res) > 0) {
            $detailsQ = "$detailsQ WHERE Referrals.rid IN $updatedRIDsStr
                                   ORDER BY CASE Referrals.rid";

            for ($i = 0; $i < count($updatedRIDs); $i++) {
                $detailsQ = $detailsQ . " WHEN " . $updatedRIDs[$i] . " THEN " . ($i+1) . "\n";
            }
            $detailsQ = $detailsQ . "END";
        }
        
        $result = ($this->db->query($detailsQ)->result());
        
//        foreach($result as $key => $row)
//        {
//            // get the array of vendor detail(s)
//            $rid = $row->rid;
//            $lid = $row->lid;
//            $VendorList = array();
//
//            if ( $lid != 0 ) {
//                // if the referral is a list
//                $this->db->select('*');
//                $this->db->from('Lists');
//                $this->db->where('lid', $lid);
//                $vidList = $this->db->get()->result();
//
//                if(array_filter($vidList)) {
//                    foreach($vidList as $vidRow)
//                    {
//                        $vid = $vidRow->vid;
//
//                        $this->db->select('*');
//                        $this->db->from('Vendors');
//                        $this->db->where('id', $vid);
//
//                        $vendorDetails = $this->db->get()->result();
//
//                        // make sure that there is a corresponding record in
//                        // referral details
//                        if(array_filter($vendorDetails)) {
//                            if($vendorDetails[0] === NULL ) {
//                                unset($result[$key]);                                
//                            } else {
//                                $VendorList[] = $vendorDetails;
//                            }
//                        } else {
//                            unset($result[$key]);
//                        }
//                    }
//                    
//                    // retrieve userlist name and details
//                    $this->db->select('*');
//                    $this->db->from('UserLists');
//                    $this->db->where('lid', $lid);
//
//                    $row->UserList = $this->db->get()->result();
//                } else {
//                    unset ($result[$key]);
//                }
//
//            } else {
//                // if the referral is single vendor, then
//                $this->db->select('*');
//                $this->db->from('ReferralDetails');
//                $this->db->where('ReferralDetails.rid', $rid);
//
//                // ReferralDetails is an associative array that holds the vendor information
//                $ReferralDetails = $this->db->get()->result();
//
//                if($ReferralDetails) {
//                    $vid = $ReferralDetails[0]->vid;
//
//                    $this->db->select('*');
//                    $this->db->from('Vendors');
//                    $this->db->where('id', $vid);
//
//                    $vendorDetails = $this->db->get()->result();
//
//                    if($vendorDetails) {
//                        if($vendorDetails[0] === NULL ) {
//                        } else {
//                            $VendorList[0] = $vendorDetails;
//                        }
//                    }
//                } else {
//                    // if no corresponding record in referralDetails
//                    // remove self from array
//                    unset($result[$key]);
//                }
//            }
//
//            $row->VendorList = array("VendorList" => $VendorList);
//
//            // retrieve a 'Likes' array of uid's
//            $this->db->select('*');
//            $this->db->from('Likes');
//            $this->db->where('rid', $rid);
//            $LikesList = $this->db->get()->result();
//
//            $row->LikesList = array("LikesList" => $LikesList);
//
//            // retrieve a 'Comments' with uid's
//            $this->db->select('*');
//            $this->db->from('Comments');
//            $this->db->join('Users', 'Users.uid = Comments.uid', 'left');
//            $this->db->order_by('date', 'asc');
//            $this->db->where('rid', $rid);
//            $CommentsList = $this->db->get()->result();
//
//            $row->CommentsList = array("CommentsList" => $CommentsList);
//
//            // add whether the user has Liked the status or not
//            $this->db->from('Likes');
//            $this->db->where('rid', $rid);
//            $this->db->where('uid', '14');
//
//            if ($this->db->count_all_results() == 0)
//            {
//                $row->alreadyLiked = "0";
//            } else {
//                // user has already liked it
//                $row->alreadyLiked = "1";
//            }
//            
//            if( $data['itemType'] != "inbox-tab" ) {
//                // add recipient detail information
//                $recipientUid = $row->uid2;
//                $this->db->select('*');
//                $this->db->from('Users'); 
//                $this->db->where('uid', $recipientUid);
//                $RecipientDetails = $this->db->get()->result();
//
//                $row->RecipientDetails = array("RecipientDetails" => $RecipientDetails);
//            }
//        }
        
//        var_dump($result);
//        echo "<BR><BR><BR>";
        
        var_dump($result);
        
        echo "<br><br>";
        
        var_dump(json_encode($result));
    }
    
    
    public function get_referral_items($data) {
        $myUID = $data['uid'];
        
        // added my mike gao to display inbox from php
//        if ($data['rowStart'] == null)
        if (!array_key_exists('rowStart', $data))
            $data['rowStart'] = $this->input->post('rowStart');
        
        // added by mike gao to display inbox from php
//        if ($data['itemType'] == null)
        if (!array_key_exists('itemType', $data))
            $data['itemType'] = $this->input->post('itemType');
        
        $data['rowsRequested'] = 3;
        
        $result = $this->get_corresponding_item_result($data);
        
        
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
