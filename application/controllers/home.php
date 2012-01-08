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
        $this->load->model('list_model');
//        $this->load->model('manage_referral_model');        
    
        $this->load->model('referrals_model');
    }

    public function index()
    {
        // get current user data
        $currentUserData = $this->session->userdata('currentUserData');
        if ($currentUserData == false) {
            header('Location: http://192.168.11.28/login');
        } else {
        
            // get user's lists from db
            $data['myLists'] = $this->list_model->get_my_lists($currentUserData['uid']);

            // pass user's name to view
            $data['currentFirstName'] = $currentUserData['firstName'];
            $data['currentLastName'] = $currentUserData['lastName'];
            $data['currentFBID'] = $currentUserData['fbid'];
            $data['currentUID'] = $currentUserData['uid'];

            // andy's merge
    //        $data['inboxItems'] = $this->manage_referral_model->load_inbox_items($currentUserData);
    //        $data['friendActivityItems'] = $this->manage_referral_model->load_friend_activity_items($currentUserData);

            $inboxData = $currentUserData;
            $inboxData['rowStart'] = 0;
            $inboxData['itemType'] = "inbox-tab";
            $data['inboxItems'] = $this->referrals_model->get_referral_items($inboxData);

            $this->load->view('home_view', $data);
        }
    }

}

?>
