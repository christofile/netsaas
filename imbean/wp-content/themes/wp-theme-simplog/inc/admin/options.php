<?php

class ThemicoAdminOptions
{
    const SECTION_HOMEPAGE_READING_OPTIONS = 'simplog-homepage-reading-options';

    const OPTIONS_NAME = 'simplog_options';

    private static $_options = array();

    public static function setup()
    {
        // Load options from DB
        self::$_options = get_option(self::OPTIONS_NAME);

        $options_class = 'ThemicoAdminOptions';
        $reading = 'reading'; // reading page

        // Add the section to reading settings so we can add our fields to it
        add_settings_section(
            ThemicoAdminOptions::SECTION_HOMEPAGE_READING_OPTIONS,
            __('Simplog Homepage', THEMICO_DOMAIN),
            array($options_class, 'sectionHomepageReadingOptionsCallback'),
            $reading
        );

        // Add the field with the names and function to use for our new settings, put it in our new section
        add_settings_field(
            ThemicoCore::prefix('home_big_posts'),
            __('Number of big posts', THEMICO_DOMAIN),
            array($options_class, 'fieldHomeBigPostsNumber'),
            $reading,
            ThemicoAdminOptions::SECTION_HOMEPAGE_READING_OPTIONS
        );

        add_settings_field(
            ThemicoCore::prefix('home_small_posts'),
            __('Number of small posts', THEMICO_DOMAIN),
            array($options_class, 'fieldHomeSmallPostsNumber'),
            $reading,
            ThemicoAdminOptions::SECTION_HOMEPAGE_READING_OPTIONS
        );

        // Register our setting so that $_POST handling is done for us and our callback function just has to echo the <input>
        register_setting('reading', ThemicoAdminOptions::OPTIONS_NAME, array($options_class, 'sanitizeOptions'));
    }

    public static function sanitizeOptions($options)
    {

        $numbers = self::_getSmallAndBigPostsNumbers();

        if (isset($options['home_big_posts'])) {

            $posts_per_page = get_option('posts_per_page');
            if (0 == $options['home_big_posts'] && 1 == $posts_per_page) {
                $options['home_big_posts'] = 1;
                $options['home_small_posts'] = 0;
            } else {

                if (in_array($options['home_big_posts'], $numbers['big'])) {
                    $options['home_small_posts'] = $posts_per_page - $options['home_big_posts'];
                } else {
                    $options['home_big_posts'] = $posts_per_page;
                    $options['home_small_posts'] = 0;
                }

            }

        } else {
            $options['home_big_posts'] = get_option('posts_per_page');
            $options['home_small_posts'] = 0;
        }
        
        return $options;
    }

    public static function sectionHomepageReadingOptionsCallback()
    {
        ?>
        <p><?php esc_html_e('Here you can set number of big and small posts which will be shown on homepage. It depends on post per page number assigned above.', THEMICO_DOMAIN); ?></p>
        <?php
    }

    /**
     *
     * Calculates all numbers for small and big posts
     * depending on current wordpress settings
     *
     * @return array
     *  'small' - array with available numbers for small posts
     *  'big'   - array with available numbers for big posts
     */
    private static function _getSmallAndBigPostsNumbers()
    {
        static $result = array();

        if (empty($result)) {

            $result['small'] = array(0);
            $result['big'] = array();

            $posts_per_page = get_option('posts_per_page');

            if ($posts_per_page % 2 == 0) {
                // maximum number (always even) of small posts
                $small_posts_number_max = $posts_per_page;
            } else {
                $first_odd = $posts_per_page - 1;
                if ($first_odd && $first_odd % 2 == 0) {
                    $small_posts_number_max = $first_odd;
                }
            }

            if (isset($small_posts_number_max)) {
                $result['small'] = array_merge($result['small'], range(2, $small_posts_number_max, 2) );
            }

            foreach ($result['small'] as $small) {
                $result['big'][] = $posts_per_page - $small;
            }

            sort($result['big']);
            sort($result['small']);
        }

        return $result;
    }


    public static function fieldHomeBigPostsNumber()
    {
        $posts_per_page = intval( get_option('posts_per_page') );
        $numbers = self::_getSmallAndBigPostsNumbers();
        $selected = isset(self::$_options['home_big_posts']) ? intval( self::$_options['home_big_posts'] ) : $posts_per_page ;

        foreach ($numbers['big'] as $number) :
        ?>
            <label>
                <?php echo esc_html($number) ?> <input class="big-post" type="radio" <?php checked($selected, $number); ?> name="<?php echo self::OPTIONS_NAME ?>[home_big_posts]" value="<?php echo esc_attr($number); ?>" />
            </label>
            &nbsp;
        <?php
        endforeach;
    }

    public static function fieldHomeSmallPostsNumber()
    {
        $posts_per_page = get_option('posts_per_page');
        $numbers = self::_getSmallAndBigPostsNumbers();
        $home_big_posts = isset(self::$_options['home_big_posts']) ? self::$_options['home_big_posts'] : $posts_per_page ;
        $selected = $posts_per_page - $home_big_posts;

        foreach ($numbers['small'] as $number) :
        ?>
            <label>
                <?php echo esc_html($number) ?> <input class="small-post" type="radio" <?php disabled($selected == $posts_per_page); ?> <?php checked($selected, $number); ?> name="<?php echo self::OPTIONS_NAME ?>[home_small_posts]" value="<?php echo esc_attr($number); ?>" />
            </label>
            &nbsp;
        <?php
        endforeach;
    }

}

?>
