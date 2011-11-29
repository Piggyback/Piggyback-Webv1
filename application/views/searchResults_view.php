<html>
    <head>
        <title>piggyback</title>
        <link rel="stylesheet" media="screen" href="../../assets/css/style.css" type="text/css" />
        <link rel="stylesheet" media="screen" href="../../assets/css/jquery.ptTimeSelect.css" type="text/css" />
        <script type="text/javascript" src="../../assets/js/jquery.js"></script> 
        <script type="text/javascript" src="../../assets/js/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="../../assets/js/jquery.ptTimeSelect.js"></script>
        <script type="text/javascript" src="../../assets/js/jquery.cust.js"></script>
    </head>
    <body>
        
    <?php
    // if there was an error
    if (is_string($searchResults)) {
        echo $searchResults;
    } 
    
    // if there are results, display them
    else {
        echo "<table id='search_results' class='tablesorter'>";
        echo "<thead>";
        echo "<tr>";
           echo "<th align='left'>Name</th>";
           echo "<th align='left'>Categories</th>";
           echo "<th align='left'>Address</th>";
           echo "<th align='left'>Phone Number</th>";
           echo "<th align='left'>Website</th>";
           echo "<th align='left'>Rating</th>";
           echo "<th align='left'>Distance</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        foreach ($searchResults as $row) {
            // if data for the given vendor was successfully pulled, display results in table
            if ($row->status == 'OK') {
                $this->load->database();
              
                // error is given for retrieving something that is not there (e.g., no website)
                // so only overwrite default NULL if there is a value returned for each key
                $name = NULL;
                $reference = NULL;
                $id = NULL;
                $lat = 0;
                $lng = 0;
                $phone = NULL;
                $addr = NULL;
                $addrNum = NULL;
                $addrStreet = NULL;
                $addrCity = NULL;
                $addrState = NULL;
                $addrCountry = NULL;
                $addrZip = NULL;
                $website = NULL;
                $icon = NULL;
                $rating = 0;
                $types = NULL;
        
                $vendor = $row->result;
                $vendorArray = get_object_vars($vendor);
                $vendorKeys = array_keys($vendorArray);
                foreach ($vendorKeys as $key){
                    if ($key == 'name') {
                        $name = $vendor->name;
                    }
                    if ($key == 'reference') {
                        $reference = $vendor->reference;
                    }
                    if ($key == 'id') {
                        $id = $vendor->id;
                    }
                    if ($key == 'geometry') {
                        $lat = $vendor->geometry->location->lat;
                        $lng = $vendor->geometry->location->lng;
                        $theta = $srcLng - $lng; 
                        $dist = sin(deg2rad($srcLat)) * sin(deg2rad($lat)) +  cos(deg2rad($srcLat)) * cos(deg2rad($lat)) * cos(deg2rad($theta)); 
                        $dist = acos($dist); 
                        $dist = rad2deg($dist); 
                        $distMiles = $dist * 60 * 1.1515;
                    }
                    if ($key == 'formatted_phone_number') {
                        $phone = $vendor->formatted_phone_number;
                    }
                    if ($key == 'formatted_address') {
                        $addr = $vendor->formatted_address;
                    }
                    if ($key == 'address_components') {
                            @$addrNum = $vendor->address_components[0]->short_name;
                            @$addrStreet = $vendor->address_components[1]->short_name;
                            @$addrCity = $vendor->address_components[2]->short_name;
                            @$addrState = $vendor->address_components[3]->short_name;
                            @$addrCountry = $vendor->address_components[4]->short_name;
                            @$addrZip = $vendor->address_components[5]->short_name;
                    }
                    if ($key == 'website') {
                        $website = $vendor->website;
                    }
                    if ($key == 'icon') {
                        $icon = $vendor->icon;
                    }
                    if ($key == 'rating') {
                        $rating = $vendor->rating;
                    }
                    if ($key == 'types') {
                        $types = $vendor->{'types'};
                    }
                }
                
                // add to vendor table if row does not exist yet
                $existingVendorQuery = "SELECT id 
                    FROM Vendor 
                    WHERE id = \"$id\"";
                $existingVendorResult = mysql_query($existingVendorQuery);
                $count = mysql_num_rows($existingVendorResult);
                if ($count == 0) {
                    $addVendorQuery = "INSERT INTO Vendor 
                                       VALUES (\"$name\",\"$reference\",\"$id\",$lat,$lng,\"$phone\",\"$addr\",\"$addrNum\",\"$addrStreet\",\"$addrCity\",\"$addrState\",\"$addrCountry\",\"$addrZip\",\"$website\",\"$icon\",$rating)";
                    mysql_query($addVendorQuery) or die("Query failed: " . mysql_error());
                }
                    
                echo "<tr>";
                echo "<td>$name<br></td>";
                echo "<td>";
                foreach ($types as $type) {
                    echo $type." ";
                }
                echo "<br></td>";
                echo "<td>$addrNum $addrStreet<BR>$addrCity $addrState $addrZip<br></td>";
                echo "<td>$phone<br></td>";
                echo "<td>$website<br></td>";
                echo "<td>";
                if ($rating == 0) {
                    echo "N/A</td>";
                } else {
                    echo $rating."<BR></td>";
                }
                echo "<td>".number_format($distMiles,2)." mi.<br></td>";
                echo "</tr>";        
            }
        }
    }
        ?>
        </tbody>
        </table>
    </body>
</html>