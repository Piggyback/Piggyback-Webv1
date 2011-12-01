<meta charset="utf-8">


    <title>piggyback search</title> 
    <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
    <link rel="stylesheet" media="screen" href="../../assets/css/style2.css" type="text/css" />
    <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
    <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>

    <script>
    $(function() {
        $( "#accordion" ).accordion({
            header: 'h3',    
            collapsible: true,
            autoHeight: true,
            navigation: true
        });
        
        // override click handler for h6 text
        // allows for Like and Comment button in the header
        $("#accordion p a").click(function() {
            window.location = $(this).attr('href');
            return false;
        });
            
    });
    </script>

    
    
    <div class="inbox">

        <div id="accordion">

            <?php
                foreach ($inboxItems as $row)
                {
                    // determine if $row is a list or single vendor
                    if ( $row->lid == 0 )
                    {   
                        // single vendor
                        // vendor name here
                        echo "<h3><a href=\"#\">" . $row->name . " ";
                        // sub title here
                        echo "<h5>" . $row->firstName . " " . $row->lastName . " says " . "\"" . $row->ReferralsComment . "\"</h5>";
                        // like, comment button here
                        echo "<p><a href=\"google.com\">Like</a><a href=\"google.com\">Comment</a></p>";
                        echo "</a></h3>";
                        // TODO: place like and comment buttons on the same line and remove trivial <a href> line @andyjiang
                        
                        
                        // vendor details here
                        echo "<div><h5>" . $row->addrNum . " " . $row->addrStreet . "</br>"; // add all list detail here
                        echo $row->addrCity . " " . $row->addrState . " " . $row->addrZip . "</br>";
                        echo $row->phone . "</br>";
                        echo $row->website;
                        echo "</h5></div>";
                        // TODO: add buttons (like, comment), dragability, @andyjiang
                    } else {
                        // list 
                        // TODO: make the List referrals into an accordion of vendor names @andyjiang
                        echo "<h3><a href=\"#\">" . $row->UserListsName . " list</br>";
                        echo "<h5>" . $row->firstName . " " . $row->lastName . " says " . "\"" . $row->ReferralsComment . "\"";
                        echo "</p></a></h5></h3>";
                        echo "<div><p>" . $row->firstName . " " . $row->lastName; // add all list detail here
                        echo "</div>";
                    }
                }
            ?>
<!--                <h3><a href="#">Section 1</a></h3>
                <div>
                        <p>
                Vendor detail
                </div>
                <h3><a href="#">Section 2</a></h3>
                <div>
                        <p>
                DATA HERE
                </div>
                <h3><a href="#">Section 3</a></h3>
                <div>
                        <p> data here
                        <ul>
                                <li>List item one</li>
                                <li>List item two</li>
                                <li>List item three</li>
                        </ul>
                </div>
                <h3><a href="#">Section 4</a></h3>
                <div>
                        <p>Cras dictum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia mauris vel est. </p><p>Suspendisse eu nisl. Nullam ut libero. Integer dignissim consequat lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. </p>
                </div>

                ?>-->

        </div>

    </div>