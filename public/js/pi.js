
/* Prevent the page from automatically going to a bit */


document.addEventListener('DOMContentLoaded', startUp);



function stickyScrollTo(el){
         var offset = -1.2 * (document.getElementsByTagName('nav')[0].offsetHeight); 
         var y = el.getBoundingClientRect().top + window.pageYOffset + offset;
         console.log(y);
         window.scrollTo({top: y, behavior: 'smooth'});
}

function startUp(){
   if (location.hash){
      console.log('hi');
      console.log(location.hash);
      stickyScrollTo(document.getElementById(location.hash.substring(1)))
   }
   lazyload();
   makeHashLinksStickyScroll();
   if (document.getElementById('screenshots')){
       addScreenshotFilters();
   }
   addBlockHideShow();
}

function addBlockHideShow(){
    document.querySelectorAll('.details').forEach(function(d){
        console.log(d);
        d.getElementsByTagName('h3')[0].addEventListener('click',hideShowBlock);
    });
}

function hideShowBlock(){
    if (this.parentNode.classList.contains('closed')){
        this.parentNode.classList.remove('closed');
    } else {
        this.parentNode.classList.add('closed');
    }
}

function addScreenshotFilters(){
    /* First, add event listener */
    
    document.querySelectorAll('#screenshots input').forEach(function(i){
        i.addEventListener('change',selectScreenshots);
    });
}

function selectScreenshots(){
    var scdiv = document.getElementById('screenshots');
    var checkedInputs = document.querySelectorAll('#screenshots input:checked');
    removeSelectedScreenshots();
    scdiv.classList.remove('filtered');
    if (checkedInputs.length > 0){
        console.log('Found checked inputs');
        scdiv.classList.add('filtered');
        checkedInputs.forEach(function(i){
            var thisKeyword = i.getAttribute('data-keyword');
            var screenshots = scdiv.querySelectorAll('.screenshot[data-keyword="' + thisKeyword + '"]');
            screenshots.forEach(function(s){
                s.classList.add('selected');
            });
        });
    }
}

function removeSelectedScreenshots(){
    var selected = document.querySelectorAll('.screenshot.selected');
    selected.forEach(function(s){
        s.classList.remove('selected');
    });
}

function makeHashLinksStickyScroll(){
    var hashLinks = document.querySelectorAll("a[href^='#']:not([data-toggle])");
    hashLinks.forEach(function(link){
        link.addEventListener('click', function(e){
           e.preventDefault();
           if (history.pushState){
               history.pushState(null, null, link.hash);
               stickyScrollTo(document.getElementById(link.hash.substring(1)));
           } else {
               location.hash = link.hash;
           }
        });
   });
}


/* 
 * Basic function for implementing lazyload for a 
 * the various types of image cards that we want to
 * lazy load. We can't just instantiate a single 
 * lazyload instance since there are a number of 
 * horizontal scrolling containers to consider.
 * 
 * This function is lifted from the lazyload documentation
 * with thanks to the authors.
 */

function fixHeaderJump(){
    
}

function lazyload(){
    
    
    //Basic lazy load for the cards.
   var lazyCards = new LazyLoad({
        elements_selector: "img[data-src]"
    });
    
    //And now lazy load for the panels
    
    if (document.querySelectorAll('.panelBody')){
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
}


function goToDetails(){
    var btns = document.querySelectorAll("[data-toggle='collapse']");
    btns.forEach(function(btn){
        console.log(btn);
        btn.addEventListener('click', function(){
            var contentEl = document.getElementById(btn.getAttribute('href').substring(1));
            var targEl = contentEl.closest('.details');
            console.log(targEl);
            if (this.getAttribute('aria-expanded') == 'false'){
                targEl.scrollIntoView({'behavior': 'smooth'});
            }
        }
    )});
    
}
function makeDetailsExpandable(){
        document.querySelectorAll('.details').forEach(function(detail){
            var header = detail.getElementsByTagName('h3')[0];
            header.classList.add('togglable');
            header.addEventListener('click', toggleDetails)
       });
}



function toggleDetails(){
    console.log(this);
   var expander = this.closest('[aria-expanded]');
   var isOpen = (expander.getAttribute('aria-expanded') == 'true');
   console.log(isOpen);
   if (isOpen){
        expander.setAttribute('aria-expanded', 'false');
    } else {
       expander.setAttribute('aria-expanded', 'true');
    }
}        

