jQuery(document).ready(function($){

/* ----- Pretty Photo ----- */

    var linksToImages = "a[href$='.png'], a[href$='.gif'], a[href$='.jpg'], a[href$='.jpeg']";

    $('article ul.slides').each(function(index, value){
        $(linksToImages, this).has('img').addClass('prettyPhoto').attr('rel', 'prettyPhoto[slider' + ++index + ']').prettyPhoto();
    });

    $('article').each(function(index, value){
        $(linksToImages, this).not('.prettyPhoto').has('img').addClass('prettyPhoto').attr('rel', 'prettyPhoto[article' + ++index + ']').prettyPhoto();
    });

/* ----- Main Menu ----- */

	if($().mobileMenu) {
		$('#main-navigation').mobileMenu();
	}

	if($().superfish) {
		$("#main-navigation > ul").superfish({
			delay: 150, // delay on mouseout
	        animation: { height:'show' }, // fade-in and slide-down animation
	        speed: 'fast', // faster animation speed
	        autoArrows: false, // disable generation of arrow mark-up
	        dropShadows: false
		});
	}



/* ----- Carousels & Sliders ----- */

	// default flex parameters
	if($().flexslider) {
		$('.flexslider').flexslider({
			controlNav: true,
			directionNav: false,
			slideshow: false
		});
	}


/* ----- Blog ----- */

	if($().masonry) {
		if( $('#masonry img').size() ) {
			$('#masonry img').each(function(){
				$(this).one("load", function(){
					$('#masonry').masonry({
						itemSelector : '.post'
					});
				}).each(function() {
			    	if(this.complete) $(this).trigger("load");
				});
			});
		} else {
			$('#masonry').masonry({
				itemSelector : '.post'
			});
		}
	}

	$(window).resize(function(){
		$('#masonry').masonry({
			itemSelector : '.post'
		});
	});

	$(".archive-group hgroup").click(function(){
		$(this).toggleClass("open").next().slideToggle(500);
	});



/* ----- Forms ----- */

	if (!Modernizr.input.placeholder){
		$("input, textarea").each(function(){
			if($(this).val()=="" && $(this).attr("placeholder")!=""){
				$(this).val($(this).attr("placeholder"));
				$(this).focus(function(){
					if($(this).val()==$(this).attr("placeholder")) $(this).val("");
				});
				$(this).blur(function(){
					if($(this).val()=="") $(this).val($(this).attr("placeholder"));
				});
			}
		});
	}

});
