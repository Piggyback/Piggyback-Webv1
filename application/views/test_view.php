<!-- the URL for this TEST SANDBOX is ../test -->

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
        refer_list();
    });
    
    function refer_list() {
        var now = new Date();
        now = now.format("yyyy-mm-dd HH:MM:ss");
        
        var uidFriends = new Array();
        var uidFriend1 = new Array();
        var uidFriend2 = new Array();
        uidFriend1['name'] = "a";
        uidFriend1['email'] = "b";
        uidFriend1['uid'] = 0;
        uidFriend2['name'] = "A";
        uidFriend2['email'] = "B";
        uidFriend2['uid'] = 1;
        uidFriends.push(uidFriend1);
        uidFriends.push(uidFriend2);
        
        var uidFriendsObj = {};
        var friendNum;
        for (var i = 0; i < uidFriends.length; i++) {
            friendNum = "friend" + i.toString();
            uidFriendsObj[friendNum] = uidFriends[i].uid;
        }
        var uidFriendsStr = JSON.stringify(uidFriendsObj);
        alert(uidFriendsStr);
        
	jQuery.post('test/refer_list', {
            lid: 2,
            uid: 7,
            numFriends: uidFriends.length,
            uidFriends: uidFriendsStr,
            date: now,
            comment: "hello check out my list"
        }, function(data) {

        });
    }
    
        </script>
    </body>
</html>


