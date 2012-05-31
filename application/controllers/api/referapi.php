<?php
require(APPPATH.'libraries/REST_Controller.php');


class Referapi extends REST_Controller
{
    
    function restRefer_post()
    {
        $data = json_decode(file_get_contents("php://input"));
//        ob_start();
//        var_dump($data);
//        $result = ob_get_clean();
//        $fp = fopen('mikegaoerror.txt', 'a');
//        fwrite($fp, $result . '\n');
//        fclose($fp);
        
        // if no comment, make it blank comment
        if (!property_exists($data, "comment")) {
                $data->comment = "";
        }
        
        // if no lid, then this is single vendor referral, so set lid to 0
        if (!property_exists($data, "lid")) {
            $data->lid = 0;
        }
        
        // if no vendor, then this is list referral, so set vid to 0
        if (!property_exists($data, "vendor")) {
            $data->vendor->vendorID = '0';
        } 
        
        // if it is a vendor referral, then add vendor to db in case it is not there
        else {
            $vendor = $data->vendor;
            if (!property_exists($vendor, "name")) {
                $vendor->name = "";
            }
            if (!property_exists($vendor, "vendorID")) {
                // should never be blank
                $vendor->vendorID = "";
            }
            if (!property_exists($vendor, "lat")) {
                $vendor->lat = 0;
            }
            if (!property_exists($vendor, "lng")) {
                $vendor->lng = 0;
            }
            if (!property_exists($vendor, "phone")) {
                $vendor->phone = "";
            }
            if (!property_exists($vendor, "addr")) {
                $vendor->addr = "";
            }
            if (!property_exists($vendor, "addrCrossStreet")) {
                $vendor->addrCrossStreet = "";
            }
            if (!property_exists($vendor, "addrCity")) {
                $vendor->addrCity = "";
            }
            if (!property_exists($vendor, "addrState")) {
                $vendor->addrState = "";
            }
            if (!property_exists($vendor, "addrCountry")) {
                $vendor->addrCountry = "";
            }
            if (!property_exists($vendor, "addrZip")) {
                $vendor->addrZip = "";
            }
            if (!property_exists($vendor, "website")) {
                $vendor->website = "";
            }
            
            $this->load->model('vendor_model');
            $this->vendor_model->add_vendor($vendor->name, $vendor->vendorID, $vendor->lat, $vendor->lng, $vendor->phone, $vendor->addr, $vendor->addrCrossStreet, $vendor->addrCity, $vendor->addrState, 
                                            $vendor->addrCountry, $vendor->addrZip, $vendor->website, $vendor->vendorPhotos);
        }
       
        $this->load->model('referrals_model');
        $this->referrals_model->add_referral_rest($data->senderUID, $data->receiverUID, $data->date, $data->lid, $data->vendor->vendorID, $data->comment);
        
        $this->response(null,200);
    }
}
?>
