<?php
class inbox_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
        $this->load->database();
    }

    // returns all comments to you for specific vid, ordered first by asc lid then by desc date
    function get_inbox_items($uid) {
        $this->db->select('Referrals.date as referral_date, Referrals.rid as referral_rid, Referrals.lid as referral_lid, Referrals.comment as referral_comment, uid1, fbid, email, firstName, lastName, UserLists.name as listName, min(vid) as vid');
        $this->db->from('Referrals');
        $this->db->join('UserLists','Referrals.lid = UserLists.lid','left');
        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
        $this->db->join('Vendors','ReferralDetails.vid = Vendors.id');
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
        // LEFT JOIN ReferralDetails ON Referrals.rid = ReferralDetails.rid
        // LEFT JOIN Vendors ON ReferralDetails.vid = Vendors.id
        // LEFT JOIN Users ON Referrals.uid1 = Users.uid
        // WHERE uid2 =2
    }
    
    // get all data needed to build list entries beforehand
    function get_list_entries($uid) {
        $this->db->select('Referrals.lid as referral_lid,Referrals.uid1 as referrer_uid,Lists.date as list_date,Lists.comment as list_comment,Vendors.id AS vendor_vid, Vendors.name AS vendor_name, Vendors.reference AS vendor_reference, Vendors.lat AS vendor_lat, Vendors.lng AS vendor_lng, Vendors.phone AS vendor_phone, Vendors.addr AS vendor_addr,
            Vendors.addrNum as vendor_addrNum, Vendors.addrStreet AS vendor_addrStreet, Vendors.addrCity AS vendor_addrCity, Vendors.addrState AS vendor_addrState, Vendors.addrCountry AS vendor_addrCountry,
            Vendors.addrZip AS vendor_addrZip, Vendors.vicinity AS vendor_vicinity, Vendors.website AS vendor_website, Vendors.icon AS vendor_icon, Vendors.rating AS vendor_rating');
        $this->db->from('Referrals');
        $this->db->join('Lists','Referrals.lid = Lists.lid','left');
        $this->db->join('Vendors','Lists.vid = Vendors.id','left');
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
        $this->db->select('ReferralDetails.vid AS referraldetails_vid, Referrals.rid AS referral_rid, Referrals.date AS referral_date, Referrals.lid AS referral_lid, Referrals.comment AS referral_comment, 
            Users.uid AS referrer_uid, Users.fbid AS referrer_fbid, Users.email AS referrer_email, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName');
        $this->db->from('Referrals');
        $this->db->join('Users','Referrals.uid1 = Users.uid','left');
        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
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
        $this->db->select('Vendors.id AS vendor_vid, Vendors.name AS vendor_name, Vendors.reference AS vendor_reference, Vendors.lat AS vendor_lat, Vendors.lng AS vendor_lng, Vendors.phone AS vendor_phone, Vendors.addr AS vendor_addr,
            Vendors.addrNum as vendor_addrNum, Vendors.addrStreet AS vendor_addrStreet, Vendors.addrCity AS vendor_addrCity, Vendors.addrState AS vendor_addrState, Vendors.addrCountry AS vendor_addrCountry,
            Vendors.addrZip AS vendor_addrZip, Vendors.vicinity AS vendor_vicinity, Vendors.website AS vendor_website, Vendors.icon AS vendor_icon, Vendors.rating AS vendor_rating');
        $this->db->from('Referrals');
        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
        $this->db->join('Vendors','ReferralDetails.vid = Vendors.id','left');
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
        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
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