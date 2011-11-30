<?php

/*
 * @andyjiang
 * 
 * controller manages all referral list interactions
 * 
 */

class referlist extends CI_Controller {

  
    public function index()
    {
        $this->load->model('refer_list_model');
        $data['userList'] = $this->refer_list_model->get_list_info();
        $data['friendList'] = $this->refer_list_model->get_friends();
        $this->load->view('send_referrals_view', $data);
    }

    public function add_referral()
    {
        $fieldData = $_POST;
        
        $this->load->model('refer_list_model');
        $this->refer_list_model->add_referral($fieldData);
        
//        // call new view for success        
    }
    
    public function get_referral()
    {
        $this->load->model('refer_list_model');
        $data['recommendLists'] = $this->refer_list_model->get_referral();
        $this->load->view('received_referrals_view', $data);
    }
}


?>
