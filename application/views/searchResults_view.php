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
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        foreach ($searchResults as $row) {
            // if data for the given vendor was successfully pulled, display results in table
            if ($row->status == 'OK') {
                // error is given for retrieving something that is not there (e.g., no website)
                // so only overwrite default NULL if there is a value returned for each key
                $name = NULL;
                $types = NULL;
                $addr = NULL;
                $phone = NULL;
                $website = NULL;
                $rating = NULL;
        
                $vendor = $row->result;
                $vendorArray = get_object_vars($vendor);
                $vendorKeys = array_keys($vendorArray);
                foreach ($vendorKeys as $key){
                    if ($key == 'name') {
                        $name = $vendor->{'name'};
                    }
                    if ($key == 'types') {
                        $types = $vendor->{'types'};
                    }
                    if ($key == 'formatted_address') {
                        $addr = $vendor->{'formatted_address'};
                    }
                    if ($key == 'formatted_phone_number') {
                        $phone = $vendor->{'formatted_phone_number'};
                    }
                    if ($key == 'website') {
                        $website = $vendor->{'website'};
                    }
                    if ($key == 'rating') {
                        $rating = $vendor->{'rating'};
                    }
                }
                    
                echo "<tr>";
                echo "<td>$name<br></td>";
                echo "<td>";
                foreach ($types as $type) {
                    echo $type." ";
                }
                echo "<br></td>";
                echo "<td>$addr<br></td>";
                echo "<td>$phone<br></td>";
                echo "<td>$website<br></td>";
                echo "<td>$rating<br></td>";
                echo "</tr>";        
            }
        }
    }
        ?>
        </tbody>
        </table>
    </body>
</html>