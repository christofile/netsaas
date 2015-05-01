<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package simplog
 * @since simplog 1.0
 */

get_header(); ?>

<section id="page-404" class="span12 post no-results not-found">

    <img src="<?php echo get_template_directory_uri() ?>/assets/img/404.png" alt="">

    <h1 class="entry-title"><?php _e( 'We are sorry but the page you are looking for does not exist.', THEMICO_DOMAIN ); ?></h1>

    <div class="entry-content center" role="main">

        <?php get_search_form(); ?>
        
    </div><!-- .entry-content -->

</section>

<?php get_footer(); ?>