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
    
    public function add_new_comment($data)
    {
        $uid = $data['uid'];        // only get user data from controller
        $rid = $this->input->post('rid');
        $date = date("Y-m-d H:i:s");
//        $date = $this->input->post('date');           // client-side date
        $comment = $this->input->post('comment');
            
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
    
    public function remove_comment($data)
    {
        // TODO: add error handling for deleting when not your own comment? @andyjiang
        $uid = $data['uid'];
        $cid = $this->input->post('cid');

        // remove the comment
        $this->db->where('cid', $cid);
        $this->db->delete('Comments');
    }
    
    public function is_already_liked($data)
    {
        $uid = $data['uid'];
        $rid = $this->input->post('rid');

        $this->db->from('Likes');
        $this->db->where('rid', $rid);
        $this->db->where('uid', $uid);

        // if numRows = 0,
        //    then USER NOT YET LIKED
        // else
        //    USER ALREADY LIKED
        return $this->db->count_all_results();
    }

    public function add_new_like($data)
    {
        $uid = $data['uid'];
        $rid = $this->input->post('rid');
        $date = date("Y-m-d H:i:s");

        $newLike = array(
            'rid' => $rid,
            'uid' => $uid,
            'date' => $date
        );

        $this->db->insert('Likes', $newLike);
    }

    public function remove_like($data)
    {
        $uid = $data['uid'];
        $rid = $this->input->post('rid');

        $this->db->where('rid', $rid);
        $this->db->where('uid', $uid);
        $this->db->delete('Likes');
    }

    /*
     * use front-end so can delete this
     */
    public function get_like_count()
    {
        $rid = $this->input->post('rid');

        // ajax wants the count of the new likes
        $this->db->from('Likes');
        $this->db->where('rid', $rid);

        // return the count of the result
        return $this->db->count_all_results();
    }
    
    public function get_referral_items($data) 
    {
        $myUID = $data['uid'];
        
        // added my mike gao to display inbox from php
        if (!array_key_exists('rowStart', $data))
            $data['rowStart'] = $this->input->post('rowStart');
        
        // added by mike gao to display inbox from php
        if (!array_key_exists('itemType', $data))
            $data['itemType'] = $this->input->post('itemType');
        
        if (!array_key_exists('rowsRequested', $data)) {
            $data['rowsRequested'] = $this->input->post('rowsRequested');
        }
        
        $rowsRequested = $data['rowsRequested'];
        $rowsNotCorrupted = 0;          // counter for not corrupted rows
        $returnedResult = array();
        $maxSize = 0;                   // max size
        $data['rowsRequested'] = $data['rowsRequested'] + 1;
        
        while ( $rowsNotCorrupted < $rowsRequested ) {
            $result = $this->get_corresponding_item_result($data);
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
                    $query = "SELECT vid, comment FROM Lists WHERE lid = ? AND vid IN (SELECT vid FROM ReferralDetails WHERE rid = ?)
                                AND date < (SELECT date FROM Referrals WHERE rid = ?) AND ((deleted != 1) 
                                OR (deleted = 1 AND deletedDate > (SELECT date FROM Referrals WHERE rid = ?)))";
                    $vidList = $this->db->query($query,array($lid,$rid,$rid,$rid))->result();

                    if(array_filter($vidList)) {
                        foreach($vidList as $vidRow)
                        {
                            $vid = $vidRow->vid;

                            $this->db->select('*');
                            $this->db->from('Vendors');
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
                    $this->db->from('ReferralDetails');
                    $this->db->where('ReferralDetails.rid', $rid);

                    // ReferralDetails is an associative array that holds the vendor information
                    $ReferralDetails = $this->db->get()->result();

                    if($ReferralDetails) {
                        $vid = $ReferralDetails[0]->vid;

                        $this->db->select('*');
                        $this->db->from('Vendors');
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
                        $isCorrupted = "6: Error in finding corresponding row in ReferralDetails table. No lists attached to this referral. 
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
                $this->db->where('uid', $myUID);

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
    
    private function get_corresponding_item_result($data) 
    {
        $myUID = $data['uid'];
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
    
    public function flag_delete_referral_item()
    {
        $rid = $this->input->post('rid');
        $itemType = $this->input->post('itemType'); 
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
}


?>
