
function checkSSOCookie() {
    $.ajax({
        url: "http://sso.local/jwtCORS.php?checkCookie=1&continue=" + window.location.hostname,
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
               } else $("#loginArea").html("<a href='./?token=" + response.token + "'>Login with SSO</a>");
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
            url: "http://sso.local/jwtCORS.php",
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
                window.location.replace("http://domain2.local/?token=" + response.token);
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
 });





