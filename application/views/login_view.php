<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../assets/css/login_css.css" media="screen" />
        <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
        <title>Piggyback Login</title>
    </head>
    <body>
        <div class="logo"></div>
        <div id="fb-root"></div>
        <script>
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '251920381531962',
                    status     : true, 
                    cookie     : true,
                    xfbml      : true
                });
                
                FB.Event.subscribe('auth.login', function() {
                    FB.api('/me', function(response_me) {
                        //TODO: CHECK IF USER ALREADY EXISTS BEFORE SCANNING FRIENDS / ADD USER AGAIN
                        // attempt to add user upon login -- duplicated will be ignored
                        jQuery.post("http://192.168.11.12/Piggyback/index.php/login/addUser", {
                            FBID: response_me.id,
                            email: response_me.email,
                            firstName: response_me.first_name,
                            lastName: response_me.last_name
                        }, function () {
                            // scan 'Users' table for any Facebook friends using the service
                            FB.api('/me/friends', function(response_friends) {
                                if(response_friends.data) {
                                    var jqxhr = jQuery.post('http://192.168.11.12/Piggyback/index.php/login/searchForFriends', {
                                        data: response_friends.data,
                                        my_id: response_me.id
                                    });
                                    // redirect page once user is created (or ignored) AND friends are scanned
                                    jqxhr.complete(function() {window.location = "http://192.168.11.12/Piggyback/index.php/home"; });
                                }
                            });
                        });
//                        alert(response.name);

                    });
                });
                
                // If user is already logged in, redirect to home page
                FB.getLoginStatus(function(response) {
                    if (response && response.status == "connected") {
                        // logged in and connected user
                        window.location = "http://192.168.11.12/Piggyback/index.php/home";
//                        alert("pass");
                    } else {
                        // no user session available -- continue with login
//                        alert("fail");
                    }
                });
            };
            (function(d){
                var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
                js = d.createElement('script'); js.id = id; js.async = true;
                js.src = "//connect.facebook.net/en_US/all.js";
                d.getElementsByTagName('head')[0].appendChild(js);
            }(document));
        </script>
        <div class="fb-login-button" data-perms="email" style="text-align: center;">Login with Facebook</div>
    </body>
</html>