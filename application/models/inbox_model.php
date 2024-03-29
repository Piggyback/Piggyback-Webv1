<?php
class inbox_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
        $this->load->database();
    }
    
    function core_data_delete_inbox_item($rid) {
        $data = array('deletedUID2' => 1);
        $this->db->where('rid',$rid);
        $this->db->update('Referrals',$data);
    }
    
    function get_inbox_items_for_core_data($uid)
    {
        $this->db->distinct();
        $this->db->select('Referrals.rid AS referral_id, Referrals.comment AS referral_comment, Referrals.date AS referral_date, 
            Referrals.uid1 AS referrer_id, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName, Users.fbid AS referrer_fbid, Users.email AS referrer_email, 
            Referrals.vid AS vendor_id, VendorsFoursquare.name AS vendor_name, VendorsFoursquare.lat AS vendor_lat, 
            VendorsFoursquare.lng AS vendor_lng, VendorsFoursquare.phone AS vendor_phone, VendorsFoursquare.addr AS vendor_addr, 
            VendorsFoursquare.addrCrossStreet AS vendor_addrCrossStreet, VendorsFoursquare.addrCity AS vendor_addrCity, 
            VendorsFoursquare.addrState AS vendor_addrState, VendorsFoursquare.addrCountry AS vendor_addrCountry, VendorsFoursquare.addrZip AS vendor_addrZip, VendorsFoursquare.website AS vendor_website, 
            Referrals.lid AS list_id, UserLists.name AS list_name, UserLists.date AS list_createdDate, UserLists.uid AS list_ownerId, COUNT(Lists.lid) AS list_count, vendor_numReferrals');
        $this->db->from('Referrals');
//        $this->db->join('(SELECT * FROM Users) AS Users', 'Referrals.uid1 = Users.uid', 'left');
        $this->db->join('Users', 'Referrals.uid1 = Users.uid', 'left');
        $this->db->join('VendorsFoursquare', 'Referrals.vid = VendorsFoursquare.id', 'left');
        $this->db->join('UserLists', 'Referrals.lid = UserLists.lid', 'left');
        $this->db->join('Lists', 'Referrals.lid = Lists.lid', 'left');
        $this->db->join('(select if(Referrals.vid = 0, Lists.vid, Referrals.vid) as vid, count(distinct uid1) AS vendor_numReferrals
            from Referrals
            left join Lists 
            on Referrals.lid = Lists.lid
            where uid2 = ' . $uid . '
            and ((Referrals.lid = 0) OR ((Lists.deletedDate > Referrals.date OR Lists.deleted = 0)
            and Lists.date < Referrals.date))
            group by vid) AS vendorCount', 'Referrals.vid = vendorCount.vid', 'left');
        $this->db->where(array('Referrals.uid2' => $uid, 'Referrals.deletedUID2' => 0));
        $this->db->where('(Referrals.lid = 0 OR (Lists.date < Referrals.date AND (Lists.deleted = 0 OR (Lists.deleted = 1 AND Lists.deletedDate > Referrals.date))))');
        $this->db->group_by(array('Referrals.rid', 'Referrals.comment', 'Referrals.date', 
            'Referrals.uid1', 'Users.firstName', 'Users.lastName', 
            'Referrals.vid', 'VendorsFoursquare.name',
            'Referrals.lid', 'UserLists.name'));
        $this->db->order_by('Referrals.date desc');
        
        return $this->db->get()->result();
    }
    
    /**
     * returns minimum data for inbox items (both single vendors and lists)
     * 
     * mikegao
     */
    function get_minimum_inbox_items($uid)
    {
        $this->db->distinct();
        $this->db->select('Referrals.rid AS referral_rid, Referrals.comment AS referral_comment, Referrals.date AS referral_date, 
            Users.uid AS referrer_uid, Users.fbid AS referrer_fbid, Users.email AS referrer_email, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName, 
            VendorsFoursquare.name AS vendor_name, VendorsFoursquare.id AS vendor_vid, VendorsFoursquare.lat AS vendor_lat, 
            VendorsFoursquare.lng AS vendor_lng, VendorsFoursquare.phone AS vendor_phone, VendorsFoursquare.addr AS vendor_addr, 
            VendorsFoursquare.addrCrossStreet AS vendor_addrCrossStreet, VendorsFoursquare.addrCity AS vendor_addrCity, 
            VendorsFoursquare.addrState AS vendor_addrState, VendorsFoursquare.addrCountry AS vendor_addrCountry, VendorsFoursquare.addrZip AS vendor_addrZip, VendorsFoursquare.website AS vendor_website, 
            Referrals.lid AS list_lid, UserLists.name AS list_name, COUNT(Lists.lid) AS list_count');
        $this->db->from('Referrals');
        $this->db->join('Users', 'Referrals.uid1 = Users.uid', 'left');
        $this->db->join('VendorsFoursquare', 'Referrals.vid = VendorsFoursquare.id', 'left');
        $this->db->join('UserLists', 'Referrals.lid = UserLists.lid', 'left');
        $this->db->join('Lists', 'Referrals.lid = Lists.lid', 'left');
        $this->db->where(array('Referrals.uid2' => $uid, 'Referrals.deletedUID2' => 0));
        $this->db->where('Referrals.lid = 0 OR (Lists.date < Referrals.date AND (Lists.deleted = 0 OR (Lists.deleted = 1 AND Lists.deletedDate > Referrals.date)))');
        $this->db->group_by(array('Referrals.rid', 'Referrals.comment', 'Referrals.date', 
            'Users.uid', 'Users.fbid', 'Users.email', 'Users.firstName', 'Users.lastName', 
            'VendorsFoursquare.name', 'VendorsFoursquare.id', 'VendorsFoursquare.lat', 
            'VendorsFoursquare.lng', 'VendorsFoursquare.phone', 'VendorsFoursquare.addr', 
            'VendorsFoursquare.addrCrossStreet', 'VendorsFoursquare.addrCity', 
            'VendorsFoursquare.addrState', 'VendorsFoursquare.addrCountry', 'VendorsFoursquare.addrZip', 'VendorsFoursquare.website', 
            'Referrals.lid', 'UserLists.name'));
        $this->db->order_by('Referrals.date desc');
        
        return $this->db->get()->result();
        
//select distinct Referrals.rid AS referral_rid, Referrals.comment AS referral_comment, Referrals.date AS referral_date, 
//Users.uid AS referrer_uid, Users.fbid AS referrer_fbid, Users.email AS referrer_email, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName, 
//VendorsFoursquare.name AS vendor_name, VendorsFoursquare.id AS vendor_vid, VendorsFoursquare.lat AS vendor_lat, 
//VendorsFoursquare.lng AS vendor_lng, VendorsFoursquare.phone AS vendor_phone, VendorsFoursquare.addr AS vendor_addr, 
//VendorsFoursquare.addrCrossStreet AS vendor_addrCrossStreet, VendorsFoursquare.addrCity AS vendor_addrCity, 
//VendorsFoursquare.addrState AS vendor_addrState, VendorsFoursquare.addrCountry AS vendor_addrCountry, VendorsFoursquare.addrZip AS vendor_addrZip, VendorsFoursquare.website AS vendor_website, 
//Referrals.lid AS list_lid, UserLists.name AS list_name, COUNT(Lists.lid) AS list_count
//from Referrals
//left join Users on Referrals.uid1 = Users.uid
//left join VendorsFoursquare on Referrals.vid = VendorsFoursquare.id
//left join UserLists on Referrals.lid = UserLists.lid
//left join Lists on Referrals.lid = Lists.lid
//where Referrals.uid2 = 1 and Referrals.deletedUID2 = 0
//and (Referrals.lid = 0 OR (Lists.date < Referrals.date AND (Lists.deleted = 0 OR (Lists.deleted = 1 AND Lists.deletedDate > Referrals.date))))
//group by Referrals.rid, Referrals.comment, Referrals.date, 
//Users.uid, Users.fbid, Users.email, Users.firstName, Users.lastName, 
//VendorsFoursquare.name, VendorsFoursquare.id, VendorsFoursquare.lat, 
//VendorsFoursquare.lng, VendorsFoursquare.phone, VendorsFoursquare.addr, 
//VendorsFoursquare.addrCrossStreet, VendorsFoursquare.addrCity, 
//VendorsFoursquare.addrState, VendorsFoursquare.addrCountry, VendorsFoursquare.addrZip, VendorsFoursquare.website, Referrals.lid, UserLists.name
//order by Referrals.date desc
        
    }
    
    /**
     * returns all single vendor inbox items
     * 
     * mikegao
     */
    function get_single_vendor_inbox_items($uid)
    {
        $this->db->distinct();
        $this->db->select('Referrals.date AS referral_date, Referrals.rid AS referral_rid, Referrals.comment AS referral_comment, 
            VendorsFoursquare.name AS vendor_name, VendorsFoursquare.id AS vendor_vid, VendorsFoursquare.lat AS vendor_lat, 
            VendorsFoursquare.lng AS vendor_lng, VendorsFoursquare.phone AS vendor_phone, VendorsFoursquare.addr AS vendor_addr, 
            VendorsFoursquare.addrCrossStreet AS vendor_addrCrossStreet, VendorsFoursquare.addrCity AS vendor_addrCity, 
            VendorsFoursquare.addrState AS vendor_addrState, VendorsFoursquare.addrCountry AS vendor_addrCountry, VendorsFoursquare.addrZip AS vendor_addrZip, VendorsFoursquare.website AS vendor_website, 
            Users.uid AS referrer_uid, Users.fbid AS referrer_fbid, Users.email AS referrer_email, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName');
        $this->db->from('Referrals');
        $this->db->join('VendorsFoursquare', 'Referrals.vid = VendorsFoursquare.id', 'left');
        $this->db->join('Users', 'Referrals.uid1 = Users.uid', 'left');
        $this->db->where(array('Referrals.uid2' => $uid, 'Referrals.lid' => 0, 'Referrals.deletedUID2' => 0));
        $this->db->order_by('Referrals.date desc');
        
        return $this->db->get()->result();
    }
    
    /**
     * returns all list inbox items
     * 
     * mikegao
     */
    function get_list_inbox_items($uid)
    {
        $this->db->distinct();
        $this->db->select('Referrals.date AS referral_date, Referrals.rid AS referral_rid, Referrals.comment AS referral_comment, 
            VendorsFoursquare.name AS vendor_name, VendorsFoursquare.id AS vendor_vid, VendorsFoursquare.lat AS vendor_lat, 
            VendorsFoursquare.lng AS vendor_lng, VendorsFoursquare.phone AS vendor_phone, VendorsFoursquare.addr AS vendor_addr, 
            VendorsFoursquare.addrCrossStreet AS vendor_addrCrossStreet, VendorsFoursquare.addrCity AS vendor_addrCity, 
            VendorsFoursquare.addrState AS vendor_addrState, VendorsFoursquare.addrCountry AS vendor_addrCountry, VendorsFoursquare.addrZip AS vendor_addrZip, VendorsFoursquare.website AS vendor_website, 
            Users.uid AS referrer_uid, Users.fbid AS referrer_fbid, Users.email AS referrer_email, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName, 
            Lists.lid AS list_lid, Lists.date AS listentry_date, Lists.comment AS listentry_comment,
            UserLists.name AS list_name, UserLists.date AS list_date, UserLists.uid AS listowner_uid');
        $this->db->from('Referrals');
        $this->db->join('UserLists', 'Referrals.lid = UserLists.lid', 'left');
        $this->db->join('Lists', 'Referrals.lid = Lists.lid', 'left');
        $this->db->join('VendorsFoursquare', 'Lists.vid = VendorsFoursquare.id', 'left');
        $this->db->join('Users', 'Referrals.uid1 = Users.uid', 'left');
        $this->db->where('Referrals.uid2', $uid);
        $this->db->where('Referrals.vid', 0);
        $this->db->where('Referrals.deletedUID2', 0);
        $this->db->where('Lists.date < Referrals.date');
        $this->db->where('(Lists.deleted = 0 OR (Lists.deleted = 1 AND Lists.deletedDate > Referrals.date))');
        $this->db->order_by('Referrals.date desc');
        
        return $this->db->get()->result();
    }
    

    // returns all comments to you for specific vid, ordered first by asc lid then by desc date
    function get_inbox_items($uid) {
        $this->db->select('Referrals.date as referral_date, Referrals.rid as referral_rid, Referrals.lid as referral_lid, Referrals.comment as referral_comment, uid1, fbid, email, firstName, lastName, UserLists.name as listName, min(vid) as vid');
        $this->db->from('Referrals');
        $this->db->join('UserLists','Referrals.lid = UserLists.lid','left');
//        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
        $this->db->join('VendorsFoursquare','Referrals.vid = VendorsFoursquare.id');
        $this->db->join('Users','Referrals.uid1 = Users.uid');
        $this->db->where('uid2',$uid);
//        $this->db->where('deletedUID1',0);
        $this->db->where('deletedUID2',0);
        $this->db->group_by(array('Referrals.date','Referrals.rid','Referrals.lid','uid1','comment'));
        $this->db->order_by('Referrals.date desc');
        return $this->db->get()->result();
        
        // SELECT Referrals.date, Referrals.rid, Referrals.lid, uid1, fbid, firstName, lastName, 
        // COMMENT , UserLists.name AS ListName, vid, Vendors.name AS VendorName
        // FROM Referrals
        // LEFT JOIN UserLists ON Referrals.lid = UserLists.lid
        // LEFT JOIN Vendors ON Referrals.vid = Vendors.id
        // LEFT JOIN Users ON Referrals.uid1 = Users.uid
        // WHERE uid2 =2
    }
    
    // get all data needed to build list entries beforehand
    function get_list_entries($uid) {
        $this->db->select('Referrals.lid as referral_lid,Referrals.uid1 as referrer_uid,Lists.date as list_date,Lists.comment as list_comment,VendorsFoursquare.id AS vendor_vid, VendorsFoursquare.name AS vendor_name, VendorsFoursquare.lat AS vendor_lat, VendorsFoursquare.lng AS vendor_lng, VendorsFoursquare.phone AS vendor_phone, VendorsFoursquare.addr AS vendor_addr,
            VendorsFoursquare.addrCrossStreet AS vendor_addrCrossStreet, VendorsFoursquare.addrCity AS vendor_addrCity, VendorsFoursquare.addrState AS vendor_addrState, VendorsFoursquare.addrCountry AS vendor_addrCountry,
            VendorsFoursquare.addrZip AS vendor_addrZip, VendorsFoursquare.website AS vendor_website');
        $this->db->from('Referrals');
        $this->db->join('Lists','Referrals.lid = Lists.lid','left');
        $this->db->join('VendorsFoursquare','Lists.vid = VendorsFoursquare.id','left');
        $this->db->where('Referrals.uid2',$uid);
//        $this->db->where('Referrals.deletedUID1',0);
        $this->db->where('Referrals.deletedUID2',0);
        $this->db->where('Referrals.lid !=',0);
        $this->db->where('Lists.date < Referrals.date');
        $this->db->where('(Lists.deleted = 0 OR (Lists.deleted = 1 AND Lists.deletedDate > Referrals.date))');
        return $this->db->get()->result();
        
//SELECT Referrals.lid AS referral_lid, Lists.date AS list_date, Lists.comment AS list_comment, Vendors.id AS vendor_vid, Vendors.name AS vendor_name, Vendors.reference AS vendor_reference, Vendors.lat AS vendor_lat, Vendors.lng AS vendor_lng, Vendors.phone AS vendor_phone, Vendors.addr AS vendor_addr, Vendors.addrNum AS vendor_addrNum, Vendors.addrStreet AS vendor_addrStreet, Vendors.addrCity AS vendor_addrCity, Vendors.addrState AS vendor_addrState, Vendors.addrCountry AS vendor_addrCountry, Vendors.addrZip AS vendor_addrZip, Vendors.vicinity AS vendor_vicinity, Vendors.website AS vendor_website, Vendors.icon AS vendor_icon, Vendors.rating AS vendor_rating
//FROM Referrals
//LEFT JOIN Lists ON Referrals.lid = Lists.lid
//LEFT JOIN Vendors ON Lists.vid = Vendors.id
//WHERE Referrals.uid2 =2
//AND Referrals.deletedUID1 =0
//AND Referrals.lid !=0
//AND Lists.date < Referrals.date
//AND (Lists.deleted = 0 OR (Lists.deleted = 1 AND Lists.deletedDate > Referrals.date))
    }
    
    // get all comments and users for recommendations to you
    function get_vendor_comments($uid) {
        $this->db->distinct();
        $this->db->select('Referrals.vid AS referral_vid, Referrals.rid AS referral_rid, Referrals.date AS referral_date, Referrals.lid AS referral_lid, Referrals.comment AS referral_comment, 
            Users.uid AS referrer_uid, Users.fbid AS referrer_fbid, Users.email AS referrer_email, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName');
        $this->db->from('Referrals');
        $this->db->join('Users','Referrals.uid1 = Users.uid','left');
//        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
        $this->db->join('Lists', 'Referrals.lid = Lists.lid', 'left');
        $this->db->where('Referrals.uid2',$uid);
        $this->db->where('Referrals.deletedUID1',0);
        $this->db->order_by('Referrals.lid ASC , Referrals.date DESC');
        return $this->db->get()->result();
        
//SELECT ReferralDetails.vid AS referraldetails_vid, Referrals.rid AS referral_rid, Referrals.date AS referral_date, Referrals.lid AS referral_lid, Referrals.comment AS referral_comment, Users.uid AS referrer_uid, Users.fbid AS referrer_fbid, Users.email AS referrer_email, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName
//FROM Referrals
//LEFT JOIN Users ON Referrals.uid1 = Users.uid
//LEFT JOIN ReferralDetails ON Referrals.rid = ReferralDetails.rid
//WHERE Referrals.uid2 =2
//AND Referrals.deletedUID1 =0
    }
    
    function get_vendors($uid) {
        $this->db->select('VendorsFoursquare.id AS vendor_vid, VendorsFoursquare.name AS vendor_name, VendorsFoursquare.lat AS vendor_lat, VendorsFoursquare.lng AS vendor_lng, VendorsFoursquare.phone AS vendor_phone, VendorsFoursquare.addr AS vendor_addr,
            VendorsFoursquare.addrCrossStreet AS vendor_addrCrossStreet, VendorsFoursquare.addrCity AS vendor_addrCity, VendorsFoursquare.addrState AS vendor_addrState, VendorsFoursquare.addrCountry AS vendor_addrCountry,
            VendorsFoursquare.addrZip AS vendor_addrZip, VendorsFoursquare.website AS vendor_website');
        $this->db->from('Referrals');
//        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
        $this->db->join('Lists', 'Referrals.lid = Lists.lid', 'left');
//        $this->db->join('Vendors','ReferralDetails.vid = Vendors.id','left');
        $this->db->join('VendorsFoursquare', 'Lists.vid = VendorsFoursquare.id', 'left');
        $this->db->where('Referrals.uid2',$uid);
        $this->db->where('Referrals.deletedUID1',0);
        return $this->db->get()->result();
//SELECT Vendors . * 
//FROM Referrals
//LEFT JOIN ReferralDetails ON Referrals.rid = ReferralDetails.rid
//LEFT JOIN Vendors ON ReferralDetails.vid = Vendors.id
//WHERE Referrals.uid2 =2
//AND Referrals.deletedUID1 =0
    }
    
    // get other friends recommended to this vendor by same person
    function other_friends_to_vendor($vid,$uidFriend,$uidMe) {
        $this->db->distinct();
        $this->db->select('uid2 AS uid, firstName, lastName, email, fbid');
        $this->db->from('Referrals');
        $this->db->join('Users','Referrals.uid2 = Users.uid','left');
//        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
        $this->db->join('Lists', 'Referrals.lid = Lists.lid', 'left');
        $this->db->where('uid1',$uidFriend);
        $this->db->where('vid',$vid);
        $this->db->where('deletedUID1',0);
        $this->db->where("uid2 IN (SELECT * FROM ((SELECT uid1 FROM Friends WHERE uid2 = $uidMe) UNION 
                                                  (SELECT uid2 FROM Friends WHERE uid1 = $uidMe)) AS MyFriends)");
     
        return $this->db->get()->result();
        
        // SELECT DISTINCT uid, firstName, lastName, email, fbid
        // FROM  `Referrals` 
        // LEFT JOIN ReferralDetails ON Referrals.rid = ReferralDetails.rid
        // LEFT JOIN Users ON Referrals.uid2 = Users.uid
        // WHERE vid =  '254e522efff78add300cd773470d8a42050d8f45'
        // AND uid1 =1
        // AND deleteduid1 =0
        // AND uid2 IN
            // (SELECT * 
            //  FROM ((
                // SELECT uid1 AS uid
                // FROM Friends
                // WHERE uid2 =2)
                // UNION 
                // (SELECT uid2 AS uid
                // FROM Friends
                // WHERE uid1 =2)) AS MyFriends)
    }
    
    // get other friends recommended to this lisr by same person
    function other_friends_to_list($lid,$uidFriend,$uidMe) {
        $this->db->distinct();
        $this->db->select('uid2 AS uid, firstName, lastName, email, fbid');
        $this->db->from('Referrals');
        $this->db->join('Users','Referrals.uid2 = Users.uid','left');
        $this->db->where('uid1',$uidFriend);
        $this->db->where('lid',$lid);
        $this->db->where('deletedUID1',0);
        $this->db->where("uid2 IN (SELECT * FROM ((SELECT uid1 FROM Friends WHERE uid2 = $uidMe) UNION 
                                                  (SELECT uid2 FROM Friends WHERE uid1 = $uidMe)) AS MyFriends)");
     
        return $this->db->get()->result();
        
        // SELECT DISTINCT uid2 as uid, firstName, lastName, email, fbid
        // FROM Referrals
        // LEFT JOIN Users ON Referrals.uid2 = Users.uid (not included yet until we want to display more info?)
        // WHERE uid1 =1
        // AND lid =24
        // AND uid2
        // IN (
            // SELECT * 
            // FROM (
                // (SELECT uid1 AS uid
                // FROM Friends
                // WHERE uid2 =2)
                // UNION
                // (SELECT uid2 AS uid
                // FROM Friends
                // WHERE uid1 =2)
                // ) AS MyFriends
            // )
        // AND deletedUID1 =0
    }
}

?>