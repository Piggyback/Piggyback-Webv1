<html>
    <head>
        <title>piggyback search</title>
    </head>
    <body>
        <form action="searchVendors/performSearch" method="post" target="searchResults">
          search for: <input type="text" name="searchText" size="50"/>
          near: <input type="text" name ="searchLocation" size="35" />
          <input type="submit" name="submitSearch" value="Search for Businesses!" />
        </form>
        
        <iframe src="searchVendors/iFrame" id="searchResults" width="100%" height="90%">
        </iframe>
        
    </body>
</html>