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
    
    public function get_list_with_entrys($uid)
    {
        $this->db->distinct();
        $this->db->select('UserLists.lid AS list_lid,
            Lists.date AS listentry_date, Lists.comment AS listentry_comment,
            Vendors.id AS vendor_vid, Vendors.name AS vendor_name, Vendors.reference AS vendor_reference, Vendors.lat AS vendor_lat, Vendors.lng AS vendor_lng, Vendors.phone AS vendor_phone, Vendors.addr AS vendor_addr,
            Vendors.addrNum as vendor_addrNum, Vendors.addrStreet AS vendor_addrStreet, Vendors.addrCity AS vendor_addrCity, Vendors.addrState AS vendor_addrState, Vendors.addrCountry AS vendor_addrCountry,
            Vendors.addrZip AS vendor_addrZip, Vendors.vicinity AS vendor_vicinity, Vendors.website AS vendor_website, Vendors.icon AS vendor_icon, Vendors.rating AS vendor_rating');
        $this->db->from('UserLists');
        $this->db->join('Lists', 'UserLists.lid = Lists.lid');
        $this->db->join('Vendors', 'Lists.vid = Vendors.id');
        $this->db->where(array('UserLists.uid' => $uid, 'UserLists.deleted' => 0, 'Lists.deleted' => 0));
        $this->db->order_by('listentry_date desc');
        
        return $this->db->get()->result();
    }
    
    public function get_list_with_entrys_with_incoming_referrals($uid)
    {
        $this->db->distinct();
        $this->db->select('ReferralDetails.vid AS referraldetails_vid, Referrals.rid AS referral_rid, Referrals.date AS referral_date, Referrals.lid AS referral_lid, Referrals.comment AS referral_comment, 
            Users.uid AS referrer_uid, Users.fbid AS referrer_fbid, Users.email AS referrer_email, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName');
        $this->db->from('UserLists');
        $this->db->join('Lists', 'UserLists.lid = Lists.lid');
        $this->db->join('ReferralDetails', 'Lists.vid = ReferralDetails.vid');
        $this->db->join('Referrals', 'ReferralDetails.rid = Referrals.rid');
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
        $query = $this->db->query("SELECT *, Lists.date AS listsDate FROM Lists INNER JOIN Vendors ON Lists.vid = Vendors.id WHERE Lists.lid = ? AND deleted != 1",array($lid));
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
            $query = "INSERT INTO Lists VALUES (?, ?, ?, ?, 0, 0)";
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
                    INNER JOIN Vendors ON Lists.vid = Vendors.id 
                    WHERE Lists.lid = ? AND deleted != 1 AND Vendors.id = ?";
        $result = $this->db->query($getVendorQuery,array($lid,$vid));
        return $result->result();
    }
    
    function add_to_existing_list_from_search($name, $reference, $id, $lat, $lng, $phone, $addr, $addrNum, $addrStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $vicinity, $website, $icon, $rating, $lid, $vid, $comment) {
        $this->db->trans_start();
        
        $this->add_vendor($name, $reference, $id, $lat, $lng, $phone, $addr, $addrNum, $addrStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $vicinity, $website, $icon, $rating);
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
                $query = "INSERT INTO Lists VALUES (?, ?, ?, ?, 0, 0)";
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
                $query = "INSERT INTO Lists VALUES (?, ?, ?, ?, 0, 0)";
                $this->db->query($query,array($outerLid,$row->vid,$dateTime,$row->comment));
            }    
        }
        
        // get vendor data so that the list html can be dynamically updated
        $getVendorQuery = "SELECT *, Lists.date AS listsDate FROM Lists 
                    INNER JOIN Vendors ON Lists.vid = Vendors.id 
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
               $q = "$q (NULL, ?, ?, ?, ?, ?, 0, 0),";
               array_push($params,$uid,$uidFriend,$date,$lid,$comment);
           }
        }

        $q = substr($q,0,-1);

        // run the query to add one entry to the Referral table for each friend referred to the list
        if(count($newFriends) > 0) {
            $this->db->trans_start();
            $result = $this->db->query($q,$params);

            // referring list from inbox - get all vendors in the referred list
            if ($prevRid != 0) {
                $getVendorQuery = "SELECT vid FROM Lists WHERE lid = ? AND date < (SELECT date FROM Referrals WHERE rid = ?) 
                                AND ((deleted != 1) OR (deleted = 1 AND deletedDate > (SELECT date FROM Referrals WHERE rid = ?)))";
                $params = array($lid,$prevRid,$prevRid);
            } 
            
            // referring list from sidebar - get all vendors in the referred list
            else {
                $getVendorQuery = "SELECT vid FROM Lists WHERE lid = ? AND deleted != 1";
                $params = array($lid);
            }
            
            $vendorResult = $this->db->query($getVendorQuery,$params);

            // set up string for adding all rows - one for each vendor in the list, for each friend
            $addRefDetsQuery = "INSERT INTO ReferralDetails VALUES ";
            $params = array();

            // get RID that was just inserted into Referrals table
            for ($i = 0; $i < count($newFriends); $i++) {
                $uidFriend = $newFriends[$i];
                $getRIDquery = "SELECT rid FROM Referrals WHERE uid1 = ? AND uid2 = ? AND date = ? AND lid = ? AND comment = ?";
                $result = $this->db->query($getRIDquery,array($uid,$uidFriend,$date,$lid,$comment));
                
                if ($result->num_rows() > 0) {
                    $rid = $result->row()->rid;
                }

                // add each vendor in the list to the referraldetails table
                if ($vendorResult->num_rows() > 0) {
                    foreach ($vendorResult->result() as $row) {
                        $addRefDetsQuery = "$addRefDetsQuery (?, ?, 0, 0),";
                        array_push($params,$rid,$row->vid);

                    }
                }
            }

            // remove last comma and run query to add rows to ReferralDetails table
            $addRefDetsQuery = substr($addRefDetsQuery,0,-1);

            if($vendorResult->num_rows() > 0) {
                $result = $this->db->query($addRefDetsQuery,$params);
            }
            
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
    
    function add_to_new_list_from_search($name, $reference, $id, $lat, $lng, $phone, $addr, $addrNum, $addrStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $vicinity, $website, $icon, $rating, $uid, $newListName, $vid, $comment) {
        $this->db->trans_start();
        
        $this->add_vendor($name, $reference, $id, $lat, $lng, $phone, $addr, $addrNum, $addrStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $vicinity, $website, $icon, $rating);        
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
    
    
    function add_vendor($name, $reference, $id, $lat, $lng, $phone, $addr, $addrNum, $addrStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $vicinity, $website, $icon, $rating) {

        // find if vendor exists in db yet        
        $existingVendorQuery = "SELECT id FROM Vendors WHERE id = ?";
        $existingVendorResult = $this->db->query($existingVendorQuery,array($id));

        // add to vendor db if it does not exist yet
        if ($existingVendorResult->num_rows() == 0) {
           $addVendorQuery = "INSERT INTO Vendors 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
           $this->db->query($addVendorQuery,array($name,$reference,$id,$lat,$lng,$phone,$addr,$addrNum,$addrStreet,$addrCity,$addrState,$addrCountry,$addrZip,$vicinity,$website,$icon,$rating));
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

            $query = "INSERT INTO Lists VALUES (?, ?, ?, ?, 0, 0)";
            $result = $this->db->query($query,array($lid,$vid,$dateTime,$comment));

            // get vendor data so that the list html can be dynamically updated
            $getVendorQuery = "SELECT *, Lists.date AS listsDate FROM Lists 
                        INNER JOIN Vendors ON Lists.vid = Vendors.id 
                        WHERE Lists.lid = ? AND deleted != 1 AND Vendors.id = ?";
            
            $result = array();
            $result['newList'] = $newListData->result();
            $result['vendor'] = $this->db->query($getVendorQuery,array($lid,$vid))->result();
            return $result;
        }
    }    
}
