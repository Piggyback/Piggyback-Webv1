<?php

/**
 *  @mike gao
 *   */

class List_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /** 
     * Functions called by listapi.php
     */
    
    public function core_data_delete_list($lid) {
        $data = array('deleted' => 1);
        $this->db->where('lid',$lid);
        $this->db->update('UserLists',$data);
    }
    
    public function core_data_delete_list_entry($leid,$date) {
        $data = array('deleted' => 1,'deletedDate' => $date);
        $this->db->where('leid',$leid);
        $this->db->update('Lists',$data);
    }
//    
//    public function get_lists_for_core_data($uid)
//    {
//        $this->db->distinct();
//        $this->db->select('UserLists.lid AS list_id, UserLists.name AS list_name, UserLists.date AS list_createdDate, UserLists.uid AS list_ownerId, COUNT(Lists.deleted) AS list_count');
//        $this->db->from('UserLists');
//        $this->db->join('Lists', 'UserLists.lid = Lists.lid', 'left');
//        $custom_where = 'UserLists.uid = ' . $uid . ' AND UserLists.deleted = 0';
//        $this->db->where($custom_where);
//        $this->db->group_by(array('UserLists.lid'));
//        $this->db->order_by('UserLists.date desc');
//        
//        return $this->db->get()->result();
//    }
    
    public function get_lists_for_core_data($uid)
    {
        $this->db->distinct();
        $this->db->select('UserLists.lid AS list_id, UserLists.name AS list_name, UserLists.date AS list_createdDate, UserLists.uid AS list_ownerId, IFNULL(list_count, 0) AS list_count', false);
        $this->db->from('UserLists');
        $this->db->join('(select count(*) as list_count, UserLists.lid as lid from UserLists left join Lists on UserLists.lid = Lists.lid 
            where Lists.deleted = 0 group by lid) AS list_count_join', 'UserLists.lid = list_count_join.lid', 'left');
        $custom_where = 'UserLists.uid = ' . $uid . ' AND UserLists.deleted = 0';
        $this->db->where($custom_where);
        $this->db->group_by(array('UserLists.lid'));
        $this->db->order_by('UserLists.date desc');
        
        return $this->db->get()->result();
    }
    
    public function post_lists_for_core_data($uid, $date, $name)
    {
        $newList = array('uid' => $uid, 'date' => $date, 'name' => $name);
        $this->db->insert('UserLists', $newList);
        
        return $this->db->insert_id();
    }
    
    public function get_list_entrys_for_core_data($uid, $lid)
    {
        $this->db->distinct();
        $this->db->select('Lists.leid AS listentry_id, Lists.lid AS listentry_assignedListID, Lists.comment AS listentry_comment, Lists.date AS listentry_addedDate, 
            VendorsFoursquare.id AS vendor_id, VendorsFoursquare.name AS vendor_name, VendorsFoursquare.lat AS vendor_lat, VendorsFoursquare.lng AS vendor_lng, VendorsFoursquare.phone AS vendor_phone, VendorsFoursquare.addr AS vendor_addr,
            VendorsFoursquare.addrCrossStreet AS vendor_addrCrossStreet, VendorsFoursquare.addrCity AS vendor_addrCity, VendorsFoursquare.addrState AS vendor_addrState, VendorsFoursquare.addrCountry AS vendor_addrCountry,
            VendorsFoursquare.addrZip AS vendor_addrZip, VendorsFoursquare.website AS vendor_website, vendor_numReferrals');
        $this->db->from('Lists');
        $this->db->join('Referrals', 'Lists.lid = Referrals.lid', 'left');
        $this->db->join('VendorsFoursquare', 'Lists.vid = VendorsFoursquare.id', 'left');
        $this->db->join('(select if(Referrals.vid = 0, Lists.vid, Referrals.vid) as vid, count(distinct uid1) AS vendor_numReferrals
            from Referrals
            left join Lists 
            on Referrals.lid = Lists.lid
            where Referrals.uid2 = ' . $uid . '
            AND (Referrals.lid = 0 OR (((Lists.deleted = 1 AND Lists.deletedDate > Referrals.date) OR Lists.deleted = 0)
            AND Lists.date < Referrals.date))
            group by vid) AS vendorCount', 'Lists.vid = vendorCount.vid', 'right');
        $this->db->where(array('Lists.lid' => $lid, 'Referrals.uid2' => $uid));
        $this->db->where('(Lists.date < Referrals.date)');
        
        return $this->db->get()->result();
    }
    
    public function get_my_list_entrys_for_core_data($uid, $lid)
    {
        $this->db->distinct();
        $this->db->select('Lists.leid AS listentry_id, Lists.lid AS listentry_assignedListID, Lists.comment AS listentry_comment, Lists.date AS listentry_addedDate, 
            VendorsFoursquare.id AS vendor_id, VendorsFoursquare.name AS vendor_name, VendorsFoursquare.lat AS vendor_lat, VendorsFoursquare.lng AS vendor_lng, VendorsFoursquare.phone AS vendor_phone, VendorsFoursquare.addr AS vendor_addr,
            VendorsFoursquare.addrCrossStreet AS vendor_addrCrossStreet, VendorsFoursquare.addrCity AS vendor_addrCity, VendorsFoursquare.addrState AS vendor_addrState, VendorsFoursquare.addrCountry AS vendor_addrCountry,
            VendorsFoursquare.addrZip AS vendor_addrZip, VendorsFoursquare.website AS vendor_website, vendor_numReferrals');
        $this->db->from('UserLists');
        $this->db->join('Lists', 'UserLists.lid = Lists.lid', 'left');
        $this->db->join('VendorsFoursquare', 'Lists.vid = VendorsFoursquare.id', 'left');
        $this->db->join('(select if(Referrals.vid = 0, Lists.vid, Referrals.vid) as vid, count(distinct uid1) AS vendor_numReferrals
            from Referrals
            left join Lists 
            on Referrals.lid = Lists.lid
            where Referrals.uid2 = ' . $uid . '
            AND (Referrals.lid = 0 OR ((Lists.deleted = 1 AND Lists.deletedDate > Referrals.date) OR Lists.deleted = 0)
            AND Lists.date < Referrals.date)
            group by vid) AS vendorCount', 'Lists.vid = vendorCount.vid', 'left');
        $this->db->where(array('Lists.lid' => $lid, 'Lists.deleted' => 0, 'UserLists.uid' => $uid, 'UserLists.deleted' => 0));        
        
        return $this->db->get()->result();
    }
    
    public function put_my_list_entrys_for_core_data($lid, $vid, $date, $comment)
    {
        $tempArray = array('lid' => $lid, 'vid' => $vid, 'date' => $date, 'comment' => $comment);
        $this->db->insert('Lists', $tempArray);
        
        return $this->db->insert_id();
//        $newListEntry = array('lid' => $lid, 'vid' => $vid, 'date' => $date, 'comment' => $comment);
//        $this->db->insert('Lists', $newListEntry);
    }
    
    public function get_list_with_entrys($uid)
    {
        $this->db->distinct();
        $this->db->select('UserLists.lid AS list_lid,
            Lists.date AS listentry_date, Lists.comment AS listentry_comment,
            VendorsFoursquare.id AS vendor_vid, VendorsFoursquare.name AS vendor_name, VendorsFoursquare.lat AS vendor_lat, VendorsFoursquare.lng AS vendor_lng, VendorsFoursquare.phone AS vendor_phone, VendorsFoursquare.addr AS vendor_addr,
            VendorsFoursquare.addrCrossStreet AS vendor_addrCrossStreet, VendorsFoursquare.addrCity AS vendor_addrCity, VendorsFoursquare.addrState AS vendor_addrState, VendorsFoursquare.addrCountry AS vendor_addrCountry,
            VendorsFoursquare.addrZip AS vendor_addrZip, VendorsFoursquare.website AS vendor_website');
        $this->db->from('UserLists');
        $this->db->join('Lists', 'UserLists.lid = Lists.lid');
        $this->db->join('VendorsFoursquare', 'Lists.vid = VendorsFoursquare.id');
        $this->db->where(array('UserLists.uid' => $uid, 'UserLists.deleted' => 0, 'Lists.deleted' => 0));
        $this->db->order_by('listentry_date desc');
        
        return $this->db->get()->result();
    }
    
    public function get_list_with_entrys_with_incoming_referrals($uid)
    {
        $this->db->distinct();
        $this->db->select('Referrals.vid AS referral_vid, Referrals.rid AS referral_rid, Referrals.date AS referral_date, Referrals.lid AS referral_lid, Referrals.comment AS referral_comment, 
            Users.uid AS referrer_uid, Users.fbid AS referrer_fbid, Users.email AS referrer_email, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName');
        $this->db->from('UserLists');
        $this->db->join('Lists', 'UserLists.lid = Lists.lid');
//        $this->db->join('ReferralDetails', 'Lists.vid = ReferralDetails.vid');
//        $this->db->join('Referrals', 'ReferralDetails.rid = Referrals.rid');
        $this->db->join('Referrals', 'Lists.vid = Referrals.vid');
        $this->db->join('Users', 'Referrals.uid1 = Users.uid');
        $this->db->where(array('UserLists.uid' => $uid, 'UserLists.deleted' => 0, 'Lists.deleted' => 0, 'Referrals.uid2' => $uid, 'Referrals.deletedUID2' => 0));
        $this->db->order_by('referral_lid asc, referral_date desc');
        
        return $this->db->get()->result();
    }
    
    public function get_list_entry_comments_of_incoming_referrals($uid)
    {
        $this->db->distinct();
        $this->db->select('Lists.lid AS listentry_lid, Lists.vid AS listentry_vid, Lists.comment AS listentry_comment');
        $this->db->from('Referrals');
        $this->db->join('Lists', 'Referrals.lid = Lists.lid');
        $this->db->where(array('Referrals.uid2' => $uid, 'Referrals.deletedUID2' => 0,'Lists.deleted' => 0));
        
        return $this->db->get()->result();
    }

    /**
     * Functions called by home controller
     */
    public function get_my_lists($currentUID)
    {
        $query = $this->db->get_where('UserLists', array('uid' => $currentUID, 'deleted !=' => 1));

        return $query->result();
    }

    /**
     *  Functions called by list_controller controller
     */
    public function get_list_content($lid)
    {
        $query = $this->db->query("SELECT *, Lists.date AS listsDate FROM Lists INNER JOIN VendorsFoursquare ON Lists.vid = VendorsFoursquare.id WHERE Lists.lid = ? AND deleted != 1",array($lid));
        return $query->result();       
    }


    public function add_list($uid, $newListName)
    {
        $dateTime = date('Y-m-d H:i:s');
        $data = array(
            'uid' => $uid,
            'name' => $newListName,
            'date' => $dateTime
        );

        // check if a list with that name exists already
//        $existsQuery = "SELECT lid FROM UserLists WHERE uid = $uid AND name = \"$newListName\" AND deleted != 1";
        $existsQuery = "SELECT lid FROM UserLists WHERE uid = ? AND name = ? AND deleted != 1";
        $result = $this->db->query($existsQuery,array($uid,$newListName));
        
        if (!$result) {
            return "Could not add list";
        }
         
        else if ($result->num_rows() > 0) {
            return "List already exists!";
        }
        
        else {
            $this->db->insert('UserLists', $data);

            // return new lid
            $query = $this->db->get_where('UserLists', array('uid' => $uid, 'name' => $newListName, 'date' => $dateTime));
            return $query->result();
        }
    }

    // add vendor to existing list -- pass lid that you want to add to, vid to add, date, and comment for vendor
    // if vendor is already in list, return error messags
    function add_vendor_to_list($lid, $vid, $comment) {
        $date = date('Y-m-d H:i:s');

        $existsQuery = "SELECT vid FROM Lists WHERE lid= ? AND vid= ? AND deleted != 1";
        $existsResult = $this->db->query($existsQuery, array($lid,$vid));

        if ($existsResult->num_rows() == 0) {
            $query = "INSERT INTO Lists(lid, vid, date, comment, deleted, deletedDate) VALUES (?, ?, ?, ?, 0, 0)";
            $result = $this->db->query($query, array($lid,$vid,$date,$comment));
            if (!$result) {
                return "Could not add to list";
            }
        }
        else {
            return "Already in list";
        }

        // get vendor data so that the list html can be dynamically updated
        $getVendorQuery = "SELECT *, Lists.date AS listsDate FROM Lists 
                    INNER JOIN VendorsFoursquare ON Lists.vid = VendorsFoursquare.id 
                    WHERE Lists.lid = ? AND deleted != 1 AND VendorsFoursquare.id = ?";
        $result = $this->db->query($getVendorQuery,array($lid,$vid));
        return $result->result();
    }
    
    function add_to_existing_list_from_search($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $website, $tags, $categories, $photos, $lid, $vid, $comment) {
        $this->db->trans_start();
        
        $this->add_vendor($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $website, $tags, $categories, $photos);
        $result = $this->add_vendor_to_list($lid, $vid, $comment);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return "Could not add to list. Please try again!";
        }
        else {
            return $result;
        }
    }
    
    function add_list_to_new_list_from_nonsearch($uid, $newListName, $lid, $rid) {
        $this->db->trans_start();
        
        // CREATE NEW LIST

        $dateTime = date('Y-m-d H:i:s');
        $data = array(
            'uid' => $uid,
            'name' => $newListName,
            'date' => $dateTime
        );

        // check if a list with that name exists already
        $existsQuery = "SELECT lid FROM UserLists WHERE uid = ? AND name = ? AND deleted != 1";
        $result = $this->db->query($existsQuery,array($uid,$newListName));

        if ($result->num_rows() > 0) {
            $this->db->trans_complete();
            return "List already exists!";
        }
        
        else {
            $this->db->insert('UserLists', $data);

            // return new lid
            $newListData = $this->db->get_where('UserLists', array('uid' => $uid, 'name' => $newListName, 'date' => $dateTime));
            
            // ADD ITEMS IN 'lid' TO NEW LIST
            $newLid = $newListData->row()->lid;
            
            $query = "SELECT vid, comment FROM Lists WHERE lid = ? AND date < (SELECT date FROM Referrals WHERE rid = ?) AND ((deleted != 1) OR (deleted = 1 AND deletedDate > (SELECT date FROM Referrals WHERE rid = ?)))";
            $result = $this->db->query($query,array($lid,$rid,$rid));
            
            foreach ($result->result() as $row) {    
                $query = "INSERT INTO Lists(lid, vid, date, comment, deleted, deletedDate) VALUES (?, ?, ?, ?, 0, 0)";
                $result = $this->db->query($query,array($newLid,$row->vid,$dateTime,$row->comment));
            }

            $result = $newLid;
        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return "Could not create list. Please try again!";
        }
        else {
            return $result;
        }
    }
    
    function add_list_to_existing_list($outerLid, $innerLid, $rid) {
        $this->db->trans_start();

        $dateTime = date('Y-m-d H:i:s');

        $query = "SELECT vid, comment FROM Lists WHERE lid = ? AND date < (SELECT date FROM Referrals WHERE rid = ?) AND ((deleted != 1) OR (deleted = 1 AND deletedDate > (SELECT date FROM Referrals WHERE rid = ?)))";
        $result = $this->db->query($query,array($innerLid,$rid,$rid));

        foreach ($result->result() as $row) {    
            $existsQuery = "SELECT vid FROM Lists WHERE lid= ? AND vid= ? AND deleted != 1";
            $existsResult = $this->db->query($existsQuery,array($outerLid,$row->vid));

            if ($existsResult->num_rows() == 0) {
                $query = "INSERT INTO Lists(lid, vid, date, comment, deleted, deletedDate) VALUES (?, ?, ?, ?, 0, 0)";
                $this->db->query($query,array($outerLid,$row->vid,$dateTime,$row->comment));
            }    
        }
        
        // get vendor data so that the list html can be dynamically updated
        $getVendorQuery = "SELECT *, Lists.date AS listsDate FROM Lists 
                    INNER JOIN VendorsFoursquare ON Lists.vid = VendorsFoursquare.id 
                    WHERE Lists.lid = ? AND deleted != 1";
        
        $result = $this->db->query($getVendorQuery, array($outerLid))->result();
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return "Could not add to list. Please try again!";
        }
        else {
            return $result;
        }        
    }
    

    function delete_list($lid) {       
        $data = array('deleted' => 1);
        $this->db->update('UserLists', $data, array('lid' => $lid));
    }

    function delete_vendor_from_list($lid, $vid) {
        $dateTime = date('Y-m-d H:i:s');

        // change deleted flag to 1
        $data = array('deleted' => 1, 'deletedDate' => $dateTime);
        $this->db->where('deleted',0);
        $this->db->update('Lists', $data, array('lid' => $lid, 'vid' => $vid));
    }

    // refer list to friends- add rows to referrals table and referraldetails table
    // uidFriends is given as a json string and immediately converted to a php object for use
    function refer_list($lid, $uid, $prevRid, $numFriends, $uidFriends, $comment) {
        $date = date('Y-m-d H:i:s');

        $emptyListQuery = "SELECT vid FROM Lists WHERE lid = ? AND deleted != 1";
        $result = $this->db->query($emptyListQuery,array($lid));
        if ($result->num_rows() == 0) {
            return "Cannot refer an empty list!";
        }
        
        // nonduplicate friends that you are referring this list to
        $newFriends = array();
        $params = array();
        
        $q = "INSERT INTO Referrals VALUES ";
        for ($i = 0; $i < $numFriends; $i++) {
           $uidFriend = $uidFriends->$i;
           $existsQuery = "SELECT rid FROM Referrals WHERE uid1 = ? AND uid2 = ? AND lid = ? AND deletedUID2 != 1";
           $result = $this->db->query($existsQuery,array($uid,$uidFriend,$lid));

           // if you have not yet referred this friend to this list, then add them onto the query
           // TODO: right now, it does not notify you if you have referred some of the friends to the list already
           if ($result->num_rows() == 0) {
               array_push($newFriends,$uidFriend);
               $q = "$q (NULL, ?, ?, ?, ?, 0, ?, 0, 0),";
               array_push($params,$uid,$uidFriend,$date,$lid,$comment);
           }
        }

        $q = substr($q,0,-1);

        // run the query to add one entry to the Referral table for each friend referred to the list
        if(count($newFriends) > 0) {
            $this->db->trans_start();
            $result = $this->db->query($q,$params);

            // referring list from inbox - get all vendors in the referred list
//            if ($prevRid != 0) {
//                $getVendorQuery = "SELECT vid FROM Lists WHERE lid = ? AND date < (SELECT date FROM Referrals WHERE rid = ?) 
//                                AND ((deleted != 1) OR (deleted = 1 AND deletedDate > (SELECT date FROM Referrals WHERE rid = ?)))";
//                $params = array($lid,$prevRid,$prevRid);
//            } 
//            
//            // referring list from sidebar - get all vendors in the referred list
//            else {
//                $getVendorQuery = "SELECT vid FROM Lists WHERE lid = ? AND deleted != 1";
//                $params = array($lid);
//            }
//            
//            $vendorResult = $this->db->query($getVendorQuery,$params);
//
//            // set up string for adding all rows - one for each vendor in the list, for each friend
//            $addRefDetsQuery = "INSERT INTO ReferralDetails VALUES ";
//            $params = array();
//
//            // get RID that was just inserted into Referrals table
//            for ($i = 0; $i < count($newFriends); $i++) {
//                $uidFriend = $newFriends[$i];
//                $getRIDquery = "SELECT rid FROM Referrals WHERE uid1 = ? AND uid2 = ? AND date = ? AND lid = ? AND comment = ?";
//                $result = $this->db->query($getRIDquery,array($uid,$uidFriend,$date,$lid,$comment));
//                
//                if ($result->num_rows() > 0) {
//                    $rid = $result->row()->rid;
//                }
//
//                // add each vendor in the list to the referraldetails table
//                if ($vendorResult->num_rows() > 0) {
//                    foreach ($vendorResult->result() as $row) {
//                        $addRefDetsQuery = "$addRefDetsQuery (?, ?, 0, 0),";
//                        array_push($params,$rid,$row->vid);
//
//                    }
//                }
//            }
//
//            // remove last comma and run query to add rows to ReferralDetails table
//            $addRefDetsQuery = substr($addRefDetsQuery,0,-1);
//
//            if($vendorResult->num_rows() > 0) {
//                $result = $this->db->query($addRefDetsQuery,$params);
//            }
//            
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return "List referral could not be processed";
            }
        }
        
        // return false if no errors
        return false;
    }

    function edit_vendor_comment($newComment, $lid, $vid) {
        $data = array('comment' => $newComment);
        $this->db->update('Lists', $data, array('lid' => $lid, 'vid' => $vid));
    }
    
    function add_to_new_list_from_search($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $website, $tags, $categories, $photos, $uid, $newListName, $vid, $comment) {
        $this->db->trans_start();

        $this->add_vendor($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $website, $tags, $categories, $photos);        
        $result = $this->add_vendor_to_new_list($uid, $newListName, $vid, $comment);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return "Could not create list. Please try again!";
        }
        else {
            return $result;
        }
    }
    
    function add_vendor_to_new_list_from_nonsearch($uid, $newListName, $vid, $comment) {
        $this->db->trans_start();
        
        // CREATE NEW LIST AND ADD VENDOR TO NEW LIST
        $result = $this->add_vendor_to_new_list($uid, $newListName, $vid, $comment);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return "Could not create list. Please try again!";
        }
        else {
            return $result;
        }

    }
    
    
//    function add_vendor($name, $reference, $id, $lat, $lng, $phone, $addr, $addrNum, $addrStreet, $addrCity, $addrState, 
//            $addrCountry, $addrZip, $vicinity, $website, $icon, $rating) {
//
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
           if($photos) {
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
    
    // **************** wrapper functions ********************** // 
    
    function add_vendor_to_new_list($uid, $newListName, $vid, $comment) {
        // CREATE NEW LIST
        $dateTime = date('Y-m-d H:i:s');
        $data = array(
            'uid' => $uid,
            'name' => $newListName,
            'date' => $dateTime
        );

        // check if a list with that name exists already
        $existsQuery = "SELECT lid FROM UserLists WHERE uid = ? AND name = ? AND deleted != 1";
        $result = $this->db->query($existsQuery,array($uid,$newListName));

        if ($result->num_rows() > 0) {
            $this->db->trans_complete();
            return "List already exists!";
        }
        
        else {
            $this->db->insert('UserLists', $data);

            // return new lid
            $newListData = $this->db->get_where('UserLists', array('uid' => $uid, 'name' => $newListName, 'date' => $dateTime));
            
            // ADD VENDOR TO NEW LIST
            $lid = $newListData->row()->lid;

            $query = "INSERT INTO Lists(lid, vid, date, comment, deleted, deletedDate) VALUES (?, ?, ?, ?, 0, 0)";
            $result = $this->db->query($query,array($lid,$vid,$dateTime,$comment));

            // get vendor data so that the list html can be dynamically updated
            $getVendorQuery = "SELECT *, Lists.date AS listsDate FROM Lists 
                        INNER JOIN VendorsFoursquare ON Lists.vid = VendorsFoursquare.id 
                        WHERE Lists.lid = ? AND deleted != 1 AND VendorsFoursquare.id = ?";
            
            $result = array();
            $result['newList'] = $newListData->result();
            $result['vendor'] = $this->db->query($getVendorQuery,array($lid,$vid))->result();
            return $result;
        }
    }    
}
