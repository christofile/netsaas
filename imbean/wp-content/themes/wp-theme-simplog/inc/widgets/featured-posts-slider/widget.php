<?php
/*-----------------------------------------------------------------------------------*/
/*  Create the widget
/*-----------------------------------------------------------------------------------*/
add_action('widgets_init', create_function( '', 'register_widget( "Widget_Themico_Featured_Posts_Slider" );' ) );

/*-----------------------------------------------------------------------------------*/
/*  Widget class
/*-----------------------------------------------------------------------------------*/
class Widget_Themico_Featured_Posts_Slider extends Widget_Themico_Base {

    public function frontendScripts()
    {
        $settings =  $this->get_settings();
        wp_enqueue_script($this->getWidgetClass() . '_slider', $this->getFullUrlByRel('assets/js/slider.js'), array('jquery'));
        foreach ($settings as $widget_number => $widget_settings ) {
            wp_localize_script($this->getWidgetClass() . '_slider', $this->id_base . '_' . $widget_number , $widget_settings['slider']);
        }
    }

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        $this->_frontend_scripts = true;
        parent::__construct(
                '', // use class name as widget unique ID
                __('Themico Featured Posts Slider', THEMICO_DOMAIN), // Name
                array(
                    'description' => __( 'Will show featured posts inside slider', THEMICO_DOMAIN ),
                )
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
        $title = isset($instance['title']) ? apply_filters( 'widget_title', $instance['title'] ) : '' ;

        echo $before_widget;
        if ( ! empty( $title ) )
                echo $before_title . $title . $after_title;
        if (!empty($instance['posts'])) :

            $args = array(
                'post__in' => $instance['posts'],
                'post_status'     => 'publish',
                'ignore_sticky_posts' => 1
            );

            $widget_class = $this->getWidgetClass();
            $featured_posts = new WP_Query( apply_filters($widget_class . '_query_args' , $args ));

            if ($featured_posts->have_posts()) :
        ?>

            <div class="flexslider">

                    <ul class="slides">

                    <?php while ($featured_posts->have_posts()) : $featured_posts->the_post(); $title = get_the_title(); ?>

                        <?php if (has_post_thumbnail()) : ?>
                	<li>
                            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr($title) ?>">
                                <?php the_post_thumbnail(apply_filters($widget_class . '_thumb_size', ThemicoCore::IMAGE_SIZE_WIDGET_FEATURED_POSTS), array('title' => $title, 'alt' => $title)); ?>
                            </a>
                        </li>
                        <?php endif; ?>

                    <?php endwhile; ?>

                </ul>
            </div>

        <?php
        endif;
        wp_reset_postdata();
        endif;
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
            $instance['slider'] = $new_instance['slider'];
            $instance['title'] = strip_tags( $new_instance['title'] );
            $instance['posts'] = $new_instance['posts'];
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
            'slider' => array(
                'animation' => 'fade',
                'animationSpeed' => 1000,
                'slideshowSpeed' => 500,
                'slideshow' => false
            ),
            'title' => '',
            'posts' => array()
        );

        $instance = wp_parse_args( (array) $instance, $defaults );
        if (!isset($instance['slider']['slideshow'])) {
            $instance['slider']['slideshow'] = false;
        }

        $generate_ms_options = create_function('', '
                        $options = array();
                        for ($i=1; $i<=20; $i++) {
                                $ms = $i*500;
                                $options[$ms] = number_format($ms/1000, 1);
                        }
                        return $options;
        ');

        ?>

        <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
        </p>

        <p>

            <label for="<?php echo $this->get_field_id( 'posts][' ); ?>"><?php esc_html_e('Posts', THEMICO_DOMAIN); ?></label>

                <?php
                    $args = array('numberposts' => '-1', 'post_type' => 'post');
                    $posts = get_posts($args);
                    if (!empty($posts)) :
                ?>

                <select id="<?php echo $this->get_field_id( 'posts][' ); ?>" name="<?php echo $this->get_field_name( 'posts][' ); ?>" multiple="multiple" class="widefat">
                    <?php foreach ( $posts as $post ) : ?>
                    <option <?php selected(in_array($post->ID, $instance['posts'])) ?> value="<?php echo esc_attr( $post->ID ); ?>"><?php echo esc_html( $post->post_title ); ?></option>
                    <?php endforeach; ?>
                </select>

                <?php else : ?>
                    <?php printf(__('Please add a couple of posts <a href="%s">here</a> to use them inside slider.', THEMICO_DOMAIN), admin_url('post-new.php')); ?>
                <?php endif; ?>
        </p>

        <p>
            <label><?php esc_html_e('Slider Options', THEMICO_DOMAIN); ?></label>

            <hr />

            <label><?php esc_html_e('Animation', THEMICO_DOMAIN); ?></label>
            <p>
                <select class="widefat" name="<?php echo $this->get_field_name( 'slider][animation' ); ?>">
                    <option <?php selected('fade' == $instance['slider']['animation'] ) ?>  value="fade"><?php esc_html_e('Fade', THEMICO_DOMAIN); ?></option>
                    <option <?php selected('slide' == $instance['slider']['animation'] ) ?>  value="slide"><?php esc_html_e('Slide', THEMICO_DOMAIN); ?></option>
                    <option <?php selected('show' == $instance['slider']['animation'] ) ?>  value="show"><?php esc_html_e('Show', THEMICO_DOMAIN); ?></option>
                </select>
            </p>

            <label><?php esc_html_e('Animation Speed', THEMICO_DOMAIN); ?></label>
            <p>
                <select class="widefat" name="<?php echo $this->get_field_name( 'slider][animationSpeed' ); ?>">
                    <?php foreach(call_user_func($generate_ms_options) as $ms => $number) : ?>
                        <option <?php selected($ms == $instance['slider']['animationSpeed'] ) ?> value="<?php echo esc_attr($ms); ?>"><?php echo esc_html($number); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>

            <label><?php esc_html_e('Slideshow Speed', THEMICO_DOMAIN); ?></label>
            <p>
                <select  class="widefat" name="<?php echo $this->get_field_name( 'slider][slideshowSpeed' ); ?>">
                    <?php foreach(call_user_func($generate_ms_options) as $ms => $number) : ?>
                        <option <?php selected($ms == $instance['slider']['slideshowSpeed'] ) ?> value="<?php echo esc_attr($ms); ?>"><?php echo esc_html($number); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>

            <label><input type="checkbox" <?php checked(true == $instance['slider']['slideshow'] ); ?> value="1" name="<?php echo $this->get_field_name( 'slider][slideshow' ); ?>"/><?php esc_html_e('Play Slideshow Automatically', THEMICO_DOMAIN); ?></label>

        </p>

        <?php
    }

}

?>