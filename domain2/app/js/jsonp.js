function login(){
    console.log('log');
    $.ajax({
         url: "http://sso.localhost/jwt.php",
         type: "GET",
         dataType: "jsonp",
         jsonp: "callback",
         data: {
               email: "joe@example.com",
               password: "joe"
             },

         // Work with the response
         success: function( response ) {
             //console.log( response ); // server response
             console.log(response.data);
         },
         error: function (response) {
             //console.log(response);
             console.log(response.data);
         }
     });
}

$(document).ready(function(){
   login(); 
});




