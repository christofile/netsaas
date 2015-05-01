<?php
/* Template Name: Archives */
?>
<?php get_header(); ?>

    <?php if ( have_posts() ) : ?>

    <section class="span12 page-title">
            <div class="inner">
                <ul>
                    <li class="template-name"><?php echo esc_html_e( 'Archives', THEMICO_DOMAIN ); ?></li>
                    <?php
                    global $wp_query;
                    /* Queue the first post, that way we know
                     * what author we're dealing with (if that is the case).
                    */
                    the_post();
                    $archive_options = ThemicoCore::getArchivesPageOptions();
                    $type = __('By Date', THEMICO_DOMAIN);
                    $count = sprintf( _n('%d Post', '%d Posts', $wp_query->found_posts, THEMICO_DOMAIN), $wp_query->found_posts );
                    if (isset($post->week)) {
                        $type = __('Weekly', THEMICO_DOMAIN);
                        $count = sprintf( _n('%d Week', '%d Weeks', $wp_query->found_posts, THEMICO_DOMAIN), $wp_query->found_posts );
                    } elseif (isset($post->year)) {
                        if (isset($post->dayofmonth)) {
                            $type = __('Daily', THEMICO_DOMAIN);
                            $count = sprintf( _n('%d Day', '%d Days', $wp_query->found_posts, THEMICO_DOMAIN), $wp_query->found_posts );
                        } elseif (isset($post->month)) {
                            $type = __('Monthly', THEMICO_DOMAIN);
                            $count = sprintf( _n('%d Month', '%d Months', $wp_query->found_posts, THEMICO_DOMAIN), $wp_query->found_posts );
                        } else {
                            $type = __('Yearly', THEMICO_DOMAIN);
                            $count = sprintf( _n('%d Year', '%d Years', $wp_query->found_posts, THEMICO_DOMAIN), $wp_query->found_posts );
                        }
                    }

                    ?>
                    <li><?php echo esc_html($type); ?></li>
                    <?php
                    /* Since we called the_post() above, we need to
                     * rewind the loop back to the beginning that way
                     * we can run the loop properly, in full.
                     */
                    rewind_posts();
                    ?>

                    <li class="count"><?php echo esc_html($count); ?></li>
                </ul>
        </div>
    </section>

    <!-- Content -->
    <section id="content" class="span9">

            <?php
            /*
                $page_object = get_queried_object();
                setup_postdata($page_object);
                global $post;
                $old_post = $post;
                $post = $page_object;

                if ( isset($GLOBALS['post']) ) {
                    $_post = & $GLOBALS['post'];
                    $GLOBALS['post'] = $page_object;
                }
                get_template_part( 'article' );
                wp_reset_postdata();
                $post = $old_post;
                if (isset($_post)) {
                    $GLOBALS['post'] = $_post;
                }*/
            ?>

            <?php /* Start the Loop */ global $wp_locale; ?>
            <?php while ( have_posts() ) : the_post();  ?>

                <?php

                    $posts_by_date_args = array();

                    if (isset($post->week)) {

                        $archive_week_separator = '&#8211;';
                        $arc_year = $post->yr;
                        $arc_week = get_weekstartend($post->yyyymmdd, get_option('start_of_week'));
                        $arc_week_start = date_i18n(get_option('date_format'), $arc_week['start']);
                        $arc_week_end = date_i18n(get_option('date_format'), $arc_week['end']);
                        $url  = sprintf('%1$s/%2$s%3$sm%4$s%5$s%6$sw%7$s%8$d', home_url(), '', '?', '=', $arc_year, '&amp;', '=', $post->week);
                        $text = $arc_week_start . $archive_week_separator . $arc_week_end;
                        $posts_by_date_args['year'] = $post->year;
                        $posts_by_date_args['w'] = $post->week;

                    } elseif (isset($post->year)) {

                        if (isset($post->dayofmonth)) {

                            $url = get_day_link($post->year, $post->month, $post->dayofmonth);
                            $date = sprintf('%1$d-%2$02d-%3$02d 00:00:00', $post->year, $post->month, $post->dayofmonth);
                            $text = mysql2date(get_option('date_format'), $date);
                            $posts_by_date_args['year'] = $post->year;
                            $posts_by_date_args['monthnum'] = $post->month;
                            $posts_by_date_args['day'] = $post->dayofmonth;

                        } elseif (isset($post->month)) {

                            $url = get_month_link( $post->year, $post->month );
                            /* translators: 1: month name, 2: 4-digit year */
                            $text = sprintf(__('%1$s %2$d', THEMICO_DOMAIN), $wp_locale->get_month($post->month), $post->year);
                            $posts_by_date_args['year'] = $post->year;
                            $posts_by_date_args['monthnum'] = $post->month;

                        } else {
                            $url = get_year_link($post->year);
                            $text = sprintf('%d', $post->year);
                            $posts_by_date_args['year'] = $post->year;
                        }
                    }

                    $posts_by_date_args['posts_per_page'] = $archive_options['posts'] ? $archive_options['posts'] : -1 ;

                    $posts_by_date = new WP_Query(apply_filters('simplog_archives_posts_args', $posts_by_date_args));

                    if ($posts_by_date->have_posts()) :
                ?>

                    <section class="archive-group">
                    	<div class="inner">
                            <hgroup <?php echo !isset($opened) ? 'class="open"' : '' ; $opened = true; ?>>
                                    <h1><?php echo esc_html($text); ?></h1>
                                    <h2><?php echo esc_html( sprintf( _n('%d Post', '%d Posts', $post->posts, THEMICO_DOMAIN), $post->posts ) ); ?></h2>
                            </hgroup>
                            <?php $total_posts = $post->posts; ?>
                            <ul>

                                <?php while ($posts_by_date->have_posts()) : $posts_by_date->the_post(); ?>

                                    <li>
                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php the_permalink(); ?>" class="thumbnail">
                                                <?php the_post_thumbnail( apply_filters('simplog_archives_thumb_size', ThemicoCore::IMAGE_SIZE_WIDGET_LATEST_POSTS) ); ?>
                                            </a>
                                        <?php endif; ?>

                                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                        <footer class="post-meta">
                                            <?php simplog_posted_on(); ?>
                                        </footer>
                                     </li>

                                <?php endwhile; ?>

                                 <?php if ($posts_by_date->post_count != $total_posts) : ?>
                                     <li>
                                        <a class="btn btn-small pull-right" href="<?php echo esc_url($url); ?>"><?php esc_html_e('View all Posts', THEMICO_DOMAIN); ?></a>
                                     </li>
                                 <?php endif; ?>
                            </ul>


                        </div>
                    </section>

                    <?php endif; wp_reset_postdata(); ?>


            <?php endwhile; ?>

        <?php loop_pagination(); ?>


    </section><!-- #content -->

    <?php get_sidebar(); ?>

    <?php else : ?>

        <?php get_template_part( 'no-results', 'index' ); ?>

    <?php endif; ?>

<?php get_footer(); ?>