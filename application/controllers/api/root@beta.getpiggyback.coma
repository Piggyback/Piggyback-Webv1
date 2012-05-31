<?php
require(APPPATH.'libraries/REST_Controller.php');

class VendorAPI extends REST_Controller
{
    function coreDataVendorReferralComments_get() 
    {
        $uid = $this->get('user');
        $vid = $this->get('vendor');
        if (!$uid or !$vid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('vendor_model');
            
            $vendorReferralCommentsResult = $this->vendor_model->get_vendor_referral_comments_for_core_data($uid, $vid);
            if ($vendorReferralCommentsResult) {
                $vendorReferralCommentsArray = array();
                
                foreach ($vendorReferralCommentsResult as $row) {
                    $vendorReferralComment = new stdClass();
                    $vendorReferralComment->referralAndVendorID = $row->referral_id . '+' . $vid;
                    $vendorReferralComment->referralID = $row->referral_id;
                    $vendorReferralComment->assignedVendorID = $vid;
                    
                    if ($row->referral_vid == $vid) {
                        $vendorReferralComment->comment = $row->referral_comment;
                    } else {
                        $vendorReferralComment->comment = $row->listentry_comment;
                    }
                    
                    $vendorReferralComment->referralDate = $row->referral_date;
                    
                    $referrer = new stdClass();
                    $referrer->userID = $row->referrer_id;
                    $referrer->fbid = $row->referrer_fbid;
                    $referrer->email = $row->referrer_email;
                    $referrer->firstName = $row->referrer_firstName;
                    $referrer->lastName = $row->referrer_lastName;
                    $referrer->thumbnail = "http://graph.facebook.com/" . $row->referrer_fbid . "/picture";
                    
                    $vendorReferralComment->referrer = $referrer;
                    
                    array_push($vendorReferralCommentsArray, $vendorReferralComment);
                }
                
                $this->response($vendorReferralCommentsArray);
            } else {
                $this->response(NULL, 404);
            }
        }
    }
    
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
    
    function vendorphotos_get() {
        $vid = $this->get('id');
        if (!$vid) {
            $this->response(NULL,400);
        } else {
            $this->load->model('vendor_model');
            $vendorPhotos = $this->vendor_model->get_vendor_photos($vid);
            $this->response($vendorPhotos,200);
        }
    }
        
}
?>
