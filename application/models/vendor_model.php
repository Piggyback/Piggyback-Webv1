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
        $query = $this->db->get_where('Vendors', array('id' => $vid));
        return $query->result();
    }
    
    // returns all comments to you for specific vid, ordered first by asc lid then by desc date
    function get_referred_by($uid, $vid) 
    {
        $this->db->distinct();
        $this->db->select('uid1,comment,firstName,lastName,fbid,email,lid,date');
        $this->db->from('Referrals');
        $this->db->join('ReferralDetails','Referrals.rid = ReferralDetails.rid','left');
        $this->db->join('Users','Referrals.uid1 = Users.uid','left');
        $this->db->where('vid',$vid);
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