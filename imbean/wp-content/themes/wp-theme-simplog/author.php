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
                    <li class="template-name"><?php echo esc_html_e( 'Authors', THEMICO_DOMAIN ); ?></li>
                    <?php
                    /* Queue the first post, that way we know
                     * what author we're dealing with (if that is the case).
                    */
                    the_post();
                    ?>
                    <li><?php echo esc_html(get_the_author()); ?></li>
                </ul>
        </div>
    </section>

    <!-- Content -->
    <section id="content" class="span9">

            <section class="post-author">
                <div class="inner">
                    <?php echo get_avatar( get_the_author_meta('ID'), 70 ); ?>

                    <div class="author-profile">
                        <h4><?php the_author_posts_link(); ?></h4>
                        <p><?php the_author_meta('description') ?></p>
                        <address>

                            <?php $user_url = get_the_author_meta('user_url'); if (!empty($user_url)) : ?>
                                <a href="<?php echo esc_url($user_url); ?>" class="btn btn-small"><?php esc_html_e('Visit website', THEMICO_DOMAIN); ?></a>
                            <?php endif; ?>

                            <?php simplog_author_social_icons(); ?>

                        </address>
                    </div>
                </div>
            </section>

            <?php
            /* Since we called the_post() above, we need to
             * rewind the loop back to the beginning that way
             * we can run the loop properly, in full.
             */
            rewind_posts();
            ?>

            <!-- Page Title -->
            <section class="page-title">
                <div class="inner">
                    <ul>
                        <li><?php esc_html_e('Author\'s Posts', THEMICO_DOMAIN); ?></li>
                        <li class="count"><?php global $wp_query; printf( _n('%d Post', '%d Posts', $wp_query->found_posts, THEMICO_DOMAIN), $wp_query->found_posts )  ?></li>
                    </ul>
                </div>
            </section>

            <!-- Smaller Items -->
            <div class="row">
                <div id="masonry">

            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part('article'); ?>

            <?php endwhile; ?>

                </div><!-- #masonry -->
            </div>

        <?php loop_pagination(); ?>

    </section><!-- #content -->

    <?php get_sidebar(); ?>

    <?php else : ?>

        <?php get_template_part( 'no-results', 'index' ); ?>

    <?php endif; ?>

<?php get_footer(); ?>