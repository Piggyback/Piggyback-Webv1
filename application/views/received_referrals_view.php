<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../assets/css/login_css.css" media="screen" />
    </head>
    <body>
        
        <!-- add a button or simple user interface to take fields and to generate list -->

        Your friends' recommendations!
        
        </br>
        </br>
        </br>
             
        <table border='0' cellspacing='0' style='border-collapse: separate' width='400'>

            <?php
                $counter = 0;

                foreach ($inboxItems as $row) {
                    if($counter % 2 ==0)
                        $shade = "<tr bgcolor='#ffffff'>";
                    else
                        $shade = "<tr bgcolor='#f1f1f1'>";

                    echo $shade;                     
//                    $rid = intval($row->rid);  // int
//                    $fromUserID = intval($row->uid1);  // int
                    
                    $listName = $row->name;
                    $listId = $row->lid;
                    $referrerName = $row->firstName;
                    $userId = $row->uid1;
                    
                    $urlencodeListName = urlencode($listName);
                    $urlencodeReferrerName = urlencode($referrerName);
                    $urlencodeUserId = urlencode($userId);
                    
                    //$listName = $row->name;  // the name of the referral list

                    echo "<td>";
                    echo "<a href=../createlist/show_list?listId=$listId&listName=$urlencodeListName>";
                    echo $listName . "</a>" . "  (shared via <a href=../createlist/show_user_list?userName=$urlencodeReferrerName&userId=$urlencodeUserId>" . $referrerName . "</a>)";
                    echo "</td>";
                    echo "</tr>";

                    $counter++;
                }
            ?>

        </table>        
        
    </body>
</html>