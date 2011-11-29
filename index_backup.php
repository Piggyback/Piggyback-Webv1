<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            echo "hello there!! welcome to piggyback! <br>";
            
            // connect to database or output error upon failure
            require_once("loginData/config.php");
            $conn = mysql_connect($dbHost, $dbuser, $dbpass)
                    or die('Could not connect to MySQL: ' . mysql_error());

            // select table within database
            mysql_select_db($dbname);
            
            // retrieve vendor data from database
            $vendorData = mysql_query("SELECT * FROM Vendors")
                    or die('Could not retrieve vendor data' . mysql_error());
            $numRows = mysql_numrows($vendorData);
            
            // print data
            while ($info = mysql_fetch_array($vendorData)) {
                echo "<b>Vendor ID: </b>" . $info['vid'] . " ";
                Print "<b>Vendor Name: </b>" . $info['name'] . " ";
                Print "<b>Vendor Address: </b>" . $info['address'] . " <br>";
            }
            
            // close database
            mysql_close($conn);
        ?>
    </body>
    <?php
    ?>
</html>
