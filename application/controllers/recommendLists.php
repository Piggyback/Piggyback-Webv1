<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class RecommendLists extends CI_Controller {

  
    public function index()
    {
        $this->load->model('recommendList_model');
        $data['userList'] = $this->recommendList_model->getListInfo();
        $data['friendList'] = $this->recommendList_model->getFriends();
        $this->load->view('recommendLists_view', $data);
    }

    public function createRecommendAction()
    {
        $fieldData = $_POST;
        
        $this->load->model('recommendList_model');
        $this->recommendList_model->addRecommendLists($fieldData);
        
        // call new view for success        
    }
    
    public function getRecommendLists()
    {
        //$fieldData = $_POST;
        $this->load->model('recommendList_model');
        $data['recommendLists'] = $this->recommendList_model->getRecommendLists();
        $this->load->view('displayRecommendLists_view', $data);
    }
}


?>
