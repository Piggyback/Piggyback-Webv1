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
        $fieldData['uid'] = 2409887757;
        $this->load->model('create_list_model');
        $data['listInfo'] = $this->create_list_model->getListInfo($fieldData);
        $data['userName'] = "derp";
        
        $this->load->view('get_list_view', $data);
    }

    public function getAction()
    {
        $listData = $_POST;
        $this->load->model('create_list_model');
        $data['listInfo'] = $this->create_list_model->getList($listData);
    
        // with listInfo, populate new view
        //$this->load->view('new_view_here', $data);
        
    }
    
//    
//    
//    public function showList()
//    {
//        // show list
//        // get the ID from any view that calls this function
//        $listId = 1; //$_GET['listId'];      // lid
//        
//        // pass the ID to model, who will query the database
//        //   and return the appropriate list
//        $this->load->model('create_list_model');
//        
//        // return vendorNames given LID (list unique id)
//        $data['vendorNames'] = $this->create_list_model->getVendorList($listId);        
//        
//        // create new view of just the vendors
//        // send new list to view, who will display it
//        $this->load->view('showList_view', $data);
//    }
    
    
}

?>
