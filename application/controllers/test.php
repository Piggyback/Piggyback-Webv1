<?php

class test extends CI_Controller {

    public function index()
    {
        $this->load->model('test_model');
        $this->load->view('test_view');
    }
    
    public function testmeth() {
        $this->load->model('test_model');
        $data['blah'] = $this->test_model->testMethod();
        $this->load->view('test_view',$data);
    }  
    
    /*
     * test method to call inboxview
     * 
     * @andyjiang
     */
    public function inboxview() {
        $currentUserData = $this->session->userdata('currentUserData');
        $this->load->model('manage_referral_model');
        $data['inboxItems'] = $this->manage_referral_model->get_inbox_items($currentUserData);      // eventually would either need to pass uid from session
        $this->load->view('inbox_view', $data);
    }
    
    /*
     * test method to add a new comment
     * 
     * @andyjiang
     */
    public function add_new_comment() {
        $currentUserData = $this->session->userdata('currentUserData');
        $this->load->model('manage_referral_model');
        $this->manage_referral_model->add_new_comment($currentUserData);
    }
    
    /*
     * test method to add a new like
     * 
     * @andyjiang
     */
    public function perform_like_action() {
        $currentUserData = $this->session->userdata('currentUserData');
        $this->load->model('manage_referral_model');
        
//        $alreadyLiked = $this->manage_referral_model->is_already_liked($currentUserData);
//        
//        if ($alreadyLiked)
//            echo " user has liked it ";
//        else
//            echo " user has not liked it ";
        
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
    
}

?>