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
     * action: retrieve recent 10 items related data to the referral (lid, comment, rid)
     * return: result()
     * 
     * 
     */
    public function get_inbox_items($data)
    {
        // should get uidRecipient from session
        $uidRecipient = $data['uid'];

        $this->db->select('*, UserLists.name AS UserListsName, Vendors.name AS VendorsName,
            UserLists.comment AS UserListsComment, Referrals.comment AS ReferralsComment, Referrals.date AS refDate');
        $this->db->from('Referrals');
        $this->db->join('Users', 'Users.uid = Referrals.uid1', 'left');
        $this->db->join('ReferralDetails', 'ReferralDetails.rid = Referrals.rid', 'left');
        $this->db->join('UserLists', 'UserLists.lid = Referrals.lid', 'left');
        $this->db->join('Vendors', 'Vendors.id = ReferralDetails.vid', 'left');
        //$this->db->join('Likes', 'Likes.rid = Referrals.rid', 'left');
        //$this->db->join('Comments', 'Comments.rid = Referrals.rid', 'left');
        //$this->db->join('Lists', 'Lists.lid = Referrals.lid', 'inner');
        
        // the following code limits query result to only 10 rows
        $this->db->order_by('refDate', 'desc');
        
        // FOR NOW TESTING, only load THREE
        $this->db->limit(3);
        
        $this->db->where('uid2', $uidRecipient);
        
        $result = $this->db->get()->result();

        //var_dump($result);

        // result needs to be formatted to include an array of likes and comments
        foreach($result as $row)
        {
            $rid = $row->rid;
            
            // retrieve a 'Likes' array of uid's
            $this->db->select('*');
            $this->db->from('Likes');
            $this->db->where('rid', $rid);
            $LikesList = $this->db->get()->result();
          
            $row->LikesList = array("LikesList" => $LikesList);
            
            // retrieve a 'Comments' with uid's
            $this->db->select('*');
            $this->db->from('Comments');
            $this->db->join('Users', 'Users.uid = Comments.uid', 'left');
            $this->db->order_by('date', 'asc');
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
     * get_more_inbox
     * 
     * AJAX-exclusive
     * 
     * inputs: user, (ajax jquery post) start row point
     * action: retrieves 10 rows after that point
     * return json encoded php array
     * 
     * 
     * TODO: work on overriding both methods @andyjiang
     */
    public function get_more_inbox($data)
    {
        // should get uidRecipient from session
                
        $uidRecipient = $data['uid'];
        $rowStart = $this->input->post('rowStart');
        
        $this->db->select('*, UserLists.name AS UserListsName, Vendors.name AS VendorsName,
            UserLists.comment AS UserListsComment, Referrals.comment AS ReferralsComment, Referrals.date AS refDate');
        $this->db->from('Referrals');
        $this->db->join('Users', 'Users.uid = Referrals.uid1', 'left');
        $this->db->join('ReferralDetails', 'ReferralDetails.rid = Referrals.rid', 'left');
        $this->db->join('UserLists', 'UserLists.lid = Referrals.lid', 'left');
        $this->db->join('Vendors', 'Vendors.id = ReferralDetails.vid', 'left');
        //$this->db->join('Likes', 'Likes.rid = Referrals.rid', 'left');
        //$this->db->join('Comments', 'Comments.rid = Referrals.rid', 'left');
        //$this->db->join('Lists', 'Lists.lid = Referrals.lid', 'inner');
        
        // the following code limits query result to only 10 rows
        $this->db->order_by('refDate', 'desc');
        // second argument is the offset amount
        $this->db->limit(3, $rowStart);
        
        $this->db->where('uid2', $uidRecipient);
        
        $result = $this->db->get()->result();

        // result needs to be formatted to include an array of likes and comments
        foreach($result as $row)
        {
            $rid = $row->rid;
            
            // retrieve a 'Likes' array of uid's
            $this->db->select('*');
            $this->db->from('Likes');
            $this->db->where('rid', $rid);
            $LikesList = $this->db->get()->result();
          
            $row->LikesList = array("LikesList" => $LikesList);
            
            // retrieve a 'Comments' with uid's
            $this->db->select('*');
            $this->db->from('Comments');
            $this->db->join('Users', 'Users.uid = Comments.uid', 'left');
            $this->db->order_by('date', 'asc');
//            $this->db->limit(1);
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
     * AJAX-exclusive (
     * 
     * inputs: comment, uid, rid 
     * action: insert new row in Comments table
     * return: void
     * 
     */
    public function add_new_comment($data)
    {
        // test so that blank comments do not get added
        if($data == "")
        {
            echo "empty";
            // empty comment
        } else {
            $uid = $data['uid'];        // only get user data from controller
            $rid = $this->input->post('rid');
            $date = date("Y-m-d H:i:s");
            $comment = $this->input->post('comment');

            // insert new row into Comments table
            $newComment = array(
                'rid' => $rid,
                'uid' => $uid,
                'date' => $date,
                'comment' => $comment
            );
        
            $this->db->insert('Comments', $newComment);
            
            // get the uniquely auto-incremented RID from Referrals
            $cid = mysql_insert_id();
            
            echo $cid;
        }
    }

    
    /*
     * remove_comment
     * 
     * AJAX-exclusive
     * 
     * inputs: cid
     * action: remove indicated row
     * return: void
     * 
     */
    public function remove_comment($data)
    {
        $uid = $data['uid'];
        $cid = $this->input->post('cid');
        
        // take cid and return new array of comments
        $this->db->select('rid');
        $this->db->from('Comments');
        $this->db->where('cid', $cid);       
        $rid = $this->db->get()->row();

        // error checking, make sure that the row exists
        
        // remove the comment
        $this->db->where('cid', $cid);
        $this->db->delete('Comments');
        
        // retrieve new comments with rid
        $this->db->select('*');
        $this->db->from('Comments');
        $this->db->join('Users', 'Users.uid = Comments.uid', 'left');
        $this->db->where('rid',$rid->rid);
        $this->db->order_by('date', 'asc');

        $newResult = $this->db->get()->result();

        // return  new array
        return $newResult;
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
        $date = date("Y-m-d H:i:s");
        
        $newLike = array(
            'rid' => $rid,
            'uid' => $uid,
            'date' => $date
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
