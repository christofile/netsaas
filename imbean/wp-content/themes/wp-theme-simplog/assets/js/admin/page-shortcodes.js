jQuery(document).ready( function($) {

    // Hardcode rocks!
    var template_to_metabox_map = {
        'templates/contact.php' : '#_simplog_contact_template_options_metabox'
    };

    var $page_template = $('select#page_template');

    // Hide metaboxes for rythm shortcode generators
    $('.rythm-shortcode-metabox').parents('.postbox').hide();

    function showShortcodeGenerator()
    {
        $('.rythm-shortcode-metabox').parents('.postbox').hide('slow');

        if ( $page_template.val() in template_to_metabox_map ) {
            $(template_to_metabox_map[$page_template.val()]).show('slow');
        }
    }

    showShortcodeGenerator.call();

    $page_template.change(showShortcodeGenerator)

    function map_and_contact_shortcode()
    {
        var shortcode = '';

        var latitude = $('#rythm-google-map-latitude').val();
        var longitude = $('#rythm-google-map-longitude').val();

        if (latitude && longitude) {

            shortcode += '[simplog_googlemap';
            shortcode += ' latitude="' + latitude + '"';
            shortcode += ' longitude="' + longitude + '"';


            var api_key = $('#rythm-google-map-api-key').val();

            if (api_key) {

                shortcode += ' apikey="' + api_key + '"';

                var marker_title = $('#rythm-google-map-marker-title').val();

                if (marker_title) {
                    shortcode += ' marker_title="' + marker_title + '"';
                }

            }

            shortcode += ']';

            if (api_key) {
                shortcode += 'INSERT MARKER CONTENT HERE';
                shortcode += '[/simplog_googlemap]';
            }

        }

        shortcode += '[simplog_contact'
        var subject = $('#rythm-contact-subject').val();
        var emailto = $('#rythm-contact-emailto').val();
        var success_msg = $('#rythm-contact-success').val();
        var error_msg = $('#rythm-contact-error').val();

        if (subject) {
            shortcode += ' subject="' + subject + '"';
        }
        if (emailto) {
            shortcode += ' emailto="' + emailto + '"';
        }
        if (success_msg) {
            shortcode += ' success_msg="' + success_msg + '"';
        }
        if (error_msg) {
            shortcode += ' error_msg="' + error_msg + '"';
        }

        shortcode += ']';
        shortcode += 'INSERT CONTACT FORM CONTENT HERE';
        shortcode += '[/simplog_contact]';

        return shortcode;
    }

    function insertTextInWpEditor(text)
    {
        // Seems like HTML editor selected so we put the text inside textarea manually
        if (null == tinyMCE.activeEditor || $('#wp-content-wrap').hasClass('html-active')) {
            $('textarea#content').val( $('textarea#content').val() + text );
        } else {
            window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, text);
        }
    }


    $('#insert-contact-and-map').click(function(){
        insertTextInWpEditor(map_and_contact_shortcode());
    })

});