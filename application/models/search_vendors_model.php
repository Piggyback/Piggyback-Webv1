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
        $limit = "500";
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
        
        $vendorDetail = file_get_contents("https://api.foursquare.com/v2/venues/$id?client_id=$clientID&client_secret=$clientSecret&v=$date");
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
   
    // add a referral to the database when a user refers a vendor to another user
    function add_referral($id,$uid,$numFriends,$uidFriends,$comment)
    {
        $this->db->trans_start();
        
        $result = $this->refer($id,$uid,$numFriends,$uidFriends,$comment);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return "Vendor referral could not be processed";
        }
        else {
            return $result;
        }
    }
    
    function refer($id,$uid,$numFriends,$uidFriends,$comment) {
        $date = date('Y-m-d H:i:s');

        // add referrals to Referral table: one for each friend in uidFriends
        if ($numFriends > 0) {

            $q = "INSERT INTO Referrals VALUES ";
            $newFriends = array();
            $params = array();
            
            for ($i = 0; $i < $numFriends; $i++) {
               $uidFriend = $uidFriends->$i;
               $flag = 0;
               
               // check if this referral has already been made (user1 to user2 for this vendor)
               $existsQuery = "SELECT rid FROM Referrals WHERE uid1 = ? AND uid2 = ? AND lid = 0 AND deletedUID2 != 1";
               $result = $this->db->query($existsQuery,array($uid,$uidFriend));
               
               if ($result->num_rows() > 0) {
                   foreach ($result->result() as $row) {
                        $existsQuery = "SELECT rid FROM ReferralDetails WHERE rid = ? AND vid = ?";
                        $res = $this->db->query($existsQuery,array($row->rid,$id));
                        if ($res->num_rows() > 0) {
                            $flag = 1;
                            break;
                        }
                   }
               }

               // if referral does not exist yet, then add it
               if ($flag == 0) {
                   $q = "$q (NULL, ?, ?, ?, 0, ?, 0, 0),";
                   array_push($newFriends,$uidFriend);
                   array_push($params,$uid,$uidFriend,$date,$comment);
               }
            }

            // delete last comma
            $q = substr($q,0,-1);
            
            if (count($newFriends) > 0) {
                $result = $this->db->query($q,$params);
                if (!$result) {
                    return "Vendor referral could not be processed";
                }
            }
            
            // get RID that was just inserted into Referrals table and build query for adding to referral details: one for each friend
            $addReferralDetailQuery = "INSERT INTO ReferralDetails VALUES ";
            $params = array();
            
            for ($i = 0; $i < count($newFriends); $i++) {
                $uidFriend = $newFriends[$i];
                $getRIDquery = "SELECT rid FROM Referrals WHERE uid1 = ? AND uid2 = ? AND date = ? AND lid = 0 AND comment = ?";

                $result = $this->db->query($getRIDquery,array($uid,$uidFriend,$date,$comment));
                if ($result->num_rows() > 0) {
                    $rid = $result->row()->rid;
                }
                $addReferralDetailQuery = "$addReferralDetailQuery (?,?,0,0),";
                array_push($params,$rid,$id);
            }
            
            // delete last comma
            $addReferralDetailQuery = substr($addReferralDetailQuery,0,-1);
            
            if (count($newFriends) > 0) {
                $result = $this->db->query($addReferralDetailQuery,$params);
            }
        }
        return false;
    }
    
    // add vendor to db: called when a vendor is added to a list or referred to a friend using google api
//    function add_vendor($name,$reference,$id,$lat,$lng,$phone,$addr,$addrNum,$addrStreet,$addrCity,$addrState,$addrCountry,$addrZip,$vicinity,$website,$icon,$rating) {   
//        // find if vendor exists in db yet        
//        $existingVendorQuery = "SELECT id FROM Vendors WHERE id = ?";
//        $existingVendorResult = $this->db->query($existingVendorQuery,array($id));
//        
//        // add to vendor db if it does not exist yet
//        if ($existingVendorResult->num_rows() == 0) {
//           $addVendorQuery = "INSERT INTO Vendors 
//                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
//           $this->db->query($addVendorQuery,array($name,$reference,$id,$lat,$lng,$phone,$addr,$addrNum,$addrStreet,$addrCity,$addrState,$addrCountry,$addrZip,$vicinity,$website,$icon,$rating));
//        }
//    }
    
    // add vendor to db for foursquare api
    function add_vendor($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, 
            $addrCountry, $addrZip, $website, $tags, $categories, $photos) {

        // find if vendor exists in db yet        
        $existingVendorQuery = "SELECT id FROM VendorsFoursquare WHERE id = ?";
        $existingVendorResult = $this->db->query($existingVendorQuery,array($id));

        // add to vendor db if it does not exist yet
        if ($existingVendorResult->num_rows() == 0) {
            
            // add vendor info to vendor table
           $addVendorQuery = "INSERT INTO VendorsFoursquare
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
           $this->db->query($addVendorQuery,array($name,$id,$lat,$lng,$phone,$addr,$addrCrossStreet,$addrCity,$addrState,$addrCountry,$addrZip,$website));
        
           // add tags to tag table
           if (count($tags) > 0) {
               $addTagsQuery = "INSERT INTO VendorsFoursquareTags VALUES ";
               foreach ($tags as $tag) {
                   $addTagsQuery = "$addTagsQuery (\"$id\",\"$tag\"),";
               }
               $addTagsQuery = substr($addTagsQuery,0,-1);
               $this->db->query($addTagsQuery);
           }
           
           // add categories to category table
           if (count($categories) > 0) {
               $addCategoriesQuery = "INSERT INTO VendorsFoursquareCategories VALUES ";
               foreach ($categories as $category) {
                   $cid = $category['cid'];
                   $categoryName = $category['categoryName'];
                   $addCategoriesQuery = "$addCategoriesQuery (\"$id\",\"$cid\",\"$categoryName\"),";
               }
               $addCategoriesQuery = substr($addCategoriesQuery,0,-1);
               $this->db->query($addCategoriesQuery);
           }
           
           // add photos to photo table
           if (count($photos) > 0) {
               $addPhotosQuery = "INSERT INTO VendorsFoursquarePhotos VALUES ";
               foreach ($photos as $photo) {
                   $pid = $photo['pid'];
                   $photoURL = $photo['photoURL'];
                   $addPhotosQuery = "$addPhotosQuery (\"$id\",\"$pid\",\"$photoURL\"),";
               }
               $addPhotosQuery = substr($addPhotosQuery,0,-1);
               $this->db->query($addPhotosQuery);
           }
        }
    }
    
    function refer_from_search($id,$uid,$numFriends,$uidFriends,$comment,$name,$lat,$lng,$phone,$addr,$addrCrossStreet,$addrCity,$addrState,$addrCountry,$addrZip,$website,$tags,$categories,$photos) {
        $this->db->trans_start();
        
        $this->add_vendor($name,$id,$lat,$lng,$phone,$addr,$addrCrossStreet,$addrCity,$addrState,$addrCountry,$addrZip,$website,$tags,$categories,$photos);
        $result = $this->refer($id,$uid,$numFriends,$uidFriends,$comment);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return "Vendor referral could not be processed";
        }
        else {
            return $result;
        }
    }

}
?>
