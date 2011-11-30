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
        $this->load->view('home_view');
    }

}

?>
