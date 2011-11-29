<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../assets/css/login_css.css" media="screen" />
    </head>
    <body>
        
        <!-- add a button or simple user interface to take fields and to generate list -->

        Create List test page 
        
        </br>
        </br>
        </br>
        
        <form action="createList/addAction" method="post" id="listField">

            UID <input type="text" name=uid id="uid"></input>
            </br>
            </br>
            List Name <input type="text" name=listName id="listName"></input>
            </br>
            </br>

            <table border='0' cellspacing='0' style='border-collapse: separate' width='400'>
                
                <?php
                    $counter = 0;
                    
                    foreach ($vendorList as $row) {
                        if($counter % 2 ==0)
                            $shade = "<tr bgcolor='#ffffff'>";
                        else
                            $shade = "<tr bgcolor='#f1f1f1'>";
                        
                        echo $shade;
                        $vendorName = $row->name;   // string                        
                        $vendorId = $row->id;  // string, varchar
                        
                        echo "<td width = '10%'><input type=checkbox name=box[] value=$vendorId> </td> <td width='85%'>";
                        echo $vendorName;
                        echo "</tr>";
                        
                        $counter++;
                    }
                ?>

            </table>

            <input type="submit" value="Create List!">
        
        </form>
        
    </body>
</html>