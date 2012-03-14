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
        $uid = $currentUserData['uid'];
        $rid = $this->input->post('rid');
        $comment = $this->input->post('comment');
        echo $this->referrals_model->add_new_comment($uid,$rid,$comment);
    }
    
    public function remove_comment() {
        $currentUserData = $this->session->userdata('currentUserData');
        $uid = $currentUserData['uid'];
        $cid = $this->input->post('cid');
        echo $this->referrals_model->remove_comment($uid,$cid);
    }
    
    public function perform_like_action() {
        $currentUserData = $this->session->userdata('currentUserData');
        $uid = $currentUserData['uid'];
        $rid = $this->input->post('rid');

        // first see if user has already liked it
        if ($this->referrals_model->is_already_liked($uid,$rid) == 0){
            // user has not yet liked it

            // proceed to add new like.
            $this->referrals_model->add_new_like($uid,$rid);
        } else {
            // user already liked it!
            // proceed to remove user's like.
            $this->referrals_model->remove_like($uid,$rid);
        }

        echo $this->referrals_model->get_like_count($rid);
    }
    
    public function get_referral_items() {
        $currentUserData = $this->session->userdata('currentUserData');
        $uid = $currentUserData['uid'];
        $rowStart = $this->input->post('rowStart');
        $rowsRequested = $this->input->post('rowsRequested');
        $itemType = $this->input->post('itemType');
        echo json_encode($this->referrals_model->get_referral_items($uid,$rowStart,$rowsRequested,$itemType));
    }
    
    public function flag_delete_referral_item() {
        $rid = $this->input->post('rid');
        $itemType = $this->input->post('itemType'); 
        $this->referrals_model->flag_delete_referral_item($rid,$itemType);
    }
    
    public function get_current_date() {
        echo $this->referrals_model->get_current_date();
    }
}

?>
