<?php

/*
    Document   : list_controller.php
    Created on : Nov 30, 2011, 6:49:47 AM
    Author     : @lemikegao
    Description:
        Controller for all list actions
*/

class List_controller extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('list_model');
    }

    public function index()
    {
        // only used for ajax function calls
    }

    public function get_list_content()
    {
        $lid = $this->input->post('lid');
        echo json_encode($this->list_model->get_list_content($lid));
    }

    public function add_list()
    {
        $uid = $this->input->post('uid');
        $newListName = $this->input->post('newListName');
        echo json_encode($this->list_model->add_list($uid, $newListName));
    }

    public function add_vendor_to_list() {
        $lid = $this->input->post('lid');
        $vid = $this->input->post('vid');
        $comment = $this->input->post('comment');
        echo json_encode($this->list_model->add_vendor_to_list($lid, $vid, $comment));
    }
    
    public function add_to_existing_list_from_search() {
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
        $tags = json_decode($this->input->post('tags'));
        $categories = $this->input->post('categories');
        $photos = $this->input->post('photos');
        $lid = $this->input->post('lid');
        $vid = $this->input->post('vid');
        $comment = $this->input->post('comment');

        echo json_encode($this->list_model->add_to_existing_list_from_search($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $website, $tags, $categories, $photos, $lid, $vid, $comment));
    }

    public function add_list_to_new_list_from_nonsearch() {
        $uid = $this->input->post('uid');
        $newListName = $this->input->post('newListName');
        $lid = $this->input->post('lid');
        $rid = $this->input->post('rid');
        echo json_encode($this->list_model->add_list_to_new_list_from_nonsearch($uid, $newListName, $lid, $rid));
    }
    
    public function add_list_to_existing_list() {
        $outerLid = $this->input->post('outerLid');
        $innerLid = $this->input->post('innerLid');
        $rid = $this->input->post('rid');
        echo json_encode($this->list_model->add_list_to_existing_list($outerLid, $innerLid, $rid));
    }
    
    public function delete_list() {
        $lid = $this->input->post('lid');
        $this->list_model->delete_list($lid);
    }

    public function delete_vendor_from_list() {
        $lid = $this->input->post('lid');
        $vid = $this->input->post('vid');
        $this->list_model->delete_vendor_from_list($lid, $vid);
    }

    public function refer_list() {
        $lid = $this->input->post('lid');
        $uid = $this->input->post('uid');
        $prevRid = $this->input->post('rid');
        $numFriends = $this->input->post('numFriends');
        $uidFriends = json_decode($this->input->post('uidFriends'));
        $comment = $this->input->post('comment');
        echo $this->list_model->refer_list($lid, $uid, $prevRid, $numFriends, $uidFriends, $comment);
    }

    public function edit_vendor_comment() {
        $newComment = $this->input->post('newComment');
        $lid = $this->input->post('lid');
        $vid = $this->input->post('vid');
        $this->list_model->edit_vendor_comment($newComment, $lid, $vid);
    }
    
    public function add_to_new_list_from_search() {
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
        $tags = json_decode($this->input->post('tags'));
        $categories = $this->input->post('categories');
        $photos = $this->input->post('photos');
        $uid = $this->input->post('uid');
        $newListName = $this->input->post('newListName');
        
        $vid = $this->input->post('vid');
        $comment = $this->input->post('comment');
        echo json_encode($this->list_model->add_to_new_list_from_search($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $website, $tags, $categories, $photos, $uid, $newListName, $vid, $comment));
    }
    
    public function add_vendor_to_new_list_from_nonsearch() {
        $uid = $this->input->post('uid');
        $newListName = $this->input->post('newListName');
        $vid = $this->input->post('vid');
        $comment = $this->input->post('comment');
        echo json_encode($this->list_model->add_vendor_to_new_list_from_nonsearch($uid, $newListName, $vid, $comment));
    }
    
    public function add_vendor() {
        $name = $this->input->post('name');
        $id = $this->input->post('id');
        $lat = $this->input->post('lat');
        $lng = $this->input->post('lng');
        $phone = $this->input->post('phone');
        $addr = $this->input->post('addr');
        $addrCrossStreet = $this->input->post('addrCrossStreet');
        $addrCity = $this->input->post('addrCity');
        $addrState = $this->input->post('addrState');
        $addrCountry = $this->input->post('addrCountry');
        $addrZip = $this->input->post('addrZip');
        $website = $this->input->post('website');
        $tags = $this->input->post('tags');
        $categories = $this->input->post('categories');
        $photos = $this->input->post('photos');
        
        $this->list_model->add_vendor($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $website, $tags, $categories, $photos);
    }
}

?>
