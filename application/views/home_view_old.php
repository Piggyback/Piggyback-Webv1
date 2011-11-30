<html>
    <head>
        <title>Piggyback Home</title>
        <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
    </head>
    <body>
        <div id="fb-root"></div>
        <script>
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '251920381531962',
                    status     : true, 
                    cookie     : true,
                    xfbml      : true
                });
                
                // If user is not logged in, redirect user to login page
                FB.getLoginStatus(function(response) {
                    if (response.status != "connected") {
                        // logged in and connected user
                        window.location = "http://192.168.11.28/login";
                    } else {
                        
                    }
                });
                
                $('div.logout').click(function () {
                    //logout when div is clicked
                    FB.logout(function(response) {
                        // user is now logged out of service AND facebook
                        // return to login page
                        window.location = "http://192.168.11.28/login";
                    });
                });
                
            };
            (function(d){
                var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
                js = d.createElement('script'); js.id = id; js.async = true;
                js.src = "//connect.facebook.net/en_US/all.js";
                d.getElementsByTagName('head')[0].appendChild(js);
            }(document));
            
        </script>
        <div id="container">
            <div class="welcome" style="float:left;"><?php echo "Welcome " . $firstName . " " . $lastName . "!" ?></div>
            <div class="logout" style="cursor:pointer; float:right;">Logout</div>
            <br/>
            <div class="fb-pic"><?php echo "<img src='https://graph.facebook.com/" . $fbid . "/picture'>"; ?> </div>
            <br/>Your friends: <br/>
            <div class="friends"><?php //$friend[0] = FBID, $friend[1] = fullname
                                    foreach ($friends as $friend) 
                                        {
                                            echo "<img src='https://graph.facebook.com/" . $friend['fbid'] . "/picture'>";
                                            echo $friend['firstName'] . " " . $friend['lastName'] . "<br>"; }
                                 ?></div>
        </div>
    </body>
</html>