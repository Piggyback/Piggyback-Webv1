<?php

/* kimhsiao */

 class referral_tracking_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
 
    public function get_likes() {
        $uid = $_POST["uid"];
        
        // get likes that are linked to your referrals
        $q = "SELECT Referrals.rid, Likes.uid, Likes.date 
              FROM Likes 
              LEFT JOIN Referrals 
              ON Likes.rid = Referrals.rid 
              WHERE Referrals.uid1 = $uid";
        
        $result = mysql_query($q);
        
        // find uid's of people who liked your referrals
        $people = array();
        $peopleQ = "SELECT uid, fbid, email, firstName, lastName
                    FROM Users
                    WHERE ";
        while($row = mysql_fetch_row($result)) {
            if (!in_array($row[1],$people)) {
                array_push($people, $row[1]);
                $peopleQ = "$peopleQ uid = $row[1] OR ";
            }
        }
        
        $peopleQ = substr($peopleQ,0,-3);
        
        // get information about people who liked your referrals
        $peopleResult = mysql_query($peopleQ);
        
        $peopleArray = array();
        while($row = mysql_fetch_row($peopleResult)) {
            $temp = array('uid'=>$row[0],'fbid'=>$row[1],'email'=>$row[2],'firstName'=>$row[3],'lastName'=>$row[4]);
            array_push($peopleArray,$temp);
        }
                
        $likeArray = array();
        $likeArray['numLikes'] = mysql_num_rows($result);
        $likeArray['numPeople'] = count($people);
        $likeArray['people'] = $peopleArray;
        echo json_encode($likeArray);
    }
    
    public function get_comments() {
        $uid = $_POST["uid"];
        
        $q = "SELECT Referrals.rid, Comments.uid, Comments.cid, Comments.comment, Comments.date 
              FROM Comments 
              LEFT JOIN Referrals 
              ON Comments.rid = Referrals.rid 
              WHERE Referrals.uid1 = $uid";
        
        $result = mysql_query($q);
        
        $count = mysql_num_rows($result);
        echo $count;
    }
 }
    
?>
