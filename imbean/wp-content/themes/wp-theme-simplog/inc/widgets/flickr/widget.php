<?php
/*-----------------------------------------------------------------------------------*/
/*  Create the widget
/*-----------------------------------------------------------------------------------*/
add_action( 'widgets_init', create_function( '', 'register_widget( "Widget_Themico_Flickr_Photostream" );' ) );

/*-----------------------------------------------------------------------------------*/
/*  Widget class
/*-----------------------------------------------------------------------------------*/
class Widget_Themico_Flickr_Photostream extends Widget_Themico_Base {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

            parent::__construct(
                    '', // use class name as widget unique ID
                    __('Themico Flickr Photostream', THEMICO_DOMAIN), // Name
                    array(
                        'description' => __( 'Will show flickr photostream', THEMICO_DOMAIN ),
                        ) // Args
            );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance )
    {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );
        echo $before_widget;
        if ( ! empty( $title ) )
                echo $before_title . $title . $after_title;
        ?>
        <div class="flickr-items">
            <script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo $instance['count']; ?>&amp;display=<?php echo $instance['display']; ?>&amp;size=s&amp;layout=x&amp;source=user&amp;user=<?php echo $instance['user']; ?>"></script>
        </div>
        <?php
        if (isset($instance['more_link']) && !empty($instance['more_link'])) {
            echo '<a href="' . esc_url($instance['more_link']) . '" class="btn">' . __('More', THEMICO_DOMAIN) . '</a>';
        }
        echo $after_widget;
    }


    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
            $instance = array();
            $instance['title'] = strip_tags( $new_instance['title'] );
            $instance['user'] = strip_tags( $new_instance['user'] );
            $instance['count'] = strip_tags( $new_instance['count'] );
            $instance['display'] = strip_tags( $new_instance['display'] );
            $instance['more_link'] = isset($new_instance['more_link']) ? strip_tags( $new_instance['more_link'] ) : '';
            return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        /* Set up some default widget settings. */
        $defaults = array(
            'title' => __('Photostream', THEMICO_DOMAIN),
            'display' => 'latest',
            'count' => '6',
            'user' => '52617155@N08',
            'more_link' => ''
        );
        $instance = wp_parse_args( (array) $instance, $defaults );

        /* Build our form -----------------------------------------------------------*/
        ?>

        <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
        </p>

        <p>
                <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e('The number of photos to be shown.', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $instance['count']; ?>" />
        </p>

        <p>
                <label for="<?php echo $this->get_field_id( 'user' ); ?>"><?php _e('The user ID', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'user' ); ?>" name="<?php echo $this->get_field_name( 'user' ); ?>" value="<?php echo $instance['user']; ?>" />
        </p>

        <p>
                <label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e('Display the latest uploads or random photos.', THEMICO_DOMAIN) ?></label>

                <p>

                <label>
                    <input type="radio" name="<?php echo $this->get_field_name( 'display' ); ?>" id="<?php echo $this->get_field_id( 'display' ); ?>" <?php checked('latest' == $instance['display']) ?> value="latest"/><?php esc_html_e('Latest', THEMICO_DOMAIN) ?>
                </label>
                <label>
                    <input type="radio" name="<?php echo $this->get_field_name( 'display' ); ?>" <?php checked('random' == $instance['display']) ?> value="random"/><?php esc_html_e('Random', THEMICO_DOMAIN) ?>
                </label>

                </p>

        </p>

        <p>
                <label for="<?php echo $this->get_field_id( 'more_link' ); ?>"><?php _e('More Link', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'more_link' ); ?>" name="<?php echo $this->get_field_name( 'more_link' ); ?>" value="<?php echo $instance['more_link']; ?>" />
        </p>

        <?php
    }

}

?>