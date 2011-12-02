<html>
    <head>
        <title>piggyback search</title>
    </head>
    <body>
        <form action="searchvendors/perform_search" method="post" target="searchresults">
          search for: <input type="text" name="searchText" size="50"/>
          near: <input type="text" name ="searchLocation" size="35" />
          <input type="submit" name="submitSearch" value="Search for Businesses!" />
        </form>
        
        <iframe src="searchvendors/iframe" id="searchresults" width="100%" height="90%">
        </iframe>
        
    </body>
</html>