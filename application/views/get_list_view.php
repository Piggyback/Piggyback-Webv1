<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../assets/css/login_css.css" media="screen" />
    </head>
    <body>
        
        <!-- add a button or simple user interface to take fields and to generate list -->

        Get List test page 
        
        </br>
        </br>
        Please see below a list of your lists
        </br>
        <form action="getList/getAction" method="post" id="listField">

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
                        $listId = intval($row->lid);    // int
                        echo "<td width = '10%'><input type=checkbox name=box[] value=$listId> </td> <td width='85%'>";
                        echo $listName;
                        echo "</tr>";
                        
                        $counter++;
                    }
                ?>

            </table>

            <input type="submit" value="Get List!">
        
        </form>
    </body>
</html>