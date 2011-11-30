<?php

/*
 * @andyjiang
 * 
 * this model will manage all list interactions with MySQL
 * 
 * tables:
 *  UserLists
 *  Lists
 * 
 * functions:
 *  add new list
 *  add vendor to list
 *  remove list
 *  remove vendor from list
 *  get vendor info from list
 * 
 */

class Manage_List_Model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /*
     * create_new_list
     * 
     * inputs: uid, array of vid, list name
     * action: creates new row in UserLists table
     * return: void
     * 
     */
    public function create_new_list($fieldData) {
        // uid should be called from session data
        // fieldData includes 'uid', 'listName', 'box' (array of vid's)
        $uid = $fieldData['uid'];
        $listName = $fieldData['listName'];
        $date = time();
        
        // create new UserLists row
        $addUserListQuery = "INSERT INTO UserLists(uid, name, date) VALUES ($uid, \"$listName\", $date)";
        mysql_query($addUserListQuery) or die("My sql error: " . mysql_error());
        
        // get the uniquely auto-incremenetd LID from UserLists
        $lid = mysql_insert_id();
        
        // create individual list that contains vendor information and comments
        // TODO: manipulate box array of vid's without using checkboxes @andyjiang
        //       to put one vid in box if its just one vendor
        foreach($fieldData['box'] as $checkedItem)
        {
            $vid = $checkedItem;    // vid is now an string, varchar
            
            $comment = "test comment here";
            $addListQuery = "INSERT INTO Lists(lid, vid, date, comment)
                             VALUES ($lid, \"$vid\", $date, \"$comment\")";
            mysql_query($addListQuery) or die("My sql error: " . mysql_error());
        }
        
    }
    
    /*
     * add_vendor_to_list
     * 
     * inputs: uid, vid, lid (target list)
     * action: add one row to Lists table, simulating an additional item in UserList
     * return: void
     * 
     */
    public function add_vendor_to_list($fieldData) {
        // uid should be called from session data
        $uid = $fieldData['uid'];
        $lid = $fieldData['lid'];
        $vid = $fieldData['vid'];
        
        // TODO: implement comments into Lists rows @andyjiang
        $comment = "test comment here";
        
        // create new Lista row
        $addListQuery = "INSERT INTO Lists(lid, vid, date, comment)
                         VALUES ($lid, \"$vid\", $date, \"$comment\")";
        mysql_query($addListQuery) or die("My sql error: " . mysql_error());
    }
    
    /*
     * remove_list
     * 
     * inputs: lid (lid is auto incremented unique key)
     * action: remove row in UserLists that corresponds to lid
     * return: void
     * 
     */
    public function remove_list($fieldData) {
        $lid = $fieldData['lid'];
        
        $deleteQuery = "DELETE FROM UserLists
                        WHERE lid = $lid";
        mysql_query($deleteQuery);
    }
    
    /*
     * remove_vendor_from_list
     * 
     * inputs: vid, lid (target list)
     * action: remove row in lid Lists that corresponds to vid
     * return: void
     * 
     */
    public function remove_vendor_from_list($fieldData) {
        $lid = $fieldData['lid'];
        $vid = $fieldData['vid'];
        
        $deleteQuery = "DELETE FROM Lists
                        WHERE vid = $vid
                        AND lid = $lid";
        mysql_query($deleteQuery);
    }
    
    /*
     * get_vendor_info_from_list
     * 
     * inputs: lid
     * action: joins Vendors and Lists, returns rows where vid is the same
     * return: result() of all Vendors rows corresponding to the vid's in the given lid
     * 
     */
    public function get_vendor_info_from_list($listId)
    {
        // given list ID, return vendor names
        
        $this->db->select('*');
        $this->db->from('Vendors');
        $this->db->join('Lists', 'Lists.vid = Vendors.id', 'left');
        $this->db->where('lid', intval($listId));
       
        $vendorNameList = $this->db->get()->result();
        
        return $vendorNameList;
    }
    
    
    
}


?>
