<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package simplog
 * @since simplog 1.0
 */

if ( ! function_exists( 'simplog_comments_pagination' ) ) :

    function simplog_comments_pagination($echo = true)
    {
        $args = array(
           'prev_next' => true,
           'prev_text' => '&laquo;', // This is the WordPress default.
           'next_text' => '&raquo;', // This is the WordPress default.
           'show_all' => true,
           'end_size' => 1,
           'mid_size' => 1,
           'type' => 'array', //list
	   'before' => '<nav class="wp-pagination wp-pagination-comments">', // Begin loop_pagination() arguments.
           'after' => '</nav>',
           'echo' => false,
           'format' => '',
        );

        $navigation_links = paginate_comments_links(apply_filters('simplog_comments_pagination_args', $args));

        $html = apply_filters('simplog_comments_pagination_html', ThemicoCore::filterLoopPagination($navigation_links, $args) );

        if ($echo) {
            echo $html;
        } else {
            return $html;
        }
    }

endif;

if ( ! function_exists( 'simplog_author_social_icons' ) ) :

    function simplog_author_social_icons()
    {
        $profiles = get_user_meta(get_the_author_meta( 'ID' ), 'widget_themico_social_profiles', true);

        $args = array(
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '',
            'after_title' => ''
        );

        if (!empty($profiles)) {
            the_widget('Widget_Themico_Social_Profiles', array( 'profiles' => $profiles ), $args);
        }
    }

endif;

if ( ! function_exists( 'is_simplog_big_post' ) ) :

    function is_simplog_big_post($query = null)
    {
        $result = false;

        if (is_home()) {

            static $big_posts_number = 0;

            if (!$big_posts_number) {
                $simplog_options = get_option('simplog_options');
                $big_posts_number = $simplog_options['home_big_posts'];
            }

            if (null === $query || !($query instanceof WP_Query)  ) {
                global $wp_query;
                $query = $wp_query;
            }

            if ($query->in_the_loop && $query->current_post + 1 <= $big_posts_number) {
                $result = true;
            }

        } else if (is_author()) {
            $result = false; // always small posts on author page
        } else {
            $result = true;
        }

        return $result;
    }

endif;

if ( ! function_exists( 'simplog_post_thumb_size' ) ) :

    function simplog_post_thumb_size()
    {
        $size = ThemicoCore::IMAGE_SIZE_POST_WIDE;

        if (is_sticky() && is_home()) {
            $size = ThemicoCore::IMAGE_SIZE_POST_STICKY;
        } elseif (is_simplog_big_post()) {
            $size = ThemicoCore::IMAGE_SIZE_POST_WIDE;
        } else {
            $size = ThemicoCore::IMAGE_SIZE_POST_SMALL;
        }

        return apply_filters('simplog_post_image_thumb_size', $size);
    }

endif;

if ( ! function_exists( 'simplog_video_content' ) ) :

    /**
     * Returns ready to use content generated using meta data of video post
     *
     * @global type $wp_embed
     * @return string
     */
    function simplog_video_content()
    {
        $post_id = get_the_ID();
        $output = '';

        $video_meta = trim( get_post_meta($post_id, '_format_video_embed', true) );

        global $content_width;

        if (is_sticky($post_id) && is_home()) {
            $content_width = 879;
            //$content_height = 300;
        } else {

            if (is_simplog_big_post()) {
                $content_width = 636;
                //$content_height = 237;
            } else {
                // small post
                $content_width = 280;
                $content_height = 238;
            }
        }

        if ($video_meta) {

            $pattern_url = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
            $pattern_shortcode = '/' . get_shortcode_regex() . '/s';

            if ( ($is_url = preg_match($pattern_url, $video_meta) ) || preg_match($pattern_shortcode, $video_meta) ) {

                do_action('simplog_before_embed_shortcode_run');

                $change_embed_oembed_html_dimensions = create_function('$html, $url, $args', 'if (isset($args[\'width\'])) {$html = preg_replace("/width=\"[0-9]*\"/", \'width="\' . $args[\'width\'] . \'"\', $html);} if (isset($args[\'height\']) && 1000 != $args[\'height\'] ) { $html = preg_replace("/height=\"[0-9]*\"/", \'height="\' . $args[\'height\'] . \'"\', $html); }  return $html;');
                add_filter('embed_oembed_html', $change_embed_oembed_html_dimensions, 10, 3);

                // use oEmbed wordpress
                global $wp_embed;
                $callback = create_function('$output, $url', 'return $url;');
                add_filter('embed_maybe_make_link', $callback, 10, 2);

                if ($is_url) {

                    $default_atts = array();
                    $default_atts['width'] = $content_width;
                    $default_atts['height'] = isset($content_height) ? $content_height : ceil( $content_width * 9 / 16  );
                    $embed_atts = apply_filters('simplog_video_content_embed_shortcode_atts', $default_atts);
                    $atts = '';

                    foreach ($embed_atts as $name => $value) {
                        $atts .= ' ' . $name . '="' . $value . '"';
                    }

                    $output = $wp_embed->run_shortcode('[embed' . $atts . ']' . $video_meta . '[/embed]');
                } else { // shortcode detected
                    $output = $wp_embed->run_shortcode($video_meta);
                }

                remove_filter('embed_maybe_make_link', $callback);
                remove_filter('embed_oembed_html', $change_embed_oembed_html_dimensions);

                // oEmbed provider not found for this URL
//                if ($output == $video_meta) {
//                    $output = '';
//                }

            } else {
                // Raw HTML
                $output = $video_meta;
            }

        }

        return apply_filters('simplog_video_post', $output, $post_id);
    }

endif;

if ( ! function_exists( 'simplog_post_has_more_link' ) ) :

    function simplog_post_has_more_link()
    {
        $id = get_the_ID();
        return  $id && in_array($id, ThemicoCore::$posts_with_more_links);
    }

endif;


if ( ! function_exists( 'simplog_theme_help_tip' ) ) :

function simplog_theme_help_tip($for = '')
{
    if (ThemicoCore::isAdministratorUserLoggedIn()) {

        switch ($for) {

            case 'footer-copyright' :
                ?>
                <div class="row">
                    <div class="span7">
                        <p class="alert clearfix">
                            <?php printf(__('&laquo;Footer Copyright&raquo; widget area generally used in conjuction with simple text widget. You can manage the widgets <a href="%s">here</a>.', THEMICO_DOMAIN), admin_url('widgets.php')); ?>
                            <br />
                            <em class="pull-right"><small><?php esc_html_e('This tip is visible only for site administrators.', THEMICO_DOMAIN); ?></small></em>
                        </p>
                    </div>
                </div>
                <?php
                break;

            case 'header-top-right-corner' :
                ?>

                <div class="row">
                    <div class="span7 pull-right">
                        <p class="alert clearfix">
                            <?php printf(__('&laquo;Header Top Right&raquo; widget area generally used in conjuction with &laquo;Themico Social Profiles&raquo; widget. You can manage the widgets <a href="%s">here</a>.', THEMICO_DOMAIN), admin_url('widgets.php')); ?>
                            <br />
                            <em class="pull-right"><small><?php esc_html_e('This tip is visible only for site administrators.', THEMICO_DOMAIN); ?></small></em>
                        </p>
                    </div>
                </div>

                <?php
                break;

            case 'site-logo' :
                ?>

                <div class="span5 pull-left">
                    <p class="alert clearfix">
                        <?php printf(__('You can manage site logo <a href="%s">here</a>.', THEMICO_DOMAIN), admin_url('themes.php?page=custom-header')); ?>
                        <br />
                        <em class="pull-right"><small><?php esc_html_e('This tip is visible only for site administrators.', THEMICO_DOMAIN); ?></small></em>
                    </p>
                </div>

                <?php
                break;

            case 'footer-sidebar' :
            ?>
            <br />
            <div class="span12">
                <p class="alert clearfix">
                    <?php printf(__('No widgets to display in 3-column &laquo;Footer Sidebar&raquo;. You can manage the widgets <a href="%s">here</a>.', THEMICO_DOMAIN), admin_url('widgets.php')); ?>
                    <br />
                    <em class="pull-right"><small><?php esc_html_e('This tip is visible only for site administrators.', THEMICO_DOMAIN); ?></small></em>
                </p>
            </div>

            <?php
                break;
            default :
                break;
        }

    }
}

endif;

if ( ! function_exists( 'simplog_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since simplog 1.0
 */
function simplog_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
        global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">

                        <header class="comment-meta comment-author vcard commentmetadata">
                            <?php echo get_avatar( $comment, 50 ); ?>

                            <cite class="fn"><?php echo get_comment_author_link(); ?></cite>

                            <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
                                    <?php echo human_time_diff( get_comment_time('U'), current_time('timestamp') ) . ' ago'; ?>
                            </time></a>

                            <?php edit_comment_link( __( '(Edit)', THEMICO_DOMAIN ), ' ' );?>

                            <?php if ($comment->user_id === $post->post_author) : // If current post author is also comment author, make it known visually. ?>
                                <strong class="author-sign">author</strong>
                            <?php endif; ?>

                        </header>

                        <section class="comment-content comment">
                            <?php comment_text(); ?>
                        </section>

                        <?php comment_reply_link( array_merge( $args, array( 'reply_text' => '<span class="reply"></span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>

		</article><!-- #comment-## -->
        </li>
	<?php
}
endif; // ends check for simplog_comment()

if ( ! function_exists( 'simplog_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since simplog 1.0
 */
function simplog_posted_on() {
	printf( __( 'Posted %1$s <span class="byline"> by <span class="author"><a href="%2$s" title="%3$s" rel="author">%4$s</a></span></span>', THEMICO_DOMAIN ),
		esc_html( get_the_date('d F Y') ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'simplog' ), get_the_author() ) ),
		get_the_author()
	);

        $categories_list = get_the_category_list( __( ', ', THEMICO_DOMAIN ) );
        if ( $categories_list && simplog_categorized_blog() ) {
            printf( __(' in category %s', THEMICO_DOMAIN), $categories_list );
        }

        if ( comments_open() || '0' != get_comments_number() ) {

            esc_html_e(' with ', THEMICO_DOMAIN);
            echo '<a href="' . esc_url(get_comments_link()) . '">';
            printf(_n('%d comment', '%d comments', get_comments_number(), THEMICO_DOMAIN), get_comments_number());
            echo '</a>';

        }
        
        if (false) {
            the_tags();
        }
}
endif;

/**
 * Returns true if a blog has more than 1 category
 *
 * @since simplog 1.0
 */
function simplog_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so simplog_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so simplog_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in simplog_categorized_blog
 *
 * @since simplog 1.0
 */
function simplog_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'simplog_category_transient_flusher' );
add_action( 'save_post', 'simplog_category_transient_flusher' );
