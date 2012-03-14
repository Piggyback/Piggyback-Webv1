<?php
require(APPPATH.'libraries/REST_Controller.php');


class Listapi extends REST_Controller
{
    /**
     * returns all lists and respective listEntrys for id
     */
    function lists_get()
    {
        $uid = $this->get('id');
        if (!$uid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('list_model');
            $this->load->model('vendor_model');
            $lists = $this->list_model->get_my_lists($uid);
            if ($lists) {
                foreach ($lists as $currentList) {
                    $listEntrys = $this->list_model->get_list_entries($currentList->lid);
                    foreach ($listEntrys as $currentListEntry) {
                        $vendor = $this->vendor_model->get_vendor_info($currentListEntry->vid);
                        $currentListEntry->vendor = $vendor[0]; // return lone result in result array
                        
                        $referredByWithComments = $this->vendor_model->get_referred_by($uid, $currentListEntry->vid);
                        
                        $i = 0;
                        foreach ($referredByWithComments as $currentReferredByWithComments) {
                            $referredByWithUserKey = new StdClass;
                            $referrerUser = new StdClass;
                            
                            $referrerUser->uid = $currentReferredByWithComments->uid1;
                            $referrerUser->fbid = $currentReferredByWithComments->fbid;
                            $referrerUser->email = $currentReferredByWithComments->email;
                            $referrerUser->firstName = $currentReferredByWithComments->firstName;
                            $referrerUser->lastName = $currentReferredByWithComments->lastName;
                            
                            $referredByWithUserKey->lid = $currentReferredByWithComments->lid;
                            $referredByWithUserKey->date = $currentReferredByWithComments->date;
                            $referredByWithUserKey->comment = $currentReferredByWithComments->comment;                            
                            $referredByWithUserKey->referrer = $referrerUser;
                            
//                            $currentReferredByWithComments = $referredByWithUserKey;
//                            $currentReferredByWithComments->test = "test";
                            $referredByWithComments[$i] = $referredByWithUserKey;
                            $i++;
                        }
                        $currentListEntry->referredByWithComments = $referredByWithComments;
                    }
                    $currentList->listEntrys = $listEntrys;
                }
                $this->response($lists, 200);
            } else {
                $this->response(array('error' => 'User could not be found'), 404);
            }
        }
    }
}

//    function lists_get()
//    {
//        $uid = $this->get('id');
//        if (!$uid) {
//            $this->response(NULL, 400);
//        } else {
//            $this->load->model('list_model');
//            $this->load->model('vendor_model');
//            $lists = $this->list_model->get_my_lists($uid);
//            if ($lists) {
//                foreach ($lists as $currentList) {
//                    $listEntrys = $this->list_model->get_list_entries($currentList->lid);
//                    foreach ($listEntrys as $currentListEntry) {
//                        $vendor = $this->vendor_model->get_vendor_info($currentListEntry->vid);
//                        $currentListEntry->vendor = $vendor[0]; // return lone result in result array
//                    }
//                    $currentList->listEntrys = $listEntrys;
//                }
//                $this->response($lists, 200);
//            } else {
//                $this->response(array('error' => 'User could not be found'), 404);
//            }
//        }
//    }

?>