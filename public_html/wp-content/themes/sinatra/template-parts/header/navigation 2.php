<?php
/**
 * The template for displaying header navigation.
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

?>

<nav class="site-navigation main-navigation sinatra-primary-nav sinatra-nav si-header-element" role="navigation"<?php sinatra_schema_markup( 'site_navigation' ); ?> aria-label="<?php esc_attr_e( 'Site Navigation', 'sinatra' ); ?>">
<?php

if ( has_nav_menu( 'sinatra-primary' ) ) {
	wp_nav_menu(
		array(
			'theme_location' => 'sinatra-primary',
			'menu_id'        => 'sinatra-primary-nav',
			'container'      => '',
			'link_before'    => '<span>',
			'link_after'     => '</span>',
		)
	);
} else {
	wp_page_menu(
		array(
			'menu_class'  => 'sinatra-primary-nav',
			'show_home'   => true,
			'container'   => 'ul',
			'before'      => '',
			'after'       => '',
			'link_before' => '<span>',
			'link_after'  => '</span>',
		)
	);
}

?>
</nav><!-- END .sinatra-nav -->
