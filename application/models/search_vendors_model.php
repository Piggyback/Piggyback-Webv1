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
        
        // set up variables
        $searchLocation = urlencode($this->input->post('searchLocation'));
        $keyword = urlencode($this->input->post('searchText'));
        define("MAPS_HOST", "https://maps.googleapis.com/maps/api");
        define("KEY", "AIzaSyA4g2M3awvxLFMxKfTyM2rBwoWxfs_1Ljs");

        // get latitude and longitude of search location using google maps API
        $geocodeRequest = file_get_contents(MAPS_HOST."/geocode/json?address=".$searchLocation."&sensor=false");
        $geocodeArray = json_decode($geocodeRequest);
        $lat = $geocodeArray->results[0]->geometry->location->lat;
        $long = $geocodeArray->results[0]->geometry->location->lng;
        $location = $lat.",".$long;

        // retrieve search results using google places API
        $searchRequest = file_get_contents(MAPS_HOST."/place/search/json?sensor=false&radius=5000&types=bakery|bar|cafe|restaurant|night_club&key=".KEY."&location=".$location."&keyword=".$keyword);
        $searchArray = json_decode($searchRequest);
        
        // return error string: ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED, or INVALID_REQUEST
        if ($searchArray->status != 'OK') {
            $retArray['searchResults'] = $searchArray->status;
            return $retArray;
        }
        
        // if there are results, use reference ID to get details and store details in vendorArray
        else if ($searchArray->status == 'OK') {
            $searchResults = $searchArray->results;
            foreach ($searchResults as $vendor) {
                $reference = $vendor->reference;
                $vendorDetailRequest = file_get_contents(MAPS_HOST."/place/details/json?reference=".$reference."&sensor=false&key=".KEY);
                $vendorArray[] = json_decode($vendorDetailRequest);
            }
            $retArray['srcLat'] = $lat;
            $retArray['srcLng'] = $long;
            $retArray['searchResults'] = $vendorArray;
            return $retArray;
        }
    }
}
?>
