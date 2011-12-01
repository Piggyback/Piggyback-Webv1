<?php
/* 
    Document   : home.php
    Created on : Nov 30, 2011, 6:49:47 AM
    Author     : gaobi
    Description:
        Controller for the entire site
*/

/* 
   TO-DOs:
*/

class Home extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {   
        $this->load->model('home_model');
        // get current user data
        $currentUserData = $this->session->userdata('currentUserData');
        
        // get user's lists from db
        $data['myLists'] = $this->home_model->load_my_lists($currentUserData['uid']);
        $this->load->view('home_view', $data);
    }

}

?>
