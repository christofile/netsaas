jQuery(document).ready(function($){

    if($().flexslider) {

            $('.widget_themico_featured_posts_slider > .flexslider').each(function(){

                var object_name = $(this).closest('.widget').attr('id').replace('-', '_');

                if (object_name !== undefined &&  object_name in window) {

                    var options = $.extend({}, {
                        controlNav: true,
                        directionNav: false
                    }, window[object_name]);

                    $(this).flexslider(options);
                }

            });
	}

});