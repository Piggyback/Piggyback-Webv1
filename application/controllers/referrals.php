<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Referrals extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('referrals_model');
    }
    
    public function add_new_comment() {
        $currentUserData = $this->session->userdata('currentUserData');
        echo $this->referrals_model->add_new_comment($currentUserData);
    }
    
    public function remove_comment() {
        $currentUserData = $this->session->userdata('currentUserData');
        echo $this->referrals_model->remove_comment();
    }
    
    public function perform_like_action() {
        $currentUserData = $this->session->userdata('currentUserData');
        
        // first see if user has already liked it
        if ($this->referrals_model->is_already_liked($currentUserData) == 0){
            // user has not yet liked it

            // proceed to add new like.
            $this->referrals_model->add_new_like($currentUserData);
        } else {
            // user already liked it!

            // proceed to remove user's like.
            $this->referrals_model->remove_like($currentUserData);
        }

        echo $this->referrals_model->get_like_count();
    }
    
    public function get_referral_items() {
        $currentUserData = $this->session->userdata('currentUserData');
        echo json_encode($this->referrals_model->get_referral_items($currentUserData));;
    }
    
    public function flag_delete_referral_item() {
        $this->referrals_model->flag_delete_referral_item();
    }
    
    public function get_current_date() {
        echo $this->referrals_model->get_current_date();
    }
}

?>
