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

        // create friend name list in a string that javascript will understand
        $friendTags = "[";
        while ($friend = mysql_fetch_row($friends)) {
            $friendTags = $friendTags . "\"$friend[3] $friend[4]\",";
            $friendArray[] = $friend;
        }
        $friendTags[strlen($friendTags)-1] = "]";
        
        $retArray['friendTags'] = $friendTags;
        $retArray['allFriendsArray'] = $friendArray;
        $retArray['myUID'] = $currentUID;
        echo json_encode($retArray);
        return;
    }
   
    // add a referral to the database when a user refers a vendor to another user
    function add_referral()
    {
        // pull paremeters
        $query = $_POST["q"];
        $id = $_POST["id"];
        
        // add referrals to Referral table
        $result = mysql_query($query);

        // parse string to get elements of query necessary to find rid
        $q2 = str_replace("INSERT INTO Referrals VALUES ","",$query);
        
        // get rid of initial parenthesis
        $queryElements = explode("),(NULL,",$q2);
        $queryElements[0] = str_replace("(NULL,","",$queryElements[0]);
        
        // get rid of final parenthesis
        $numRows = count($queryElements);
        $queryElements[$numRows-1] = substr($queryElements[$numRows-1],0,-1);
        
        foreach ($queryElements as $row) {
            // separate string into uid1, uid2, date, lid, comment
            $rowElements = explode(",",$row);
            $uid1 = $rowElements[0];
            $uid2 = $rowElements[1];
            $date = $rowElements[2];
            $lid = $rowElements[3];
            $comment = $rowElements[4];
            
            // get RID that was just inserted into Referrals table
            $getRIDquery = "SELECT rid FROM Referrals WHERE uid1 = $uid1 AND uid2 = $uid2 AND date = $date AND lid = $lid AND comment = $comment";
            $result = mysql_query($getRIDquery);
            $resultRow = mysql_fetch_row($result);
            $rid = $resultRow[0];
            
            // add new row to ReferralDetails table using this rid and the vid
            $addReferralDetailQuery = "INSERT INTO ReferralDetails VALUES ($rid,\"$id\",0,0)";
            echo $addReferralDetailQuery;
            mysql_query($addReferralDetailQuery);
        }
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
        
        // add vendor to vendor database if it does not exist yet
        $existingVendorQuery = "SELECT id 
            FROM Vendors 
            WHERE id = \"$id\"";
        $existingVendorResult = mysql_query($existingVendorQuery);
        $count = mysql_num_rows($existingVendorResult);
        if ($count == 0) {
           $addVendorQuery = "INSERT INTO Vendors 
                           VALUES (\"$name\",\"$reference\",\"$id\",$lat,$lng,\"$phone\",\"$addr\",\"$addrNum\",\"$addrStreet\",\"$addrCity\",\"$addrState\",\"$addrCountry\",\"$addrZip\",\"$vicinity\",\"$website\",\"$icon\",$rating)";
            echo "\n\nquery: $addVendorQuery";
            mysql_query($addVendorQuery);
        }
    }

}
?>
