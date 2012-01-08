<?php

/*
 * @andyjiang
 *
 * this model will manage all referral interactions with MySQL
 *
 * tables:
 *  Referrals
 *  ReferralDetails
 *
 * functions:
 *  create new referral
 *  create referral details
 */

class Manage_Referral_Model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /*
     * create_new_referral
     *
     * inputs: uid1, uid2, lid, vid
     * action: insert new row in Referrals table
     * return: void
     *
     * if lid = 0; then use vid
     * else
     * disregard vid
     *
     */
    public function create_new_referral($data)
    {
        // Data: 'uid1', 'uid2', 'lid', 'vid'
        // uid should be generated from session
        $uid1 = $data['uid1'];
        $uid2 = $data['uid2'];
        $lid = $data['lid'];
        $vid = $data['vid'];
        $date = time();

        // insert new row in Referrals table
        $newReferral = array(
            'uid1' => $uid1,
            'uid2' => $uid2,
            'date' => $date,
            'lid' => $lid
        );
        $this->db->insert('Referrals', $newReferral);

        // get the uniquely auto-incremented RID from Referrals
        $rid = mysql_insert_id();

        // prepare input paramters for create_referral_details
        $newData['rid'] = $rid;
        $newData['vid'] = $vid;
        $newData['status'] = "test comment here";
        $newData['lidEnd'] = 0;                     // test data for now

        // if lid == 0, then we use vid
        if ($lid == 0)
        {
            // use vid
            $this->create_referral_details($newData);

        } else {
            // create referral details with vid array from lid
            $this->load->model('manage_list_model');
            $vendorNameList = $this->manage_list_model->get_vendor_info_from_list($lid);

            // go through all vid's in the lid and create_referral_details for each one
            foreach($vendorNameList as $row)
            {
                $newData['vid'] = intval($row->vid);
                $this->create_referral_details($newData);
            }
        }

    }

    /*
     * create_referral_details
     *
     * inputs: rid (taken from create_new_referral), vid, status, lidEnd (target lid)
     * action: insert new role in ReferralDetails table with given parameters
     * return: void
     *
     */
    public function create_referral_details($data)
    {
        // fieldData: 'rid', 'vid', 'status', 'lidEnd'
        $rid = $data('rid');
        $vid = $data('vid');
        $status = $data('status');
        $lidEnd = $data('lidEnd');

        // insert new row into ReferralDetails table
        $newReferralDetail = array(
            'rid' => $rid,
            'vid' => $vid,
            'status' => $status,
            'lidEnd' => $lidEnd
        );
        $this->db->insert('ReferralDetails', $newReferralDetail);
    }

    
    private function get_where_string($data) {
        $myUID = $data['uid'];
        $itemType = $data['itemType'];
        
        // itemType
        // "inbox", "friends_activity", "referral_tracking"
        switch ($itemType) {
            case "inbox":
                $where = "Referrals.uid2 = " . $myUID;
                break;
            case "friends_activity":
                $this->db->select('*');
                $this->db->from('Friends');
                $this->db->where('uid1', $myUID );
                $this->db->or_where('uid2', $myUID ); // depends on whether or not the Friends table's row is one way relationship
                $uidFriends = $this->db->get()->result();

                $i = 0;
                $friendArray = array();
                foreach($uidFriends as $row)
                {
                    $friendArray[$i] = $row->uid1;
                    $friendArray[$i+1] = $row->uid2;
                    $i = $i + 2;
                }

                // user must have friends, otherwise, return empty array
                if (array_filter($friendArray, 'trim')) {
                    $where = "(uid1 != " . $myUID  . " AND " . "uid2 != " . $myUID  . ") AND ( uid1 IN (" . implode(",", $friendArray) . ") OR uid2 IN (" . implode(",", $friendArray) . ") )";
                } else {
                    $where = "(uid1 != " . $myUID  . " AND " . "uid2 != " . $myUID  . ")";
                }
                break;
            case "referral_tracking":
                $where = "uid1 = " . $myUID;
                break;
            default:
//                $where = "DEFAULT" ;
                $where = "Referrals.uid2 = " . $myUID;
        }
        
        return $where;
    }
    
    private function get_corresponding_item_result($data) {
        $myUID = $data['uid'];
        $itemType = $data['itemType'];
        $rowsRequested = $data['rowsRequested'];
        $rowStart = $data['rowStart'];
        
        // itemType
        // "inbox-tab", "friends-activity-tab", "referral-tracking-tab"
        switch ($itemType) {
            case "inbox-tab":
                $where = "Referrals.uid2 = " . $myUID;
                $this->db->select('*, Referrals.comment AS ReferralsComment, Referrals.date AS refDate');

                $this->db->from('Referrals');
                $this->db->join('Users', 'Users.uid = Referrals.uid1');

                // the following code limits query result
                $this->db->order_by('Referrals.date', 'desc');
                $this->db->limit($rowsRequested, $rowStart);
                //$this->db->where('Referrals.uid2', $uidRecipient);
                $this->db->where($where);

                $result = $this->db->get()->result();
                break;
            case "friend-activity-tab":
                $this->db->select('*');
                $this->db->from('Friends');
                $this->db->where('uid1', $myUID );
                $this->db->or_where('uid2', $myUID ); // depends on whether or not the Friends table's row is one way relationship
                $uidFriends = $this->db->get()->result();

                $i = 0;
                $friendArray = array();
                foreach($uidFriends as $row)
                {
                    $friendArray[$i] = $row->uid1;
                    $friendArray[$i+1] = $row->uid2;
                    $i = $i + 2;
                }

                // user must have friends, otherwise, return empty array
                if (array_filter($friendArray, 'trim')) {
                    $where = "(uid1 != " . $myUID  . " AND " . "uid2 != " . $myUID  . ") AND ( uid1 IN (" . implode(",", $friendArray) . ") OR uid2 IN (" . implode(",", $friendArray) . ") )";
                } else {
                    $where = "(uid1 != " . $myUID  . " AND " . "uid2 != " . $myUID  . ")";
                }
                
                $data['where'] = $where;
                $data['onCriterion'] = "Referrals.uid1";
                $result = $this->recent_activity_first_query($data);
                //var_dump($result);
                break;
            case "referral-tracking-tab":
                $where = "uid1 = " . $myUID;
                $data['where'] = $where;
                $data['onCriterion'] = "Referrals.uid2";
                $result = $this->recent_activity_first_query($data);
                break;
            default:
                $where = "DEFAULT" ;
        }
        return $result;
    }
    
    private function recent_activity_first_query($data) {
        $where = $data['where'];
        $onCriterion = $data['onCriterion'];
        
        // return rid's with most recent activity (comments / likes)
        $q = "(select rid, date
              from Likes
              where rid in (select rid from Referrals where " . $where . "))
              union
              (select rid, date
              from Comments
              where rid in (select rid from Referrals where " . $where . "))
              order by date desc;";

        $res = mysql_query($q);

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
                     ON Users.uid = " . $onCriterion;
        
        if (mysql_num_rows($res) > 0) {
            $detailsQ = "$detailsQ WHERE Referrals.rid IN $updatedRIDsStr
                                   ORDER BY CASE Referrals.rid";
        
            for ($i = 0; $i < count($updatedRIDs); $i++) {
                $detailsQ = $detailsQ . " WHEN " . $updatedRIDs[$i] . " THEN " . ($i+1) . "\n";
            }
            $detailsQ = $detailsQ . "END";
        }
        
        return $this->db->query($detailsQ)->result();
    }

    public function get_friends_items($data)
    {
        // should get uidRecipient from session
        $myUID = $data['uid'];
        $rowsRequested = $data['rowsRequested'];
        $rowStart = $data['rowStart'];

        // get uidRecipient's friends
//        $this->db->select('*');
//        $this->db->from('Friends');
//        $this->db->where('uid1', $myUID );
//        $this->db->or_where('uid2', $myUID ); // depends on whether or not the Friends table's row is one way relationship
//        $uidFriends = $this->db->get()->result();
//
//        $i = 0;
//        $friendArray = array();
//        foreach($uidFriends as $row)
//        {
//            $friendArray[$i] = $row->uid1;
//            $friendArray[$i+1] = $row->uid2;
//            $i = $i + 2;
//        }
//        
//        // user must have friends, otherwise, return empty array
//        if (array_filter($friendArray, 'trim')) {
//            $where = "(uid1 != " . $myUID  . " AND " . "uid2 != " . $myUID  . ") AND ( uid1 IN (" . implode(",", $friendArray) . ") OR uid2 IN (" . implode(",", $friendArray) . ") )";
//        } else {
//            $where = "(uid1 != " . $myUID  . " AND " . "uid2 != " . $myUID  . ")";
//        }
//        
        // returns the where string
        
        $data['itemType'] = "friends_activity";
        
//        $where = $this->get_where_string($data);
        
        // return rid's with most recent activity (comments / likes)
//        $q = "(select rid, date
//              from Likes
//              where rid in (select rid from Referrals where " . $where . "))
//              union
//              (select rid, date
//              from Comments
//              where rid in (select rid from Referrals where " . $where . "))
//              order by date desc;";
//
//        $res = mysql_query($q);
//
//        // create an ordered list of these rid's to order the results of the details query below
//        $updatedRIDs = array();
//        
//        if (mysql_num_rows($res) > 0) {
//            $updatedRIDsStr = "(";
//            while ($row = mysql_fetch_row($res)) {
//                if (!in_array($row[0],$updatedRIDs)) {
//                    array_push($updatedRIDs, $row[0]);
//                    $updatedRIDsStr = $updatedRIDsStr . $row[0] . ",";
//                }
//            }
//
//            $updatedRIDsStr[strlen($updatedRIDsStr)-1] = ")";
//        }
//     
//        $detailsQ = "SELECT *, Referrals.comment AS ReferralsComment, Referrals.date AS refDate
//                     FROM Referrals
//                     LEFT JOIN Users
//                     ON Users.uid = Referrals.uid1";
//        
//        if (mysql_num_rows($res) > 0) {
//            $detailsQ = "$detailsQ WHERE Referrals.rid IN $updatedRIDsStr
//                                   ORDER BY CASE Referrals.rid";
//        
//            for ($i = 0; $i < count($updatedRIDs); $i++) {
//                $detailsQ = $detailsQ . " WHEN " . $updatedRIDs[$i] . " THEN " . ($i+1) . "\n";
//            }
//            $detailsQ = $detailsQ . "END";
//        }
//        
//        $result = $this->db->query($detailsQ)->result();
        
        $result = $this->get_corresponding_item_result($data);
        
        // result needs to be formatted to include an array of likes and comments
        foreach($result as $key => $row)
        {
            // get the array of vendor detail(s)
            $rid = $row->rid;
            $lid = $row->lid;
            $VendorList = array();

            if ( $lid != 0 ) {
                // if the referral is a list
                $this->db->select('*');
                $this->db->from('Lists');
                $this->db->where('lid', $lid);
                $vidList = $this->db->get()->result();

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
                            if($vendorDetails[0] === NULL ) {
                                unset($result[$key]);                                
                            } else {
                                $VendorList[] = $vendorDetails;
                            }
                        } else {
                            unset($result[$key]);
                        }
                    }
                    
                    // retrieve userlist name and details
                    $this->db->select('*');
                    $this->db->from('UserLists');
                    $this->db->where('lid', $lid);

                    $row->UserList = $this->db->get()->result();
                } else {
                    unset ($result[$key]);
                }

            } else {
                // if the referral is single vendor, then
                $this->db->select('*');
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

                    if($vendorDetails) {
                        if($vendorDetails[0] === NULL ) {
                        } else {
                            $VendorList[0] = $vendorDetails;
                        }
                    }
                } else {
                    unset($result[$key]);
                }
            }

            $row->VendorList = array("VendorList" => $VendorList);

            // retrieve a 'Likes' array of uid's
            $this->db->select('*');
            $this->db->from('Likes');
            $this->db->where('rid', $rid);
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

            if ($this->db->count_all_results() == 0)
            {
                $row->alreadyLiked = "0";
            } else {
                // user has already liked it
                $row->alreadyLiked = "1";
            }


            // add recipient detail information
            $recipientUid = $row->uid2;
            $this->db->select('*');
            $this->db->from('Users'); 
            $this->db->where('uid', $recipientUid);
            $RecipientDetails = $this->db->get()->result();

            $row->RecipientDetails = array("RecipientDetails" => $RecipientDetails);

        }
        
        
        return $result;
    }



    /*
     * get_inbox_items
     *
     * inputs: uid2 (receiving user)
     * action: retrieve recent 10 items related data to the referral (lid, comment, rid)
     * return: result()
     *
     *
     */
    public function get_inbox_items($data)
    {
        // should get uidRecipient from session
        $uidRecipient = $data['uid'];
        $rowStart = $data['rowStart'];
        $rowsRequested = $data['rowsRequested'];
        
        $data['itemType'] = "inbox";
        
//        $where = $this->get_where_string($data);
//
//        $this->db->select('*, Referrals.comment AS ReferralsComment, Referrals.date AS refDate');
//
//        $this->db->from('Referrals');
//        $this->db->join('Users', 'Users.uid = Referrals.uid1');
//
//        // the following code limits query result
//        $this->db->order_by('Referrals.date', 'desc');
//        $this->db->limit($rowsRequested, $rowStart);
//        //$this->db->where('Referrals.uid2', $uidRecipient);
//        $this->db->where($where);
//
//        $result = $this->db->get()->result();

        $result = $this->get_corresponding_item_result($data);
        
        // need to check whether it has reached the end.
        // can retrieve three additional unique records?
        // if so, proceed
        // otherwise, stop and return error

        // result needs to be formatted to include an array of likes and comments
        foreach($result as $key => $row)
        {
            // get the array of vendor detail(s)
            $rid = $row->rid;
            $lid = $row->lid;
            $VendorList = array();

            if ( $lid != 0 ) {
                // if the referral is a list
                $this->db->select('*');
                $this->db->from('Lists');
                $this->db->where('lid', $lid);
                $vidList = $this->db->get()->result();

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
                            if($vendorDetails[0] === NULL ) {
                                unset($result[$key]);                                
                            } else {
                                $VendorList[] = $vendorDetails;
                            }
                        } else {
                            unset($result[$key]);
                        }
                    }
                    
                    // retrieve userlist name and details
                    $this->db->select('*');
                    $this->db->from('UserLists');
                    $this->db->where('lid', $lid);

                    $row->UserList = $this->db->get()->result();
                } else {
                    unset ($result[$key]);
                }

            } else {
                // if the referral is single vendor, then
                $this->db->select('*');
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

                    if($vendorDetails) {
                        if($vendorDetails[0] === NULL ) {
                        } else {
                            $VendorList[0] = $vendorDetails;
                        }
                    }
                } else {
                    // if no corresponding record in referralDetails
                    // remove self from array
                    unset($result[$key]);
                }
            }

            $row->VendorList = array("VendorList" => $VendorList);

            // retrieve a 'Likes' array of uid's
            $this->db->select('*');
            $this->db->from('Likes');
            $this->db->where('rid', $rid);
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
            $this->db->where('uid', $uidRecipient);

            if ($this->db->count_all_results() == 0)
            {
                $row->alreadyLiked = "0";
            } else {
                // user has already liked it
                $row->alreadyLiked = "1";
            }
        }

        //var_dump($result);

        return $result;
    }

    public function get_referral_items($data) {
        $myUID = $data['uid'];
        $data['rowStart'] = $this->input->post('rowStart');
        $data['itemType'] = $this->input->post('itemType');
        $data['rowsRequested'] = 3;
        
        $result = $this->get_corresponding_item_result($data);
        
        foreach($result as $key => $row)
        {
            // get the array of vendor detail(s)
            $rid = $row->rid;
            $lid = $row->lid;
            $VendorList = array();

            if ( $lid != 0 ) {
                // if the referral is a list
                $this->db->select('*');
                $this->db->from('Lists');
                $this->db->where('lid', $lid);
                $vidList = $this->db->get()->result();

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
                            if($vendorDetails[0] === NULL ) {
                                unset($result[$key]);                                
                            } else {
                                $VendorList[] = $vendorDetails;
                            }
                        } else {
                            unset($result[$key]);
                        }
                    }
                    
                    // retrieve userlist name and details
                    $this->db->select('*');
                    $this->db->from('UserLists');
                    $this->db->where('lid', $lid);

                    $row->UserList = $this->db->get()->result();
                } else {
                    unset ($result[$key]);
                }

            } else {
                // if the referral is single vendor, then
                $this->db->select('*');
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

                    if($vendorDetails) {
                        if($vendorDetails[0] === NULL ) {
                        } else {
                            $VendorList[0] = $vendorDetails;
                        }
                    }
                } else {
                    // if no corresponding record in referralDetails
                    // remove self from array
                    unset($result[$key]);
                }
            }

            $row->VendorList = array("VendorList" => $VendorList);

            // retrieve a 'Likes' array of uid's
            $this->db->select('*');
            $this->db->from('Likes');
            $this->db->where('rid', $rid);
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

            if ($this->db->count_all_results() == 0)
            {
                $row->alreadyLiked = "0";
            } else {
                // user has already liked it
                $row->alreadyLiked = "1";
            }
            
            if( $data['itemType'] != "inbox" ) {
                // add recipient detail information
                $recipientUid = $row->uid2;
                $this->db->select('*');
                $this->db->from('Users'); 
                $this->db->where('uid', $recipientUid);
                $RecipientDetails = $this->db->get()->result();

                $row->RecipientDetails = array("RecipientDetails" => $RecipientDetails);
            }
            
        }

        return $result;
    }
    
    /*
     * get_more_inbox
     *
     * AJAX-exclusive
     *
     * inputs: user, (ajax jquery post) start row point
     * action: retrieves 10 rows after that point
     * return json encoded php array
     */
    public function get_more_inbox($data)
    {
        $data['rowStart'] = $this->input->post('rowStart');
        $data['rowsRequested'] = 3;
        return $this->get_inbox_items($data);
    }
//
    public function load_inbox_items($data)
    {
        $data['rowStart'] = 0;
        $data['rowsRequested'] = 3;
        return $this->get_inbox_items($data);
    }

    public function get_more_friend_activity($data)
    {
        $newData['uid'] = $data['uid'];
        $newData['rowStart'] = $this->input->post('rowStart');
        $newData['rowsRequested'] = 3;
        return $this->get_friends_items($newData);
    }

    public function load_friend_activity_items($data)
    {
        $newData['uid'] = $data['uid'];
        $newData['rowStart'] = 0;
        $newData['rowsRequested'] = 3;
        
        return $this->get_friends_items($newData);;
    }

    /*
     * add_new_comment
     *
     * AJAX-exclusive (
     *
     * inputs: comment, uid, rid
     * action: insert new row in Comments table
     * return: void
     *
     */
    public function add_new_comment($data)
    {
        // test so that blank comments do not get added
        if($data == "")
        {
            echo "empty";
            // empty comment
        } else {
            $uid = $data['uid'];        // only get user data from controller
            $rid = $this->input->post('rid');
            $date = date("Y-m-d H:i:s");
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

            echo $cid;
        }
    }


    /*
     * remove_comment
     *
     * AJAX-exclusive
     *
     * inputs: cid
     * action: remove indicated row
     * return: void
     *
     */
    public function remove_comment($data)
    {
        $uid = $data['uid'];
        $cid = $this->input->post('cid');

//        // take cid and return new array of comments
//        $this->db->select('rid');
//        $this->db->from('Comments');
//        $this->db->where('cid', $cid);
//        $rid = $this->db->get()->row();

        // error checking, make sure that the row exists

        // remove the comment
        $this->db->where('cid', $cid);
        $this->db->delete('Comments');

//        // retrieve new comments with rid
//        $this->db->select('*');
//        $this->db->from('Comments');
//        $this->db->join('Users', 'Users.uid = Comments.uid', 'left');
//        $this->db->where('rid',$rid->rid);
//        $this->db->order_by('date', 'asc');
//
//        $newResult = $this->db->get()->result();

        // return  new array
        return $newResult;
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

    public function get_like_count()
    {
        $rid = $this->input->post('rid');

        // ajax wants the count of the new likes
        $this->db->from('Likes');
        $this->db->where('rid', $rid);

        // return the count of the result
        return $this->db->count_all_results();
    }

    /*
     * given vendor ID,
     * get vendor details in array
     */
    public function get_vendor_details($data)
    {
        $uid = $data['uid'];
        $vid = $this->input->post('vendorID');

        // ajax wants the array of the vendor details
        $this->db->select('*');
        $this->db->from('Vendors');
        $this->db->where('id', $vid);

        return $this->db->get()->result();
    }

}

?>
