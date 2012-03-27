<?php

class searchvendors extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('search_vendors_model');
    }
    
    public function perform_search() {      
        $searchLoc = $this->input->post('searchLocation');
        $searchText = $this->input->post('searchText');
        echo json_encode($this->search_vendors_model->search_vendors($searchLoc,$searchText));
    }
    
    public function get_friends() {
        echo $this->search_vendors_model->get_friends();
    }

    public function get_search_details() {
        $id = $this->input->post('id');
        echo json_encode($this->search_vendors_model->get_search_details($id));
    }
}

?>