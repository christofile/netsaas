<?php

add_action( 'widgets_init', create_function( '', 'register_widget( "Widget_Themico_Related_Posts" );' ) );

class Widget_Themico_Related_Posts extends Widget_Themico_Base {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		parent::__construct(
                        '',
			__('Themico Related Posts', THEMICO_DOMAIN), // Name
                        array(
                            'description' => __( 'Will show related posts. This widget will be shown only on single post.', THEMICO_DOMAIN ),
                            ) // Args
		);
	}

        protected function fetchRelatedPosts($args = array(), $post_id = NULL)
        {
            global $wpdb;
            $post_id = (null === $post_id) ? get_the_ID() : intval($post_id) ;
            if (!$post_id) {
                return false;
            }

            $defaults = array(
                'post_type' => get_post_type($post_id),
                'taxonomies' => get_post_taxonomies(), //array('post_tag', 'category'),
                'limit' => 3
            );

            $widget_class = $this->getWidgetClass();

            $args = wp_parse_args($args,  apply_filters($widget_class . '_related_post_type_and_tax', $defaults));

            $in_taxonomies = array();
            foreach ($args['taxonomies'] as $tax) {
                $in_taxonomies[] = '\'' . $tax . '\'';
            }
            $in_taxonomies = implode(',', $in_taxonomies);

            $sql = "SELECT p.ID FROM {$wpdb->prefix}posts p
            JOIN {$wpdb->prefix}term_relationships tr ON (p.ID = tr.object_id)
            JOIN (
            select tr.term_taxonomy_id from {$wpdb->prefix}term_relationships tr
            JOIN {$wpdb->prefix}term_taxonomy tt USING(term_taxonomy_id)
            where tr.object_id = $post_id
            and tt.taxonomy in ({$in_taxonomies})
            ) mt USING (term_taxonomy_id)
            WHERE p.ID != $post_id AND p.post_status = 'publish' AND p.post_type = '{$args['post_type']}' GROUP BY p.ID ORDER BY p.post_date DESC
            LIMIT {$args['limit']};";

            // execute SQL and save result set to the related_cache variable
            $results = $wpdb->get_results($sql);

            return $results;
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
            $widget_class = $this->getWidgetClass();

            $show_widget  = apply_filters($widget_class . '_show_widget', is_single());

            if ($show_widget) {

		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( ! isset( $instance['number'] ) || !$instance['number'] )
			$instance['number'] = 3;

		echo $args['before_widget'];

		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

                $related_posts = $this->fetchRelatedPosts( array('limit' => $instance['number']) );

                if ($related_posts) {
                    global $post;

                    foreach ($related_posts as $related_post) { $post = get_post($related_post->ID); setup_postdata($post); ?>

                        <article class="post">
                            <?php if (has_post_thumbnail()) : ?>
                            <div class="post-media">
                                <a href="<?php the_permalink(); ?>" class="thumbnail">
                                    <?php the_post_thumbnail( apply_filters($widget_class . '_thumb_size', ThemicoCore::IMAGE_SIZE_WIDGET_FEATURED_POSTS)); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                	        <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
	                        <footer class="post-meta">
                                    <?php echo get_the_date('j F Y'); ?>
            	            </footer>
                    	</article>

                    <?php }

                    wp_reset_postdata();
                }

                //wp_reset_postdata();
		echo $args['after_widget'];

            }
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
            $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 3;
            ?>

            <p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', THEMICO_DOMAIN ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

            <p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show:', THEMICO_DOMAIN ); ?></label>
            <input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>

            <?php
	}

}
?>
