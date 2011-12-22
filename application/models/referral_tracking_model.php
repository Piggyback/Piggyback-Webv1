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
    
        public function get_referral_tracking($data)
    {
        // should get uidRecipient from session
//        $myUID = $data['uid'];
//        $rowStart = $data['rowStart'];
//        $rowsRequested = $data['rowsRequested'];
            $myUID = 7;
            $rowStart = 0;
            $rowsRequested = 3;
        
        // create query to find rid's of your referrals with most recent activity
        $q = "(select rid, date 
              from Likes 
              where rid in (select rid from Referrals where uid1=$myUID)) 
              union 
              (select rid, date 
              from Comments 
              where rid in (select rid from Referrals where uid1=$myUID)) 
              order by date desc;";
        
        $res = mysql_query($q);
        
        // create an ordered list of these rid's to order the results of the details query below
        $updatedRIDs = array();
        $updatedRIDsStr = "(";
        while ($row = mysql_fetch_row($res)) {
            if (!in_array($row[0],$updatedRIDs)) {
                array_push($updatedRIDs, $row[0]);
                $updatedRIDsStr = $updatedRIDsStr . $row[0] . ",";
            }
        }
        
        $updatedRIDsStr[strlen($updatedRIDsStr)-1] = ")";

        $detailsQ = "SELECT *, UserLists.name AS UserListsName, Vendors.name AS VendorsName, Referrals.comment AS ReferralsComment, Referrals.date AS refDate
                    FROM Referrals
                    LEFT JOIN Users 
                    ON Users.uid = Referrals.uid2
                    LEFT JOIN ReferralDetails
                    ON ReferralDetails.rid = Referrals.rid
                    LEFT JOIN UserLists 
                    ON UserLists.lid = Referrals.lid
                    LEFT JOIN Vendors
                    ON Vendors.id = ReferralDetails.vid
                    WHERE Referrals.rid IN $updatedRIDsStr
                    ORDER BY CASE Referrals.rid";
        
        for ($i = 0; $i < count($updatedRIDs); $i++) {
            $detailsQ = $detailsQ . " WHEN " . $updatedRIDs[$i] . " THEN " . ($i+1) . "\n";
        }
        $detailsQ = $detailsQ . "END";
        
        $result = $this->db->query($detailsQ)->result();
        
        // result needs to be formatted to include an array of likes and comments
        foreach($result as $row)
        {
            $rid = $row->rid;
            
            // retrieve a 'Likes' array of uid's
            $this->db->select('*');
            $this->db->from('Likes');
            $this->db->where('rid', $rid);
            $LikesList = $this->db->get()->result();
          
            $row->LikesList = array("LikesList" => $LikesList);
            
            // retrieve a 'Comments' with uid's
            $this->db->select('*');
            $this->db->from('Comments');
            $this->db->join('Users', 'Users.uid = Comments.uid', 'left');
            $this->db->order_by('date', 'asc');
            $this->db->where('rid', $rid);
            $CommentsList = $this->db->get()->result();
            
            $row->CommentsList = array("CommentsList" => $CommentsList);
            
            // add whether the user has Liked the status or not
            $this->db->from('Likes');
            $this->db->where('rid', $rid);
            $this->db->where('uid', $myUID);
            
            if ($this->db->count_all_results() == 0)
            {
                $row->alreadyLiked = "0";
            } else {
                // user has already liked it
                $row->alreadyLiked = "1";
            }
        }
        return $result;
    }
    
 }
    
?>
