<?php

class searchVendors extends CI_Controller {

    public function index()
    {
        $this->load->view('searchVendors_view');
        $this->load->model('searchVendors_model');
    }
    
    public function iFrame()
    {
        $this->load->view('iframe_view');
    }   
    
    public function performSearch() 
    {
        $this->load->model('searchVendors_model');
        //$data['searchResults'] = $this->searchVendors_model->searchVendors();
        $data = $this->searchVendors_model->searchVendors();
        $this->load->view('searchResults_view',$data);
    }
}

?>