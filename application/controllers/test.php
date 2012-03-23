<?php

class test extends CI_Controller {

    
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('test_model');
    }
    
    
    public function index()
    {
        $this->load->view('test_view');
        $this->transfer_google_vendor_table_to_foursquare_api();
    }
    
    public function transfer_google_vendor_table_to_foursquare_api() {
        $this->test_model->transfer_google_vendor_table_to_foursquare_api();
    }

}

?>