
function login() {
    var email = $("input[name='email']").prop("value");
    var password = $("input[name='password']").prop("value");
    $.ajax({
         url: "http://sso.localhost/jwtCORS.php",
         type: "GET",
         dataType: "json",
         data: {
               email: email,
               password: password
             },

         // Work with the response
         success: function( response ) {
             console.log( response ); // server response

             window.location.replace("http://domain1.local/?token=" + response.token);
         },
         error: function (response) {
             console.log(response);
         }
     });
 }
 
 $(document).ready(function(){
      $("#loginButton").click(login);
 });





