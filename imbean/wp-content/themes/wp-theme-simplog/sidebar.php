<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package simplog
 * @since simplog 1.0
 */
?>
<!-- Right Sidebar -->
<section id="right-sidebar" class="span3">

    <?php do_action( 'before_sidebar' ); ?>

    <?php if ( ! dynamic_sidebar( 'right-sidebar' ) ) : ?>

            <aside id="search" class="widget widget_search">
                <h4 class="widget-title"><?php esc_html_e('Search', THEMICO_DOMAIN); ?></h4>
                <form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
                    <fieldset>
                        <input type="text" class="field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" id="s" placeholder="<?php esc_attr_e( 'Search &hellip;', 'simplog' ); ?>" />
                    </fieldset>
                </form>
            </aside>

            <aside id="archives" class="widget widget_archive">
                    <h4 class="widget-title"><?php _e( 'Archives', 'simplog' ); ?></h4>
                    <ul>
                        <?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
                    </ul>
            </aside>

            <aside id="meta" class="widget">
                    <h4 class="widget-title"><?php _e( 'Meta', 'simplog' ); ?></h4>
                    <ul>
                            <?php wp_register(); ?>
                            <li><?php wp_loginout(); ?></li>
                            <?php wp_meta(); ?>
                    </ul>
            </aside>

    <?php endif; // end sidebar widget area ?>

</section><!-- #right-sidebar -->