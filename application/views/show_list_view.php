<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../assets/css/login_css.css" media="screen" />
    </head>
    <body>
        
        <!-- add a button or simple user interface to take fields and to generate list -->
        <?php
        echo $listName;
        
        ?>
        
        </br>
        </br>
        </br>
             
        <table border='0' cellspacing='0' style='border-collapse: separate' width='400'>

            <?php
                $counter = 0;

                foreach ($vendorNames as $row) {
                    if($counter % 2 ==0)
                        $shade = "<tr bgcolor='#ffffff'>";
                    else
                        $shade = "<tr bgcolor='#f1f1f1'>";

                    echo $shade;                     
                    
                    $vendorName = $row->name;

                    echo "<td>";
                    echo $vendorName;
                    echo "</td>";
                    echo "</tr>";

                    $counter++;
                }
            ?>

        </table>        
        
    </body>
</html>