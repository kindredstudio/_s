<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kindred
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', '_s' ); ?></a>


	<!-- <?php
	if ( is_front_page() ) : ?>

	<header id="masthead" class="home-site-header" role="banner">

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<?php wp_nav_menu( array('menu' => 'primary', 'theme_location' => 'primary') ); ?>
		</nav>

		<div class="site-branding">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/img/logo.png" />
		</div>

	</header>

	<?php else : ?> -->

	<header id="masthead" class="site-header" role="banner">

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<?php wp_nav_menu( array('menu' => 'primary', 'theme_location' => 'primary') ); ?>
		</nav>

		<div class="site-branding">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/img/logo.png" />
		</div>

	</header>

	<!-- <?php endif; ?> -->

	<main id="content">
