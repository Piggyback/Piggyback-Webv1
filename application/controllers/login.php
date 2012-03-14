<?php
/* 
    Document   : login.php
    Created on : Nov 30, 2011, 6:49:47 AM
    Author     : gaobi
    Description:
        Login controller.
*/

/* 
   TO-DOs:
*/
class Login extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
    }

    public function index()
    {
        $this->load->view('login_view');
    }
    
    public function check_if_user_exists()
    {
        $fbid = $this->input->post('fbid');
        echo $this->login_model->check_if_user_exists($fbid);
    }
    
    public function add_user() 
    {
        $fbid = $this->input->post('fbid');
        $email = $this->input->post('email');
        $firstName = $this->input->post('firstName');
        $lastName = $this->input->post('lastName');
        $this->login_model->add_user($fbid, $email, $firstName, $lastName);
    }
    
    public function search_for_friends()
    {
        $this->data = $this->input->post('data');
        $this->login_model->search_for_friends($data);
    }
}

?>
