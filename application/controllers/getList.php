<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class GetList extends CI_Controller {

//    public function __construct() {
//        parent::__construct();
//    }
    
    public function index()
    {
        $this->load->model('create_list_model');
        $data['listInfo'] = $this->create_list_model->getListInfo();
        $this->load->view('get_list_view', $data);
    }

    public function getAction()
    {
        $this->load->model('create_list_model');
        $this->create_list_model->getList($_POST);
    }
}

?>
