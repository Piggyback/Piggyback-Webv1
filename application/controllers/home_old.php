<?php
/**
 * @mikegao
 * 
 * controller for friends viewing page 
 * **NOTE**: Page is now OLD -- new home controller is the bulk of the entire site -- 11/30/11 (first sprint)
 * 
 * created: 11/29/11
 */
//require "assets/facebook-php-sdk/facebook.php";
class Home_old extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
//        $this->load->model('Facebook_model');
    }

    public function index()
    {        
        $this->load->model('home_model_old');
        $friends = $this->home_model_old->load_friends();

        $data['friends'] = $friends;
        
        $currentUserData = $this->session->userdata('currentUserData');
        $data['fbid'] = $currentUserData['fbid'];
        $data['firstName'] = $currentUserData['firstName'];
        $data['lastName'] = $currentUserData['lastName'];
        $this->load->view('home_view_old', $data);
    }

}

?>
