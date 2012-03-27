<?php
require(APPPATH.'libraries/REST_Controller.php');

class InboxAPI extends REST_Controller
{
    /**
     * returns all single vendors referred to user
     */
    function inboxSingleVendorInboxItems_get() {
        $uid = $this->get('id');
        if (!$uid) {
            $this->response(NULL,400);
        } else {
            $this->load->model('inbox_model');
            
            $singleVendorInboxItemsResult = $this->inbox_model->get_single_vendor_inbox_items($uid);
            if ($singleVendorInboxItemsResult) {
                // build arrays in first loop
                $referrerAssocArray = array();
                $vendorAssocArray = array();
                $referralCommentsAssocArray = array();          // assoc array
                $singleVendorInboxItemsArray = array();         // array
                
                // 1. loop through results to set up the associative arrays
                foreach ($singleVendorInboxItemsResult as $row) {    
                    // initialize referrer if not in assoc array
                    if (!array_key_exists($row->referrer_uid, $referrerAssocArray)) {
                        $referrer = new stdClass();
                        $referrer->uid = $row->referrer_uid;
                        $referrer->fbid = $row->referrer_fbid;
                        $referrer->email = $row->referrer_email;
                        $referrer->firstName = $row->referrer_firstName;
                        $referrer->lastName = $row->referrer_lastName;
                        
                        $referrerAssocArray[$row->referrer_uid] = $referrer;
                    }
                    
                    // initialize vendor if not in assoc array
                    if (!array_key_exists($row->vendor_vid, $vendorAssocArray)) {
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
                        
                        $vendorAssocArray[$row->vendor_vid] = $vendor;
                    }
                    
                    // initialize EMPTY array of referral comments if not in assoc array -- will push content later
                    if (!array_key_exists($row->vendor_vid, $referralCommentsAssocArray)) {
                        $referralCommentsAssocArray[$row->vendor_vid] = array();
                    }
                    
                    // initialize vendor referral comment and push into the assoc array
                    $vendorReferralComment = new stdClass();
                    $vendorReferralComment->referrer = $referrerAssocArray[$row->referrer_uid];
                    $vendorReferralComment->comment = $row->referral_comment;
                    $vendorReferralComment->date = $row->referral_date;
                    $vendorReferralComment->referralLid = 0;
                    $vendorReferralComment->listEntryComment = null;
                    
                    array_push($referralCommentsAssocArray[$row->vendor_vid], $vendorReferralComment);
                }
                
                // 2. loop through results to create singleVendorInboxItem objects
                foreach ($singleVendorInboxItemsResult as $row) {
                    $inboxItem = new stdClass();
                    $inboxItem->date = $row->referral_date;
                    $inboxItem->rid = $row->referral_rid;
                    $inboxItem->referrer = $referrerAssocArray[$row->referrer_uid];
                    $inboxItem->comment = $row->referral_comment;
                    $inboxItem->vendor = $vendorAssocArray[$row->vendor_vid];
                    $inboxItem->list = null;
                    $inboxItem->nonUniqueReferralComments = $referralCommentsAssocArray[$row->vendor_vid];
                    array_push($singleVendorInboxItemsArray, $inboxItem);
                }
                
                $this->response($singleVendorInboxItemsArray);
            } else {
                $this->response(array('error' => 'User has no single vendor inbox items'), 404);
            }
        }
    }
    
    function inboxListInboxItems_get() {
        $uid = $this->get('id');
        if (!$uid) {
            $this->response(NULL,400);
        } else {
            $this->load->model('inbox_model');
            
            $listInboxItemsResult = $this->inbox_model->get_list_inbox_items($uid);
            if ($listInboxItemsResult) {
                // build arrays in first loop
                $listInboxItemsAssocArray = array(); // key=rid
                $vendorAssocArray = array();    // key=vid      
                $referrerAssocArray = array();  // key=uid
                $listAssocArray = array();      // key=lid
//                $listEntrysAssocArray = array();    // key=lid
                $referralCommentsAssocArray = array();  // key=vid
                
                // 1. loop through results to set up the associative arrays
                foreach ($listInboxItemsResult as $row) {    
                    // initialize referrer if not in assoc array
                    if (!array_key_exists($row->referrer_uid, $referrerAssocArray)) {
                        $referrer = new stdClass();
                        $referrer->uid = $row->referrer_uid;
                        $referrer->fbid = $row->referrer_fbid;
                        $referrer->email = $row->referrer_email;
                        $referrer->firstName = $row->referrer_firstName;
                        $referrer->lastName = $row->referrer_lastName;
                        
                        $referrerAssocArray[$row->referrer_uid] = $referrer;
                    }
                    
                    // initialize vendor if not in assoc array
                    if (!array_key_exists($row->vendor_vid, $vendorAssocArray)) {
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
                        
                        $vendorAssocArray[$row->vendor_vid] = $vendor;
                    }
                    
                    // initialize EMPTY array of referral comments if not in assoc array -- will push content later
                    if (!array_key_exists($row->vendor_vid, $referralCommentsAssocArray)) {
                        $referralCommentsAssocArray[$row->vendor_vid] = array();
                    }
                    
                    // initialize vendor referral comment and push into the assoc array
                    $vendorReferralComment = new stdClass();
                    $vendorReferralComment->referrer = $referrerAssocArray[$row->referrer_uid];
                    $vendorReferralComment->comment = $row->referral_comment;
                    $vendorReferralComment->date = $row->referral_date;
                    $vendorReferralComment->referralLid = $row->list_lid;
                    $vendorReferralComment->listEntryComment = $row->listentry_comment;
                    
                    array_push($referralCommentsAssocArray[$row->vendor_vid], $vendorReferralComment);
                }
                
                // 2. loop through results to create list and listEntry objects
                foreach ($listInboxItemsResult as $row) {
                    // initialize list object AND initialize EMPTY array of list entrys if not in list assoc array
                    if (!array_key_exists($row->list_lid, $listAssocArray)) {
                        $list = new stdClass();
                        $list->uid = $row->listowner_uid;
                        $list->lid = $row->list_lid;
                        $list->date = $row->list_date;
                        $list->name = $row->list_name;
                        $list->listEntrys = array();
                        
                        $listAssocArray[$row->list_lid] = $list;
                    } 
                    
                    // push list entry to respective array
                    $listEntry = new stdClass();
                    $listEntry->vendor = $vendorAssocArray[$row->vendor_vid];
                    $listEntry->date = $row->listentry_date;
                    $listEntry->comment = $row->listentry_comment;
                    $listEntry->referredBy = $referralCommentsAssocArray[$row->vendor_vid];
                    
                    array_push($listAssocArray[$row->list_lid]->listEntrys, $listEntry);
                }
                
                // 3. loop through results to create listInboxItems
                foreach ($listInboxItemsResult as $row) {
                    if (!array_key_exists($row->referral_rid, $listInboxItemsAssocArray)) {
                        $inboxItem = new stdClass();
                        $inboxItem->date = $row->referral_date;
                        $inboxItem->rid = $row->referral_rid;
                        $inboxItem->referrer = $referrerAssocArray[$row->referrer_uid];
                        $inboxItem->comment = $row->referral_comment;
                        $inboxItem->vendor = null;
                        $inboxItem->list = $listAssocArray[$row->list_lid];
                        $inboxItem->nonUniqueReferralComments = null;
                        
                        $listInboxItemsAssocArray[$row->referral_rid] = $inboxItem;
                    }
                }
                
                $this->response(array_values($listInboxItemsAssocArray));
            } else {
                $this->response(array('error' => 'User has no list inbox items'), 404);
            }
        }
    }
    
    // get both single vendor and list inbox items
    function inbox_get() {
                $uid = $this->get('id');
        if (!$uid) {
            $this->response(NULL,400);
        } else {
            $this->load->model('inbox_model');
            
            $singleVendorInboxItemsResult = $this->inbox_model->get_single_vendor_inbox_items($uid);
            $listInboxItemsResult = $this->inbox_model->get_list_inbox_items($uid);
            if ($singleVendorInboxItemsResult || $listInboxItemsResult) {
                $referrerAssocArray = array();  // key=uid
                $vendorAssocArray = array();    // key=vid
                $referralCommentsAssocArray = array();  // key=vid
                $singleVendorInboxItemsArray = array(); // array
                  
                $listAssocArray = array();      // key=lid
                $listInboxItemsAssocArray = array(); // array  
                if ($singleVendorInboxItemsResult) {                    
                    // 1. loop through results to set up the associative arrays
                    foreach ($singleVendorInboxItemsResult as $row) {    
                        // initialize referrer if not in assoc array
                        if (!array_key_exists($row->referrer_uid, $referrerAssocArray)) {
                            $referrer = new stdClass();
                            $referrer->uid = $row->referrer_uid;
                            $referrer->fbid = $row->referrer_fbid;
                            $referrer->email = $row->referrer_email;
                            $referrer->firstName = $row->referrer_firstName;
                            $referrer->lastName = $row->referrer_lastName;

                            $referrerAssocArray[$row->referrer_uid] = $referrer;
                        }

                        // initialize vendor if not in assoc array
                        if (!array_key_exists($row->vendor_vid, $vendorAssocArray)) {
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

                            $vendorAssocArray[$row->vendor_vid] = $vendor;
                        }

                        // initialize EMPTY array of referral comments if not in assoc array -- will push content later
                        if (!array_key_exists($row->vendor_vid, $referralCommentsAssocArray)) {
                            $referralCommentsAssocArray[$row->vendor_vid] = array();
                        }

                        // initialize vendor referral comment and push into the assoc array
                        $vendorReferralComment = new stdClass();
                        $vendorReferralComment->referrer = $referrerAssocArray[$row->referrer_uid];
                        $vendorReferralComment->comment = $row->referral_comment;
                        $vendorReferralComment->date = $row->referral_date;
                        $vendorReferralComment->referralLid = 0;
                        $vendorReferralComment->listEntryComment = null;

                        array_push($referralCommentsAssocArray[$row->vendor_vid], $vendorReferralComment);
                    }

                    // 2. loop through results to create singleVendorInboxItem objects
                    foreach ($singleVendorInboxItemsResult as $row) {
                        $inboxItem = new stdClass();
                        $inboxItem->date = $row->referral_date;
                        $inboxItem->rid = $row->referral_rid;
                        $inboxItem->referrer = $referrerAssocArray[$row->referrer_uid];
                        $inboxItem->comment = $row->referral_comment;
                        $inboxItem->vendor = $vendorAssocArray[$row->vendor_vid];
                        $inboxItem->list = null;
                        $inboxItem->nonUniqueReferralComments = $referralCommentsAssocArray[$row->vendor_vid];
                        array_push($singleVendorInboxItemsArray, $inboxItem);
                    }
                }
                
                if ($listInboxItemsResult) {                
                    // 1. loop through results to set up the associative arrays
                    foreach ($listInboxItemsResult as $row) {    
                        // initialize referrer if not in assoc array
                        if (!array_key_exists($row->referrer_uid, $referrerAssocArray)) {
                            $referrer = new stdClass();
                            $referrer->uid = $row->referrer_uid;
                            $referrer->fbid = $row->referrer_fbid;
                            $referrer->email = $row->referrer_email;
                            $referrer->firstName = $row->referrer_firstName;
                            $referrer->lastName = $row->referrer_lastName;

                            $referrerAssocArray[$row->referrer_uid] = $referrer;
                        }

                        // initialize vendor if not in assoc array
                        if (!array_key_exists($row->vendor_vid, $vendorAssocArray)) {
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

                            $vendorAssocArray[$row->vendor_vid] = $vendor;
                        }

                        // initialize EMPTY array of referral comments if not in assoc array -- will push content later
                        if (!array_key_exists($row->vendor_vid, $referralCommentsAssocArray)) {
                            $referralCommentsAssocArray[$row->vendor_vid] = array();
                        }

                        // initialize vendor referral comment and push into the assoc array
                        $vendorReferralComment = new stdClass();
                        $vendorReferralComment->referrer = $referrerAssocArray[$row->referrer_uid];
                        $vendorReferralComment->comment = $row->referral_comment;
                        $vendorReferralComment->date = $row->referral_date;
                        $vendorReferralComment->referralLid = $row->list_lid;
                        $vendorReferralComment->listEntryComment = $row->listentry_comment;

                        array_push($referralCommentsAssocArray[$row->vendor_vid], $vendorReferralComment);
                    }

                    // 2. loop through results to create list and listEntry objects
                    foreach ($listInboxItemsResult as $row) {
                        // initialize list object AND initialize EMPTY array of list entrys if not in list assoc array
                        if (!array_key_exists($row->list_lid, $listAssocArray)) {
                            $list = new stdClass();
                            $list->uid = $row->listowner_uid;
                            $list->lid = $row->list_lid;
                            $list->date = $row->list_date;
                            $list->name = $row->list_name;
                            $list->listEntrys = array();

                            $listAssocArray[$row->list_lid] = $list;
                        } 

                        // push list entry to respective array
                        $listEntry = new stdClass();
                        $listEntry->vendor = $vendorAssocArray[$row->vendor_vid];
                        $listEntry->date = $row->listentry_date;
                        $listEntry->comment = $row->listentry_comment;
                        $listEntry->referredBy = $referralCommentsAssocArray[$row->vendor_vid];

                        array_push($listAssocArray[$row->list_lid]->listEntrys, $listEntry);
                    }

                    // 3. loop through results to create listInboxItems
                    foreach ($listInboxItemsResult as $row) {
                        if (!array_key_exists($row->referral_rid, $listInboxItemsAssocArray)) {
                            $inboxItem = new stdClass();
                            $inboxItem->date = $row->referral_date;
                            $inboxItem->rid = $row->referral_rid;
                            $inboxItem->referrer = $referrerAssocArray[$row->referrer_uid];
                            $inboxItem->comment = $row->referral_comment;
                            $inboxItem->vendor = null;
                            $inboxItem->list = $listAssocArray[$row->list_lid];
                            $inboxItem->nonUniqueReferralComments = null;

                            $listInboxItemsAssocArray[$row->referral_rid] = $inboxItem;
                        }
                    }
                }
                
                // sort all inbox items by date
                function cmp($a, $b)
                {
                    return strcmp($b->date, $a->date);
                }
               
                $inboxItemsArray = array_merge($singleVendorInboxItemsArray, array_values($listInboxItemsAssocArray));
                usort($inboxItemsArray, 'cmp');
                $this->response($inboxItemsArray);
            } else {
                $this->response(array('error' => 'User has no inbox items'), 404);
            }
        }
    }
    
    // get inbox contents
//    function inbox_get() {
//        $uid = $this->get('id');
//        if (!$uid) {
//            $this->response(NULL,400);
//        } else {
//            $this->load->model('inbox_model');
//            $this->load->model('list_model');
//            
//            $vendorList = $this->inbox_model->get_vendors($uid);
//            $vendorAssocArray = array();
//            foreach ($vendorList as $row) {
//                $vendor = new stdClass();
//                $vendor->vid = $row->vendor_vid;
//                $vendor->name = $row->vendor_name;
//                $vendor->reference = $row->vendor_reference;
//                $vendor->lat = $row->vendor_lat;
//                $vendor->lng = $row->vendor_lng;
//                $vendor->phone = $row->vendor_phone;
//                $vendor->addr = $row->vendor_addr;
//                $vendor->addrNum = $row->vendor_addrNum;
//                $vendor->addrStreet = $row->vendor_addrStreet;
//                $vendor->addrCity = $row->vendor_addrCity;
//                $vendor->addrState = $row->vendor_addrState;
//                $vendor->addrCountry = $row->vendor_addrCountry;
//                $vendor->addrZip = $row->vendor_addrZip;
//                $vendor->vicinity = $row->vendor_vicinity;
//                $vendor->website = $row->vendor_website;
//                $vendor->icon = $row->vendor_icon;
//                $vendor->rating = $row->vendor_rating;
//                
//                $vendorAssocArray[$row->vendor_vid] = $vendor;
//            }
//            
//            // get all listEntry comments from incoming referrals
//            $listEntryCommentsOfIncomingReferralsResult = $this->list_model->get_list_entry_comments_of_incoming_referrals($uid);
//            $listEntryCommentsOfIncomingReferralsAssocArray = array(array());
//            foreach ($listEntryCommentsOfIncomingReferralsResult as $row) {
//                $listEntryCommentsOfIncomingReferralsAssocArray[$row->listentry_vid][$row->listentry_lid] = $row->listentry_comment;
//            }
//
//            // set up incoming referrals associative array
//            $listsWithEntrysWithIncomingReferralsResult = $this->inbox_model->get_vendor_comments($uid);
//            $incomingReferralsAssocArray = array();
//            if ($listsWithEntrysWithIncomingReferralsResult) {
//                foreach ($listsWithEntrysWithIncomingReferralsResult as $row) {
//                    if (!array_key_exists($row->referral_vid, $incomingReferralsAssocArray)) {                            
//                        $incomingReferralsAssocArray[$row->referral_vid] = array();
//                    }
//
//                    $referredBy = new stdClass();
//                    $referrer = new stdClass();
//
//                    $referrer->uid = $row->referrer_uid;
//                    $referrer->fbid = $row->referrer_fbid;
//                    $referrer->email = $row->referrer_email;
//                    $referrer->firstName = $row->referrer_firstName;
//                    $referrer->lastName = $row->referrer_lastName;
//
//                    $referredBy->referrer = $referrer;
//                    $referredBy->comment = $row->referral_comment;
//                    $referredBy->rid = $row->referral_rid;
//                    $referredBy->referralLid = $row->referral_lid;
//                    $referredBy->date = $row->referral_date;
//                    if (array_key_exists($row->referral_vid, $listEntryCommentsOfIncomingReferralsAssocArray)) {
//                        if (array_key_exists($row->referral_lid, $listEntryCommentsOfIncomingReferralsAssocArray[$row->referral_vid])) {
//                            $referredBy->listEntryComment = $listEntryCommentsOfIncomingReferralsAssocArray[$row->referral_vid][$row->referral_lid];
//                        } else {
//                            $referredBy->listEntryComment = "";
//                        }
//                    } else {
//                        $referredBy->listEntryComment = "";
//                    }
//                    array_push($incomingReferralsAssocArray[$row->referral_vid], $referredBy);
//                }
//            }
//            
//            
//            // build list entrys
//            $listEntryResults = $this->inbox_model->get_list_entries($uid);
//            if ($listEntryResults) {
//                    $listEntrysArray = array(array());
//                    foreach ($listEntryResults as $row) {
//                        // create vendor object
//                        $vendor = new stdClass();
//
//                        $vendor->vid = $row->vendor_vid;
//                        $vendor->name = $row->vendor_name;
//                        $vendor->reference = $row->vendor_reference;
//                        $vendor->lat = $row->vendor_lat;
//                        $vendor->lng = $row->vendor_lng;
//                        $vendor->phone = $row->vendor_phone;
//                        $vendor->addr = $row->vendor_addr;
//                        $vendor->addrNum = $row->vendor_addrNum;
//                        $vendor->addrStreet = $row->vendor_addrStreet;
//                        $vendor->addrCity = $row->vendor_addrCity;
//                        $vendor->addrState = $row->vendor_addrState;
//                        $vendor->addrCountry = $row->vendor_addrCountry;
//                        $vendor->addrZip = $row->vendor_addrZip;
//                        $vendor->vicinity = $row->vendor_vicinity;
//                        $vendor->website = $row->vendor_website;
//                        $vendor->icon = $row->vendor_icon;
//                        $vendor->rating = $row->vendor_rating;
//
//                        // create listEntry object
//                        $listEntry = new stdClass();
//                        $listEntry->vendor = $vendor;
//                        $listEntry->date = $row->list_date;
//                        $listEntry->comment = $row->list_comment;
//                        if (array_key_exists($row->vendor_vid, $incomingReferralsAssocArray)) {
//                            $listEntry->referredBy = $incomingReferralsAssocArray[$row->vendor_vid];
//                        } else {
//                            $listEntry->referredBy = array();
//                        }
//
////                        if(array_key_exists($row->referral_lid,$listEntrysArray)) {
////                            array_push($listEntrysArray[$row->referral_lid],$listEntry);
////                        } else {
////                            $listEntrysArray[$row->referral_lid] = array($listEntry);
////                        }                              
//                    
//                        if (array_key_exists($row->referral_lid, $listEntrysArray)) {
//                            if (array_key_exists($row->referrer_uid, $listEntrysArray[$row->referral_lid])) {
//                                array_push($listEntrysArray[$row->referral_lid][$row->referrer_uid], $listEntry);
//                            }
//                            else {
//                                $listEntrysArray[$row->referral_lid][$row->referrer_uid] = array($listEntry);
//                            }
//                        } else {
//                            $listEntrysArray[$row->referral_lid][$row->referrer_uid] = array($listEntry);
//                        }
//                    }
//                }
//            
//            $inbox = $this->inbox_model->get_inbox_items($uid);
//            
//            if ($inbox) {
//                // build new object to return API results with necessary information
//                $newInbox = array();
//                
//                foreach ($inbox as $inboxItem) {
//                    $newInboxItem = new stdClass();
//                    
//                    // get user who recommended
//                    $referrer = new stdClass();
//                    $referrer->uid = $inboxItem->uid1;
//                    $referrer->fbid = $inboxItem->fbid;
//                    $referrer->firstName = $inboxItem->firstName;
//                    $referrer->lastName = $inboxItem->lastName;
//                    $referrer->email = $inboxItem->email;
//                    $newInboxItem->referrer = $referrer;
//                    
//                    // get other information tied to all referrals
//                    $newInboxItem->date = $inboxItem->referral_date;
//                    $newinboxItem->rid = $inboxItem->referral_rid;
//                    $newInboxItem->lid = $inboxItem->referral_lid;
//                    $newInboxItem->comment = $inboxItem->referral_comment;
//
//                    // list recommendation
//                    if ($inboxItem->referral_lid != 0) {
//                        $newInboxItem->listName = $inboxItem->listName;
//                        
//                        // get other friends referred to this list
////                        $otherFriends = $this->inbox_model->other_friends_to_list($inboxItem->referral_lid,$inboxItem->uid1,$uid);
//                        $newInboxItem->otherFriends = array();
//                        
////                        if (array_key_exists($inboxItem->referral_lid,$listEntrysArray)) {
////                            $newInboxItem->listEntrys = $listEntrysArray[$inboxItem->referral_lid];
////                        } else {
////                            $newInboxItem->listEntrys = array();
////                        }
////                        
//                        if (array_key_exists($inboxItem->referral_lid,$listEntrysArray)) {
//                            if (array_key_exists($referrer->uid, $listEntrysArray[$inboxItem->referral_lid])) {
//                                $newInboxItem->listEntrys = $listEntrysArray[$inboxItem->referral_lid][$referrer->uid];
//                            } else {
//                                $newInboxItem->listEntrys = array();
//                            }
//                        } else {
//                            $newInboxItem->listEntrys = array();
//                        }
//                    }
//                    
//                    // vendor recommendation
//                    else {
//                        $newInboxItem->vid = $inboxItem->vid;
//
//                        // get other friends referred to this vendor
////                        $otherFriends = $this->inbox_model->other_friends_to_vendor($inboxItem->vid,$inboxItem->uid1,$uid);
//                        $otherFriends = array();
//                        $newInboxItem->otherFriends = $otherFriends;
//                        
//                        // get comments for recommendations to you and vendor information
//                        if (array_key_exists($inboxItem->vid, $incomingReferralsAssocArray)) {
//                            $newInboxItem->nonUniqueReferralComments = $incomingReferralsAssocArray[$inboxItem->vid];
//                        } else {
//                            $newInboxItem->nonUniqueReferralComments = array();
//                        }
//                                                
//                        if (array_key_exists($inboxItem->vid,$vendorAssocArray)) {
//                            $newInboxItem->vendor = $vendorAssocArray[$inboxItem->vid];
//                        } else {
//                            $newInboxItem->vendor = nil;
//                        }
//
//                    }
//                    array_push($newInbox,$newInboxItem);
//                }
//            }
//            
//            $this->response($newInbox,200);
//        }
//    }
//    
//    function inboxlistentries_get($uid){
//        $uid = $this->get('uid');
//        if (!$uid) {
//            $this->response(NULL,400);
//        } else {
//            $this->load->model('inbox_model');
//            $response = $this->inbox_model->get_list_entries($uid);
//            $this->response($response,200);
//        }
//    }
//    // get other friends recommended to this vendor by same person
//    function othersToVendor_get() {
//        $vid = $this->get('vid');
//        $uidFriend = $this->get('uidFriend'); 
//        $uidMe = $this->get('uidMe');
//    }
//    
//    // get other friends recommended to this list by same person
//    function othersToList_get() {
//        $lid = $this->get('lid');
//        $uidFriend = $this->get('uidFriend'); 
//        $uidMe = $this->get('uidMe');
//        
//        if (!$lid || !$uidMe || !$uidFriend) {
//            $this->response(NULL,400);
//        } else {
//            $this->load->model('inbox_model');
//            $friends = $this->inbox_model->other_friends_to_list($lid,$uidFriend,$uidMe);
//            $this->response($friends,200);
//        }
//    }
}
?>