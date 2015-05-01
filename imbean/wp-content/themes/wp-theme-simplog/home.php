<?php

get_header(); ?>

<?php if (!is_paged()) : ?>

    <?php $sticky_ids = get_option('sticky_posts'); ?>

    <?php if ($sticky_ids) : ?>

        <?php $sticky_posts = new WP_Query(array('post__in' => get_option('sticky_posts'))); ?>

        <?php if ($sticky_posts->have_posts()) : ?>

            <!-- Sticky Posts -->
            <section id="featured-content" class="span12">

                <?php while ( $sticky_posts->have_posts() ) : $sticky_posts->the_post(); ?>

                    <?php get_template_part('article'); ?>

                <?php endwhile; ?>

            </section>

        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    <?php endif; ?>

<?php endif; ?>

<?php if ( have_posts() ) : ?>

<!-- Content -->
<section id="content" class="span9">


        <?php $small_posts_container = false; global $wp_query; ?>

        <?php /* Start the Loop */ ?>
        <?php while ( have_posts() ) : the_post(); ?>

            <?php if (!$small_posts_container && !is_simplog_big_post()) : $small_posts_container = 1; ?>
                <!-- Smaller Items -->
                <div class="row">
                    <div id="masonry">
            <?php endif; ?>

            <?php get_template_part('article'); ?>

            <?php if ($wp_query->current_post + 1 == $wp_query->post_count && 1 === $small_posts_container) : ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php endwhile; ?>

    <?php loop_pagination(); ?>


</section><!-- #content -->

<?php get_sidebar(); ?>

<?php else : ?>

    <?php get_template_part( 'no-results', 'index' ); ?>

<?php endif; ?>

<?php get_footer(); ?>