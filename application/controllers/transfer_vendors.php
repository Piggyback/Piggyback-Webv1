<?php

class transfer_vendors extends CI_Controller {

    
    function __construct()
    {
        parent::__construct();
        $this->load->model('transfer_vendors_model');
    }
    
    
    public function index()
    {
        $this->load->view('transfer_vendors_view');
        $this->transfer_google_vendor_table_to_foursquare_api();
    }
    
    public function transfer_google_vendor_table_to_foursquare_api() {
        $this->transfer_vendors_model->transfer_google_vendor_table_to_foursquare_api();
    }

}

?>