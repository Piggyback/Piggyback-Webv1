<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../assets/css/login_css.css" media="screen" />
    </head>
    <body>
        
        <!-- add a button or simple user interface to take fields and to generate list -->

        Recommend List Test Page
        
        </br>
        </br>
        </br>
        
        <form action="recommendLists/createRecommendAction" method="post" id="listField">
            Which lists would you like to recommend?
            </br>
            </br>
            
            <table border='0' cellspacing='0' style='border-collapse: separate' width='400'>
                
                <?php
                    $counter = 0;
                    
                    foreach ($userList as $row) {
                        if($counter % 2 ==0)
                            $shade = "<tr bgcolor='#ffffff'>";
                        else
                            $shade = "<tr bgcolor='#f1f1f1'>";
                        
                        echo $shade;                     
                        $listId = intval($row->lid);  // int
                        $listName = $row->name;  // string
                        
                        echo "<td width = '10%'><input type=checkbox name=box[] value=$listId> </td> <td width='85%'>";
                        echo $listName;
                        echo "</tr>";
                        
                        $counter++;
                    }
                ?>
    
            </table>
            
            </br>
            </br>
            To whom do you want to recommend these lists?
            </br>
            </br>
            <table border='0' cellspacing='0' style='border-collapse: separate' width='400'>
                
                <?php
                    $counter = 0;
                    
                    foreach ($friendList as $row) {
                        if($counter % 2 ==0)
                            $shade = "<tr bgcolor='#ffffff'>";
                                else
                            $shade = "<tr bgcolor='#f1f1f1'>";
                        
                        echo $shade;              
                        $fbid2 = intval($row->fbid2);  // int
                        
                        echo "<td width = '10%'><input type=checkbox name=uid2[] value=$fbid2> </td> <td width='85%'>";
                        echo $fbid2;
                        echo "</tr>";
                        
                        $counter++;
                    }
                ?>

            </table>
            </br>
            
            <input type="submit" value="Recommend List!">
        
        </form>
        
    </body>
</html>