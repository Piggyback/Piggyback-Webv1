<html>
    <body>
<?php
$q=$_GET["q"];

$this->load->database();
$result = mysql_query($q);
if (!$result) {
    echo "<script type=\"text/javascript\">alert(\"Error with sending referral. Please try again.\");</script>";
}
?>
</body>
</html>