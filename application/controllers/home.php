<?php

class Home extends CI_Controller {

    public function index()
    {
        $this->load->view('home_view');
    }
    
    public function getFriends()
    {
//        echo 3;
        $this->load->model('home_model');
        $friends = $this->home_model->load_friends();
        
        
        // pass array back to view
        echo json_encode($friends);
//        foreach ($friends as $friend) {
//            echo $friend . '\n';
//        }
    }
}

?>
