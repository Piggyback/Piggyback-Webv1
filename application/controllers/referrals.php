<?php

/*
 * @andyjiang
 * 
 */

class referrals extends CI_Controller {
    
    public function index()
    {
        // nothing
    }
    
    /*
     * loads inbox view
     *
     */
    public function inbox_view()
    {
        $currentUserData = $this->session->userdata('currentUserData');
        $this->load->model('manage_referral_model');
        $data['inboxItems'] = $this->manage_referral_model->load_inbox_items($currentUserData);      // eventually would either need to pass uid from session
        $this->load->view('inbox_view', $data);
    }
    
    /*
     * see friends activity
     *
     */
    public function friends_activity_view()
    {
        $currentUserData = $this->session->userdata('currentUserData');
        $this->load->model('manage_referral_model');

        $data['newsFeedItems'] = $this->manage_referral_model->load_friend_activity_items($currentUserData);
        $this->load->view('friend_activity_view', $data);
    }

    /*
     * add a new comment
     * 
     */
    public function add_new_comment() {
        $currentUserData = $this->session->userdata('currentUserData');
        $this->load->model('manage_referral_model');
        $this->manage_referral_model->add_new_comment($currentUserData);
    }
    
    /*
     * to perform 'like' action
     * 
     */
    public function perform_like_action() {
        $currentUserData = $this->session->userdata('currentUserData');
        $this->load->model('manage_referral_model');
        
        // first see if user has already liked it
        if ($this->manage_referral_model->is_already_liked($currentUserData) == 0){
            // user has not yet liked it
            
            // proceed to add new like.
            $this->manage_referral_model->add_new_like($currentUserData);
        } else {
            // user already liked it!
            
            // proceed to remove user's like.
            $this->manage_referral_model->remove_like($currentUserData);
        }
            
        echo $this->manage_referral_model->get_like_count();
        
    }
    
    
    /*
     * only retrieve X rows from model / mysql
     * 
     */
    public function get_more_inbox() {
        $currentUserData = $this->session->userdata('currentUserData');
        $this->load->model('manage_referral_model');
        
        echo json_encode($this->manage_referral_model->get_more_inbox($currentUserData));
        //echo 1;
    }
        
    /*
     * only retrieve X rows from model / mysql
     * 
     */
    public function get_more_friend_activity() {
        $currentUserData = $this->session->userdata('currentUserData');
        $this->load->model('manage_referral_model');
        
        echo json_encode($this->manage_referral_model->get_more_friend_activity($currentUserData));
        //echo 1;
    }
    
    /*
     * delete a comment
     * 
     */
    public function remove_comment() {
        $currentUserData = $this->session->userdata('currentUserData');
        $this->load->model('manage_referral_model');
        
        echo json_encode($this->manage_referral_model->remove_comment($currentUserData));
    }
}

?>
