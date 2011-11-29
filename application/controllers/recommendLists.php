<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class RecommendLists extends CI_Controller {

  
    public function index()
    {
        $this->load->model('recommend_list_model');
        $data['userList'] = $this->recommend_list_model->getListInfo();
        $data['friendList'] = $this->recommend_list_model->getFriends();
        $this->load->view('recommendLists_view', $data);
    }

    public function createRecommendAction()
    {
        $fieldData = $_POST;
        
        $this->load->model('recommend_list_model');
        $this->recommend_list_model->addRecommendLists($fieldData);
        
        // call new view for success        
    }
    
    public function getRecommendLists()
    {
        $this->load->model('recommend_list_model');
        $data['recommendLists'] = $this->recommend_list_model->getRecommendLists();
        $this->load->view('displayRecommendLists_view', $data);
    }
}


?>
