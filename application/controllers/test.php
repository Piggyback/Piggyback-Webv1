<?php

class test extends CI_Controller {

    public function index()
    {
        $this->load->model('test_model');
        $this->load->view('test');
    }
    
    public function testmeth() {
        $this->load->model('test_model');
        $data['blah'] = $this->test_model->testMethod();
        $this->load->view('test_view',$data);
    }  
    
    public function refer_list() {
        $this->load->model('test_model');
        $this->test_model->refer_list();
    }

}

?>