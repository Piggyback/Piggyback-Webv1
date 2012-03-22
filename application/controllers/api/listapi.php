<?php
require(APPPATH.'libraries/REST_Controller.php');


class Listapi extends REST_Controller
{   
    /**
     * returns all lists
     */
    function lists_get()
    {
        $uid = $this->get('id');
        if (!$uid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('list_model');
            
            $result = $this->list_model->get_my_lists($uid);
            if ($result) {
                $this->response($result);
            } else {
                $this->response(array('error' => 'User could not be found'), 404);
            }
        }
    }
    
    /**
     * returns all lists and their listentrys
     */
    function listsAndEntrys_get()
    {
        $uid = $this->get('id');
        if (!$uid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('list_model');
            
            $listsResult = $this->list_model->get_my_lists($uid);
            if ($listsResult) {
                $listArray = array();
                
                foreach ($listsResult as $listsRow) {
                    $list = new stdClass();
                    
                    $list->uid = $listsRow->uid;
                    $list->lid = $listsRow->lid;
                    $list->date = $listsRow->date;
                    $list->name = $listsRow->name;
                    $list->listEntrys = array();
                    
                    $listArray[$listsRow->lid] = $list;
                }
                            
                $listsAndEntrysResult = $this->list_model->get_list_with_entrys($uid);
                if ($listsAndEntrysResult) {
                    foreach ($listsAndEntrysResult as $row) {
                        // create vendor object
                        $vendor = new stdClass();

                        $vendor->vid = $row->vendor_vid;
                        $vendor->name = $row->vendor_name;
                        $vendor->reference = $row->vendor_reference;
                        $vendor->lat = $row->vendor_lat;
                        $vendor->lng = $row->vendor_lng;
                        $vendor->phone = $row->vendor_phone;
                        $vendor->addr = $row->vendor_addr;
                        $vendor->addrNum = $row->vendor_addrNum;
                        $vendor->addrStreet = $row->vendor_addrStreet;
                        $vendor->addrCity = $row->vendor_addrCity;
                        $vendor->addrState = $row->vendor_addrState;
                        $vendor->addrCountry = $row->vendor_addrCountry;
                        $vendor->addrZip = $row->vendor_addrZip;
                        $vendor->vicinity = $row->vendor_vicinity;
                        $vendor->website = $row->vendor_website;
                        $vendor->icon = $row->vendor_icon;
                        $vendor->rating = $row->vendor_rating;

                        // create listEntry object
                        $listEntry = new stdClass();

                        $listEntry->vendor = $vendor;
                        $listEntry->date = $row->listentry_date;
                        $listEntry->comment = $row->listentry_comment;

                        $list = $listArray[$row->list_lid];
                        array_push($list->listEntrys, $listEntry);                               
                    }                
                
                }
                
                $this->response(array_values($listArray), 200);
                $this->response($listsResult, 200);
            } else {
                $this->response(array('error' => 'User could not be found'), 404);
            }
        }
    }
    
    /**
     * returns all lists and respective listEntrys and referrals for id
     */
    function listsAndEntrysAndIncomingReferrals_get()
    {
        $uid = $this->get('id');
        if (!$uid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('list_model');
            
            $listsResult = $this->list_model->get_my_lists($uid);
            if ($listsResult) {
                $listAssocArray = array();
                
                foreach ($listsResult as $listsRow) {
                    // create list object
                    $list = new stdClass();
                    
                    $list->uid = $listsRow->uid;
                    $list->lid = $listsRow->lid;
                    $list->date = $listsRow->date;
                    $list->name = $listsRow->name;
                    $list->listEntrys = array();
                    
                    $listAssocArray[$listsRow->lid] = $list;
                }
                
                // get all listEntry comments from incoming referrals
                $listEntryCommentsOfIncomingReferralsResult = $this->list_model->get_list_entry_comments_of_incoming_referrals($uid);
                $listEntryCommentsOfIncomingReferralsAssocArray = array(array());
                foreach ($listEntryCommentsOfIncomingReferralsResult as $row) {
                    $listEntryCommentsOfIncomingReferralsAssocArray[$row->listentry_vid][$row->listentry_lid] = $row->listentry_comment;
                }
                
                // set up incoming referrals associative array
                $listsWithEntrysWithIncomingReferralsResult = $this->list_model->get_list_with_entrys_with_incoming_referrals($uid);
                $incomingReferralsAssocArray = array();
                if ($listsWithEntrysWithIncomingReferralsResult) {
                    foreach ($listsWithEntrysWithIncomingReferralsResult as $row) {
                        if (!array_key_exists($row->referraldetails_vid, $incomingReferralsAssocArray)) {                            
                            $incomingReferralsAssocArray[$row->referraldetails_vid] = array();
                        }
                        
                        $referredBy = new stdClass();
                        $referrer = new stdClass();

                        $referrer->uid = $row->referrer_uid;
                        $referrer->fbid = $row->referrer_fbid;
                        $referrer->email = $row->referrer_email;
                        $referrer->firstName = $row->referrer_firstName;
                        $referrer->lastName = $row->referrer_lastName;

                        $referredBy->referrer = $referrer;
                        $referredBy->comment = $row->referral_comment;
                        $referredBy->rid = $row->referral_rid;
                        $referredBy->referralLid = $row->referral_lid;
                        $referredBy->date = $row->referral_date;
                        if (array_key_exists($row->referraldetails_vid, $listEntryCommentsOfIncomingReferralsAssocArray)) {
                            if (array_key_exists($row->referral_lid, $listEntryCommentsOfIncomingReferralsAssocArray[$row->referraldetails_vid])) {
                                $referredBy->listEntryComment = $listEntryCommentsOfIncomingReferralsAssocArray[$row->referraldetails_vid][$row->referral_lid];
                            } else {
                                $referredBy->listEntryComment = "";
                            }
                        } else {
                            $referredBy->listEntryComment = "";
                        }
                        
                        array_push($incomingReferralsAssocArray[$row->referraldetails_vid], $referredBy);
                    }
                }

                // get listEntrys
                $listsWithEntrysResult = $this->list_model->get_list_with_entrys($uid);
                if ($listsWithEntrysResult) {
                    foreach ($listsWithEntrysResult as $row) {
                        // create vendor object
                        $vendor = new stdClass();

                        $vendor->vid = $row->vendor_vid;
                        $vendor->name = $row->vendor_name;
                        $vendor->reference = $row->vendor_reference;
                        $vendor->lat = $row->vendor_lat;
                        $vendor->lng = $row->vendor_lng;
                        $vendor->phone = $row->vendor_phone;
                        $vendor->addr = $row->vendor_addr;
                        $vendor->addrNum = $row->vendor_addrNum;
                        $vendor->addrStreet = $row->vendor_addrStreet;
                        $vendor->addrCity = $row->vendor_addrCity;
                        $vendor->addrState = $row->vendor_addrState;
                        $vendor->addrCountry = $row->vendor_addrCountry;
                        $vendor->addrZip = $row->vendor_addrZip;
                        $vendor->vicinity = $row->vendor_vicinity;
                        $vendor->website = $row->vendor_website;
                        $vendor->icon = $row->vendor_icon;
                        $vendor->rating = $row->vendor_rating;

                        // create listEntry object
                        $listEntry = new stdClass();

                        $listEntry->vendor = $vendor;
                        $listEntry->date = $row->listentry_date;
                        $listEntry->comment = $row->listentry_comment;
                        if (array_key_exists($row->vendor_vid, $incomingReferralsAssocArray)) {
                            $listEntry->referredBy = $incomingReferralsAssocArray[$row->vendor_vid];
                        } else {
                            $listEntry->referredBy = array();
                        }

                        $list = $listAssocArray[$row->list_lid];
                        array_push($list->listEntrys, $listEntry);                               
                    }                
                
                }
                
                $this->response(array_values($listAssocArray), 200);
            } else {
                $this->response(array('error' => 'User could not be found'), 404);
            }
        }
    }
    
    function listEntryCommentsOfIncomingReferrals_get()
    {
        $uid = $this->get('id');
        if (!$uid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('list_model');
            
            $result = $this->list_model->get_list_entry_comments_of_incoming_referrals($uid);
            
            if ($result) {
                $this->response($result, 200);
            } else {
                $this->response(array('error' => 'User could not be found'), 404);
            }
        }
    }
}

?>