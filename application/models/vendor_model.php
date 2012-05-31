<?php
class vendor_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
        $this->load->database();
        $this->load->library('Subquery');
    }
    
    function add_vendor($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $website, $vendorPhotos) {

        // find if vendor exists in db yet        
        $existingVendorQuery = "SELECT id FROM VendorsFoursquare WHERE id = ?";
        $existingVendorResult = $this->db->query($existingVendorQuery,array($id));

        // add to vendor db if it does not exist yet
        if ($existingVendorResult->num_rows() == 0) {
            
            // add vendor info to vendor table
           $addVendorQuery = "INSERT INTO VendorsFoursquare
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
           $this->db->query($addVendorQuery,array($name,$id,$lat,$lng,$phone,$addr,$addrCrossStreet,$addrCity,$addrState,$addrCountry,$addrZip,$website));
        
           // add vendor photos to vendor photos table
           if ($vendorPhotos) {
               $addPhotosQuery = "INSERT INTO VendorsFoursquarePhotos VALUES ";
               foreach ($vendorPhotos as $photo) {
                   $pid = $photo->pid;
                   $photoURL = $photo->photoURL;
                   $addPhotosQuery = "$addPhotosQuery (\"$id\",\"$pid\",\"$photoURL\"),";
               }
               $addPhotosQuery = substr($addPhotosQuery,0,-1);
               $this->db->query($addPhotosQuery);
           }
        }
    }
    
    function get_vendor_referral_comments_for_core_data($uid, $vid)
    {
        $this->db->select("* FROM (SELECT Referrals.rid AS referral_id, Referrals.comment AS referral_comment, Referrals.date AS referral_date, Referrals.vid AS referral_vid, Lists.vid AS list_vid,
            Referrals.uid1 AS referrer_id, Users.firstName AS referrer_firstName, Users.lastName AS referrer_lastName, Users.fbid AS referrer_fbid, Users.email AS referrer_email,
            Lists.comment AS listentry_comment FROM Referrals LEFT JOIN Users ON Referrals.uid1 = Users.uid LEFT JOIN Lists ON Referrals.lid = Lists.lid WHERE Referrals.uid2 = " . $uid . " AND
                (Referrals.vid = '" . $vid . "' OR Lists.vid = '" . $vid . "') AND ((Referrals.lid = 0) OR ((Lists.deletedDate > Referrals.date OR Lists.deleted = 0)
            and Lists.date < Referrals.date)) ORDER BY Referrals.vid DESC, Referrals.date DESC) AS ordered_referrals");
        $this->db->group_by('referrer_id');
        $this->db->order_by('referral_date DESC');
        return $this->db->get()->result();
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
        $this->db->select('uid1,Referrals.comment,firstName,lastName,fbid,email,Referrals.lid,Referrals.date');
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
    
    function get_vendor_photos($vid) {
        $this->db->select();
        $this->db->from('VendorsFoursquarePhotos');
        $this->db->where('vid',$vid);
        return $this->db->get()->result();
    }
}

?>