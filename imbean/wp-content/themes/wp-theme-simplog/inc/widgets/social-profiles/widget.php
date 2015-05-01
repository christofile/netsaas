<?php
/*-----------------------------------------------------------------------------------*/
/*  Create the widget
/*-----------------------------------------------------------------------------------*/
add_action('widgets_init', create_function( '', 'register_widget( "Widget_Themico_Social_Profiles" );' ) );

/*-----------------------------------------------------------------------------------*/
/*  Widget class
/*-----------------------------------------------------------------------------------*/
class Widget_Themico_Social_Profiles extends Widget_Themico_Base {

    public function adminScripts()
    {
        $screen = get_current_screen();
        if ('widgets' ==  $screen->id || 'profile' == $screen->id) {
            wp_enqueue_script($this->getWidgetClass() . 'admin-controls', $this->getFullUrlByRel('admin.js'), array('jquery', 'thickbox'));
        }
    }

    public function adminStyles()
    {
        $screen = get_current_screen();
        if ('widgets' ==  $screen->id || 'profile' == $screen->id) {
            wp_enqueue_style($this->getWidgetClass() . 'admin-style', $this->getFullUrlByRel('admin.css'), array('thickbox'));
        }
    }

    public $group_tag = '';

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        $this->_admin_scripts = true;
        $this->_admin_styles = true;

            $control_ops = array('width' => 560, 'height' => 300);

            parent::__construct(
                    '', // use class name as widget unique ID
                    __('Themico Social Profiles', THEMICO_DOMAIN), // Name
                    array(
                        'description' => __( 'Will show social profiles icons', THEMICO_DOMAIN ),
                    ), // Args
                    $control_ops
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
        $profiles = isset( $instance['profiles'] ) ? $instance['profiles']  : '' ;
        echo $before_widget;
        if ( ! empty( $title ) )
                echo $before_title . $title . $after_title;
        if (!empty($profiles)) :
            $icons = $this->_getIconsRootPathAndUrl();
            $icons_root_url = $icons['url'];
        ?>

        <nav class="social-profiles">
            <ul>
                <?php foreach ($profiles as $profile) : $image = str_replace('%%icons_uri%%', $icons_root_url, $profile['image']);?>
                <li><a title="<?php echo esc_attr($profile['title']); ?>" href="<?php echo esc_url($profile['link']); ?>"><img src="<?php echo esc_attr($image); ?>" alt="<?php echo esc_attr($profile['title']); ?>"></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
        endif;

        echo $after_widget;
    }

    private function _getIconsRootPathAndUrl()
    {
        static $icons = array();

        if (empty($icons)) {

            $rel_path = 'assets/img/social-icons';

            $root_icons_path = path_join( get_stylesheet_directory() , $rel_path );

            $root_icons_url = trailingslashit( get_stylesheet_directory_uri() ) . $rel_path;

            // If child theme does not contain directory with social icons then load them from parent theme
            if (is_child_theme() && !file_exists($root_icons_path)) {
                // Parent theme URL
                $root_icons_url = trailingslashit( get_template_directory_uri() ) . $rel_path;

                // Parent theme dir
                $root_icons_path = path_join(get_template_directory() , $rel_path );
            }

            $icons['url'] = $root_icons_url;
            $icons['path'] = $root_icons_path;

        }

        return $icons;

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
            $instance['profiles'] = $new_instance['profiles'];
            $instance['title'] = strip_tags( $new_instance['title'] );

            return $instance;
    }

    function the_group_open($t = 'div')
    {
            echo $this->get_the_group_open($t);
    }

    public function is_first()
    {
        return $this->_first;
    }

    public function is_last()
    {
        return $this->_last;
    }

    private $_limit = 0;

    /**
     * @since	1.1
     * @access	public
     */
    function get_the_group_open($t = 'div')
    {
            $this->group_tag = $t;

            $loop_open = NULL;

            $loop_open_classes = array('wpa_loop', 'wpa_loop-' . sanitize_key( $this->name ) );

            $css_class = array('wpa_group', 'wpa_group-'. sanitize_key( $this->name ));

            if ($this->is_first())
            {
                    array_push($css_class, 'first');

                    $loop_open = '<div class="wpa_loop">';

                    if ($this->_limit)
                    {
                            array_push($loop_open_classes, 'wpa_loop_limit-' . $this->_limit);
                    }

                    $loop_open = '<div id="wpa_loop-'. sanitize_key( $this->name ) .'" class="' . implode(' ', $loop_open_classes) . '">';
            }

            if ($this->is_last())
            {
                    array_push($css_class, 'last');
                    array_push($css_class, 'tocopy');
            }

            return $loop_open . '<' . $t . ' class="'. implode(' ', $css_class) . '">';
    }

    function the_group_close()
    {
        echo $this->get_the_group_close();
    }

    function get_the_group_close()
    {
            $loop_close = NULL;

            if ($this->is_last())
            {
                    $loop_close = '</div>';
            }

            return '</' . $this->group_tag . '>' . $loop_close;
    }

    private $_first = false;
    private $_last = false;

    public function getImagesFromDir($path)
    {
        $path = $this->toUnixPath($path);

        $images = array();

        $allowed_image_extensions = array('gif', 'png', 'jpeg', 'jpg', 'jpe');

        if (is_readable($path))
            try {

                $dir = new DirectoryIterator($path);

                foreach ($dir as $file) {

                    if ($file->isFile()) {

                        $image     = pathinfo($file->getFilename());
                        $extension = strtolower($image['extension']);
                        if (in_array($extension, $allowed_image_extensions) && $file->isReadable()) {
                            $file_name       = $file->getFilename();

                            if (!isset($images[$path])) {
                                $images[$path] = array();
                            }

                            $images[$path][] = $file_name;
                        }
                    } elseif ($file->isDir() && !$file->isDot()) {
                        $images = array_merge($images, $this->getImagesFromDir($file->getPathname()));
                    }
                }

                ksort($images);
            } catch (Exception $e) {

            }

        return $images;
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
            'profiles' => array(
                array(
                    'title' => 'Title',
                    'link' => 'http://',
                    'image' => 'Image URL'
                )
            ),
            'title' => '',
            'show_widget_title' => true
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $profiles_count = count( $instance['profiles'] );
        /* Build our form -----------------------------------------------------------*/

        $icons = $this->_getIconsRootPathAndUrl();

        $root_icons_path = $icons['path'];

        $root_icons_url = $icons['url'];

        $icons_by_path = $this->getImagesFromDir($root_icons_path);

        $predefined_icons = '<div class="predefined-icons">';

        $widget_class = $this->getWidgetClass();

        foreach ($icons_by_path as $path => $icons) {

            $continue =  apply_filters_ref_array($widget_class . '_filter_icons_by_path', array( false, $root_icons_path, $path, $icons ));

            if ($continue) {
                continue;
            }

            $subdir = str_replace($root_icons_path, '', $path);

            $predefined_icons .= '<p>';

            foreach ($icons as $icon) {
                $icon_url = trailingslashit($root_icons_url . $subdir) . $icon;

                $title = ucfirst(pathinfo($icon, PATHINFO_FILENAME));

                $icon_tpl = str_replace($root_icons_url, '%%icons_uri%%', $icon_url);

                $predefined_icons .= '<a class="predefined-social-icon" href="#"><img data-icon="' . esc_attr($icon_tpl) . '" src="' . esc_url($icon_url) . '" alt="' . esc_attr($title) . '" /></a>';
            }

            $predefined_icons .= '</p>';

        }

        $predefined_icons .= '</div>';

        ?>

        <?php if ($instance['show_widget_title']) : ?>
            <p>
                    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', THEMICO_DOMAIN) ?></label>
                    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
            </p>
        <?php endif; ?>

        <div class="postbox <?php echo esc_attr($this->getWidgetClass()); ?>">

            <?php $i = 0; foreach ($instance['profiles'] as $n => $profile) : $i++; ?>

            <?php
                $this->_first = ($i === 1);
                $this->_last = ($i == $profiles_count);
            ?>

            <?php $this->the_group_open(); ?>

                <table class="form-table">
                    <tbody>

                        <tr>
                            <th style="padding-left: 0px;"><label><?php esc_html_e('Title', THEMICO_DOMAIN); ?></label></th>
                            <td>
                                <input type="text" name="<?php echo $this->get_field_name( 'profiles][' . $n . '][title' ); ?>" value="<?php  echo esc_attr($profile['title']);  ?>" class="social-profile-title"/>
                            </td>
                        </tr>

                        <tr>
                            <th style="padding-left: 0px;"><label><?php esc_html_e('URL', THEMICO_DOMAIN); ?></label></th>
                            <td>
                                <input type="text" name="<?php echo $this->get_field_name( 'profiles][' . $n . '][link' ); ?>" value="<?php  echo esc_attr($profile['link']);  ?>" class="social-profile-url"/>
                            </td>
                        </tr>

                        <tr>
                            <th style="padding-left: 0px;">
                                <label><?php esc_html_e('Icon', THEMICO_DOMAIN); ?></label>
                            </th>
                            <td>

                                <input type="text" name="<?php echo $this->get_field_name( 'profiles][' . $n . '][image' ); ?>" value="<?php  echo esc_attr($profile['image']);  ?>" class="social-profile-icon" />

                                <p>
                                    <div class="social-icon-preview">
                                        <img src="<?php echo esc_url( str_replace('%%icons_uri%%', $root_icons_url, $profile['image']) ) ?>" alt="" />
                                    </div>
                                </p>

                                <p>
                                    <a class="toggle-icons button" href="#"><?php esc_html_e('Theme Icons', THEMICO_DOMAIN) ?></a>
                                    &blacksquare;
                                    <a title="<?php esc_attr_e('Media Library', THEMICO_DOMAIN) ?>" class="button insert-media add_media" href="#"><span class="wp-media-buttons-icon"></span> <?php esc_attr_e('Media Library', THEMICO_DOMAIN) ?></a>

                                    <?php echo $predefined_icons; ?>

                                </p>

                                <p>
                                    <a href="#" class="dodelete button"><?php esc_html_e('Remove', THEMICO_DOMAIN); ?></a>
                                </p>


                            </td>
                        </tr>

                    </tbody>
                </table>

            <?php $this->the_group_close(); ?>

            <?php endforeach; ?>

            <p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-<?php echo sanitize_key( $this->name ) ?> button"><?php esc_html_e('Add New Social Profile', THEMICO_DOMAIN) ?></a></p>

        </div>

        <?php
    }

}

?>