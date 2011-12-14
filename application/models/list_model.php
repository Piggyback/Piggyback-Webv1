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

    function refer_list() {
        $lid = $_POST["lid"];
        $date = $_POST["date"];
        $comment = $_POST["comment"];
    }
}
