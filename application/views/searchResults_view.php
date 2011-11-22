<html>
    <head>
        <title>piggyback</title>
        <link rel="stylesheet" media="screen" href="../../assets/css/style.css" type="text/css" />
        <link rel="stylesheet" media="screen" href="../../assets/css/jquery.ptTimeSelect.css" type="text/css" />
        <script type="text/javascript" src="../../assets/js/jquery.js"></script> 
        <script type="text/javascript" src="../../assets/js/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="../../assets/js/jquery.ptTimeSelect.js"></script>
        <script type="text/javascript" src="../../assets/js/jquery.cust.js"></script>
    </head>
    <body>
        
    <table id="search_results" class="tablesorter">
    <thead>
    <tr>
       <th align='left'>Name</th>
       <th align='left'>Category</th>
       <th align='left'>Address</th>
    </tr>
    </thead>
    <tbody>
    <?php
        foreach ($searchResults as $row) {
            echo "<tr>";
            echo "<td>{$row->name}<br></td>";
            echo "<td>{$row->category}<br></td>";
            echo "<td>{$row->street}<br>{$row->city}, {$row->state} {$row->zip}<br></td>";
            echo "</tr>";
        }
        ?>
            
        </tbody>
        </table>
    </body>
</html>