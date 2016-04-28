/**
 * Handless SSO AJAX and CORS login
 */
var WebgardenSSO = Class.create({
    /**
     * constructor, initializes ajax and binds event listeners
     */
    initialize: function() {
        this.loginArea = $('id-login-area');
        this.initAjax();
        this.formOnSubmit();
        this.checkCookie();
        
        this.loginSection = $$('#id-client-login .card-wrap').first();
        this.loginSection.hide();
    },
    /**
     * Checks login state from specific element
     *
     * @param selector
     * @returns {*}
     */
    getLogin: function(selector) {
        var item  = $$(selector).first();
        if(item){
            var value = item.getValue();
            if(value) {
                return value;
            } else {
                return null;
            }    
        } else {
            return null;
        }
    },
    /**
     * Listener binded on submitting form
     */
    formOnSubmit: function() {
        if($('id-sso-form')){
            $('id-sso-form').observe('submit', function(e){
                e.preventDefault();
                var email = this.getLogin("#id-sso-form input[name='email']");
                var password = this.getLogin("#id-sso-form input[name='password']");
                if(email && password){
                    this.login(email, password);
                }
            }.bind(this));
        }
    },
    /**
     * AJAX login request
     *
     * @param email
     * @param password
     */
    login: function(email, password) {
        var url = 'http://sso.local/loginPlain.php';
        var parameters = '?m=3&login=1&email=' + email + '&password=' + password;
        this.ajaxRequest.onreadystatechange = this.ajaxLoginResolver.bind(this);

        this.ajaxRequest.open('POST', url);
        this.ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        this.ajaxRequest.send(parameters);
    },
    /**
     * AJAX request checking login cookie
     */
    checkCookie: function() {
        if(!$('id-user-id')) {
            var url = 'http://sso.local/loginPlain.php';
            var parameters = 'm=3&check_cookie=1';
            this.ajaxRequest.onreadystatechange = this.ajaxCheckCookieResolver.bind(this);

            this.ajaxRequest.open('POST', url);
            this.ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            this.ajaxRequest.send(parameters);
        }
    },
    /**
     * Initializes AJAX and sets withCredentials parameter essential for CORS
     */
    initAjax: function () {      
        if (window.XMLHttpRequest) { // Mozilla, Safari, IE7+ ...
            this.ajaxRequest = new XMLHttpRequest();
        } else if (window.ActiveXObject) { // IE 6 and older
            this.ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
        }
        this.ajaxRequest.withCredentials = true;
    },
    /**
     * Handles AJAX response from login function
     */
    ajaxLoginResolver: function(){
        if (this.ajaxRequest.readyState === XMLHttpRequest.DONE) {
            if (this.ajaxRequest.status === 200) {
                var response = JSON.parse(this.ajaxRequest.response);
                if(response.status === "ok") {
                    window.location.replace(window.location.origin + '/?sso_token=' + response.sso_token);
                } else {
                    $('id-sso-form').insert({
                        after: '<div id="messages"><div class="message warn">Login failed, please try again</div></div>'
                    });
                    $('messages').fadeOut(3000);
                    $('id-pass').value = '';
                }
            } else {
                console.log('login, there was a problem with the request.');
            }
        }
    },
    /**
     * Handles AJAX response from checkCookie function
     */
    ajaxCheckCookieResolver: function() {
        if (this.ajaxRequest.readyState === XMLHttpRequest.DONE) {
            if (this.ajaxRequest.status === 200) {
                console.log(this.ajaxRequest.response);
                var response = JSON.parse(this.ajaxRequest.response);
                if(response.status === "ok") {
                    if(this.loginArea){
                        var html = '<div id="id-sso-links"><p>You are logged in as <strong>' + response.email + '</strong> at <a href="http://sso.local/login.php">Webgarden SSO</a></p>';
                        html += "<ul><li><a href='./?sso_token=" + response.sso_token + "' title='" + response.email + "'>Continue as " + response.email + "</a></li>";
                        html += "<li><a id='id-relog' href='#' title='Log in as another user'>Log in as another user</a></li></ul></div>";
                        var messages = $('messages');
                        if(messages){
                            var content = messages.innerHTML;
                            html += '<div id="messages">' + content + '</div>';
                            messages.remove();
                        }
                       
                        this.loginArea.hide();
                        this.loginArea.insert({
                            before: html
                        });

                        if(messages) {
                            $('messages').fadeOut(3000);
                        }
                       
                        $("id-relog").observe('click', function(e){
                            e.preventDefault();
                            this.loginArea.show();
                            $("id-sso-links").hide();
                            return false;
                        }.bind(this));
                    }
                } else if(response.status === "no_cookie") {
                    console.log("check_cookie, no SSO cookie present.");
                } else if(response.status === "bad_login") {
                    console.log("check_cookie, bad login.");
                }
                
            } else {
                console.log('check_cookie, there was a problem with the request.');
            }
        }
        if(this.loginSection) {
            this.loginSection.show();
        }
    }
});

//document onload event
document.observe('dom:loaded', function(){
    var wg = new WebgardenSSO();
});
