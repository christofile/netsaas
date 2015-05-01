<?php
/**
 * simplog functions and definitions
 *
 * @package simplog
 * @since simplog 1.0
 */

/**
 * Domain name for translation strings
 */
define('THEMICO_DOMAIN', 'simplog');

// fontend class name
$themico_core_class = 'ThemicoCore';

if (!class_exists($themico_core_class)) {

    class ThemicoCore
    {
        /*
         * This magic number used as 'limit' argument for wp_get_archives() function for later catching the SQL query used for fetching archives by specified 'type' in wp_get_archives() function
         */
        const MAGIC_NUMBER_WP_GET_ARCHIVES_LIMIT = 1234567;

        const IMAGE_SIZE_POST_STICKY = 'post-sticky';
        const IMAGE_SIZE_POST_WIDE = 'post-wide';
        const IMAGE_SIZE_POST_SMALL = 'post-small';
        const IMAGE_SIZE_WIDGET_LATEST_POSTS = 'widget-latest-posts';
        const IMAGE_SIZE_WIDGET_FEATURED_POSTS = 'widget-featured-posts';
        const IMAGE_SIZE_WIDGET_POPULAR_POSTS = 'widget-popular-posts';


        const MENU_LOCATION_TOP_BOTTOM = 'top-bottom-menu';
        const MENU_LOCATION_MAIN = 'main-menu';

        static $posts_with_more_links = array();

        /**
         * Catched archives SQL query
         *
         * @var string
         */
        private static $_archives_sql = '';

        /**
         * Archive options on page with template 'template/archive.php'
         *
         * @var array
         */
        private static $_archives_options = array();

        public static function getArchivesPageOptions()
        {
            return self::$_archives_options;
        }

        /**
         * Sets the post excerpt length
         *
         * To override this length in a child theme, remove the filter and add your own
         * function tied to the excerpt_length filter hook.
         *
         * @return int
         */
        public static function filterExcerptLength($length)
        {
            return $length;
        }

        /**
         * Returns a "Continue Reading" link for excerpts
         *
         * @return string "Continue Reading" link
         */
        public static function getContinueReadingLink() {
            return '<p class="more-link"><a class="btn" href="'. get_permalink() . '">' . __( 'Read more', THEMICO_DOMAIN ) . '</a></p>';
        }

        /**
         * Adds a pretty "Continue Reading" link to custom post excerpts.
         *
         * To override this link in a child theme, remove the filter and add your own
         * function tied to the get_the_excerpt filter hook.
         *
         * @return string Excerpt with a pretty "Continue Reading" link
         */
        public static function filterExcerpt($output)
        {
            if ( has_excerpt() && ! is_attachment() ) {
                    $output .= self::getContinueReadingLink();
            }
            return $output;
        }

        /**
         * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and boilerplate_continue_reading_link().
         *
         * To override this in a child theme, remove the filter and add your own
         * function tied to the excerpt_more filter hook.
         *
         * @return string An ellipsis
         */
        public static function filterExcerptMore($more)
        {
            return ' &hellip;' . self::getContinueReadingLink();
        }

        public static function isCurrentPostInLoopHasMoreLink()
        {

        }

        public static function filterContentMoreLink($link)
        {
            self::$posts_with_more_links[] = get_the_ID();
            return self::getContinueReadingLink();
        }


        public static function getMenuDescrBySlug($slug)
        {
            $descriptions = array(
                self::MENU_LOCATION_MAIN => __( 'Main Menu', THEMICO_DOMAIN ),
                self::MENU_LOCATION_TOP_BOTTOM => __( 'Top And Bottom Menu', THEMICO_DOMAIN ),
            );

            return isset($descriptions[$slug]) ? $descriptions[$slug] : '' ;
        }


        public static function _filterArchivesQuery($query)
        {
            $archives_query = str_replace('LIMIT ' . self::MAGIC_NUMBER_WP_GET_ARCHIVES_LIMIT, '%s', $query);

            if ($archives_query != $query) {
                self::$_archives_sql = $archives_query;
                $query = str_replace('%s', 'LIMIT 1', $archives_query);
            }

            return $query;
        }

        public static function _filterArchivesPostsRequest($request, WP_Query $query)
        {
            if ($query->is_main_query()) {

                $sections = isset(self::$_archives_options['sections']) ? absint(self::$_archives_options['sections']) : 0 ;

                if ($sections) {

                    $query->set('posts_per_page', $sections);

                    // archives pagination
                    if (  !isset($query->query_vars['offset']) || empty($query->query_vars['offset']) ) {
                        $paged = max ( absint( get_query_var('paged') ), 1 );
                        $pgstrt = ($paged - 1) * $query->query_vars['posts_per_page'] . ', ';
                    } else {
                        $pgstrt = 0 . ', ';
                    }
                    $limits = 'LIMIT ' . $pgstrt . $query->query_vars['posts_per_page'];
                } else {
                    $limits = '';
                    $query->set('nopaging', true);
                    $query->set('posts_per_page', -1);
                }

                $request = str_replace(array('SELECT ', '%s'), array('SELECT SQL_CALC_FOUND_ROWS ', $limits), self::$_archives_sql);
                remove_filter(current_filter(), array(get_class(), __FUNCTION__));
            }

            return $request;

        }


        public static function filterPreGetPosts(WP_Query $query)
        {
            if ( $query->is_main_query()) {

                if ($query->is_home()) {
                    // exclude sticky posts
                    $query->set('post__not_in', get_option( 'sticky_posts' ));
                } elseif (is_page_template('templates/archive.php')) {

                    add_filter('pre_option_show_on_front', create_function('$value', 'return \'page\';'));
                    add_filter('pre_option_page_for_posts', create_function('$value', 'return get_queried_object_id();'));
                    add_filter('template_include', create_function('$template', 'return get_template_directory() . \'/\' . get_post_meta(get_queried_object_id(), \'_wp_page_template\', true );'));

                    /**
                     * HACK
                     * $query->is_singular = false; Alternative way but not clean
                     *
                     * increases sql requests at least +1 by calling  get_page_by_path() inside parse_query()
                     */
                     $query->parse_query();
                     $query->is_page = true;
                     $query->is_home = false;

                    $archive_options = get_post_meta(get_queried_object_id(), '_' . self::prefix('archive_template_options'), true);

                    $allowed_types = array('yearly', 'monthly', 'weekly', 'daily');
                    if (!$archive_options) {
                        $archive_options = array();
                    }

                    // Options validation and defaults
                    $archive_options['type'] = isset($archive_options['type']) && in_array($archive_options['type'], $allowed_types) ? $archive_options['type'] : 'monthly';
                    $archive_options['sections'] = isset($archive_options['sections']) ? absint($archive_options['sections']) : 0;
                    $archive_options['posts'] = isset($archive_options['posts']) ? absint($archive_options['posts']) : 0;

                    self::$_archives_options = $archive_options;

                    /*
                     * XXX This hack used for catching archives SQL query generated by wp_get_archives() function.
                     * To be sure that we catched required query magic number was used.
                     * Catched query will be available in self::$_archives_sql variable
                     */
                    $cache = wp_cache_get('wp_get_archives', 'general');


                    // If archive cache is not empty then clear it to force SQL query
                    if ($cache) {
                        wp_cache_delete('wp_get_archives', 'general');
                    }
                    $log_archives_sql = array(get_class(), '_filterArchivesQuery');
                    add_filter('query', $log_archives_sql);
                    // Magic number used for detecting archives query
                    wp_get_archives(array('type' => $archive_options['type'], 'limit' => self::MAGIC_NUMBER_WP_GET_ARCHIVES_LIMIT, 'echo' => false));
                    remove_filter('query', $log_archives_sql);

                    // Restore previously cached data
                    if ($cache) {
                        $new_cache = array_merge( $cache, (array) wp_cache_get('wp_get_archives', 'general'));
                        wp_cache_set('wp_get_archives', $new_cache, 'general');
                    }
                    /** HACK END**/
                    add_filter( 'posts_request', array(get_class(), '_filterArchivesPostsRequest'), 10, 2 );
                }
            }

            return $query;
        }


        /**
         * Makes some changes to the <title> tag, by filtering the output of wp_title().
         *
         * If we have a site description and we're viewing the home page or a blog posts
         * page (when using a static front page), then we will add the site description.
         *
         * If we're viewing a search result, then we're going to recreate the title entirely.
         * We're going to add page numbers to all titles as well, to the middle of a search
         * result title and the end of all other titles.
         *
         * The site title also gets added to all titles.
         *
         * @param string $title Title generated by wp_title()
         * @param string $separator The separator passed to wp_title().
         * @param string $seplocation Separator location passed to wp_title().
         * @return string The new title, ready for the <title> tag.
         */
        public static function filterWpTitle($title, $separator, $seplocation)
        {
            // Don't affect wp_title() calls in feeds.
            if ( is_feed() )
                    return $title;

            // The $paged global variable contains the page number of a listing of posts.
            // The $page global variable contains the page number of a single post that is paged.
            // We'll display whichever one applies, if we're not looking at the first page.
            global $paged, $page;

            if ( is_search() ) {
                    // If we're a search, let's start over:
                    $title = sprintf( __( 'Search results for %s', THEMICO_DOMAIN ), '"' . get_search_query() . '"' );
                    // Add a page number if we're on page 2 or more:
                    if ( $paged >= 2 )
                            $title .= " $separator " . sprintf( __( 'Page %s', THEMICO_DOMAIN ), $paged );
                    // Add the site name to the end:
                    $title .= " $separator " . get_bloginfo( 'name', 'display' );
                    // We're done. Let's send the new title back to wp_title():
                    return $title;
            }

            // Otherwise, let's start by adding the site name to the end:
            $title .= get_bloginfo( 'name', 'display' );

            // If we have a site description and we're on the home/front page, add the description:
            $site_description = get_bloginfo( 'description', 'display' );
            if ( $site_description && ( is_home() || is_front_page() ) )
                    $title .= " $separator " . $site_description;

            // Add a page number if necessary:
            if ( $paged >= 2 || $page >= 2 )
                    $title .= " $separator " . sprintf( __( 'Page %s', THEMICO_DOMAIN ), max( $paged, $page ) );

            // Return the new title to wp_title():
            return $title;
        }

        /**
         * Add Support for locale changing on the fly. Must be called before load_theme_textdomain()
         */
        public static function filterLocale($locale)
        {
            return isset($_GET["l"]) ? $_GET["l"] : $locale;
        }

        public static function filterImageSizeNamesChoose($size_names)
        {
            //$size_names[self::IMAGE_SIZE_PORTFOLIO_ITEM] = __('Portfolio image', THEMICO_DOMAIN);
            //$size_names[self::IMAGE_SIZE_TEAM_ITEM] = __('Team image', THEMICO_DOMAIN);
            return $size_names;
        }

        /**
         * Sets up theme defaults and registers support for various WordPress features.
         *
         * Note that this function is hooked into the after_setup_theme hook, which runs
         * before the init hook. The init hook is too late for some features, such as indicating
         * support post thumbnails.
         *
         * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
         * @uses register_nav_menus() To add support for navigation menus.
         * @uses add_custom_background() To add support for a custom background.
         * @uses add_editor_style() To style the visual editor.
         * @uses load_theme_textdomain() For translation/localization support.
         * @uses add_custom_image_header() To add support for a custom header.
         * @uses register_default_headers() To register the default custom header images provided with the theme.
         * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
         *
         */
        public static function setup()
        {

            /**
             * Custom template tags for this theme.
             */
            require( get_template_directory() . '/inc/template-tags.php' );

            /**
             * This theme uses wp_nav_menu() in one location.
             */
            register_nav_menus( array(
                    self::MENU_LOCATION_TOP_BOTTOM => self::getMenuDescrBySlug(self::MENU_LOCATION_TOP_BOTTOM),
                    self::MENU_LOCATION_MAIN => self::getMenuDescrBySlug(self::MENU_LOCATION_MAIN)
            ) );

            /**
             * Make theme available for translation
             * Translations can be filed in the /languages/ directory
             * If you're building a theme based on simplog, use a find and replace
             * to change 'simplog' to the name of your theme in all the template files
             */
            load_theme_textdomain(THEMICO_DOMAIN, get_template_directory() . "/languages");

            $locale = get_locale();
            $locale_file = get_template_directory() . "/languages/$locale.php";
            if ( is_readable( $locale_file ) )
                    require_once( $locale_file );

            // This theme styles the visual editor with editor-style.css to match the theme style.
            //add_editor_style(); TODO Add editors style

            /**
             * Enable support for Post Thumbnails
             */
            add_theme_support( 'post-thumbnails' );

            add_post_type_support( 'page', 'post-formats' );

            add_theme_support( 'post-formats', array( 'image', 'video', 'gallery' ) );

            add_image_size( self::IMAGE_SIZE_POST_STICKY, '879', '300', true );
            add_image_size( self::IMAGE_SIZE_POST_WIDE, '636', '237', true );
            add_image_size( self::IMAGE_SIZE_POST_SMALL, '280', '238', true );
            add_image_size( self::IMAGE_SIZE_WIDGET_LATEST_POSTS, '43', '43', true );
            add_image_size( self::IMAGE_SIZE_WIDGET_FEATURED_POSTS, '160', '140', true );
            add_image_size( self::IMAGE_SIZE_WIDGET_POPULAR_POSTS, '51', '51', true);

            /**
             * Add default posts and comments RSS feed links to head
             */
            add_theme_support( 'automatic-feed-links' );
        }

        public static function printScripts()
        {
        }

        public static function printStyles()
        {
        }

        public static function printFooterScripts()
        {
        }

        public static function loadScripts()
        {

            if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                    wp_enqueue_script( 'comment-reply' );
            }

            if ( is_singular() && wp_attachment_is_image() ) {
                    wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/assets/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
            }

            $template_directory_uri = get_template_directory_uri();

            wp_enqueue_script( self::prefix('modernizr.custom'), $template_directory_uri . '/assets/js/modernizr.custom.js');
            wp_enqueue_script('jquery');
            wp_enqueue_script( self::prefix('prettyPhoto'), $template_directory_uri . '/assets/js/jquery.prettyPhoto.js', array('jquery'), '', true);
            wp_enqueue_script( self::prefix('flexslider'), $template_directory_uri . '/assets/js/jquery.flexslider-min.js', array('jquery'), '', true);
            wp_enqueue_script( self::prefix('mobilemenu'), $template_directory_uri . '/assets/js/jquery.mobilemenu.js', array('jquery'), '', true);
            wp_enqueue_script( self::prefix('masonry'), $template_directory_uri . '/assets/js/jquery.masonry.min.js', array('jquery'), '', true);
            wp_enqueue_script( self::prefix('superfish'), $template_directory_uri . '/assets/js/superfish.js', array('jquery'), '', true);
            wp_enqueue_script( self::prefix('main'), $template_directory_uri . '/assets/js/main.js', array('jquery'), '', true);

        }

        public static function loadStyles()
        {
            $template_directory_uri = get_template_directory_uri();
            wp_enqueue_style( self::prefix('prettyPhoto'), $template_directory_uri . '/assets/css/prettyPhoto.css');
            wp_enqueue_style( self::prefix('bootstrap.min'), $template_directory_uri . '/assets/css/bootstrap.css');
            wp_enqueue_style( self::prefix('flexslider'), $template_directory_uri . '/assets/css/flexslider.css');
            wp_enqueue_style( self::prefix('style'), get_stylesheet_uri());
        }

        /**
         * Adds two classes to the array of body classes.
         * The first is if the site has only had one author with published posts.
         * The second is if a singular post being displayed
         */
        public static function filterBodyClasses( $classes )
        {
            if ( function_exists( 'is_multi_author' ) && ! is_multi_author() )
                    $classes[] = 'single-author';
            if ( is_singular() && ! is_home())
                    $classes[] = 'singular';

            return $classes;
        }

        public static function prefix($str)
        {
            return THEMICO_DOMAIN . '_' . $str;
        }

        public static function widgetsInit()
        {
            register_sidebar( array(
                    'name' => __( 'Right Sidebar', THEMICO_DOMAIN ),
                    'id' => 'right-sidebar',
                    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                    'after_widget' => "</aside>", // 'after_widget' => "</div></aside>",
                    'before_title' => '<h4 class="widget-title">',
                    'after_title' => '</h4>', // 'after_title' => '</h4><div class="widget-content">',
            ) );

            register_sidebar( array(
                    'name' => __( 'Header > Top Right Corner', THEMICO_DOMAIN ),
                    'id' => 'header-top-right-corner',
                    'before_widget' => '',
                    'after_widget' => '',
                    'before_title' => '<h4 class="widget-title">',
                    'after_title' => '</h4>',
            ) );

            register_sidebar( array(
                    'name' => __( 'Footer', THEMICO_DOMAIN ),
                    'id' => 'footer-sidebar',
                    'before_widget' => '<aside id="%1$s" class="widget span4 %2$s">',
                    'after_widget' =>  '</aside>',
                    'before_title' => '<h4 class="widget-title">',
                    'after_title' => '</h4>',
            ) );

            register_sidebar( array(
                    'name' => __( 'Footer Copyright', THEMICO_DOMAIN ),
                    'id' => 'footer-copyright',
                    'before_widget' => '',
                    'after_widget' => '',
                    'before_title' => '<h4 class="widget-title">',
                    'after_title' => '</h4>',
            ) );
        }

        public static function loadWidgets()
        {
            $template_directory = get_template_directory();
            require_once $template_directory . '/inc/widgets/class-widget-themico-base.php';
            require_once $template_directory . '/inc/widgets/latest-posts/widget.php';
            require_once $template_directory . '/inc/widgets/flickr/widget.php';
            require_once $template_directory . '/inc/widgets/social-profiles/widget.php';
            require_once $template_directory . '/inc/widgets/featured-posts-slider/widget.php';
            require_once $template_directory . '/inc/widgets/most-popular/widget.php';
            require_once $template_directory . '/inc/widgets/tweets/widget.php';
            require_once $template_directory . '/inc/widgets/related-posts/widget.php';

            // Work around for changing the html markup of archives widget links
            add_filter('widget_archives_args', array(get_class(), 'filterWidgetArchivesArgs' ));

            // Work around for adding a link to "all archives" page at the end of the archives widget
            add_filter('dynamic_sidebar_params', array(get_class(), 'filterDynamicSidebarParams'));

        }

        public function filterDynamicSidebarParams($params)
        {
            if ( 'archives' == substr($params[0]['widget_id'], 0, 8) ) {

                // get_page_templates
                $args = array(
                    'meta_key' => '_wp_page_template',
                    'meta_value' => 'templates/archive.php',
                    'sort_column' => 'menu_order',
                    'sort_order' => 'desc'
                );

                $archive_pages = get_pages($args);

                if ($archive_pages) {

                    // Get the first page sorted by menu order
                    $first_page = current($archive_pages);

                    $params[0]['after_widget'] = '<a href="' . get_permalink($first_page->ID) . '" class="btn">' . esc_html__('See all Archives', THEMICO_DOMAIN) . '</a>' . $params[0]['after_widget'];
                }
            }

            return $params;
        }


        public static function filterWidgetArchivesArgs($args)
        {
            if ('monthly' == $args['type']) {
                add_filter('get_archives_link', array(get_class(), 'filterArchivesLink'));
            }
            return $args;
        }

        public static function filterArchivesLink($link_html)
        {
            $link_html = preg_replace('#^(.*)([\d]{4})(\</a\>\</li\>)$#', "$1<span>$2</span>$3", $link_html);
            return $link_html;
        }


        public static function filterPostClass($classes)
        {
            static $j = 0;
            $classes[] = (++$j % 2 == 0) ? 'even' : 'odd';

            if (!is_singular()) {
                $classes[] = 'inner';
            }

            // hack which allows to apply hardcoded css styles also on pages
            $classes[] = 'post';

            if (is_home()) {

                if (is_sticky()) {
                    $classes[] = 'post-size-full';
                } else {

                    if (is_simplog_big_post()) {
                        $classes[] = 'post-size-big';
                    } else {
                        $classes[] = 'post-size-small';
                    }

                }
            } elseif (is_author()) {
                $classes[] = 'post-size-small';
            } else {
                $classes[] = 'post-size-big';
            }

            return $classes;
        }

        public static function isAdministratorUserLoggedIn()
        {
            return is_user_logged_in() && current_user_can('administrator');
        }

        public static function runMenuFallbackIfNoItems($items, $args)
        {
            if ( ($args->theme_location == self::MENU_LOCATION_TOP_BOTTOM  || $args->theme_location == self::MENU_LOCATION_MAIN ) && empty($items) && is_callable($args->fallback_cb)) {
                call_user_func( $args->fallback_cb, (array) $args );
            }

            return $items;
        }

        public static function filterLoopPagination($page_links, $args)
        {
            $pager_links = array();

            $prev_link = current($page_links);
            end($page_links);
            $next_link = current($page_links);

            if (false !== strpos($prev_link, 'prev page-numbers')) {
                $pager_links['prev'] = $prev_link;
                array_shift($page_links); // remove prev link from pagination
            }
            if (false !== strpos($next_link, 'next page-numbers')) {
                $pager_links['next'] = $next_link;
                array_pop($page_links);
            }

            $r = '';
            $r .= '<ul class="pager">' . "\n\t<li>";
            $r .= join("</li>\n\t<li>", $pager_links);
            $r .= "</li>\n</ul>\n";
            $r .= '<div class="pagination">';

            $r .= "<ul class='page-numbers'>\n\t";
            // page-numbers current
            foreach ($page_links as $page_link) {

                if (!isset($current_found) && false !== strpos($page_link, 'page-numbers current')) {
                    $curent_found = true;
                    $r .= '<li class="active"><a href="#">' . strip_tags($page_link) . '</a></li>';
                } else {
                    $r .= '<li>' . $page_link . '</li>';
                }

            }
            $r .= "</ul>\n";
            $r .= '</div>';
            $r = $args['before'] . $r . $args['after'];

            return $r;
        }

        public static function filterLoopPaginationArgs($args)
        {
            return array(
		'format' => '',
		'prev_next' => true,
		'prev_text' => '&laquo;', // This is the WordPress default.
		'next_text' => '&raquo;', // This is the WordPress default.
		'show_all' => true,
		'end_size' => 1,
		'mid_size' => 1,
		'add_fragment' => '',
		'type' => 'array', //list
		'before' => '<nav class="inner wp-pagination">', // Begin loop_pagination() arguments.
		'after' => '</nav>',
		'echo' => true,
            );
        }

        public static function filterGetAvatar($avatar, $id_or_email, $size, $default, $alt)
        {
            return str_replace("class='avatar", "class='thumbnail avatar", $avatar);
        }

        public static function emptyMenuFallback($args)
        {
            if (self::isAdministratorUserLoggedIn()) :

                $before = '';
                $after = '';

                switch ($args['theme_location']) {
                    case self::MENU_LOCATION_TOP_BOTTOM:
                        $before = '<nav id="header-navigation"><div class="container"><div class="span6 pull-left">';
                        $after = '</div></div></nav>';
                        break;

                    case self::MENU_LOCATION_MAIN :
                        $before = '<br /><br /><br /><div class="row"> <div class="span12">';
                        $after = '</div></div>';
                        break;

                    default:
                        break;
                }
            ?>

                <?php echo $before; ?>
                <p class="alert clearfix">
                    <?php printf(__('This is &laquo;%s&raquo; location. You can manage the menu <a href="%s">here</a>.', THEMICO_DOMAIN), self::getMenuDescrBySlug($args['theme_location']), admin_url('nav-menus.php')); ?>
                    <br />
                    <em class="pull-right"><small><?php esc_html_e('This tip is visible only for site administrators.', THEMICO_DOMAIN); ?></small></em>
                </p>
                <?php echo $after; ?>

            <?php
            endif;
        }

        public static function filterEmbedOembedHtml($html, $url, $attr, $post_ID)
        {
            $return = '<div class="embed-container">'.$html.'</div>';
            return $return;
        }

        public static function filterCommentFormDefaults($defaults)
        {
            $commenter = wp_get_current_commenter();

            $req = get_option( 'require_name_email' );
            $aria_req = ( $req ? " aria-required='true'" : '' );

            $defaults['fields']['author'] = '<fieldset class="pull-left">' . '<input id="author" placeholder="' . __('Name', THEMICO_DOMAIN) . '" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $aria_req . ' /></fieldset>';
            $defaults['fields']['email'] = '<fieldset>' . '<input id="email" name="email" placeholder="' . __('Email', THEMICO_DOMAIN) . '" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '"' . $aria_req . ' /></fieldset>';
            unset($defaults['fields']['url']);
            $defaults['title_reply'] = __('Submit a Comment', THEMICO_DOMAIN);
            $defaults['comment_field'] = '<fieldset><textarea placeholder="' . _x( 'Comment', 'noun', THEMICO_DOMAIN ) . '" id="comment" name="comment" aria-required="true"></textarea></fieldset>';
            $defaults['comment_notes_before'] = '<p class="comment-notes">' . __( 'We would love to hear your thoughts. Feel free to submit a comment and join the conversation!', THEMICO_DOMAIN ) . '</p>';
            $defaults['label_submit'] = __('Submit Comment', THEMICO_DOMAIN);

            return $defaults;
        }

        /**
         * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
         *
         * @since simplog 1.0
         */
        public static function filterWpPageMenuArgs($args)
        {
            $args['show_home'] = true;
            return $args;
        }

    }
}

/*-----------------------------------------------------------------------------------*/
/*	Theme Setup
/*-----------------------------------------------------------------------------------*/
add_action( 'after_setup_theme', array($themico_core_class, 'setup') );

/*-----------------------------------------------------------------------------------*/
/*	Misc
/*-----------------------------------------------------------------------------------*/
add_filter('locale', array($themico_core_class, 'filterLocale')); // add support for changing locale on the fly by passing locale in GET parameter @example http://site.com?l=en_US
add_filter('body_class', array($themico_core_class, 'filterBodyClasses')); // add extra body classes
add_action('widgets_init', array($themico_core_class, 'widgetsInit')); // add widget areas
ThemicoCore::loadWidgets(); // load widget classes
//add_filter('widget_text', 'do_shortcode'); // Use shortcodes in text widgets.
add_filter( 'wp_title', array($themico_core_class, 'filterWpTitle'), 10, 3 );

add_filter ( 'post_class' , array($themico_core_class, 'filterPostClass'));
// Run menu fallback_cb if no items to display
add_filter('wp_nav_menu_objects', array($themico_core_class, 'runMenuFallbackIfNoItems'), 10, 2);
add_filter('loop_pagination_args', array($themico_core_class, 'filterLoopPaginationArgs'));
add_filter('loop_pagination', array($themico_core_class, 'filterLoopPagination'), 10, 2);
add_filter('get_avatar', array($themico_core_class, 'filterGetAvatar'), 10, 5);
add_filter('comment_form_defaults', array($themico_core_class, 'filterCommentFormDefaults' ));
add_filter( 'embed_oembed_html', array($themico_core_class, 'filterEmbedOembedHtml'), 10, 4 ) ;
add_filter( 'wp_page_menu_args', array($themico_core_class, 'filterWpPageMenuArgs') );

//return apply_filters('get_avatar', $avatar, $id_or_email, $size, $default, $alt);

/*-----------------------------------------------------------------------------------*/
/*	Alter Main Query
/*-----------------------------------------------------------------------------------*/
add_action('pre_get_posts', array($themico_core_class, 'filterPreGetPosts'));

/*-----------------------------------------------------------------------------------*/
/*	Images
/*-----------------------------------------------------------------------------------*/
add_filter('image_size_names_choose', array($themico_core_class, 'filterImageSizeNamesChoose')); // show added by add_image_size() function sizes in media library

/*-----------------------------------------------------------------------------------*/
/*	Scripts And Styles
/*-----------------------------------------------------------------------------------*/
// print inline css and javascript
add_action('wp_print_scripts', array($themico_core_class, 'printScripts'));
add_action('wp_print_styles', array($themico_core_class, 'printStyles'));
// load external styles and scripts
add_action('wp_enqueue_scripts', array($themico_core_class, 'loadScripts'));
add_action('wp_enqueue_scripts', array($themico_core_class, 'loadStyles'));
// print footer scripts
//add_action('wp_print_footer_scripts', array($themeclass,'printFooterScripts'));

/*-----------------------------------------------------------------------------------*/
/*	Excerpt And Read More Link Settings
/*-----------------------------------------------------------------------------------*/
add_filter( 'excerpt_length', array($themico_core_class, 'filterExcerptLength') );
add_filter( 'get_the_excerpt', array($themico_core_class ,'filterExcerpt') );
add_filter( 'excerpt_more', array($themico_core_class, 'filterExcerptMore') );
add_filter( 'the_content_more_link', array($themico_core_class, 'filterContentMoreLink'));


/*-----------------------------------------------------------------------------------*/
/*	Content Width
/*-----------------------------------------------------------------------------------*/

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( !isset($content_width) ) $content_width = 640;

/**
 * Implement the Custom Header feature
 */
require( get_template_directory() . '/inc/custom-header.php' );
