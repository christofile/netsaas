<?php
/*-----------------------------------------------------------------------------------*/
/*  Create the widget
/*-----------------------------------------------------------------------------------*/
add_action( 'widgets_init', create_function( '', 'register_widget( "Themico_Latest_Tweets_Widget" );' ) );
add_action('wp_enqueue_scripts', array('Themico_Latest_Tweets_Widget', 'setupScriptsAndStyles'));

/*-----------------------------------------------------------------------------------*/
/*  Widget class
/*-----------------------------------------------------------------------------------*/
class Themico_Latest_Tweets_Widget extends WP_Widget {

    /*
     * Register and queue JS scripts
     */
    public static function setupScriptsAndStyles()
    {
        $widget_rel_path = str_replace(str_replace('\\', '/', get_template_directory()), '', str_replace('\\', '/', dirname(__FILE__)));
        $template_directory_uri = get_template_directory_uri();
        $handle = get_class();
	wp_register_script($handle, $template_directory_uri . $widget_rel_path . '/jquery.tweet.js', array('jquery'));
	wp_register_style($handle, $template_directory_uri . $widget_rel_path . '/style.css');

	wp_enqueue_script($handle);
	wp_enqueue_style($handle);
    }

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

            parent::__construct(
                    '', // use class name as widget unique ID
                    __('Themico Latest Tweets', THEMICO_DOMAIN), // Name
                    array(
                        'description' => __('Recent Tweets Widget for Twitter API v1.1 with Cache', THEMICO_DOMAIN ),
                        'classname'   => 'themico-latest-tweets-widget'
                        ) // Args
            );
    }



    //convert links to clickable format
    function convert_links($status, $targetBlank = true, $linkMaxLen = 250)
    {

        // the target
        $target = $targetBlank ? " target=\"_blank\" " : "";

        // convert link to url
        $status = preg_replace("/((http:\/\/|https:\/\/)[^ )]+)/e", "'<a href=\"$1\" title=\"$1\" $target >'. ((strlen('$1')>=$linkMaxLen ? substr('$1',0,$linkMaxLen).'...':'$1')).'</a>'", $status);

        // convert @ to follow
        $status = preg_replace("/(@([_a-z0-9\-]+))/i", "<a href=\"http://twitter.com/$2\" title=\"Follow $2\" $target >$1</a>", $status);

        // convert # to search
        $status = preg_replace("/(#([_a-z0-9\-]+))/i", "<a href=\"https://twitter.com/search?q=$2\" title=\"Search $1\" $target >$1</a>", $status);

        // return the status
        return $status;
    }

    //convert dates to readable format
    function relative_time($a)
    {
        //get current timestampt
        $b      = strtotime("now");
        //get timestamp when tweet created
        $c      = strtotime($a);
        //get difference
        $d      = $b - $c;
        //calculate different time values
        $minute = 60;
        $hour   = $minute * 60;
        $day    = $hour * 24;
        $week   = $day * 7;

        if (is_numeric($d) && $d > 0) {
            //if less then 3 seconds
            if ($d < 3)
                return "right now";
            //if less then minute
            if ($d < $minute)
                return floor($d) . " seconds ago";
            //if less then 2 minutes
            if ($d < $minute * 2)
                return "about 1 minute ago";
            //if less then hour
            if ($d < $hour)
                return floor($d / $minute) . " minutes ago";
            //if less then 2 hours
            if ($d < $hour * 2)
                return "about 1 hour ago";
            //if less then day
            if ($d < $day)
                return floor($d / $hour) . " hours ago";
            //if more then day, but less then 2 days
            if ($d > $day && $d < $day * 2)
                return "yesterday";
            //if less then year
            if ($d < $day * 365)
                return floor($d / $day) . " days ago";
            //else return more than a year
            return "over a year ago";
        }
    }


    public function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret)
    {
        $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
        return $connection;
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

        extract($args);
        if (!empty($instance['title'])) {
            $title = apply_filters('widget_title', $instance['title']);
        }

        echo $before_widget;

        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        }


        //check settings and die if not set
        if (empty($instance['consumerkey']) || empty($instance['consumersecret']) || empty($instance['accesstoken']) || empty($instance['accesstokensecret']) || empty($instance['cachetime']) || empty($instance['username'])) {
            echo __('<strong>Please fill all widget settings!</strong>', THEMICO_DOMAIN) . $after_widget;
            return;
        }


        //check if cache needs update
        $tp_twitter_plugin_last_cache_time = get_option('tp_twitter_plugin_last_cache_time');
        $diff                              = time() - $tp_twitter_plugin_last_cache_time;
        $crt                               = $instance['cachetime'] * 3600;

        //	yes, it needs update
        if ($diff >= $crt || empty($tp_twitter_plugin_last_cache_time)) {

            if (!require_once(dirname(__FILE__) . '/twitteroauth.php')) {
                echo __('<strong>Couldn\'t find twitteroauth.php!</strong>', THEMICO_DOMAIN) . $after_widget;
                return;
            }


            $connection = $this->getConnectionWithAccessToken($instance['consumerkey'], $instance['consumersecret'], $instance['accesstoken'], $instance['accesstokensecret']);
            $tweets     = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" . $instance['username'] . "&count=10") or wp_die(__('Couldn\'t retrieve tweets! Wrong username?', THEMICO_DOMAIN));


            if (!empty($tweets->errors)) {
                if ($tweets->errors[0]->message == 'Invalid or expired token') {
                    echo '<strong>' . $tweets->errors[0]->message . '!</strong><br />You\'ll need to regenerate it <a href="https://dev.twitter.com/apps" target="_blank">here</a>!' . $after_widget;
                } else {
                    echo '<strong>' . $tweets->errors[0]->message . '</strong>' . $after_widget;
                }
                return;
            }

            for ($i = 0; $i <= count($tweets); $i++) {
                if (!empty($tweets[$i])) {
                    $tweets_array[$i]['created_at'] = $tweets[$i]->created_at;
                    $tweets_array[$i]['text']       = $tweets[$i]->text;
                    $tweets_array[$i]['status_id']  = $tweets[$i]->id_str;
                }
            }

            //save tweets to wp option
            update_option('tp_twitter_plugin_tweets', serialize($tweets_array));
            update_option('tp_twitter_plugin_last_cache_time', time());

            echo '<!-- twitter cache has been updated! -->';
        }

        $tp_twitter_plugin_tweets = maybe_unserialize(get_option('tp_twitter_plugin_tweets'));

        if (!empty($tp_twitter_plugin_tweets)) {
            ?>
                <ul>

                    <?php foreach ($tp_twitter_plugin_tweets as $i => $tweet) : if ($i >= $instance['count']) break; ?>
                    <li><span><?php echo $this->convert_links($tweet['text']); ?></span><br><a class="twitter_time" target="_blank" href="http://twitter.com/<?php echo $instance['username'] ?>/statuses/<?php echo $tweet['status_id'] ?>"><?php echo $this->relative_time($tweet['created_at']) ?></a></li>
                    <?php endforeach; ?>

                </ul>

        <?php
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
    public function update( $new_instance, $old_instance )
    {

            $instance = array();

            $instance['title'] = strip_tags( $new_instance['title'] );
            $instance['consumerkey'] = strip_tags( $new_instance['consumerkey'] );
            $instance['consumersecret'] = strip_tags( $new_instance['consumersecret'] );
            $instance['accesstoken'] = strip_tags( $new_instance['accesstoken'] );
            $instance['accesstokensecret'] = strip_tags( $new_instance['accesstokensecret'] );
            $instance['cachetime'] = strip_tags( $new_instance['cachetime'] );
            $instance['username'] = strip_tags( $new_instance['username'] );
            $instance['count'] = strip_tags( $new_instance['count'] );

            if($old_instance['username'] != $new_instance['username']){
                    delete_option('tp_twitter_plugin_last_cache_time');
            }

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
            'title' => __('Latest Tweets', THEMICO_DOMAIN),
            'consumerkey' => '',
            'consumersecret' => '',
            'accesstoken' => '',
            'accesstokensecret' => '',
            'cachetime' => '',
            'username' => '',
            'count' => '5'
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        /* Build our form -----------------------------------------------------------*/
        ?>


        <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
        </p>

        <p>
                <label for="<?php echo $this->get_field_id( 'consumerkey' ); ?>"><?php _e('Consumer Key:', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'consumerkey' ); ?>" name="<?php echo $this->get_field_name( 'consumerkey' ); ?>" value="<?php echo esc_attr( $instance['consumerkey'] ); ?>" />
        </p>

        <p>
                <label for="<?php echo $this->get_field_id( 'consumersecret' ); ?>"><?php _e('Consumer Secret:', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'consumersecret' ); ?>" name="<?php echo $this->get_field_name( 'consumersecret' ); ?>" value="<?php echo esc_attr( $instance['consumersecret'] ); ?>" />
        </p>

        <p>
                <label for="<?php echo $this->get_field_id( 'accesstoken' ); ?>"><?php _e('Access Token:', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'accesstoken' ); ?>" name="<?php echo $this->get_field_name( 'accesstoken' ); ?>" value="<?php echo esc_attr( $instance['accesstoken'] ); ?>" />
        </p>

        <p>
                <label for="<?php echo $this->get_field_id( 'accesstokensecret' ); ?>"><?php _e('Access Token Secret:', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'accesstokensecret' ); ?>" name="<?php echo $this->get_field_name( 'accesstokensecret' ); ?>" value="<?php echo esc_attr( $instance['accesstokensecret'] ); ?>" />
        </p>

        <p>
                <label for="<?php echo $this->get_field_id( 'cachetime' ); ?>"><?php _e('Cache Tweets in every hours:', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'cachetime' ); ?>" name="<?php echo $this->get_field_name( 'cachetime' ); ?>" value="<?php echo esc_attr( $instance['cachetime'] ); ?>" />
        </p>

        <p>
                <label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e('Twitter Username:', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo esc_attr( $instance['username'] ); ?>" />
        </p>


        <p>
                <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e('Tweets to display:', THEMICO_DOMAIN) ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo esc_attr( $instance['count'] ); ?>" />
        </p>

        <?php
    }

}

?>