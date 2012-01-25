<?php

/**
 *  @mike gao
 *   */

class List_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_my_lists($currentUID)
    {
        $query = $this->db->get_where('UserLists', array('uid' => $currentUID, 'deleted !=' => 1));

        return $query->result();
    }


    public function get_list_content()
    {
        $lid = $this->input->post('lid');
        $query = $this->db->query("SELECT *, Lists.date AS listsDate FROM Lists INNER JOIN Vendors ON Lists.vid = Vendors.id WHERE Lists.lid = $lid AND deleted != 1;");
        echo json_encode($query->result());
    }
    
    public function get_add_to_list_content() {
        $lid = $this->input->post('lid');
        $rid = $this->input->post('rid');
        
        $query = "SELECT vid, comment FROM Lists WHERE lid = $lid AND date < (SELECT date FROM Referrals WHERE rid = $rid) AND ((deleted != 1) OR (deleted = 1 AND deletedDate > (SELECT date FROM Referrals WHERE rid = $rid)))";
        $result = $this->db->query($query);
        echo json_encode($result->result());
    }


    public function add_list()
    {
        $uid = $this->input->post('uid');
        $newListName = $this->input->post('newListName');

        $dateTime = date('Y-m-d H:i:s');
        $data = array(
            'uid' => $uid,
            'name' => $newListName,
            'date' => $dateTime
        );

        // check if a list with that name exists already
        $existsQuery = "SELECT lid FROM UserLists WHERE uid = $uid AND name = \"$newListName\" AND deleted != 1";
        $result = $this->db->query($existsQuery);
        
        if (!$result) {
            echo json_encode("Could not add list");
            return;
        }
         
        else if ($result->num_rows() > 0) {
            echo json_encode("List already exists!");
            return;
        }
        
        else {
            $this->db->insert('UserLists', $data);

            // return new lid
            $query = $this->db->get_where('UserLists', array('uid' => $uid, 'name' => $newListName, 'date' => $dateTime));
            echo json_encode($query->result());
        }
    }

    // add vendor to existing list -- pass lid that you want to add to, vid to add, date, and comment for vendor
    // if vendor is already in list, return error messags
    function add_vendor_to_list() {
        
        $lid = $this->input->post('lid');
        $vid = $this->input->post('vid');
        $date = $this->input->post('date');
        $comment = $this->input->post('comment');

        $existsQuery = "SELECT vid FROM Lists WHERE lid=$lid AND vid=\"$vid\" AND deleted != 1";
        $existsResult = $this->db->query($existsQuery);

        if ($existsResult->num_rows() == 0) {
            $query = "INSERT INTO Lists VALUES ($lid,\"$vid\",\"$date\",\"$comment\",0,0)";
            $result = $this->db->query($query);
            if (!$result) {
                echo "Could not add to list";
                return;
            }
        }
        else {
            echo "Already in list";
            return;
        }

        // get vendor data so that the list html can be dynamically updated
        $getVendorQuery = "SELECT *, Lists.date AS listsDate FROM Lists 
                    INNER JOIN Vendors ON Lists.vid = Vendors.id 
                    WHERE Lists.lid = $lid AND deleted != 1 AND Vendors.id = \"$vid\"";
        $result = $this->db->query($getVendorQuery);
        echo json_encode($result->result());
    }

    function delete_list() {
        $lid = $this->input->post('lid');
        // delete from Lists table
//        $this->db->delete('Lists', array('lid' => $lid));

        // delete from UserLists table
//        $this->db->delete('UserLists', array('lid' => $lid));
        
        // change deleted flag to 1
        $data = array('deleted' => 1);
        $this->db->update('UserLists', $data, array('lid' => $lid));
    }

    function delete_vendor_from_list() {
        $lid = $this->input->post('lid');
        $vid = $this->input->post('vid');
        $dateTime = date('Y-m-d H:i:s');

        // change deleted flag to 1
        $data = array('deleted' => 1, 'deletedDate' => $dateTime);
        $this->db->update('Lists', $data, array('lid' => $lid, 'vid' => $vid));
    }

    // refer list to friends- add rows to referrals table and referraldetails table
    // uidFriends is given as a json string and immediately converted to a php object for use
    function refer_list() {
        $lid = $this->input->post('lid');
        $uid = $this->input->post('uid');
        $prevRid = $this->input->post('rid');
        $numFriends = $this->input->post('numFriends');
        $uidFriends = json_decode($this->input->post('uidFriends'));
        $comment = $this->input->post('comment');
        
        $date = date('Y-m-d H:i:s');

        // nonduplicate friends that you are referring this list to
        $newFriends = array();
        
        $q = "INSERT INTO Referrals VALUES ";
        for ($i = 0; $i < $numFriends; $i++) {
           $uidFriend = $uidFriends->$i;
           $existsQuery = "SELECT rid FROM Referrals WHERE uid1 = $uid AND uid2 = $uidFriend AND lid = $lid";
           $result = $this->db->query($existsQuery);

           // if you have not yet referred this friend to this list, then add them onto the query
           // TODO: right now, it does not notify you if you have referred some of the friends to the list already
//           if (mysql_num_rows($result) == 0) {
           if ($result->num_rows() == 0) {
               array_push($newFriends,$uidFriend);
               $q = "$q (NULL, $uid, $uidFriend, \"$date\", $lid, \"$comment\", 0, 0),";
           }
        }

        $q = substr($q,0,-1);

        // run the query to add one entry to the Referral table for each friend referred to the list
        if(count($newFriends) > 0) {
            $this->db->trans_start();
            $result = $this->db->query($q);

            // referring list from inbox - get all vendors in the referred list
            if ($prevRid != 0) {
                $getVendorQuery = "SELECT vid FROM Lists WHERE lid = $lid AND date < (SELECT date FROM Referrals WHERE rid = $prevRid) 
                                AND ((deleted != 1) OR (deleted = 1 AND deletedDate > (SELECT date FROM Referrals WHERE rid = $prevRid)))";
            } 
            
            // referring list from sidebar - get all vendors in the referred list
            else {
                $getVendorQuery = "SELECT vid FROM Lists WHERE lid = $lid AND deleted != 1";
            }
            
            $vendorResult = $this->db->query($getVendorQuery);

            // set up string for adding all rows - one for each vendor in the list, for each friend
            $addRefDetsQuery = "INSERT INTO ReferralDetails VALUES ";

            // get RID that was just inserted into Referrals table
            for ($i = 0; $i < count($newFriends); $i++) {
                $uidFriend = $newFriends[$i];
                $getRIDquery = "SELECT rid FROM Referrals WHERE uid1 = $uid AND uid2 = $uidFriend AND date = \"$date\" AND lid = $lid AND comment = \"$comment\"";
                $result = $this->db->query($getRIDquery);
                
                if ($result->num_rows() > 0) {
                    $rid = $result->row()->rid;
                }

                // add each vendor in the list to the referraldetails table
                if ($vendorResult->num_rows() > 0) {
                    foreach ($vendorResult->result() as $row) {
                        $addRefDetsQuery = "$addRefDetsQuery ($rid,\"$row->vid\",0,0),";
                    }
                }
            }

            // remove last comma and run query to add rows to ReferralDetails table
            $addRefDetsQuery = substr($addRefDetsQuery,0,-1);

            if($vendorResult->num_rows() > 0) {
                $result = $this->db->query($addRefDetsQuery);
            }
            
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                echo "List referral could not be processed";
            }
        }
        
        // return false if no errors
        return false;
    }

    function edit_vendor_comment() {
        $newComment = $this->input->post('newComment');
        $lid = $this->input->post('lid');
        $vid = $this->input->post('vid');

        $data = array('comment' => $newComment);
        $this->db->update('Lists', $data, array('lid' => $lid, 'vid' => $vid));
    }
    
    // **************** wrapper functions ********************** // 
    
    function add_to_new_list_from_search() {
        $this->db->trans_start();
        
        add_list();
        add_vendor_to_list();
        add_vendor();
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            echo "Could not create list. Please try again!";
        }       
    }
    
    function add_to_existing_list_from_search() {
        $this->db->trans_start();
        
        
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            echo "Adding to list could not be processed";
        }        
    }
    
    function add_vendor_to_new_list_from_nonsearch() {
        $this->db->trans_start();
        
        
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            echo "Adding to new list could not be processed";
        }       
    }
    
    function add_list_to_new_list_from_nonsearch() {
        $this->db->trans_start();
        
        
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            echo "Adding to new list could not be processed";
        }    
    }
    
    function refer_from_search() {
        $this->db->trans_start();
        
        
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            echo "Referral could not be processed";
        }       
    }  
}
