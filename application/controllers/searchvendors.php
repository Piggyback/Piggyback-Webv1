<?php

class searchvendors extends CI_Controller {

    public function index()
    {
        $this->load->view('search_vendors_view');
        $this->load->model('search_vendors_model');
    }
    
    public function iframe()
    {
        $this->load->view('iframe_view');
    }   
    
    public function perform_search() 
    {
        $this->load->model('search_vendors_model');
//        $data = $this->search_vendors_model->search_vendors();
//        $this->load->view('search_results_view',$data);
        
        $this->search_vendors_model->search_vendors();

    }
    
    public function add_referral()
    {
        $this->load->model('search_vendors_model');
        $this->search_vendors_model->add_referral();
    }
}

?>