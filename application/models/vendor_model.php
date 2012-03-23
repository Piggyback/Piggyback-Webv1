<?php
class vendor_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * @kimhsiao
     */
    function get_vendor_info($vid) 
    {
        $this->db->select('*, id as vid');
        $query = $this->db->get_where('VendorsFoursquare', array('id' => $vid));
        return $query->result();
    }
    
    // get referral comments to you along with vendor data 
//    function vendor_referred_by($uid,$vid) {
//        $this->db->distinct();
//        $this->db->select('uid1,comment,firstName,lastName,fbid,email,lid,date,Vendors.id AS vendor_vid, Vendors.name AS vendor_name, Vendors.reference AS vendor_reference, Vendors.lat AS vendor_lat, Vendors.lng AS vendor_lng, Vendors.phone AS vendor_phone, Vendors.addr AS vendor_addr,
//            Vendors.addrNum as vendor_addrNum, Vendors.addrStreet AS vendor_addrStreet, Vendors.addrCity AS vendor_addrCity, Vendors.addrState AS vendor_addrState, Vendors.addrCountry AS vendor_addrCountry,
//            Vendors.addrZip AS vendor_addrZip, Vendors.vicinity AS vendor_vicinity, Vendors.website AS vendor_website, Vendors.icon AS vendor_icon, Vendors.rating AS vendor_rating');
//        $this->db->from('Referrals');
//        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
//        $this->db->join('Users','Referrals.uid1 = Users.uid','left');
//        $this->db->join('Vendors','ReferralDetails.vid = Vendors.id','left');
//        $this->db->where('vid',$vid);
//        $this->db->where('uid2',$uid);
//        $this->db->where('deletedUID1',0);
//        $this->db->order_by('lid asc, date desc');
//        return $this->db->get()->result();
//    }
//    
    // returns all comments to you for specific vid, ordered first by asc lid then by desc date
    function get_referred_by($uid, $vid) 
    {
        $this->db->distinct();
        $this->db->select('uid1,comment,firstName,lastName,fbid,email,Referrals.lid,date');
        $this->db->from('Referrals');
//        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
        $this->db->join('Lists', 'Referrals.lid = Lists.lid', 'left');
        $this->db->join('Users','Referrals.uid1 = Users.uid','left');
        $this->db->where('Lists.vid',$vid);
        $this->db->where('uid2',$uid);
        $this->db->where('deletedUID1',0);
        $this->db->order_by('lid asc, date desc');
        return $this->db->get()->result();
    }
   
    
//    function get_referred_by($uid,$vid) {
//        $this->db->distinct();
//        $this->db->select('uid1,comment,firstName,lastName,fbid,lid,date');
//        $this->db->from('Referrals');
//        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
//        $this->db->join('Users','Referrals.uid1 = Users.uid','left');
//        $this->db->where('vid',$vid);
//        $this->db->where('uid2',$uid);
//        $this->db->where('deletedUID1',0);
//        $this->db->order_by('lid asc, date desc');
//        return $this->db->get()->result();
//        
//        //SELECT DISTINCT uid1, COMMENT , firstName, lastName, lid, DATE
//        //FROM Referrals
//        //LEFT JOIN ReferralDetails ON Referrals.rid = ReferralDetails.rid
//        //LEFT JOIN Users ON Referrals.uid1 = Users.uid
//        //WHERE vid =  '20e88edee4c1c8bb4c59e58015b66146e21ff45b'
//        //AND uid2 =2
//        //AND deletedUID1 =0
//        //ORDER BY lid ASC , DATE DESC 
//        //LIMIT 0 , 30
//    }
}

?>