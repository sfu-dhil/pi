
document.addEventListener('DOMContentLoaded', startUp);

function startUp(){
   lazyload();
}


function lazyload(){
    var lazyLoadInstance = new LazyLoad({
    elements_selector: "img[data-src]"
});
}

