<?php
/* Template Name: Contact */
?>
<?php get_header(); ?>

    <?php if ( have_posts() ) : ?>

    <section class="span12 page-title">
        <div class="inner">
            <ul>
                <li class="template-name"><?php echo esc_html_e( 'Contact Us', THEMICO_DOMAIN ); ?></li>
                <li><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', THEMICO_DOMAIN ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></li>
            </ul>
        </div>
    </section>

    <!-- Content -->
    <section id="content" class="span9">

        <div class="inner">

            <?php while ( have_posts() ) : the_post();  ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php the_content(); ?>
                </article>

            <?php endwhile; ?>

        </div>

        <?php loop_pagination(); ?>

    </section><!-- #content -->

    <?php get_sidebar(); ?>

    <?php else : ?>

        <?php get_template_part( 'no-results', 'index' ); ?>

    <?php endif; ?>

<?php get_footer(); ?>