<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package simplog
 * @since simplog 1.0
 */
get_header(); ?>

    <?php if ( have_posts() ) : ?>

    <section class="span12 page-title">
            <div class="inner">
                <ul>
                    <li><?php printf( __( 'Search Results for: %s', THEMICO_DOMAIN ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></li>
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