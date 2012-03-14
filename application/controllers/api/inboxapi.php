<?php
require(APPPATH.'libraries/REST_Controller.php');

class InboxAPI extends REST_Controller
{
    // get inbox contents
    function inbox_get() {
        $uid = $this->get('uid');
        if (!$uid) {
            $this->response(NULL,400);
        } else {
            $this->load->model('inbox_model');
            $this->load->model('list_model');
            $this->load->model('vendor_model');
            $inbox = $this->inbox_model->get_inbox_items($uid);
            
            if ($inbox) {
                foreach ($inbox as $inboxItem) {
                    // list recommendation
                    if ($inboxItem->lid != 0) {
                        // get other friends referred to this list
                        $otherFriends = $this->inbox_model->other_friends_to_list($inboxItem->lid,$inboxItem->uid1,$uid);
                        $inboxItem->otherFriends = $otherFriends;
                        
                        // get contents of list
                        $listEntrys = $this->list_model->get_list_entries($inboxItem->lid);
                        foreach ($listEntrys as $currentListEntry) {
                            $vendor = $this->vendor_model->get_vendor_info($currentListEntry->vid);
                            $currentListEntry->vendor = $vendor[0]; // return lone result in result array
                        }
                        $inboxItem->listEntrys = $listEntrys;
                    }
                    
                    // vendor recommendation
                    else {
                        // get vendor info
                        $vendor = $this->vendor_model->get_vendor_info($inboxItem->vid);
                        $inboxItem->vendor = $vendor[0];
                        
                        // get other friends referred to this vendor
                        $otherFriends = $this->inbox_model->other_friends_to_vendor($inboxItem->vid,$inboxItem->uid1,$uid);
                        $inboxItem->otherFriends = $otherFriends;
                    }
                }
            }
            
            $this->response($inbox,200);
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