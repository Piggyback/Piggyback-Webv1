<?php

/**
 *  @mike gao
 *
 *  Controls all intereactions with list_model.php
 *
 *   */

class List_controller extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('list_model');
    }

    public function index()
    {
        // only used for ajax function calls
    }

    public function get_list_content()
    {
        $this->list_model->get_list_content();
    }
    
//    public function get_add_to_list_content()
//    {
//        $this->list_model->get_add_to_list_content();
//    }

    public function add_list()
    {
        echo json_encode($this->list_model->add_list());
    }

    public function add_vendor_to_list() {
        echo json_encode($this->list_model->add_vendor_to_list());
    }
    
    public function add_to_existing_list_from_search() {
        echo json_encode($this->list_model->add_to_existing_list_from_search());
    }

    public function add_list_to_new_list_from_nonsearch() {
        echo json_encode($this->list_model->add_list_to_new_list_from_nonsearch());
    }
    
    public function add_list_to_existing_list() {
        echo json_encode($this->list_model->add_list_to_existing_list());
    }
    
    public function delete_list() {
        $this->list_model->delete_list();
    }

    public function delete_vendor_from_list() {
        $this->list_model->delete_vendor_from_list();
    }

    public function refer_list() {
        echo $this->list_model->refer_list();
    }

    public function edit_vendor_comment() {
        $this->list_model->edit_vendor_comment();
    }
    
    public function add_to_new_list_from_search() {
        echo json_encode($this->list_model->add_to_new_list_from_search());
    }
    
    public function add_vendor_to_new_list_from_nonsearch() {
        echo json_encode($this->list_model->add_vendor_to_new_list_from_nonsearch());
    }
    
    public function add_vendor() {
        $this->list_model->add_vendor();
    }
}

?>
