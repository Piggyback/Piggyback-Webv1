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
        $query = $this->db->get_where('UserLists', array('uid' => $currentUID));

        return $query->result();
    }


    public function get_list_content()
    {
        $lid = $this->input->post('lid');
        $query = $this->db->query("SELECT *, Lists.date AS listsDate FROM Lists INNER JOIN Vendors ON Lists.vid = Vendors.id WHERE Lists.lid = " . $lid . ";");
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
    function add_vendor_to_list() {

        $lid = $_POST["lid"];
        $vid = $_POST["vid"];
        $date = $_POST["date"];
        $comment = $_POST["comment"];

        $existsQuery = "SELECT vid FROM Lists WHERE lid=$lid AND vid=\"$vid\"";

        $existsResult = mysql_query($existsQuery);
        $count = mysql_num_rows($existsResult);
        if ($count == 0) {
            $query = "INSERT INTO Lists VALUES ($lid,\"$vid\",\"$date\",\"$comment\")";
            $success = mysql_query($query);
            if (!$success) {
                echo "Could not add to list";
                return;
            }
        }
        else {
            echo "Already in list";
            return;
        }
        // return false if everything worked and list was successfully added
        echo false;
    }

    function delete_list() {
        $lid = $this->input->post('lid');
        // delete from Lists table
        $this->db->delete('Lists', array('lid' => $lid));

        // delete from UserLists table
        $this->db->delete('UserLists', array('lid' => $lid));
    }

    function delete_vendor_from_list() {
        $lid = $this->input->post('lid');
        $vid = $this->input->post('vid');

        // delete from Lists table
        $this->db->delete('Lists', array('lid' => $lid, 'vid' => $vid));
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
                
        $q = "INSERT INTO Referrals VALUES ";
        for ($i = 0; $i < $numFriends; $i++) {
           $friendNum = "friend".$i;
           $uidFriend = $uidFriends->$friendNum;
           $q = "$q (NULL, $uid, $uidFriend, \"$date\", $lid, \"$comment\"),";
        }
        
        $q = substr($q,0,-1);
        echo $q;
              
        // run the query to add one entry to the Referral table for each friend referred to the list
        if($numFriends > 0) {
            mysql_query($q);
            
            // get all vendors in the referred list
            $getVendorQuery = "SELECT vid from Lists where lid = $lid";
            $vendorResult = mysql_query($getVendorQuery);
            
            // set up string for adding all rows - one for each vendor in the list, for each friend
            $addRefDetsQuery = "INSERT INTO ReferralDetails VALUES ";
            
            // get RID that was just inserted into Referrals table
            for ($i = 0; $i < $numFriends; $i++) {
                $friendNum = "friend".$i;
                $uidFriend = $uidFriends->$friendNum;
                $getRIDquery = "SELECT rid FROM Referrals WHERE uid1 = $uid AND uid2 = $uidFriend AND date = \"$date\" AND lid = $lid AND comment = \"$comment\"";
                $result = mysql_query($getRIDquery);
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
                echo $addRefDetsQuery;
                mysql_query($addRefDetsQuery);
            }
        }
    }
}
