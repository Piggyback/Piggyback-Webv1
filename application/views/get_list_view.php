<?php
    $this->load->database();
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../assets/css/login_css.css" media="screen" />
    </head>
    <body>
        
        <!-- add a button or simple user interface to take fields and to generate list -->

        Get List test page 
        
        </br>
        </br>
        Please see below a list of LID's
        </br>
        <form action="getList/getAction" method="post" id="listField">

            <table border='0' cellspacing='0' style='border-collapse: separate' width='400'>

                <?php
                    $getListNames = "SELECT lid FROM UserLists";
                    $data = mysql_query($getListNames) or die("My sql error: " . mysql_error());
                    $counter = 0;
                    while($info = mysql_fetch_array($data))
                    {
                        if ($counter % 2 == 0)
                            $shade = "<tr bgcolor='#ffffff'>";
                        else
                            $shade = "<tr bgcolor='#f1f1f1'>";

                        echo $shade;            
                        $lid = $info['lid'];
                        echo "<td width = '10%'><input type=checkbox name=box[] value='$lid'> </td>
                            <td width = '85%'>";
                        echo $lid;
                        echo "</tr>";

                        $counter++;
                    }
                ?>

            </table>

            <input type="submit" value="Get List!">
        
        </form>
    </body>
</html>