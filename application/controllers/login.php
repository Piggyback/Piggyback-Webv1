<?php

class Login extends CI_Controller {

    public function index()
    {
        $this->load->view('login_view');
    }
    
    public function checkIfUserExists()
    {
        $this->load->model('login_model');
        $this->login_model->check_if_user_exists();
    }
    
    public function addUser() 
    {
        echo 1;

        $this->load->model('login_model');
        $this->login_model->add_new_user();
    }
    
    public function searchForFriends()
    {
        echo 2;
        
        $this->load->model('login_model');
        $this->login_model->search_for_friends();
    }
}

?>
