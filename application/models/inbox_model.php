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
        $this->db->select('Referrals.date, Referrals.rid, Referrals.lid, uid1, fbid, firstName, lastName, comment, UserLists.name as ListName, vid');
        $this->db->from('Referrals');
        $this->db->join('UserLists','Referrals.lid = UserLists.lid','left');
        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
        $this->db->join('Vendors','ReferralDetails.vid = Vendors.id');
        $this->db->join('Users','Referrals.uid1 = Users.uid');
        $this->db->where('uid2',$uid);
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