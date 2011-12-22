<?php

/* kimhsiao */

class referral_tracking extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('referral_tracking_model');
    }
    
    public function index()
    {
        $this->load->view('referral_tracking_view');
    }
    
    public function get_likes() {
        $this->referral_tracking_model->get_likes();
    }
    
    public function get_comments() {
        $this->referral_tracking_model->get_comments();
    }
    
    public function get_referral_tracking() {
        $currentUserData = $this->session->userdata('currentUserData');        
        echo json_encode($this->referral_tracking_model->get_referral_tracking($currentUserData));
    }
}

?>
