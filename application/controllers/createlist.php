<?php

/*
 * @andyjiang
 * 
 * this controller manages all list related items
 *  
 * 
 */

class CreateList extends CI_Controller {

    public function index()
    {
        // creates view that allows user to build lists from table of vendors
        $this->load->model('create_list_model');
        $data['vendorList'] = $this->create_list_model->get_all_vendors();
        
        // view will now display a list of all vendors
        $this->load->view('create_list_view', $data);
    }

    public function add_action()
    {
        // using POST data, insert list items into UserLists and Lists
        $fieldData = $_POST;
        
        $this->load->model('create_list_model');
        $this->create_list_model->add_list($fieldData);
                
        // with the uid received, load get_list_view
        $data['listInfo'] = $this->create_list_model->get_list_info($fieldData);
        $data['uid'] = $fieldData['uid'];
        
        $this->load->view('get_list_view', $data);
        
    }    
        
    public function show_user_list()
    {
        // get userName
        $data['userName'] = $_GET['userName'];
        $fieldData['uid'] = $_GET['userId'];
        $data['uid'] = $fieldData['uid'];
        
        // retrieve lists that belong to that user
        $this->load->model('create_list_model');
        $data['listInfo'] = $this->create_list_model->get_list_info($fieldData);
        
        // create new view for it
        $this->load->view('get_list_view', $data);
        
    }
    
    public function show_list()
    {
        // get the ID from any view that calls this function
        $listId = $_GET['listId'];      // lid
                
        // pass the ID to model, who will query the database
        //   and return the appropriate list
        $this->load->model('create_list_model');
        
        // return vendorNames given LID (list unique id)
        $data['vendorNames'] = $this->create_list_model->get_vendor_list($listId);        
        $data['listName'] = $_GET['listName'];
        
        // create new view of just the vendors
        // send new list to view, who will display it
        $this->load->view('showList_view', $data);
    }
    
    public function edit_list()
    {
        
    }
    
    
}

?>
