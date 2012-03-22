<?php
require(APPPATH.'libraries/REST_Controller.php');

class InboxAPI extends REST_Controller
{
    // get inbox contents
    function inbox_get() {
        $uid = $this->get('id');
        if (!$uid) {
            $this->response(NULL,400);
        } else {
            $this->load->model('inbox_model');
            $this->load->model('list_model');
            
            $vendorList = $this->inbox_model->get_vendors($uid);
            $vendorAssocArray = array();
            foreach ($vendorList as $row) {
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
                
                $vendorAssocArray[$row->vendor_vid] = $vendor;
            }
            
            // get all listEntry comments from incoming referrals
            $listEntryCommentsOfIncomingReferralsResult = $this->list_model->get_list_entry_comments_of_incoming_referrals($uid);
            $listEntryCommentsOfIncomingReferralsAssocArray = array(array());
            foreach ($listEntryCommentsOfIncomingReferralsResult as $row) {
                $listEntryCommentsOfIncomingReferralsAssocArray[$row->listentry_vid][$row->listentry_lid] = $row->listentry_comment;
            }

            // set up incoming referrals associative array
            $listsWithEntrysWithIncomingReferralsResult = $this->inbox_model->get_vendor_comments($uid);
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
            
            
            // build list entrys
            $listEntryResults = $this->inbox_model->get_list_entries($uid);
            if ($listEntryResults) {
                    $listEntrysArray = array(array());
                    foreach ($listEntryResults as $row) {
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
                        $listEntry->date = $row->list_date;
                        $listEntry->comment = $row->list_comment;
                        if (array_key_exists($row->vendor_vid, $incomingReferralsAssocArray)) {
                            $listEntry->referredBy = $incomingReferralsAssocArray[$row->vendor_vid];
                        } else {
                            $listEntry->referredBy = array();
                        }

//                        if(array_key_exists($row->referral_lid,$listEntrysArray)) {
//                            array_push($listEntrysArray[$row->referral_lid],$listEntry);
//                        } else {
//                            $listEntrysArray[$row->referral_lid] = array($listEntry);
//                        }                              
                    
                        if (array_key_exists($row->referral_lid, $listEntrysArray)) {
                            if (array_key_exists($row->referrer_uid, $listEntrysArray[$row->referral_lid])) {
                                array_push($listEntrysArray[$row->referral_lid][$row->referrer_uid], $listEntry);
                            }
                            else {
                                $listEntrysArray[$row->referral_lid][$row->referrer_uid] = array($listEntry);
                            }
                        } else {
                            $listEntrysArray[$row->referral_lid][$row->referrer_uid] = array($listEntry);
                        }
                    }
                }
            
            $inbox = $this->inbox_model->get_inbox_items($uid);
            
            if ($inbox) {
                // build new object to return API results with necessary information
                $newInbox = array();
                
                foreach ($inbox as $inboxItem) {
                    $newInboxItem = new stdClass();
                    
                    // get user who recommended
                    $referrer = new stdClass();
                    $referrer->uid = $inboxItem->uid1;
                    $referrer->fbid = $inboxItem->fbid;
                    $referrer->firstName = $inboxItem->firstName;
                    $referrer->lastName = $inboxItem->lastName;
                    $referrer->email = $inboxItem->email;
                    $newInboxItem->referrer = $referrer;
                    
                    // get other information tied to all referrals
                    $newInboxItem->date = $inboxItem->referral_date;
                    $newinboxItem->rid = $inboxItem->referral_rid;
                    $newInboxItem->lid = $inboxItem->referral_lid;
                    $newInboxItem->comment = $inboxItem->referral_comment;

                    // list recommendation
                    if ($inboxItem->referral_lid != 0) {
                        $newInboxItem->listName = $inboxItem->listName;
                        
                        // get other friends referred to this list
//                        $otherFriends = $this->inbox_model->other_friends_to_list($inboxItem->referral_lid,$inboxItem->uid1,$uid);
                        $otherFriends = array();
                        $newInboxItem->otherFriends = $otherFriends;
                        
//                        if (array_key_exists($inboxItem->referral_lid,$listEntrysArray)) {
//                            $newInboxItem->listEntrys = $listEntrysArray[$inboxItem->referral_lid];
//                        } else {
//                            $newInboxItem->listEntrys = array();
//                        }
//                        
                        if (array_key_exists($inboxItem->referral_lid,$listEntrysArray)) {
                            if (array_key_exists($referrer->uid, $listEntrysArray[$inboxItem->referral_lid])) {
                                $newInboxItem->listEntrys = $listEntrysArray[$inboxItem->referral_lid][$referrer->uid];
                            } else {
                                $newInboxItem->listEntrys = array();
                            }
                        } else {
                            $newInboxItem->listEntrys = array();
                        }
                    }
                    
                    // vendor recommendation
                    else {
                        $newInboxItem->vid = $inboxItem->vid;

                        // get other friends referred to this vendor
//                        $otherFriends = $this->inbox_model->other_friends_to_vendor($inboxItem->vid,$inboxItem->uid1,$uid);
                        $otherFriends = array();
                        $newInboxItem->otherFriends = $otherFriends;
                        
                        // get comments for recommendations to you and vendor information
                        if (array_key_exists($inboxItem->vid, $incomingReferralsAssocArray)) {
                            $newInboxItem->nonUniqueReferralComments = $incomingReferralsAssocArray[$inboxItem->vid];
                        } else {
                            $newInboxItem->nonUniqueReferralComments = array();
                        }
                                                
                        if (array_key_exists($inboxItem->vid,$vendorAssocArray)) {
                            $newInboxItem->vendor = $vendorAssocArray[$inboxItem->vid];
                        } else {
                            $newInboxItem->vendor = nil;
                        }

                    }
                    array_push($newInbox,$newInboxItem);
                }
            }
            
            $this->response($newInbox,200);
        }
    }
    
    function inboxlistentries_get($uid){
        $uid = $this->get('uid');
        if (!$uid) {
            $this->response(NULL,400);
        } else {
            $this->load->model('inbox_model');
            $response = $this->inbox_model->get_list_entries($uid);
            $this->response($response,200);
        }
    }
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