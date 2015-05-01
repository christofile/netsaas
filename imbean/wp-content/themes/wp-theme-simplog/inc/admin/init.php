<?php

$themico_core_admin_class = 'ThemicoCoreAdmin';
if (!class_exists($themico_core_admin_class)) {

    class ThemicoCoreAdmin
    {
        public static function loadScripts()
        {

            $screen = get_current_screen();

            if ('page' == $screen->post_type) {
                wp_enqueue_script( self::prefix('page-shortcodes'), get_template_directory_uri() . '/assets/js/admin/page-shortcodes.js', array( 'jquery') );
            } elseif ($screen->id == 'options-reading') {
                wp_enqueue_script(self::prefix('admin-reading.js'), get_template_directory_uri() . '/assets/js/admin/reading.js' , array('jquery'));
            }

        }


        public static function prefix($str)
        {
            return ThemicoCore::prefix($str);
        }

        public static function loadStyles()
        {
            global $pagenow;
            $template_directory_uri = get_template_directory_uri();

            // Load wpalchemy metaboxes style
            if ('post.php' == $pagenow || 'post-new.php' == $pagenow) {
                wp_enqueue_style(self::prefix('wpalchemy-metabox'), $template_directory_uri . '/assets/css/admin-metaboxes.css');
            }

        }

        public static function setupMetaboxes()
        {
            $template_directory = get_template_directory();
            require_once $template_directory . '/libs/vendor/wpalchemy/MediaAccess.php';
            require_once $template_directory . '/libs/vendor/wpalchemy/MetaBox.php';

            /*-----------------------------------------------------------------------------------*/
            /*	Archive Template
            /*-----------------------------------------------------------------------------------*/
            new WPAlchemy_MetaBox(array(
                'id'    =>  '_' . self::prefix('archive_template_options'), //  Starting your name with an underscore will effectively hide it from also appearing in the custom fields area.
                'title' =>  __('Archive Options', THEMICO_DOMAIN),
                'template' => get_stylesheet_directory() . '/inc/admin/metaboxes/archive-page.php',
                'types' => array('page'),
                'context' => 'side',
                'priority' => 'high',
                'include_template' => array('templates/archive.php')
            ));

            new WPAlchemy_MetaBox(array(
                'id'    =>  '_' . self::prefix('contact_template_options'), //  Starting your name with an underscore will effectively hide it from also appearing in the custom fields area.
                'title' =>  __('Shortcodes Generator', THEMICO_DOMAIN),
                'template' => get_stylesheet_directory() . '/inc/admin/metaboxes/contact-page.php',
                'types' => array('page'),
                'context' => 'normal',
                'priority' => 'high',
                //'include_template' => array('templates/contact.php'),
                'save_filter' => '__return_false',
            ));

        }

        public static function setupPostFormats()
        {

            add_filter('cfpf_base_url', create_function('$url', '$fslashed_dir = trailingslashit(str_replace("\\\\","/", dirname( get_template_directory() . "/libs/vendor/wp-post-formats-ui/cf-post-formats.php"))); $fslashed_abs = trailingslashit(str_replace("\\\\","/",ABSPATH)); return site_url(str_replace( $fslashed_abs, "", $fslashed_dir )); '));
            require_once (get_template_directory() . '/libs/vendor/wp-post-formats-ui/cf-post-formats.php');

        }

        public static function onWordpressImportStart()
        {
            if (!class_exists('Simplog_Demo_Data')) {
                $template_directory = get_template_directory();
                require_once $template_directory . '/inc/helpers/class-simplog-demo-data.php';
            }

            Simplog_Demo_Data::importStart();
        }

        public static function onWordpressImportEnd()
        {
            Simplog_Demo_Data::importEnd();

            remove_action('import_end', array(get_class(), __FUNCTION__));

        }

        public static function filterMediaSendToEditor($html, $id, $attachment)
        {
            if (false !== strpos($html, '[/caption]')) {
                $html = str_replace('<a href="', '<a class="thumbnail" href="', $html);
            }
            return $html;
        }

        public static function filterUserContactmethods($contactmethods)
        {
            unset($contactmethods['aim'], $contactmethods['yim'], $contactmethods['jabber']);
            return $contactmethods;
        }

        public static function addSocialProfilesToUser($user)
        {

            $social_profiles = new Widget_Themico_Social_Profiles();

            // This hack allows tio build correct form field names
            $social_profiles->number = '0';

            $skip_icons = create_function('$skip, $root_icons_path, $path, $icons', 'return $path != path_join($root_icons_path, \'24x21\');');

            add_filter('widget_themico_social_profiles_filter_icons_by_path', $skip_icons, 10, 4);

            $profiles = get_user_meta($user->ID, 'widget_themico_social_profiles', true);

            $instance = array(
                'show_widget_title' => false
            );
            if (!empty($profiles)) {
                $instance['profiles'] = $profiles;
            }
            ?>

            <h3><?php esc_html_e('Social Profiles'); ?></h3>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><?php esc_html_e('Social Profiles'); ?></th>
                        <td>
                            <?php $social_profiles->form($instance); ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php
            remove_filter('widget_themico_social_profiles_filter_icons_by_path', $skip_icons, 10, 4);
        }

        public static function saveUserSocialProfiles($user_id)
        {
            $profiles = isset($_POST['widget-themico_social_profiles']['0']['profiles']) ? $_POST['widget-themico_social_profiles']['0']['profiles'] : array() ;

            if (!empty($profiles)) {
                update_user_meta($user_id, 'widget_themico_social_profiles', $profiles);
            }
        }

        public static function removePostFormatsFromCustomPages()
        {
            $screen = get_current_screen();
            if ('page' == $screen->post_type) {

                $page_id = get_the_ID();

                // exclude post formats for next templates
                $templates = array('templates/contact.php', 'templates/archive.php');

                $page_template = get_page_template_slug( $page_id );

                if (in_array($page_template, $templates)) {
                    remove_action('add_meta_boxes', 'cfpf_add_meta_boxes');
                    remove_action('edit_form_after_title', 'cfpf_post_admin_setup');
                }

            }

        }

        public static function onThemeActivated()
        {
            $simplog_options = get_option('simplog_options', array());
            if (!isset($simplog_options['home_big_posts'])) {
                $posts_per_page = get_option('posts_per_page');
                $simplog_options['home_big_posts'] = $posts_per_page;
                $simplog_options['home_small_posts'] = 0;
                update_option('simplog_options', $simplog_options);
            }

            add_action('admin_notices', array(get_class(), 'themeAdminNotices'));

        }

        public static function themeAdminNotices()
        {
            $theme_name = __('Simplog Theme', THEMICO_DOMAIN);

            $template_directory = get_template_directory();

            $demo_data_file = $template_directory . '/assets/demo-data.xml';
            if (file_exists($demo_data_file)) {
                $demo_data_url = get_template_directory_uri() . '/assets/demo-data.xml';
            }

            if (isset($demo_data_url)) {
                $file = '<a href="' . $demo_data_url . '">' . basename($template_directory) . '/assets/demo-data.xml' . '</a>';
            } else {
                $file = '<em>&laquo;' . basename($template_directory) . '/assets/demo-data.xml' . '&raquo;</em>';
            }

            $text = sprintf( __('You can import demo data using standart wordpress tool. Go to <em><a href="' . admin_url('import.php') . '">&laquo;Tools > Import&raquo;</a></em>, select <em><strong>WordPress</strong></em> and upload %s file.', THEMICO_DOMAIN), $file );
            $text .= '<br />';
            $text .= sprintf( __('After importing our sample data you\'ll get the site which will look absolutely the same as our <a href="%s">demo</a>. When performing the import procedure, be sure to check the <em>&laquo;Download and import file attachments&raquo;</em> box.', THEMICO_DOMAIN), 'http://wpdemo.themi.co/simplog/');

            $html = '<div class="updated fade"><i class="alignright"><small>' . $theme_name . '</small></i><p>' . $text . '</p></div>';

            echo $html;
        }


    }
}

/*-----------------------------------------------------------------------------------*/
/*	Scripts And Styles
/*-----------------------------------------------------------------------------------*/

// load external styles and scripts
add_action('admin_enqueue_scripts', array($themico_core_admin_class, 'loadScripts'));
add_action('admin_enqueue_scripts', array($themico_core_admin_class, 'loadStyles'));

/*-----------------------------------------------------------------------------------*/
/*	Setup Metaboxes
/*-----------------------------------------------------------------------------------*/
ThemicoCoreAdmin::setupMetaboxes();

/*-----------------------------------------------------------------------------------*/
/*	WordPress Importer
/*-----------------------------------------------------------------------------------*/
add_action( 'import_start', array($themico_core_admin_class, 'onWordpressImportStart') );
add_action( 'import_end', array($themico_core_admin_class, 'onWordpressImportEnd') );

add_action('after_switch_theme', array($themico_core_admin_class, 'onThemeActivated'));

/*-----------------------------------------------------------------------------------*/
/*	Admin Panel Setup
/*-----------------------------------------------------------------------------------*/
require_once dirname(__FILE__) . '/options.php';
add_action('admin_init', array('ThemicoAdminOptions', 'setup'));

/*-----------------------------------------------------------------------------------*/
/*	Post Formats
/*-----------------------------------------------------------------------------------*/
add_action('init', array($themico_core_admin_class, 'setupPostFormats'));

// Adding "thumbnail" class to link inside media caption shortcode content
add_filter('media_send_to_editor', array($themico_core_admin_class, 'filterMediaSendToEditor'), 10, 3);

// Remive AIM, Yahoo, Jabber from user profile
add_filter( 'user_contactmethods', array($themico_core_admin_class, 'filterUserContactmethods'), 10, 1);

add_action( 'show_user_profile', array($themico_core_admin_class, 'addSocialProfilesToUser') );
add_action( 'edit_user_profile', array($themico_core_admin_class, 'addSocialProfilesToUser') );

add_action( 'personal_options_update', array($themico_core_admin_class, 'saveUserSocialProfiles') );
add_action( 'edit_user_profile_update', array($themico_core_admin_class, 'saveUserSocialProfiles') );

add_action('add_meta_boxes', array($themico_core_admin_class, 'removePostFormatsFromCustomPages'), 100);