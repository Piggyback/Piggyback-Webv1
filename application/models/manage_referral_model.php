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

    /*
     * get_referral_details
     * 
     * inputs: uid2 (receiving user)
     * action: retrieve all related data to the referral (lid, comment, rid)
     * return: result()
     * 
     */
    public function get_inbox_items($data)
    {
        // should get uidRecipient from session
        $uidRecipient = $data['uid'];

        $this->db->select('*, UserLists.name AS UserListsName, Vendors.name AS VendorsName,
            UserLists.comment AS UserListsComment, Referrals.comment AS ReferralsComment');
        $this->db->from('Referrals');
        $this->db->join('Users', 'Users.uid = Referrals.uid1', 'left');
        $this->db->join('ReferralDetails', 'ReferralDetails.rid = Referrals.rid', 'left');
        $this->db->join('UserLists', 'UserLists.lid = Referrals.lid', 'left');
        $this->db->join('Vendors', 'Vendors.id = ReferralDetails.vid', 'left');
        //$this->db->join('Likes', 'Likes.rid = Referrals.rid', 'left');
        //$this->db->join('Comments', 'Comments.rid = Referrals.rid', 'left');
        //$this->db->join('Lists', 'Lists.lid = Referrals.lid', 'inner');
        
        $this->db->where('uid2', $uidRecipient);
        
        $result = $this->db->get()->result();

        //var_dump($result);

        // result needs to be formatted
        foreach($result as $row)
        {
            $rid = $row->rid;
            
            // retrieve a 'Likes' array of uid's
            $this->db->select('uid');
            $this->db->from('Likes');
            $this->db->where('rid', $rid);
            $LikesList = $this->db->get()->result();
          
            $row->LikesList = array("LikesList" => $LikesList);
            
            // retrieve a 'Comments' with uid's
            $this->db->select('*');
            $this->db->from('Comments');
            $this->db->join('Users', 'Users.uid = Comments.uid', 'left');
            $this->db->where('rid', $rid);
            $CommentsList = $this->db->get()->result();
            
            $row->CommentsList = array("CommentsList" => $CommentsList);
            
            // add whether the user has Liked the status or not
            $this->db->from('Likes');
            $this->db->where('rid', $rid);
            $this->db->where('uid', $uidRecipient);
            
            if ($this->db->count_all_results() == 0)
            {
                // user has not liked it yet
                $row->alreadyLiked = "0";
            } else {
                // user has already liked it
                $row->alreadyLiked = "1";
            }
        }
        
        //var_dump($result);
        
        return $result;
    }
    
    /*
     * add_new_comment
     * 
     * AJAX-exclusive
     * 
     * inputs: comment, uid, rid 
     * action: insert new row in Comments table
     * return: void
     * 
     */
    public function add_new_comment($data)
    {
        $uid = $data['uid'];        // only get user data from controller
        $rid = $this->input->post('rid');
        $comment = $this->input->post('comment');
        
        // insert new row into Comments table
        $newComment = array(
            'rid' => $rid,
            'uid' => $uid,
            'comment' => $comment
        );
        
        $this->db->insert('Comments', $newComment);

        echo "success";
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
        
        $newLike = array(
            'rid' => $rid,
            'uid' => $uid
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
    
}

?>
