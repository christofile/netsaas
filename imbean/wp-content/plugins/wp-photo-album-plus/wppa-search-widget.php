<?php
/* wppa-searchwidget.php
* Package: wp-photo-album-plus
*
* display the search widget
* Version 4.9.10
*
*/

class SearchPhotos extends WP_Widget {
    /** constructor */
    function SearchPhotos() {
        parent::WP_Widget(false, $name = 'Search Photos');	
		$widget_ops = array('classname' => 'wppa_search_photos', 'description' => __( 'WPPA+ Search Photos', 'wppa') );	//
		$this->WP_Widget('wppa_search_photos', __('Search Photos', 'wppa'), $widget_ops);															//
    }

	/** @see WP_Widget::widget */
    function widget($args, $instance) {		
		global $widget_content;
		global $wppa;
		global $wppa_opt;
		global $wpdb;

        extract( $args );

		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Search Photos', 'wppa'), 'label' => '' ) );
        
 		$widget_title = apply_filters('widget_title', $instance['title']);

		// Display the widget
		echo $before_widget;
			
		if ( ! empty( $widget_title ) ) { echo $before_title . $widget_title . $after_title; }
		
		$page = $wppa_opt['wppa_search_linkpage'];
		$iret = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `" . $wpdb->posts . "` WHERE `post_type` = 'page' AND `post_status` = 'publish' AND `ID` = %s", $page));
		if ( ! $iret ) $page = '0';	// Page vanished
		if ( $page == '0' ) {
			echo __a('Please select a search results landing page in Table IX-C1');
		}
		else {
			$pagelink = wppa_dbg_url(get_page_link($page));
?>
			<form id="wppa_searchform" action="<?php echo($pagelink) ?>" method="post" class="widget_search">
				<div>
					<?php echo $instance['label'] ?>
					<input type="text" name="wppa-searchstring" id="wppa_s" value="<?php echo $wppa['searchstring'] ?>" />
					<input id = "wppa_searchsubmit" type="submit" value="<?php _e('Search', 'wppa'); ?>" />
				</div>
			</form>
<?php
		}
		
		echo $after_widget;
    }
	
    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['label'] = $new_instance['label'];
		
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Search Photos', 'wppa'), 'label' => '' ) );
		$title = $instance['title'];
		$label = $instance['label'];
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wppa'); ?></label><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('label'); ?>"><?php _e('Text:', 'wppa');  ?></label><input class="widefat" id="<?php echo $this->get_field_id('label'); ?>" name="<?php echo $this->get_field_name('label'); ?>" type="text" value="<?php echo esc_attr($label) ?>" /></p>
		<small><?php _e('Enter optional text that will appear before the input box. This may contain HTML so you can change font size and color.', 'wppa'); ?></small>
<?php
    }

} // class SearchPhotos

// register SearchPhotos widget
add_action('widgets_init', create_function('', 'return register_widget("SearchPhotos");'));

