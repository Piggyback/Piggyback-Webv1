<?php
    $this->load->database();
?>

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
            LID <input type="text" name=lid id="lid"></input>
            </br>
            </br>
            List Name <input type="text" name=listName id="listName"></input>
            </br>
            </br>

            <table border='0' cellspacing='0' style='border-collapse: separate' width='400'>

                <?php
                    $retrieveVendorList = "SELECT name FROM Vendor";
                    $data = mysql_query($retrieveVendorList) or die(mysql_error());
                    $counter = 0;
                    while($info = mysql_fetch_array($data))
                    {
                        if ($counter % 2 == 0)
                            $shade = "<tr bgcolor='#ffffff'>";
                        else
                            $shade = "<tr bgcolor='#f1f1f1'>";

                        echo $shade;            
                        $vendorName = $info['name'];
                        echo "<td width = '10%'><input type=checkbox name=box[] value='$vendorName'> </td>
                            <td width = '85%'>";
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