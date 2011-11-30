<!-- 
    @mikegao

    created: 11/29/11
-->
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../assets/css/login_css.css" media="screen" />
        <script type="text/javascript" src="../assets/js/jquery.min.js" ></script>
        <title>Piggyback Login</title>
        
        <!-- facebook required og meta properties -->
<!--        <meta property="og:title" content="Piggyback"/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="http://192.168.11.28/login"/>-->
        
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
                        jQuery.post("http://192.168.11.28/login/check_if_user_exists", {
                            fbid: response_me.id
                        }, function(data) {
                            if (data == 0) {
                                // user does not exist, proceed to add_user
                                jQuery.post("http://192.168.11.28/login/add_user", {
                                    fbid: response_me.id,
                                    email: response_me.email,
                                    firstName: response_me.first_name,
                                    lastName: response_me.last_name
                                }, function () {
                                    // scan 'Users' table for any Facebook friends using the service
                                    FB.api('/me/friends', function(response_friends) {
                                        if(response_friends.data) {
                                            var jqxhr = jQuery.post('http://192.168.11.28/login/search_for_friends', {
                                                data: response_friends.data
                                            });
                                            // redirect page once user is created (or ignored) AND friends are scanned
                                            jqxhr.complete(function() {window.location = "http://192.168.11.28/home"; });
                                        }
                                    });
                                });
                            } else {
                                window.location = "http://192.168.11.28/home";
                            }
                        });

                    });
                });
                
                // If user is already logged in, redirect to home page
                FB.getLoginStatus(function(response) {
                    if (response && response.status == "connected") {
                        // logged in and connected user
                        FB.api('/me', function(response_me) {
                            // set current session info
                            var jqxhr = jQuery.post("http://192.168.11.28/login/check_if_user_exists", {
                                fbid: response_me.id
                            }, function() {
                                jqxhr.complete(function() {window.location = "http://192.168.11.28/home"; });
                            });
                        });
                    } else {
                        // no user session available -- continue with login
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