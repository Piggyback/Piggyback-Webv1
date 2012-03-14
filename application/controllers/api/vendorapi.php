<?php
require(APPPATH.'libraries/REST_Controller.php');

class VendorAPI extends REST_Controller
{
    function vendor_get() {
        $vid = $this->get('vid');
        if (!$vid) {
            $this->response(NULL,400);
        } else {
            $this->load->model('vendor_model');
            $vendor = $this->vendor_model->get_vendor_info($vid);
            $this->response($vendor,200);
        }
    }
    
    function referredby_get() {
        $uid = $this->get('uid');
        $vid = $this->get('vid');
        if (!$vid || !$uid) {
            $this->response(NULL,400);
        } else {
            $this->load->model('vendor_model');
            $referredby = $this->vendor_model->get_referred_by($uid,$vid);
            $this->response($referredby,200);
        }
    }
}
?>
