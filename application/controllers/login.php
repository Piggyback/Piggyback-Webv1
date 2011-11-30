<?php
/**
 * @mikegao
 * 
 * controller for all login / user creation
 * 
 * created: 11/29/11
 */

class Login extends CI_Controller {

    public function index()
    {
        $this->load->view('login_view');
    }
    
    public function check_if_user_exists()
    {
        $this->load->model('login_model');
        $this->login_model->check_if_user_exists();
    }
    
    public function add_user() 
    {
        $this->load->model('login_model');
        $this->login_model->add_user();
    }
    
    public function search_for_friends()
    {
        
        $this->load->model('login_model');
        $this->login_model->search_for_friends();
    }
    
    public function init_session()
    {
        
    }
}

?>
