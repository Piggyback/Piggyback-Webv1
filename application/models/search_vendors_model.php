<?php
class search_vendors_model extends CI_Model {
    
    function __construct() {
        // Call the Model consutrctor
        parent::__construct();
        $this->load->database();
    }
    
    function search_vendors($searchLoc,$searchText) {
        $searchLocation = urlencode($searchLoc);
        $searchQuery = urlencode($searchText);
        
        // get latitude and longitude of search location using google maps API
        $geocodeRequest = file_get_contents(MAPS_HOST."/geocode/json?address=".$searchLocation."&sensor=false");
        $geocodeArray = json_decode($geocodeRequest);
        
        // if no results for geocoding, return error message
        if ($geocodeArray->status != "OK"){
            $searchResults = array("error","locationError",$geocodeArray->status);
            return $searchResults;
        }
        
        $lat = $geocodeArray->results[0]->geometry->location->lat;
        $long = $geocodeArray->results[0]->geometry->location->lng;
        $locCoordinates = "$lat,$long";
        
        $radius = "100000";
        $intent = "checkin";
        $limit = "20";
        $date = date('Ymd');
        $clientID = "LQYMHEIG05TK2HIQJGJ3MUGDNBAW1OKJKM4SSUFNYGSQMQIZ";
        $clientSecret = "AXDTUGX5AA1DXDI2HUWVSODSFGKIK2RQYYGUWSUBDC0R5OLX";
        
        $searchRequest = file_get_contents("https://api.foursquare.com/v2/venues/search?query=$searchQuery&ll=$locCoordinates&radius=$radius&intent=$intent&limit=$limit&client_id=$clientID&client_secret=$clientSecret&v=$date");
        $searchArray = json_decode($searchRequest);
        
        // return array of returned vendors
        if ($searchArray->meta->code == 200) {
            $searchResults = $searchArray->response->venues;
            return $searchResults;
        } else {
            $searchResults = array("error","searchError",$searchArray->meta->code);
            return $searchResults;
        }
    }
    
// search using google places api
//    function search_vendors($searchLoc,$searchText) {        
//        // set up variables
//        $searchLocation = urlencode($searchLoc);
//        $keyword = urlencode($searchText);
//
//        // get latitude and longitude of search location using google maps API
//        $geocodeRequest = file_get_contents(MAPS_HOST."/geocode/json?address=".$searchLocation."&sensor=false");
//        $geocodeArray = json_decode($geocodeRequest);
//        
//        // if no results for geocoding, return error message
//        if ($geocodeArray->status != "OK"){
//            $retArray['searchResults'] = array("error","locationError",$geocodeArray->status);
//            return json_encode($retArray);
//        }
//        $lat = $geocodeArray->results[0]->geometry->location->lat;
//        $long = $geocodeArray->results[0]->geometry->location->lng;
//        $location = $lat.",".$long;
//
//        $radius = "50000";
//        
//        // retrieve search results using google places API
//        $types = "&types=bakery|bar|cafe|restaurant|night_club";
//        
//        $searchRequest = file_get_contents(MAPS_HOST."/place/search/json?sensor=false&radius=".$radius.$types."&key=".KEY."&location=".$location."&keyword=".$keyword);
//        $searchArray = json_decode($searchRequest);
//        
//        // return error string: ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED, or INVALID_REQUEST
//        if ($searchArray->status != 'OK') {
//            $retArray['searchResults'] = array("error","searchError",$searchArray->status);
//            return json_encode($retArray);
//        }
//        
//        // if there are results, use reference ID to get details and store details in vendorArray
//        else if ($searchArray->status == 'OK') {
//            $searchResults = $searchArray->results;
//            
//            $retArray['srcLat'] = $lat;
//            $retArray['srcLng'] = $long;
//            $retArray['searchResults'] = $searchResults;
//            return json_encode($retArray);
//        }
//    }
    
    function get_search_details($id) {
        $date = date('Ymd');
        $clientID = "LQYMHEIG05TK2HIQJGJ3MUGDNBAW1OKJKM4SSUFNYGSQMQIZ";
        $clientSecret = "AXDTUGX5AA1DXDI2HUWVSODSFGKIK2RQYYGUWSUBDC0R5OLX";
        
        $vendorDetail = json_decode(file_get_contents("https://api.foursquare.com/v2/venues/$id?client_id=$clientID&client_secret=$clientSecret&v=$date"));
        
//        // get human readable address if only a lat/lng is provided by foursquare
//        $vendorLocationDetails = $vendorDetail->response->venue->location;
//        
//        if (!(key_exists('address',$vendorLocationDetails)) || 
//            !(key_exists('city',$vendorLocationDetails) || 
//            !(key_exists('state',$vendorLocationDetails) || 
//            !(key_exists('country',$vendorLocationDetails) || 
//            !(key_exists('postalCode',$vendorLocationDetails)))))) {
//            
//            // no complete address, use google api to get human readable address from lat/lng
//            $latlng = $vendorLocationDetails->lat . "," . $vendorLocationDetails->lng;
//            $addrResults = json_decode(file_get_contents(MAPS_HOST."/geocode/json?latlng=".$latlng."&sensor=false"));
//            
//            // if no street num, then use google formatted address
//            if (!(key_exists('address',$vendorLocationDetails))) {
//                $formattedAddrComponents = explode(",",$addrResults->results[0]->formatted_address);
//                $vendorLocationDetails->address = $formattedAddrComponents[0];
//            } 
//            
//
//            foreach ($addrResults->results[0]->address_components as $addrComponent) {
//                switch ($addrComponent->types[0]) {
//                    case "locality":
//                        if (!(key_exists('city',$vendorLocationDetails))) {
//                            $vendorLocationDetails->city = $addrComponent->short_name;
//                        }
//                        break;
//                    case "administrative_area_level_1":
//                        if (!(key_exists('state',$vendorLocationDetails))) {
//                            $vendorLocationDetails->state = $addrComponent->short_name;
//                        }
//                        break;
//                    case "country":
//                        if (!(key_exists('country',$vendorLocationDetails))) {
//                            $vendorLocationDetails->country = $addrComponent->short_name;
//                        }
//                        break;    
//                    case "postal_code":
//                        if (!(key_exists('postalCode',$vendorLocationDetails))) {
//                            $vendorLocationDetails->postalCode = $addrComponent->short_name;
//                        }
//                        break;
//                }
//            }
//        } 

        // concatenate postal code to 5 digits
        if (key_exists('postalCode',$vendorDetail->response->venue->location)) {
            $vendorDetail->response->venue->location->postalCode = substr($vendorDetail->response->venue->location->postalCode,0,5);
        }
        
        return $vendorDetail;
    }
    
// get search details for one vendor using google places api
//    function get_search_details($reference) {
//        $vendorDetail = file_get_contents(MAPS_HOST."/place/details/json?reference=".$reference."&sensor=false&key=".KEY);
//        return $vendorDetail;
//    }
    
    // return a list of people who are friends with the current user
    // the returned list is in json format so that javascript can read it as an array
    function get_friends() {
        // get friends of current user
        $currentUserData = $this->session->userdata('currentUserData');
        $currentUID = $currentUserData['uid'];
        $friendQuery = "SELECT uid, fbid, email, firstName, lastName
                        FROM Users
                        WHERE uid IN (SELECT uid2 FROM Friends WHERE uid1 = ?
                                      UNION
                                      SELECT uid1 FROM Friends WHERE uid2 = ?)";
        $friends = $this->db->query($friendQuery,array($currentUID,$currentUID))->result();
        
        $retArray['allFriendsArray'] = $friends;
        $retArray['myUID'] = $currentUID;
        return json_encode($retArray);
    }
     
//    
//    // add vendor to db: called when a vendor is added to a list or referred to a friend using google api
////    function add_vendor($name,$reference,$id,$lat,$lng,$phone,$addr,$addrNum,$addrStreet,$addrCity,$addrState,$addrCountry,$addrZip,$vicinity,$website,$icon,$rating) {   
////        // find if vendor exists in db yet        
////        $existingVendorQuery = "SELECT id FROM Vendors WHERE id = ?";
////        $existingVendorResult = $this->db->query($existingVendorQuery,array($id));
////        
////        // add to vendor db if it does not exist yet
////        if ($existingVendorResult->num_rows() == 0) {
////           $addVendorQuery = "INSERT INTO Vendors 
////                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
////           $this->db->query($addVendorQuery,array($name,$reference,$id,$lat,$lng,$phone,$addr,$addrNum,$addrStreet,$addrCity,$addrState,$addrCountry,$addrZip,$vicinity,$website,$icon,$rating));
////        }
////    }
//    
//    // add vendor to db for foursquare api
//    function add_vendor($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
//            $addrCountry, $addrZip, $website, $tags, $categories, $photos) {
//
//        // find if vendor exists in db yet        
//        $existingVendorQuery = "SELECT id FROM VendorsFoursquare WHERE id = ?";
//        $existingVendorResult = $this->db->query($existingVendorQuery,array($id));
//
//        // add to vendor db if it does not exist yet
//        if ($existingVendorResult->num_rows() == 0) {
//            
//            // add vendor info to vendor table
//           $addVendorQuery = "INSERT INTO VendorsFoursquare
//                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
//           $this->db->query($addVendorQuery,array($name,$id,$lat,$lng,$phone,$addr,$addrCrossStreet,$addrCity,$addrState,$addrCountry,$addrZip,$website));
//        
//           // add tags to tag table
//           if (count($tags) > 0) {
//               $addTagsQuery = "INSERT INTO VendorsFoursquareTags VALUES ";
//               foreach ($tags as $tag) {
//                   $addTagsQuery = "$addTagsQuery (\"$id\",\"$tag\"),";
//               }
//               $addTagsQuery = substr($addTagsQuery,0,-1);
//               $this->db->query($addTagsQuery);
//           }
//           
//           // add categories to category table
//           if (count($categories) > 0) {
//               $addCategoriesQuery = "INSERT INTO VendorsFoursquareCategories VALUES ";
//               foreach ($categories as $category) {
//                   $cid = $category['cid'];
//                   $categoryName = $category['categoryName'];
//                   $addCategoriesQuery = "$addCategoriesQuery (\"$id\",\"$cid\",\"$categoryName\"),";
//               }
//               $addCategoriesQuery = substr($addCategoriesQuery,0,-1);
//               $this->db->query($addCategoriesQuery);
//           }
//           
//           // add photos to photo table
//           if (count($photos) > 0) {
//               $addPhotosQuery = "INSERT INTO VendorsFoursquarePhotos VALUES ";
//               foreach ($photos as $photo) {
//                   $pid = $photo['pid'];
//                   $photoURL = $photo['photoURL'];
//                   $addPhotosQuery = "$addPhotosQuery (\"$id\",\"$pid\",\"$photoURL\"),";
//               }
//               $addPhotosQuery = substr($addPhotosQuery,0,-1);
//               $this->db->query($addPhotosQuery);
//           }
//        }
//    }

}
?>
