<?php

class searchvendors extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('search_vendors_model');
    }
    
    public function perform_search() {      
        $searchLoc = $this->input->post('searchLocation');
        $searchText = $this->input->post('searchText');
        echo $this->search_vendors_model->search_vendors($searchLoc,$searchText);
    }
    
    public function get_friends() {
        echo $this->search_vendors_model->get_friends();
    }
    
    public function add_referral() {
        $id = $this->input->post('id');
        $uid = $this->input->post('myUID');
        $numFriends = $this->input->post('numFriends');
        $uidFriends = json_decode($this->input->post('uidFriends'));
        $comment = $this->input->post('comment');
        echo $this->search_vendors_model->add_referral($id,$uid,$numFriends,$uidFriends,$comment);
    }
    
    public function add_vendor() {
        $name = $this->input->post('name');
        $reference = $this->input->post('reference');
        $id = $this->input->post('id');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $phone = $this->input->post('phone');
        $addr = $this->input->post('addr');
        $addrNum = $this->input->post('addrNum');
        $addrStreet = $this->input->post('addrStreet');
        $addrCity = $this->input->post('addrCity');
        $addrState = $this->input->post('addrState');
        $addrCountry = $this->input->post('addrCountry');
        $addrZip = $this->input->post('addrZip');
        $vicinity = $this->input->post('vicinity');
        $website = $this->input->post('website');
        $icon = $this->input->post('icon');
        $rating = $this->input->post('rating');
        echo $this->search_vendors_model->add_vendor($name,$reference,$id,$lat,$lng,$phone,$addr,$addrNum,$addrStreet,$addrCity,$addrState,$addrCountry,$addrZip,$vicinity,$website,$icon,$rating);
    }
    
    public function refer_from_search() {
        $id = $this->input->post('id');
        $uid = $this->input->post('myUID');
        $numFriends = $this->input->post('numFriends');
        $uidFriends = json_decode($this->input->post('uidFriends'));
        $comment = $this->input->post('comment');
        $name = $this->input->post('name');
        $reference = $this->input->post('reference');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $phone = $this->input->post('phone');
        $addr = $this->input->post('addr');
        $addrNum = $this->input->post('addrNum');
        $addrStreet = $this->input->post('addrStreet');
        $addrCity = $this->input->post('addrCity');
        $addrState = $this->input->post('addrState');
        $addrCountry = $this->input->post('addrCountry');
        $addrZip = $this->input->post('addrZip');
        $vicinity = $this->input->post('vicinity');
        $website = $this->input->post('website');
        $icon = $this->input->post('icon');
        $rating = $this->input->post('rating');
        echo $this->search_vendors_model->refer_from_search($id,$uid,$numFriends,$uidFriends,$comment,$name,$reference,$lat,$lng,$phone,$addr,$addrNum,$addrStreet,$addrCity,$addrState,$addrCountry,$addrZip,$vicinity,$website,$icon,$rating);
    }    
    
    public function get_search_details() {
        $reference = $this->input->post('reference');
        echo $this->search_vendors_model->get_search_details($reference);
    }
}

?>