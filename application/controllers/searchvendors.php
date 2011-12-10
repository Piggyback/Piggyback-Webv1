<?php

class searchvendors extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('search_vendors_model');
    }
    
    public function index() {
        $this->load->view('search_vendors_view');
    }
    
    public function iframe() {
        $this->load->view('iframe_view');
    }   
    
    public function perform_search() {      
        $this->search_vendors_model->search_vendors();
    }
    
    public function get_friends() {
        $this->search_vendors_model->get_friends_list();
    }
    
    public function add_referral() {
        $this->search_vendors_model->add_referral();
    }
    
    public function add_vendor() {
        $this->search_vendors_model->add_vendor();
    }
}

?>