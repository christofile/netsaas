<?php
/**
 * @package simplog
 * @since simplog 1.0
 */
$post_format = get_post_format();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php if (!empty($post_format)) : ?>

        <?php if ('image' == $post_format && has_post_thumbnail()) : ?>
            <div class="post-media">
                <a href="<?php the_permalink(); ?>" class="thumbnail">
                    <?php the_post_thumbnail( simplog_post_thumb_size() ); ?>
                </a>
            </div>
        <?php elseif ('video' == $post_format) : ?>
            <div class="post-media">
                <div class="thumbnail">
                    <?php echo simplog_video_content(); ?>
                </div>
            </div>
        <?php elseif ('gallery' == $post_format): ?>

            <?php
            $args = array(
                    'post_type' => 'attachment',
                    'numberposts' => -1,
                    'post_status' => null,
                    'post_parent' => get_the_ID(),
                    'order' => 'ASC',
                    'orderby' => 'menu_order ID',
            );

            $gallery_attachments = get_posts( apply_filters('simplog_gallery_posts_args' , $args ));
            if ($gallery_attachments) :
            ?>

            <div class="post-media">
                <div class="flexslider">
                    <ul class="slides">
                        <?php foreach ($gallery_attachments as $attachment) : ?>
                            <li>
                                <?php echo wp_get_attachment_link($attachment->ID, simplog_post_thumb_size()); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <?php endif; ?>

        <?php endif ?>

    <?php endif; ?>

    <?php if (is_singular()) : ?>
        <h1 class="entry-title">
            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', THEMICO_DOMAIN ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
        </h1>
    <?php elseif (is_sticky() && is_home()) : ?>
        <h1 class="entry-title">
            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', THEMICO_DOMAIN ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a> <?php esc_html_e('by', THEMICO_DOMAIN); ?> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="author-nickname"><?php echo esc_html(get_the_author()); ?></a>
        </h1>
    <?php else : ?>
        <h2 class="entry-title">
            <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', THEMICO_DOMAIN ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
        </h2>
    <?php endif; ?>

    <footer class="post-meta">
        <?php simplog_posted_on(); ?>
    </footer>

    <div class="entry-content">

        <?php if (is_singular()) : ?>

            <?php the_content(); ?>

        <?php else : ?>

            <?php if (is_simplog_big_post()) : ?>

                <?php if (has_excerpt()) : ?>

                    <?php the_excerpt(); ?>

                <?php else : ?>

                    <?php the_content(); ?>

                    <?php if (!simplog_post_has_more_link()) : ?>
                        <?php echo ThemicoCore::getContinueReadingLink(); ?>
                    <?php endif; ?>

                <?php endif; ?>

            <?php elseif (!(is_sticky() && is_home()) ) : echo ThemicoCore::getContinueReadingLink(); ?>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</article>
