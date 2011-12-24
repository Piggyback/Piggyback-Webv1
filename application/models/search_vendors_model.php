<?php
class search_vendors_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
        $this->load->database();
    }
    
    function search_vendors()
    {        
        // set up variables
        $searchLocation = urlencode($this->input->post('searchLocation'));
        $keyword = urlencode($this->input->post('searchText'));
        define("MAPS_HOST", "https://maps.googleapis.com/maps/api");
        define("KEY", "AIzaSyA4g2M3awvxLFMxKfTyM2rBwoWxfs_1Ljs");

        // get latitude and longitude of search location using google maps API
        $geocodeRequest = file_get_contents(MAPS_HOST."/geocode/json?address=".$searchLocation."&sensor=false");
        $geocodeArray = json_decode($geocodeRequest);
        
        // if no results for geocoding, return error message
        if ($geocodeArray->status != "OK"){
            $retArray['searchResults'] = array("error","locationError",$geocodeArray->status);
            echo json_encode($retArray);
            return;
//            return $retArray;
        }
        $lat = $geocodeArray->results[0]->geometry->location->lat;
        $long = $geocodeArray->results[0]->geometry->location->lng;
        $location = $lat.",".$long;

        // retrieve search results using google places API
        $searchRequest = file_get_contents(MAPS_HOST."/place/search/json?sensor=false&radius=5000&types=bakery|bar|cafe|restaurant|night_club&key=".KEY."&location=".$location."&keyword=".$keyword);
        $searchArray = json_decode($searchRequest);
        
        // return error string: ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED, or INVALID_REQUEST
        if ($searchArray->status != 'OK') {
            $retArray['searchResults'] = array("error","searchError",$searchArray->status);
            echo json_encode($retArray);
            return;
//            return $retArray;
        }
        
        // if there are results, use reference ID to get details and store details in vendorArray
        else if ($searchArray->status == 'OK') {
            $searchResults = $searchArray->results;
//            foreach ($searchResults as $vendor) {
            $vendor = $searchResults[0];    // TODO: REMOVE THIS -- only will return 1 result
                $reference = $vendor->reference;
                $vendorDetailRequest = file_get_contents(MAPS_HOST."/place/details/json?reference=".$reference."&sensor=false&key=".KEY);
                $vendorArray[] = json_decode($vendorDetailRequest);
                
    $vendor = $searchResults[1];    // TODO: REMOVE THIS -- only will return 1 result
                $reference = $vendor->reference;
                $vendorDetailRequest = file_get_contents(MAPS_HOST."/place/details/json?reference=".$reference."&sensor=false&key=".KEY);
                $vendorArray[] = json_decode($vendorDetailRequest);
//            }
            
//            echo json_encode($vendorArray);
            $retArray['srcLat'] = $lat;
            $retArray['srcLng'] = $long;
            $retArray['searchResults'] = $vendorArray;
            echo json_encode($retArray);
            return;
//            return $retArray;
        }
    }
    
    // return a list of people who are friends with the current user
    // the returned list is in json format so that javascript can read it as an array
    function get_friends_list() {
        // get friends of current user
        $currentUserData = $this->session->userdata('currentUserData');
        $currentUID = $currentUserData['uid'];
        $friendQuery = "SELECT uid, fbid, email, firstName, lastName
                        FROM Users
                        WHERE uid IN (SELECT uid2 FROM Friends WHERE uid1 = $currentUID
                                      UNION
                                      SELECT uid1 FROM Friends WHERE uid2 = $currentUID)";
        $friends = mysql_query($friendQuery);

        // create friend array, if there is an error with the query, return an empty friends list
        $friendArray = array();
        if ($friends) {
            while ($friend = mysql_fetch_row($friends)) {
                $friendArray[] = $friend;
            }
        }
        
        $retArray['allFriendsArray'] = $friendArray;
        $retArray['myUID'] = $currentUID;
        echo json_encode($retArray);
        return;
    }
   
    // add a referral to the database when a user refers a vendor to another user
    function add_referral()
    {
        // pull paremeters
        $id = $_POST["id"];
        $uid = $_POST["myUID"];
        $numFriends = $_POST["numFriends"];
        $uidFriends = json_decode($_POST["uidFriends"]);
        $date = $_POST["date"];
        $comment = $_POST["comment"];
        
        // add referrals to Referral table: one for each friend in uidFriends
        if ($numFriends > 0) {
            $q = "INSERT INTO Referrals VALUES ";
            $newFriends = array();
            
            for ($i = 0; $i < $numFriends; $i++) {
               $uidFriend = $uidFriends->$i;
               $flag = 0;
               
               // check if this referral has already been made (user1 to user2 for this vendor)
               $existsQuery = "SELECT rid FROM Referrals WHERE uid1 = $uid AND uid2 = $uidFriend AND lid = 0";
               $result = mysql_query($existsQuery);
               if (!$result) {
                   echo "Referral could not be processed1";
                   return;
               }
               
               while ($rid = mysql_fetch_row($result)) {
                   $existsQuery = "SELECT rid FROM ReferralDetails WHERE rid = $rid[0] AND vid = \"$id\"";
                   $res = mysql_query($existsQuery);
                   if (!$res) {
                       echo "Referral could not be processed2";
                       return;
                   }
                   
                   // if referral is found to exist already, mark flag
                   if (mysql_num_rows($res) > 0) {
                       $flag = 1;
                       break;
                   }
               }
               
               // if referral does not exist yet, then add it
               if ($flag == 0) {
                   $q = "$q (NULL, $uid, $uidFriend, \"$date\", 0, \"$comment\"),";
                   array_push($newFriends,$uidFriend);
               }
            }

            // delete last comma
            $q = substr($q,0,-1);
            
            if (count($newFriends) > 0) {
                $result = mysql_query($q);
                if (!$result) {
                    echo "Referral could not be processed3";
                    return;
                }
            }
            
            // get RID that was just inserted into Referrals table and build query for adding to referral details: one for each friend
            $addReferralDetailQuery = "INSERT INTO ReferralDetails VALUES ";
            for ($i = 0; $i < count($newFriends); $i++) {
                $uidFriend = $newFriends[$i];
                $getRIDquery = "SELECT rid FROM Referrals WHERE uid1 = $uid AND uid2 = $uidFriend AND date = \"$date\" AND lid = 0 AND comment = \"$comment\"";
                $result = mysql_query($getRIDquery);
                if (!$result) {
                    echo "Referral could not be processed4";
                    return;
                }
                $resultRow = mysql_fetch_row($result);
                $rid = $resultRow[0];
                $addReferralDetailQuery = "$addReferralDetailQuery ($rid,\"$id\",0,0),";
            }
            
            // delete last comma
            $addReferralDetailQuery = substr($addReferralDetailQuery,0,-1);
            
            if (count($newFriends) > 0) {
                $result = mysql_query($addReferralDetailQuery);
                if (!$result) {
                    echo "Referral could not be processed5";
                    return;
                }
            }
        }
        return false;
    }
    
    // add vendor to db: called when a vendor is added to a list or referred to a friend
    function add_vendor() {        
        $name = $_POST["name"];
        $reference = $_POST["reference"];
        $id = $_POST["id"];
        $lat = $_POST["lat"];
        $lng = $_POST["lng"];
        $phone = $_POST["phone"];
        $addr = $_POST["addr"];
        $addrNum = $_POST["addrNum"];
        $addrStreet = $_POST["addrStreet"];
        $addrCity = $_POST["addrCity"];
        $addrState = $_POST["addrState"];
        $addrCountry = $_POST["addrCountry"];
        $addrZip = $_POST["addrZip"];
        $vicinity = $_POST["vicinity"];
        $website = $_POST["website"];
        $icon = $_POST["icon"];
        $rating = $_POST["rating"];
        
        // find if vendor exists in db yet
        $existingVendorQuery = "SELECT id 
            FROM Vendors 
            WHERE id = \"$id\"";
        $existingVendorResult = mysql_query($existingVendorQuery);
        if (!$existingVendorResult) {
            echo "Could not add vendor";
            return;
        }
        
        $count = mysql_num_rows($existingVendorResult);
        
        // add to vendor db if it does not exist yet
        if ($count == 0) {
           $addVendorQuery = "INSERT INTO Vendors 
                           VALUES (\"$name\",\"$reference\",\"$id\",$lat,$lng,\"$phone\",\"$addr\",\"$addrNum\",\"$addrStreet\",\"$addrCity\",\"$addrState\",\"$addrCountry\",\"$addrZip\",\"$vicinity\",\"$website\",\"$icon\",$rating)";
            $result = mysql_query($addVendorQuery);
            if (!$result) {
                echo "Could not add vendor";
                return;
            }
        }
        
        // return false if everything worked correctly
        echo false;
    }

}
?>
