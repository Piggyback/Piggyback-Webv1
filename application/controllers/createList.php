<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class CreateList extends CI_Controller {

//    public function __construct() {
//        parent::__construct();
//    }
    
    public function index()
    {
        $this->load->view('create_list_view');
    }

    public function addAction()
    {
        
        $this->load->model('create_list_model');
        
        $myInstance = new Create_List_Model();
        $myInstance->addList($_POST);
        
    }    
}

?>
