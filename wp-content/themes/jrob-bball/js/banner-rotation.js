// Start Slideshow When JS is loaded.    
var c = 0;
var t = 0;
var timeout = null;

function gotoNext() {
    // Set Active State on Link
	$$('#tabs a').each(function(link){link.className=''});
	$$('#tabs a')[t].className = 'active';

	// Animate Slides
    $$('div.slide')[c].setStyle({'opacity': 1});
    $$('div.slide')[t].setStyle({'opacity': 0,'display': 'none'});
     if (c != t) {
         Effect.Fade($$('div.slide')[c], { duration: 1.0 });
     }
     Effect.Appear($$('div.slide')[t], { duration: 1.0 });

	// Set New Current
	c = t;
	// Set New Target
	if (t == ($$('div.slide').length - 1)) {
	    t = 0;
    } else {
	    t = t + 1;
    }
	// Set Timeout
	timeout = setTimeout("gotoNext()", 12000);
}

function gotoStart() {
    // Set Active State on Link
	$$('#tabs a').each(function(link){link.className=''});
	$$('#tabs a')[t].className = 'active';

	// Animate Slides
    $$('div.slide')[t].setStyle({'opacity': 1,'display': 'block'});
     Effect.Appear($$('div.slide')[t], { duration: 0 });

	// Set New Current
	c = t;
	// Set New Target
	if (t == ($$('div.slide').length - 1)) {
	    t = 0;
    } else {
	    t = t + 1;
    }
	// Set Timeout
	timeout = setTimeout("gotoNext()", 12000);
}

document.observe("dom:loaded", slideshow);
function slideshow() {
	$$('div.slide').each(function(e){
        e.setStyle({'opacity': 0,'display': 'none'});
    });
	
    // Start Slide
    timeout = setTimeout("gotoStart()", 0);
    
    function gotoSlide(target) {
        // Set New Target
        t = target;
    	// Animate Slides
        $$('div.slide')[c].setStyle({'opacity': 1});
        $$('div.slide')[t].setStyle({'opacity': 0,'display': 'none'});
         if (c != t) {
             Effect.Fade($$('div.slide')[c], { duration: 1.0 });
         }
         Effect.Appear($$('div.slide')[t], { duration: 1.0 });

    	// Set New Current
    	c = t;
    	// Set New Target
    	if (t == ($$('div.slide').length - 1)) {
    	    t = 0;
        } else {
    	    t = t + 1;
        }
    }

    function stopSlideshow() {
        // Stop Animation
        clearTimeout(timeout);
    }

    function resumeSlideshow() {
    	// Set Timeout
    	timeout = setTimeout("gotoNext()", 12000);
    }

    function updateFeature(e) {
        // Set Active State on Link
    	$$('#tabs a').each(function(link){link.className=''});
    	e.target.className = 'active';
                
        switch ($(e.target).up().className) {
            case 'first':
    		    gotoSlide(0);
    		    break;
            case 'second':
    		    gotoSlide(1);
    		    break;
            case 'third':
    		    gotoSlide(2);
    		    break;
            case 'fourth':
    		    gotoSlide(3);
    		    break;
			case 'fifth':
    		    gotoSlide(4);
    		    break;
			case 'sixth':
    		    gotoSlide(5);
    		    break;
			case 'seventh':
    		    gotoSlide(6);
    		    break;
			case 'eighth':
    		    gotoSlide(7);
    		    break;
    	}	
    }
    
    // Hover Listners
    $$('#tabs a').each(function(e){
        e.observe('click', updateFeature);
    });
}