<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package simplog
 * @since simplog 1.0
 */
?>
            </div>
    	</div>
    </section>


    <!-- Footer -->
    <footer id="footer">
    	<div class="container">
    		<div class="row">

                <!-- Footer Sidebar -->
                <?php if ( is_active_sidebar( 'footer-sidebar') ) : ?>
                        <?php dynamic_sidebar( 'footer-sidebar' ); ?>
                <?php else : ?>
                        <?php simplog_theme_help_tip('footer-sidebar'); ?>
                <?php endif; ?>
            </div>
    	</div>
		<!-- Copyright -->
    	<div id="copyright">
        	<div class="container">

                    <!-- Navigation -->
                    <?php $menu_result = wp_nav_menu( array(
                        'fallback_cb' => array('ThemicoCore', 'emptyMenuFallback'),
                        'depth' => 1,
                        'theme_location' => ThemicoCore::MENU_LOCATION_TOP_BOTTOM,
                        'container' => 'nav',
                        'container_id' => 'footer-navigation',
                        'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        )
                    ); ?>

                    <?php if ($menu_result === false) ThemicoCore::emptyMenuFallback(array('theme_location' => ThemicoCore::MENU_LOCATION_TOP_BOTTOM)); // For wp 3.5 only ?>

                    <?php if ( is_active_sidebar( 'footer-copyright') ) : ?>
                            <?php dynamic_sidebar( 'footer-copyright' ); ?>
                    <?php else : ?>
                        <?php simplog_theme_help_tip('footer-copyright'); ?>
                    <?php endif; ?>
            </div>
        </div>
    </footer><!-- #footer -->


<?php wp_footer(); ?>

</body>
</html>