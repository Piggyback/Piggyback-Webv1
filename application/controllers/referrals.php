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
    
        public function add_referral() {
        $id = $this->input->post('id');
        $uid = $this->input->post('myUID');
        $numFriends = $this->input->post('numFriends');
        $uidFriends = json_decode($this->input->post('uidFriends'));
        $comment = $this->input->post('comment');
        echo $this->referrals_model->add_referral($id,$uid,$numFriends,$uidFriends,$comment);
    }
    
    public function add_vendor() {
        $name = $this->input->post('name');
//        $reference = $this->input->post('reference');
        $id = $this->input->post('id');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $phone = $this->input->post('phone');
        $addr = $this->input->post('addr');
//        $addrNum = $this->input->post('addrNum');
        $addrCrossStreet = $this->input->post('addrCrossStreet');
        $addrCity = $this->input->post('addrCity');
        $addrState = $this->input->post('addrState');
        $addrCountry = $this->input->post('addrCountry');
        $addrZip = $this->input->post('addrZip');
//        $vicinity = $this->input->post('vicinity');
        $website = $this->input->post('website');
//        $icon = $this->input->post('icon');
//        $rating = $this->input->post('rating');
        $tags = $this->input->post('tags');
        $categories = $this->input->post('categories');
        $photos = $this->input->post('photos');
        echo $this->referrals_model->add_vendor($name,$id,$lat,$lng,$phone,$addr,$addrCrossStreet,$addrCity,$addrState,$addrCountry,$addrZip,$website,$tags,$categories,$photos);
    }
    
    public function refer_from_search() {
        $id = $this->input->post('id');
        $uid = $this->input->post('myUID');
        $numFriends = $this->input->post('numFriends');
        $uidFriends = json_decode($this->input->post('uidFriends'));
        $comment = $this->input->post('comment');
        $name = $this->input->post('name');
//        $reference = $this->input->post('reference');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $phone = $this->input->post('phone');
        $addr = $this->input->post('addr');
//        $addrNum = $this->input->post('addrNum');
        $addrCrossStreet = $this->input->post('addrCrossStreet');
        $addrCity = $this->input->post('addrCity');
        $addrState = $this->input->post('addrState');
        $addrCountry = $this->input->post('addrCountry');
        $addrZip = $this->input->post('addrZip');
//        $vicinity = $this->input->post('vicinity');
        $website = $this->input->post('website');
//        $icon = $this->input->post('icon');
//        $rating = $this->input->post('rating');
        $tags = json_decode($this->input->post('tags'));
        $categories = $this->input->post('categories');
        $photos = $this->input->post('photos');
        echo $this->referrals_model->refer_from_search($id,$uid,$numFriends,$uidFriends,$comment,$name,$lat,$lng,$phone,$addr,$addrCrossStreet,$addrCity,$addrState,$addrCountry,$addrZip,$website,$tags,$categories,$photos);
    }    
}

?>
