<?php

$this->load->database();
$json = file_get_contents("assets/text/FbPlacesResults.txt");
$results = json_decode($json);

$rootResults = $results->{'data'};

foreach ($rootResults as $vendor) {
    // set all variables to null
    $name = NULL;
    $category = NULL;
    $vid = 0;
    $location = NULL;
    $street = NULL;
    $city = NULL;
    $state = NULL;
    $country = NULL;
    $zip = NULL;
    $lat = 0;
    $long = 0;
    
    // overwrite with data if it exists
    $name = $vendor->{'name'};
    $category = $vendor->{'category'};
    $vid = $vendor->{'id'};
    $location = $vendor->{'location'};
    $locArray = get_object_vars($location);
    $locKeys = array_keys($locArray);
    foreach ($locKeys as $key){
        if ($key == 'street') {
            $street = $location->{'street'};
        }
        if ($key == 'city') {
            $city = $location->{'city'};
        }
        if ($key == 'state') {
            $state = $location->{'state'};
        }
        if ($key == 'country') {
            $country = $location->{'country'};
        }
        if ($key == 'zip') {
            $zip = $location->{'zip'};
        }
        if ($key == 'latitude') {
            $lat = $location->{'latitude'};
        }
        if ($key == 'longitude') {
            $long = $location->{'longitude'};
        }
    }

    // only add if vendor doesnt exist in database yet
    $existingRow = "SELECT count(vid) 
                    FROM Vendor 
                    WHERE vid = $vid";
    $existingRowResult = mysql_query($existingRow);
    if (mysql_num_rows($existingRowResult) == 0) {
        $addVendorQuery = "INSERT INTO Vendor 
                           VALUES ($vid,\"$name\",\"$category\",\"$street\",\"$city\",\"$state\",\"$country\",\"$zip\",$lat,$long)";
        echo $addVendorQuery;        
        mysql_query($addVendorQuery) or die("Query failed: " . mysql_error());
    }
}

?>
