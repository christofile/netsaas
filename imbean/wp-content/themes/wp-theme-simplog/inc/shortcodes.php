<?php

function simplog_googlemap($atts, $content = '')
{
     $template_directory_uri = get_template_directory_uri();

     // Hardcoded demo data
     $atts = shortcode_atts(array(
              'apikey' => '',
	      'latitude' => '34.092809',
              'longitude' => '-118.328661',
              'marker_title' => __('Hello!', THEMICO_DOMAIN),
              'marker_image' => $template_directory_uri . '/assets/img/marker.png'
     ), $atts);
     extract($atts);


     $shortcode_unique_id = md5(serialize($atts) . $content);
     $object_name = 'themicoMapMarkerInfo_' . $shortcode_unique_id;

     unset($atts['marker_title'], $atts['marker_image'], $atts['apikey']);
     $map_data_atts = '';
     foreach ($atts as $attr => $val) {
         $map_data_atts .= ' data-' . $attr . '="' . esc_attr( $val ) . '"';
     }

     // Load scripts in footer
     wp_register_script(ThemicoCore::prefix('themico-google-map'), $template_directory_uri . '/assets/js/themico-google-map.js', array('jquery'), '', true);
     $google_loader_url = 'http://www.google.com/jsapi';
     if (!empty($apikey)) {
         $google_loader_url .= '?key=' . $apikey;
     }
     wp_register_script(ThemicoCore::prefix('google-loader'), $google_loader_url, array(), '', true);


     // Order is important!
     wp_enqueue_script(ThemicoCore::prefix('google-loader'));
     wp_enqueue_script(ThemicoCore::prefix('themico-google-map'));

     $object_data = array(
         'title' => $marker_title,
         'image' => $marker_image,
         'content' => $content
     );

     wp_localize_script(ThemicoCore::prefix('themico-google-map'), $object_name, $object_data);

     return '<div class="thumbnail"><div id="' . $object_name . '" class="themico-google-map"' . $map_data_atts . '></div></div>';
}

function simplog_contact($atts, $content = '')
{
     extract(shortcode_atts(array(
              'subject' => __('Contact Form', THEMICO_DOMAIN),
	      'emailto' => get_bloginfo('admin_email'),
              'success_msg' => __('Your email was successfully sent and I will be in touch with you soon.', THEMICO_DOMAIN),
              'error_msg' => __('Please check if you\'ve filled all the fields with valid information.', THEMICO_DOMAIN)
     ), $atts));


     if(isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'themico-contact-form') ) {

         $submit_done = true;

         $name = isset($_POST['contactname']) ? trim($_POST['contactname']) : '' ;
         $email = isset($_POST['email']) ? trim($_POST['email']) : '' ;
         $message = isset($_POST['message']) ? trim(stripslashes($_POST['message'])) : '' ;

         $error = false;


         //Check to make sure required fields are not empty
         foreach (compact('name', 'email', 'message') as $field) {
             if (empty($field)) {
                 $error = true;
                 break;
             }
         }

         if (!$error && !is_email($email)) {
             $error = true;
         }



         if (!$error) {

		$body = "Name: $name \n\nEmail: $email \n\nSubject: $subject \n\nMessage:\n $message";
		$headers = 'From: ' . get_bloginfo('name'). ' <'.$emailto.'>' . "\r\n" . 'Reply-To: ' . $email;
		$email_sent = wp_mail($emailto, $subject, $body, $headers);

                if (!$email_sent)
                    $error = true;
         }



     }

     $output = '<section id="contact">';
     $output .= $content;

     if (isset($submit_done)) {
         if ($error) {
             $output .= '<p class="alert alert-error">' . $error_msg . '</p>';
         } else {
             $output .= '<p class="alert alert-success">' . $success_msg . '</p>';
         }
     }

     $output .= '<form action="' . esc_url(get_permalink() ) . '#contact" method="POST" id="commentform">';
     $output .= wp_nonce_field('themico-contact-form', '_wpnonce', true, false);
     $output .= '<fieldset class="pull-left"><input type="text" id="contactname" name="contactname" placeholder="' .  __('Name', THEMICO_DOMAIN) . '"></fieldset>';
     $output .= '<fieldset><input type="text" id="email" name="email" placeholder="' . __('Email', THEMICO_DOMAIN) . '"></fieldset>';
     $output .= '<fieldset><textarea id="message" name="message" placeholder="' . __('Message', THEMICO_DOMAIN) . '"></textarea></fieldset>';
     $output .= '<input class="btn btn-large" type="submit" id="submit" name="submit" value="' . __('Send Message', THEMICO_DOMAIN) . '">';
     $output .= '</form>';
     $output .= '</section>';

     return $output;

}

add_shortcode('simplog_googlemap', 'simplog_googlemap');
add_shortcode('simplog_contact', 'simplog_contact');

?>