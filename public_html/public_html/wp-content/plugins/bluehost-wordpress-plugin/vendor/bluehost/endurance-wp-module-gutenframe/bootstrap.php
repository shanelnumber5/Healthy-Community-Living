<?php
/**
 * Only Initialize if WordPress Is Available.
 */
if ( function_exists( 'add_action' ) ) {
	add_action( 'after_setup_theme', 'eig_module_gutenframe_register' );
}
/**
 * Register endurance-wp-module-gutenframe
 *
 * @return void
 */
function eig_module_gutenframe_register() {
	eig_register_module(
		array(
			'name'     => 'gutenframe',
			'label'    => __( 'Gutenframe', 'endurance' ),
			'callback' => 'eig_module_gutenframe_load',
			'isActive' => true,
			'isHidden' => true,
		)
	);

	if ( ! defined( 'EIG_GUTENFRAME_PYM_URL' ) ) {
		define(
			'EIG_GUTENFRAME_PYM_URL',
			str_replace( WP_CONTENT_DIR, content_url(), __DIR__ ) . '/assets/pym.js'
		);
	}
}

/**
 * Instantiate endurance-wp-module-gutenframe main class.
 *
 * @return void
 */
/**
 * Instantiate endurance-wp-module-gutenframe main class.
 *
 * @return void
 */
function eig_module_gutenframe_load() {
	if (
		class_exists( 'EIG_Module_Gutenframe' ) &&
		is_user_logged_in() &&
		current_user_can( 'edit_posts' ) &&
		is_admin()
	) {
		new EIG_Module_Gutenframe();
	}
}
