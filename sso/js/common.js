Element.prototype.fadeOut = function(time) {
    var element = this;
    var step = 0.05;
    var timeStep = time * step;
    var opacity = 1;

    var fadeOutRecursion = function(){
        element.style.opacity = opacity;
        if(opacity > 0){
            setTimeout(fadeOutRecursion, timeStep);
            opacity -= step;
        } else {
            element.style.opacity = 0;
           // return;
        }

        //if(opacity <= 0) element.remove();
    };
    fadeOutRecursion();

};

document.addEventListener("DOMContentLoaded", function() {
    if(document.getElementById('messages')) {
        document.getElementById('messages').fadeOut(3000);
    }

});
