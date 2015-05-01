<?php
/**
 * The Template for displaying all single posts.
 *
 * @package simplog
 * @since simplog 1.0
 */

get_header(); ?>

<!-- Content -->
<section id="content" class="span9">
        <div class="inner">


            <?php while ( have_posts() ) : the_post(); ?>

                    <?php get_template_part( 'article' ); ?>

                    <!-- Post Author -->
                    <section class="post-author">

                        <?php echo get_avatar( get_the_author_meta('ID'), 70 ); ?>

                        <div class="author-profile">
                            <h4><?php esc_html_e('Author:', THEMICO_DOMAIN) ?> <?php the_author_posts_link(); ?></h4>
                            <p><?php the_author_meta('description') ?></p>
                            <address>

                                <?php $user_url = get_the_author_meta('user_url'); if (!empty($user_url)) : ?>
                                    <a href="<?php echo esc_url($user_url); ?>" class="btn btn-small"><?php esc_html_e('Visit website', THEMICO_DOMAIN); ?></a>
                                <?php endif; ?>

                                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta( 'ID' ))) ?>" class="btn btn-small"><?php esc_html_e('View more posts'); ?></a>
                                <?php simplog_author_social_icons(); ?>

                            </address>
                        </div>
                    </section>

                    <?php
                            // If comments are open or we have at least one comment, load up the comment template
                            if ( comments_open() || '0' != get_comments_number() )
                                    comments_template( '', true );
                    ?>

            <?php endwhile; // end of the loop. ?>

        </div>
</section><!-- #content -->


<?php get_sidebar(); ?>
<?php get_footer(); ?>