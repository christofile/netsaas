<?php

add_action( 'widgets_init', create_function( '', 'register_widget( "Widget_Themico_Latest_Posts" );' ) );

class Widget_Themico_Latest_Posts extends Widget_Themico_Base {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		parent::__construct(
                        '',
			__('Themico Latest Posts', THEMICO_DOMAIN), // Name
                        array(
                            'description' => __( 'Will show the latest posts', THEMICO_DOMAIN ),
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
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( ! isset( $instance['number'] ) )
			$instance['number'] = '5';

		if ( ! $number = absint( $instance['number'] ) )
 			$number = 5;

                $thumburl = isset( $instance['thumburl'] ) ? $instance['thumburl'] : $this->getFullUrlByRel('images/post_thumb.png');

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

                $widget_class = $this->getWidgetClass();

                $args = apply_filters($widget_class . '_query_args',
                    array(
                        'posts_per_page' => $number,
                        'ignore_sticky_posts' => 1
                    )
                );

                $latest = new WP_Query($args); ?>

                    <?php while ($latest->have_posts()) : $latest->the_post(); ?>

                        <dl>
                        	<dt>
                                    <a href="<?php the_permalink(); ?>" class="thumbnail">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail( apply_filters($widget_class . '_thumb_size', ThemicoCore::IMAGE_SIZE_WIDGET_LATEST_POSTS), array('class' => 'post_thumb') ); ?>
                                        <?php else : ?>
                                            <img class="post_thumb" src="<?php echo $thumburl; ?>" />
                                        <?php endif; ?>
                                    </a>
                                </dt>
                        	<dd>
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j F Y'); ?></time>
                        	</dd>
                        </dl>

                    <?php endwhile; ?>

        <?php
                wp_reset_postdata();
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
                $instance['number'] = (int) $new_instance['number'];
                $instance['thumburl'] = isset( $new_instance['thumburl'] ) ? $new_instance['thumburl'] : $this->getFullUrlByRel('images/post_thumb.png');
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance )
        {
            $title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
            $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
            $thumburl = isset( $instance['thumburl'] ) ? $instance['thumburl'] : $this->getFullUrlByRel('images/post_thumb.png');
            $offset = isset( $instance['offset'] ) ? intval( $instance['offset'] ) : 0;

            ?>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', THEMICO_DOMAIN ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show:', THEMICO_DOMAIN ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'thumburl' ) ); ?>"><?php _e( 'Default thumbnail URL:', THEMICO_DOMAIN ); ?></label> <br />
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'thumburl' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumburl' ) ); ?>" type="text" value="<?php echo esc_attr( $thumburl ); ?>" />
            </p>

            <?php
	}

}
?>
