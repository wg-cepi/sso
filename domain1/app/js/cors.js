
function checkSSOCookie() {
    $.ajax({
        url: "http://sso.local/app/module_sso/module_sso.php?m=3&checkCookie=1&continue=" + window.location.origin,
        type: "GET",
        dataType: "json",
        xhrFields: {
            withCredentials: true
        },

        // Work with the response
        success: function( response ) {
            console.log( response ); // server response
            if(response.status == "ok") {
                 if($("#userLogged").length) {
                    $("#loginArea").remove();
                 } else {
                    var html = "<ul id='ssoLinks'><li><a href='./?token=" + response.token + "' title='" + response.email + "'>Continue as " + response.email + "</a></li>";
                    html += "<li><a id='relog' href='#' title='Log in as another user'>Login in as another user</a></li></ul>";
                
                    $("#loginArea").after(html);
                    $("#loginArea").hide();

                    $("#relog").click(function(e){
                        e.preventDefault();
                        $("#loginArea").show();
                        $("#ssoLinks").hide();
                    });
                 }
            }
            if(response.status == "no_cookie") {
                $("#loginArea").append("<p>No cookie</p>");
            }
        },
        error: function (response) {
            console.log(response);
        }
    });
}
function login() {
    var email = $("input[name='email']").prop("value");
    var password = $("input[name='password']").prop("value");
    if(email && password){
        $.ajax({
            url: "http://sso.local/app/module_sso/module_sso.php?m=3&login=1",
            type: "GET",
            dataType: "json",
            data: {
                  email: email,
                  password: password
                },
            xhrFields: {
                withCredentials: true
            },
            // Work with the response
            success: function( response ) {
                console.log(response); // server response
                console.log(response.token);
                window.location.replace("http://domain1.local/?token=" + response.token);
            },
            error: function (response) {
                console.log(response);
            }
        });
    }
 }
 
 $(document).ready(function(){
    $("#loginButton").click(function(e){
        e.preventDefault();
        login();
    });
    
    checkSSOCookie();
    
    console.log(window.location);
 });





