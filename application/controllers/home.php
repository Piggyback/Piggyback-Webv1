<?php
/**
 * @mikegao
 * 
 * controller for friends viewing page 
 * //TODO: MG - merge with @andyjiang's home page
 * 
 * created: 11/29/11
 */
//require "assets/facebook-php-sdk/facebook.php";
class Home extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
//        $this->load->model('Facebook_model');
    }

    public function index()
    {        
        $this->load->model('home_model');
        $friends = $this->home_model->load_friends();

        $data['friends'] = $friends;
        
        $currentUserData = $this->session->userdata('currentUserData');
        $data['fbid'] = $currentUserData['fbid'];
        $data['firstName'] = $currentUserData['firstName'];
        $data['lastName'] = $currentUserData['lastName'];
        $this->load->view('home_view', $data);
    }

}

?>
