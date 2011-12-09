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
        $this->load->model('list_model');
        $this->load->model('manage_referral_model');
        // get current user data
        $currentUserData = $this->session->userdata('currentUserData');

        // get user's lists from db
        $data['myLists'] = $this->list_model->get_my_lists($currentUserData['uid']);

        // pass user's name to view
        $data['currentFirstName'] = $currentUserData['firstName'];
        $data['currentLastName'] = $currentUserData['lastName'];
        $data['currentFBID'] = $currentUserData['fbid'];

        // andy's merge
        $data['inboxItems'] = $this->manage_referral_model->get_inbox_items($currentUserData);

        $this->load->view('home_view', $data);
    }

}

?>
