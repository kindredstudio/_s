<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package kindred
 */

get_header(); ?>

<div id="error-page">

	<h1 class="page-title"><?php esc_html_e( 'Uh oh, you broke it.', 'kindred' ); ?></h1>
	<p>Just kidding! Maybe try returning to <a href="<?php echo esc_url( home_url( '/' ) ); ?>">our homepage</a>.</p>

</div>

<?php
get_footer();
