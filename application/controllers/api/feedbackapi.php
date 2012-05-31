<?php
require(APPPATH.'libraries/REST_Controller.php');

class FeedbackAPI extends REST_Controller
{
    function feedback_post() 
    {
        $data = json_decode(file_get_contents("php://input"));
        
        if (!property_exists($data, "comment")) {
                $data->comment = "";
        }
        if (!property_exists($data, "uid")) {
                $data->uid = 0;
        }
        if (!property_exists($data, "date")) {
                $data->date = date();
        }
        
        $this->load->model('feedback_model');
        $this->feedback_model->add_feedback($data->comment,$data->uid,$data->date);
        
        $this->response(null,200);
    }
}
?>