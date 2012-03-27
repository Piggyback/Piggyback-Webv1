<?php

class Referrals_Model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_current_date()
    {
        $date = date("Y-m-d H:i:s");
        return $date;
    }
    
    public function add_new_comment($uid,$rid,$comment)
    {
        $date = date("Y-m-d H:i:s");
            
        // insert new row into Comments table
        $newComment = array(
            'rid' => $rid,
            'uid' => $uid,
            'date' => $date,
            'comment' => $comment
        );

        $this->db->insert('Comments', $newComment);

        // get the uniquely auto-incremented RID from Referrals
        $cid = mysql_insert_id();
        return $cid;
    }
    
    public function remove_comment($uid,$cid)
    {
        // TODO: add error handling for deleting when not your own comment? @andyjiang

        // remove the comment
        $this->db->where('cid', $cid);
        $this->db->delete('Comments');
    }
    
    public function is_already_liked($uid,$rid)
    {
        $this->db->from('Likes');
        $this->db->where('rid', $rid);
        $this->db->where('uid', $uid);

        // if numRows = 0,
        //    then USER NOT YET LIKED
        // else
        //    USER ALREADY LIKED
        return $this->db->count_all_results();
    }

    public function add_new_like($uid,$rid)
    {
        $date = date("Y-m-d H:i:s");

        $newLike = array(
            'rid' => $rid,
            'uid' => $uid,
            'date' => $date
        );

        $this->db->insert('Likes', $newLike);
    }

    public function remove_like($uid,$rid)
    {
        $this->db->where('rid', $rid);
        $this->db->where('uid', $uid);
        $this->db->delete('Likes');
    }

    /*
     * use front-end so can delete this
     */
    public function get_like_count($rid)
    {
        // ajax wants the count of the new likes
        $this->db->from('Likes');
        $this->db->where('rid', $rid);

        // return the count of the result
        return $this->db->count_all_results();
    }
    
    public function get_referral_items($uid,$rowStart,$rowsReq,$itemType) 
    {       
//        // added my mike gao to display inbox from php
//        if (!array_key_exists('rowStart', $data))
            $data['rowStart'] = $rowStart;
//        
//        // added by mike gao to display inbox from php
//        if (!array_key_exists('itemType', $data))
            $data['itemType'] = $itemType;
//        
//        if (!array_key_exists('rowsRequested', $data)) {
            $data['rowsRequested'] = $rowsReq;
//        }
        
        $rowsRequested = $data['rowsRequested'];
        $rowsNotCorrupted = 0;          // counter for not corrupted rows
        $returnedResult = array();
        $maxSize = 0;                   // max size
        $data['rowsRequested'] = $data['rowsRequested'] + 1;
        
        while ( $rowsNotCorrupted < $rowsRequested ) {
            $result = $this->get_corresponding_item_result($uid,$data);
            $maxSize = count($result);
            if ( $maxSize < $rowsRequested ) {
                $rowsRequested = $maxSize;
            }

            foreach($result as $key => $row) {
                $isCorrupted = "";       // "" is not corrupted
                // get the array of vendor detail(s)
                $rid = $row->rid;
                $lid = $row->lid;
                $VendorList = array();

                // if the referral is a list
                if ( $lid != 0 ) {
                    // so that you display referred lists as they were at time of referral
                    $query = "SELECT vid, comment FROM Lists WHERE lid = ? AND date < (SELECT date FROM Referrals WHERE rid = ?) AND ((deleted != 1) 
                                OR (deleted = 1 AND deletedDate > (SELECT date FROM Referrals WHERE rid = ?)))";
                    $vidList = $this->db->query($query,array($lid,$rid,$rid,$rid))->result();

                    if(array_filter($vidList)) {
                        foreach($vidList as $vidRow)
                        {
                            $vid = $vidRow->vid;

                            $this->db->select('*');
                            $this->db->from('VendorsFoursquare');
                            $this->db->where('id', $vid);

                            $vendorDetails = $this->db->get()->result();

                            // make sure that there is a corresponding record in
                            // referral details
                            if(array_filter($vendorDetails)) {
                                $vendorDetails['senderComment'] = $vidRow->comment;
                                $VendorList[] = $vendorDetails;
                            } else {
                                $isCorrupted = "2: Error in finding corresponding row in Vendors table. Vendor details data is missing. 
                                                RID: " . $rid . ", LID: " . $lid . ", comment: " . $row->comment;
                            }
                        }
                    } else {
                        $isCorrupted = "3: Error in finding corresponding row in Lists table. No lists attached to this referral. 
                                        RID: " . $rid . ", LID: " . $lid . ", comment: " . $row->comment;
                    }

                    // retrieve userlist name and details
                    $this->db->select('*');
                    $this->db->from('UserLists');
                    $this->db->where('lid', $lid);

                    $userListItem = $this->db->get()->result();

                    if ( !$userListItem ) {
                        // manually put in userList data
                        $x = new stdClass();
                        $x->uid = 0;
                        $x->lid = 0;
                        $x->date = 0;
                        $x->name = "Check out this list!";

                        $row->UserList[0] = $x;
                    } else {
                        $row->UserList = $userListItem;
                    }

                } else {
                    // if the referral is single vendor, then
                    $this->db->select('vid');
                    $this->db->from('Referrals');
                    $this->db->where('Referrals.rid', $rid);

                    // ReferralDetails is an associative array that holds the vendor information
                    $Referrals = $this->db->get()->result();

                    if($Referrals) {
                        $vid = $Referrals[0]->vid;

                        $this->db->select('*');
                        $this->db->from('VendorsFoursquare');
                        $this->db->where('id', $vid);

                        $vendorDetails = $this->db->get()->result();

                        if(array_filter($vendorDetails)) {
                            $VendorList[0] = $vendorDetails;
                        } else {
                            // unset
                            $isCorrupted = "5: Error in finding corresponding row in Vendors table. Vendor details data is missing. 
                                            RID: " . $rid . ", LID: " . $lid . ", comment: " . $row->comment;
                        }
                    } else {
                        // if no corresponding record in referralDetails
                        $isCorrupted = "6: Error in finding corresponding row in Referrals table. No lists attached to this referral. 
                                        RID: " . $rid . ", LID: " . $lid . ", comment: " . $row->comment;
                    }
                }

                //echo $isCorrupted;
                $row->isCorrupted = $isCorrupted;
                $row->VendorList = array("VendorList" => $VendorList);

                // retrieve a 'Likes' array of uid's
                $this->db->select('*');
                $this->db->from('Likes');
                $this->db->where('rid', $rid);
                $this->db->join('Users', 'Users.uid = Likes.uid');
                
                $LikesList = $this->db->get()->result();

                $row->LikesList = array("LikesList" => $LikesList);

                // retrieve a 'Comments' with uid's
                $this->db->select('*');
                $this->db->from('Comments');
                $this->db->join('Users', 'Users.uid = Comments.uid', 'left');
                $this->db->order_by('date', 'asc');
                $this->db->where('rid', $rid);
                $CommentsList = $this->db->get()->result();

                $row->CommentsList = array("CommentsList" => $CommentsList);

                // add whether the user has Liked the status or not
                $this->db->from('Likes');
                $this->db->where('rid', $rid);
                $this->db->where('uid', $uid);

                if ($this->db->count_all_results() == 0) {
                    $row->alreadyLiked = "0";
                } else {  // user has already liked it
                    $row->alreadyLiked = "1";
                }

                if( $data['itemType'] != "inbox" ) {
                    // add recipient detail information
                    $recipientUid = $row->uid2;
                    $this->db->select('*');
                    $this->db->from('Users'); 
                    $this->db->where('uid', $recipientUid);
                    $RecipientDetails = $this->db->get()->result();
                    
                    if(array_filter($RecipientDetails)) {
                        $row->RecipientDetails = array("RecipientDetails" => $RecipientDetails);
                    } else {
                        $isCorrupted = "7: Recipient data is missing from the User table. RID: " . $rid . ", LID: " . $lid . ", UID2: " . $row->uid2;
                    }
                }
                
                if ($isCorrupted == "")        // if it is not corrupted, then increment the counter
                    $rowsNotCorrupted++;                
            }
        
            // add result to returnedResult
            $returnedResult = array_merge($returnedResult, $result);
            
            if ( $rowsNotCorrupted < $rowsRequested ) {
                // set conditions for next loop, only try to get one more
                $data['rowsRequested'] = 1;
                $data['rowStart'] = count($returnedResult);
            }
        }
        
        // if there is one more row, then
//        if ( count($returnedResult) > $expectedRowsRequested ) {
//            $returnedResult["showMore"] = TRUE;
//        } else {
//            $returnedResult["showMore"] = FALSE;
//        }
                
        return $returnedResult;
    }
    
    private function get_corresponding_item_result($myUID,$data) 
    {
        $itemType = $data['itemType'];
        $rowsRequested = $data['rowsRequested'];
        $rowStart = $data['rowStart'];
        
        // itemType
        // "inbox", "friend-activity", "referral-tracking"
        switch ($itemType) {
            case "inbox":
                $where = "Referrals.uid2 = $myUID AND deletedUID2 = 0"; // if recipient flag is 0, undeleted

                $this->db->select('*, Referrals.comment AS ReferralsComment, Referrals.date AS refDate');

                $this->db->from('Referrals');
                $this->db->join('Users', 'Users.uid = Referrals.uid1');

                // the following code limits query result
                $this->db->order_by('Referrals.date', 'desc');
                $this->db->limit($rowsRequested, $rowStart);  // gets the requested number of rows
                $this->db->where($where);

                $result = $this->db->get()->result();
                break;
            case "friend-activity":
                $this->db->select('*');
                $this->db->from('Friends');
                $this->db->where('uid1', $myUID );
                $this->db->or_where('uid2', $myUID ); // depends on whether or not the Friends table's row is one way relationship
                $myFriends = $this->db->get()->result();

                $i = 0;
                $friendArray = array();
                foreach($myFriends as $row)
                {
                    $friendArray[$i] = $row->uid1;
                    $friendArray[$i+1] = $row->uid2;
                    $i = $i + 2;
                }

                // user must have friends, otherwise, return empty array
                if (array_filter($friendArray, 'trim')) {
                    $where = "((uid1 != " . $myUID  . " AND " . "uid2 != " . $myUID  . ") AND ( uid1 IN (" . implode(",", $friendArray) . ")
                                OR uid2 IN (" . implode(",", $friendArray) . ") ))";
                } else {
                    $where = "uid1 = $myUID";
                }
                $where = $where . " AND (deletedUID1 = 0 OR deletedUID2 = 0)";
                
                $data['where'] = $where;
                $data['onCriterion'] = "Referrals.uid1";
                $result = $this->recent_activity_first_query($data);
                break;
            case "referral-tracking":
                $where = "uid1 = $myUID AND deletedUID1 = 0";   // if sender flag is 0, undeleted
                
                $data['where'] = $where;
                $data['onCriterion'] = "Referrals.uid2";
                
                $result = $this->recent_activity_first_query($data);
                break;
            default:
                $result = "DEFAULT ERROR" ;
        }
        return $result;
    }
    
    private function recent_activity_first_query($data) 
    {
        $where = $data['where'];
        $onCriterion = $data['onCriterion'];
        $rowsRequested = $data['rowsRequested'];
        $rowStart = $data['rowStart'];

        // return rid's with most recent activity (comments / likes / referral actions)
        $q = "(select rid, date
              from Likes
              where rid in (select rid from Referrals where " . $where . "))
              union
              (select rid, date
              from Comments
              where rid in (select rid from Referrals where " . $where . "))
              union
              (select rid, date
              from Referrals
              where rid in (select rid from Referrals where " . $where . "))
              order by date desc;";
        
        $res = $this->db->query($q);

        // create an ordered list of these rid's to order the results of the details query below
        $updatedRIDs = array();
        $updatedRIDsStr = "";
        
        if ( $res->num_rows() > 0 ) {
            $updatedRIDsStr = "(";
            foreach ( $res->result() as $row ) {
                if (!in_array($row->rid,$updatedRIDs)) {
                    array_push($updatedRIDs, $row->rid);
                    $updatedRIDsStr = $updatedRIDsStr . $row->rid . ",";
                }
            }
            $updatedRIDsStr[strlen($updatedRIDsStr)-1] = ")";
        }
     
//        $detailsQ = "SELECT *
//                     FROM Referrals
//                     LEFT JOIN Users
//                     ON Users.uid = " . $onCriterion;
        
        if ( $res->num_rows() > 0 ) {
            $detailsQ = "SELECT *
                     FROM Referrals
                     LEFT JOIN Users
                     ON Users.uid = " . $onCriterion;
            $detailsQ = "$detailsQ WHERE Referrals.rid IN $updatedRIDsStr
                                   ORDER BY CASE Referrals.rid";
            for ($i = 0; $i < count($updatedRIDs); $i++) {
                $detailsQ = "$detailsQ WHEN $updatedRIDs[$i] THEN ($i+1) \n";
            }
            $detailsQ = "$detailsQ END";
            $detailsQ = "$detailsQ LIMIT $rowStart, $rowsRequested";
            return $this->db->query($detailsQ)->result();

            
        }
        else
            return array();
//        $detailsQ = "$detailsQ LIMIT $rowStart, $rowsRequested";
        
//        return $this->db->query($detailsQ)->result();
    }
    
    public function flag_delete_referral_item($rid,$itemType)
    {
        // if inbox, then delete uid2
        // if referral-tracking, then delete uid1
        switch ($itemType) {
            case "inbox":
                $data = array('deletedUID2' => 1);
                break;
            case "referral-tracking":
                $data = array('deletedUID1' => 1);
                break;
            case "friend-activity":
                $data = array('deletedUID1' => 0);
                break;
            default:
                $data = array('deletedUID1' => 0);
                break;
        }
        $this->db->update('Referrals', $data, array('rid' => $rid));
    }
    
    // add a referral to the database when a user refers a vendor to another user
    function add_referral($id,$uid,$numFriends,$uidFriends,$comment)
    {
        $this->db->trans_start();
        
        $result = $this->refer($id,$uid,$numFriends,$uidFriends,$comment);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return "Vendor referral could not be processed";
        }
        else {
            return $result;
        }
    }
    
    function refer($id,$uid,$numFriends,$uidFriends,$comment) {
        $date = date('Y-m-d H:i:s');

        // add referrals to Referral table: one for each friend in uidFriends
        if ($numFriends > 0) {

            $q = "INSERT INTO Referrals VALUES ";
            $newFriends = array();
            $params = array();
            
            for ($i = 0; $i < $numFriends; $i++) {
               $uidFriend = $uidFriends->$i;
               $flag = 0;
               
               // check if this referral has already been made (user1 to user2 for this vendor)
               $existsQuery = "SELECT rid FROM Referrals WHERE uid1 = ? AND uid2 = ? AND lid = 0 AND deletedUID2 != 1";
               $result = $this->db->query($existsQuery,array($uid,$uidFriend));
               
               if ($result->num_rows() > 0) {
                   foreach ($result->result() as $row) {
                        $existsQuery = "SELECT rid FROM Referrals WHERE rid = ? AND vid = ?";
                        $res = $this->db->query($existsQuery,array($row->rid,$id));
                        if ($res->num_rows() > 0) {
                            $flag = 1;
                            break;
                        }
                   }
               }

               // if referral does not exist yet, then add it
               if ($flag == 0) {
                   $q = "$q (NULL, ?, ?, ?, 0, ?, ?, 0, 0),";
                   array_push($newFriends,$uidFriend);
                   array_push($params,$uid,$uidFriend,$date,$id,$comment);
               }
            }

            // delete last comma
            $q = substr($q,0,-1);
            
            if (count($newFriends) > 0) {
                $result = $this->db->query($q,$params);
                if (!$result) {
                    return "Vendor referral could not be processed";
                }
            }
            
            // get RID that was just inserted into Referrals table and build query for adding to referral details: one for each friend
//            $addReferralDetailQuery = "INSERT INTO ReferralDetails VALUES ";
//            $params = array();
//            
//            for ($i = 0; $i < count($newFriends); $i++) {
//                $uidFriend = $newFriends[$i];
//                $getRIDquery = "SELECT rid FROM Referrals WHERE uid1 = ? AND uid2 = ? AND date = ? AND lid = 0 AND comment = ?";
//
//                $result = $this->db->query($getRIDquery,array($uid,$uidFriend,$date,$comment));
//                if ($result->num_rows() > 0) {
//                    $rid = $result->row()->rid;
//                }
//                $addReferralDetailQuery = "$addReferralDetailQuery (?,?,0,0),";
//                array_push($params,$rid,$id);
//            }
//            
//            // delete last comma
//            $addReferralDetailQuery = substr($addReferralDetailQuery,0,-1);
//            
//            if (count($newFriends) > 0) {
//                $result = $this->db->query($addReferralDetailQuery,$params);
//            }
        }
        return false;
    }
    
    // add vendor to db: called when a vendor is added to a list or referred to a friend using google api
//    function add_vendor($name,$reference,$id,$lat,$lng,$phone,$addr,$addrNum,$addrStreet,$addrCity,$addrState,$addrCountry,$addrZip,$vicinity,$website,$icon,$rating) {   
//        // find if vendor exists in db yet        
//        $existingVendorQuery = "SELECT id FROM Vendors WHERE id = ?";
//        $existingVendorResult = $this->db->query($existingVendorQuery,array($id));
//        
//        // add to vendor db if it does not exist yet
//        if ($existingVendorResult->num_rows() == 0) {
//           $addVendorQuery = "INSERT INTO Vendors 
//                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
//           $this->db->query($addVendorQuery,array($name,$reference,$id,$lat,$lng,$phone,$addr,$addrNum,$addrStreet,$addrCity,$addrState,$addrCountry,$addrZip,$vicinity,$website,$icon,$rating));
//        }
//    }
    
    // add vendor to db for foursquare api
    function add_vendor($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $website, $tags, $categories, $photos) {

        // find if vendor exists in db yet        
        $existingVendorQuery = "SELECT id FROM VendorsFoursquare WHERE id = ?";
        $existingVendorResult = $this->db->query($existingVendorQuery,array($id));

        // add to vendor db if it does not exist yet
        if ($existingVendorResult->num_rows() == 0) {
            
            // add vendor info to vendor table
           $addVendorQuery = "INSERT INTO VendorsFoursquare
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
           $this->db->query($addVendorQuery,array($name,$id,$lat,$lng,$phone,$addr,$addrCrossStreet,$addrCity,$addrState,$addrCountry,$addrZip,$website));
        
           // add tags to tag table
           if (count($tags) > 0) {
               $addTagsQuery = "INSERT INTO VendorsFoursquareTags VALUES ";
               foreach ($tags as $tag) {
                   $addTagsQuery = "$addTagsQuery (\"$id\",\"$tag\"),";
               }
               $addTagsQuery = substr($addTagsQuery,0,-1);
               $this->db->query($addTagsQuery);
           }
           
           // add categories to category table
//           if (count($categories) > 0) {
           if ($categories) {
               $addCategoriesQuery = "INSERT INTO VendorsFoursquareCategories VALUES ";
               foreach ($categories as $category) {
                   $cid = $category['cid'];
                   $categoryName = $category['categoryName'];
                   $addCategoriesQuery = "$addCategoriesQuery (\"$id\",\"$cid\",\"$categoryName\"),";
               }
               $addCategoriesQuery = substr($addCategoriesQuery,0,-1);
               $this->db->query($addCategoriesQuery);
           }
           
           // add photos to photo table
//           if (count($photos) > 0) {
           if ($photos) {
               $addPhotosQuery = "INSERT INTO VendorsFoursquarePhotos VALUES ";
               foreach ($photos as $photo) {
                   $pid = $photo['pid'];
                   $photoURL = $photo['photoURL'];
                   $addPhotosQuery = "$addPhotosQuery (\"$id\",\"$pid\",\"$photoURL\"),";
               }
               $addPhotosQuery = substr($addPhotosQuery,0,-1);
               $this->db->query($addPhotosQuery);
           }
        }
    }
    
    function refer_from_search($id,$uid,$numFriends,$uidFriends,$comment,$name,$lat,$lng,$phone,$addr,$addrCrossStreet,$addrCity,$addrState,$addrCountry,$addrZip,$website,$tags,$categories,$photos) {
        $this->db->trans_start();
                
        $this->add_vendor($name,$id,$lat,$lng,$phone,$addr,$addrCrossStreet,$addrCity,$addrState,$addrCountry,$addrZip,$website,$tags,$categories,$photos);
        $result = $this->refer($id,$uid,$numFriends,$uidFriends,$comment);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return "Vendor referral could not be processed";
        }
        else {
            return $result;
        }
    }
}


?>
