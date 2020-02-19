
document.addEventListener('DOMContentLoaded', startUp);

function startUp(){
   lazyload();
}


function lazyload(){
    
    
   var lazyCards = new LazyLoad({
        elements_selector: "img[data-src]"
    });
    var lazyPanels = new LazyLoad({
    elements_selector: ".panelBody",
    // When the .horzContainer div enters the viewport...
    callback_enter: function(el) {
        var oneLL = new LazyLoad({
            container: el
        });
    }
});

  
}

