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
        $this->load->model('manage_referral_model');
        $data['inboxItems'] = $this->manage_referral_model->get_inbox_items();      // eventually would either need to pass uid from session
        $this->load->view('received_referrals_view', $data);
    }
}

?>