<?php
class Test_Model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
        $this->load->database();
    }
    
    
    function transfer_google_vendor_table_to_foursquare_api() {
        $getGoogleVendorData = "SELECT name,lat,lng FROM Vendors";
        $results = $this->db->query($getGoogleVendorData)->result();
        
        $radius = "1000";
        $intent = "match";
        $limit = "1";
        $date = date('Ymd');
        $clientID = "LQYMHEIG05TK2HIQJGJ3MUGDNBAW1OKJKM4SSUFNYGSQMQIZ";
        $clientSecret = "AXDTUGX5AA1DXDI2HUWVSODSFGKIK2RQYYGUWSUBDC0R5OLX";

        foreach($results as $vendor) {
            $searchQuery= urlencode($vendor->name);
            $locCoordinates = "$vendor->lat,$vendor->lng";

            $venueMatch = json_decode(file_get_contents("https://api.foursquare.com/v2/venues/search?query=$searchQuery&ll=$locCoordinates&radius=$radius&intent=$intent&limit=$limit&client_id=$clientID&client_secret=$clientSecret&v=$date"));
             if (count($venueMatch->response->venues) > 0) {
                 if ($venueMatch->meta->code == 200) {
                     // get details for the matching venue
                    $venueID = $venueMatch->response->venues[0]->id;
                    $venueDetail = json_decode(file_get_contents("https://api.foursquare.com/v2/venues/$venueID?client_id=$clientID&client_secret=$clientSecret&v=$date"));
                    if(array_key_exists("name",$venueDetail->response->venue)) {
                        $name = $venueDetail->response->venue->name;
                    } else {
                        $name = "";
                    }

                    if(array_key_exists("id",$venueDetail->response->venue)) {
                        $id = $venueDetail->response->venue->id;
                    } else {
                        $id = "";
                    }

                    if(array_key_exists("lat",$venueDetail->response->venue->location)) {
                        $lat = $venueDetail->response->venue->location->lat;
                    } else {
                        $lat = 0;
                    }

                    if(array_key_exists("lng",$venueDetail->response->venue->location)) {
                        $lng = $venueDetail->response->venue->location->lng;
                    } else {
                        $lng = 0;
                    }

                    if(array_key_exists("formattedPhone",$venueDetail->response->venue->contact)) {
                        $phone = $venueDetail->response->venue->contact->formattedPhone;
                    } else {
                        $phone = "";
                    }

                    if(array_key_exists("address",$venueDetail->response->venue->location)) {
                        $addr = $venueDetail->response->venue->location->address;
                    } else {
                        $addr = "";
                    }

                    if(array_key_exists("crossStreet",$venueDetail->response->venue->location)) {
                        $addrCrossStreet = $venueDetail->response->venue->location->crossStreet;
                    } else {
                        $addrCrossStreet = "";
                    }

                    if(array_key_exists("city",$venueDetail->response->venue->location)) {
                        $addrCity = $venueDetail->response->venue->location->city;
                    } else {
                        $addrCity = "";
                    }

                    if(array_key_exists("state",$venueDetail->response->venue->location)) {
                        $addrState = $venueDetail->response->venue->location->state;
                    } else {
                        $addrState = "";
                    }

                    if(array_key_exists("country",$venueDetail->response->venue->location)) {
                        $addrCountry = $venueDetail->response->venue->location->country;
                    } else {
                        $addrCountry = "";
                    }

                    if(array_key_exists("postalCode",$venueDetail->response->venue->location)) {
                        $addrZip = $venueDetail->response->venue->location->postalCode;
                    } else {
                        $addrZip = "";
                    }

                    if(array_key_exists("url",$venueDetail->response->venue)) {
                        $website = $venueDetail->response->venue->url;
                    } else {
                        $website = "";
                    }

                    if(array_key_exists("tags",$venueDetail->response->venue)) {
                        $tags = $venueDetail->response->venue->tags;
                    } else {
                        $tags = array();
                    }

                    $counter = 0;
                    if(array_key_exists("categories",$venueDetail->response->venue)) {
                        $categories = array(array());
                        foreach ($venueDetail->response->venue->categories as $category) {
                            $categories[$counter]['cid'] = $category->id;
                            $categories[$counter]['categoryName'] = $category->name;
                            $counter++;
                        }
                    } else {
                        $categories = array();
                    }

                    $counter = 0;
                    if(array_key_exists("groups",$venueDetail->response->venue->photos)) {
                        $photos = array(array());
                        foreach ($venueDetail->response->venue->photos->groups as $group) {
                            foreach ($group->items as $photo) {
                                $photos[$counter]['pid'] = $photo->id;
                                $photos[$counter]['photoURL'] = $photo->url;
                                $counter++;
                            }
                        }
                    } else {
                        $photos = array();
                    }

                    // add them to the foursquare tables for vendors, categories, photos, etc.
                    $this->add_vendor($name, $id, $lat, $lng, $phone, $addr, $addrCrossStreet, $addrCity, $addrState, $addrCountry, $addrZip, $website, $tags, $categories, $photos);                
                 } else {
                     echo "Error returned for $searchQuery at $lat, $lng in foursquare database. Add manually\n\n";
                 }
             } else {
                 echo "Could not find $searchQuery at $lat, $lng in foursquare database. Add manually\n\n";
             }
        }   
    }
    

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
}
?>
