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
                
                
                FB.api('/me', function(response) {
                    // attempt to add user upon login -- duplicated will be ignored
                    $('div.welcome').html('Welcome ' + response.name + '!');
                    
                    jQuery.post("http://192.168.11.12/Piggyback/index.php/home/getFriends", {
                            FBID: response.id
                    }, function (data) {
                        var friends = JSON.parse(data);
//                        $('div.friends').html(friends[1]);
//                        $html = "";
//                        jQuery.each(friends, function(index, value) {
//                            $html = $html + index + ': ' + value + '<br/';
//                        });
//                        $('div.friends').html($html);
                        $('div.friends').html(data);
                    });
                });

                
                // If user is already logged in, redirect to home page
                FB.getLoginStatus(function(response) {
                    if (response.status != "connected") {
                        // logged in and connected user
                        window.location = "http://192.168.11.12/Piggyback/index.php/login";
//                        alert("pass");
                    } else {
                        // no user session available -- continue with login
//                        alert("fail");
                    }
                });
                
                $('div.logout').click(function () {
                    //logout when div is clicked
                    FB.logout(function(response) {
                        // user is now logged out of service AND facebook
                        // return to login page
                        window.location = "http://192.168.11.12/Piggyback/index.php/login";
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
            <div class="welcome" style="float:left;"></div>
            <div class="logout" style="cursor:pointer; float:right;">Logout</div>
            <br/><br/>Your friends: <br/>
            <div class="friends"></div>
        </div>
    </body>
</html>