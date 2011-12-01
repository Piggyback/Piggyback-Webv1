<?php
class search_vendors_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
    }
    
    function search_vendors()
    {
        // load database
        $this->load->database();
        
//        // set up variables
//        $searchLocation = urlencode($this->input->post('searchLocation'));
//        $keyword = urlencode($this->input->post('searchText'));
//        define("MAPS_HOST", "https://maps.googleapis.com/maps/api");
//        define("KEY", "AIzaSyA4g2M3awvxLFMxKfTyM2rBwoWxfs_1Ljs");
//
//        // get latitude and longitude of search location using google maps API
//        $geocodeRequest = file_get_contents(MAPS_HOST."/geocode/json?address=".$searchLocation."&sensor=false");
//        $geocodeArray = json_decode($geocodeRequest);
//        $lat = $geocodeArray->results[0]->geometry->location->lat;
//        $long = $geocodeArray->results[0]->geometry->location->lng;
//        $location = $lat.",".$long;
//
//        // retrieve search results using google places API
//        $searchRequest = file_get_contents(MAPS_HOST."/place/search/json?sensor=false&radius=5000&types=bakery|bar|cafe|restaurant|night_club&key=".KEY."&location=".$location."&keyword=".$keyword);
//        $searchArray = json_decode($searchRequest);
//        
//        // return error string: ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED, or INVALID_REQUEST
//        if ($searchArray->status != 'OK') {
//            $retArray['searchResults'] = $searchArray->status;
//            return $retArray;
//        }
//        
//        // if there are results, use reference ID to get details and store details in vendorArray
//        else if ($searchArray->status == 'OK') {
//            $searchResults = $searchArray->results;
//            foreach ($searchResults as $vendor) {
//                $reference = $vendor->reference;
//                $vendorDetailRequest = file_get_contents(MAPS_HOST."/place/details/json?reference=".$reference."&sensor=false&key=".KEY);
//                $vendorArray[] = json_decode($vendorDetailRequest);
//            }
//            $retArray['srcLat'] = $lat;
//            $retArray['srcLng'] = $long;
//            $retArray['searchResults'] = $vendorArray;
//            return $retArray;
//        }
        return true;
    }
    
    function add_referral()
    {
        $query = $_GET["q"];
        $vid = $_GET["vid"];
        
        // add referrals to Referral table
        $this->load->database();
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
            $addReferralDetailQuery = "INSERT INTO ReferralDetails VALUES ($rid,\"$vid\",0,0)";
            echo $addReferralDetailQuery;
            mysql_query($addReferralDetailQuery);
        }
    }
}
?>
