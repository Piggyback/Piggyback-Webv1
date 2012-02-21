<?php
/*
    Document   : home.php
    Created on : Nov 30, 2011, 6:49:47 AM
    Author     : @lemikegao
    Description:
        Controller for the entire site
*/

class Home extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('list_model');
    
        $this->load->model('referrals_model');
    }

    public function index()
    {
        // get current user data
        $currentUserData = $this->session->userdata('currentUserData');
        if ($currentUserData == false) {
            header('Location: login');
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
            $inboxData['rowsRequested'] = 3;
            $inboxData['itemType'] = "inbox";
            $data['inboxItems'] = $this->referrals_model->get_referral_items($inboxData);

            $this->load->view('home_view', $data);
        }
    }
    
    // andy created this function, not sure where else to put it
    public function send_email() {
        $currentUserData = $this->session->userdata('currentUserData');
        
        $config = Array(
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'getpiggybackbugs',
            'smtp_pass' => DB_PASSWORD,
            'mailtype'  => 'html', 
            'charset'   => 'iso-8859-1',
            'newline'   => "\r\n",
            'crlf'      => "\r\n"
        );
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        
        $senderBody = str_replace("\r\n", "<br>", $this->input->post('senderBody'));
        $senderBody = str_replace("\n", "<br>", $this->input->post('senderBody'));
        $senderName = $currentUserData['firstName'] . ' ' . $currentUserData['lastName'];
        $senderEmail = $currentUserData['email'];
        
        $this->email->from($senderEmail, $senderName);
        $this->email->reply_to($senderEmail, $senderName);
        $this->email->to('getpiggyback@gmail.com');
        $this->email->subject("[bug] Piggyback Bug Report from ". $senderName);
        $this->email->message($senderBody);
        
        
        if (!$this->email->send()) {
            echo "Failed to send email\n";
            echo $this->email->print_debugger();
        }
    }

}

?>
