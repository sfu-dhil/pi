
document.addEventListener('DOMContentLoaded', startUp);

function startUp(){
   lazyload();
   makeDropdowns();
}


function lazyload(){
    var lazyLoadInstance = new LazyLoad({
    elements_selector: "img[data-src]"
});
}

