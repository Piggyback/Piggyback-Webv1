<?php
require(APPPATH.'libraries/REST_Controller.php');


class Userapi extends REST_Controller
{
    /**
     * returns all lists for id
     */
    function user_get()
    {
        $fbid = $this->get('fbid');
        if (!$fbid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('user_model');
            $user = $this->user_model->get_user($fbid);
            if ($user) {
                $this->response($user, 200);
            } else {
                $this->response(NULL, 404);
            }
        }
    }
    
    function user_post()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!property_exists($data, "email")) {
                $data->email = "";
        }
        if (!property_exists($data, "firstName")) {
                $data->firstName = "";
        }
        if (!property_exists($data, "lastName")) {
                $data->lastName = "";
        }

        $this->load->model('user_model');
        $user = new stdClass();
        
        if ($this->user_model->check_if_user_exists($data->fbid)) {
            $friends = array();
            
            $existingUserArray = $this->user_model->get_user($data->fbid);
            $existingUser = $existingUserArray[0];
            $user->userID = $existingUser->userID;
            
            $friendsFromDB = $this->user_model->get_friends_for_current_user($user->userID);
            foreach ($friendsFromDB as $currentFriend) {
                $newFriend = new stdClass();
                $newFriend->userID = $currentFriend->uid;
                $newFriend->fbid = $currentFriend->fbid;
                $newFriend->email = $currentFriend->email;
                $newFriend->firstName = $currentFriend->firstName;
                $newFriend->lastName = $currentFriend->lastName;
                $newFriend->thumbnail = "http://graph.facebook.com/" . $newFriend->fbid . "/picture";
                $newFriend->friends = array();  // TODO: currently no friends
                    
                array_push($friends, $newFriend);  
            }
            
            $user->friends = $friends;
        } else {
            $uid = $this->user_model->add_user($data->fbid, $data->email, $data->firstName, $data->lastName);

            // check which FB friends are using piggyback
            $allUsersFromDB = $this->user_model->get_all_users();
            $friendFBIDArray = $data->friendsID;
            $friendsUID = array();
            $userAssocArrayWithFBIDKey = array();
            $friends = array();

            foreach ($allUsersFromDB as $currentUserFromDB) {
                $userAssocArrayWithFBIDKey[$currentUserFromDB->fbid] = $currentUserFromDB;
            }

            foreach ($friendFBIDArray as $currentFBIDFromPB) {
                if (array_key_exists($currentFBIDFromPB, $userAssocArrayWithFBIDKey)) {
                    // friend is using PB
                    $newFriend = new stdClass();
                    $newFriend->userID = $userAssocArrayWithFBIDKey[$currentFBIDFromPB]->uid;
                    $newFriend->fbid = $userAssocArrayWithFBIDKey[$currentFBIDFromPB]->fbid;
                    $newFriend->email = $userAssocArrayWithFBIDKey[$currentFBIDFromPB]->email;
                    $newFriend->firstName = $userAssocArrayWithFBIDKey[$currentFBIDFromPB]->firstName;
                    $newFriend->lastName = $userAssocArrayWithFBIDKey[$currentFBIDFromPB]->lastName;
                    $newFriend->thumbnail = "http://graph.facebook.com/" . $newFriend->fbid . "/picture";
                    $newFriend->friends = array();  // TODO: currently no friends

                    array_push($friends, $newFriend);
                    array_push($friendsUID, $newFriend->userID);
                }
            }

            // add friends to DB
            $this->user_model->add_friends_for_current_user($uid, $friendsUID);

            $user->userID = $uid;
            $user->friends = $friends;
        }
        
        $this->response($user);
    }
    
    function userFriends_get()
    {
        $uid = $this->get('user');
        if (!$uid) {
            $this->response(NULL, 400);
        } else {
            $this->load->model('user_model');
            $friendsFromDB = $this->user_model->get_friends_for_current_user($uid);
            if ($friendsFromDB) {
                $friends = array();
                foreach ($friendsFromDB as $currentFriend) {
                    $newFriend = new stdClass();
                    $newFriend->userID = $currentFriend->uid;
                    $newFriend->fbid = $currentFriend->fbid;
                    $newFriend->email = $currentFriend->email;
                    $newFriend->firstName = $currentFriend->firstName;
                    $newFriend->lastName = $currentFriend->lastName;
                    $newFriend->thumbnail = "http://graph.facebook.com/" . $newFriend->fbid . "/picture";
                    $newFriend->friends = array();  // TODO: currently no friends
                    
                    array_push($friends, $newFriend);
                }
                
                $user = new stdClass();
                $user->userID = $uid;
                $user->friends = $friends;
                $this->response($user, 200);
            } else {
                $this->response(NULL, 404);
            }
        }
    }
}
?>
