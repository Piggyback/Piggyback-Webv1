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
        $addReferralQuery = "INSERT INTO Referrals(uid1, uid2, date, lid)
                             VALUES ($uid1, $uid2, $date, $lid)";
        mysql_query($addReferralQuery) or die("My sql error: " . mysql_error());
        
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
        $addReferralDetailsQuery = "INSERT INTO ReferralDetails(rid, vid, status, lidEnd)
                                    VALUES ($rid, \"$lid\", $status, $lidEnd)";
        mysql_query($addReferralDetailsQuery) or die("My sql error: " . mysql_error());
    }

    /*
     * get_referral_details
     * 
     * inputs: uid2 (receiving user)
     * action: retrieve all related data to the referral (lid, comment, rid)
     * return: result()
     * 
     */
    public function get_inbox_items()
    {
        // should get uidRecipient from session
        $uidRecipient = 1;  // Seung Hyo

        $this->db->select('*, UserLists.name AS UserListsName, Vendors.name AS VendorsName, UserLists.comment AS UserListsComment, Referrals.comment AS ReferralsComment');
        $this->db->from('Referrals');
        $this->db->join('Users', 'Users.uid = Referrals.uid1', 'left');
        $this->db->join('ReferralDetails', 'ReferralDetails.rid = Referrals.rid', 'left');
        $this->db->join('UserLists', 'UserLists.lid = Referrals.lid', 'left');
        $this->db->join('Vendors', 'Vendors.id = ReferralDetails.vid', 'left');
        //$this->db->join('Lists', 'Lists.lid = Referrals.lid', 'inner');
        
        $this->db->where('uid2', $uidRecipient);
                
        $result = $this->db->get()->result();

        var_dump($result);

        return $result;
    }
    
}

?>