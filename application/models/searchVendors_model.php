<?php
class searchVendors_model extends CI_Model {
    
    function __construct() 
    {
        // Call the Model consutrctor
        parent::__construct();
    }
    
    function searchVendors()
    {
        $this->load->database();
        $this->searchText = $this->input->post('searchText');

        //echo "Results for: <b>$this->searchText</b><br><br>";
        //$data['searchText'] = $this->searchText;
        // retrieve results
        $searchQuery = "SELECT name, category, street, city, state, zip 
                        FROM Vendor 
                        WHERE name like '%$this->searchText%'";
        $searchResult = $this->db->query($searchQuery)->result();
        
//        //echo "Results for: <b>{$data['searchText']}</b><BR><BR>";
//        // display results in table
//        echo "<table id=\"search_results\" class=\"tablesorter\">";
//        echo "<thead>";
//        echo "<tr>";
//            echo "<th align='left'>Name</th>";
//            echo "<th align='left'>Category</th>";
//            echo "<th align='left'>Address</th>";
//        echo "</tr>";
//        echo "</thead>";
//        echo "<tbody>";
//        foreach ($searchResult as $row) {
//            echo "<tr>";
//            echo "<td>{$row->name}<br></td>";
//            echo "<td>{$row->category}<br></td>";
//            echo "<td>{$row->street}<br>{$row->city}, {$row->state} {$row->zip}<br></td>";
//            echo "</tr>";
//        }
//        echo "</tbody>";
//        echo "</table>";
//     
        return $searchResult;
    }
}
?>
