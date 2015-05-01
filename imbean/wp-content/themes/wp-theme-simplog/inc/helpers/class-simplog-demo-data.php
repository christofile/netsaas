<?php

class Simplog_Demo_Data
{
    private static $_featured_posts = array();

    private static $_user = '';

    public static function importStart()
    {
        self::setupPermalinks();

        // Remove previosly assigned menus before starting the import
        wp_delete_nav_menu(ThemicoCore::MENU_LOCATION_MAIN);
        wp_delete_nav_menu(ThemicoCore::MENU_LOCATION_TOP_BOTTOM);

        update_option('posts_per_page', 5);
        $simplog_options = get_option('simplog_options', array());
        $simplog_options['home_big_posts'] = 3;
        $simplog_options['home_small_posts'] = 2;
        update_option('simplog_options', $simplog_options);

        global $wpdb;
        $wpdb->query("UPDATE {$wpdb->posts} SET post_status = 'draft' WHERE post_status = 'publish'");

        foreach ($GLOBALS['wp_import']->posts as $key => $post )  {

            if ($post['post_type'] == 'nav_menu_item' && 'simplog-main-menu' == $post['terms'][0]['slug']) {

                $postmeta = $post['postmeta'];

                switch ($post['post_title']) {

                    case 'Home' :
                    case 'Homepage' :
                        $item_url = site_url();
                    break;
                    case '404' :
                        $item_url = site_url() . '/404';
                    break;
                    case 'Author' :
                        $item_url = '';
                    break;
                }

                if (isset($item_url)) {

                    foreach ($post['postmeta'] as $meta_key => $meta_val) {

                        if ('_menu_item_url' ==  $meta_val['key'] && !empty($item_url)) {
                            $postmeta[$meta_key]['value'] = $item_url;
                            break;
                        }

                    }

                    unset($item_url);

                    $GLOBALS['wp_import']->posts[$key]['postmeta'] = $postmeta;

                }

            }

        }

        add_action('import_post_meta', array(get_class(), '_onImportPostMeta'), 10, 3);
    }

    public static function _onImportPostMeta($post_id, $key, $value)
    {

        if (empty(self::$_user)) {
            $post = get_post($post_id);
            self::$_user = $post->post_author;
        }

        if ('simplog_demo_featured_posts_widget' == $key) {
            self::$_featured_posts[] = $post_id;
        }

    }

    public static function importEnd()
    {

        $profiles = array(
            array(
                'title' => 'Twitter',
                'link' => '#',
                'image' => '%%icons_uri%%/24x21/twitter.png'
            ),
            array(
                'title' => 'Facebook',
                'link' => '#',
                'image' => '%%icons_uri%%/24x21/facebook.png'
            ),
        );

        update_user_meta(self::$_user, 'widget_themico_social_profiles', $profiles);
        update_user_meta(self::$_user, 'description', 'Good user interface design facilitates finishing the task at hand without drawing unnecessary attention to itself. Graphic design may be utilized to support its usability.');
        wp_update_user( array ('ID' => self::$_user, 'user_url' => 'http://themeforest.net/user/PixelForces/portfolio') ) ;

        global $wpdb;
        $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE meta_value = %s AND meta_key = '_menu_item_url' LIMIT 1", get_author_posts_url( self::$_user ) , 'http://wpdemo.themi.co/simplog/author/admin/') );

        remove_action('import_post_meta', array(get_class(), '_onImportPostMeta'), 10, 3);

        self::setupMenu();
        self::setupWidgets();
    }

    public static function setupPermalinks()
    {
        global $wp_rewrite;

        if (!$wp_rewrite->using_permalinks()) {

            // Change default permalink stucture to postname
            $iis7_permalinks = iis7_supports_permalinks();

            $prefix = $blog_prefix = '';
            if ( ! got_mod_rewrite() && ! $iis7_permalinks )
                    $prefix = '/index.php';
            if ( is_multisite() && !is_subdomain_install() && is_main_site() )
                    $blog_prefix = '/blog';

            $permalink_structure = '/%postname%/';

            if ( ! empty( $permalink_structure ) ) {

                    $permalink_structure = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', $permalink_structure ) );

                    if ( $prefix && $blog_prefix )
                            $permalink_structure = $prefix . preg_replace( '#^/?index\.php#', '', $permalink_structure );
                    else
                            $permalink_structure = $blog_prefix . $permalink_structure;
            }

            $wp_rewrite->set_permalink_structure( $permalink_structure );

            flush_rewrite_rules();
        }
    }


    public static function setupWidgets()
    {
        if (!function_exists('next_widget_id_number')) {
            /** WordPress Administration Widgets API */
            require_once(ABSPATH . 'wp-admin/includes/widgets.php');
        }

        if (!function_exists('wp_ajax_save_widget')) {
            /** Load Ajax Handlers for WordPress Core */
            require_once( ABSPATH . 'wp-admin/includes/ajax-actions.php' );
        }

        $nonce = wp_create_nonce('save-sidebar-widgets');

        $theme_sidebars_with_default_widgets = array(

            'right-sidebar' => array(

                strtolower('Themico_Related_Posts') => array(
                    'title' => 'Related Posts',
                    'number' => 3
                ),

                strtolower('Themico_Flickr_Photostream') => array(
                    'title' => 'Photostream',
                    'display' => 'latest',
                    'count' => 6,
                    'user' => '52617155@N08'
                ),

                strtolower('Themico_Social_Profiles') => array(
                    'title' => 'Get Connected',
                    'profiles' => array(
                        array(
                            'title' => 'Dribbble',
                            'link' => '#',
                            'image' => '%%icons_uri%%/42x36/dribbble.png'
                        ),
                        array(
                            'title' => 'Forrst',
                            'link' => '#',
                            'image' => '%%icons_uri%%/42x36/forrst.png'
                        ),
                        array(
                            'title' => 'Twitter',
                            'link' => '#',
                            'image' => '%%icons_uri%%/42x36/twitter.png'
                        ),
                        array(
                            'title' => 'Facebook',
                            'link' => '#',
                            'image' => '%%icons_uri%%/42x36/facebook.png'
                        )
                    ),

                ),

                strtolower('Themico_Latest_Posts') => array(
                    'title' => 'Latest Posts',
                    'number' => 3,
                ),

                'archives'  => array(
                    'title' => 'Archives',
                ),

                strtolower('Themico_Featured_Posts_Slider') => array(

                    'slider' => array(
                        'animation' => 'fade',
                        'animationSpeed' => 1000,
                        'slideshowSpeed' => 5000,
                        'slideshow' => true
                    ),
                    'title' => 'Featured Work',
                    'posts' => self::$_featured_posts
                ),

                'search'  => array(
                    'title' => 'Search',
                ),

                'tag_cloud'  => array(
                    'title' => 'Popular Tags',
                    'taxonomy' => 'post_tag'
                ),

            ),

            'header-top-right-corner' => array(

              strtolower('Themico_Social_Profiles') => array(
                    'title' => '',
                    'profiles' => array(
                        array(
                            'title' => 'Dribbble',
                            'link' => '#',
                            'image' => '%%icons_uri%%/46x41/dribbble.png'
                        ),
                        array(
                            'title' => 'Forrst',
                            'link' => '#',
                            'image' => '%%icons_uri%%/46x41/forrst.png'
                        ),
                        array(
                            'title' => 'Twitter',
                            'link' => '#',
                            'image' => '%%icons_uri%%/46x41/twitter.png'
                        ),
                        array(
                            'title' => 'Facebook',
                            'link' => '#',
                            'image' => '%%icons_uri%%/46x41/facebook.png'
                        ),
                        array(
                            'title' => 'Linkedin',
                            'link' => '#',
                            'image' => '%%icons_uri%%/46x41/linkedin.png'
                        ),
                        array(
                            'title' => 'Pinterest',
                            'link' => '#',
                            'image' => '%%icons_uri%%/46x41/pinterest.png'
                        )
                    ),

                ),

            ),

            'footer-sidebar' => array(

                strtolower('Themico_Most_Popular') => array(
                    'title' => 'Trending Posts',
                    'number' => 2,
                ),
                
                strtolower('Themico_Latest_Tweets_Widget') => array(
                    'title' => 'Latest Tweets',
                    'username' => 'envato',
                    'consumerkey' => 'TgXvmXxMVMW9khZNSpxnTw',
                    'consumersecret' => 'cpbgO5efD6X3sWu94jGd5ToHGcUo5NRFw7iO3qUIfM',
                    'accesstoken' => '771312306-IMMPZ7L3Ui0UFospGtUtq1acxLizysH6djX7U73Z',
                    'accesstokensecret' => 'emWvg3QX86EM2Yz8aRKjObaJNn6hdz58Sb4NnoVnY',
                    'cachetime' => 1,
                    'count' => 2

                ),
                
                strtolower('Themico_Flickr_Photostream') => array(
                    'title' => 'Photostream',
                    'display' => 'latest',
                    'count' => 6,
                    'user' => '52617155@N08',
                    'more_link' => '#'
                )
            ),

            'footer-copyright' => array(
                'text' => array(
                    'title' => '',
                    'text' => 'Copyright 2014 - Simplog'
                )
            )
        );


        $sidebars_widgets = wp_get_sidebars_widgets();

        global $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_updates;

        // Clear theme sidebars and set default widgets
        foreach ($theme_sidebars_with_default_widgets as $sidebar_index => $widgets) {

            // Clear sidebar
            $sidebars_widgets[$sidebar_index] = array();

            foreach ($widgets as $id_base => $widget_settings) {

                $next_widget_id = next_widget_id_number($id_base);
                $widget_id = $id_base . '-' . $next_widget_id;

                /**
                 * wp_ajax_save_widget() function usually called by JS during AJAX-request
                 * That is why I full HTTP request variables and skip die() by altering default wp die handler
                 */
                unset($_POST['widget-' . $id_base]);
                $_REQUEST['savewidgets'] = $nonce;
                $_POST['add_new'] = 'multi';
                $_POST['id_base'] = $id_base;
                $_POST['widget-id'] = $widget_id;
                $_POST['sidebar'] = $sidebar_index;
                $_POST['multi_number'] = $next_widget_id;
                $_POST['widget-' . $id_base] = array(
                    $next_widget_id => $widget_settings
                );

                $wp_die_handler = create_function('$handler', 'return "__return_true";');

                add_filter('wp_die_handler', $wp_die_handler);

                // Hack that allows to save multiple widgets with the same base id
                $wp_registered_widget_updates[$id_base]['callback'][0]->updated = false;

                // Hack that avoids calling the code line "call_user_func_array( $form['callback'], $form['params'] );" in wp_ajax_save_widget() function
                $wp_registered_widget_controls[$widget_id] = false;

                wp_ajax_save_widget();

                unset( $wp_registered_widget_controls[$widget_id]);

                remove_filter('wp_die_handler', $wp_die_handler);

                $sidebars_widgets[$sidebar_index][] = $widget_id;
            }

            wp_set_sidebars_widgets($sidebars_widgets);
            // TODO: Delete only widgets with base id were processed
            $wp_registered_widgets = array();
            do_action('widgets_init');
        }

    }

    public static function setupMenu()
    {
        $menu_map = array(
            'simplog-top-and-bottom-menus' => ThemicoCore::MENU_LOCATION_TOP_BOTTOM,
            'simplog-main-menu' => ThemicoCore::MENU_LOCATION_MAIN
        );

        foreach ($menu_map as $slug => $location) {

            $locations = get_nav_menu_locations();

            // Find menu ID by hard coded slug. Not the best but working solution.
            $menus = wp_get_nav_menus();
            foreach($menus as $menu) {
                if ($slug == $menu->slug) {
                    $menu_id = $menu->term_id;
                    break;
                }
            }

            // Assign founded menu to nav menu location
            if (isset($menu_id)) {

                $locations[$location] = $menu_id;

                // Update menu theme locations
                set_theme_mod( 'nav_menu_locations', array_map( 'absint', $locations ) );

                // Store 'auto-add' pages.
                $nav_menu_option = (array) get_option( 'nav_menu_options' );

                update_option( 'nav_menu_options', $nav_menu_option );

            }


        }

    }

}


?>
