<!--
    Document   : login_view.php
    Created on : Nov 30, 2011, 6:49:47 AM
    Author     : gaobi
    Description:
        login view
-->
<!--
   TO-DOs:
-->
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../assets/css/login_css.css" media="screen" />
        <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
        <script type="text/javascript" src="../assets/js/facebook.js" ></script>
        <title>Piggyback Login</title>
    </head>
    <body>
        <div id="fb-root"></div>
        <script>
            loadFbApiForLoginClosedBeta();
//            window.fbAsyncInit = function() {
//                FB.init({
//                    appId      : '251920381531962',
//                    channelUrl : 'http://192.168.11.28/channel.php',
//                    status     : true,
//                    cookie     : true,
//                    xfbml      : true,
//                    oauth      : true
//                });
//
//                // event handler for when user logs in
//                FB.Event.subscribe('auth.login', function() {
//                    FB.api('/me', function(response_me) {
//                        jQuery.post("http://192.168.11.28/login/check_if_user_exists", {
//                            fbid: response_me.id
//                        }, function(data) {
//                            if (data == 0) {
//                                // user does not exist, proceed to add_user
//                                jQuery.post("http://192.168.11.28/login/add_user", {
//                                    fbid: response_me.id,
//                                    email: response_me.email,
//                                    firstName: response_me.first_name,
//                                    lastName: response_me.last_name
//                                }, function () {
//                                    // scan 'Users' table for any Facebook friends using the service
//                                    FB.api('/me/friends', function(response_friends) {
//                                        if(response_friends.data) {
//                                            var jqxhr = jQuery.post('http://192.168.11.28/login/search_for_friends', {
//                                                data: response_friends.data
//                                            });
//                                            // redirect page once user is created (or ignored) AND friends are scanned
//                                            jqxhr.complete(function() {
////                                                window.location = "http://192.168.11.28/home"; 
//                                            });
//                                        }
//                                    });
//                                });
//                            } else {
//                                // user exists, redirect to home page
////                                window.location = "http://192.168.11.28/home";
//                            }
//                        });
//
//                    });
//                });
//
//                // if user is already logged in, redirect to home page
//                FB.getLoginStatus(function(response) {
//                    if (response && response.status === "connected") {
//                        // logged in and connected user
//                        var jqxhr = jQuery.post("http://192.168.11.28/login/check_if_user_exists", {
//                            fbid: response.authResponse.userID
//                        }, function() {
//                            jqxhr.complete(function() {
////                                    window.location = "http://192.168.11.28/home"; 
//                            });
//                        });
//                    } else {
//                        // no user session available -- continue with login
//                    }
//                });
//            };
//            (function(d){
//                var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
//                js = d.createElement('script'); js.id = id; js.async = true;
//                js.src = "//connect.facebook.net/en_US/all.js";
//                d.getElementsByTagName('head')[0].appendChild(js);
//            }(document));
        </script>
        <div class="logo"></div>
        <div class="fb-login-button" data-scope="email" style="text-align: center;">Login with Facebook</div>
    </body>
</html>
