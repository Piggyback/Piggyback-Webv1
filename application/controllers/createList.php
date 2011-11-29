<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class CreateList extends CI_Controller {

    public function index()
    {
        // creates view that allows user to build lists from table of vendors
        $this->load->model('create_list_model');
        $data['vendorList'] = $this->create_list_model->getAllVendors();
        $this->load->view('create_list_view', $data);
    }

    public function addAction()
    {
        // using POST data, insert list items into UserLists and Lists
        $fieldData = $_POST;
        
        $this->load->model('create_list_model');
        $this->create_list_model->addList($fieldData);
                
        // with the uid received, load get_list_view
        $data['listInfo'] = $this->create_list_model->getListInfo($fieldData);
        $this->load->view('get_list_view', $data);
        
    }    
    
//    public function getAction()
//    {
//        $listData = $_POST;
//        $this->load->model('create_list_model');
//        $data['listInfo'] = $this->create_list_model->getList($listData);
//    
//        // with listInfo, populate new view
//        //$this->load->view('new_view_here', $data);
//    }
    
    public function showUserList()
    {
        // get userName
        $data['userName'] = $_GET['userName'];
        $fieldData['uid'] = $_GET['userId'];
        
        // retrieve lists that belong to that user
        $this->load->model('create_list_model');
        $data['listInfo'] = $this->create_list_model->getListInfo($fieldData);
        
        // create new view for it
        $this->load->view('get_list_view', $data);
        
    }
    
    public function showList()
    {
        // get the ID from any view that calls this function
        $listId = $_GET['listId'];      // lid
                
        // pass the ID to model, who will query the database
        //   and return the appropriate list
        $this->load->model('create_list_model');
        
        // return vendorNames given LID (list unique id)
        $data['vendorNames'] = $this->create_list_model->getVendorList($listId);        
        $data['listName'] = $_GET['listName'];
        
        // create new view of just the vendors
        // send new list to view, who will display it
        $this->load->view('showList_view', $data);
    }
}

?>
