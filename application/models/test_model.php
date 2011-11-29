<?php
class test_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
    }
    
    function testMethod()
    {
        $hello = "hahaha";
        return $hello;
    }
}
?>
