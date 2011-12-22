<html>
    <head>
        <title>piggyback search</title> 
        <link rel="stylesheet" media="screen" href="../../assets/jquery-ui-1.8.16.custom/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" />
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-1.6.2.min.js" type="text/javascript"></script>
        <script src="../../assets/jquery-ui-1.8.16.custom/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
        <script src="../../assets/js/date.format.js" type="text/javascript"></script>
        <script src="../../assets/jquery-ui-1.8.16.custom/development-bundle/ui/jquery.ui.accordionCustom.js"></script>
                <script type="text/javascript" src="../assets/js/date.format.js"></script>
        <meta charset="utf-8">
	
    </head>
    <body>
    <script type="text/javascript">
            
    $(document).ready(function() {
//        get_likes();
//        get_comments();
        loadReferralTracking();
    });
    
    function get_likes() {
        jQuery.post('referral_tracking/get_likes', {
            uid: 7
        }, function(data) {
            var parsedJSON = jQuery.parseJSON(data);
            
            var numLikes = parsedJSON.numLikes;
            var people = parsedJSON.people;
            
            alert("you have " + numLikes + " total likes from " + parsedJSON.numPeople + " people on your referrals!");

            for (var i = 0; i < people.length; i++) {
                alert(people[i].firstName); 
            }
            
        });
    }
    
    function get_comments() {
        jQuery.post('referral_tracking/get_comments', {
            uid: 7
        }, function(data) {
            alert("you have " + data + " total comments on your referrals!");
        });
    }
    
    function loadReferralTracking() {
        var loadStart = 3;

        jQuery.post('referral_tracking/get_referral_tracking', function(data) {
            var parsedJSON = jQuery.parseJSON(data);
            alert(parsedJSON);
//            displayMoreReferrals(parsedJSON);
//            loadStart = loadStart+3;
        });
}
    
    </script>
    </body>
</html>

