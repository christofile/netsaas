<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package simplog
 * @since simplog 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
        <title><?php
                /*
                 * Print the <title> tag based on what is being viewed.
                 * We filter the output of wp_title() a bit -- see
                 * ThemicoCore::filterWpTitle() in functions.php.
                 */
                wp_title( '|', true, 'right' );
        ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

        <?php if (file_exists( get_template_directory() . '/favicon.ico')) : ?>
	<!--Favicon of your website-->
        <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri();  ?>/favicon.ico" />
        <?php endif; ?>
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
        /* Always have wp_head() just before the closing </head>
         * tag of your theme, or you will break many plugins, which
         * generally use this hook to add elements to <head> such
         * as styles, scripts, and meta tags.
         * @see ThemicoCore::loadStyles() in functions.php.
         * @see ThemicoCore::loadScripts() in functions.php.
         * @see ThemicoCore::printStyles() in functions.php.
         * @see ThemicoCore::printScripts() in functions.php.
         */
        wp_head();
?>

</head>

<body <?php body_class(); ?>>

	<!-- Header -->
	<header id="header">

            <!-- Navigation -->
            <?php $menu_result = wp_nav_menu( array(
                'fallback_cb' => array('ThemicoCore', 'emptyMenuFallback'),
                'depth' => 1,
                'theme_location' => ThemicoCore::MENU_LOCATION_TOP_BOTTOM,
                'container' => 'nav',
                'container_id' => 'header-navigation',
                'items_wrap'      => '<div class="container"><ul id="%1$s" class="%2$s">%3$s</ul></div>',
                )
            ); ?>

            <?php if ($menu_result === false) ThemicoCore::emptyMenuFallback(array('theme_location' => ThemicoCore::MENU_LOCATION_TOP_BOTTOM)); // For wp 3.5 only ?>

        <div class="container">

            <?php if (get_header_image()) : ?>

                <!-- Logo -->
                <a id="logo" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home" href="<?php echo home_url( '/' ); ?>">
                    <img src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" />
                </a>

            <?php else : ?>
                    <?php simplog_theme_help_tip('site-logo'); ?>
            <?php endif; ?>

            <?php if ( is_active_sidebar( 'header-top-right-corner') ) : ?>
                <?php dynamic_sidebar( 'header-top-right-corner' ); ?>
            <?php else : ?>
                <?php simplog_theme_help_tip('header-top-right-corner'); ?>
            <?php endif; ?>


            <!-- Navigation -->
            <?php $main_menu = wp_nav_menu( array(
                'fallback_cb' => array('ThemicoCore', 'emptyMenuFallback'),
                'theme_location' => ThemicoCore::MENU_LOCATION_MAIN,
                'container' => 'nav',
                'container_id' => 'main-navigation',
                'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                )
            ); ?>

            <?php if ($main_menu === false) ThemicoCore::emptyMenuFallback(array('theme_location' => ThemicoCore::MENU_LOCATION_MAIN)); // For wp 3.5 only ?>

        </div>
    </header><!-- #header -->

    <!-- Page Body (Sidebars and Content) -->
    <section id="body">
    	<div class="container">
    		<div class="row">
