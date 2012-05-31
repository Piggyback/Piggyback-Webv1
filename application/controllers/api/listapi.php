<?php
require(APPPATH.'libraries/REST_Controller.php');


class Listapi extends REST_Controller
{   
    function coreDataListDelete_put() {
        $lid = $this->put('lid');
        
        $this->load->model('list_model');
        $this->list_model->core_data_delete_list($lid);
        
        $this->response($lid);
    }
    
    function coreDataListEntryDelete_put() {
        $leid = $this->put('leid');
        $date = $this->put('date');
        
        $this->load->model('list_model');
        $this->list_model->core_data_delete_list_entry($leid,$date);
        
        $this->response($leid);
    }
    
    function coreDataLists_get()
    {
        $uid = $this->get('id');
        if (!$uid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('list_model');
            
            $listsResult = $this->list_model->get_lists_for_core_data($uid);
            if ($listsResult) {
                $listsArray = array();
                
                foreach ($listsResult as $row) {
                    $list = new stdClass();
                    $list->listID = $row->list_id;
                    $list->createdDate = $row->list_createdDate;
                    $list->name = $row->list_name;
                    $list->listOwnerID = $row->list_ownerId;
                    $list->listCount = $row->list_count;

                    array_push($listsArray, $list);
                }
                
                $this->response($listsArray);
            } else {
                $this->response(NULL, 404);
            }
        }
    }
    
    function coreDataLists_post()
    {
        $data = json_decode(file_get_contents("php://input"));
        
        // vendor info
//        $vid = $this->post('vid');
//        $vendorName = $this->post('vendorName');
//        $lat = $this->post('lat');
//        $lng = $this->post('lng');
//        $phone = $this->post('phone');
//        $addr = $this->post('addr');
//        $addrCrossStreet = $this->post('addrCrossStreet');
//        $addrCity = $this->post('addrCity');
//        $addrState = $this->post('addrState');
//        $addrCountry = $this->post('addrCountry');
//        $addrZip = $this->post('addrZip');
//        $website = $this->post('website');
        
        
        $this->load->model('list_model');
        $lid = $this->list_model->post_lists_for_core_data($data->uid, $data->date, $data->name);
//        $this->list_model->add_new_vendor($vid, $vendorName, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, $addrCountry, $addrZip, $website);
        
        $list = new stdClass();
        $list->listID = $lid;

        $this->response($list);        
    }
    
    function coreDataListEntrys_get()
    {
        $lid = $this->get('list');
        $uid = $this->get('user');
        if (!$lid or !$uid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('list_model');
            
            $listEntrysResult = $this->list_model->get_list_entrys_for_core_data($uid, $lid);
            if ($listEntrysResult) {
                $listEntrysArray = array();
                $vendorAssocArray = array();
                
                foreach ($listEntrysResult as $row) {
                    if (!array_key_exists($row->vendor_id, $vendorAssocArray)) {
                        $vendor = new stdClass();
                        $vendor->vendorID = $row->vendor_id;
                        $vendor->name = $row->vendor_name;
                        $vendor->lat = $row->vendor_lat;
                        $vendor->lng = $row->vendor_lng;
                        $vendor->phone = $row->vendor_phone;
                        $vendor->addr = $row->vendor_addr;
                        $vendor->addrCrossStreet = $row->vendor_addrCrossStreet;
                        $vendor->addrCity = $row->vendor_addrCity;
                        $vendor->addrState = $row->vendor_addrState;
                        $vendor->addrCountry = $row->vendor_addrCountry;
                        $vendor->addrZip = $row->vendor_addrZip;
                        $vendor->website = $row->vendor_website;
                        $vendor->vendorReferralCommentsCount = $row->vendor_numReferrals;

                        $vendorAssocArray[$row->vendor_id] = $vendor;
                    }
                
                    $listEntry = new stdClass();
                    $listEntry->listEntryID = $row->listentry_id;
                    $listEntry->assignedListID = $row->listentry_assignedListID;
                    $listEntry->comment = $row->listentry_comment;
                    $listEntry->addedDate = $row->listentry_addedDate;
                    $listEntry->vendor = $vendorAssocArray[$row->vendor_id];
                    
                    array_push($listEntrysArray, $listEntry);
                }
                
                $this->response($listEntrysArray);
            } else {
                $this->response(NULL, 404);
            }
        }
    }
    
    function coreDataMyListEntrys_get()
    {
        $lid = $this->get('list');
        $uid = $this->get('user');
        if (!$lid or !$uid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('list_model');
            
            $listEntrysResult = $this->list_model->get_my_list_entrys_for_core_data($uid, $lid);
            if ($listEntrysResult) {
                $listEntrysArray = array();
                $vendorAssocArray = array();
                
                foreach ($listEntrysResult as $row) {
                    if (!array_key_exists($row->vendor_id, $vendorAssocArray)) {
                        $vendor = new stdClass();
                        $vendor->vendorID = $row->vendor_id;
                        $vendor->name = $row->vendor_name;
                        $vendor->lat = $row->vendor_lat;
                        $vendor->lng = $row->vendor_lng;
                        $vendor->phone = $row->vendor_phone;
                        $vendor->addr = $row->vendor_addr;
                        $vendor->addrCrossStreet = $row->vendor_addrCrossStreet;
                        $vendor->addrCity = $row->vendor_addrCity;
                        $vendor->addrState = $row->vendor_addrState;
                        $vendor->addrCountry = $row->vendor_addrCountry;
                        $vendor->addrZip = $row->vendor_addrZip;
                        $vendor->website = $row->vendor_website;
                        $vendor->vendorReferralCommentsCount = $row->vendor_numReferrals;

                        $vendorAssocArray[$row->vendor_id] = $vendor;
                    }
                
                    $listEntry = new stdClass();
                    $listEntry->listEntryID = $row->listentry_id;
                    $listEntry->assignedListID = $row->listentry_assignedListID;
                    $listEntry->comment = $row->listentry_comment;
                    $listEntry->addedDate = $row->listentry_addedDate;
                    $listEntry->vendor = $vendorAssocArray[$row->vendor_id];
                    
                    array_push($listEntrysArray, $listEntry);
                }
                
                $this->response($listEntrysArray);
            } else {
                $this->response(NULL, 404);
            }
        }
    }
    
    // add new list entry
    // add vendor to vendor database
    function coreDataMyListEntrys_post()
    {
        $data = json_decode(file_get_contents("php://input"));
//        ob_start();
//        var_dump($data);
//        $result = ob_get_clean();
//        $fp = fopen('mikegaoerror.txt', 'a');
//        fwrite($fp, $result . '\n');
//        fclose($fp);
        
//        if (!$lid) {
//            $this->response(NULL, 400);
//        } else {
            $this->load->model('list_model');
            $this->load->model('vendor_model');
            
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

            $leid = $this->list_model->put_my_list_entrys_for_core_data($data->lid, $vendor->vendorID, $data->date, $data->comment);
            $this->vendor_model->add_vendor($vendor->name, $vendor->vendorID, $vendor->lat, $vendor->lng, $vendor->phone, $vendor->addr, $vendor->addrCrossStreet, $vendor->addrCity, $vendor->addrState, 
                                            $vendor->addrCountry, $vendor->addrZip, $vendor->website, $vendor->vendorPhotos);
            
            $listEntry = new stdClass();
            $listEntry->listEntryID = $leid;

            $this->response($listEntry);
//        }
    }
    
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
                        $vendor->lat = $row->vendor_lat;
                        $vendor->lng = $row->vendor_lng;
                        $vendor->phone = $row->vendor_phone;
                        $vendor->addr = $row->vendor_addr;
                        $vendor->addrCrossStreet = $row->vendor_addrCrossStreet;
                        $vendor->addrCity = $row->vendor_addrCity;
                        $vendor->addrState = $row->vendor_addrState;
                        $vendor->addrCountry = $row->vendor_addrCountry;
                        $vendor->addrZip = $row->vendor_addrZip;
                        $vendor->website = $row->vendor_website;

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
                        if (!array_key_exists($row->referral_vid, $incomingReferralsAssocArray)) {                            
                            $incomingReferralsAssocArray[$row->referral_vid] = array();
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
                        if (array_key_exists($row->referral_vid, $listEntryCommentsOfIncomingReferralsAssocArray)) {
                            if (array_key_exists($row->referral_lid, $listEntryCommentsOfIncomingReferralsAssocArray[$row->referral_vid])) {
                                $referredBy->listEntryComment = $listEntryCommentsOfIncomingReferralsAssocArray[$row->referral_vid][$row->referral_lid];
                            } else {
                                $referredBy->listEntryComment = "";
                            }
                        } else {
                            $referredBy->listEntryComment = "";
                        }
                        
                        array_push($incomingReferralsAssocArray[$row->referral_vid], $referredBy);
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
                        $vendor->lat = $row->vendor_lat;
                        $vendor->lng = $row->vendor_lng;
                        $vendor->phone = $row->vendor_phone;
                        $vendor->addr = $row->vendor_addr;
                        $vendor->addrCrossStreet = $row->vendor_addrCrossStreet;
                        $vendor->addrCity = $row->vendor_addrCity;
                        $vendor->addrState = $row->vendor_addrState;
                        $vendor->addrCountry = $row->vendor_addrCountry;
                        $vendor->addrZip = $row->vendor_addrZip;
                        $vendor->website = $row->vendor_website;

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