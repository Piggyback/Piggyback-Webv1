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
        // and deleted != 1?
        $query = $this->db->query("SELECT vid, comment FROM Lists WHERE lid = $lid and date < (SELECT date FROM Referrals WHERE rid = $rid)");
        echo json_encode($query->result());
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

        $this->db->insert('UserLists', $data);

        // return new lid
        $query = $this->db->get_where('UserLists', array('uid' => $uid, 'name' => $newListName, 'date' => $dateTime));
        echo json_encode($query->result());
    }

    // add vendor to existing list -- pass lid that you want to add to, vid to add, date, and comment for vendor
    // if vendor is already in list, return error messags
    function add_vendor_to_list() {

        $lid = $_POST["lid"];
        $vid = $_POST["vid"];
        $date = $_POST["date"];
        $comment = $_POST["comment"];

        // and delete !=1 ?
        $existsQuery = "SELECT vid FROM Lists WHERE lid=$lid AND vid=\"$vid\" AND deleted != 1";

        $existsResult = mysql_query($existsQuery);
        if (!$existsResult) {
            echo "Could not add to list";
            return;
        }
        $count = mysql_num_rows($existsResult);
        if ($count == 0) {
            $query = "INSERT INTO Lists VALUES ($lid,\"$vid\",\"$date\",\"$comment\",0)";
            $result = mysql_query($query);
            if (!$result) {
                echo "Could not add to list";
                return;
            }
        }
        else {
            echo "Already in list";
            return;
        }
        // return false if everything worked and list was successfully added
        
//SELECT *, Lists.date AS listsDate FROM Lists INNER JOIN Vendors ON Lists.vid = Vendors.id WHERE Lists.lid = " . $lid . " AND deleted != 1;");

        $getVendorQuery = "SELECT *, Lists.date AS listsDate FROM Lists INNER JOIN Vendors ON Lists.vid = Vendors.id WHERE Lists.lid = $lid AND deleted != 1 AND Vendors.id = \"$vid\"";
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

        // change deleted flag to 1
        $data = array('deleted' => 1);
        $this->db->update('Lists', $data, array('lid' => $lid, 'vid' => $vid));
    }

    // refer list to friends- add rows to referrals table and referraldetails table
    // uidFriends is given as a json string and immediately converted to a php object for use
    function refer_list() {
        $lid = $_POST["lid"];
        $uid = $_POST["uid"];
        $numFriends = $_POST["numFriends"];
        $uidFriends = json_decode($_POST["uidFriends"]);
        $date = $_POST["date"];
        $comment = $_POST["comment"];
        
        // nonduplicate friends that you are referring this list to
        $newFriends = array();
        
        $q = "INSERT INTO Referrals VALUES ";
        for ($i = 0; $i < $numFriends; $i++) {
           $uidFriend = $uidFriends->$i;
           $existsQuery = "SELECT rid FROM Referrals WHERE uid1 = $uid AND uid2 = $uidFriend AND lid = $lid";
           $result = mysql_query($existsQuery);
           if (!$result) {
               echo "Referral could not be processed1";
               return;
           }
           // if you have not yet referred this friend to this list, then add them onto the query
           // TODO: right now, it does not notify you if you have referred some of the friends to the list already
           if (mysql_num_rows($result) == 0) {
               array_push($newFriends,$uidFriend);
               $q = "$q (NULL, $uid, $uidFriend, \"$date\", $lid, \"$comment\", 0, 0),";
           }
        }

        $q = substr($q,0,-1);

        // run the query to add one entry to the Referral table for each friend referred to the list
        if(count($newFriends) > 0) {
            $result = mysql_query($q);
            if (!$result) {
                echo "Referral could not be processed2";
                return;
            }

            // get all vendors in the referred list
            $getVendorQuery = "SELECT vid from Lists where lid = $lid";
            $vendorResult = mysql_query($getVendorQuery);
            if (!$vendorResult) {
                echo "Could not retrieve vendors in referred list";
                return;
            }

            // set up string for adding all rows - one for each vendor in the list, for each friend
            $addRefDetsQuery = "INSERT INTO ReferralDetails VALUES ";

            // get RID that was just inserted into Referrals table
            for ($i = 0; $i < count($newFriends); $i++) {
                $uidFriend = $newFriends[$i];
                $getRIDquery = "SELECT rid FROM Referrals WHERE uid1 = $uid AND uid2 = $uidFriend AND date = \"$date\" AND lid = $lid AND comment = \"$comment\"";
                $result = mysql_query($getRIDquery);
                if (!$result) {
                    echo "Referral could not be processed3";
                    return;
                }
                $resultRow = mysql_fetch_row($result);
                $rid = $resultRow[0];

                // add each vendor in the list to the referraldetails table
                while($vid = mysql_fetch_row($vendorResult)) {
                    $addRefDetsQuery = "$addRefDetsQuery ($rid,\"$vid[0]\",0,0),";
                }

                // move pointer to vendor array back to the beginning so it can be traversed again for next friend
                mysql_data_seek($vendorResult,0);
            }

            // remove last comma and run query to add rows to ReferralDetails table
            $addRefDetsQuery = substr($addRefDetsQuery,0,-1);

            if(mysql_num_rows($vendorResult) > 0) {
                $result = mysql_query($addRefDetsQuery);
                if (!$result) {
                    echo "Referral details could not be processed";
                    return;
                }
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
}
