<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package simplog
 * @since simplog 1.0
 */
get_header(); ?>

    <?php if ( have_posts() ) : ?>

    <?php
        global $wp_query;
        $template = '';
        $title = '';
        if ( is_category() ) {
            $template = __( 'Category Archives', 'simplog' );
            $title = single_cat_title( '', false );
        } elseif ( is_tag() ) {
            $template = __( 'Tag Archives', 'simplog' );
            $title = single_tag_title( '', false );
        } elseif ( is_author() ) {
            /* Queue the first post, that way we know
             * what author we're dealing with (if that is the case).
            */
            the_post();
            /* Since we called the_post() above, we need to
             * rewind the loop back to the beginning that way
             * we can run the loop properly, in full.
             */
            $template = __( 'Author Archives', 'simplog' );
            $title = get_the_author();
            rewind_posts();

        } elseif ( is_day() ) {
            $template = __( 'Daily Archives', 'simplog' );
            $title = get_the_date();
        } elseif ( is_month() ) {
            $template = __( 'Monthly Archives', 'simplog' );
            $title = get_the_date( 'F Y' );
        } elseif(isset( $wp_query->query_vars['w']) && !empty($wp_query->query_vars['w'])) {
            $template = __( 'Weekly Archives', 'simplog' );
            $archive_week_separator = '&#8211;';
            $arc_year = $wp_query->query_vars['year'];
            $week_start = new DateTime();
            $week_start->setISODate($arc_year, $wp_query->query_vars['w']);
            $arc_week = get_weekstartend($week_start->format('Y-m-d'), get_option('start_of_week'));
            $arc_week_start = date_i18n(get_option('date_format'), $arc_week['start']);
            $arc_week_end = date_i18n(get_option('date_format'), $arc_week['end']);
            $title = $arc_week_start . $archive_week_separator . $arc_week_end;
        } elseif ( is_year() ) {
            $template = __( 'Yearly Archives', 'simplog' );
            $title = get_the_date( 'Y' );
        } else {
            $template = __( 'Archives', 'simplog' );
        }
    ?>

    <section class="span12 page-title">
            <div class="inner">
                <ul>
                    <?php if ($template) : ?>
                        <li class="template-name"><?php echo esc_html( $template ); ?></li>
                    <?php endif; ?>
                    <?php if ($title) : ?>
                        <li><?php echo esc_html($title); ?></li>
                    <?php endif; ?>
                    <li class="count"><?php global $wp_query; printf( _n('%d Post', '%d Posts', $wp_query->found_posts, THEMICO_DOMAIN), $wp_query->found_posts )  ?></li>
                </ul>
        </div>
    </section>

    <!-- Content -->
    <section id="content" class="span9">

            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part('article'); ?>

            <?php endwhile; ?>

        <?php loop_pagination(); ?>


    </section><!-- #content -->

    <?php get_sidebar(); ?>

    <?php else : ?>

        <?php get_template_part( 'no-results', 'index' ); ?>

    <?php endif; ?>

<?php get_footer(); ?>