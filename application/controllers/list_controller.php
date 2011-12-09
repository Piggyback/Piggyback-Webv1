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
}

?>
