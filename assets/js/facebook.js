function loadFbApiForLogin() {
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '163970203713342',
            channelUrl : 'channel', // does this file even exist?
            status     : true,
            cookie     : true,
            xfbml      : true,
            oauth      : true
        });

        // event handler for when user logs in
        FB.Event.subscribe('auth.login', function() {
            FB.api('/me', function(response_me) {
                jQuery.post("login/check_if_user_exists", {
                    fbid: response_me.id
                }, function(data) {
                    if (data == 0) {
                        // user does not exist, proceed to add_user
                        jQuery.post("login/add_user", {
                            fbid: response_me.id,
                            email: response_me.email,
                            firstName: response_me.first_name,
                            lastName: response_me.last_name
                        }, function () {
                            // scan 'Users' table for any Facebook friends using the service
                            FB.api('/me/friends', function(response_friends) {
                                if(response_friends.data) {
                                    var jqxhr = jQuery.post('login/search_for_friends', {
                                        data: response_friends.data
                                    });
                                    // redirect page once user is created (or ignored) AND friends are scanned
                                    jqxhr.complete(function() {
                                        window.location = "home"; 
                                    });
                                }
                            });
                        });
                    } else {
                        // user exists, redirect to home page
                        window.location = "home";
                    }
                });
            });
        });

        // if user is already logged in, redirect to home page
        FB.getLoginStatus(function(response) {
            if (response && response.status === "connected") {
                // logged in and connected user
                var jqxhr = jQuery.post("login/check_if_user_exists", {
                    fbid: response.authResponse.userID
                }, function() {
                    jqxhr.complete(function() {
                        window.location = "home"; 
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
}

function loadFbApiForLoginClosedBeta() {
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '251920381531962',
            channelUrl : 'channel', // does this file even exist?
            status     : true,
            cookie     : true,
            xfbml      : true,
            oauth      : true
        });

        // event handler for when user logs in
        FB.Event.subscribe('auth.login', function() {
            FB.api('/me', function(response_me) {
                jQuery.post("login/check_if_user_exists", {
                    fbid: response_me.id
                }, function(data) {
                    if (data == 1) {
                    // scan 'Users' table for any Facebook friends using the service
                        FB.api('/me/friends', function(response_friends) {
                            if(response_friends.data) {
                                var jqxhr = jQuery.post('login/search_for_friends', {
                                    data: response_friends.data
                                });
                                // redirect page once friends are scanned
                                jqxhr.complete(function() {
                                    window.location = "home"; 
                                });
                            }
                        });
                    } else {
                        alert ("Piggyback is currently in the closed beta stage. Please contact team Piggyback to request access.")
                    }
                });
            });
        });

        // if user is already logged in, redirect to home page
        FB.getLoginStatus(function(response) {
            if (response && response.status === "connected") {
                // logged in and connected user
                var jqxhr = jQuery.post("login/check_if_user_exists", {
                    fbid: response.authResponse.userID
                }, function(data) {
//                    jqxhr.complete(function() {
                        if (data == 1) {
                            window.location = "home"; 
                        } else {
                            alert ("Piggyback is currently in the closed beta stage. Please contact team Piggyback to request access.")
                        }
//                    });
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
}

function loadFbApiForHome() {
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '163970203713342',
            channelUrl : 'channel',
            status     : true,
            cookie     : true,
            xfbml      : true,
            oauth      : true
        });

        // If user is not logged in, redirect user to login page
        FB.getLoginStatus(function(response) {
            if (response.status != "connected") {
                // logged in and connected user
                window.location = "login";
            } else if (response.authResponse.expiresIn < 0) {
//                alert('omg. session expired. let gaotse know, ' + response.authResponse.expiresIn);
            } else {
                // do nothing
//                alert(response.authResponse.expiresIn);
            }
        });

        $('#logout').click(function () {
            //logout when div is clicked
            FB.logout(function(response) {
                // user is now logged out of service AND facebook
                // return to login page
                window.location = "login";
            });
        });

    };
    (function(d){
        var js, id = 'facebook-jssdk';if (d.getElementById(id)) {return;}
        js = d.createElement('script');js.id = id;js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        d.getElementsByTagName('head')[0].appendChild(js);
    }(document));
}

function loadFbApiForHomeClosedBeta() {
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '251920381531962',
            channelUrl : 'channel',
            status     : true,
            cookie     : true,
            xfbml      : true,
            oauth      : true
        });

        // If user is not logged in, redirect user to login page
        FB.getLoginStatus(function(response) {
            if (response.status != "connected") {
                // logged in and connected user
                window.location = "login";
            } else if (response.authResponse.expiresIn < 0) {
//                alert('omg. session expired. let gaotse know, ' + response.authResponse.expiresIn);
            } else {
                jQuery.post("login/check_if_user_exists", {
                    fbid: response.authResponse.userID
                }, function(data) {
                    if (data == 0) {
                        window.location = "login";
                    }
                });
            }
        });

        $('#logout').click(function () {
            //logout when div is clicked
            FB.logout(function(response) {
                // user is now logged out of service AND facebook
                // return to login page
                window.location = "login";
            });
        });

    };
    (function(d){
        var js, id = 'facebook-jssdk';if (d.getElementById(id)) {return;}
        js = d.createElement('script');js.id = id;js.async = true;
        js.src = "//connect.facebook.net/en_US/all.js";
        d.getElementsByTagName('head')[0].appendChild(js);
    }(document));
}
