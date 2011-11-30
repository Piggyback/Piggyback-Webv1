<meta charset="utf-8">


    <title>piggyback search</title> 
    <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
    <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
    <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>

    <script>
    $(function() {
            $( "#accordion" ).accordion({
                    collapsible: true,
                    autoHeight: false,
                    navigation: true
            });
    });
    </script>

    
    
    <div class="inbox">

        <div id="accordion">

            <?php foreach ($inboxItems as $row): ?>
                <h3><a href="#">Section 1</a></h3>
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

                ?>

        </div>

    </div>