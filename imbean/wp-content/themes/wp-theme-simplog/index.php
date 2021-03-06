<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package simplog
 * @since simplog 1.0
 */

get_header(); ?>

    <!-- Content -->
    <section id="content" class="span9">

        <?php if ( have_posts() ) : ?>

            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part('article'); ?>

            <?php endwhile; ?>

        <?php loop_pagination(); ?>

        <?php else : ?>

            <?php get_template_part( 'no-results', 'index' ); ?>

        <?php endif; ?>

    </section><!-- #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>