<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package simplog
 * @since simplog 1.0
 */
?>

<section id="page-404" class="span12 post no-results not-found">

    <h1 class="entry-title"><?php _e( 'Nothing Found', THEMICO_DOMAIN ); ?></h1>

    <div class="entry-content center">
            <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

                    <p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', THEMICO_DOMAIN ), admin_url( 'post-new.php' ) ); ?></p>

            <?php elseif ( is_search() ) : ?>

                    <p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', THEMICO_DOMAIN ); ?></p>
                    <?php get_search_form(); ?>

            <?php else : ?>

                    <p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', THEMICO_DOMAIN ); ?></p>
                    <?php get_search_form(); ?>

            <?php endif; ?>
    </div><!-- .entry-content -->

</section>
