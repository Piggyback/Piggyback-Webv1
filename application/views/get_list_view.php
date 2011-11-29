<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../assets/css/login_css.css" media="screen" />
    </head>
    <body>
        
        <!-- add a button or simple user interface to take fields and to generate list -->

        <!-- add username here -->
        <?php
            echo $uid . "'s other fantastic lists!";
        ?>
            
        </br>
        </br>
        </br>

        <table border='0' cellspacing='0' style='border-collapse: separate' width='400'>

            <?php
                $counter = 0;
                foreach ($listInfo as $row) {
                    if($counter % 2 ==0)
                        $shade = "<tr bgcolor='#ffffff'>";
                    else
                        $shade = "<tr bgcolor='#f1f1f1'>";

                    echo $shade;
                    $listName = $row->name;         // string
                    $urlencodeListName = urlencode($listName);
                    $listId = intval($row->lid);    // int
                    echo "<td><a href=../createList/showList?listId=$listId&listName=$urlencodeListName>";
                    echo $listName;
                    echo "</a></br></tr>";

                    $counter++;
                }
            ?>

        </table>

    </body>
</html>